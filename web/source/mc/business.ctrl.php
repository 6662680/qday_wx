<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$dos = array('display');
$do = in_array($do, $dos) ? $do : 'display';
load()->func('tpl');

if ($do == 'display') {
	if (checksubmit('submit')) {
		
		$setting = array(
			'title' => $_GPC['title'],
			'thumb' => $_GPC['thumb'],
			'content' => $_GPC['content'],
			'phone' => $_GPC['phone'],
			'qq' => $_GPC['qq'],
			'province' => $_GPC['dis']['province'],
			'city' =>$_GPC['dis']['city'],
			'district' => $_GPC['dis']['district'],
			'address' => $_GPC['address'],
			'lng' => $_GPC['baidumap']['lng'],
			'lat' => $_GPC['baidumap']['lat'],
			'industry1' => $_GPC['industry']['parent'],
			'industry2' => $_GPC['industry']['child'],
		);
		
		
		$data['business'] = iserializer($setting);
		$sql = 'SELECT `id` FROM ' . tablename('mc_card') . " WHERE `uniacid` = :uniacid";
		$count = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));
		if (!empty($count)) {
			pdo_update('mc_card', $data, array('uniacid' => $_W['uniacid']));
		} else {
			$data['uniacid'] = $_W['uniacid'];
			pdo_insert('mc_card', $data);
		}
		message('商家设置成功！', referer(), 'success');
	}
	
	$sql = 'SELECT `status`, `business` FROM ' . tablename('mc_card') . " WHERE `uniacid` = :uniacid";
	$list = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));
	if ($list['status'] == 0) {
		message('会员卡功能未开启', url('mc/card'), 'error');
	}
	if (!empty($list['business'])) {
		$item = iunserializer($list['business']);
		$reside['province'] = $item['province'];
		$reside['city'] = $item['city'];
		$reside['district'] = $item['district'];
	}
}

template('mc/business');