<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区商家
 */
defined('IN_IA') or exit('Access Denied');

	global $_GPC,$_W;
	$list = pdo_fetchall("SELECT * FROM".tablename('xcommunity_business')."WHERE weid='{$_W['weid']}'");
	if ($_GPC['op'] == 'change') {
		if(pdo_query("UPDATE ".tablename('xcommunity_business')."SET status='{$_GPC['status']}' WHERE id=:id",array(':id' =>$_GPC['id']))){
			message('操作成功',referer(),'success');
		}	
	}
	include $this->template('business');