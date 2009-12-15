<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
session_start();

require_once dirname(__FILE__). '/config.php';
require_once dirname(__FILE__). '/constants.php';

set_include_path(get_include_path() . PATH_SEPARATOR . GENERIS_BASE_PATH.'/..');

include_once 'tao/includes/prepend.php';
?>