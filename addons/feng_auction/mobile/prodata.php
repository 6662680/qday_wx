<?php
	$people = pdo_fetch("SELECT * FROM " . tablename('auction_member') . " WHERE uniacid= '{$weid}' AND from_user= '{$_W['fans']['from_user']}'");
	include $this->template('prodata');
?>