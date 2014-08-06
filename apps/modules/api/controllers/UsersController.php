<?php
namespace Modules\Api\Controllers;

use \Models\Services\Services as Services;

class UsersController extends BaseController
{
    public function indexAction()
    {
        try {
            return $this->view->users = Services::getService('User')->getLast();
        } catch (\Exception $e) {
            return array('error' => $e->getMessage());
        }
    }
}

