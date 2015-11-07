<?php 
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$_W['page']['title'] = '用户列表 - 用户管理 - 用户管理';
$pindex = max(1, intval($_GPC['page']));
$psize = 20;

$where = ' WHERE 1 ';
$params = array();
if (!empty($_GPC['status'])) {
	$where .= " AND status = :status";
	$params[':status'] = intval($_GPC['status']);
}
if (!empty($_GPC['username'])) {
	$where .= " AND username LIKE :username";
	$params[':username'] = "%{$_GPC['username']}%";
}
if (!empty($_GPC['group'])) {
	$where .= " AND groupid = :groupid";
	$params[':groupid'] = intval($_GPC['group']);
}

$sql = 'SELECT * FROM ' . tablename('users') .$where . " LIMIT " . ($pindex - 1) * $psize .',' .$psize;
$users = pdo_fetchall($sql, $params);
$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('users') . $where, $params);
$pager = pagination($total, $pindex, $psize);

$founders = explode(',', $_W['config']['setting']['founder']);
foreach($users as &$user) {
	$user['founder'] = in_array($user['uid'], $founders);
}
unset($user);

$usergroups = pdo_fetchall("SELECT id, name FROM ".tablename('users_group'), array(), 'id');
$settings = setting_load('register');
$settings = $settings['register'];

template('user/display');
