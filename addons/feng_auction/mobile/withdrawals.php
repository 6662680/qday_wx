<?php
	$members = pdo_fetch("SELECT * FROM ".tablename('auction_member')." WHERE uniacid = '{$weid}' and from_user = '{$_W['fans']['from_user']}' ");
	if (empty($members['bankcard']) || empty($members['bankname']) || empty($members['alipay']) || empty($members['aliname'])) {
		message('请先完善您的提现账号！',$this->createMobileUrl('prodata'), 'error');
	}
	$withd_record = pdo_fetchall("SELECT * FROM ".tablename('auction_withdrawals')." WHERE uniacid = '{$weid}' and uid = '{$members['id']}' ORDER BY id DESC");
	$allprice = 0;
	if (!empty($withd_record)) {
		foreach ($withd_record as $key => $value) {
			$allprice+=$value['price'];
		}
	}
	include $this->template('withdrawals');
?>