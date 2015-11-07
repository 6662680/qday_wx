<?php
	$goods = pdo_fetch("SELECT * FROM ".tablename('auction_member')." WHERE uniacid = '{$weid}' and from_user = '{$_W['fans']['from_user']}' ");
	include $this->template('recharge');
?>