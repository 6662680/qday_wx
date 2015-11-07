<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$dos = array('shortcut');
$do = in_array($do, $dos) ? $do : 'shortcut';

if($do == 'shortcut') {
	$templates = array();
	$path = IA_ROOT . '/app/themes/quick';
	if (is_dir($path)) {
		if ($handle = opendir($path)) {
			while (false !== ($templatepath = readdir($handle))) {
				$ext = pathinfo($templatepath);
				$ext = $ext['extension'];
				if ($templatepath != '.' && $templatepath != '..' && !empty($ext)) {
					$pathinfo = pathinfo($templatepath);
					$templates[] = $pathinfo['filename'];
				}
			}
		}
	}
	$template = $_GPC['file'];
	$template = in_array($template, $templates) ? $template : 'default';
	load()->model('app');
	$_W['quickmenu']['menus'] = app_navs('shortcut', $multiid);
	if(empty($_W['quickmenu']['menus'])) {
		$_W['quickmenu']['menus'] = array(
			array('name' => '菜单一', 'css' => array('icon' => array('icon' => 'fa-home'))),
			array('name' => '菜单二', 'css' => array('icon' => array('icon' => 'fa-book'))),
			array('name' => '菜单三', 'css' => array('icon' => array('icon' => 'fa-pencil'))),
			array('name' => '菜单四', 'css' => array('icon' => array('icon' => 'fa-cog'))),
			array('name' => '菜单五', 'css' => array('icon' => array('icon' => 'fa-flag'))),
		);
	}
	$_W['quickmenu']['template'] = '../quick/' . $template;
	template('../default/common/header');
	template('../default/common/footer');
}
