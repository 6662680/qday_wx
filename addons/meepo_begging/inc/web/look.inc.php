<?php
global $_W,$_GPC;
load()->model('mc');
$uid = $_GPC['uid'];
$sql = "SELECT * FROM ".tablename('meepo_begging_user')." WHERE uid = :uid AND status = :status ORDER BY createtime DESC";
$params = array(':uid'=>$uid,':status'=>1);
$items = pdo_fetchall($sql,$params);

foreach($items as $ri){
	$sql = "SELECT uid FROM ".tablename('mc_mapping_fans')." WHERE uniacid = :uniacid AND openid = :openid";
	$params = array(':uniacid'=>$_W['uniacid'],':openid'=>$ri['fopenid']);
	$fuid = pdo_fetchcolumn($sql,$params);
	$user2 = mc_fetch($fuid);
	$ri['avatar'] = tomedia($user2['avatar']);
	$ri['nickname'] = $user2['nickname'];
	$ri['createtime'] = date('Y-m-d',$ri['createtime']);//秒
	
	if(!empty($ri)){
		$list[] = $ri;
	}
}

include $this->template('look');