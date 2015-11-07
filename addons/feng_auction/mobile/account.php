<?php
	$consume = pdo_fetchall("SELECT * FROM " . tablename('auction_record') . " WHERE uniacid = '{$weid}' and from_user ='{$_W['fans']['from_user']}' and bond > 0 ORDER BY createtime DESC ");
	if (!empty($consume)) {
		$allconsume = 0;
		foreach ($consume as $key => $value) {
		$allconsume+=$value['bond'];
		}
	}
	$recharge = pdo_fetchall("SELECT * FROM " . tablename('auction_recharge') . " WHERE uniacid = '{$weid}' and from_user ='{$_W['fans']['from_user']}' and status = 1 ORDER BY createtime DESC ");
	if (!empty($recharge)) {
		$allrecharge = 0;
		foreach ($recharge as $key => $value) {
		$allrecharge+=$value['price'];
		}
	}
	include $this->template('account');
?>