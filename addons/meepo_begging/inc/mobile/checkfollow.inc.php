<?php

global $_W;
if($_W['fans']['follow']){
			//已经关注
	$uid = $this->checkauth();
	if(empty($uid)){
		checkauth();
	}else{
		$url = $_W['siteroot'].'app/'.$this->createMobileUrl('index',array('uid'=>$uid));
		header("Location:$url");
		exit();
	}
}else{
	$sql = 'SELECT `subscribeurl` FROM ' . tablename('account_wechats') . " WHERE `acid` = :acid";
	$subscribeurl = pdo_fetchcolumn($sql, array(':acid' => intval($_W['acid'])));
	message('正在跳转关注页面，说明：参加活动必须关注'.$_W['account']['name'].',然后再打开当前链接，进行活动参加！',$subscribeurl,success);
	exit();
}