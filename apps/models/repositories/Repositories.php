<?php 
namespace PhalconSkeletons\Models\Repositories;

use PhalconSkeletons\Models\Repositories\Exceptions;

abstract class Repositories
{
    public static function getRepository($name)
    {
        $className = APP_NAMESPACE . "\\Models\\Repositories\\Repository\\{$name}";

        if ( ! class_exists($className)) {
            throw new Exceptions\InvalidRepositoryException("Repository {$className} doesn't exists.");
        }
        
        return new $className();
    }
}