<?php
namespace Modules\Api;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher;
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
            // 'Modules\Api\Controllers' => __DIR__ . '/controllers/',

            // @todo : register and local models if any
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
        $di->set('dispatcher', function() {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('Modules\Api\Controllers');
            return $dispatcher;
        });

        /**
         * Setting up the view component
         */
        $di['view'] = function () {
            $view = new View();
            $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
            $view->disable();
            return $view;
        };
    }
}