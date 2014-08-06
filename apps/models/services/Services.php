<?php 
namespace Models\Services;

use Models\Services\Exceptions;

abstract class Services
{
    public static function getService($name)
    {
        $className = "\\Models\\Services\\Service\\{$name}";
        if ( !class_exists($className) ) {
            throw new Exceptions\InvalidServiceException("Class {$className} doesn't exists.");
        }
        
        return new $className();
    }
}