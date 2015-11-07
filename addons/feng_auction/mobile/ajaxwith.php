<?php
	$result="";
	$data=array(
		'uniacid'=>$weid,
		'uid'=>$_GPC['uid'],
		'ordersn'=>date('md') . random(4, 1),
		'status'=>0,
		'paytype'=>$_GPC['paytype'],
		'price'=>$_GPC['experience'],
		'createtime'=>TIMESTAMP
	);

	if(pdo_insert('auction_withdrawals',$data))
	{
		$result="您的提现申请提交成功！";
		$balance['balance'] = $_GPC['balance'] - $_GPC['experience'];
		pdo_update('auction_member', $balance, array('id' => $_GPC['uid']));
	}
	else
	{
		$result="您的提现申请提交失败！";
	}
	echo $result;
?>