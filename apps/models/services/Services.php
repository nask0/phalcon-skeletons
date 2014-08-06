<?php 
namespace PhalconSkeletons\Models\Services;

use PhalconSkeletons\Models\Services\Exceptions;

abstract class Services
{
    public static function getService($name)
    {
        $className = APP_NAMESPACE . "\\Models\\Services\\Service\\{$name}";

        if ( !class_exists($className) ) {
            throw new Exceptions\InvalidServiceException("Class {$className} doesn't exists.");
        }
        
        return new $className();
    }
}