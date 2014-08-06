<?php
namespace PhalconSkeletons\Modules\Frontend\Controllers;

use \PhalconSkeletons\Models\Services\Services as Services;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        try {
            $this->view->users = Services::getService('User')->getLast();
        } catch (\Exception $e) {
            $this->flash->error($e->getMessage());
        }
    }
}

