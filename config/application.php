<?php
return new \Phalcon\Config(array(
    'debug' => true,
    'database' => array(
        'adapter'  => 'Mysql',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname'   => 'phalcon-tests',
    ),
    'application' => array(
        'baseUri' => '/',
        'autoload' => array()
    )
));