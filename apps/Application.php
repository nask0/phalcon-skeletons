<?php
/**
 * Application bootstrap class that is responsible for initializing of shared services, modules, models and so on.
 * Inspired from Multiple-Service-Layer sample architecture (see https://github.com/phalcon/mvc).
 * Use some kind of "Shared-Service-Layer" for database crud/orm whatever you like to call it.
 */
namespace PhalconSkeletons;

// Services are globally registered in this file
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
    // @todo: get those values from application (global) config
    protected $debug = true;
    protected $appMode = '';
    protected $appModes = array('development', 'testing', 'staging', 'live');

    protected $modulesConfig = array();
    protected $appDefaultModule = '';

    /**
     * @param string $defaultModule
     * @throws \Exception
     * @todo - still experimental variant, but looks good :P
     */
    public function init($defaultModule = 'frontend')
    {
        try {
            $this->appDefaultModule = (string) $defaultModule;
            $appConfig = include PATH_ROOT . 'config' . DIRECTORY_SEPARATOR . 'application.php';

            // Log some things ...
            $systemLogger = new SystemLogger(PATH_LOGS . 'system.log');

            $systemLogger->debug('* Application bootstrap begin ['.__CLASS__.']');
            $systemLogger->debug('* Registered application namespace: ' . APP_NAMESPACE);
            $systemLogger->debug('* Registered application root path: ' . PATH_ROOT);

            // @todo : add registered namespaces as well
            $systemLogger->debug('* Registered application directories :');
            $systemLogger->debug('    apps : ' . PATH_APPS);
            $systemLogger->debug('    models : ' . PATH_MODELS);
            $systemLogger->debug('    modules : ' . PATH_MODULES);
            $systemLogger->debug('    data : ' . PATH_DATA);
            $systemLogger->debug('    logs : ' . PATH_LOGS);
            $systemLogger->debug('    library : ' . PATH_LIBRARY);
            $systemLogger->debug('    vendor : ' . PATH_VENDOR);
            $systemLogger->debug('');

            // Init autoloaders
            $systemLogger->debug('* Initializing autoloaders');

            $loader = new Loader();
            $loader->registerNamespaces(array(
                'Phalcon' => PATH_LIBRARY . 'Phalcon' . DIRECTORY_SEPARATOR,
                APP_NAMESPACE . '\Models\Entities' => PATH_MODELS . 'entities' . DIRECTORY_SEPARATOR,
                APP_NAMESPACE . '\Models\Services' => PATH_MODELS . 'services' . DIRECTORY_SEPARATOR,
                APP_NAMESPACE . '\Models\Repositories' => PATH_MODELS . 'repositories' . DIRECTORY_SEPARATOR,
            ))
            ->registerDirs(array(
                PATH_VENDOR,
                PATH_LIBRARY
            ))
            ->register();
            // var_dump($loader); exit;

            // Init a DI
            $systemLogger->debug('* Initializing Dependency Injector');
            $di = new \Phalcon\DI\FactoryDefault();

            $systemLogger->debug('** Setting up config service [appConfig] in DI');
            $di->set('appConfig', function() use ($appConfig) {
                return $appConfig;
            }, true); // shared

            $systemLogger->debug('** Setting up session service [session] in DI');
            $di->set('session', function() {
                $session = new SessionAdapter();
                $session->start();
                return $session;
            }, true); // shared

            $systemLogger->debug('** Setting up database service [db] in DI');
            $di->set('db', function () use ($appConfig) {
                return new DbAdapter(array(
                    "host" => $appConfig->database->host,
                    "username" => $appConfig->database->username,
                    "password" => $appConfig->database->password,
                    "dbname" => $appConfig->database->dbname
                ));
            }, true); // shared

            $systemLogger->debug('** Setting up url service [url] in DI');
            $di->set('url', function () {
                $url = new UrlResolver();
                $url->setBaseUri('/');

                return $url;
            });

            $systemLogger->debug('** Setting up router service [router] in DI');
            $modulesConfig = $this->_getModulesConfig();
            $di->set('router', function() use ($defaultModule, $modulesConfig) {
                $router = new Router(false);
                $router->setDefaultModule($defaultModule);

                $router->add('/', array(
                    'module' => "frontend",
                    'controller' => "index",
                    'action' => "index"
                ))->setName('front-index');

                foreach ($modulesConfig as $moduleName => $module) {
                    // do not route default module
                    if ($defaultModule == $moduleName) continue;

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

            $systemLogger->debug('** Dependance Injector is ready to use');
            $this->setDI($di);

            $systemLogger->debug('* Register application modules');
            $this->registerModules($modulesConfig);

            $systemLogger->debug('* Application bootstrap complete');
            $systemLogger->debug('* Processing request and return response');
            $this->handle()->send();

            // var_dump(Services::getService('User')->getLast()); exit;
            // var_dump($di->get('router')); exit;
            // var_dump($this->_getModulesConfig(), get_include_path(), get_required_files(), $loader, get_declared_classes());
        } catch(\Exception $e) {
            throw new \Exception($e);
        }
    }

    private function _registerDefaultServices()
    {

    }

    private function _getModulesConfig()
    {
        try {
            $installedModulesDir = new \DirectoryIterator(PATH_MODULES);
            foreach ($installedModulesDir as $moduleDir) {
                if (!$moduleDir->isDot() && $moduleDir->isDir()) {
                    $moduleName = $moduleDir->getFilename();
                    $className = APP_NAMESPACE .'\\Modules' . '\\'. \Phalcon\Text::camelize($moduleName) . '\\Module';
                    $classPath = PATH_MODULES . $moduleName . DIRECTORY_SEPARATOR . 'Module.php';

                    $this->modulesConfig[$moduleName] = array(
                        'className' => $className,
                        'path' => $classPath
                    );
                }
            }
            return $this->modulesConfig;
        } catch (\Exception $e) {
            die('Unable to register installed modules !');
        }
    }
}