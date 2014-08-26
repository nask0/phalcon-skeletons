<?php
// Note that this configuration may be overwritten by the environment config file (e.g. local.php / development.php)
return new \Phalcon\Config(array(
    'application' => array(
        // explicitly set application debug mode to overwrite environment settings
        'debug' => true,
        'baseUri' => '/',
        // modules configuration
        'modules' => array(
            'api' => true,
            'frontend' => true
        ),
        'defaultModule' => 'frontend'
    ),
));