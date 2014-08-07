<?php 
namespace Models\Repositories;

use Models\Repositories\Exceptions;

abstract class Repositories
{
    protected static $_repos = array();

    public static function getRepository($name)
    {
        $name = (string) $name;

        if ( isset(self::$_repos[$name]) ) {
            echo 'cache hit<br />';
            return self::$_repos[$name];
        }

        $className = "\\Models\\Repositories\\Repository\\{$name}";
        if ( ! class_exists($className)) {
            throw new Exceptions\InvalidRepositoryException("Repository {$className} doesn't exists.");
        }

        self::$_repos[$name] = new $className();
        return self::$_repos[$name];
    }
}