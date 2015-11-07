<?php
	
	//fans_require($_W['fans']['from_user'], array('mobile'));
	$follow = pdo_fetch("select uid from ".tablename('mc_mapping_fans')." where uniacid = ".$weid." and openid = '".$_W['openid']."'");
	$info = mc_fetch($follow['uid'], array('avatar', 'realname', 'credit1', 'address', 'mobile'));
	//$info = fans_search($_W['fans']['from_user'], array('avatar', 'realname', 'credit1', 'address', 'mobile'));
	if(substr_count($info['avatar'],'http://')>1){
		$info['avatar'] = pdo_fetchcolumn("select avatar from ".tablename('mc_members')." where uniacid = ".$weid." and from_user = '".$_W['openid']."'");
	}
	$starttime = strtotime(date('Y-m-d 00:00:00'));
	$endtime = strtotime(date('Y-m-d 23:59:59'));
	$credit = pdo_fetchcolumn("select credit from ".tablename('weilive_credit')." where from_user = '".$_W['openid']."' and createtime >= ".$starttime." and createtime <= ".$endtime);
	$credit = empty($credit)?-1:$credit;
	if($op=='complete'){
		if($_GPC['opp']=='post'){
			$info = array(
				'realname'=>$_GPC['realname'],
				'mobile'=>$_GPC['mobile'],
				'address'=>trim($_GPC['address'])
			);
			pdo_update('mc_members', $info, array('uid'=>$follow['uid']));
			echo 1;
			exit;
		}
		include $this->template('complete');
		exit;
	}
	
	if($op=='report'){
		$credit1 = mt_rand(1,10);
		$credits = array(
			'weid'=>$weid,
			'from_user'=>$_W['openid'],
			'realname'=>$info['realname'],
			'mobile'=>$info['mobile'],
			'credit'=>$credit1,
			'flag'=>0,
			'createtime'=>time(),
		);
		if($credit == -1){
			pdo_insert('weilive_credit', $credits);
			$fcredit = mc_fetch($follow['uid'], array('credit1'));
			pdo_update('mc_members', array('credit1'=>$fcredit['credit1']+$credit1), array('uid'=>$follow['uid']));
			echo $credit1;
			exit;
		} else {
			echo 0;
			exit;
		}
	}
	
	if($op=='creditlog'){
		$creditlogs = pdo_fetchall("select * from ".tablename('weilive_credit')." where weid = ".$weid." and from_user = '".$_W['openid']."'");
		include $this->template('creditlog');
		exit;
	}
	
	if($op=='mycoupon'){
		$mycoupons = pdo_fetchall("select a.* from ".tablename('weilive_coupon')." as c left join ".tablename('weilive_activity')." as a on c.weid = a.weid and c.actid = a.id where a.flag = 0 and c.isuse = 0 and c.weid = ".$weid." and c.from_user = '".$_W['openid']."'");
		$shops = pdo_fetchall("select id, title from ".tablename('weilive_stores')." where weid = ".$weid." and checked = 1 and status = 1");
		$shop = array();
		foreach($shops as $s){
			$shop[$s['id']] = $s['title'];
		}
		include $this->template('mycoupon');
		exit;
	}
	
	if($op=='myactivity'){
		$myactivities = pdo_fetchall("select a.* from ".tablename('weilive_coupon')." as c left join ".tablename('weilive_activity')." as a on c.weid = a.weid and c.actid = a.id where a.flag = 1 and c.isuse = 0 and c.weid = ".$weid." and c.from_user = '".$_W['openid']."'");
		$shops = pdo_fetchall("select id, title from ".tablename('weilive_stores')." where weid = ".$weid." and checked = 1 and status = 1");
		$shop = array();
		foreach($shops as $s){
			$shop[$s['id']] = $s['title'];
		}
		include $this->template('myactivity');
		exit;
	}
	
	include $this->template('home');
?>