<?php
namespace PhalconSkeletons\Modules\Api\Controllers;

use Phalcon\Mvc\Controller;

class BaseController extends Controller
{
    public function beforeExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher)
    {
        // $this->response->setContentType('application/json', 'UTF-8');
        // var_dump($dispatcher, get_class_methods($dispatcher), get_class_methods($response));
        // die('beforeExecuteRoute');
    }
    
    public function afterExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher)
    {
        $data = $dispatcher->getReturnedValue();
        // var_dump($data); exit;
        // var_dump(json_encode($data)); exit;

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data, JSON_UNESCAPED_SLASHES));
        $this->response->send();
        exit;
    }
}