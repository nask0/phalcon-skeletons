<?php 
namespace Models\Repositories;

use PhalconSkeletons\Models\Repositories\Exceptions;

abstract class Repositories
{
    public static function getRepository($name)
    {
        $className = "\\Models\\Repositories\\Repository\\{$name}";

        if ( ! class_exists($className)) {
            throw new Exceptions\InvalidRepositoryException("Repository {$className} doesn't exists.");
        }
        
        return new $className();
    }
}