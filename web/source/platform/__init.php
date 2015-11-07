<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
if(!empty($_GPC['multiid'])) {
	define('ACTIVE_FRAME_URL', url('site/multi/display'));
}
$sysmods = system_modules();
if($action == 'cover') {
	$dos = array('site', 'mc', 'card','module');
	$do = in_array($do, $dos) ? $do : 'module';
	if($do != 'module' && $do != 'card') {
		define('FRAME', $do);
	}
	if($do == 'card') {
		define('FRAME', 'mc');
	}
} elseif($action == 'reply') {
	$m = $_GPC['m'];
	if(in_array($m, $sysmods)) {
		define('FRAME', 'platform');
	}
} elseif($action == 'stat') {
	$m = $_GPC['m'];
	if(!empty($m) && !in_array($m, $sysmods)) {
		define('FRAME', 'ext');
		define('ACTIVE_FRAME_URL', url('home/welcome/ext/') . 'm=' . $m);
	} elseif(!empty($m)) {
		define('FRAME', 'platform');
		define('ACTIVE_FRAME_URL', url('platform/reply/') . 'm=' . $m);
	} else {
		define('FRAME', 'platform');
	}
} else {
	define('FRAME', 'platform');
}

$frames = buildframes(array(FRAME));
$frames = $frames[FRAME];
