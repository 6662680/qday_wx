<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('mc');

$_W['uniacid'] = intval($_GPC['i']);
if(empty($_W['uniacid'])) {
	$_W['uniacid'] = intval($_GPC['weid']);
}
$_W['uniaccount'] = uni_fetch($_W['uniacid']);
//var_dump($_W['uniaccount']);
if(empty($_W['uniaccount'])) {
	exit('指定主公众号不存在。');
}

$_W['session_id'] = '';
if (isset($_GPC['state']) && !empty($_GPC['state']) && strexists($_GPC['state'], 'we7sid-')) {
	$pieces = explode('-', $_GPC['state']);
	$_W['session_id'] = $pieces[1];
	unset($pieces);
}
if (empty($_W['session_id'])) {
	$_W['session_id'] = $_COOKIE[session_name()];
}
if (empty($_W['session_id'])) {
	$_W['session_id'] = "{$_W['uniacid']}-" . random(20) ;
	$_W['session_id'] = md5($_W['session_id']);
	setcookie(session_name(), $_W['session_id']);
}
session_id($_W['session_id']);

load()->classs('wesession');
WeSession::start($_W['uniacid'], CLIENT_IP);

if (empty($_W['acid'])){
	$_W['acid'] = intval($_GPC['j']);
}
if (empty($_W['acid']) && !empty($_SESSION['acid'])) {
	$_W['acid'] = intval($_SESSION['acid']);
}
if (!empty($_W['acid'])) {
	$_W['account'] = account_fetch($_W['acid']);
	if (empty($_W['account']) || intval($_W['account']['uniacid']) != intval($_W['uniacid'])) {
		$_W['acid'] = 0;
		$_W['account'] = null;
	}
}
if (empty($_W['account'])) {
	$accounts = uni_accounts();
	foreach($accounts as $k => $v){
		$_W['account'] = $v;
		$_W['acid'] = $_W['account']['acid'];
		break;
	}
	unset($k, $v, $accounts);
}
if (empty($_W['account'])) {
	exit('指定(子)公众号不存在。');
}

if ((!empty($_SESSION['acid']) && $_W['acid'] != $_SESSION['acid']) || 
	(!empty($_SESSION['uniacid']) && $_W['uniacid'] != $_SESSION['uniacid'])) {
	$keys = array_keys($_SESSION);
	foreach ($keys as $key) {
		unset($_SESSION[$key]);
	}
	unset($keys, $key);
}

$_SESSION['acid'] = $_W['acid'];
$_SESSION['uniacid'] = $_W['uniacid'];

if (!empty($_SESSION['openid'])) {
	$_W['openid'] = $_SESSION['openid'];
	$_W['fans'] = mc_fansinfo($_W['openid']);
	$_W['fans']['from_user'] = $_W['openid'];
}
if (!empty($_SESSION['uid']) || (!empty($_W['fans']) && !empty($_W['fans']['uid']))) {
	$uid = intval($_SESSION['uid']);
	if (empty($uid)) {
		$uid = $_W['fans']['uid'];
	}
	_mc_login(array('uid' => $uid));
	unset($uid);
}
if (empty($_W['openid']) && !empty($_SESSION['oauth_openid'])) {
	$_W['openid'] = $_SESSION['oauth_openid'];
	$_W['fans'] = array(
		'openid' => $_SESSION['oauth_openid'],
		'from_user' => $_SESSION['oauth_openid'],
		'follow' => 0
	);
}
$oauth_acc = $_W['account'];
if (intval($oauth_acc['level']) != 4) {
	$setting = uni_setting($_W['uniacid'], array('oauth'));
	$oauth = $setting['oauth'];
	if (!empty($oauth) && !empty($oauth['status']) && !empty($oauth['account'])) {
		$oauth_acc = account_fetch($oauth['account']);
	}
	unset($setting, $oauth);
}
if(!empty($oauth_acc) && intval($oauth_acc['level']) == 4) { 	$_W['oauth_account'] = $oauth_acc;
}
unset($oauth_acc);

if (($_W['container'] == 'wechat' && !empty($_W['oauth_account']) && !$_GPC['logout'] && empty($_W['openid']) && ($controller != 'auth' || ($controller == 'auth' && !in_array($action, array('forward', 'oauth'))))) ||
	($_W['container'] == 'wechat' && !empty($_W['oauth_account']) && (intval($_W['account']['level']) < 4) && !$_GPC['logout'] && empty($_SESSION['oauth_openid']) && ($controller != 'auth'))) {
	$state = 'we7sid-'.$_W['session_id'];
	if (empty($_SESSION['dest_url'])) {
		$_SESSION['dest_url'] = base64_encode($_SERVER['QUERY_STRING']);
	}
	$url = "{$_W['siteroot']}app/index.php?i={$_W['uniacid']}&j={$_W['acid']}&c=auth&a=oauth&scope=snsapi_base";
	$callback = urlencode($url);
	
	$forward = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$_W['oauth_account']['key']}&redirect_uri={$callback}&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
	header('Location: ' . $forward);
	exit();
}

$_W['account']['groupid'] = $_W['uniaccount']['groupid'];
$_W['account']['qrcode'] = "{$_W['attachurl']}qrcode_{$_W['acid']}.jpg?time={$_W['timestamp']}";
$_W['account']['avatar'] = "{$_W['attachurl']}headimg_{$_W['acid']}.jpg?time={$_W['timestamp']}";

if ($_W['container'] == 'wechat') {
	$jsauth_acc = $_W['account'];
	if ($jsauth_acc['level'] < 3) {
		load()->model('account');
		$unisetting = uni_setting();
		$acid = intval($unisetting['jsauth_acid']);
		if(!empty($acid) && $acid != $_W['acid']){
			$account = account_fetch($acid);
			if(!empty($account)){
				$jsauth_acc = $account;
			}
			unset($account);
		}
		unset($acid, $unisetting);
	}
	
	load()->classs('weixin.account');
	$accObj = WeiXinAccount::create($jsauth_acc);
	$_W['account']['jssdkconfig'] = $accObj->getJssdkConfig();
	$_W['account']['jsauth_acid'] = $jsauth_acc['acid'];
	
	unset($jsauth_acc, $accObj);
}
$_W['card_permission'] = 0;
if($_W['acid'] && $_W['account']['level'] >= 3 && $_W['container'] == 'wechat') {
	$_W['card_permission'] = 1;
}

load()->func('compat.biz');