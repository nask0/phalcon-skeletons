<?php
namespace PhalconSkeletons\Models\Services\Service;
use PhalconSkeletons\Models\Repositories\Repositories as Repositories;

class User
{
    public function getLast()
    {
        return Repositories::getRepository('User')->getLast();
    }
}