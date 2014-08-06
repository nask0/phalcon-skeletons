<?php
return new \Phalcon\Config(array(
    'debug' => true,
    'baseUri' => '/',
    // enable/disable modules from here
    'modules' => array('frontend', 'api'),
    'defaultModule' => 'frontend',
    'autoload' => array(
        'folders' => array(),
        'classes' => array(),
        'namespaces' => array(),
    ),
    'database' => array(
        'adapter'  => 'Mysql',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname'   => 'phalcon-tests',
    ),
));