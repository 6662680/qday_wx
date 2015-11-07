<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
$_W['page']['title'] = '全局设置 - 其他设置 - 系统管理';
load()->model('setting');
load()->func('communication');

if(checksubmit('bae_delete_update') || checksubmit('bae_delete_install')) {
	if(!empty($_GPC['bae_delete_update'])) {
		unlink(IA_ROOT . '/data/update.lock');
	} elseif(!empty($_GPC['bae_delete_install'])) {
		unlink(IA_ROOT . '/data/install.lock');
	}
	message('操作成功！', url('system/common'), 'success');
}

if(checksubmit('submit')) {
	$mail = array(
		'username' => $_GPC['username'],
		'password' => $_GPC['password'],
		'smtp' => $_GPC['smtp'],
		'sender' => $_GPC['sender'],
		'signature' => $_GPC['signature'],
	);
	setting_save($mail, 'mail');
	$row = pdo_fetchcolumn("SELECT `notify` FROM ".tablename('uni_settings') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));
	$notify = iunserializer($row);
	if(!is_array($notify)) {
		$notify['sms'] = array();
		$notify['sms']['balance'] = 0;
		$notify['sms']['signature'] = '系统默认';
	}
	$notify['mail'] = $mail;
	$update = array();
	$update['notify'] = iserializer($notify);
	pdo_update('uni_settings', $update, array('uniacid' => $_W['uniacid']));
	if(!empty($_GPC['testsend']) && !empty($_GPC['receiver'])) {
		$result = ihttp_email($_GPC['receiver'], $_W['setting']['copyright']['sitename'] . '验证邮件' . date('Y-m-d H:i:s'), '如果您收到这封邮件则表示您系统的发送邮件配置成功！');
		if(is_error($result)) {
			message($result['message']);
		}
	}
	message('更新设置成功！', url('system/common'));
}

if(checksubmit('authmodesubmit')) {
	$authmode = intval($_GPC['authmode']);
	setting_save($authmode, 'authmode');
	message('更新设置成功！', url('system/common'));
}

if(checksubmit('sms_submit')) {
	$sms = $_GPC['sms'];
	setting_save($sms, 'sms');
	if(!empty($_GPC['sms_testsend']) && !empty($_GPC['sms_receiver'])) {
           	        load()->func('sms');
                          $content = "您的验证码是：【{$_W['setting']['copyright']['sitename']}短信测试】。如需帮助请联系客服。";
                          $result = sms_send($_GPC['sms_receiver'], $content,$_W['setting']['copyright']['sitename'],false);
	        if(is_error($result)) {
	 	message($result['message']);
	        }
	}
	message('更新设置成功！', url('system/common'));
}

setting_load(array('authmode', 'mail','sms'));
template('system/common');