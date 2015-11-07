<?php
	$people = pdo_fetch("SELECT * FROM " . tablename('auction_member') . " WHERE uniacid= '{$weid}' AND from_user= '{$_W['fans']['from_user']}'");
	if (!$people) {
		message('请先填写您的资料！', $this->createMobileUrl('prodata'), 'warning');
	}
	$kf_url = $share_data['url'];
	include $this->template('profile');
?>