<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 微信端 物业费查询
 */
defined('IN_IA') or exit('Access Denied');

	global $_W,$_GPC;
	$title = '物业费查询';
	$op = !empty($_GPC['op'])?$_GPC['op']:'display';
	$member = $this->changemember();
	$region = pdo_fetch("SELECT * FROM".tablename('xcommunity_region')."WHERE id='{$member['regionid']}'");  
	if ($op == 'display') {
		//查当前公众号下面的费用时间
		$list = pdo_fetchAll("SELECT * FROM".tablename('xcommunity_protime')."WHERE weid='{$_W['weid']}'");
		include $this->template('propertyfree');
	}
	
	if ($_GPC['op'] == 'ajax') {
		$protime = $_GPC['ym'];
		$pro = pdo_fetch("SELECT * FROM".tablename('xcommunity_protime')."WHERE protime=:protime AND weid='{$_W['weid']}'",array(':protime' => $protime));

		$profrees = pdo_fetchAll("SELECT * FROM".tablename('xcommunity_propertyfree')."WHERE protimeid='{$pro['id']}' AND mobile='{$member['mobile']}'");
		
		$result = array(
				'data'   => $profrees,
				'info'   => "成功",
				'status' => 1,
			);
		print_r(json_encode($result));
	}