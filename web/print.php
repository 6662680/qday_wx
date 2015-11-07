<?php
/**
 * 微站管理
 * [WDL] Copyright (c) 2013 B2CTUI.COM
 */
define('IN_SYS', true);
require '../framework/bootstrap.inc.php';
require IA_ROOT . '/web/common/bootstrap.sys.inc.php';
load()->web('common');
load()->web('template');
 
header('Content-Type: text/html; charset=GBK');

$modulename ='weisrc_dish';

$site = WeUtility::createModuleSite($modulename);
if(!is_error($site)) {
 	$method = 'doWebPrint';
 	//$site->module = array_merge($_W['modules'][$modulename], $_W['account']['modules'][$_W['modules'][$modulename]['mid']]);
	$site->weid = $_W['uniacid'];
	$site->inMobile = false;
	if (method_exists($site, $method)) {
		exit($site->$method());
	}
}

exit("访问的方法 {$method} 不存在.");
