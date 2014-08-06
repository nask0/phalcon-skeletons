<?php
namespace PhalconSkeletons\Modules\Frontend\Controllers;

use \PhalconSkeletons\Models\Services\Services as Services;

class UsersController extends ControllerBase
{
    public function indexAction()
    {
        try {
            $this->view->users = Services::getService('User')->getLast();
        } catch (\Exception $e) {
            return array('error' => $e->getMessage());
            // $this->flash->error($e->getMessage());
        }
    }
}

