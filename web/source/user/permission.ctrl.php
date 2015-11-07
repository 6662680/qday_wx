<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$_W['page']['title'] = '查看用户权限 - 用户管理 - 用户管理';
load()->model('setting');

$uid = intval($_GPC['uid']);
$user = user_single($uid);
if(empty($user)) {
	message('访问错误, 未找到指定操作用户.');
}

$founders = explode(',', $_W['config']['setting']['founder']);
$isfounder = in_array($user['uid'], $founders);
if($isfounder) {
	message('访问错误, 无法编辑站长.');
}

$do = $_GPC['do'];
$dos = array('deny', 'delete', 'auth', 'revo', 'revos', 'select', 'role', 'menu', 'edit');
$do = in_array($do, $dos) ? $do: 'edit';

if($do == 'edit') {
		if (!empty($user['groupid'])) {
		$group = pdo_fetch("SELECT * FROM ".tablename('users_group')." WHERE id = '{$user['groupid']}'");
		if (!empty($group)) {
			$package = iunserializer($group['package']);
			$group['package'] = uni_groups($package);
		}
	}
	$weids = pdo_fetchall("SELECT uniacid, role FROM ".tablename('uni_account_users')." WHERE uid = '$uid'", array(), 'uniacid');
	if (!empty($weids)) {
		$wechats = pdo_fetchall("SELECT * FROM ".tablename('uni_account')." WHERE uniacid IN (".implode(',', array_keys($weids)).")");
	}
	template('user/permission');
}

if($do == 'deny') {
	if($_W['ispost'] && $_W['isajax']) {
		$founders = explode(',', $_W['config']['setting']['founder']);
		if(in_array($uid, $founders)) {
			exit('管理员用户不能禁用.');
		}
		$somebody = array();
		$somebody['uid'] = $uid;
		
		if (intval($user['status']) == 2) {
			$somebody['status'] = 1;
		} else {
			$somebody['status'] = 2;
		}
		if(user_update($somebody)) {
			exit('success');
		}
	}
}

if ($do == 'select') {
	$uid = intval($_GPC['uid']);
	$condition = '';
	$params = array();
	if(!empty($_GPC['keyword'])) {
		$condition = ' AND `name` LIKE :name';
		$params[':name'] = "%{$_GPC['keyword']}%";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$total = 0;
	
	$list = pdo_fetchall("SELECT * FROM ".tablename('uni_account')." WHERE 1 $condition LIMIT ".(($pindex - 1) * $psize).",{$psize}");
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('uni_account')." WHERE 1 $condition");
	$pager = pagination($total, $pindex, $psize, '', array('ajaxcallback'=>'null'));
	
	$permission = pdo_fetchall("SELECT uniacid FROM ".tablename('uni_account_users')." WHERE uid = '$uid'", array(), 'uniacid');
	template('user/select');
}

if ($do == 'menu') {
	$uniacid = intval($_GPC['uniacid']);
	$uid = intval($_GPC['uid']);
	load()->model('user');
	load()->model('module');
	
	$user = user_single(array('uid' => $uid));
	if (empty($user)) {
		message('您操作的用户不存在或是已经被删除！');
	}
	if (!pdo_fetchcolumn("SELECT id FROM ".tablename('uni_account_users')." WHERE uid = :uid AND uniacid = :uniacid", array(':uid' => $uid, ':uniacid' => $uniacid))) {
		message('此用户没有操作该统一公众号的权限，请选指派“管理者”权限！');
	}
	$result = pdo_fetchall("SELECT url, id FROM ".tablename('users_permission')." WHERE uid = :uid AND uniacid = :uniacid", array(':uid' => $uid, ':uniacid' => $uniacid));
	$hasurls = array();
	if (!empty($result)) {
		foreach ($result as $row) {
			$hasurls[$row['id']] = $row['url'];
		}
	}
	if (checksubmit('submit')) {
		if (empty($_GPC['permurls'])) {
			pdo_query("DELETE FROM ".tablename('users_permission')." WHERE uid = '{$uid}'");
		} else {
			foreach ($_GPC['permurls'] as $url) {
				if (($pos = array_search($url, $hasurls)) !== FALSE) {
					unset($hasurls[$pos]);
					continue;
				}
				parse_str($url, $tokens);
				pdo_insert('users_permission', array(
					'uid' => $uid,
					'uniacid' => $uniacid,
					'url' => $url,
				));
			}
						pdo_query("DELETE FROM ".tablename('users_permission')." WHERE uid = '{$uid}' AND id IN ('".implode("','", array_keys($hasurls))."')");
		}
		message('操作菜单权限成功！', url('user/permission/menu', array('uid' => $uid, 'uniacid' => $uniacid)), 'success');
	}
	$menus = buildframes(array('platform', 'site', 'mc', 'setting', 'ext'));
	template('user/menu');
}

if ($do == 'auth') {
	$uniacid = intval($_GPC['uniacid']);
	$uid = intval($uid);
	
	$isexists = pdo_fetch("SELECT * FROM ".tablename('uni_account_users')." WHERE uid = :uid AND uniacid = :uniacid", array(':uid' => $uid, ':uniacid' => $uniacid));
	if (empty($isexists)) {
		pdo_insert('uni_account_users', array('uniacid' => $uniacid, 'uid' => $uid));
	}
	exit('success');
}

if ($do == 'revo') {
	$uniacid = intval($_GPC['uniacid']);
	$uid = intval($uid);
	
	$isexists = pdo_fetch("SELECT * FROM ".tablename('uni_account_users')." WHERE uid = :uid AND uniacid = :uniacid", array(':uid' => $uid, ':uniacid' => $uniacid));
	if (!empty($isexists)) {
		pdo_delete('uni_account_users', array('uniacid' => $uniacid, 'uid' => $uid));
	}
	exit('success');
}

if ($do == 'role') {
	$uid = intval($_GPC['uid']);
	$uniacid = intval($_GPC['uniacid']);
	$role = !empty($_GPC['role']) && in_array($_GPC['role'], array('operator', 'manager')) ? $_GPC['role'] : 'operator';
	pdo_update('uni_account_users', array('role' => $role), array('uid' => $uid, 'uniacid' => $uniacid));
}
