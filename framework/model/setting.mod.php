<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function setting_save($data = '', $key = '') {
	if (empty($data) && empty($key)) {
		return FALSE;
	}
	if (is_array($data) && empty($key)) {
		foreach ($data as $key => $value) {
			$record[] = "('$key', '".iserializer($value)."')";
		}
		if ($record) {
			$return = pdo_query("REPLACE INTO ".tablename('core_settings')." (`key`, `value`) VALUES " . implode(',', $record));
		}
	} else {
		$record = array();
		$record['key'] = $key;
		$record['value'] = iserializer($data);
		$return = pdo_insert('core_settings', $record, TRUE);
	}
	cache_build_setting();
	return $return;
}


function setting_load($key = '') {
	global $_W;
	if (empty($key)) {
		$settings = pdo_fetchall('SELECT * FROM ' . tablename('core_settings'), array(), 'key');
	} else {
		$key = is_array($key) ? $key : array($key);
		$settings = pdo_fetchall('SELECT * FROM ' . tablename('core_settings') . " WHERE `key` IN ('".implode("','", $key)."')", array(), 'key');
	}
	if(is_array($settings)) {
		foreach($settings as $k => &$v) {
			$settings[$k] = iunserializer($v['value']);
		}
	}
	if(!is_array($_W['setting'])) {
		$_W['setting'] = array();
	}
	$_W['setting'] = array_merge($_W['setting'], $settings);
	return $settings;
}

function setting_upgrade_version($family, $version, $release) {
	$verfile = IA_ROOT . '/framework/version.inc.php';
	$verdat = <<<VER
<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

define('IMS_FAMILY', '{$family}');
define('IMS_VERSION', '{$version}');
define('IMS_RELEASE_DATE', '{$release}');
VER;
	file_put_contents($verfile, trim($verdat));
}
