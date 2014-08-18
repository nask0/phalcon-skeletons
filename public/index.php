<?php
/**
 * Application main cycle.
 */
ini_set('log_errors', true);
ini_set('report_memleaks', true);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

// path definitions
define('PATH_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);
chdir(PATH_ROOT);

define('PATH_APPS', PATH_ROOT. 'apps' . DIRECTORY_SEPARATOR);
define('PATH_DATA', PATH_ROOT . 'data' . DIRECTORY_SEPARATOR);
define('PATH_LOGS', PATH_DATA . 'logs' . DIRECTORY_SEPARATOR);
define('PATH_CONFIG', PATH_ROOT. 'config' . DIRECTORY_SEPARATOR);
define('PATH_LIBRARY', PATH_ROOT . 'library' . DIRECTORY_SEPARATOR);
define('PATH_VENDOR', PATH_ROOT . 'vendor' . DIRECTORY_SEPARATOR);
define('PATH_MODELS', PATH_APPS . 'models' . DIRECTORY_SEPARATOR);
define('PATH_MODULES', PATH_APPS . 'modules' . DIRECTORY_SEPARATOR);

// load env config
if ( !file_exists(PATH_CONFIG . 'env.php') ) {
    die('Unable to determine environment. Check env.php file in config folder ['.PATH_CONFIG .']');
}
$env = require_once PATH_CONFIG . 'env.php';

// load config according to environment
if ( !file_exists(PATH_CONFIG . 'env.php') ) {
    die('Unable to load config file ' . PATH_CONFIG . $env.'.php');
}
$config = require_once PATH_CONFIG . $env . '.php';
if ( !($config instanceof \Phalcon\Config) ) {
    die('Unable to load config object from file config/'.$env.'.php');
}

// application specific definitions
define('APP_ENV', $env);
define('APP_NAMESPACE', \Phalcon\Text::camelize(basename(PATH_ROOT)));

// load application bootstrap class
$appFile = PATH_APPS . 'Bootstrap.php';
if ( !file_exists($appFile)) {
    die('Unable to find application file in : ' . $appFile);
} else {
    require_once $appFile;
}

try {
    $appClass = '\\Apps\\Bootstrap';
    if ( !class_exists($appClass) ) {
        die('Unable to find application class '.$appClass . ' in ' . PATH_APPS . 'Bootstrap.php');
    }

    $application = new $appClass();
    $application->setConfig($config)
                ->setEnv($env)
                ->load();

} catch (Phalcon\Exception $e) {
    echo $e->getMessage();
} catch (PDOException $e) {
    echo $e->getMessage();
}