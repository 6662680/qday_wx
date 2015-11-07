<?php
	if (empty($_GPC['sid'])) {
	    message('抱歉，参数错误！', '', 'error');
	}
	$id = intval($_GPC['sid']);
	$goods = pdo_fetch("SELECT * FROM ".tablename('auction_goodslist')." WHERE uniacid = '{$weid}' and id = '{$id}' ");
	if ($goods['end_time']<TIMESTAMP) {
	  	$goods['state']='已结束';
	  	if (empty($goods['q_uid'])) {
	  		$redata = pdo_fetch("SELECT * FROM " . tablename('auction_record') . " WHERE uniacid = '{$weid}' and sid ='{$id}' ORDER BY createtime DESC limit 1");
	  		$data['q_uid']=$redata['nickname'];
	  		$data['q_user']=$redata['from_user'];
	  		pdo_update('auction_goodslist', $data, array('id' => $id));
	  	}
	  }else{
	  	$goods['state']='进行中';
	  }
	$records = pdo_fetchall("SELECT * FROM ".tablename('auction_record')." WHERE uniacid = '{$weid}' and sid = '{$id}' and from_user= '{$_W['fans']['from_user']}' ORDER BY createtime DESC");
	include $this->template('redetails');
?>