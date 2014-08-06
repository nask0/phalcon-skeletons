<?php
/**
 * Application main cycle.
 */
use Phalcon\Logger\Adapter\File as SystemLogger;

// @todo
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('PATH_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);
chdir(PATH_ROOT);

// @todo - i cannot see better variant for this for now?
define('APP_NAMESPACE', \Phalcon\Text::camelize(basename(PATH_ROOT)));

define('PATH_APPS', PATH_ROOT. 'apps' . DIRECTORY_SEPARATOR);
define('PATH_DATA', PATH_ROOT . 'data' . DIRECTORY_SEPARATOR);
define('PATH_LOGS', PATH_DATA . 'logs' . DIRECTORY_SEPARATOR);
define('PATH_LIBRARY', PATH_ROOT . 'library' . DIRECTORY_SEPARATOR);
define('PATH_VENDOR', PATH_ROOT . 'vendor' . DIRECTORY_SEPARATOR);
define('PATH_MODELS', PATH_APPS . 'models' . DIRECTORY_SEPARATOR);
define('PATH_MODULES', PATH_APPS . 'modules' . DIRECTORY_SEPARATOR);

try {
    // initialize application bootstrap class
    $appFile = PATH_APPS . 'Application.php';
    if ( !file_exists($appFile)) {
        throw new \Exception('Unable to find application file in : ' . $appFile);
    }

    require_once  $appFile;

    $appClass = APP_NAMESPACE . '\\Application';
    if ( !class_exists($appClass) ) {
        throw new \Phalcon\Exception('Unable to find application class '.$appClass . ' in ' . PATH_APPS . 'Application.php');
    }

    $application = new $appClass();
    $application->init();

} catch (Phalcon\Exception $e) {
    echo $e->getMessage();
} catch (PDOException $e) {
    echo $e->getMessage();
}