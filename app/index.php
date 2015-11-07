<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
define('IN_MOBILE', true);
require '../framework/bootstrap.inc.php';
load()->app('common');
load()->app('template');
load()->model('app');
require IA_ROOT . '/app/common/bootstrap.app.inc.php';

$acl = array(
	'home' => array(
		'default' => 'home',
	),
	'mc' => array(
		'default' => 'home'
	)
);

$settings = setting_load('copyright');
if ($settings['copyright']['status'] == 1) {
	$_W['siteclose'] = true;
	message('抱歉，站点已关闭，关闭原因：' . $settings['copyright']['reason']);
}

$multiid = intval($_GPC['t']);
if(empty($multiid)) {
		$setting = uni_setting();
	$multiid = intval($setting['default_site']);
	unset($setting);
}

$multi = pdo_fetch("SELECT * FROM ".tablename('site_multi')." WHERE id=:id AND uniacid=:uniacid", array(':id' => $multiid, ':uniacid' => $_W['uniacid']));
if(empty($multi)) {
	exit('没有找到指定微站.');
}
$multi['site_info'] = @iunserializer($multi['site_info']);
$multi['quickmenu'] = @iunserializer($multi['quickmenu']);

$styleid = !empty($_GPC['s']) ? intval($_GPC['s']) : intval($multi['styleid']);
$style = pdo_fetch("SELECT * FROM ".tablename('site_styles')." WHERE id = :id AND uniacid=:uniacid", array(':id' => $styleid, ':uniacid' => $_W['uniacid']));
if (empty($style)) {
	exit('没有找到指定微站风格.');
}

$templates = uni_templates();
$templateid = intval($style['templateid']);
$template = $templates[$templateid];

$_W['template'] = !empty($template) ? $template['name'] : 'default';
$_W['styles'] = array();

if(!empty($template)) {
	$sql = "SELECT `variable`, `content` FROM " . tablename('site_styles_vars') . " WHERE `uniacid`=:uniacid AND `styleid`=:styleid";
	$params = array();
	$params[':uniacid'] = $_W['uniacid'];
	$params[':styleid'] = $styleid;
	$stylevars = pdo_fetchall($sql, $params);
	if(!empty($stylevars)) {
		foreach($stylevars as $row) {
			if (strexists($row['variable'], 'img')) {
				$row['content'] = tomedia($row['content']);
			}
			$_W['styles'][$row['variable']] = $row['content'];
		}
	}
	unset($stylevars, $row, $sql, $params);
}

$_W['page'] = array();
$_W['page']['title'] = $multi['title'];
if(is_array($multi['site_info'])) {
	$_W['page'] = array_merge($_W['page'], $multi['site_info']);
}
unset($multi, $styleid, $style, $templateid, $template, $templates);

$controllers = array();
$handle = opendir(IA_ROOT . '/app/source/');
if(!empty($handle)) {
	while($dir = readdir($handle)) {
		if($dir != '.' && $dir != '..') {
			$controllers[] = $dir;
		}
	}
}
if(!in_array($controller, $controllers)) {
	$controller = 'home';
}
$init = IA_ROOT . "/app/source/{$controller}/__init.php";
if(is_file($init)) {
	require $init;;
}

$actions = array();
$handle = opendir(IA_ROOT . '/app/source/' . $controller);
if(!empty($handle)) {
	while($dir = readdir($handle)) {
		if($dir != '.' && $dir != '..' && strexists($dir, '.ctrl.php')) {
			$dir = str_replace('.ctrl.php', '', $dir);
			$actions[] = $dir;
		}
	}
}

if(empty($actions)) {
	header("location: index.php?i={$_W['uniacid']}&j={$_W['acid']}&c=home?refresh");
}
if(!in_array($action, $actions)) {
	$action = $acl[$controller]['default'];
}
if(!in_array($action, $actions)) {
	$action = $actions[0];
}

init_quickmenus($multiid);
require _forward($controller, $action);

function _forward($c, $a) {
	$file = IA_ROOT . '/app/source/' . $c . '/' . $a . '.ctrl.php';
	return $file;
}