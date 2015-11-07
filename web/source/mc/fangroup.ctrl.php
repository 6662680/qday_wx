<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$dos = array('post', 'display', 'qr', 'chat');
$do = !empty($_GPC['do']) && in_array($do, $dos) ? $do : 'display';

$accs = uni_accounts();
$accounts = array();
if(!empty($accs)) {
	foreach($accs as $acc) {
		if($acc['level'] > 2) {
			$accounts[$acc['acid']] = array_elements(array('name', 'acid'), $acc);
		}
	}
}
if($do == 'display') {
	if(empty($_GPC['acid']) && count($accounts) == 1){
		$account = current($accounts);
		if($account !== false){
			$acid = intval($account['acid']);
		}
	} else {
		$acid = intval($_GPC['acid']);
		if(!empty($acid) && !empty($accounts[$acid])) {
			$account = $accounts[$acid];
		}
	}
	if($acid > 0) {
		$account = WeAccount::create($acid);
		$groups = $account->fetchFansGroups();
		if(is_error($groups)) {
			message($groups['message'], url('mc/fangroup'), 'error');
		} else {
			$exist = pdo_fetch('SELECT * FROM ' . tablename('mc_fans_groups') . ' WHERE uniacid = :uniacid AND acid = :acid', array(':uniacid' => $_W['uniacid'], ':acid' => $acid));
			if(empty($exist)) {
				if(!empty($groups['groups'])) {
					$groups_tmp = array();
					foreach($groups['groups'] as $da) {
						$groups_tmp[$da['id']] = $da;
					}
					$data = array('acid' => $acid, 'uniacid' => $_W['uniacid'], 'groups' => iserializer($groups_tmp));
					pdo_insert('mc_fans_groups', $data);
				}
			} else {
				if(!empty($groups['groups'])) {
					$groups_tmp = array();
					foreach($groups['groups'] as $da) {
						$groups_tmp[$da['id']] = $da;
					}
					$data = array('groups' => iserializer($groups_tmp));
					pdo_update('mc_fans_groups', $data, array('uniacid' => $_W['uniacid'], 'acid' => $acid));
				}
			}
		}
	}
}
if($do == 'post') {
	$acid = intval($_GPC['acid']);
	if(empty($acid)) {
		message('公众号id错误', '', 'error');
	}
	$account = WeAccount::create($acid);
	if(!empty($_GPC['groupname'])) {
		foreach($_GPC['groupname'] as $key => $value) {
			if(empty($value)) {
				continue;
			} else {
				$data = array('id' => $_GPC['groupid'][$key], 'name' => $value);
				$state = $account->editFansGroupname($data);
				if(is_error($state)) {
					message($state['message'], url('mc/fangroup/', array('acid' => $acid)), 'error');
				}
			}
		}
	}
	if(!empty($_GPC['group_add'])) {
		foreach($_GPC['group_add'] as $value) {
			if(empty($value)) {
				continue;
			} else {
				$value = trim($value);
				$state = $account->addFansGroup($value);
				if(is_error($state)) {
					message($state['message'], url('mc/fangroup/', array('acid' => $acid)), 'error');
				}
			}
		}
	}
	message('保存分组名称成功', url('mc/fangroup/', array('acid' => $acid)), 'success');
}
template('mc/fansgroup');