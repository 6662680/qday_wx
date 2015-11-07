<?php
//红包设置
//
global $_W,$_GPC;
$this->__init();
load()->func('tpl');
$sql = "SELECT * FROM ".tablename('meepo_begging_set')." WHERE uniacid = :uniacid";
$params = array(':uniacid'=>$_W['uniacid']);
$settings = pdo_fetch($sql,$params);
$settings = unserialize($settings['set']);
if($_W['ispost']) {
			//字段验证, 并获得正确的数据$dat
	load()->func('file');
	mkdirs(ROOT_PATH . '/cert');
	$r = true;
	if (!empty($_GPC['cert'])) {
		$ret = file_put_contents(ROOT_PATH . '/cert/apiclient_cert.pem.' . $_W['uniacid'], trim($_GPC['cert']));
		$r = $r && $ret;
	}
	if (!empty($_GPC['key'])) {
		$ret = file_put_contents(ROOT_PATH . '/cert/apiclient_key.pem.' . $_W['uniacid'], trim($_GPC['key']));
		$r = $r && $ret;
	}
	if (!empty($_GPC['ca'])) {
		$ret = file_put_contents(ROOT_PATH . '/cert/rootca.pem.' . $_W['uniacid'], trim($_GPC['ca']));
		$r = $r && $ret;
	}
	if (!$r) {
		message('证书保存失败, 请保证 /addons/microb_redpack/cert/ 目录可写');
	}
	$input = array_elements(array(
		'appid',
		'secret',
		'mchid',
		'password',
		'ip',
		'minnum',
		'logo'
		) , $_GPC);
	$input['appid'] = trim($input['appid']);
	$input['secret'] = trim($input['secret']);
	$input['mchid'] = trim($input['mchid']);
	$input['password'] = trim($input['password']);
	$input['ip'] = trim($input['ip']);
	$input['minnum'] = trim($input['minnum']);
	$input['logo'] = trim($input['logo']);
	
			// /id set createtiem/
	$data = array();
	$data['set'] = serialize($input);
	$data['uniacid'] = $_W['uniacid'];

	if(empty($settings)){
		pdo_insert('meepo_begging_set',$data);
	}else{
		pdo_update('meepo_begging_set',$data,array('uniacid'=>$_W['uniacid']));
	}

	message('提交成功',referer(),success);
}

$setting = uni_setting($_W['uniacid'], array('payment'));
$accounts = account_fetch($setting['payment']['wechat']['account']);
if(empty($settings['minnum'])) {
	$settings['minnum'] = '100.00';
}
if(empty($settings['appid'])) {
	$settings['appid'] = $accounts['key'];
}
if(empty($settings['secret'])) {
	$settings['secret'] =  $accounts['secret'];
}
if (empty($settings['ip'])) {
	$settings['ip'] = $_SERVER['SERVER_ADDR'];
}
if(empty($settings['mchid'])){
	$settings['mchid'] = $setting['payment']['wechat']['mchid'];
}
if(empty($settings['password'])){
	$settings['password'] = $setting['payment']['wechat']['apikey'];
}
include $this->template('hongset');