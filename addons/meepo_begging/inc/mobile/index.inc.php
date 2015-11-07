<?php
//乞讨活动首页
global $_W,$_GPC;
load()->model('mc');
$uid = $this->checkauth();
		//进去以后先到 启动页面
if(empty($_GPC['uid'])){
	if(!empty($uid)){
		$url = $this->createMobileUrl('index',array('uid'=>$uid));
		header("Location:$url");
		exit();
	}
}

$uid = $_GPC['uid']?$_GPC['uid']:$uid;

$setting = $this->module['config'];


if($uid>0){
	$sql = "SELECT * FROM ".tablename('meepo_begging')." WHERE uid = :uid limit 1";
	$params = array(':uid'=>$uid);
	$begging = pdo_fetch($sql,$params);
	if(empty($begging)){
		pdo_insert('meepo_begging',array('uid'=>$uid,'uniacid'=>$_W['uniacid'],'money'=>0,'createtime'=>time()));
	}
}
$user = mc_fetch($uid,array('avatar','nickname'));
$user['createtime'] = $begging['createtime'];
$user['money'] = $begging['money']+$begging['cash'];
		// if(empty($_GPC['bootPage2'])){
		// 	$item = array();
		// 	if(empty($_GPC['uid'])){
		// 		$item['href'] = $_W['siteroot'].'app/'.$this->createMobileUrl('index',array('uid'=>$uid));
		// 	}else{
		// 		$item['href'] = $_W['siteroot'].'app/'.$this->createMobileUrl('index',array('uid'=>$_GPC['uid']));
		// 	}
		// 	// //设置缓存，缓存期间不加载 启动页
		// 	// isetcookie('bootPage', 'noneBootPage');
		// 	// unset($_GPC['bootPage']);
		// 	$item['thumb'] = '../addons/meepo_begging/template/mobile/img/002.jpg?t='.time();

		// 	$this->template('bootPage');
		// }

$bo = array();
if(!empty($setting['bootFirst'])){
	$bo['href'] = '#/event/home/'.$uid;
	$bo['thumb'] = tomedia($setting['bootFirst']);
	$boot['first'] = $bo;
}else{
	$bo['href'] = '#/event/home/'.$uid;
	$bo['thumb'] = '../addons/meepo_begging/template/mobile/img/003.jpg';
	$boot['first'] = $bo;
}
if(!empty($setting['bootSecond'])){
	$bo['href'] = '#/event/home/'.$uid;
	$bo['thumb'] = tomedia($setting['bootSecond']);
	$boot['second'] = $bo;
}else{
	$bo['href'] = '#/event/home/'.$uid;
	$bo['thumb'] = '../addons/meepo_begging/template/mobile/img/003.jpg';
	$boot['second'] = $bo;
}
if(!empty($setting['bootThird'])){
	$bo['href'] = '#/event/home/'.$uid;
	$bo['thumb'] = tomedia($setting['bootThird']);
	$boot['third'] = $bo;
}else{
	$bo['href'] = '#/event/home/'.$uid;
	$bo['thumb'] = '../addons/meepo_begging/template/mobile/img/002.jpg';
	$boot['third'] = $bo;
}

$boot['title'] = $setting['bootTitle']?$setting['bootTitle']:'微乞丐';
$boot['uid'] = $uid;

$confirm['title'] = $setting['orderTitle']?$setting['orderTitle']:'一分也是爱，大爷多点爱';
$confirm['money'] = $setting['orderMoney']?$setting['orderMoney']:'打赏金额';
$confirm['message'] = $setting['orderMessage']?$setting['orderMessage']:'打赏留言';
$confirm['submit'] = $setting['orderSubmit']?$setting['orderSubmit']:'立即打赏';


$menu['lefttitle'] = $setting['mainLeftTitle']?$setting['mainLeftTitle']:'丐帮总舵排行榜';
$menu['righttitle'] = $setting['mainRightTitle']?$setting['mainRightTitle']:'我的打赏排行榜';


