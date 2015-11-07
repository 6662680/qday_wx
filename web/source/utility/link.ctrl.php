<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$callback = $_GPC['callback'];
load()->model('module');

$modulemenus = array();
$modules = uni_modules();
foreach($modules as &$module) {
	if($module['type'] == 'system') {
		continue;
	}
	$entries = module_app_entries($module['name'], array('home', 'profile', 'shortcut', 'function', 'cover'));
	if(empty($entries)) {
		continue;
	}
	$module['cover'] = $entries['cover'];
	$module['home'] = $entries['home'];
	$module['profile'] = $entries['profile'];
	$module['shortcut'] = $entries['shortcut'];
	$module['function'] = $entries['function'];
	if($module['type'] == '') {
		$module['type'] = 'other';
	}
	$modulemenus[$module['type']][$module['name']] = $module;
}
$modtypes = module_types();

$sysmenus = array(
	array('title'=>'微站首页','url'=> murl('home')),
	array('title'=>'个人中心','url'=> murl('mc')),
);

$multis = pdo_fetchall('SELECT id,title FROM ' . tablename('site_multi') . ' WHERE uniacid = :uniacid AND status != 0', array(':uniacid' => $_W['uniacid']));
if(!empty($multis)) {
	foreach($multis as $multi) {
		$multimenus[] = array('title' => $multi['title'], 'url' => murl('home', array('t' => $multi['id'])));
	}
}

$linktypes = array(
	'cover' => '封面链接',
	'home' => '微站首页导航',
	'profile'=>'微站个人中心导航',
	'shortcut' => '微站快捷功能导航',
	'function' => '微站独立功能'
);

template('utility/link');
