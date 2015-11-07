<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 微信端报修
 */
defined('IN_IA') or exit('Access Denied');

	global $_GPC,$_W;
	$title = '报修服务';
	$op = !empty($_GPC['op'])?$_GPC['op']:'display';
	$categories = array(
			'1'=>'水暖',
			'2'=>'公共设施',
			'3'=>'电器设施',
			);
	//查小区编号
	$member = $this->changemember();
	//查报修子类 报修主类ID=3
	$categories = pdo_fetchall("SELECT * FROM".tablename('xcommunity_servicecategory')."WHERE weid='{$_W['weid']}' AND parentid=3");
	if($op == 'post'){
		if ($_W['ispost']) {
			$data  = array(
				'openid'      => $_W['fans']['from_user'],
				'weid'        => $_W['weid'],
				'regionid'    => $member['regionid'],
				'type'        => 1,
				'category'    => $_GPC['category'],
				'content'     => $_GPC['content'],
				'createtime'  => $_W['timestamp'],
				'status'      => 0,
				'rank'        => 0,
				'comment'     => 0,
				'requirement' => '无',
				'resolve'     => '',
				'resolver'    => '',
				'resolvetime' => '',
				'images' => serialize($_GPC['picIds']),
			);
			//无线打印
			if($this->module['config']['print_status']){
				if (empty($this->module['config']['print_type']) || $this->module['config']['print_type'] == '2') {
					$data['print_sta'] = -1;
				}
			}	
			pdo_insert("xcommunity_report",$data);
			$id = pdo_insertid();
			//短信提醒
			// $con = $_GPC['content'];
			// $this->Resms($con);
			message('报修申请提交成功,请查看"我的报修"等待工作人员联系。',$this->createMobileUrl('repair',array('op'=>'display')),'success');
		}
	}elseif ($op == 'display'||$op=='more') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		//通过Id查出回复记录，在一起组装一个新的二维数组
		$list    = pdo_fetchall("select * from ".tablename("xcommunity_report")."where weid='{$_W['weid']}' and type=1 LIMIT ".($pindex - 1) * $psize.','.$psize);
		if($op!='more'||!empty($list)){
			$total = pdo_fetchcolumn('select count(*) from'.tablename("xcommunity_report")."where  weid='{$_W['weid']}' and type=1 ");
			$pager = pagination($total, $pindex, $psize);
		}
		foreach ($list as $key => $value) {
			$list[$key]['reply'] = pdo_fetchall('select * from'.tablename("xcommunity_reply")."where weid=:weid AND reportid=:reportid",array(':weid'=>$_W['weid'],':reportid'=>$value['id']));
			$images = unserialize($value['images']);
			if ($images) {
				$picid = implode(',', $images);
				$imgs = pdo_fetchall("SELECT * FROM".tablename('xfcommunity_images')."WHERE id in({$picid})");
				$list[$key]['img'] = $imgs;
			}

		}
		if($op=='more'){
			include $this->template('repair_more');
			exit;
		}
	}elseif ($op == 'resolve') {
		//业主完成报修申请
		$id   = intval($_GPC['id']);
		$item = pdo_fetch("select * from".tablename("xcommunity_report")."where id=:id AND weid=:weid",array(':weid'=>$_W['weid'],':id'=>$id));
		$update = array(
			'status'  => 1,
			'rank'    => $_GPC['rank'],
			'comment' => $_GPC['comment'],
			);
		if($_W['ispost']){
			pdo_update("xcommunity_report",$update,array('id' => $id));
			message('谢谢评价',$this->createMobileUrl('repair',array('op' => 'display')));
	 	}
	}elseif ($op == 'cancel') {
		//取消报修申请
		$id = intval($_GPC['id']);
		pdo_update("xcommunity_report",array('status' => 2),array('id'=>$id));
		message('已取消');
	}elseif ($op == 'my') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		//通过Id查出回复记录，在一起组装一个新的二维数组
		$list    = pdo_fetchall("select * from ".tablename("xcommunity_report")."where weid='{$_W['weid']}' and openid='{$_W['fans']['from_user']}' and type=1 LIMIT ".($pindex - 1) * $psize.','.$psize);
		if($op!='more'||!empty($list)){
			$total = pdo_fetchcolumn('select count(*) from'.tablename("xcommunity_report")."where  weid='{$_W['weid']}' and type=1 ");
			$pager = pagination($total, $pindex, $psize);
		}
		foreach ($list as $key => $value) {
			$list[$key]['reply'] = pdo_fetchall('select * from'.tablename("xcommunity_reply")."where weid=:weid AND reportid=:reportid",array(':weid'=>$_W['weid'],':reportid'=>$value['id']));
			$images = unserialize($value['images']);
			if ($images) {
				$picid = implode(',', $images);
				$imgs = pdo_fetchall("SELECT * FROM".tablename('xfcommunity_images')."WHERE id in({$picid})");
				$list[$key]['img'] = $imgs;
			}

		}

		include $this->template('repair_my');exit();

	}	
	include $this->template('repair');

