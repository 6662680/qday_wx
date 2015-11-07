<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台黑名单管理
 */
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
$type = !empty($_GPC['type']) ? $_GPC['type'] : 2;
if ($type == 2) {
	$table = 'xcommunity_fled';
}else{
	$table = 'xcommunity_carpool';
}
$pindex = max(1, intval($_GPC['page']));
$psize  = 10;
$list = pdo_fetchAll("SELECT * FROM".tablename($table)."WHERE weid='{$_W['weid']}' AND status = 1 LIMIT ".($pindex - 1) * $psize.','.$psize);

$total =pdo_fetchcolumn("SELECT COUNT(*) FROM".tablename($table)."WHERE weid='{$_W['weid']}' AND status = 1");
$pager  = pagination($total, $pindex, $psize);
if ($_GPC['op'] == 'delete') {
	if($_GPC['id']){
		pdo_delete($table,array('id'=>$_GPC['id']));
		echo json_encode(array('state' => 1));
	}
}elseif ($_GPC['op'] == 'toblack') {
	if ($_GPC['id']) {
		pdo_query("UPDATE ".tablename($table)."SET status =0 WHERE id=:id",array(':id' => $_GPC['id']));
		echo json_encode(array('state' => 1));
	}
}
include $this->template('black');