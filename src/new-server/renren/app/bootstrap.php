<?php
error_reporting(E_ALL & ~E_NOTICE | E_STRICT);
//error_reporting(E_ALL);
//error_reporting(0);

date_default_timezone_set('Asia/Shanghai');

//$starttime = getmicrotime();

// define root dir of the application
define('ROOT_DIR', dirname(dirname(__FILE__)));

require (ROOT_DIR . '/app/config/define.php');
set_include_path(LIB_DIR . PATH_SEPARATOR . get_include_path());

require (ROOT_DIR . '/app/config/params.php');

// register autoload class function
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

//init view
$smartyParams = array('left_delimiter' => '{%', 'right_delimiter' => '%}',
                      'plugins_dir' => array('plugins', LIB_DIR . '/MyLib/Smarty/plugins'));

$view = new MyLib_Zend_View_Smarty($smartyParams);
$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($view);
Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);


// setting controller params
$controllerFront = Zend_Controller_Front::getInstance();
$modules = array('default' => 'default/controllers');

foreach ($modules as $module => $path) {
    $controllerFront->addControllerDirectory(MODULES_DIR . '/' . $path, $module);
}

//$controllerFront->registerPlugin(new MyLib_Zend_Controller_Plugin_Auth());
$controllerFront->setParam('noErrorHandler', false);
$controllerFront->throwExceptions(true);

//set_time_limit(4);

function shutdown_handler()
{
  $last_error = error_get_last();

  if ($last_error != null) {
      $error_type = $last_error['type'];
      if ($error_type === E_ERROR || $error_type === E_CORE_ERROR || $error_type === E_COMPILE_ERROR || $error_type === E_USER_ERROR) {
          //err_log('[E_ERROR] ' . $_SERVER['REQUEST_URI']);
          global_error_output();
      }
  }
}

//register_shutdown_function("shutdown_handler");

function global_exception_handler($exception)
{
    err_log($exception->getMessage());

    global_error_output();
}

//set_exception_handler('global_exception_handler');

try {
    $controllerFront->dispatch();
}
catch (Exception $e) {
    err_log($_SERVER['REQUEST_URI'] . ':' . $e->getMessage());
    global_error_output();
}

function global_error_output()
{
    ob_end_clean();

    ob_start();

    header('HTTP/1.1 200 OK');
    header('Content-type: text/html; charset=UTF-8');
    echo '-100';
}

function err_log($msg)
{
	try {
		$log_name = 'error_logger';
	    if (!Zend_Registry::isRegistered($log_name)) {
	        //$writer = new MyLib_Zend_Log_Writer_Stream(LOG_DIR . '/' . $log_name . '.log');
	        $writer = new Zend_Log_Writer_Stream(LOG_DIR . '/' . $log_name . '.log');
	        $logger = new Zend_Log($writer);
	        Zend_Registry::set($log_name, $logger);
	    }
	    else {
	        $logger = Zend_Registry::get($log_name);
	    }

        $logger->log($msg, Zend_Log::ERR);
    }
    catch (Exception $e) {
    	
    }
}

function debug_log($msg)
{
    if (!(defined('ENABLE_DEBUG') && ENABLE_DEBUG)) {
        return;
    }

    try {
	    $log_name = 'debug_logger';
	    if (!Zend_Registry::isRegistered($log_name)) {
	        $writer = new MyLib_Zend_Log_Writer_Stream(LOG_DIR . '/' . $log_name . '.log');
	        $logger = new Zend_Log($writer);
	        Zend_Registry::set($log_name, $logger);
	    }
	    else {
	        $logger = Zend_Registry::get($log_name);
	    }

        $logger->log($msg, Zend_Log::DEBUG);
    }
    catch (Exception $e) {
    }
}

function info_log($msg, $prefix = 'default')
{
	try {
		$log_name = $prefix . '_logger';
	    if (!Zend_Registry::isRegistered($log_name)) {
	        $writer = new Zend_Log_Writer_Stream(LOG_DIR . '/' . $log_name . '.log');
	        $logger = new Zend_Log($writer);
	        Zend_Registry::set($log_name, $logger);
	    }
	    else {
	        $logger = Zend_Registry::get($log_name);
	    }

        $logger->log($msg, Zend_Log::INFO);
    }
    catch (Exception $e) {

    }
}

function getmicrotime()
{
    //list($usec, $sec) = explode(' ', microtime());
    //return ((float) $usec + (float) $sec);
    return microtime(true);
}

function getexecutetime()
{
    $starttime = Zend_Registry::get('StartTime');
    $stoptime = getmicrotime();

    return round($stoptime - $starttime, 10);
}

function getTime()
{
	return $_SERVER['REQUEST_TIME'];
}
