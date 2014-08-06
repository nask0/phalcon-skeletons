<?php
namespace Modules\Frontend;

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
        $loader->registerNamespaces(
                array(
                    'Modules\Frontend\Controllers' => __DIR__ . '/controllers/',

                    // registering global models
                    'Models\Entities' => PATH_MODELS . 'entities' . DIRECTORY_SEPARATOR,
                    'Models\Services' => PATH_MODELS . 'services' . DIRECTORY_SEPARATOR,
                    'Models\Repositories' => PATH_MODELS . 'repositories' . DIRECTORY_SEPARATOR,
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
        //Registering a dispatcher
        $di->set('dispatcher', function() {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('Modules\Frontend\Controllers');
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