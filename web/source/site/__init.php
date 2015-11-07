<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
if(!empty($_GPC['f']) && $_GPC['f'] == 'multi') {
	define('ACTIVE_FRAME_URL', url('site/multi/display'));
}
$sysmodules = system_modules();
if(!empty($_GPC['styleid'])) {
	define('ACTIVE_FRAME_URL', url('site/style/styles'));
}

if($controller == 'site') {
	$m = $_GPC['m'];
	if(in_array($m, $sysmodules)) {
		define('FRAME', 'platform');
		define('CRUMBS_NAV', 2);
		define('ACTIVE_FRAME_URL', url('platform/reply/', array('m' => $m)));
	}
}

if($action != 'entry' && $action != 'nav') {
	define('FRAME', 'site');
}
$frames = buildframes(array(FRAME));
$frames = $frames[FRAME];
