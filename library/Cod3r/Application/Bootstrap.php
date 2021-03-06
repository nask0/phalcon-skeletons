<?php
/**
 * Application bootstrap class that is responsible for initializing of shared services, modules, models and so on.
 * Inspired from Multiple-Service-Layer sample architecture (see https://github.com/phalcon/mvc).
 * Use some kind of "Shared-Service-Layer" for database crud/orm whatever you like to call it.
 */
namespace Cod3r\Application;

// Services are globally registered in this file
use Phalcon\Exception as PhalconException;
use Phalcon\Loader;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Logger\Adapter\File as SystemLogger;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;

class Bootstrap extends \Phalcon\Mvc\Application
{
    const ENV_LIVE = 'live';
    const ENV_LOCAL = 'local';
    const ENV_STAGING = 'staging';
    const ENV_TESTING = 'testing';
    const ENV_DEVELOPMENT = 'development';

    private $_env = '';
    private $_debug = true;
    private $_appConfig = null;
    private $_systemLogger = null;
    private $_modulesConfig = array();

    public function __construct($env, \Phalcon\Config $config, $syslog = null, $di = null)
    {
        $this->_appConfig = $config;
        $this->setEnv($env);

        if ( !($syslog instanceof \Phalcon\Logger\Adapter\File) ) {
            $this->_systemLogger = new SystemLogger(PATH_LOGS . 'system.log');
        } else {
            $this->_systemLogger = $syslog;

        }
        $this->_bootstrapLog();

        parent::__construct($di);
    }

    /**
     * Application main cycle - setups auto load, modules, models,
     * application services and so on.
     *
     * @throws \Exception
     */
    public function load()
    {
        try {
            $this->_setAutoload();
            $this->_loadDI();
            $this->_loadModules();

            $this->handle()->send();
        } catch(\Exception $e) {
            throw new PhalconException('Unable to load application : '.$e->getMessage());
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
     * Application config getter
     *
     * @return object \Phalcon\Config
     */
    public function getConfig()
    {
        return $this->_appConfig();
    }


    /**
     * Set application environment.
     *
     * @param string $env
     * @return object $this
     * @throws \Phalcon\Exception
     */
    public function setEnv($env)
    {
        switch ( $env ) {
            case self::ENV_LOCAL:
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
                throw new \Phalcon\Exception('Invalid or no environment set : ' . $env);
        }
    }

    public function getEnv()
    {
        return $this->_env;
    }

    /**
     * Get system logger.
     *
     * @return SystemLogger
     */
    public function getSystemLog()
    {
        return $this->_systemLogger;
    }

    /**
     * Setup default dependency injector.
     *
     * @return object Bootstrap
     */
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
        });

        // Setting UrlResolver - this components aids in the generation of: URIs, URLs and Paths
        $baseUri = $this->_appConfig->application->baseUri;
        $di->set('url', function() use ($baseUri) {
            $url = new UrlResolver();
            $url->setBaseUri($baseUri);

            return $url;
        }, true);

        // Register default routes
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

        // set dispatcher for use in modules
        $di->set('dispatcher', function() {
            $dispatcher = new Dispatcher();
            return $dispatcher;
        }, true);

        $this->setDI($di);
        return $this;
    }

    /**
     * Load registered modules.
     *
     * @return object \Phalcon\Mvc\Application
     * @throws \Phalcon\Exception
     */
    private function _loadModules()
    {
        if ( empty($this->_appConfig->application->modules) ) {
            throw new PhalconException('Unable to register any modules. Check your modules folder and config.');
        }

        try {
            $installedModulesDir = new \DirectoryIterator(PATH_MODULES);
            foreach ($installedModulesDir as $moduleDir) {
                if ( !$moduleDir->isDot() && $moduleDir->isDir() ) {
                    $moduleName = $moduleDir->getFilename();
                    if ( (isset($moduleName)) && true === $this->_appConfig->application->modules[$moduleName]) {
                        $className = '\\Modules' . '\\'. \Phalcon\Text::camelize($moduleName) . '\\Module';
                        $classPath = PATH_MODULES . $moduleName . DIRECTORY_SEPARATOR . 'Module.php';
                        if ( !file_exists($classPath) ) {
                            throw new \Phalcon\Exception('Unable to load class '.$className.' from file: '.$classPath);
                        }

                        $this->_modulesConfig[$moduleName] = array(
                            'className' => $className,
                            'path' => $classPath
                        );
                    }
                }
            }

            if ( empty($this->_modulesConfig) ) {
                throw new PhalconException('There is no modules configured.');
            }

            // register enabled modules to application
            $this->registerModules($this->_modulesConfig);
        } catch (\Exception $e) {
            // @todo: log
            throw new PhalconException('Error occured while registering modules configuration : '. $e->getMessage());
        }
    }

    /**
     * Set auto loading
     *
     * @return object Bootstrap
     * @todo: consider autoloading of_VENDOR and _LIBRARY paths.
     */
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

        $loader->register();
    }

    private function _bootstrapLog()
    {
        // Log some things ...
        $this->_systemLogger->debug('* Application bootstrap begin ['.__CLASS__.']');
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
