<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->func('cache.' . $_W['config']['setting']['cache']);


function cache_load($key, $unserialize = false) {
	global $_W;
	$data = $_W['cache'][$key] = cache_read($key);
	if ($key == 'setting') {
		$_W['setting'] = $data;
		return $_W['setting'];
	} elseif ($key == 'modules') {
		$_W['modules'] = $data;
		return $_W['modules'];
	} else {
		return $unserialize ? iunserializer($data) : $data;
	}
}

function &cache_global($key) {
	
}
