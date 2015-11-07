<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$uniacid = intval($_GPC['uniacid']);
$role = uni_permission($_W['uid'], $uniacid);
if(empty($role)) {
	message('操作失败, 非法访问.');
}
isetcookie('__uniacid', $uniacid, 7 * 86400);
isetcookie('__uid', $_W['uid'], 7 * 86400);
header('location: ' . url('home/welcome'));