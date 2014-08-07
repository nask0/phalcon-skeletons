<?php
namespace Models\Services\Service;
use Models\Repositories\Repositories as Repositories;

class Users
{
    public function getLast()
    {
        return Repositories::getRepository('Users')->getLastFive();
    }
}