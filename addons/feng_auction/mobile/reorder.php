<?php
	if (empty($_GPC['count'])) {
        message('抱歉，参数错误！', '', 'error');
    }
	$count = intval($_GPC['count']);
	$ordersn=date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
	$proplemess = pdo_fetch("SELECT * FROM ".tablename('auction_member')." WHERE uniacid = '{$weid}' and from_user ='{$_W['fans']['from_user']}' ");
	if (empty($proplemess)) {
		message('请先填写您的资料！', $this->createMobileUrl('prodata'), 'warning');
	}

	$data=array(
		'from_user'=>$_W['fans']['from_user'],
		'nickname'=>$proplemess['nickname'],
		'uniacid'=>$weid,
		'uid'=>$proplemess['id'],
		'ordersn'=>$ordersn,
		'status'=>0,
		'price'=>$count,
		'createtime' => TIMESTAMP,
	);

	if(pdo_insert(auction_recharge,$data))
	{
		$orderid = pdo_insertid();
		message('提交成功！',$this->createMobileUrl('pay',array('id'=>$orderid)),'success');
	}else{
		message('提交失败！');
	}
?>