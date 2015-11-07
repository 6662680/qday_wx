<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$do = $_GPC['do'];
$dos = array('deny', 'delete', 'auth', 'revo', 'revos', 'select', 'role');
$do = in_array($do, $dos) ? $do: 'edit';

if (empty($_W['isfounder'])) {
	message('您无权限进行该操作！', referer(), 'error');
}
$uniacid = intval($_GPC['uniacid']);

if($do == 'edit') {
	$_W['page']['title'] = '账号操作员列表 - 编辑主公众号';
	$account = pdo_fetch("SELECT * FROM ".tablename('uni_account')." WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
	if (empty($account)) {
		message('抱歉，您操作的公众号不存在或是已经被删除！');
	}
	$permission = pdo_fetchall("SELECT id, uid, role FROM ".tablename('uni_account_users')." WHERE uniacid = '$uniacid'", array(), 'uid');
	if (!empty($permission)) {
		$member = pdo_fetchall("SELECT username, uid FROM ".tablename('users')." WHERE uid IN (".implode(',', array_keys($permission)).")", array(), 'uid');
	}
	$uids = array();
	foreach ($permission as $v) {
		$uids[] = $v['uid'];
	}
	$founders = explode(',', $_W['config']['setting']['founder']);
	template('account/permission');
}

if ($do == 'auth') {
	$uids = $_GPC['uid'];
	if(empty($uids) || !is_array($uids) || empty($uniacid)) {
		exit('error');
	}
	foreach($uids as $v) {
		$tmpuid = intval($v);
		$data = array(
				'uniacid' => $uniacid,
				'uid' => $tmpuid,
				'role' => 'manager',
		);
		if ($_GPC['reference'] == 'solution') {
			$data['role'] = 'operator';
		}
		$exists = pdo_fetch("SELECT * FROM ".tablename('uni_account_users')." WHERE uid = :uid AND uniacid = :uniacid", array(':uniacid' => $uniacid, ':uid' => $tmpuid));
		if(empty($exists)) {
			pdo_insert('uni_account_users', $data);
		}
	}
	exit('success');
}

if ($do == 'revo') {
	$uid = intval($_GPC['uid']);
	if(empty($uid) || empty($uniacid)) {
		exit('error');
	}
	$data = array(
		'uniacid' => $uniacid,
		'uid' => $uid,
	);
	$exists = pdo_fetch("SELECT * FROM ".tablename('uni_account_users')." WHERE uid = :uid AND uniacid = :uniacid", array(':uniacid' => $uniacid, ':uid' => $uid));
	if(!empty($exists)) {
		pdo_delete('uni_account_users', $data);
	}
	exit('success');
}

if ($do == 'revos') {
	$ids = $_GPC['ids'];
	$ms = array();
	foreach($ids as $v) {
		$id = intval($v);
		if($id) {
			array_push($ms, $id);
		}
	}
	if(!empty($ms)){
		$sql = 'DELETE FROM ' . tablename('uni_account_users') . " WHERE `id` IN (".implode(',', $ms).")";
		pdo_query($sql);
	}
	exit('success');
}

if ($do == 'select') {
	$condition = '';
	$params = array();
	if(!empty($_GPC['keyword'])) {
		$condition = '`username` LIKE :username';
		$params[':username'] = "%{$_GPC['keyword']}%";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = 0;
	
	$list = pdo_fetchall("SELECT * FROM ".tablename('users')." WHERE status = '0' ".(!empty($_W['config']['setting']['founder']) ? " AND uid NOT IN ({$_W['config']['setting']['founder']})" : '')." LIMIT ".(($pindex - 1) * $psize).",{$psize}");
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('users')." WHERE status = '0' ".(!empty($_W['config']['setting']['founder']) ? " AND uid NOT IN ({$_W['config']['setting']['founder']})" : '')."");
	$pager = pagination($total, $pindex, $psize, '', array('ajaxcallback'=>'null'));

	$permission = pdo_fetchall("SELECT uid FROM ".tablename('uni_account_users')." WHERE uniacid = '$uniacid'", array(), 'uid');
	template('account/select');
	exit;
}

if ($do == 'role') {
	$uid = intval($_GPC['uid']);
	$uniacid = intval($_GPC['uniacid']);
	$role = !empty($_GPC['role']) && in_array($_GPC['role'], array('operator', 'manager')) ? $_GPC['role'] : 'operator';
	$state = pdo_update('uni_account_users', array('role' => $role), array('uid' => $uid, 'uniacid' => $uniacid));
	if($state === false) exit('error'); else exit('success');
}
