<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区报修信息
 */
defined('IN_IA') or exit('Access Denied');
	global $_GPC,$_W;
	$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$regionid  = $_GPC['regionid'];
	$id = intval($_GPC['id']);
	//获取报修表单提交的数据
	$data =array(
		'status'      => $_GPC['status'],
		'requirement' => $_GPC['requirement'],
		'resolver'    => $_W['username'],
		'resolvetime' => $_W['timestamp'],
		'resolve'     => $_GPC['status'],
		);
	//报修来往回复提交的数据
	$insert = array(
		'weid'       => $_W['weid'],
		'openid'     => $_W['fans']['from_user'],
		'reportid'   => $id,
		'isreply'    => 1,
		'content'    => $_GPC['reply'],
		'createtime' => $_W['timestamp'],
		);
	$starttime = strtotime($_GPC['birth-start']);
	$endtime   = strtotime($_GPC['birth-end']);
	if (!empty($starttime) && $starttime==$endtime) {
		$endtime = $endtime+86400-1;
	}
	//查报修子类 报修主类ID=3
	$categories = pdo_fetchall("SELECT * FROM".tablename('xcommunity_servicecategory')."WHERE weid='{$_W['weid']}' AND parentid=3");
	if($op == 'display'){
		//搜索
		/*bug
		$condition = '';
		if (!empty($_GPC['category'])) {
			$condition .= " AND category = '{$_GPC['category']}'";
		}
		if (!empty($_GPC['status'])) {
			$condition .=" AND a.status = '{$_GPC['status']}'";
		}
		if ($starttime && $endtime) {
			$condition .=" between '{$starttime}' and '{$endtime}'";
		}	
		*/
		//显示报修记录
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 10;
		$sql    = "select a.id,a.category,b.realname,b.mobile,a.content,a.createtime,a.status from".tablename("xcommunity_report")."as a left join".tablename("xcommunity_member")."as b on a.openid=b.openid where a.weid='{$_W['weid']}' $condition and a.regionid=".$regionid." and a.type = 1 LIMIT ".($pindex - 1) * $psize.','.$psize;
		$list   = pdo_fetchall($sql);
		$total  = pdo_fetchcolumn('select count(*) from'.tablename("xcommunity_report")."as a left join".tablename("xcommunity_member")."as b on a.openid=b.openid where a.weid='{$_W['weid']}' $condition and a.regionid=".$regionid." and a.type = 1");
		$pager  = pagination($total, $pindex, $psize);
		
	}elseif ($op == 'post') {
		//查出对于ID的报修记录
		$sql    = "select a.images,a.requirement,a.id,a.category,b.realname,b.mobile,a.content,a.createtime,a.status from".tablename("xcommunity_report")."as a left join".tablename("xcommunity_member")."as b on a.openid=b.openid where a.weid='{$_W['weid']}' and a.regionid='{$regionid}' and a.id='{$id}'";
		$value  = pdo_fetch($sql);
		$images = unserialize($value['images']);
		if ($images) {
			$picid  = implode(',', $images);
		    $imgs   = pdo_fetchall("SELECT * FROM".tablename('xfcommunity_images')."WHERE id in({$picid})");
		}
		$reply  = pdo_fetchall("SELECT * FROM".tablename('xcommunity_reply')."WHERE reportid=:id",array(':id' => $id));
		//把报修记录和reply记录组成一个新的数组
		 $item = array();
		 $item = array(
				'id'          =>$value['id'] ,
				'requirement' =>$value['requirement'],
				'category'    =>$value['category'],
				'realname'    =>$value['realname'],
				'content'     =>$value['content'],
				'createtime'  =>$value['createtime'],
				'status'      =>$value['status'],
				'reply'       =>$reply,
				'img'		  =>$imgs,
		 	 );

		if ($_W['ispost']) {
			pdo_update("xcommunity_report",$data,array('id'=>$id));
			pdo_insert("xcommunity_reply",$insert);
			message('更新成功!',referer(),'success');
		}
	}elseif ($op == 'delete') {
		pdo_delete("xcommunity_report",array('id'=>$id));
		message('删除成功！',referer(),'success');
	}
	include $this->template('repair');