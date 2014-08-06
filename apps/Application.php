<?php
/**
 * Application bootstrap class that is responsible for initializing of shared services, modules, models and so on.
 * Inspired from Multiple-Service-Layer sample architecture (see https://github.com/phalcon/mvc).
 * Use some kind of "Shared-Service-Layer" for database crud/orm whatever you like to call it.
 */
namespace PhalconSkeletons;

// Services are globally registered in this file
use Phalcon\Exception;
use Phalcon\Loader;
use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Logger\Adapter\File as SystemLogger;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
// use \PhalconTests\Models\Services\Services as Services;

class Application extends \Phalcon\Mvc\Application
{
    const ENV_LIVE = 'live';
    const ENV_STAGING = 'staging';
    const ENV_TESTING = 'testing';
    const ENV_DEVELOPMENT = 'development';

    private $_env = '';
    private $_debug = true;
    private $_appConfig = null;
    private $_systemLogger = null;
    private $_modulesConfig = array();

    public function __construct($dependencyInjector = null)
    {
        if ( !($this->_systemLogger instanceof \Phalcon\Logger\Adapter\File) ) {
            $this->_systemLogger = new SystemLogger(PATH_LOGS . 'system.log');
        }
        parent::__construct($dependencyInjector);
    }

    /**
     * Application main cycle - setups auto load, modules, models,
     * application services and so on.
     *
     * @throws \Exception
     */
    public function load($config = null, $env = '')
    {
        $this->_bootstrapLog();

        if ($config instanceof \Phalcon\Config) {
            $this->_appConfig = $config;
        }

        if ( !empty($env) ) {
            $this->setEnv($env);
        }

        try {
            $this->_setAutoload()
                 ->_loadDI()
                 ->_loadModules()
                 ->handle()
                 ->send();
        } catch(\Exception $e) {
            throw new \Phalcon\Exception('Unable to load application : '.$e->getMessage());
        }
    }

    /**
     * Set application debug mode
     *
     * @param $dbgMode
     */
    public function setDebug($dbgMode)
    {
        if ( true === $this->_appConfig->application->debug ) {
            error_reporting(E_ALL);

            ini_set('log_errors', true);
            ini_set('report_memleaks', true);
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);

            $this->_debug = true;
            return true;
        }

        if (true === (bool) $dbgMode) {
            error_reporting(E_ALL);

            ini_set('log_errors', true);
            ini_set('report_memleaks', true);
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);

