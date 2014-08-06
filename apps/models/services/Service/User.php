<?php
namespace Models\Services\Service;
use Models\Repositories\Repositories as Repositories;

class User
{
    public function getLast()
    {
        return Repositories::getRepository('User')->getLast();
    }
}