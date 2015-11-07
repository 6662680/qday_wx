<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区用户信息
 */
defined('IN_IA') or exit('Access Denied');
	global $_GPC,$_W;
	$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$regionid  = $_GPC['regionid'];
	$id        = $_GPC['id'];
	if ($op == 'display') { 
			$condition = '';
			if (!empty($_GPC['realname'])) {
				$condition .= " AND realname LIKE '%{$_GPC['realname']}%'";
			}
			if (!empty($_GPC['mobile'])) {
				$condition .= " AND mobile LIKE '%{$_GPC['mobile']}%'";
			}
			//显示住户信息
			$pindex = max(1, intval($_GPC['page']));
			$psize  = 10;
			$sql    = "select * from ".tablename("xcommunity_member")."where weid='{$_W['weid']}' $condition and regionid='{$regionid}' LIMIT ".($pindex - 1) * $psize.','.$psize;
			$list   = pdo_fetchall($sql);
			$total  = pdo_fetchcolumn('select count(*) from'.tablename("xcommunity_member")."where weid='{$_W['weid']}' $condition and regionid=:regionid",array(':regionid' => $regionid));
			$pager  = pagination($total, $pindex, $psize);

	}elseif($op == 'post') {
		//查看住户信息
		if ($id) {
			$member = pdo_fetch("SELECT * FROM".tablename('xcommunity_member')."WHERE id=:id",array(':id' => $id));
		}
		//查看小区信息
		$regions = $this->regions();
		if(checksubmit('submit')){
		//修改用户信息
		$item  = pdo_fetch("SELECT title FROM".tablename("xcommunity_region")."WHERE id=:regionid",array(':regionid' => $regionid));
			$data = array(
				'realname'   =>$_GPC['realname'],
				'mobile'     =>$_GPC['mobile'],
				'regionid'   =>$_GPC['_regionid'],
				'address'    =>$_GPC['address'],
				'remark'     =>$_GPC['remark'],
				'createtime' =>$_W['timestamp'],
				'regionname' =>$item['title'],
				);
			pdo_update("xcommunity_member",$data,array('id' => $id));
			message('修改成功',$this->createWebUrl('region'), 'success');
		}		
	}elseif ($op == 'delete') {
		//删除用户
		pdo_delete("xcommunity_member",array('id'=>$id));
		message('删除成功',referer(), 'success');
	}elseif($op == 'verify'){
		//审核用户
		$status = $_GPC['status'];
		$data   = array('status' => $status);
		pdo_update("xcommunity_member",$data,array('id'=>$id));
		message('操作成功！',referer(), 'success');
	}elseif ($op == 'warrant') {
		//授权管理员操作
		$manage_status = $_GPC['manage_status'];
		pdo_query("update ".tablename("xcommunity_member")." set manage_status ='{$manage_status}' where id='{$id}'");
		message('操作成功!',referer(),'success');
	}
	include $this->template('member');