<?php
	$ops = array('display', 'post', 'delete'); // 只支持此 3 种操作.
	$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
	if($op=='display'){
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$members = pdo_fetchall("SELECT * FROM ".tablename('auction_member')." WHERE uniacid = '{$weid}' ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('auction_member') . " WHERE uniacid = '{$weid}' ");
		$pager = pagination($total, $pindex, $psize);
		include $this->template('member');
		exit;
	}
	if($op=='delete'){
		$id = intval($_GPC['id']);
		if(empty($id)){
			message("未找到指定会员!");
		}
		$result= pdo_delete('auction_member',array('id' =>$id ));
		if($result == 1){
			message("删除成功",$this->createWebUrl('member'),'success');

		}else{
			message("删除失败",$this->createWebUrl('member'),'error');
		}
	}
	if($op=='post'){
		$id=intval($_GPC['id']);
		$chargenum=intval($_GPC['chargenum']);
		$member = pdo_fetch("SELECT * FROM ".tablename('auction_member')." WHERE uniacid = '{$weid}' and id='{$_GPC['id']}'");
		$data=array(
			'balance'=>$member['balance']+$chargenum
		);
		$ordersn=date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
		$rdata=array(
			'uniacid'=>$weid,
			'from_user'=>$member['from_user'],
			'uid' => $member['id'],
			'nickname'=>$member['nickname'],
			'ordersn' => $ordersn,
			'status'=>1,
			'paytype'=>5,
			'price'=>$chargenum,
			'createtime'=>time()
			
		);
		if(pdo_insert('auction_recharge',$rdata))
		{
			if(pdo_update('auction_member', $data, array('id' => $_GPC['id'])))
			{
				$result="余额充值成功！";
			}
			else
			{
				$result="余额充值失败！";
			}
		}
		echo $result;
		exit;
	}
?>