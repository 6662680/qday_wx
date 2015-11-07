<?php
	$people = pdo_fetch("SELECT * FROM " . tablename('feng_member') . " WHERE uniacid= '{$_W['uniacid']}' AND from_user= '{$_W['fans']['from_user']}'");
	if (!$people) {
		message('请先填写您的资料！', $this->createMobileUrl('prodata'), 'warning');
	}
	include $this->template('profile');
?>