<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan isNOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$current['designer'] = ' class="current"';

$accs = uni_accounts();
$accounts = array();
if(!empty($accs)) {
	foreach($accs as $acc) {
		if($acc['level'] > 1) {
			$accounts[$acc['acid']] = array_elements(array('name', 'acid'), $acc);
		}
	}
}
$dos = array('display', 'save', 'remove', 'refresh', 'search_key');
if($_W['isajax']) {
	if($do == 'search_key') {
		$condition = '';
		$key_word = trim($_GPC['key_word']);
		if(!empty($key_word)) {
			$condition = " AND content LIKE '%{$key_word}%' ";
		}
		
		$data = pdo_fetchall('SELECT content FROM ' . tablename('rule_keyword') . " WHERE (uniacid = 0 OR uniacid = :uniacid) AND status != 0 " . $condition . ' ORDER BY uniacid DESC,displayorder DESC LIMIT 15', array(':uniacid' => $_W['uniacid']));
		$exit_da = array();
		if(!empty($data)) {
			foreach($data as $da) {
				$exit_da[] = $da['content'];
			}
		}
		exit(json_encode($exit_da));
	}
	$post = $_GPC['__input'];
	if(!empty($post['method'])) {
		$do = $post['method'];
	}
}

$do = in_array($do, $dos) ? $do : 'display';

if($do == 'remove') {
	$flag = true;
	foreach($accounts as $acc) {
		$account = WeAccount::create($acc['acid']);
		$update = $account->menuQuery();
		$update[] = array('createtime' => time());
		if($flag) {
			pdo_update('uni_settings', array('menuset' => iserializer($update)), array('uniacid' => $_W['uniacid']));
			$flag = false;
		}
		$ret = $account->menuDelete();
		if(is_error($ret)) {
			exit(json_encode($ret));
		}
	}
	exit('success');
}

if($do == 'save') {
	if ($post['type'] == 'history') {
				$sql = 'SELECT `menuset` FROM ' . tablename('uni_settings'). ' WHERE `uniacid` = :uniacid';
		$post['menus'] = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
		$post['menus'] = iunserializer(base64_decode($post['menus']));
		if(empty($post['menus']) || !is_array($post['menus'])){
			exit(json_encode(error('-1','菜单数据历史记录错误.')));
		}
		unset($post['menus'][count($post['menus']) - 1]);
	}

	if (!empty($post['menus'])) {
		foreach ($post['menus'] as &$m) {
			$m['title'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $m['title']);
			if (!empty($m['subMenus'])) {
				foreach ($m['subMenus'] as &$subm) {
					$subm['title'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $subm['title']);
				}
			}
		}
	}
	$menuset = $menus = $post['menus'];
	
	if ($post['type'] != 'history') {
		$menuset[] = array('createtime' => time());
		pdo_update('uni_settings', array('menuset' => base64_encode(iserializer($menuset))), array('uniacid' => $_W['uniacid']));
	}
	
	foreach($accounts as $acc) {
		$account = WeAccount::create($acc['acid']);
		$ret = $account->menuCreate($menus);
		if(is_error($ret)) {
			exit(json_encode($ret));
		}
	}
	exit('success');
}

if($do == 'display') {
	$_W['page']['title'] = '菜单设计器 - 自定义菜单 - 高级功能';
	if(!empty($accounts)) {
		if(empty($menus) || !is_array($menus)) {
			$acc = array_shift($accounts);
			$account = WeAccount::create($acc['acid']);
			$menus = $account->menuQuery();
		}
	}
	if (is_error($menus)) {
		message($menus['message'], '', 'error');
	}
	$sql = 'SELECT `menuset` FROM ' . tablename('uni_settings'). ' WHERE `uniacid` = :uniacid';
	$hmenus = array();
	$hmenu = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid']));
	if (!empty($hmenu)) {
		$hmenus = iunserializer(base64_decode($hmenu));
		$createtime = !empty($hmenus) && is_array($hmenus) ? array_pop($hmenus) : '';
	}
	if (!is_array($hmenus)) {
		$hmenus = array();
	}
	if(!is_array($menus)) {
		$menus = array();
	}
}
template('platform/menu');
