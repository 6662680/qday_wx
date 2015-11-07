<?php
	$this->Judge();
	$myauction = pdo_fetchall("SELECT * FROM ".tablename('auction_goodslist')." WHERE uniacid = '{$weid}' and q_user = '{$_W['fans']['from_user']}' ");
	include $this->template('myauction');
?>