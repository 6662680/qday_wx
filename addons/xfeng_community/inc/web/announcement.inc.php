<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区公告信息
 */
defined('IN_IA') or exit('Access Denied');
	global $_GPC,$_W;
	$regionid = $_GPC['regionid'];
	$op = !empty($_GPC['op'])?$_GPC['op']:'display';
	$id = intval($_GPC['id']);
	if($op == 'display'){
		//公告搜索
		$condition = '';
		if (!empty($_GPC['title'])) {
			$condition .= " AND title LIKE '%{$_GPC['title']}%'";
		}
		//管理公告
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 10;
		$sql    = "select * from ".tablename("xcommunity_announcement")."where regionid='{$regionid}' $condition and weid = {$_W['weid']} LIMIT ".($pindex - 1) * $psize.','.$psize;
		
		$list   = pdo_fetchall($sql);
		$total  = pdo_fetchcolumn('select count(*) from'.tablename("xcommunity_announcement")."where  regionid=:regionid  $condition and weid = {$_W['weid']}",array(':regionid' => $regionid));
		$pager  = pagination($total, $pindex, $psize);
	}
	if($op == 'post'){
		if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM".tablename('xcommunity_announcement')."WHERE id=:id",array(':id' =>$id));
			$starttime = !empty($item['starttime']) ? date('Y-m-d',$item['starttime']) : date('Y-m-d',timestamp);
			$endtime = !empty($item['endtime']) ? date('Y-m-d',$item['endtime']) : date('Y-m-d',timestamp);
		}
		//添加公告
		if(checksubmit('submit')){
			$starttime = strtotime($_GPC['birth']['start']);
			$endtime   = strtotime($_GPC['birth']['end']);
			if (!empty($starttime) && $starttime==$endtime) {
				$endtime = $endtime+86400-1;
			}
			$insert = array(
					'weid'       => $_W['weid'],
					'regionid'   =>$regionid,
					'title'      =>$_GPC['title'],
					'content'    =>htmlspecialchars_decode($_GPC['content']),
					'createtime' =>$_W['timestamp'],
					'starttime'  =>$starttime,
					'endtime'    =>$endtime,
					'status'     =>$_GPC['status'],
					'author'     =>$_W['account']['name'],
				);
			if(empty($id)){
				pdo_insert("xcommunity_announcement",$insert);
			}else{
				pdo_update("xcommunity_announcement",$insert,array('id'=>$id));
			}
			message('更新信息成功',referer(), 'success');
		}
	}
	if($op == 'delete'){
		//删除公告
		pdo_delete("xcommunity_announcement",array('id'=>$id));
		message('删除成功',referer(), 'success');
	}
	include $this->template('announcement');