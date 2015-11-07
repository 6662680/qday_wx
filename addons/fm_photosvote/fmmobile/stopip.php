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
		$myavatar = $avatar;
		$mynickname = $nickname;
		$title = $reply['title'];
		if ($_GPC['iptype'] == 'local') {
			$diqu = $_GPC['diqu'];
			$nowlocal = $_GPC['nowlocal'];
			//if (!empty($nowlocal)) {
			//	setcookie("user_diqu_".$uniacid, $diqu, time()+3600*2);
			//	setcookie("user_local_".$uniacid, $nowlocal, time()+3600*2);
			//}
			if (!empty($reply['iplocaldes'])) {
				$str = array('#限制地区#'=>$diqu,'#用户地区#'=>$nowlocal);
				$des = strtr($reply['iplocaldes'],$str);
				
			}else {
				$des = "你所在的地区不在本次投票地区。<br />本次投票地区： <br /><span class=\"text-info\">".$diqu."</span><br /> 内";
			}

			
		}else {
			$des = "你所在的IP无法访问!<br/>请稍后访问";
		}


		//$_share['link'] = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'fromuser' => $from_user));//分享URL
		 $_share['title'] = $reply['sharetitle'];
		$_share['content'] =  $reply['sharecontent'];
		$_share['imgUrl'] = toimage($reply['sharephoto']);
		
		
		$toye = $this->_stopllq('stopip');
		include $this->template($toye);
		