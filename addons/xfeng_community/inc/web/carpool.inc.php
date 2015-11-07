<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区拼车
 */
defined('IN_IA') or exit('Access Denied');

	global $_GPC,$_W;
	$op = !empty($_GPC['op']) ? $_GPC['op'] :'display';
	if ($op == 'display') {
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 10;
		$list = pdo_fetchall("SELECT * FROM".tablename('xcommunity_carpool')."WHERE  weid='{$_W['weid']}' AND status = 0 LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM".tablename('xcommunity_carpool')."WHERE weid='{$_W['weid']}'");
		$pager  = pagination($total, $pindex, $psize);
		include $this->template('carpool');
	}elseif ($op == 'delete') {
		if($_GPC['id']){
			pdo_delete('xcommunity_carpool',array('id'=>$_GPC['id']));
			echo json_encode(array('state' => 1));
		}
	}elseif ($op == 'toblack') {
		if ($_GPC['id']) {
			pdo_query("UPDATE ".tablename('xcommunity_carpool')."SET status =1 WHERE id=:id",array(':id' => $_GPC['id']));
			echo json_encode(array('state' => 1));
		}
	}


