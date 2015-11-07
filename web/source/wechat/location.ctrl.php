<?php
/**
 * [Weizan System] Copyright (c) 2013 qdaygroup.com
 * $sn$
 */
defined('IN_IA') or exit('Access Denied');
$dos = array('display', 'post', 'logo');
$do = in_array($do, $dos) ? $do : 'display';
$accounts = uni_accounts(); //todo:需要做权限判断

if($do == 'logo') {
	load()->func('tpl');
	$coupon_setting = pdo_fetch('SELECT * FROM ' . tablename('coupon_setting') . ' WHERE uniacid = :id', array(':id' => $_W['uniacid']));

	if(checksubmit('submit')) {
		$_GPC['logo'] = trim($_GPC['logo']);
		$acid = intval($_GPC['acid']);
		if(empty($acid)) {
			message('请选择公众号', referer(), 'info');
		}
		empty($_GPC['logo']) && message('请上传商户logo', referer(), 'info');
		load()->model('coupon');
		$acc = new coupon($acid);
		$status = $acc->LocationLogoupload($_GPC['logo']);

		if(is_error($status)) {
			message($status['message'], referer(), 'error');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'acid' => $acid,
			'logourl' => $status['url'],
		);
		if(empty($coupon_setting)) {
			pdo_insert('coupon_setting', $data);
		} else {
			pdo_update('coupon_setting', $data, array('uniacid' => $_W['uniacid']));
		}
		message('上传商户LOGO成功', referer(), 'success');
	}
}

if($do == 'post') {
	load()->func('tpl');
	$id = intval($_GPC['id']);
	//$item = pdo_fetch('SELECT * FROM ' . tablename('coupon_location') . ' WHERE acid = :aid AND id = :id', array(':id' => $id, ':aid' => $acid));
	if(checksubmit('submit')) {
		$data['acid'] = intval($_GPC['acid']) ? intval($_GPC['acid']) : message('请选择公众号');
		$data['business_name'] = trim($_GPC['business_name']) ? urlencode(trim($_GPC['business_name'])) : message('门店名称不能为空');
		$data['branch_name'] = urlencode(trim($_GPC['branch_name']));
		$data['category'] = (trim($_GPC['category']) && trim($_GPC['subclass'])) ? urlencode(trim($_GPC['category'])) . '-' .  urlencode(trim($_GPC['subclass'])) : message('请选择门店分类');
		$data['province'] = trim($_GPC['reside']['province']) ? urlencode(trim($_GPC['reside']['province'])) : message('请选择门店所在省');
		$data['city'] = trim($_GPC['reside']['city']) ? urlencode(trim($_GPC['reside']['city'])) : message('请选择门店所在市');
		$data['district'] = trim($_GPC['reside']['district']) ? urlencode(trim($_GPC['reside']['district'])) : message('请选择门店所在区');
		$data['address'] = trim($_GPC['address']) ? urlencode(trim($_GPC['address'])) : message('门店详细地址不能为空');
		$data['longitude'] = trim($_GPC['baidumap']['lng']) ? trim($_GPC['baidumap']['lng']) : message('请选择门店所在地理位置经度');
		$data['latitude'] = trim($_GPC['baidumap']['lat']) ? trim($_GPC['baidumap']['lat']) : message('请选择门店所在地理位置维度');
		$data['telephone'] = trim($_GPC['telephone']) ? trim($_GPC['telephone']) : message('门店电话不能为空');
		$acid = intval($_GPC['acid']);
		load()->model('coupon');
		unset($data['acid']);
		$post[] = $data;
		$acc = new coupon($acid);
		$status = $acc->LocationBatchAdd($post);
		if(is_error($status)) {
			message($status['message'], referer(), 'error');
		}
		$data['acid'] = $acid;
		$data['uniacid'] = $_W['uniacid'];
		//插入数据库
		pdo_insert('coupon_location', $data);
		$id = pdo_insertid();
		message('添加门店成功', url('manage/location_list'), 'success');
	}
}

if($do == 'display') {
	load()->model('coupon');
	$acid = 269;
	$acc = new coupon($acid);
	$status = $acc->GetColors($post);

}
template('wechat/location');


