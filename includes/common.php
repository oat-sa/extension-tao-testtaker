<?php



require_once 	dirname(__FILE__). "/config.php";
require_once 	dirname(__FILE__). "/constants.php";



set_include_path(get_include_path() . PATH_SEPARATOR . GENERIS_BASE_PATH);

core_control_FrontController::connect(API_LOGIN, API_PASS, API_MODULE);
core_kernel_classes_Session::singleton()->setLg($GLOBALS['lang']);
core_kernel_classes_Session::singleton()->defaultLg = 'en';

?>