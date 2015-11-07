<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区租赁信息
 */
defined('IN_IA') or exit('Access Denied');
	global $_GPC,$_W;
	$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$regionid  = intval($_GPC['regionid']);
	$id        = intval($_GPC['id']);
	//查租赁子类 租赁主类ID=1
		$categories = pdo_fetchall("SELECT * FROM".tablename('xcommunity_servicecategory')."WHERE weid='{$_W['weid']}' AND parentid=2");
	if ($op == 'display') {
		//搜索
		
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 10;
		$sql    = "select * from ".tablename("xcommunity_service")."where weid = '{$_W['weid']}' and servicecategory = 2 and regionid='{$regionid}' LIMIT ".($pindex - 1) * $psize.','.$psize;
		$list   = pdo_fetchall($sql);
		foreach ($list as $key => $value) {
			$member                 = pdo_fetch("SELECT * FROM".tablename('xcommunity_member')."WHERE openid = '{$value['openid']}'");
			$list[$key]['realname'] = $member['realname'];
			$list[$key]['mobile']   = $member['mobile'];
		}
		$total  = pdo_fetchcolumn('select count(*) from'.tablename("xcommunity_service")."where weid = '{$_W['weid']}' and servicecategory = 2 and regionid=".$regionid);
		$pager  = pagination($total, $pindex, $psize);
		
		
	}elseif($op == 'post'){
		//编辑
		if ($id) {
			$item       = pdo_fetch("SELECT * FROM".tablename('xcommunity_service')."WHERE id=:id",array(':id' => $id));
			$member     = pdo_fetch("SELECT * FROM".tablename('xcommunity_member')."WHERE openid = '{$item['openid']}'");
			$images = unserialize($item['images']);
			$picid  = implode(',', $images);
			$imgs   = pdo_fetchall("SELECT * FROM".tablename('xfcommunity_images')."WHERE id in({$picid})");
		}
		if(checksubmit('submit')){
			$data = array(
			'status'               => $_GPC['status'],
			'servicesmallcategory' => $_GPC['servicesmallcategory'],
			'contacttype'          => $_GPC['contacttype'],
			'requirement'          => $_GPC['requirement'],
			'remark'               => $_GPC['remark'],
			);
			pdo_update("xcommunity_service",$data,array('id' => $id,'weid' => $_W['weid']));
			message('修改成功',$this->createWebUrl('houselease',array('op'=>'display','regionid'=>$regionid)),'success');
		}
	}elseif ($op == 'delete') {
		//删除
		pdo_delete("xcommunity_service",array('id' => $id));
		message('房屋租赁信息删除成功。',referer(),'success');
	}
	include $this->template('houselease');