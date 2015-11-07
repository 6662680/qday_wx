<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

		if ($istop['ipannounce'] == 1) {
			$announce = pdo_fetchall("SELECT * FROM " . tablename($this->table_announce) . " WHERE uniacid= '{$uniacid}' AND rid= '{$rid}' ORDER BY id DESC");
			
		}
				
		
		//赞助商
		if ($reply['isreg'] == 1) {
			$advs = pdo_fetchall("SELECT * FROM " . tablename($this->table_advs) . " WHERE enabled=1 AND ismiaoxian = 0  AND uniacid= '{$uniacid}'  AND rid= '{$rid}' ORDER BY displayorder ASC");
			foreach ($advs as &$adv) {
				if (substr($adv['link'], 0, 5) != 'http:') {
					$adv['link'] = "http://" . $adv['link'];
				}
			}
			unset($adv);
		}
		
		//查询是否参与活动
		//if(!empty($from_user)) {
		$where = '';
		
		
		$mygift = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));			
		//}
		$picarr = $this->getpicarr($uniacid,$reply['tpxz'],$from_user,$rid);
		
		
		
		$reply['sharetitle']= $this->get_share($uniacid,$rid,$from_user,$reply['sharetitle']);
		$reply['sharecontent']= $this->get_share($uniacid,$rid,$from_user,$reply['sharecontent']);
		//整理数据进行页面显示
		$myavatar = $avatar;
		$mynickname = $nickname;
		$title = $reply['sharetitle'] . '报名';
		
		$_share['link'] = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'fromuser' => $from_user));//分享URL
		 $_share['title'] = $reply['sharetitle'];
		$_share['content'] =  $reply['sharecontent'];
		$_share['imgUrl'] = toimage($reply['sharephoto']);
		
		
		$toye = $this->_stopllq('reg');
		include $this->template($toye);
		