<?php
	$hostid = $_GPC['hostid'];
	
	if($op=='detail'){
		$item = pdo_fetch("select * from ".tablename('weilive_shophost')." where id = ".$hostid);
	}
	
	if(checksubmit('submit')){
		$host = array(
			'realname'=>$_GPC['realname'],
			'mobile'=>$_GPC['mobile'],
			'pwd'=>$_GPC['pwd'],
		);
		
		$temp = pdo_update('weilive_shophost', $host, array('id'=>$hostid));
		message('提交成功！', $this->createWebUrl('stores'), 'success');
	}
	
	include $this->template('web/host_detail');
?>