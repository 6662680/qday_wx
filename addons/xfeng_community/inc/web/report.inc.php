<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区投诉信息
 */
defined('IN_IA') or exit('Access Denied');
	global $_W,$_GPC;
	$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$regionid  = $_GPC['regionid'];
	$id        = $_GPC['id'];
	//查投诉子类 投诉主类ID=4
	$categories = pdo_fetchall("SELECT * FROM".tablename('xcommunity_servicecategory')."WHERE weid='{$_W['weid']}' AND parentid=4");
	if($op == 'display'){
		//搜索 type 1为报修，2为投诉
		$category  = $_GPC['category'];
		$type      = 2;
		$regionid  = $_GPC['regionid'];
		$starttime = strtotime($_GPC['birth-start']);
		$endtime   = strtotime($_GPC['birth-end']);
		if (!empty($starttime) && $starttime==$endtime) {
			$endtime = $endtime+86400-1;
		}
		$condition = '';
		/*bug
		if (!empty($_GPC['title'])) {
			$condition .= " AND title LIKE '%{$_GPC['title']}%'";
		}
		//bug
		if (!empty($_GPC['status'])) {
			// $condition .=" where status in";
		}
		if ($starttime && $endtime) {
			$condition .="between '{$starttime}' and '{$endtime}'";
		}*/
		//显示投诉记录
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 10;
		$sql    = "select a.id,a.category,b.realname,b.mobile,a.content,a.createtime,a.status,a.resolver,a.resolve,a.resolvetime from".tablename("xcommunity_report")."as a left join".tablename("xcommunity_member")."as b on a.openid=b.openid where a.weid='{$_W['weid']}' and a.regionid=".$regionid." and a.type = 2 LIMIT ".($pindex - 1) * $psize.','.$psize;
		$list   = pdo_fetchall($sql);
		$total  = pdo_fetchcolumn('select count(*) from'.tablename("xcommunity_report")."as a left join".tablename("xcommunity_member")."as b on a.openid=b.openid where a.weid='{$_W['weid']}' and a.regionid=".$regionid." and a.type = 2");
		$pager  = pagination($total, $pindex, $psize);	
	}elseif ($op == 'post') {
		//对应ID的投诉记录查看
		$sql  = "select a.id,a.category,b.realname,b.mobile,a.content,a.createtime,a.status,a.resolver,a.resolve,a.resolvetime from".tablename("xcommunity_report")."as a left join".tablename("xcommunity_member")."as b on a.openid=b.openid where a.weid='{$_W['weid']}' and a.regionid='{$regionid}' and a.id='{$id}'";
		print_r($sql);exit();
		$item = pdo_fetch($sql);
		if($_W['ispost']){
			if (!empty($_GPC['resolve'])) {
				$resolver = empty($_GPC['resolver'])?$_W['username']:$_GPC['resolver'];
				$data = array(
				'status'      => 1,
				'resolve'     => $_GPC['resolve'],
				'resolver'    => $resolver,
				'resolvetime' => $_W['timestamp'],
				);
				pdo_update("xcommunity_report",$data,array('id'=>$id));
				message('处理成功！',referer(),'success');
			}		
		}
	}elseif($op == 'delete'){
		pdo_delete("xcommunity_report",array('weid'=>$_W['weid'],'id' =>$id));
		message('删除成功！',referer(),'success');
	}
	include $this->template('report');