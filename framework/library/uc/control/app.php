<?php

/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */

!defined('IN_UC') && exit('Access Denied');

class appcontrol extends base {

	function __construct() {
		$this->appcontrol();
	}

	function appcontrol() {
		parent::__construct();
		$this->load('app');
	}

	function onls() {
		$this->init_input();
		$applist = $_ENV['app']->get_apps('appid, type, name, url, tagtemplates, viewprourl, synlogin');
		$applist2 = array();
		foreach($applist as $key => $app) {
			$app['tagtemplates'] = $this->unserialize($app['tagtemplates']);
			$applist2[$app['appid']] = $app;
		}
		return $applist2;
	}

	function onadd() {
	}

	function onucinfo() {
	}

	function _random($length, $numeric = 0) {
	}

	function _generate_key() {
	}

	function _format_notedata($notedata) {
	}
}

?>