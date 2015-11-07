<?php
	if($op=='display'){
		$pindex = max(1, intval($_GPC['page']));
		$psize = 30;
		$list = pdo_fetchall("select * from ".tablename('weilive_complain')." where weid = ".$weid. " order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("select count(id) from ".tablename('weilive_complain')." where weid = ".$weid);
		$pager = pagination1($total, $pindex, $psize);
	}
	
	if($op=='sort'){
		$op = 'display';
		$sort = array(
			'mobile'=>$_GPC['mobile']
		);
		$list = pdo_fetchall("select * from ".tablename('weilive_complain')." where weid = ".$weid." and mobile like '%".$sort['mobile']."%' order by createtime desc");
	}
	
	if($op=='post'){
		$id = $_GPC['id'];
		$item = pdo_fetch("select * from ".tablename('weilive_complain')." where id = ".$id);
	}
	
	if(checksubmit('submit')){
		$update = array(
			'mobile'=>$_GPC['mobile'],
			'content'=>trim($_GPC['content']),
		);
		pdo_update('weilive_complain', $update, array('id'=>$_GPC['id']));
		message('提交成功！', $this->createWebUrl('complain'), 'success');
	}
	
	if($op=='delete'){
		$temp = pdo_delete('weilive_complain', array('id'=>$_GPC['id']));
		if($temp){
			message('删除成功！', $this->createWebUrl('complain'), 'success');
		} else {
			message('删除失败！');
		}
	}
	
	include $this->template('web/complain');
?>