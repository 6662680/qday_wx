<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区家政信息
 */
defined('IN_IA') or exit('Access Denied');
	global $_GPC,$_W;
	$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$regionid  = $_GPC['regionid'];
	$id        = intval($_GPC['id']);
	//查家政子类 家政主类ID=1
		$categories = pdo_fetchall("SELECT * FROM".tablename('xcommunity_servicecategory')."WHERE weid='{$_W['weid']}' AND parentid=1");
	if ($op == 'display') {
		//搜索BUG
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 10;
		$list   = pdo_fetchall("SELECT * FROM".tablename('xcommunity_service')." WHERE weid='{$_W['weid']}' AND servicecategory=1 AND regionid='{$regionid}' LIMIT ".($pindex - 1) * $psize.','.$psize);
		foreach ($list as $key => $value) {
			$member                 = pdo_fetch("SELECT * FROM".tablename('xcommunity_member')."WHERE openid = '{$value['openid']}'");
			$list[$key]['realname'] = $member['realname'];
			$list[$key]['mobile']   = $member['mobile'];
		}
		$total  = pdo_fetchcolumn("SELECT COUNT(*) FROM".tablename('xcommunity_service')."WHERE weid='{$_W['weid']}' AND servicecategory=1 AND regionid='{$regionid}'");
		$pager  = pagination($total, $pindex, $psize);
	}elseif($op == 'post'){
		//编辑
		$item       = pdo_fetch("SELECT * FROM".tablename('xcommunity_service')."WHERE id=:id",array(':id' => $id));
		$member     = pdo_fetch("SELECT * FROM".tablename('xcommunity_member')."WHERE openid = '{$item['openid']}'");
		if(checksubmit('submit')){
			$data = array(
			'status'               => $_GPC['status'],
			);
			pdo_update("xcommunity_service",$data,array('id' => $id));
			message('修改成功',$this->createWebUrl('homemaking',array('op'=>'display','regionid'=>$regionid)),'success');
		}
	}elseif ($op == 'delete') {
		//删除
		pdo_delete("xcommunity_service",array('id' => $id));
		message('家政服务信息删除成功。',referer(),'success');
	}
	include $this->template('homemaking');