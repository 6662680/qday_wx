<?php
	$follow = pdo_fetch("select uid, follow from ".tablename('mc_mapping_fans')." where uniacid = ".$weid." and openid = '".$_W['openid']."'");
	if($op=='detail'){
		//活动ID
		$id = $_GPC['id'];
		$detail = pdo_fetch("select * from ".tablename('weilive_activity')." where isopen = 1 and start_time < ".time()." and end_time > ".time()." and id = ".$id);
		$store = pdo_fetch("select * from ".tablename('weilive_stores')." where id = ".$detail['storeid']);
		$numed = pdo_fetchcolumn("select nums from ".tablename('weilive_coupon')." where weid = ".$weid." and isuse = 0 and flag = 0 and from_user = '".$_W['openid']."' and actid = ".$id);
		$numed = empty($numed)?0:$numed;
	}
	if($op=='storedetail'){
		//店家ID
		$id = $_GPC['id'];
		$setting = pdo_fetch("SELECT * FROM " . tablename('weilive_setting') . " WHERE weid = :weid ", array(':weid' => $weid));
		$pindex = max(1, intval($_GPC['page']));
		$psize = empty($setting) ? 1 : intval($setting['pagesize']);
		$store = pdo_fetch("select * from ".tablename('weilive_stores')." where id = ".$id);
		$coupons = pdo_fetchall("select * from ".tablename('weilive_activity')." where flag = 0 and isopen = 1 and start_time < ".time()." and end_time > ".time()." and weid = ".$weid." and storeid = ".$id);
		$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where flag = 1 and isopen = 1 and start_time < ".time()." and end_time > ".time()." and storeid = ".$id);
		$comments = pdo_fetchall("select * from ".tablename('weilive_comment')." where storeid = ".$id." and isopen = 1 and createtime >= ".(time()-3600*720)." order by id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("select count(id) from ".tablename('weilive_comment')." where storeid = ".$id." and isopen = 1 and createtime >= ".(time()-3600*720));
		$pager = pagination1($total, $pindex, $psize);
		include $this->template('moreactivity');
		exit;
	}

	if($op=='more'){
		//店家ID
		$id = $_GPC['id'];
		$setting = pdo_fetch("SELECT * FROM " . tablename('weilive_setting') . " WHERE weid = :weid ", array(':weid' => $weid));
		$pindex = max(1, intval($_GPC['page']));
		$psize = empty($setting) ? 1 : intval($setting['pagesize']);
		$store = pdo_fetch("select * from ".tablename('weilive_stores')." where id = ".$id);
		$coupons = pdo_fetchall("select * from ".tablename('weilive_activity')." where flag = 0 and isopen = 1 and ischeck = 1 and start_time < ".time()." and end_time > ".time()." and weid = ".$weid." and storeid = ".$id." order by type asc");
		$type = array();
		$flag = 0;
		foreach($coupons as $key=>$c){
			if($key==0){
				$type[$flag][$key] = $c;			
			} else {
				$k = $key - 1;
				if($c['type']==$coupons[$k]['type']){
					$type[$flag][$key] = $c;
				} else {
					$type[$key][$key] = $c;
					$flag = $key;
				}
			}
		}
		//var_dump($type);
		$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where flag = 1 and isopen = 1 and ischeck = 1 and start_time < ".time()." and end_time > ".time()." and weid = ".$weid." and storeid = ".$id);
		$comments = pdo_fetchall("select * from ".tablename('weilive_comment')." where storeid = ".$id." and isopen = 1 and createtime >= ".(time()-3600*720)." order by id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("select count(id) from ".tablename('weilive_comment')." where storeid = ".$id." and isopen = 1 and createtime >= ".(time()-3600*720));
		$pager = pagination1($total, $pindex, $psize);
		include $this->template('moreactivity');
		exit;
	}
	if($op=='attach'){
		
		if(empty($follow) || $follow['follow']==0){
			echo -4;
			exit;
		}
		//领取数量
		$nums = intval($_GPC['nums']);
		//优惠券ID
		$actid = intval($_GPC['actid']);
		$coupon = array(
			'weid'=>$weid,
			'from_user'=>$_W['openid'],
			'actid'=>$actid,
			'nums'=>$nums,
			'isuse'=>0,
			'flag'=>0,
			'createtime'=>time()
		);
		$islegal = pdo_fetch("select num, numed, credit, catch from ".tablename('weilive_activity')." where id = ".$actid." and isopen = 1 and ischeck = 1 and weid = ".$weid." and start_time < ".time()." and end_time > ".time());
		
		if(empty($islegal)){
			//该优惠券不存在
			echo -1;
			exit;
		} else {
			//已领取数量
			$mynums = pdo_fetchcolumn("select nums from ".tablename('weilive_coupon')." where weid = ".$weid." and isuse = 0 and from_user = '".$_W['openid']."' and actid = ".$actid);
			$mynums = empty($mynums)?0:$mynums;
			//将拥有的总数量
			$numsing = $nums + $mynums;
			//$fans = fans_search($_W['fans']['from_user'], array('credit1'));
			$fans = pdo_fetch("select credit1 from ".tablename('mc_members')." where uid = ".$follow['uid']);
			if(!empty($mynums)){
				if($numsing <= $islegal['numed']){
					if($islegal['num'] == -1 || $numsing <= $islegal['num']){
						if($numsing <= $islegal['num']){
							$all = $islegal['num'] - $nums;
						} else {
							$all = -1;
						}
						if($fans['credit1']>=$islegal['credit']*$nums){
							$coupon['nums'] = $mynums + $nums;
							pdo_update('weilive_activity', array('num'=>$all), array('id'=>$actid));
							pdo_update('weilive_coupon', $coupon, array('weid'=>$weid, 'from_user'=>$_W['openid'], 'actid'=>$actid));
							pdo_update('mc_members', array('credit1'=>$fans['credit1']-$islegal['credit']*$nums), array('uid'=>$follow['uid']));
							//领取成功
							echo 1;
							exit;
						} else {
							//积分不够
							echo -5;
							exit;
						}
						
					}
				} elseif ($mynums >= $islegal['numed']){
					//不能再领取了
					echo -2;
					exit;
				} else {
					//领取数量不合法
					echo -3;
					exit;
				}
			} else {
				//限领数量合法
				if($numsing <= $islegal['numed']){
					if($islegal['num'] == -1 || $numsing <= $islegal['num']){
						if($nums <= $islegal['num']){
							$all = $islegal['num'] - $nums;
						} else {
							$all = -1;
						}
						if($fans['credit1']>=$islegal['credit']*$nums){
							$catch = $islegal['catch'] + 1;
							pdo_update('weilive_activity', array('num'=>$all, 'catch'=>$catch), array('id'=>$actid));
							pdo_insert('weilive_coupon', $coupon);
							pdo_update('mc_members', array('credit1'=>$fans['credit1']-$islegal['credit']*$nums), array('uid'=>$follow['uid']));
							echo 1;
							exit;
						} else {
							echo -5;
							exit;
						}
					}
				} elseif ($nums > $islegal['numed']){
					echo -3;
					exit;
				} else {
					echo -2;
					exit;
				}
			}
		}
	}
	
	if($op=='noreceive'){
		//优惠券ID
		$actid = intval($_GPC['actid']);
		//要取消的数量
		$mynums = pdo_fetchcolumn("select nums from ".tablename('weilive_coupon')." where weid = ".$weid." and from_user = '".$_W['openid']."' and actid = ".$actid);
		$actnums = pdo_fetch("select num, catch from ".tablename('weilive_activity')." where weid = ".$weid." and id = ".$actid);
		$all = $mynums + $actnums['num'];
		$catch = $actnums['catch'] - 1;
		pdo_update('weilive_activity', array('num'=>$all, 'catch'=>$catch), array('id'=>$actid));
		pdo_delete('weilive_coupon', array('weid'=>$weid, 'from_user'=>$_W['openid'], 'actid'=>$actid, 'isuse'=>0));
		echo 1;
		exit;
	}
	
	if($op=='checkcode'){
		$code = $_GPC['code'];
		$nums = $_GPC['nums'];
		$storeid = $_GPC['storeid'];
		$actid = $_GPC['actid'];
		$ischeck = pdo_fetch("select id from ".tablename('weilive_stores')." where id = ".$storeid." and pwd = '".$code."'");
		$superpwd = pdo_fetchcolumn("SELECT id FROM " . tablename('weilive_setting') . " WHERE pwd = '".$code."' and weid = :weid ", array(':weid' => $weid));
		$haha = 0;
		if(!empty($ischeck)){
			$haha++;
		}
		if(!empty($superpwd)){
			$haha++;
		}
		if($haha == 0){
			//验证码错误
			echo -1;
			exit;
		} else {
			$usedinfo = pdo_fetch("select used, score from ".tablename('weilive_activity')." where id = ".$actid);
			$used = $usedinfo['used'] + $nums;
			$use = array(
				'isuse'=>1,
				'usetime'=>time()
			);
			pdo_update('weilive_coupon', $use, array('weid'=>$weid, 'from_user'=>$_W['openid'], 'actid'=>$actid, 'isuse'=>0));
			pdo_update('weilive_activity', array('used'=>$used), array('id'=>$actid));
			$info = mc_fetch($follow['uid'], array('credit1', 'realname', 'mobile'));
			pdo_update('mc_members', array('credit1'=>$info['credit1']+$usedinfo['score']), array('uid'=>$follow['uid']));
			//积分记录
			if($usedinfo['score'] > 0){
				$credits = array(
					'weid'=>$weid,
					'storeid'=>$storeid,
					'from_user'=>$_W['fans']['from_user'],
					'realname'=>$info['realname'],
					'mobile'=>$info['mobile'],
					'credit'=>$usedinfo['score'],
					'flag'=>1,
					'createtime'=>time(),
				);
				pdo_insert('weilive_credit', $credits);
			}
			echo 1;
			exit;
		}
	}
	
	if($op=='comment'){
		$info = mc_fetch($follow['uid'], array('mobile'));
		$comment = array(
			'weid'=>$weid,
			'storeid'=>$_GPC['storeid'],
			'mobile'=>$info['mobile'],
			'comment'=>trim($_GPC['comment']),
			'isopen'=>1,
			'createtime'=>time()
		);
		pdo_insert('weilive_comment', $comment);
		echo 1;
		exit;
	}
	
	include $this->template('coupon');
?>