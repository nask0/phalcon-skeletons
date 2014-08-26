<?php
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
        'enableAllModules' => false,
        'defaultModule' => 'frontend'
    )
));