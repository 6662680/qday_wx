<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan isNOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */

if (empty($_W['isfounder'])) {
	$group = pdo_fetch("SELECT * FROM ".tablename('users_group')." WHERE id = '{$_W['user']['groupid']}'");
	$group_account = uni_groups((array)iunserializer($group['package']));
} else {
	$group_account = uni_groups();
	$group_account[-1] = array('id' => -1, 'name' => '所有服务');
}

$allow_group = array_keys($group_account);
$allow_group[] = 0;

if($_W['ispost']) {
		$uniacid = intval($_GPC['uniacid']);
	$groupid = intval($_GPC['groupid']);
	
	$state = uni_permission($_W['uid'], $uniacid);
	if($state != 'founder' && $state != 'manager') {
		exit('illegal-uniacid');
	}
	
	if(!in_array($groupid, $allow_group)) {
		exit('illegal-group');
	} else {
		pdo_update('uni_account', array('groupid' => $groupid), array('uniacid' => $uniacid));
		if($groupid == 0) {
			exit('基础服务');
		} elseif($groupid == -1) {
			exit('所有服务');
		} else {
			exit($group_account[$groupid]['name']);
		}
	}
	exit();
}

$_W['page']['title'] = '公众号列表 - 公众号';
$tables = array(1 => 'account_wechats', 2 => 'account_yixin');
$pindex = max(1, intval($_GPC['page']));
$psize = 15;
$start = ($pindex - 1) * $psize;
$condition = '';
$pars = array();
$keyword = trim($_GPC['keyword']);
$s_uniacid = intval($_GPC['s_uniacid']);
if(!empty($keyword)) {
	$condition =" AND `name` LIKE :name";
	$pars[':name'] = "%{$keyword}%";
}
if(!empty($s_uniacid)) {
	$condition =" AND `uniacid` = :uniacid";
	$pars[':uniacid'] = $s_uniacid;
}

$uid = $_W['uid'];
if(empty($_W['isfounder'])) {
	$condition .= " AND `uniacid` IN (SELECT `uniacid` FROM " . tablename('uni_account_users') . " WHERE `uid`=:uid)";
	$pars[':uid'] = $uid;
}
$tsql = "SELECT COUNT(*) FROM " . tablename('uni_account') . " WHERE 1 = 1{$condition}";
$total = pdo_fetchcolumn($tsql, $pars);
$sql = "SELECT * FROM " . tablename('uni_account') . " WHERE 1 = 1{$condition} ORDER BY `uniacid` DESC LIMIT {$start}, {$psize}";
$pager = pagination($total, $pindex, $psize);
$list = pdo_fetchall($sql, $pars);
$groups = pdo_fetchall("SELECT * FROM ".tablename('uni_group'), array(), 'id');
$groups[0] = array('id' => 0, 'name' => '基础服务');
$groups[-1] = array('id' => -1, 'name' => '所有服务');

if(!empty($list)) {
	foreach($list as &$account) {
		$account['details'] = uni_accounts($account['uniacid']);
		$account['group'] = $groups[$account['groupid']];
		$account['role'] = uni_permission($_W['uid'], $account['uniacid']);
	}
}

$types = account_types();
template('account/display');
