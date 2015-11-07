<?php
	if (empty($_GPC['price'])||empty($_GPC['id'])) {
        message('抱歉，参数错误！', '', 'error');
    }
	$price = $_GPC['price'];
	$id = $_GPC['id'];
	if ($price > 0) {
		$member = pdo_fetch("SELECT * FROM ".tablename('auction_member')." WHERE uniacid = '{$weid}' and from_user = '{$_W['fans']['from_user']}' ");
		if ($member['balance'] > $price) {
			$m_data['balance'] = $member['balance'] - $price;
			$data['status'] =  1;
			if (pdo_update('auction_member', $m_data, array('id' => $member['id']))) {
				pdo_update('auction_goodslist', $data, array('id' => $id));
				message('余款付款成功！',$this->createMobileUrl('myauction'), 'success');
			}
		}else{
			message('余额不足，请充值！',$this->createMobileUrl('recharge'), 'error');
		}
	}else{
		$member = pdo_fetch("SELECT * FROM ".tablename('auction_member')." WHERE uniacid = '{$weid}' and from_user = '{$_W['fans']['from_user']}' ");
		$m_data['balance'] = $member['balance'] - $price;
		$data['status'] =  1;
		if (pdo_update('auction_member', $m_data, array('id' => $member['id']))) {
			pdo_update('auction_goodslist', $data, array('id' => $id));
			$rech_data['uniacid'] = $weid;
			$rech_data['from_user'] = $member['from_user'];
			$rech_data['nickname'] = $member['nickname'];
			$rech_data['uid'] = $member['id'];
			$rech_data['ordersn'] = 88888888;
			$rech_data['status'] = 1;
			$rech_data['paytype'] = 4;
			$rech_data['price'] = -$price;
			$rech_data['createtime'] = time();
			pdo_insert('auction_recharge', $rech_data);
			message('余款付款成功！',$this->createMobileUrl('myauction'), 'success');
		}
	}
?>