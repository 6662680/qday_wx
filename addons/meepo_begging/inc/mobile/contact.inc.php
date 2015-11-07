<?php
global $_W,$_GPC;
$uid = $this->checkauth();
// if($_W['ispost']){
$sql = "SELECT * FROM ".tablename('meepo_begging')." WHERE uid = :uid AND uniacid = :uniacid";
$params = array(':uniacid'=>$_W['uniacid'],':uid'=>$_W['member']['uid']);
$begging = pdo_fetch($sql,$params);
$input = $_GPC['__input'];

$data = array();
$data['type'] = 1;
if(!empty($input['apply'])){
	$data['apply'] = trim($input['apply']);
	$data['type'] = 2;
}
$data['openid'] = $_W['openid'];
$data['money'] = floatval($input['money']);
$data['uid'] = $_W['member']['uid'];
$data['uniacid'] = $_W['uniacid'];
$data['createtime'] = time();
$data['status'] = 0;

$begging['money'] = floatval($begging['money']);

if($data['money']<=0){
	die(json_encode(array('status'=>false,'message'=>'对不起，请输入大于0的金额')));
}
//扣除金钱
$success = false;
if($begging['money']< $data['money']){
	die(json_encode(array('status'=>false,'message'=>'对不起，余额不足')));
}else{
	$success = pdo_update('meepo_begging',
		array('money'=>$begging['money']-$data['money'],'cash'=>$begging['cash']+$data['money']),
		array('uid'=>$data['uid'],'uniacid'=>$data['uniacid']));
}


//插入日志
if($success){
	$success = pdo_insert('meepo_begging_log',$data);
}

if($success){
	die(json_encode(array('status'=>true,'message'=>'提现成功','href'=>$_W['siteroot'].'app/'.$this->createMobileUrl('index',array('uid'=>$_W['member']['uid'])).'#/event/home/'.$_W['member']['uid'])));
}else{
	die(json_encode(array('status'=>false,'message'=>'提交失败')));
}
		// }