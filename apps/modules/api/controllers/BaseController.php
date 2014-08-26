<?php
namespace Modules\Api\Controllers;

use Phalcon\Mvc\Controller;

class BaseController extends Controller
{
    public function afterExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher)
    {
        $data = $dispatcher->getReturnedValue();

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setContent(json_encode($data, JSON_UNESCAPED_SLASHES));
        $this->response->send();
        exit;
    }
}