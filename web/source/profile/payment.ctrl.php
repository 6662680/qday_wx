<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$_W['page']['title'] = '支付参数 - 公众号选项';
if (!checkpermission('wechats', $id)) {
	message('公众号不存在或是您没有权限操作！');
}
$setting = uni_setting($_W['uniacid'], array('payment'));
$pay = $setting['payment'];
if(!is_array($pay)) {
	$pay = array();
}
if($_W['ispost']) {
	$credit = array_elements(array('switch'), $_GPC['credit']);
	$credit['switch'] = $credit['switch'] == 'true';
	$card = array_elements(array('switch'), $_GPC['card']);
	$card['switch'] = intval($card['switch']);
	$alipay = array_elements(array('switch', 'account', 'partner', 'secret'), $_GPC['alipay']);
	$alipay['switch'] = $alipay['switch'] == 'true';
	$alipay['account'] = trim($alipay['account']);
	$alipay['partner'] = trim($alipay['partner']);
	$alipay['secret'] = trim($alipay['secret']);
	$delivery = array_elements(array('switch'), $_GPC['delivery']);
	$delivery['switch'] = $delivery['switch'] == 'true';
	if($alipay['switch'] && (empty($alipay['account']) || empty($alipay['partner']) || empty($alipay['secret']))) {
		message('请输入完整的支付宝接口信息.');
	}
	if($_GPC['alipay']['t'] == 'true') {
		$params = array();
		$params['tid'] = md5(uniqid());
		$params['user'] = '测试用户';
		$params['fee'] = '0.01';
		$params['title'] = '测试支付接口';
		load()->model('payment');
		load()->func('communication');
		$ret = alipay_build($params, $alipay);
		if($ret['url']) {
			header("location: {$ret['url']}");
		}
		exit();
	}
	$wechat = array_elements(array('switch', 'account', 'signkey', 'partner', 'key', 'version', 'mchid', 'apikey', 'version'), $_GPC['wechat']);
	$wechat['switch'] = $wechat['switch'] == 'true';
	$wechat['signkey'] = $wechat['version'] == 2 ? trim($wechat['apikey']) : trim($wechat['signkey']);
	$wechat['partner'] = trim($wechat['partner']);
	$wechat['key'] = trim($wechat['key']);
	if($wechat['switch'] && empty($wechat['account'])) {
		message('请输入完整的微信支付接口信息.');
	}
	$unionpay = array_elements(array('switch', 'signcertpwd', 'merid'), $_GPC['unionpay']);
	$unionpay['switch'] = $unionpay['switch'] == 'true';
	if($unionpay['switch'] && (empty($unionpay['merid']) || empty($unionpay['signcertpwd']))) {
		message('请输入完整的银联支付接口信息.');
	}
	if ($unionpay['switch'] && empty($_FILES['unionpay']['tmp_name']['signcertpath']) && !file_exists(IA_ROOT . '/attachment/unionpay/PM_'.$_W['uniacid'].'_acp.pfx')) {
		message('请上联银商户私钥证书.');
	}
	$baifubao = array_elements(array('switch', 'signkey', 'mchid'), $_GPC['baifubao']);
	$baifubao['switch'] = $baifubao['switch'] == 'true';
	if($baifubao['switch'] && (empty($baifubao['signkey']) || empty($baifubao['mchid']))) {
		message('请输入完整的百付宝支付接口信息.');
	}
	if(!is_array($pay)) {
		$pay = array();
	}
	$pay['credit'] = $credit;
	$pay['alipay'] = $alipay;
	$pay['wechat'] = $wechat;
	$pay['delivery'] = $delivery;
	$pay['unionpay'] = $unionpay;
	$pay['baifubao'] = $baifubao;
	$pay['card'] = $card;
	
	if ($unionpay['switch'] && !empty($_FILES['unionpay']['tmp_name']['signcertpath'])) {
		load()->func('file');
		mkdirs(IA_ROOT . '/attachment/unionpay/');
		file_put_contents(IA_ROOT . '/attachment/unionpay/PM_'.$_W['uniacid'].'_acp.pfx', file_get_contents($_FILES['unionpay']['tmp_name']['signcertpath']));
		$public_rsa = '-----BEGIN CERTIFICATE-----
MIIDNjCCAh6gAwIBAgIQEAAAAAAAAAAAAAAQBQdAIDANBgkqhkiG9w0BAQUFADAh
MQswCQYDVQQGEwJDTjESMBAGA1UEChMJQ0ZDQSBPQ0ExMB4XDTEyMTIxODAyMDA1
MVoXDTE1MTIxODAyMDA1MVowfDELMAkGA1UEBhMCQ04xDTALBgNVBAoTBE9DQTEx
ETAPBgNVBAsTCENGQ0EgTFJBMRkwFwYDVQQLExBPcmdhbml6YXRpb25hbC0xMTAw
LgYDVQQDFCc4MzEwMDAwMDAwMDgzMDQwQDAwMDQwMDAwOlNJR05AMDAwMDAwMDEw
ggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDFG+NnBXN++aUUAbgVFOt/
pi2McB79P+tmkS98Pnlj+pEvCc2nltq2VZzfJvGb1UE6lXKXoCG+NosZMj64uda9
Du2up78Z92HGdT2tkZ0RaoouR4jCY0Bmz0+5zObjR607vwBTvln9idG9ZGK2Lm35
QSxjpLolRPEnz/rgxFG9ezxVfI9eQ7JmuBk/OXyzjA1JQwAMhdAT3GJO0JMmMDvC
Q0pNyTsu1oyQPJoCaV3qPfpcvatMKYsVxo2Zeogqw2x2L6KE8BODrj6m6Ue1aUMn
9Ch1XbR/dB8M2M+nVtOAVb6DA6kVuNFlMl2uzxD8MQlhos8aT+vCx1v9p21k3+jz
AgMBAAGjDzANMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQUFAAOCAQEAhgW/gcDa
fqs0oWDH81XnTVvDCp5mwDo+wxgzVRTEtudU6seKcc2kiBe1RqegtUX2le/eAzcD
mo7nxHMy73ANdP/wha+P2gp+mo3buhO244pQphMV+Yu8djHTFH8+hRkCbnsrndYc
qNiJ/yhsUpaJ4nY+oEoyut0id6QddKiNPYoTFz0fy/VqNP6g+23zFy6sIg+gffVZ
6o3CsZVu9z5umUjzfV384iSWovq+/IdSZ4g/jerdPtje/CKYTmzG5nsCa/s+i7Rf
D5scSlfi7iW2Q7Sc/HlrtOAglt7IyjRSsFPPxuBXmSITc2GDKyKI46u8RXpccAUh
YspJ5MXOYLZN7A==
-----END CERTIFICATE-----';
		file_put_contents(IA_ROOT . '/attachment/unionpay/UpopRsaCert.cer', trim($public_rsa));
	}
	
	$dat = iserializer($pay);
	if(pdo_update('uni_settings', array('payment' => $dat), array('uniacid' => $_W['uniacid'])) !== false) {
		message('保存支付信息成功. ', 'refresh');
	} else {
		message('保存支付信息失败, 请稍后重试. ');
	}
	exit();
}
$pay['unionpay']['signcertexists'] = file_exists(IA_ROOT . '/attachment/unionpay/PM_'.$_W['uniacid'].'_acp.pfx');

$accs = uni_accounts();
$accounts = array();
if(!empty($accs)) {
	foreach($accs as $acc) {
		if($acc['type'] == '1' && $acc['level'] >= '3') {
			$accounts[$acc['acid']] = array_elements(array('name', 'acid', 'key', 'secret'), $acc);
		}
	}
}
template('profile/payment');
