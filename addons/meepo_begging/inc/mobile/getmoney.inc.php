<?php

global $_W;
$uid = $this->checkauth();

if($uid>0){
	$sql = "SELECT money FROM ".tablename('meepo_begging_money')." WHERE uniacid = :uniacid AND uid = :uid";
	$params = array(':uniacid'=>$_W['uniacid'],':uid'=>$uid);
	$money = pdo_fetchcolumn($sql,$params);
	die(json_encode(array('status'=>true,'message'=>'获取余额成功','money'=>floatval($money))));
}else{
	die(json_encode(array('status'=>false,'message'=>'登陆验证失败，请检查是否注册或是否取消关注')));
}