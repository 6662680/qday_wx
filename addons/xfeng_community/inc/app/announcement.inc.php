<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 微信端公告页面
 */
defined('IN_IA') or exit('Access Denied');
	global $_GPC,$_W;			
	$title  = '小区公告';
	$op = !empty($_GPC['op'])?$_GPC['op']:'display';
	$id = intval($_GPC['id']);
	if($op == 'display' || $op == 'more'){
		//是否是管理员操作
		$member = $this->changemember();
		//显示公告列表  status 1禁用，2启用
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		//如果是管理员，显示所有公告，否则显示启用的公告
		$condition = '';
		if (!$member['manage_status']) {
			$condition = "and status = 2";
		}
		$sql = "select * from ".tablename("xcommunity_announcement")."where weid='{$_W['weid']}' $condition and regionid='{$member['regionid']}' order by id desc LIMIT ".($pindex - 1) * $psize.','.$psize;
		$list  = pdo_fetchall($sql);
		if ($op == 'more') {
			include $this->template('announcement_more');exit();
		}
	}elseif($op =='detail'){
		$item  = pdo_fetch("select * from ".tablename("xcommunity_announcement")."where weid='{$_W['weid']}' and id =:id",array(':id' => $id));	
	}elseif ($op == 'delete') {
		pdo_delete("xcommunity_announcement",array('id' => $id ,'weid' => $_W['weid']));
		message('删除成功',referer(),'success');
	}elseif ($op == 'update') {
		//添加更新公告
		if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM".tablename('xcommunity_announcement')."WHERE id=:id",array(':id' => $id));
		}
		//查小区编号
		$member = $this->changemember();
		$data = array(
				'weid'       =>$_W['weid'],
				'regionid'   =>$member['regionid'],
				'title'      =>$_GPC['title'],
				'content'    =>htmlspecialchars_decode($_GPC['content']),
				'createtime' =>$_W['timestamp'],
				'status'     =>$_GPC['status'],
				'author'     =>$_W['account']['name'],
			);
		if($_W['ispost']){
			if (empty($id)) {
				pdo_insert("xcommunity_announcement",$data);
				message('发布成功',$this->createMobileUrl('announcement',array('op' => 'display' )),'success');
			}else{
	    		pdo_update("xcommunity_announcement",$data,array('id' => $id,'weid' => $_W['weid'] ));
	    		message('更新成功',$this->createMobileUrl('announcement',array('op' => 'display')),'success');
			}
		}
	}elseif($op == 'verify'){
		//公告状态
		$status = $_GPC['status'];
		pdo_query("update".tablename("xcommunity_announcement")." set status='{$status}' where id =:id and weid=:weid",array(':id' => $id,':weid' => $_W['weid']));
		message('操作成功',referer(),'success');
	}
	include $this->template('announcement');