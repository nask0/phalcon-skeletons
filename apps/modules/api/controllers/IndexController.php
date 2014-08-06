<?php
namespace Modules\Api\Controllers;

class IndexController extends BaseController
{
    public function indexAction()
    {
        try {
            return array('error' => 'Nothing to see here, try api/users instead :)');
        } catch (\Exception $e) {
            return array('error' => 1001);
        }
    }
}