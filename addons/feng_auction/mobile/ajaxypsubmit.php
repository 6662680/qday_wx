<?php
	$result="";
	$data=array(
		'uniacid'=>$weid,
		'from_user'=>$_W['fans']['from_user'],
		'realname'=>$_GPC['acceptname'],
		'nickname'=>$_GPC['nickname'],
		'mobile'=>$_GPC['phone'],
		'address'=>$_GPC['addr'],
		'bankcard'=>$_GPC['bankcard'],
		'bankname'=>$_GPC['bankname'],
		'alipay'=>$_GPC['alipay'],
		'aliname'=>$_GPC['aliname'],
	);

	if (empty($_GPC['id'])) {
		if(pdo_insert('auction_member',$data))
		{
			$result="您的资料修改成功！";
		}
		else
		{
			$result="您的资料修改失败！";
		}
	}else{
		if(pdo_update('auction_member', $data, array('id' => $_GPC['id'])))
		{
			$result="您的资料修改成功！";
		}
		else
		{
			$result="您的资料修改失败！";
		}
	}
	
	echo $result;
?>