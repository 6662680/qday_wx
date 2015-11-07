<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$dos = array('display', 'manage', 'delete');
$do = in_array($do, $dos) ? $do : 'display';
load()->func('tpl');
load()->model('mc');

$setting = pdo_fetch("SELECT * FROM ".tablename('mc_card')." WHERE uniacid = '{$_W['uniacid']}'");

if ($do == 'display') {
	if ($_W['ispost'] && $_W['isajax']) {
		$sql = 'SELECT `uniacid` FROM ' . tablename('mc_card') . " WHERE `uniacid` = :uniacid";
		$status = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));
		if (empty($status)) {
			$open = array('uniacid' => $_W['uniacid']);
			pdo_insert('mc_card', $open);
		}
		$data['status'] = intval($_GPC['status']);
		if (false === pdo_update('mc_card', $data, array('uniacid' => $_W['uniacid']))) {
			exit('error');
		}
		exit('success');
	}
	$fields = mc_fields();
	if (!empty($setting)) {
		$setting['color'] = iunserializer($setting['color']);
		$setting['background'] = iunserializer($setting['background']);
		$setting['fields'] = iunserializer($setting['fields']);
		if(!empty($setting['fields'])) {
			foreach($setting['fields'] as $field) {
				$re_fields[] = $field['bind'];
			}
			if(!in_array('realname', $re_fields)) {
				$setting['fields'][] = array('title' => '姓名', 'require' => 1, 'bind' => 'realname');
			}
			if(!in_array('mobile', $re_fields)) {
				$setting['fields'][] = array('title' => '手机号', 'require' => 1, 'bind' => 'mobile');
			}
		}
		if(empty($setting['logo'])) {
			$setting['logo'] = 'images/global/card/logo.png';
		}
	} 
	if (checksubmit('submit')) {
		if (empty($_GPC['title'])) {
			message('请输入会员卡名称！');
		}
		if (empty($_GPC['format'])) {
			message('请输入会员卡的卡号生成格式！');
		}
		$data = array(
				'title' => $_GPC['title'],
				'color' => iserializer(array(
						'title' => $_GPC['color-title'],
						'number' => $_GPC['color-number'],
				)),
				'background' => iserializer(array(
						'background' => $_GPC['background'],
						'image' => $_GPC[$_GPC['background'].'-bg'],
				)),
				'logo' => $_GPC['logo'],
				'format' => $_GPC['format'],
				'description' => trim($_GPC['description']),
				'fields' => ''
		);
		$data['fields'][] = array('title' => '姓名', 'require' => 1, 'bind' => 'realname');
		$data['fields'][] = array('title' => '手机号', 'require' => 1, 'bind' => 'mobile');
		if (!empty($_GPC['fields'])) {
			foreach ($_GPC['fields']['title'] as $index => $row) {
				if (empty($_GPC['fields']['title'][$index]) || $_GPC['fields']['bind'][$index] == 'mobile' || $_GPC['fields']['bind'][$index] == 'realname') {
					continue;
				}
				$data['fields'][] = array(
						'title' => $_GPC['fields']['title'][$index],
						'require' => intval($_GPC['fields']['require'][$index]),
						'bind' => $_GPC['fields']['bind'][$index],
				);
			}
			
			$data['fields'] = iserializer($data['fields']);
		}
		if (!empty($setting)) {
			pdo_update('mc_card', $data, array('uniacid' => $_W['uniacid']));
		} else {
			$data['uniacid'] = $_W['uniacid'];
			pdo_insert('mc_card', $data);
		}
		message('会员卡设置成功！', url('mc/card/display'), 'success');
	}
}

if ($do == 'manage') {
	if ($_W['ispost']) {
		$status = array('status' => intval($_GPC['status']));
		if (false === pdo_update('mc_card_members', $status, array('uniacid' => $_W['uniacid'], 'id' => $_GPC['cardid']))) {
			exit('error');
		}
		exit('success');
	}
	if ($setting['status'] == 0) {
		message('会员卡功能未开启', url('mc/card'), 'error');
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$where = !empty($_GPC['cardsn']) ? " AND `cardsn` LIKE '%{$_GPC['cardsn']}%' " : '';
	if (is_numeric($_GPC['status'])) {
		$status = intval($_GPC['status']);
		$where .= " AND `status` = '" . $status . "'";
	}
	$sql = 'SELECT * FROM ' . tablename('mc_card_members') . " WHERE uniacid = :uniacid $where ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize;
	$list = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mc_card_members') . " WHERE uniacid = '{$_W['uniacid']}' $where");
	$pager = pagination($total, $pindex, $psize);
	foreach ($list as $value) {
		$uids[] = $value['uid'];
	}
	if (!empty($uids)) {
		$uids = array_unique($uids);
	}
	$realnames = mc_fetch($uids, array('realname', 'mobile'));
	foreach ($list as &$value) {
		$value['realname'] = $realnames[$value['uid']]['realname'];
		$value['mobile'] = $realnames[$value['uid']]['mobile'];
	}
}

if ($do == 'delete') {
	$cardid = intval($_GPC['cardid']);
	if (pdo_delete('mc_card_members',array('id' =>$cardid))) {
		message('删除会员卡成功',url('mc/card/manage'),'success');
	} else {
		message('删除会员卡失败',url('mc/card/manage'),'error');
	}
}


template('mc/card');