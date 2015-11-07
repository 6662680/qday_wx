<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
		$vfrom = $_GPC['do'];

		if ($istop['ipannounce'] == 1) {
			$announce = pdo_fetchall("SELECT * FROM " . tablename($this->table_announce) . " WHERE uniacid= '{$uniacid}' AND rid= '{$rid}' ORDER BY id DESC");
			
		}
		//查询自己是否参与活动
		if(!empty($from_user)) {
		    $mygift = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
			
		}
		//查询是否参与活动
		if(!empty($tfrom_user)) {
		    $user = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $tfrom_user,':rid' => $rid));
			if ($user['status'] != 1 && $tfrom_user != $from_user) {
				$urlstatus =  $_W['siteroot'] .'app/'.$this->createMobileUrl('photosvoteview',array('rid'=> $rid));
				echo "<script>alert('ID:".$user['id']." 号选手正在审核中，请查看其他选手，谢谢！');location.href='".$urlstatus."';</script>";     
				die();
		  		//message('该选手正在审核中，请查看其他选手，谢谢！',$this->createMobileUrl('photosvoteview',array('rid'=> $rid)),'error');
		  	}

			$str = array('&'=>'%26');
			$url = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'duli'=> '1', 'fromuser' => $from_user, 'tfrom_user' => $tfrom_user));
			$url = $this->dwz($url);			
			$uavatar = toimage($user['avatar']);
			$ewmavatar = $_W['siteroot'] . 'attachment/headimg_'.$_W['acid'].'.jpg';
		    if ($user) {
				$yuedu = $tfrom_user.$from_user.$rid.$uniacid;
				//setcookie("user_yuedu", -10000);	
			   if ($_COOKIE["user_yuedu"] != $yuedu) {
					 pdo_update($this->table_users, array('hits' => $user['hits']+1,), array('rid' => $rid, 'from_user' => $tfrom_user));
					 setcookie("user_yuedu", $yuedu, time()+3600*24);
				}
		    }
			//$follow = pdo_fetch("SELECT follow FROM ".tablename('mc_mapping_fans')." WHERE uniacid = :uniacid and openid = :from_user", array(':uniacid' => $uniacid,':from_user' => $from_user));
		}
		$picarr = $this->getpicarr($uniacid,$reply['tpxz'],$tfrom_user,$rid);
		
		
		$starttime=mktime(0,0,0);//当天：00：00：00
		$endtime = mktime(23,59,59);//当天：23：59：59
		$times = '';
		$times .= ' AND createtime >=' .$starttime;
		$times .= ' AND createtime <=' .$endtime;
		$uservote = pdo_fetch("SELECT * FROM ".tablename($this->table_log)." WHERE uniacid = :uniacid AND from_user = :from_user  AND tfrom_user = :tfrom_user AND rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':tfrom_user' => $tfrom_user,':rid' => $rid));
		$uallonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));
		$udayonetp = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid AND from_user = :from_user AND tfrom_user = :tfrom_user AND rid = :rid '.$times.' ORDER BY createtime DESC', array(':uniacid' => $uniacid, ':from_user' => $from_user, ':tfrom_user' => $tfrom_user,':rid' => $rid));		
		
		//每个奖品的位置
		//虚拟人数据配置
		$now = time();
		if($now-$reply['xuninum_time']>$reply['xuninumtime']){
		    pdo_update($this->table_reply, array('xuninum_time' => $now,'xuninum' => $reply['xuninum']+mt_rand($reply['xuninuminitial'],$reply['xuninumending'])), array('rid' => $rid));
		}
		//虚拟人数据配置
		//参与活动人数
		$totals = $reply['xuninum'] + pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_users).' WHERE uniacid=:uniacid and rid=:rid', array(':uniacid' => $uniacid,':rid' => $rid));
		//参与活动人数
		//查询分享标题以及内容变量
		$reply['sharetitle']= $this->get_share($uniacid,$rid,$from_user,$reply['sharetitle']);
		$reply['sharecontent']= $this->get_share($uniacid,$rid,$from_user,$reply['sharecontent']);
		//整理数据进行页面显示
		$myavatar = $avatar;
		$mynickname = $nickname;
		$shareurl = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'duli'=> '2', 'fromuser' => $from_user, 'tfrom_user' => $tfrom_user));//分享URL
		$regurl = $_W['siteroot'] .'app/'.$this->createMobileUrl('reg', array('rid' => $rid));//关注或借用直接注册页
		$shouquan = base64_encode($_SERVER ['HTTP_HOST'].'anquan_ma_photosvote');
		$unrname = !empty($user['realname']) ? $user['realname'] : $user['nickname'] ;
		$title = $unrname . ' 的投票详情！';
		
		$sharetitle = $unrname . '正在参加'. $reply['title'] .'，快来为'.$unrname.'投一票吧！';
		$sharecontent = $unrname . '正在参加'. $reply['title'] .'，快来为'.$unrname.'投一票吧！';
		$picture = !empty($user['avatar']) ? toimage($user['avatar']) : toimage($user['photo']);
		
		
		$_share['link'] =$_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'duli'=> '2', 'fromuser' => $from_user, 'tfrom_user' => $tfrom_user));//分享URL
		 $_share['title'] = $unrname . '正在参加'. $reply['title'] .'，快来为'.$unrname.'投一票吧！';
		$_share['content'] = $unrname . '正在参加'. $reply['title'] .'，快来为'.$unrname.'投一票吧！';
		$_share['imgUrl'] = !empty($user['avatar']) ? toimage($user['avatar']) : toimage($user['photo']);
		
		
		$toye = $this->_stopllq('tuserphotos');
		include $this->template($toye);