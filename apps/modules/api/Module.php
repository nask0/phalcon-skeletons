<?php
namespace PhalconSkeletons\Modules\Api;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use \Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders()
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            APP_NAMESPACE . '\Modules\Api\Controllers' => __DIR__ . '/controllers/',
        ))
        ->register();
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices($di)
    {
        $di->set('dispatcher', function() {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace(APP_NAMESPACE . '\Modules\Api\Controllers');
            return $dispatcher;
        });

        /**
         * Setting up the view component
         */
        $di['view'] = function () {
            $view = new View();
            // $view->setViewsDir(__DIR__ . '/views/');
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
            return $view;
        };

        /**
         * Database connection is created based in the parameters defined in the configuration file

        $di['db'] = function () use ($config) {
            return new DbAdapter(array(
                "host" => $config->database->host,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname" => $config->database->dbname
            ));
        };
        **/
    }
}