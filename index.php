<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once dirname(__FILE__). '/../tao/includes/class.Bootstrap.php';

$options = array();
if (substr(Context::getInstance()->getModuleName(),0,3) == 'SaS') {
	$options = array(
		'constants' => array('wfEngine')
	);
}
$bootStrap = new BootStrap('taoSubjects', $options);
$bootStrap->start();
$bootStrap->dispatch();
?>