            $this->_debug = true;
        } else {
            error_reporting(0);

            ini_set('log_errors', false);
            ini_set('report_memleaks', false);
            ini_set('display_errors', false);
            ini_set('display_startup_errors', false);

            $this->_debug = false;
        }
    }

    /**
     * Whether debug application mode is activated.
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->_debug;
    }

    /**
     * Register application config
     *
     * @param \Phalcon\Config $config
     * @return object \Phalcon\Mvc\Application
     */
    public function setConfig(\Phalcon\Config $config)
    {
        $this->_appConfig = $config;
        return $this;
    }

    /**
     * Application config getter
     *
     * @return object \Phalcon\Config
     */
    public function getConfig()
    {
        return $this->_appConfig();
    }


    public function setEnv($env)
    {
        switch ( $env ) {
            case self::ENV_TESTING:
            case self::ENV_DEVELOPMENT:
                $this->_env = $env;;
                $this->setDebug(true);
                return $this;
            break;

            case self::ENV_LIVE:
            case self::ENV_STAGING:
                $this->_env = $env;
                $this->setDebug(false);
                return $this;
            break;

            default:
                throw new \Exception('Invalid or no environment set : ' . $env);
        }
    }

    public function getEnv()
    {
        return $this->_env;
    }

    public function getSystemLog()
    {
        return $this->_systemLogger;
    }

    private function _loadDI()
    {
        $di = new FactoryDefault();

        $di->set('session', function() {
            $session = new SessionAdapter();
            $session->start();
            return $session;
        }, true); // shared

        $dbConfig = $this->_appConfig->database;
        $di->set('db', function() use ($dbConfig) {
            return new DbAdapter(array(
                'host' => $dbConfig->host,
                'username' => $dbConfig->username,
                'password' => $dbConfig->password,
                'dbname' => $dbConfig->dbname
            ));
        }, true); // shared

        $baseUri = $this->_appConfig->application->baseUri;
        $di->set('url', function() use ($baseUri) {
            $url = new UrlResolver();
            $url->setBaseUri($baseUri);

            return $url;
        });

        $enabledModules = $this->_appConfig->application->modules;
        $defaultModule = $this->_appConfig->application->defaultModule;
        $di->set('router', function() use ($enabledModules, $defaultModule) {
            $router = new Router(false);
            $router->setDefaultModule($defaultModule);

            $router->add('/', array(
                'module' => "frontend",
                'controller' => "index",
                'action' => "index"
            ))->setName('front-index');

            foreach ($enabledModules as $moduleName => $isModuleEnabled) {
                // do not route default module or disabled modules
                if ($defaultModule == $moduleName || true !== $isModuleEnabled) continue;

                $router->add('#^/'.$moduleName.'(|/)$#', array(
                    'module' => $moduleName,
                    'controller' => 'index',
                    'action' => 'index',
                ));

                $router->add('#^/'.$moduleName.'/([a-zA-Z0-9\_]+)[/]{0,1}$#', array(
                    'module' => $moduleName,
                    'controller' => 1,
                ));

                $router->add('#^/'.$moduleName.'[/]{0,1}([a-zA-Z0-9\_]+)/([a-zA-Z0-9\_]+)(/.*)*$#', array(
                    'module' => $moduleName,
                    'controller' => 1,
                    'action' => 2,
                    'params' => 3,
                ));
            }

            return $router;
        });

        $this->setDI($di);
        return $this;
    }

    private function _loadModules()
    {
        if ( empty($this->_appConfig->application->modules) ) {
            throw new \Phalcon\Exception('Unable to register any modules. Check your modules folder and config.');
        }

        try {
            $installedModulesDir = new \DirectoryIterator(PATH_MODULES);
            foreach ($installedModulesDir as $moduleDir) {
                if ( !$moduleDir->isDot() && $moduleDir->isDir() ) {
                    $moduleName = $moduleDir->getFilename();
                    if ( (isset($moduleName)) && true === $this->_appConfig->application->modules[$moduleName]) {
                        $className = APP_NAMESPACE .'\\Modules' . '\\'. \Phalcon\Text::camelize($moduleName) . '\\Module';
                        $classPath = PATH_MODULES . $moduleName . DIRECTORY_SEPARATOR . 'Module.php';
                        if ( !file_exists($classPath) ) {
                            throw new \Phalcon\Exception('Unable to load class file '.$classPath.' for module '.$className);
                        }

                        $this->_modulesConfig[$moduleName] = array(
                            'className' => $className,
                            'path' => $classPath
                        );
                    }
                }
            }

            if ( empty($this->_modulesConfig) ) {
                throw new \Phalcon\Exception('There is no modules configured.');
            }

            $this->registerModules($this->_modulesConfig);
            return $this;
        } catch (\Exception $e) {
            // @todo: log
            throw new \Phalcon\Exception('Error occured while registering modules configuration : '. $e->getMessage());
        }
    }

    private function _setAutoload()
    {
        $loader = new Loader();
        if ( !empty($this->_appConfig->autoload->namespaces)) {
            $ns = array();
            foreach ( $this->_appConfig->autoload->namespaces as $nsName => $nsPath ) {
                $ns[$nsName] = PATH_ROOT . $nsPath;
            }

            $loader->registerNamespaces($ns);
        }

        // $loader->registerDirs(array(PATH_VENDOR, PATH_LIBRARY));
        $loader->register();
        return $this;
    }

    private function _bootstrapLog()
    {
        // Log some things ...
        $this->_systemLogger->debug('* Application bootstrap begin ['.__CLASS__.']');
        $this->_systemLogger->debug('* Registered application namespace: ' . APP_NAMESPACE);
        $this->_systemLogger->debug('* Registered application root path: ' . PATH_ROOT);

        // @todo : add registered namespaces as well
        $this->_systemLogger->debug('* Registered application directories :');
        $this->_systemLogger->debug('    apps : ' . PATH_APPS);
        $this->_systemLogger->debug('    models : ' . PATH_MODELS);
        $this->_systemLogger->debug('    modules : ' . PATH_MODULES);
        $this->_systemLogger->debug('    data : ' . PATH_DATA);
        $this->_systemLogger->debug('    logs : ' . PATH_LOGS);
        $this->_systemLogger->debug('    library : ' . PATH_LIBRARY);
        $this->_systemLogger->debug('    vendor : ' . PATH_VENDOR);
        $this->_systemLogger->debug('');
    }
}