<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 注册短信验证
 */
defined('IN_IA') or exit('Access Denied');
	global $_GPC,$_W;
	WeSession::$expire = 600;	
	WeSession::start();
	$mobile = $_GPC['mobile'];
	if ($_GPC['type'] == 'verify') {
		$member = pdo_fetch("select * from".tablename("xcommunity_member")."where weid='{$_W['weid']}' and mobile=:mobile",array(':mobile' => $mobile);
	}else{
		$member = pdo_fetch("select * from".tablename("xcommunity_business")."where weid='{$_W['weid']}' and mobile=:mobile",array(':mobile' => $mobile));
	}
	if (!empty($member)) {
		//已经注册
		message('已经注册');
	}
	if($mobile==$_SESSION['mobile']){
		$code=$_SESSION['code'];
	}else{
		$code= random(6,1);
		$_SESSION['mobile']=$mobile;
		$_SESSION['code']=$code;
	}
	//验证是否开启
	if($this->module['config']['verifycode'] || $this->module['config']['businesscode']){
		$mobile    = $_SESSION['mobile'];
		$tpl_id    = $this->module['config']['resgisterid'];
		$tpl_value = urlencode("#code#=$code");
		$appkey    = $this->module['config']['sms_account'];
		$params    = "mobile=".$mobile."&tpl_id=".$tpl_id."&tpl_value=".$tpl_value."&key=".$appkey;
		$url       = 'http://v.juhe.cn/sms/send';
		$content   = ihttp_post($url,$params);
		return $content;
	}