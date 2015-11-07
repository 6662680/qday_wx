<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区常用查询
 */
defined('IN_IA') or exit('Access Denied');
	global $_W,$_GPC;
	$op = !empty($_GPC['op'])?$_GPC['op']:'display';
	if ($op == 'post') {
			if($_GPC['id']){
				$item = pdo_fetch("SELECT * FROM".tablename('xcommunity_search')."WHERE id=:id",array(':id' => $_GPC['id']));
			}
			$data = array(
				'weid'  => $_W['weid'],
				'sname' => $_GPC['sname'],
				'surl'  => $_GPC['surl'],
				'icon'  => $_GPC['icon'],
				'status' => $_GPC['status'],
			);
			if($_W['ispost']){
				if (empty($_GPC['id'])) {
					pdo_insert("xcommunity_search",$data);
				}else {
					pdo_update("xcommunity_search",$data,array('id' => $_GPC['id']));
				}
				message('更新成功',referer(),'success');
			}
	}elseif($op == 'display'){
		$list = pdo_fetchAll('SELECT * FROM'.tablename('xcommunity_search')."WHERE weid='{$_W['weid']}'");
		if ($_GPC['id']) {
			if (empty($_GPC['status'])) {
				pdo_query("UPDATE ".tablename('xcommunity_search')." SET status=0 WHERE id=:id",array(':id' => $_GPC['id']));
				message('成功关闭',referer(),'success');
			}else{
				pdo_query("UPDATE ".tablename('xcommunity_search')." SET status=1 WHERE id=:id",array(':id' => $_GPC['id']));
				message('成功开启',referer(),'success');
			}

		}
		
	}elseif($op == 'delete'){
		pdo_delete("xcommunity_search",array('id' => $_GPC['id']));
		message('删除成功',referer(),'success');
	}
	include $this->template('search');
