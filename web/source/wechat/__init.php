<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan isNOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */

define('FRAME', 'mc');
$frames = buildframes(array('mc'));
$frames = $frames['mc'];

if($controller == 'wechat') {
	if(in_array($action, array('manage', 'card'))) {
		define('ACTIVE_FRAME_URL', url('wechat/account'));
	}
}
