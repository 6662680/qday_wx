<?php

/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
function cache_build_template() {
	load()->func('file');
	rmdirs(IA_ROOT . '/data/tpl', true);
}


function cache_build_setting() {
	$sql = 'SELECT * FROM ' . tablename('core_settings');
	$setting = pdo_fetchall($sql, array(), 'key');
	if(is_array($setting)) {
		foreach($setting as $k => $v) {
			$setting[$v['key']] = iunserializer($v['value']);
		}
		cache_write("setting", $setting);
	}
}


function cache_build_modules() {
	$modules = pdo_fetchall("SELECT * FROM " . tablename('modules') . ' ORDER BY `mid` ASC', array(), 'name');
	$pMenus = array();
	$sMenus = array();
	if (!empty($modules)) {
		foreach ($modules as $mid => &$module) {
			if (!empty($module['subscribes'])) {
				$module['subscribes'] = iunserializer($module['subscribes']);
			}
			if (!empty($module['handles'])) {
				$module['handles'] = iunserializer($module['handles']);
			}
			if (!empty($module['options'])) {
				$module['options'] = iunserializer($module['options']);
			}
			if (!empty($module['platform_menus'])) {
				$module['platform_menus'] = iunserializer($module['platform_menus']);
				if(is_array($module['platform_menus']) && !empty($module['platform_menus'])) {
					foreach($module['platform_menus'] as $row) {
						$pMenus[] = array(
							'title' => $row['title'],
							'do' => $row['do'],
							'name' => $module['name']
						);
					}
				}
			}
			if (!empty($module['site_menus'])) {
				$module['site_menus'] = iunserializer($module['site_menus']);
				if(is_array($module['site_menus']) && !empty($module['site_menus'])) {
					foreach($module['site_menus'] as $row) {
						$sMenus[] = array(
							'title' => $row['title'],
							'do' => $row['do'],
							'name' => $module['name']
						);
					}
				}
			}
		}
	}
	cache_write('menus:platform', $pMenus);
	cache_write('menus:site', $sMenus);
	cache_write('modules', $modules);
}

function cache_build_hook() {
	global $_W;
	if (empty($_W['modules'])) {
		return false;
	}
	foreach ($_W['modules'] as $mid => $module) {
		$file = IA_ROOT . "/source/modules/{$module['name']}/processor.php";
		if (!file_exists($file)) {
			continue;
		}
		include_once $file;
	}

	$classes = get_declared_classes();
	$classnames = $hooks =array();
	$namekey = 'ModuleProcessor';
	$namekeyLen = strlen($namekey);
	
	foreach($classes as $classname) {
		if(substr($classname, -$namekeyLen) == $namekey) {
			$classnames[] = $classname;
		}
	}
	foreach($classnames as $index => $classname) {
		$methods = get_class_methods($classname);
		foreach($methods as $funcname) {
			preg_match('/hook(.*)/', $funcname, $match);
			if (empty($match[1])) {
				continue;
			}
			$hookname = strtolower($match[1]);
			$modulename = strtolower(str_replace($namekey, '', $classname));
			if (in_array($modulename, array_keys($_W['modules']))) {
				$hooks[$hookname][] = array($modulename, $funcname);
			}
		}
	}
	if (!empty($hooks)) {
		cache_write("hooks", $hooks);
	}
}


function cache_build_users_struct() {
	$struct = array();
	$result = pdo_fetchall("SHOW COLUMNS FROM ".tablename('mc_members'));
	if (!empty($result)) {
		foreach ($result as $row) {
			$struct[] = $row['Field'];
		}
		cache_write('usersfields', $struct);
	}
	return $struct;
}
