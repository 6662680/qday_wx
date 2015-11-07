<?php
		
	if($op=='zone'){
		$location_as = pdo_fetchall("select distinct location_a from ".tablename('weilive_stores')." where weid = ".$weid." and location_c = '".$_GPC['text']."'");
		$data = array();
		$data['关闭'] = 0;
		foreach($location_as as $a){
			$data[$a['location_a']] = $this->createMobileUrl('kill', array('op'=>'area', 'location_a'=>$a['location_a']));
		}
		echo json_encode($data);
		exit;
	}
	
	if($op=='detail'){
		//活动ID
		$id = $_GPC['id'];
		$detail = pdo_fetch("select * from ".tablename('weilive_activity')." where isopen = 1 and ischeck = 1 and id = ".$id);
		$store = pdo_fetch("select * from ".tablename('weilive_stores')." where id = ".$detail['storeid']);
		$numed = pdo_fetchcolumn("select nums from ".tablename('weilive_coupon')." where weid = ".$weid." and isuse = 0 and flag = 1 and from_user = '".$_W['fans']['from_user']."' and actid = ".$id);
		//该活动领券数量
		$numed = empty($numed)?0:$numed;
		$downtime = $detail['start_time']-time()<=0?0:$detail['start_time']-time();
		include $this->template('activity');
		exit;
	}
	
	if($op=='kill'){
		$follow = pdo_fetch("select uid, follow from ".tablename('mc_mapping_fans')." uniacid = ".$weid." and openid = '".$_W['openid']."'");
		//$follow = fans_search($_W['fans']['from_user'], array('follow'));
		if(empty($follow) || $follow['follow']==0){
			echo -5;
			exit;
		}
		$nums = 1;
		//优惠券ID
		$actid = intval($_GPC['actid']);
		$coupon = array(
			'weid'=>$weid,
			'from_user'=>$_W['openid'],
			'actid'=>$actid,
			'nums'=>1,
			'isuse'=>0,
			'flag'=>1,
			'createtime'=>time()
		);
		$islegal = pdo_fetch("select * from ".tablename('weilive_activity')." where id = ".$actid." and isopen = 1 and ischeck = 1 and weid = ".$weid);
		if(empty($islegal)){
			//该秒杀活动不存在
			echo 0;
			exit;
		} elseif($islegal['start_time']>time()){
			echo -1;
			exit;
		} elseif($islegal['end_time']<=time()){
			echo -2;
			exit;
		} else {
			//已领取数量
			$mynums = pdo_fetchcolumn("select nums from ".tablename('weilive_coupon')." where weid = ".$weid." and isuse = 0 and from_user = '".$_W['fans']['from_user']."' and actid = ".$actid);
			$mynums = empty($mynums)?0:$mynums;
			//将拥有的总数量
			$numsing = $nums + $mynums;
			
			if(!empty($mynums)){
				if($numsing <= $islegal['numed']){
					if($islegal['num'] == -1 || $numsing <= $islegal['num']){
						if($numsing <= $islegal['num']){
							$all = $islegal['num'] - $nums;
						} else {
							$all = -1;
						}
						$coupon['nums'] = $mynums + $nums;
						pdo_update('weilive_activity', array('num'=>$all), array('id'=>$actid));
						pdo_update('weilive_coupon', $coupon, array('weid'=>$weid, 'from_user'=>$_W['fans']['from_user'], 'actid'=>$actid));
						//领取成功
						echo 1;
						exit;
					}
				} elseif ($mynums >= $islegal['numed']){
					//不能再领取了
					echo -3;
					exit;
				} else {
					//领取数量不合法
					echo -4;
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
						$catch = $islegal['catch'] + 1;
						pdo_update('weilive_activity', array('num'=>$all, 'catch'=>$catch), array('id'=>$actid));
						pdo_insert('weilive_coupon', $coupon);
						
						echo 1;
						exit;
					}
				} elseif ($nums > $islegal['numed']){
					echo -4;
					exit;
				} else {
					echo -3;
					exit;
				}
			}
		}
	}
	
	$setting = pdo_fetch("SELECT * FROM " . tablename('weilive_setting') . " WHERE weid = :weid ", array(':weid' => $weid));
	$pindex = max(1, intval($_GPC['page']));
	$psize = empty($setting) ? 10 : intval($setting['pagesize']);
	if($op=='display'){
		$styles = 1;
		$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where end_time > ".time()." and flag = 1 and isopen = 1 and ischeck = 1 and weid = ".$weid." order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("select count(id) from ".tablename('weilive_activity')." where end_time > ".time()." and flag = 1 and isopen = 1 and ischeck = 1 and weid = ".$weid);
	}
	
	if($op=='lowprice'){
		$styles = 3;
		$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where end_time > ".time()." and flag = 1 and isopen = 1 and ischeck = 1 and weid = ".$weid." order by kill_price, createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("select count(id) from ".tablename('weilive_activity')." where end_time > ".time()." and flag = 1 and isopen = 1 and ischeck = 1 and weid = ".$weid);
	}
	
	if($op=='area'){
		$styles = 4;
		$stores = pdo_fetchall("SELECT id FROM " . tablename('weilive_stores') . " WHERE weid = ".$weid." and location_a = '".$_GPC['location_a']."' AND status<>0 AND checked=1");
		$storeids = array();
		foreach($stores as $key=>$s){
			array_push($storeids, $s['id']);
		}
		$storeids = implode(",", $storeids);
		$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where storeid in (".$storeids.") and start_time < ".time()." and end_time > ".time()." and flag = 1 and ischeck = 1 and isopen = 1 and weid = ".$weid." order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("select count(*) from ".tablename('weilive_activity')." where storeid in (".$storeids.") and start_time < ".time()." and end_time > ".time()." and flag = 1 and ischeck = 1 and isopen = 1 and weid = ".$weid);
	}
	
	if($op=='nearby'){
		$styles = 2;
		if(empty($_GPC['lng'])){
			$gps = false;
		} else {
			$gps = true;
			$lng = $_GPC['lng'];
			$lat = $_GPC['lat'];
			$stores = pdo_fetchall("SELECT id, lng, lat FROM " . tablename('weilive_stores') . " WHERE weid = :weid AND status<>0 AND checked=1", array(':weid' => $weid));
			$storeids = array('0'=>0);
			$distance = empty($setting) ? 5 : $setting['distance'];
			foreach($stores as $key=>$s){
				$dis = getDistance($lng, $lat, $s['lng'], $s['lat']);
				if($dis < $distance){
					array_push($storeids, $s['id']);
				}
			}
			$storeids = implode(",", $storeids);
			$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where storeid in (".$storeids.") and start_time < ".time()." and end_time > ".time()." and flag = 1 and ischeck = 1 and isopen = 1 and weid = ".$weid." order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn("select count(*) from ".tablename('weilive_activity')." where storeid in (".$storeids.") and start_time < ".time()." and end_time > ".time()." and flag = 1 and ischeck = 1 and isopen = 1 and weid = ".$weid);
		}
	}
	
	$pager = pagination1($total, $pindex, $psize);
	
	$location_cs = pdo_fetchall("select distinct location_c from ".tablename('weilive_stores')." where weid = ".$weid);
	$data = array();
	$data['关闭'] = 0;
	foreach($location_cs as $key=>$c){
		$data[$c['location_c']] = $key;
	}
	$data = json_encode($data);
	
	
	include $this->template('kill');
?>