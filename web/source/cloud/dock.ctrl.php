<?php 
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
load()->model('cloud');
$dos = array('auth', 'build', 'schema', 'download', 'module.query', 'module.info', 'module.build', 'theme.query', 'theme.info', 'theme.build', 'application.build');
$do = in_array($do, $dos) ? $do : '';
if(empty($do)) {
	exit();
}
if($do != 'auth') {
	if(is_error(cloud_prepare())) {
		exit('cloud service is unavailable.');
	}
}

$post = file_get_contents('php://input');

if($do == 'auth') {
	$secret = random(32);
	$auth = @json_decode(base64_decode($post), true);
	if(empty($auth)) {
		exit;
	}
	$auth['secret'] = $secret;
	cache_write('cloud:auth:transfer', $auth);
	exit($secret);
}

if($do == 'build') {
	$dat = __secure_decode($post);
	if(!empty($dat)) {
		$secret = random(32);
		$ret = array();
		$ret['data'] = $dat;
		$ret['secret'] = $secret;
		file_put_contents(IA_ROOT . '/data/application.build', iserializer($ret));
		exit($secret);
	}
}

if($do == 'schema') {
	$dat = __secure_decode($post);
	if(!empty($dat)) {
		$secret = random(32);
		$ret = array();
		$ret['data'] = $dat;
		$ret['secret'] = $secret;
		file_put_contents(IA_ROOT . '/data/application.schema', iserializer($ret));
		exit($secret);
	}
}

if($do == 'download') {
	$ret = iunserializer($post);
	$gz = function_exists('gzcompress') && function_exists('gzuncompress');
	$file = base64_decode($ret['file']);
	if($gz) {
		$file = gzuncompress($file);
	}

	$string = (md5($file) . $ret['path'] . $_W['setting']['site']['token']);
	if(md5($string) == $ret['sign']) {
		$path = IA_ROOT . $ret['path'];
		load()->func('file');
		@mkdirs(dirname($path));
		file_put_contents($path, $file);
		$sign = md5(md5_file($path) . $ret['path'] . $_W['setting']['site']['token']);
		if($ret['sign'] == $sign) {
			exit('success');
		}
	}
	exit('failed');
}

if(in_array($do, array('module.query', 'module.info', 'module.build', 'theme.query', 'theme.info', 'theme.build', 'application.build'))) {
	$dat = __secure_decode($post);
	if(!empty($dat)) {
		$secret = random(32);
		$ret = array();
		$ret['data'] = $dat;
		$ret['secret'] = $secret;
		file_put_contents(IA_ROOT . '/data/' . $do, iserializer($ret));
		exit($secret);
	}
}

function __secure_decode($post) {
	global $_W;
	$ret = iunserializer($post);
	$string = ($ret['data'] . $_W['setting']['site']['token']);
	if(md5($string) == $ret['sign']) {
		return $ret['data'];
	}
	return false;
}