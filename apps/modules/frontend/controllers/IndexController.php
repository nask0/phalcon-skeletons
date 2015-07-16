<?php
namespace Modules\Frontend\Controllers;

use Models\Services\Services as Services;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        /*
        try {
            $this->view->users = Services::getService('Users')->getLast();
        } catch (\Exception $e) {
            $this->flash->error($e->getMessage());
        }
        */
    }
}

