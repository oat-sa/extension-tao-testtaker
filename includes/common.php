<?php

session_start();


/**
 * this code is executed before all other script
 */

# constants definition
define('HTTP_GET', 		'GET');
define('HTTP_POST', 	'POST');
define('HTTP_PUT', 		'PUT');
define('HTTP_DELETE', 	'DELETE');
define('HTTP_HEAD', 	'HEAD');

# all error
error_reporting(E_ALL);

# xdebug custom error reporting
if (function_exists("xdebug_enable"))  {
	xdebug_enable();
}

require_once 	dirname(__FILE__). "/config.php";
require_once 	dirname(__FILE__). "/constants.php";
require 		dirname(__FILE__).'/../../PHP-Framework/clearbricks/common/_main.php';
require_once 	dirname(__FILE__).'/../../generis/common/common.php';


/**
 * @function tao_autoload
 * permits to include classes automatically using the ARGOUML class naming convention
 * @param 	string		$pClassName		Name of the class
 */
function tao_autoload($pClassName) {
	
	global $__autoload;
	
	if ( isset($__autoload[$pClassName])) {
		require_once $__autoload[$pClassName];
	}
	else {
		$split = split("_",$pClassName);
		$path = GENERIS_BASE_PATH.'/';
		for ( $i = 0 ; $i<sizeof($split)-1 ; $i++){
			$path .= $split[$i].'/';
		}
		
	    $filePath = $path . 'class.'.$split[sizeof($split)-1] . '.php';
		if (file_exists($filePath)){
			require_once $filePath;
		}
	}
}

/**
 * @function fw_autoload
 * permits to include classes automatically
 * @param 	string		$pClassName		Name of the class
 */
function fw_autoload($pClassName) {
	if (isset($GLOBALS['classpath']) && is_array($GLOBALS['classpath'])) {
		foreach($GLOBALS['classpath'] as $path) {
			if (file_exists($path. $pClassName . '.class.php')) {
    			require_once $path . $pClassName . '.class.php';
    			break;
			}
		}
	}
}

spl_autoload_register("fw_autoload");
spl_autoload_register("tao_autoload");
spl_autoload_register("Plugin::pluginClassAutoLoad");

set_include_path(get_include_path() . PATH_SEPARATOR . GENERIS_BASE_PATH);

core_control_FrontController::connect(API_LOGIN, API_PASS, API_MODULE);
core_kernel_classes_Session::singleton()->setLg($GLOBALS['lang']);
core_kernel_classes_Session::singleton()->defaultLg = 'en';

?>