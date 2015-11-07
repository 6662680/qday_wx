<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 微信端投诉页面
 */
defined('IN_IA') or exit('Access Denied');

	global $_GPC,$_W;
	$title = '投诉服务';
	$op = !empty($_GPC['op'])?$_GPC['op']:'display';
	//查投诉子类 投诉主类ID=4
	$categories = pdo_fetchall("SELECT * FROM".tablename('xcommunity_servicecategory')."WHERE weid='{$_W['weid']}' AND parentid=4");
	//查小区编号
	$member = $this->changemember();
	if($op == 'post'){
		if (checksubmit('submit')) {
			$data  = array(
				'openid'      => $_W['fans']['from_user'],
				'weid'        => $_W['weid'],
				'regionid'    => $member['regionid'],
				'type'        => 2,
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
				);
			//无线打印
			if($this->module['config']['print_status']){
				if (empty($this->module['config']['print_type']) || $this->module['config']['print_type'] == '2') {
					$data['print_sta'] = -1;
				}
			}
			pdo_insert("xcommunity_report",$data);
			//短信提醒
			// $con = $_GPC['content'];
			// $this->Resms($con);
			message('投诉成功,请查看"我的投诉"等待工作人员联系。',$this->createMobileUrl('report',array('op'=>'display')),'success');
		}
	}elseif ($op == 'display' ||$op=='more') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		//投诉记录查询
		$list = pdo_fetchall("select * from ".tablename("xcommunity_report")."where  weid='{$_W['weid']}' and type=2 LIMIT ".($pindex - 1) * $psize.','.$psize);
		if($op!='more'||!empty($list)){
			$total = pdo_fetchcolumn("select count(*) from".tablename("xcommunity_report")."where weid='{$_W['weid']}' and type=2");
			$pager = pagination($total, $pindex, $psize);
		}
		if($op=='more'){
			include $this->template('report_more');
			exit;
		}
	}elseif ($op == 'cancel') {
		//取消投诉
		$id   = $_GPC['id'];
		if ($id) {
			pdo_update("xcommunity_report",array('status' => 2),array('id'=>$id));
		}
	}elseif ($op == 'my') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		//投诉记录查询
		$list = pdo_fetchall("select * from ".tablename("xcommunity_report")."where openid='{$_W['fans']['from_user']}' and weid='{$_W['weid']}' and type=2 LIMIT ".($pindex - 1) * $psize.','.$psize);
		include $this->template('report_my');
		exit();
	}
	include $this->template('report');