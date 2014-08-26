<?php
/**
 * Application main cycle.
 *
 * --
 * use Phalcon\Logger\Adapter\File as SystemLogger;
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
    die('Unable to determine environment. Check for valid env.php file in config folder ['.PATH_CONFIG .']');
}
$env = require PATH_CONFIG . 'env.php';
if ( !is_string($env) ) {
    die('Unable to determine environment. Check for valid env.php file in config folder ['.PATH_CONFIG .']');
}

// load config according to environment
if ( !file_exists(PATH_CONFIG . $env . '.php') ) {
    die('Unable to load config file ' . PATH_CONFIG . $env.'.php');
}

$appConfig = require_once PATH_CONFIG . 'application.php';
$envConfig = require_once PATH_CONFIG . $env . '.php';
if ( !($appConfig instanceof \Phalcon\Config) || !($envConfig instanceof \Phalcon\Config)) {
    die('Unable to load needed config objects. Check your config folder.');
}
$envConfig->merge($appConfig);

// boot application
require_once PATH_LIBRARY . 'Cod3r/Application/Bootstrap.php';

try {
    $application = new \Cod3r\Application\Bootstrap($env, $envConfig);
    $application->load();
} catch (Phalcon\Exception $e) {
    echo $e->getMessage();
} catch (PDOException $e) {
    echo $e->getMessage();
}
