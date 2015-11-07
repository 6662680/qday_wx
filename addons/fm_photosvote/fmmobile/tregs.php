<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
$reply['sharetitle']= $this->get_share($uniacid,$rid,$from_user,$reply['sharetitle']);
		$reply['sharecontent']= $this->get_share($uniacid,$rid,$from_user,$reply['sharecontent']);
		//整理数据进行页面显示
		$myavatar = $avatar;
		$mynickname = $nickname;
		$title = $mynickname . '的录音室';
		
		$_share['link'] = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'fromuser' => $from_user));//分享URL
		 $_share['title'] = $reply['sharetitle'];
		$_share['content'] =  $reply['sharecontent'];
		$_share['imgUrl'] = toimage($reply['sharephoto']);
		
		
		if ($reply['voicemoshi'] == 0) {
			//if (preg_match('/Android/i',$agent)) {
			//	$isAndroid='true';
			//}else {
			//	$isAndroid='false';
			//}
			$toye = $this->_stopllq('treg');
			include $this->template($toye);
		}elseif ($reply['voicemoshi'] == 1) {
			
			$toye = $this->_stopllq('treg1');
			include $this->template($toye);
		}
	