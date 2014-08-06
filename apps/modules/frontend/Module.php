<?php
namespace PhalconSkeletons\Modules\Frontend;

use Phalcon\Loader;
use Phalcon\Mvc\View;
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
        $loader->registerNamespaces(array(APP_NAMESPACE . '\Modules\Frontend\Controllers' => __DIR__ . '/controllers/'))
               ->register();
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices($di)
    {
        //Registering a dispatcher
        $di->set('dispatcher', function() {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace(APP_NAMESPACE . '\Modules\Frontend\Controllers');
            return $dispatcher;
        });

        /**
         * Setting up the view component
         */
        $di['view'] = function () {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');

            return $view;
        };
    }
}