<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台物业团队
 */
defined('IN_IA') or exit('Access Denied');
	global $_GPC,$_W;
	$id  = intval($_GPC['id']);
	//显示当前物业信息
	$item = pdo_fetch("SELECT * FROM".tablename("xcommunity_property")."WHERE weid='{$_W['weid']}'");
	$data = array(
			'weid'       => $_W['weid'],
			'title'      => $_GPC['title'],
			'content'    => htmlspecialchars_decode($_GPC['content']),
			'createtime' => $_W['timestamp'],
			'topPicture' => $_GPC['topPicture'],
		);
	
	if ($_W['ispost']) {
		if (empty($id)) {
			pdo_insert("xcommunity_property",$data);
			message('添加成功',$this->createWebUrl('property',array('op' => 'display')),'success');
		}else{
			pdo_update("xcommunity_property",$data,array('id' => $id));
			message('修改成功',referer(),'success');
		}
	}
	include $this->template('property');