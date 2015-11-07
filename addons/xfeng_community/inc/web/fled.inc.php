<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区二手交易
 */
defined('IN_IA') or exit('Access Denied');
	
	global $_W,$_GPC;
	$op = !empty($_GPC['op']) ? $_GPC['op'] :'display';
	if ($op == 'display') {
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 10;
		$list = pdo_fetchall("SELECT * FROM".tablename('xcommunity_fled')."WHERE  weid='{$_W['weid']}' AND status =0 LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM".tablename('xcommunity_fled')."WHERE weid='{$_W['weid']}'");
		$pager  = pagination($total, $pindex, $psize);
		include $this->template('fled');
	}elseif ($op == 'delete') {
		if($_GPC['id']){
			pdo_delete('xcommunity_fled',array('id'=>$_GPC['id']));
			echo json_encode(array('state' => 1));
		}
	}elseif ($op == 'detail') {
		$item = pdo_fetch("SELECT * FROM".tablename("xcommunity_fled")."WHERE id=:id",array(':id' => $_GPC['id']));
		if (!$item) {
			message('该商品不存在');
		}
		$images = unserialize($item['images']);
		if ($images) {
			$picid  = implode(',', $images);
		    $imgs   = pdo_fetchall("SELECT * FROM".tablename('xfcommunity_images')."WHERE id in({$picid})");
		}
		include $this->template('fled_detail');
	}elseif ($op == 'toblack') {
		if ($_GPC['id']) {
			pdo_query("UPDATE ".tablename('xcommunity_fled')."SET status =1 WHERE id=:id",array(':id' => $_GPC['id']));
			echo json_encode(array('state' => 1));
		}
	}


	