<?php

global $_W,$_GPC;
$uid = $this->checkauth();
$setting = $this->module['config'];

$uid = !empty($_GPC['uid'])?$_GPC['uid']:$uid;

if($uid>0){
	$sql = "SELECT * FROM ".tablename('meepo_begging')." WHERE uid = :uid limit 1";
	$params = array(':uid'=>$uid);
	$begging = pdo_fetch($sql,$params);
	if(empty($begging)){
		pdo_insert('meepo_begging',array('uid'=>$uid,'uniacid'=>$_W['uniacid'],'money'=>0,'createtime'=>time()));
	}
}
$user = mc_fetch($uid);
if(empty($user['avatar'])){
	load()->func('communication');
	if(empty($_W['acid'])){
		$_W['acid'] = pdo_fetchcolumn("SELECT acid FROM ".tablename('mc_mapping_fans')." WHERE uniacid='{$_W['uniacid']}' AND openid = '{$_W['openid']}'");
	}
	$account = account_fetch($_W['acid']);
	load()->classs('weixin.account');
	$accObj= WeixinAccount::create($_W['account']['acid']);
	$account['access_token']['token'] = $accObj->fetch_token();

	if(empty($account['access_token']['token'])){
		return false;
	}
	$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$account['access_token']['token']."&openid=".$_W['openid']."&lang=zh_CN";
	$re = ihttp_get($url);

	if ($re['code'] == 200) {
		$userinfo = json_decode($re['content'],true);
		if($userinfo['errcode'] == '41001'){
			return $this->respText($userinfo['errmsg']);
		}

		$user['nickname'] = stripslashes($userinfo['nickname']);
		$user['avatar'] = rtrim($userinfo['avatar'], '0') . 132;
		$user['gender'] = $userinfo['sex'];
		$user['nationality'] = $userinfo['country'];
		$user['resideprovince'] = $userinfo['province'] . '省';
		$user['residecity'] = $userinfo['city'] . '市';

		pdo_update('mc_members',$user,array('uid'=>$_W['member']['uid']));
	}

}
$user['content'] = $setting['mainContent']?$setting['mainContent']:'实在揭不开锅了，只有讨饭了，一分也是爱，大爷赏点吧！';
$user['money'] = $begging['money']+$begging['cash'];
$user['group'] = $setting['groupTitle']?$setting['groupTitle']:'丐帮帮众';
$user['activity'] = '关于乞讨';
$user['about']['url'] = $_W['siteroot'].'app/'.$this->createMobileUrl('about');

$user['createtime'] = $begging['createtime'];
$sql = "SELECT COUNT(*) FROM ".tablename('meepo_begging')." WHERE uniacid = :uniacid AND money > :money";
$params = array(':uniacid'=>$_W['uniacid'],':money'=>$begging['money']);
$user['num'] = pdo_fetchcolumn($sql,$params);
$user['num'] = $user['num']+1;

$user['title'] = $setting['mainTitle']?$setting['mainTitle']:'一分也是爱，大爷赏点吧！';
$user['bg_img'] = $setting['mainImage']?$setting['mainImage']:'../addons/meepo_begging/template/mobile/img/001.jpg';
$user['confirm'] = '确认施舍';
$user['foottitle'] = '一分也是爱，不要嫌少哦';
$user['footmoney'] = '金额';
$user['footmessage'] = '留言';
$user['share'] = $setting['footerRight']?$setting['footerRight']:'立即乞讨';
$user['tixian'] = $setting['footerLeft']?$setting['footerLeft']:'立即提现';

$user['footer'] = $setting['otherFooterLeft']?$setting['otherFooterLeft']:'立即打赏';
$user['meto'] = $setting['otherFooterRight']?$setting['otherFooterRight']:'我也要参加';

$user['checkFollow'] = $_W['siteroot'].'app/'.$this->createMobileUrl('checkFollow');

if($_GPC['uid'] == $_W['member']['uid']){
	$user['isMe'] = true;
}else{
	$user['isMe'] = false;
}
$user['notMemessage'] = $setting['otherTopMessage']?$setting['otherTopMessage']:'参加乞讨活动，需要向帮好友支付任意金额，支付完成后自动跳转自己的活动链接，请收藏!';
$user['isMemessage'] = $setting['topMessage']?$setting['topMessage']:'赶紧告诉小伙伴吧，凑足了饭钱，就不会饿肚子了!';
if(empty($user['title'])){
	$user['title'] = '一分也是爱，大爷赏点吧！';
}
$sql = "SELECT * FROM ".tablename('meepo_begging_user')." WHERE uid = :uid AND status = :status ORDER BY createtime DESC";
$params = array(':uid'=>$uid,':status'=>1);
$items = pdo_fetchall($sql,$params);

foreach($items as $ri){
	$user2 = fans_search($ri['fopenid']);
	$ri['avatar'] = $user2['avatar'];
	$ri['nickname'] = $user2['nickname'];
	$ri['time'] = (time()-$ri['createtime']);

	$res = get_timef($ri['createtime'],time());
	$date=$res['day'];
	$hour=$res['hour'];
	$minute=$res['min'];
	$second=$res['sec'];

	
	if(!empty($ri)){
		$user['items'][] = $ri;
	}
}

die(json_encode($user));