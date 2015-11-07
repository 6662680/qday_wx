<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$dos = array('display', 'set', 'cancel', 'message', 'search_key');
$do = !empty($_GPC['do']) && in_array($do, $dos) ? $do : 'display';

load()->model('reply');
load()->model('account');

$_W['account']['modules'] = uni_modules();
if($_W['isajax']) {
	if($do == 'search_key') {
		$condition = '';
		$key_word = trim($_GPC['key_word']);
		if(!empty($key_word)) {
			$condition = " AND content LIKE '%{$key_word}%' ";
		}
		
		$data = pdo_fetchall('SELECT content FROM ' . tablename('rule_keyword') . " WHERE (uniacid = 0 OR uniacid = :uniacid) AND status != 0 " . $condition . ' ORDER BY uniacid DESC,displayorder DESC LIMIT 100', array(':uniacid' => $_W['uniacid']));
		$exit_da = array();
		if(!empty($data)) {
			foreach($data as $da) {
				$exit_da[] = $da['content'];
			}
		}
		exit(json_encode($exit_da));
	}
	exit('error');
}
if($do == 'display') {
	$_W['page']['title'] = '系统回复 - 特殊回复 - 高级功能';
	if (checksubmit('submit')) {
		$settings = array(
			'default' => trim($_GPC['default']),
			'welcome' => trim($_GPC['welcome']),
		);
		$item = pdo_fetch('SELECT uniacid FROM '.tablename('uni_settings')." WHERE uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
		if(!empty($item)){
			pdo_update('uni_settings', $settings, array('uniacid' => $_W['uniacid']));
		}else{
			$settings['uniacid'] = $_W['uniacid'];
			pdo_insert('uni_settings', $settings);
		}
		message('系统回复更新成功！', url('platform/special/display'));
	}
	$setting = uni_setting($_W['uniacid'], array('default', 'welcome'));
	template('platform/special-display');
	exit;
}

if($do == 'message') {
	$_W['page']['title'] = '特殊消息类型处理 - 特殊回复 - 高级功能';
	$mtypes = array();
	$mtypes['image'] = '图片消息';
	$mtypes['voice'] = '语音消息';
	$mtypes['video'] = '视频消息';
	$mtypes['shortvideo'] = '小视频消息';
	$mtypes['location'] = '位置消息';
	$mtypes['trace'] = '上报地理位置';
	$mtypes['link'] = '链接消息';
	$mtypes['enter'] = '进入聊天窗口';
	$mtypes['merchant_order'] = '微小店消息';
	if(checksubmit()) {
		$s = array_elements(array_keys($mtypes), $_GPC);
		$ms = array();
		foreach($_W['account']['modules'] as $m) {
			$ms[] = $m['name'];
		}
		foreach($s as $k => $v) {
			if($v != '' && !in_array($v, $ms)) {
				message($mtypes[$k] . "选择的处理模块无效. ");
			}
		}
		$row = array();
		$row['default_message'] = iserializer($s);
		if(pdo_update('uni_settings', $row, array('uniacid' => $_W['uniacid']))!== FALSE) {
			message('保存特殊类型消息处理成功.', 'refresh');
		} else {
			message('保存失败, 请稍后重试. ');
		}
	}
	$setting = uni_setting($_W['uniacid'], array('default_message'));
	$ds = array();
	foreach($mtypes as $k => $v) {
		$row = array();
		$row['type'] = $k;
		$row['title'] = $v;
		$row['handles'] = array();
		foreach($_W['account']['modules'] as $m) {
			if(is_array($_W['account']['modules'][$m['name']]['handles']) && in_array($k, $_W['account']['modules'][$m['name']]['handles'])) {
				$row['handles'][] = array('name' => $m['name'], 'title' => $_W['account']['modules'][$m['name']]['title']);
			}
		}
		$row['empty'] = empty($row['handles']);
		$row['current'] = is_array($setting['default_message']) ? $setting['default_message'][$k] : '';
		$ds[] = $row;
	}
	template('platform/special-message');
}

if($do == 'set') {
	$rid = intval($_GPC['id']);
	$rule = pdo_fetch("SELECT id, module FROM ".tablename('rule')." WHERE id = :id", array(':id' => $rid));
	if (empty($rule)) {
		message('抱歉，要设置的规则不存在或是已经被删除！', '', 'error');
	}
	$value = iserializer(array(
			'module' => $rule['module'],
			'id' => $rid,
	));
	if ($_GPC['type'] == 'default') {
		$data = array(
				'default' => $value,
		);
	} elseif ($_GPC['type'] == 'welcome') {
		$data = array(
				'welcome' => $value,
		);
	}
	pdo_update('uni_settings', $data, array('uniacid' => $_W['uniacid']));
	message('设置系统回复更新成功！', referer(), 'success');
}

if($do == 'cancel') {
	if ($_GPC['type'] == 'default') {
		$data = array(
				'default' => '',
		);
	} elseif ($_GPC['type'] == 'welcome') {
		$data = array(
				'welcome' => '',
		);
	}
	pdo_update('uni_settings', $data, array('uniacid' => $_W['uniacid']));
	message('取消系统回复成功！', referer(), 'success');
}


