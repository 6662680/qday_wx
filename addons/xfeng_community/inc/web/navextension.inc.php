<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台导航管理
 */
defined('IN_IA') or exit('Access Denied');
	global $_W,$_GPC;
	$op = !empty($_GPC['op'])?$_GPC['op']:'display';
	$id = intval($_GPC['id']);
	if($op == 'post'){
		if (!empty($id)) {
			$sql  = "select * from".tablename("xcommunity_navExtension")."where id=:id,array(':id' => $id)";
			$item = pdo_fetch($sql);
		}
		$data = array(
			'weid'    => $_W['weid'],
			'title'   => $_GPC['title'],
			'navurl'  => $_GPC['navurl'],
			'icon'    => $_GPC['icon'],
			'content' => $_GPC['content'],
			'cate'	  => $_GPC['cate'],
			'bgcolor' => $_GPC['bgcolor'],
		);
		if ($_W['ispost']) {
			if (empty($id)) {
				pdo_insert("xcommunity_navExtension",$data);
				message('添加成功',referer(),'success');
			}else{
				pdo_update("xcommunity_navExtension",$data,array('id' => $id));
				message('更新成功',referer(),'success');
			}
		}
	}elseif($op == 'display'){
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 10;
		$sql    = "select * from".tablename("xcommunity_navExtension")."where weid='{$_W['weid']}' LIMIT ".($pindex - 1) * $psize.','.$psize;
		$list   = pdo_fetchall($sql);
		$total  = pdo_fetchcolumn('select count(*) from'.tablename("xcommunity_navExtension")."where weid='{$_W['weid']}'");
		$pager  = pagination($total, $pindex, $psize);
	}elseif ($op == 'delete') {
		pdo_delete("xcommunity_navExtension",array('id' => $id));
		message('删除成功',referer(),'success');
	}
	include $this->template('navExtension');