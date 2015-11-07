<?php
global $_W,$_GPC;
$uid = $this->checkauth();
$input = $_GPC['__input'];

$data = array();
$data['fopenid'] = $_W['openid'];
$data['money'] = floatval($input['money']);
$data['uid'] = $input['uid'];
$data['uniacid']=$_W['uniacid'];
$data['message'] = $input['message'];
$data['avatar'] = $profile['avatar'];
$data['nickname'] = $profile['nickname'];
$data['createtime'] = time();
$data['status'] = 0;

$return = pdo_insert('meepo_begging_user',$data);
$begid = pdo_insertid();

if(empty($return)){
	die(json_encode(array('status'=>false,'message'=>'提交失败')));
}else{
			//跳转到支付页面
			// $parmas = array();
			// $params['tid'] = 'MEEPO_BEGGING_'.$begid;
			// $params['user'] = $uid;
			// $params['fee'] = floatval($post['money']);
			// $params['title'] = '一分也是爱，不要嫌少';
			// $params['ordersn'] = 'MEEPO_BEGGING_'.random(5,1);
			// $params['virtual'] = true;

			// $this->pay($parmas);
	die(json_encode(array('status'=>true,'message'=>'提交订单成功','begid'=>$begid)));
}