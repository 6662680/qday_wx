<?php
	
	$ismobile = 'weilivemobile'.$weid;
	$ispwd = 'weilivepwd'.$weid;
	
	$setting = pdo_fetch("SELECT * FROM " . tablename('weilive_setting') . " WHERE weid = :weid ", array(':weid' => $weid));
	$pindex = max(1, intval($_GPC['page']));
	$psize = empty($setting) ? 10 : intval($setting['pagesize']);
	if($op=='display'){
		$isreg = 0;
		if(empty($_COOKIE[$ismobile]) && empty($_COOKIE[$ispwd])){
			
		} else {
			$host = pdo_fetchall("select id, mobile, pwd from ".tablename('weilive_shophost')." where weid = ".$weid);
			foreach($host as $h){
				if($h['mobile']==$_COOKIE[$ismobile] && $h['pwd']==$_COOKIE[$ispwd]){
					$isreg = 1;
					$shops = pdo_fetchall("select * from ".tablename('weilive_stores')." where weid = ".$weid." and hostid = ".$h['id']);
					$activities = pdo_fetchall("select storeid, count(num) as num, flag from ".tablename('weilive_activity')." where weid = ".$weid." group by storeid, flag");
					$num = array();
					foreach($activities as $act){
						if($act['flag']==0){
							$num[$act['storeid']][0] = $act['num'];
						} else {
							$num[$act['storeid']][1] = $act['num'];
						}
					}
					$host = $h;
					break;
				}
			}
		}
		if($isreg == 0){
			include $this->template('login');
			exit;		
		}
	}
	
	if($op=='reg'){
		$isreg = 0;
		$mobile = pdo_fetchall("select from_user, mobile from ".tablename('weilive_shophost')." where weid = ".$weid);
		foreach($mobile as $m){
			if($m['mobile']==$_GPC['mobile'] && $m['from_user']==$_W['openid']){
				$isreg = 1;
				break;
			}
		}
		if($isreg == 1){
			message('该用户名已存在，请重新注册！');
		}
		$follow = pdo_fetch("select uid from ".tablename('mc_mapping_fans')." where uniacid = ".$weid." and openid = '".$_W['openid']."'");
		$info = mc_fetch($follow['uid'], array('realname'));
		$host = array(
			'weid'=>$weid,
			'from_user'=>$_W['openid'],
			'realname'=>$info['realname'],
			'mobile'=>trim($_GPC['mobile']),
			'pwd'=>$_GPC['pwd'],
			'createtime'=>time()
		);
		if($isreg == 1){
			message('请勿重复注册！');
		} else {
			pdo_insert('weilive_shophost', $host);
			message('注册成功！', $this->createMobileUrl('myshop'), 'success');
		}
	}
	
	if($op=='login'){
		$isreg = 0;
		$host = pdo_fetchall("select id, mobile, pwd from ".tablename('weilive_shophost')." where weid = ".$weid);
		foreach($host as $h){
			if($h['mobile']==trim($_GPC['mobile']) && $h['pwd']==$_GPC['pwd']){
				$isreg = 1;
				setcookie($ismobile, $_GPC['mobile'], time()+3600*24);
				setcookie($ispwd, $_GPC['pwd'], time()+3600*24);
				message('登录成功！', $this->createMobileUrl('myshop'), 'success');
				
			}
		}
		if($isreg == 0){
			message('用户名或密码错误！');
		}
	}
	
	if($op=='exit'){
		setcookie($ismobile, '', time()+3600*240);
		setcookie($ispwd, '', time()+3600*240);
		$url = $this->createMobileUrl('myshop');
		header("location:$url");
	}
	
	//登录状态才可往下执行
	if(empty($_COOKIE[$ismobile]) && empty($_COOKIE[$ispwd])){
		include $this->template('login');
		exit;
	}
	
	if($op=='couponlist'){

		$storeid = $_GPC['storeid'];
		$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where weid = ".$weid." and flag = 0 and storeid = ".$storeid);
		include $this->template('couponlist');
		exit;
	}
	
	if($op=='couponpost'){

		$storeid = $_GPC['storeid'];
		if($_GPC['opp']=='detail'){
			$id = $_GPC['id'];
			$active = pdo_fetch("select * from ".tablename('weilive_activity')." where weid = ".$weid." and flag = 0 and id = ".$_GPC['id']);
		}
		if(checksubmit('submit')){
			$title = trim($_GPC['title'])?trim($_GPC['title']):message('活动名称不能为空！');
			$price = is_numeric($_GPC['price'])?$_GPC['price']:message('请输入合法原价！');
			$kill_price = is_numeric($_GPC['kill_price'])?$_GPC['kill_price']:message('请输入合法秒杀价！');
			$num = is_numeric($_GPC['num'])?$_GPC['num']:-1;
			$numed = is_numeric($_GPC['numed'])?$_GPC['numed']:1;
			$activity_detail = trim($_GPC['activity_detail'])?trim($_GPC['activity_detail']):message('优惠详情不能为空！');
			$description = trim($_GPC['description'])?trim($_GPC['description']):message('使用说明不能为空！');
			
			$activity = array(
				'weid'=>$weid,
				'title'=>$title,
				'storeid'=>$storeid,
				'logo'=>$_GPC['logo'],
				'price'=>$price,
				'kill_price'=>$kill_price,
				'num'=>$num,
				'numed'=>$numed,
				'description'=>$description,
				'activity_detail'=>$activity_detail,
				'start_time'=>strtotime($_GPC['start_time']),
				'end_time'=>strtotime($_GPC['end_time']),
				'type'=>$_GPC['type'],
				'isopen'=>1,
				'ischeck'=>1,
				'flag'=>0,
				'createtime'=>time()
			);
			if(!empty($_GPC['logo'])){
				require_once  dirname(__FILE__).'/../phpthumb/ThumbLib.inc.php'; 
				try { 
					$thumb = PhpThumbFactory::create($_W['attachurl'].$_GPC['logo']);
				} catch (Exception $e) { 
					// handle error here however you'd like 
				} 
				$name = time();
				$realpath = substr($_GPC['logo'], 0, strrpos($_GPC['logo'], '/')+1);
				$thumb->adaptiveResize(120, 72);
				$thumb->save("../attachment/$realpath"."thumb".$name.".jpg");
				$activity['thumb'] = $realpath."thumb".$name.".jpg";
			}

			if(intval($_GPC[id])){
				unset($activity['createtime']);
				$temp = pdo_update('weilive_activity', $activity, array('id'=>$_GPC['id']));
			} else {
				$temp = pdo_insert('weilive_activity', $activity);
			}
			if($temp){
				$url = $this->createMobileUrl('myshop', array('op'=>'couponlist', 'storeid'=>$storeid));
				header("Location: $url");
				//message('提交活动成功！', $this->createMobileUrl('myshop', array('op'=>'couponlist', 'storeid'=>$storeid)), 'success');
			} else {
				message('提交活动失败，请重新提交！');
			}
		}
		include $this->template('couponpost');
		exit;	
	}
	
	
	if($op=='activitylist'){

		$storeid = $_GPC['storeid'];
		$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where weid = ".$_W['weid']." and flag = 1 and storeid = ".$storeid);
		include $this->template('activitylist');
		exit;
	}
	
	if($op=='activitypost'){

		$storeid = $_GPC['storeid'];
		if(checksubmit('submit')){
			$title = trim($_GPC['title'])?trim($_GPC['title']):message('活动名称不能为空！');
			$price = is_numeric($_GPC['price'])?$_GPC['price']:message('请输入合法原价！');
			$kill_price = is_numeric($_GPC['kill_price'])?$_GPC['kill_price']:message('请输入合法秒杀价！');
			$num = is_numeric($_GPC['num'])?$_GPC['num']:-1;
			$activity_detail = trim($_GPC['activity_detail'])?trim($_GPC['activity_detail']):message('优惠详情不能为空！');
		
			$activity = array(
				'weid'=>$weid,
				'title'=>$title,
				'logo'=>$_GPC['logo'],
				'storeid'=>$storeid,
				'price'=>$price,
				'kill_price'=>$kill_price,
				'num'=>$num,
				'activity_detail'=>$activity_detail,
				'start_time'=>strtotime($_GPC['start_time']),
				'end_time'=>strtotime($_GPC['end_time']),
				'cost_time'=>strtotime($_GPC['cost_time']),
				'isopen'=>1,
				'ischeck'=>0,
				'flag'=>1,
				'createtime'=>time()
			);
			if(!empty($_GPC['logo'])){
				require_once  dirname(__FILE__).'/../phpthumb/ThumbLib.inc.php'; 
				try { 
					$thumb = PhpThumbFactory::create($_W['attachurl'].$_GPC['logo']);
				} catch (Exception $e) { 
					// handle error here however you'd like 
				} 
				$name = time();
				$realpath = substr($_GPC['logo'], 0, strrpos($_GPC['logo'], '/')+1);
				$thumb->adaptiveResize(120, 72);
				$thumb->save("../attachment/$realpath"."thumb".$name.".jpg");
				$activity['thumb'] = $realpath."thumb".$name.".jpg";
			}
			if(intval($_GPC[id])){
				unset($activity['createtime']);
				$temp = pdo_update('weilive_activity', $activity, array('id'=>$_GPC['id']));
			} else {
				$temp = pdo_insert('weilive_activity', $activity);
			}
			if($temp){
				$url = $this->createMobileUrl('myshop', array('op'=>'activitylist', 'storeid'=>$storeid));
				header("Location: $url");
				//message('提交活动成功！', $this->createMobileUrl('myshop', array('op'=>'activitylist', 'storeid'=>$storeid)), 'success');
			} else {
				message('提交活动失败，请重新提交！', $this->createMobileUrl('myshop', array('op'=>'activitypost', 'storeid'=>$storeid)), 'error');
			}
		}
		include $this->template('activitypost');
		exit;
	}
	
	if($op=='memberlist'){

		$storeid = $_GPC['storeid'];
		$members = pdo_fetchall("select distinct from_user, realname, mobile from ".tablename('weilive_credit')." where weid = ".$weid." and storeid = ".$_GPC['storeid']." LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$m = pdo_fetchall("select distinct from_user, realname, mobile from ".tablename('weilive_credit')." where weid = ".$weid." and storeid = ".$_GPC['storeid']);
		$total = sizeof($m);
		$pager = pagination1($total, $pindex, $psize);
		include $this->template('memberlist');
		exit;		
	}
	
	if($op=='build'){

		$hostid = $_GPC['hostid'];
		$location_cs = pdo_fetchall("select distinct location_c from ".tablename('weilive_stores')." where weid = ".$weid);
		$categorys = pdo_fetchall("select id, name from ".tablename('weilive_category')." where enabled = 1 and parentid = 0 and weid = ".$weid);
        $category = pdo_fetchall("SELECT id, parentid, name FROM ".tablename('weilive_category')." WHERE weid = ".$weid." ORDER BY parentid ASC, displayorder DESC");
		if (!empty($category)) {
            $child = array();
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $child[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        }
		include $this->template('buildshop');
	}
	
	if(checksubmit('submit')){
		$data = array(
			'hostid'=>$_GPC['hostid'],
			'weid' => intval($weid),
			'logo'=>$_GPC['logo'],
			'permit'=>$_GPC['permit'],
			'pwd'=>$_GPC['pwd'],
			'title' => trim($_GPC['title']),
			'description' => trim($_GPC['description']),
			'pcate' => intval($_GPC['pcate']),
			'ccate' => intval($_GPC['ccate']),
			'business_time' => $_GPC['business_time'],
			'level' => 3,
			'tel' => trim($_GPC['tel']),
			'location_p' => trim($_GPC['location_p']),
			'location_c' => trim($_GPC['location_c']),
			'location_a' => trim($_GPC['location_a']),
			'place' => trim($_GPC['place']),
			'lng' => trim($_GPC['lng']),
			'lat' => trim($_GPC['lat']),
			'status' => intval($_GPC['status']),
			'isfirst' => intval($_GPC['isfirst']),
			'checked' => 0,
			'dateline' => TIMESTAMP
		);
		pdo_insert('weilive_stores', $data);
		$url = $this->createMobileUrl('myshop');
		header("Location: $url");
		//message('数据更新成功！', $this->createMobileUrl('myshop'), 'success');
	}
	include $this->template('myshop');
?>