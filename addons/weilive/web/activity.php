<?php
	//店家ID
	$storeid = $_GPC['storeid'];
	if($op=='display'){
		$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where weid = ".$weid." and storeid = ".$storeid." order by displayorder desc");
	
	}
	
	if($_GPC['id']==''){
		$activity = array(
			'displayorder'=>0,
			'isopen'=>1,
			'ischeck'=>1,
			'type'=>1
		);
	} else {
		$activity = pdo_fetch('select * from '.tablename('weilive_activity')." where id = ".$_GPC['id']);
	}
	
	if(checksubmit('submit') && $_GPC['op']=='post'){
		$title = trim($_GPC['title'])?trim($_GPC['title']):message('活动名称不能为空！');
		//$logo = trim($_GPC['logo'])?trim($_GPC['logo']):message('请上传活动Logo！');
		$price = is_numeric($_GPC['price'])?$_GPC['price']:message('请输入合法原价！');
		$kill_price = is_numeric($_GPC['kill_price'])?$_GPC['kill_price']:message('请输入合法秒杀价！');
		$prompt = trim($_GPC['prompt'])?trim($_GPC['prompt']):message('温馨提示不能为空！');
		$activity_detail = trim($_GPC['activity_detail'])?trim($_GPC['activity_detail']):message('优惠详情不能为空！');
		$description = trim($_GPC['description'])?trim($_GPC['description']):message('使用说明不能为空！');
		$num = is_numeric($_GPC['num'])?$_GPC['num']:-1;
		$numed = is_numeric($_GPC['numed'])?$_GPC['numed']:1;
		$score = is_numeric($_GPC['score'])?$_GPC['score']:0;
		$credit = is_numeric($_GPC['credit'])?$_GPC['credit']:0;
		$catch = is_numeric($_GPC['score'])?$_GPC['catch']:0;
		$used = is_numeric($_GPC['used'])?$_GPC['used']:0;
	
		$activity = array(
			'weid'=>$weid,
			'title'=>$title,
			'logo'=>$_GPC['logo'],
			'storeid'=>$storeid,
			'price'=>$price,
			'kill_price'=>$kill_price,
			'num'=>$num,
			'numed'=>$numed,
			'score'=>$score,
			'credit'=>$credit,
			'catch'=>$catch,
			'used'=>$used,
			'prompt'=>$prompt,
			'description'=>$description,
			'activity_detail'=>$activity_detail,
			'start_time'=>strtotime($_GPC['start_time']),
			'end_time'=>strtotime($_GPC['end_time']),
			'isopen'=>$_GPC['isopen'],
			'ischeck'=>$_GPC['ischeck'],
			'type'=>$_GPC['type'],
			'createtime'=>time()
		);
		if (!empty($_GPC['logo'])) {
			require_once dirname(__FILE__).'/../phpthumb/ThumbLib.inc.php';
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
			message('提交活动成功！', $this->createWebUrl('activity', array('op'=>'display', 'storeid'=>$storeid)), 'success');
		} else {
			message('提交活动失败，请重新提交！', '', 'error');
		}
	}
	if(checksubmit('submit') && $_GPC['op']=='posts'){
		foreach ($_GPC['displayorder'] as $id => $displayorder) {
			pdo_update('weilive_activity', array('displayorder' => $displayorder), array('id' => $id));
		}
		message('排序更新成功！', $this->createWebUrl('activity', array('op' => 'display', 'storeid'=>$storeid)), 'success');
	}
	if($op=='delete'){
		$temp = pdo_delete('weilive_activity', array('id'=>$_GPC['id'], 'storeid'=>$storeid));
		if($temp){
			message('删除活动成功！', $this->createWebUrl('activity', array('op'=>'display', 'storeid'=>$storeid)), 'success');
		} else {
			message('删除活动失败，请重新删除！','', 'error');
		}
	}
	
	include $this->template('web/activity');
?>