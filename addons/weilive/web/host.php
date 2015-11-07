<?php
	$id = $_GPC['id'];
	
	if($op=='display'){
		//$setting = pdo_fetch("SELECT * FROM " . tablename('weilive_setting') . " WHERE weid = :weid ", array(':weid' => $weid));
		$pindex = max(1, intval($_GPC['page']));
		$psize = 30;
		$list = pdo_fetchall("select * from ".tablename('weilive_shophost')." where weid = ".$weid. " order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("select count(id) from ".tablename('weilive_shophost')." where weid = ".$weid);
		$pager = pagination1($total, $pindex, $psize);
	}
	
	if($op=='sort'){
		$op='display';
		$sort = array(
			'realname'=>$_GPC['realname'],
			'mobile'=>$_GPC['mobile']
		);
		$list = pdo_fetchall("select * from ".tablename('weilive_shophost')." where weid = ".$weid." and realname like '%".$sort['realname']."%' and mobile like '%".$sort['mobile']."%' order by createtime desc");
	}
	
	if($op=='detail'){
		$item = pdo_fetch("select * from ".tablename('weilive_shophost')." where id = ".$id);
	}
	
	if(checksubmit('submit')){
		$host = array(
			'realname'=>$_GPC['realname'],
			'mobile'=>$_GPC['mobile'],
			'pwd'=>$_GPC['pwd'],
		);
		
		$temp = pdo_update('weilive_shophost', $host, array('id'=>$id));
		message('提交成功！', $this->createWebUrl('host'), 'success');
	}
	
	if($op=='delete'){
		$temp = pdo_delete('weilive_shophost', array('id'=>$_GPC['id']));
		if($temp){
			message('删除成功！', $this->createWebUrl('shophost'), 'success');
		} else {
			message('删除失败！', $this->createWebUrl('shophost', array('op'=>'detail', 'id'=>$id)), 'error');
		}
	}
	
	include $this->template('web/host_list');
?>