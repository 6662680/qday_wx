<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function logging_run($log, $type = 'normal', $filename = 'run', $includePost = true) {
	global $_W;
	$filename .= '.log';
	
	$path = IA_ROOT . '/data/logs/';
	if (empty($path)) {
		return error(1, '目录创建失败');
	}	
	
	$logFormat = "%date %type %user %url %context";
	
	if ($includePost) {
		if(!empty($GLOBALS['_POST'])) {
			$context[] = logging_implode($GLOBALS['_POST']);
		}
	}
	
	if (is_array($log)) {
		$context[] = logging_implode($log);
	} else {
		$context[] = preg_replace('/[ \t\r\n]+/', ' ', $log);
	}
	
	$log = str_replace(explode(' ', $logFormat), array(
			'['.date('Y-m-d H:i:s', $_W['timestamp']).']',
			$type,
			$_W['username'],
			$_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"],
			implode("\n", $context),
			), $logFormat);
	
	file_put_contents($path.$filename, $log."\r\n", FILE_APPEND);
	return true;
}


function logging_error() {
	
}

function logging_mkdir() {
	$logRoot = IA_ROOT . '/data/logs/';
	$logDir = $logRoot . date('Ymd');
	if (mkdirs($logDir)) {
		return $logDir . '/';
	} else {
		return false;
	}
}

function logging_implode($array, $skip = array()) {
	$return = '';
	if(is_array($array) && !empty($array)) {
		foreach ($array as $key => $value) {
			if(empty($skip) || !in_array($key, $skip, true)) {
				if(is_array($value)) {
					$return .= "$key={".logging_implode($value, $skip)."}; ";
				} else {
					$return .= "$key=$value; ";
				}
			}
		}
	}
	return $return;
}