$this->__init();
$limit = intval($setting['rankRightNum'])?intval($setting['rankRightNum']):50;
$sql = "SELECT * FROM ".tablename('meepo_begging_user')." WHERE uid = :uid AND status = :status ORDER BY money DESC LIMIT {$limit}";
$params = array(':uid'=>$uid,':status'=>1);
$rights = pdo_fetchall($sql,$params);

foreach($rights as $ri){
	$user2 = fans_search($ri['fopenid']);
	$ri['avatar'] = $user2['avatar'];
	$ri['nickname'] = $user2['nickname'];
	if(!empty($ri)){
		$menu['right'][] = $ri;
	}
}

		// 丐帮排名
$limit = intval($setting['rankLeftNum'])?intval($setting['rankLeftNum']):50;
$sql = "SELECT * FROM ".tablename('meepo_begging')." WHERE uniacid = :uniacid ORDER BY money+cash DESC  LIMIT {$limit}";
$params = array(':uniacid'=>$_W['uniacid']);
$lefts = pdo_fetchall($sql,$params);
$menu['left'] = array();
foreach($lefts as $ri){
	$user2 = mc_fetch($ri['uid']);
	$ri['avatar'] = $user2['avatar'];
	$ri['nickname'] = $user2['nickname'];
	$ri['createtime'] = date('Y-m-d',$ri['createtime']);
			// $_W['siteroot'].'app/'.$this->createMobileUrl('index',array('uid'=>$ri['uid'])).
	$ri['href'] = '#/event/home/'.$ri['uid'];
	$ri['money'] = $ri['money'] + $ri['cash'];
	if(!empty($ri)){
		$menu['left'][] = $ri;
	}

			// $params = array(':uid'=>$ri['uid'],':status'=>1);
			// $sql = "SELECT SUM(money) FROM ".tablename('meepo_begging_user')." WHERE uid = :uid AND status = :status";
			// $money = pdo_fetchcolumn($sql,$params);
			// pdo_update('meepo_begging',array('money'=>$money),array('uid'=>$ri['uid']));
}

$time = time()-$user['createtime'];
		//秒$user['createtime'])/(60*60*24));
		// $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);

$res = get_timef($user['createtime'],time());
$date=$res['day'];
$hour=$res['hour'];
$minute=$res['min'];
$second=$res['sec'];

$str = $data?$data.'天':'';
$str .= $hour?$hour.'小时':'';
$str .= $minute?$minute.'分':'';
$str .= $second?$second.'秒':'';

		//提现页面设置
$contact = array();
$contact['title'] = $setting['conTitle']?$setting['conTitle']:'提出饭钱';
$contact['avatar'] = $user['avatar'];
$contact['nickname'] = $user['nickname'];
$contact['group'] = $user['group'];
$contact['content'] = $setting['conContent']?$setting['conContent']:'辛苦了大半天，终于可以饱餐一顿了，赶紧提现吧！';
$contact['money'] = $setting['conMoney']?$setting['conMoney']:'提现金额';
$contact['message'] = $setting['conMessage']?$setting['conMessage']:'请输入提现留言';
$contact['submit'] = $setting['conSubmit']?$setting['conSubmit']:'立即提现';
$contact['openid'] = $setting['conOpenid']?$setting['conOpenid']:'请输入openid';
$contact['apply'] = $setting['conApply']?$setting['conApply']:'请输入支付宝账号';
$money = $begging['money'];

$_share = array();
$_share['title'] = $user['nickname'].'可怜可怜我吧，给我一点爱！';
$_share['desc'] = '经过'.$str.'的努力，已经讨到'.$user['money'].'元，赶紧来和我一起乞讨吧！';
$_share['imgUrl'] = tomedia($user['avatar']);
$_share['link'] = $_W['siteroot'].'app/'.$this->createMobileUrl('index',array('uid'=>$uid),true).'#/event/home/'.$uid;

include $this->template('index');