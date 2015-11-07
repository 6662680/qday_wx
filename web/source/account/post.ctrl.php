<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan isNOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->func('tpl');
$dos = array('basic', 'list', 'high');
$do = in_array($do, $dos) ? $do : 'basic';

$id = $uniacid = intval($_GPC['uniacid']);
if(!empty($id)) {
	$state = uni_permission($_W['uid'], $id);
	if($state != 'founder' && $state != 'manager') {
		message('没有该公众号操作权限！');
	}
} else {
	if(empty($_W['isfounder']) && is_error($permission = uni_create_permission($_W['uid'], 1))) {
		message($permission['message'], '' , 'error');
	}
}

if (empty($_W['isfounder'])) {
	$group = pdo_fetch("SELECT * FROM ".tablename('users_group')." WHERE id = '{$_W['user']['groupid']}'");
	$group['package'] = uni_groups((array)iunserializer($group['package']));
} else {
	$group['package'] = uni_groups();
	$group['package'][-1] = array('id' => -1, 'name' => '所有服务');
}

$allow_group = array_keys($group['package']);
$allow_group[] = 0;

if($do == 'basic') {
	$_W['page']['title'] = '公众号基本信息 - 编辑主公众号';
	if(empty($id)) {

	} elseif (checksubmit('submit')) {
		$groupid = intval($_GPC['groupid']);
		if(!in_array($groupid, $allow_group)) {
			message('您所在的用户组没有使用该服务套餐的权限');
		}
		load()->model('module');
		$uniaccount = array(
				'name' => $_GPC['name'],
				'groupid' => intval($_GPC['groupid']),
				'description' => $_GPC['description'],
		);
		if($_GPC['isexpire'] == '1') {
			strtotime($_GPC['endtime']) > TIMESTAMP ? '' : message('服务套餐过期时间必须大于当前时间', '', 'error');
			$updatedata['groupdata'] = iserializer(array('isexpire' => 1, 'oldgroupid' => intval($_GPC['groupidhide']), 'endtime' => strtotime($_GPC['endtime'])));
		} else {
			$updatedata['groupdata'] = iserializer(array('isexpire' => 0, 'oldgroupid' => intval($_GPC['groupidhide']), 'endtime' => TIMESTAMP));
		}

		if($_W['isfounder']) {
			$notify['sms']['balance'] = intval($_GPC['balance']);
			$notify['sms']['signature'] = trim($_GPC['signature']);
			$notify = iserializer($notify);
			$updatedata['notify'] = $notify;
		}
		pdo_update('uni_settings', $updatedata , array('uniacid' => $id));
		pdo_update('uni_account', $uniaccount, array('uniacid' => $id));
		module_build_privileges();
		message('更新公众号成功！', referer(), 'success');
	}

	$account = array();
	if (!empty($id)) {
		$account = pdo_fetch("SELECT * FROM ".tablename('uni_account')." WHERE uniacid = :id", array(':id' => $id));
		$settings = uni_setting($id, array('notify', 'groupdata'));
		$groupdata = $settings['groupdata'] ? $settings['groupdata'] : array('isexpire' => 0, 'oldgroupid' => '' ,'endtime' => TIMESTAMP);
		$notify = $settings['notify'] ? $settings['notify'] : array();
	} else {
		$groupdata = array('isexpire' => 0, 'oldgroupid' => '' ,'endtime' => TIMESTAMP);
	}
	$group = array();
	if (empty($_W['isfounder'])) {
		$group = pdo_fetch("SELECT * FROM ".tablename('users_group')." WHERE id = '{$_W['user']['groupid']}'");
		$group['package'] = uni_groups((array)iunserializer($group['package']));
	} else {
		$group['package'] = uni_groups();
		$group['package'][-1] = array('id' => -1, 'name' => '所有服务');
	}
	template('account/post');
}

if ($do == 'list') {
	$_W['page']['title'] = '子公众号列表 - 编辑主公众号';
	$accounts = uni_accounts($uniacid);
	$types = account_types();
	template('account/list');
}
