<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan isNOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

define('WEIXIN_ROOT', 'https://mp.weixin.qq.com');
define('YIXIN_ROOT', 'https://plus.yixin.im');


function uni_create_permission($uid, $type = 1) {
	$groupid = pdo_fetchcolumn('SELECT groupid FROM ' . tablename('users') . ' WHERE uid = :uid', array(':uid' => $uid));
	$groupdata = pdo_fetch('SELECT maxaccount, maxsubaccount FROM ' . tablename('users_group') . ' WHERE id = :id', array(':id' => $groupid));
	$list = pdo_fetchall('SELECT uniacid FROM ' . tablename('uni_account_users') . ' WHERE uid = :uid AND role = :role ', array(':uid' => $uid, ':role' => 'manager'));
	foreach($list as $item) {
		$uniacids[] = $item['uniacid'];
	}
	unset($item);
	$uniacidnum = count($list);
		if($type == 1) {
		if($uniacidnum >= $groupdata['maxaccount']) {
			return error('-1', '您所在的用户组最多只能创建' . $groupdata['maxaccount'] . '个主公号');
		}
	} elseif($type == 2) {
		$subaccountnum = 0;
		if(!empty($uniacids)) {
			$subaccountnum = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('account') . ' WHERE uniacid IN (' . implode(',', $uniacids) . ')');
		}
		if($subaccountnum >= $groupdata['maxsubaccount']) {
			return error('-1', '您所在的用户组最多只能创建' . $groupdata['maxsubaccount'] . '个子公号');
		}
	}
	return true;
}


function uni_create($uniAccount, $account = array()) {
	global $_W;
	load()->model('module');
	if(isset($uniAccount['groupdata'])) {
		$unisettings['groupdata'] = $uniAccount['groupdata'];
	}
	if($_W['isfounder'] && isset($uniAccount['notify'])) {
		$unisettings['notify'] = $uniAccount['notify'];
	}
	$unisettings['bootstrap'] = $uniAccount['bootstrap'];
	unset($uniAccount['bootstrap'], $uniAccount['groupdata'], $uniAccount['notify']);
	
	pdo_insert('uni_account', $uniAccount);
	$uniacid = pdo_insertid();
	if(empty($uniacid)) {
		return error('-1', '添加公众号基本信息失败');
	}
	
		$template = pdo_fetch('SELECT id,title FROM ' . tablename('site_templates') . " WHERE name = 'default'");
	$styles = array();
	$styles['uniacid'] = $uniacid;
	$styles['templateid'] = $template['id'];
	$styles['name'] = $template['title'] . '_' . random(4);
	pdo_insert('site_styles', $styles);
	$styleid = pdo_insertid();
	
		$multi['uniacid'] = $uniacid;
	$multi['title'] = $uniAccount['name'];
	$multi['quickmenu'] = iserializer(array('template' => 'default', 'enablemodule' => array()));
	$multi['styleid'] = $styleid;
	pdo_insert('site_multi', $multi);
	$multi_id = pdo_insertid();
	
	$unisettings['uniacid'] = $uniacid;
	$unisettings['default_site'] = $multi_id;
		$unisettings['creditnames'] = array('credit1' => array('title' => '积分', 'enabled' => 1), 'credit2' => array('title' => '余额', 'enabled' => 1));
	$unisettings['creditnames'] = iserializer($unisettings['creditnames']);
	$unisettings['creditbehaviors'] = array('activity' => 'credit1', 'currency' => 'credit2');
	$unisettings['creditbehaviors'] = iserializer($unisettings['creditbehaviors']);
	pdo_insert('uni_settings', $unisettings);
	
		pdo_insert('mc_groups', array('uniacid' => $uniacid, 'title' => '默认会员组', 'isdefault' => 1));
	
	$account_users = array('uniacid' => $uniacid, 'uid' => $_W['uid'], 'role' => 'manager');
	pdo_insert('uni_account_users', $account_users);
	
	module_build_privileges();
	
	if(!empty($account)) {
		$acid = account_create($uniacid, $account);
		if(empty($acid)) {
			return error('-1', '添加公众号信息失败');
		}
		return array('acid' => $acid, 'uniacid' => $uniacid);
	}
	return $uniacid;
}


function uni_group_check() {
	global $_W;
	$settings = uni_setting();
	$groupdata = $settings['groupdata'] ? $settings['groupdata'] : array();
	if($_W['account']['groupid'] != 0 && $groupdata['isexpire'] == 1 && $groupdata['endtime'] < TIMESTAMP) {
		pdo_update('uni_account', array('groupid' => 0), array('uniacid' => $_W['uniacid']));
		pdo_update('uni_settings', array('groupdata' => iserializer(array('isexpire' => 1, 'endtime' => $groupdata['endtime'], 'oldgroupid' => $_W['account']['groupid']))), array('uniacid' => $_W['uniacid']));
		$_W['account']['groupid'] = 0;
		load()->model('module');
		module_build_privileges();
		return true;
	}
	return false;
}

function uni_owned($uid = 0) {
	global $_W;
	
	$uid = empty($uid) ? $_W['uid'] : intval($uid);
	
	$uniaccounts = array();
	
	$founders = explode(',', $_W['config']['setting']['founder']);
	if(in_array($uid, $founders)) {
		$uniaccounts = pdo_fetchall("SELECT * FROM " . tablename('uni_account') . " ORDER BY `uniacid` DESC", array(), 'uniacid');
	} else {
		$uniacids = pdo_fetchall("SELECT uniacid FROM ".tablename('uni_account_users')." WHERE uid = :uid", array(':uid' => $uid), 'uniacid');
		if(!empty($uniacids)) {
			$uniaccounts = pdo_fetchall("SELECT * FROM " . tablename('uni_account') . " WHERE uniacid IN (".implode(',', array_keys($uniacids)).") ORDER BY `uniacid` DESC", array(), 'uniacid');
		}
	}
	
	return $uniaccounts;
}


function uni_permission($uid = 0, $uniacid = 0) {
	global $_W;
	
	$uid = empty($uid) ? $_W['uid'] : intval($uid);
	$uniacid = empty($uniacid) ? $_W['uniacid'] : intval($uniacid);
	
	$founders = explode(',', $_W['config']['setting']['founder']);
	if(in_array($uid, $founders)) {
		return 'founder';
	}
	
	$sql = 'SELECT `role` FROM ' . tablename('uni_account_users') . ' WHERE `uid`=:uid AND `uniacid`=:uniacid';
	$pars = array();
	$pars[':uid'] = $uid;
	$pars[':uniacid'] = $uniacid;
	$role = pdo_fetchcolumn($sql, $pars);
	return $role;
}


function uni_accounts($uniacid = 0) {
	global $_W;
	$uniacid = empty($uniacid) ? $_W['uniacid'] : intval($uniacid);
	$accounts = pdo_fetchall("SELECT w.*, a.type, a.isconnect FROM ".tablename('account')." a INNER JOIN ".tablename('account_wechats')." w USING(acid) WHERE a.uniacid = :uniacid ORDER BY a.acid ASC", array(':uniacid' => $uniacid), 'acid');
	foreach ($accounts as $acid => &$account) {
		$account['access_token'] = @iunserializer($account['access_token']);
		$account['jsapi_ticket'] = @iunserializer($account['jsapi_ticket']);
		$account['card_ticket'] = @iunserializer($account['card_ticket']);
	}
	
	return $accounts;
}


function uni_fetch($uniacid = 0) {
	global $_W;
	$uniacid = empty($uniacid) ? $_W['uniacid'] : intval($uniacid);
	$sql = 'SELECT * FROM ' . tablename('uni_account') . ' WHERE `uniacid` = :uniacid';
	$uniaccount = pdo_fetch($sql, array(':uniacid' => $uniacid));
	return $uniaccount;
}


function uni_modules($enabledOnly = true) {
	global $_W;
	$account = uni_fetch();
	$groupid = $account['groupid'];
	
	if (empty($groupid)) {
		$modules = pdo_fetchall("SELECT * FROM ".tablename('modules') . " WHERE issystem = 1 ORDER BY issystem DESC, mid ASC", array(), 'name');
	} elseif ($groupid == '-1') {
		$modules = pdo_fetchall("SELECT * FROM ".tablename('modules') . " ORDER BY issystem DESC, mid ASC", array(), 'name');
	} else {
		$wechatgroup = pdo_fetch("SELECT `modules` FROM ".tablename('uni_group')." WHERE id = :id", array(':id' => $groupid));
		$ms = '';
		if (!empty($wechatgroup['modules'])) {
			$wechatgroup['modules'] = iunserializer($wechatgroup['modules']);
			$ms = implode("','", $wechatgroup['modules']);
			$ms = " OR `name` IN ('{$ms}')";
		}
		$modules = pdo_fetchall("SELECT * FROM ".tablename('modules') . " WHERE issystem = 1{$ms} ORDER BY issystem DESC, mid ASC", array(), 'name');
	}
	foreach($modules as $k => $v) {
		if($v['issolution'] && $v['target'] != $_W['uniacid']) {
			unset($modules[$k]);
		}
	}
	if (!empty($modules)) {
		$ms = implode("','", array_keys($modules));
		$ms = "'{$ms}'";
		$mymodules = pdo_fetchall("SELECT `module`, `enabled`, `settings` FROM ".tablename('uni_account_modules')." WHERE uniacid = '{$_W['uniacid']}' AND `module` IN ({$ms}) ORDER BY enabled DESC", array(), 'module');
	}
	if (!empty($mymodules)) {
		foreach ($mymodules as $name => $row){
			if ($enabledOnly && !$modules[$name]['issystem']) {
				if ($row['enabled'] == 0 || empty($modules[$name])) {
					unset($modules[$name]);
					continue;
				}
			}
			if(!empty($row['settings'])) {
				$modules[$name]['config'] = iunserializer($row['settings']);
			}
			$modules[$name]['enabled'] = $row['enabled'];
		}
	}
	foreach ($modules as $name => &$row) {
		if ($row['issystem'] == 1) {
			$row['enabled'] = 1;
		} elseif (!isset($row['enabled'])) {
			$row['enabled'] = 1;
		}
		if(empty($row['config'])) {
			$row['config'] = array();
		}
		if(!empty($row['subscribes'])) {
			$row['subscribes'] = iunserializer($row['subscribes']);
		}
		if(!empty($row['handles'])) {
			$row['handles'] = iunserializer($row['handles']);
		}
		unset($modules[$name]['description']);
	}
	return $modules;
}


function uni_groups($groupids = array()) {
	$condition = '';
	if (!is_array($groupids)) {
		return array();
	}
	if (!empty($groupids)) {
		foreach ($groupids as $i => $row) {
			$groupids[$i] = intval($row);
		}
		unset($row);
		$condition = " WHERE id IN (".implode(',', $groupids).")";
	}
	$list = pdo_fetchall("SELECT * FROM ".tablename('uni_group').$condition." ORDER BY id ASC", array(), 'id');
	if (!empty($list)) {
		foreach ($list as &$row) {
			if (!empty($row['modules'])) {
				$modules = iunserializer($row['modules']);
				if (is_array($modules)) {
					$row['modules'] = pdo_fetchall("SELECT name, title FROM ".tablename('modules')." WHERE name IN ('".implode("','", $modules)."')");
				}
			}
			if (!empty($row['templates'])) {
				$templates = iunserializer($row['templates']);
				if (is_array($templates)) {
					$row['templates'] = pdo_fetchall("SELECT name, title FROM ".tablename('site_templates')." WHERE id IN ('".implode("','", $templates)."')");
				}
			}
		}
	}
	return $list;
}


function uni_templates() {
	global $_W;
	$groupid = $_W['account']['groupid'];
	if (empty($groupid)) {
		$templates = pdo_fetchall("SELECT * FROM ".tablename('site_templates') . " WHERE name = 'default'", array(), 'id');
	} elseif ($groupid == '-1') {
		$templates = pdo_fetchall("SELECT * FROM ".tablename('site_templates') . " ORDER BY id ASC", array(), 'id');
	} else {
		$wechatgroup = pdo_fetch("SELECT modules, templates FROM ".tablename('uni_group')." WHERE id = :id", array(':id' => $groupid));
		if (!empty($wechatgroup['templates'])) {
			$wechatgroup['templates'] = unserialize($wechatgroup['templates']);
		}
		$templates = pdo_fetchall("SELECT * FROM ".tablename('site_templates') . " WHERE name = 'default' ".(!empty($wechatgroup['templates']) ? " OR id IN (".implode(',', $wechatgroup['templates']).")" : '')." ORDER BY id ASC", array(), 'id');
	}
	return $templates;
}


function uni_setting($uniacid = 0, $fields = '*') {
	global $_W;
	$uniacid = empty($uniacid) ? $_W['uniacid'] : $uniacid;
	
	static $unisettings;
	if(empty($unisettings)){
		$unisettings = array();
	}
	
	if(empty($unisettings[$uniacid])){
		$unisetting = pdo_fetch("SELECT * FROM ".tablename('uni_settings')." WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
		if(!empty($unisetting)){
			$serialize = array('site_info', 'menuset', 'stat', 'oauth', 'passport', 'uc', 'notify', 'creditnames', 'default_message', 'creditbehaviors', 'shortcuts', 'quickmenu', 'payment', 'groupdata');
			foreach($unisetting as $key => &$row) {
				if(in_array($key, $serialize)) {
					$row = iunserializer($row);
				}
			}
		}
		$unisettings[$uniacid] = $unisetting;
	}
	if (is_array($fields)) {
		return array_elements($fields, $unisettings[$uniacid]);
	}
	return $unisettings[$uniacid];
}


function account_types() {
	static $types;
	if(empty($types)) {
		$types = array();
		$types['wechat'] = array(
			'title' => '微信',
			'name' => 'wechat',
			'sn' => '1',
			'table' => 'account_wechats'
		);
		$types['yixin'] = array(
			'title' => '易信',
			'name' => 'yixin',
			'sn' => '2',
			'table' => 'account_yixin'
		);
	}
	return $types;
}


function account_create($uniacid, $account) {
	$accountdata = array('uniacid' => $uniacid, 'type' => $account['type'], 'hash' => random(8));
	
	if($account['type'] == 1) {
		$tablename = 'account_wechats';
	} else if($account['type'] == 2) {
		$tablename = 'account_yixin';
	} else  if($account['type'] == 3){
		$tablename = 'account_alipay';
	}
	pdo_insert('account', $accountdata);
	$acid = pdo_insertid();
	$account['acid'] = $acid;
	$account['token'] = random(32);
	$account['encodingaeskey'] = random(43);
	$account['uniacid'] = $uniacid;
	unset($account['type']);
	pdo_insert($tablename, $account);
	
	return $acid;
}


function account_fetch($acid) {
	global $_W;
	$account = pdo_fetch("SELECT w.*, a.type, a.isconnect FROM ".tablename('account')." a INNER JOIN ".tablename('account_wechats')." w USING(acid) WHERE acid = :acid", array(':acid' => $acid));
	if (!empty($account)) {
		$account['access_token'] = @iunserializer($account['access_token']);
		$account['jsapi_ticket'] = @iunserializer($account['jsapi_ticket']);
		$account['card_ticket'] = @iunserializer($account['card_ticket']);
	}
	return $account;
}


function account_weixin_login($username = '', $password = '', $imgcode = '') {
	global $_W, $_GPC;
	if (empty($username) || empty($password)) {
		$username = $_W['account']['username'];
		$password = $_W['account']['password'];
	}
	$auth['token'] = cache_load('wxauth:'.$username.':token');
	$auth['cookie'] = cache_load('wxauth:'.$username.':cookie');
	load()->func('communication');
	if (!empty($auth['token']) && !empty($auth['cookie'])) {
		$response = ihttp_request(WEIXIN_ROOT . '/home?t=home/index&lang=zh_CN&token='.$auth['token'], '', array('CURLOPT_REFERER' => 'https://mp.weixin.qq.com/', 'CURLOPT_COOKIE' => $auth['cookie']));
		if (is_error($response)) {
			return false;
		}
		if (strexists($response['content'], '登录超时')) {
			cache_delete('wxauth:'.$username.':token');
			cache_delete('wxauth:'.$username.':cookie');
		}
		return true;
	}
	$loginurl = WEIXIN_ROOT . '/cgi-bin/login?lang=zh_CN';
	$post = array(
		'username' => $username,
		'pwd' => $password,
		'imgcode' => $imgcode,
		'f' => 'json',
	);
		$code_cookie = $_GPC['code_cookie'];
	$response = ihttp_request($loginurl, $post, array('CURLOPT_REFERER' => 'https://mp.weixin.qq.com/', 'CURLOPT_COOKIE' => $code_cookie));
	if (is_error($response)) {
		return false;
	}
	$data = json_decode($response['content'], true);
	if ($data['base_resp']['ret'] == 0) {
		preg_match('/token=([0-9]+)/', $data['redirect_url'], $match);
		cache_write('wxauth:'.$username.':token', $match[1]);
		cache_write('wxauth:'.$username.':cookie', implode('; ', $response['headers']['Set-Cookie']));
		isetcookie('code_cookie', '', -1000);
	} else {
		switch ($data['ErrCode']) {
			case "-1":
				$msg = "系统错误，请稍候再试。";
				break;
			case "-2":
				$msg = "微信公众帐号或密码错误。";
				break;
			case "-3":
				$msg = "微信公众帐号密码错误，请重新输入。";
				break;
			case "-4":
				$msg = "不存在该微信公众帐户。";
				break;
			case "-5":
				$msg = "您的微信公众号目前处于访问受限状态。";
				break;
			case "-6":
				$msg = "登录受限制，需要输入验证码，稍后再试！";
				break;
			case "-7":
				$msg = "此微信公众号已绑定私人微信号，不可用于公众平台登录。";
				break;
			case "-8":
				$msg = "微信公众帐号登录邮箱已存在。";
				break;
			case "-200":
				$msg = "因您的微信公众号频繁提交虚假资料，该帐号被拒绝登录。";
				break;
			case "-94":
				$msg = "请使用微信公众帐号邮箱登陆。";
				break;
			case "10":
				$msg = "该公众会议号已经过期，无法再登录使用。";
				break;
			default:
				$data['ErrCode'] = -2;
				$msg = "未知的返回。";
		}
		return error($data['ErrCode'], $msg);
	}
	return true;
}


function account_weixin_basic($username) {
	global $wechat;
	$response = account_weixin_http($username, WEIXIN_ROOT . '/cgi-bin/settingpage?t=setting/index&action=index&lang=zh_CN');
	if (is_error($response)) {
		return array();
	}
	$info = array();
	preg_match('/fakeid=([0-9]+)/', $response['content'], $match);
	$fakeid = $match[1];
	$image = account_weixin_http($username, WEIXIN_ROOT . '/misc/getheadimg?fakeid=' . $fakeid);
	if (!is_error($image) && !empty($image['content'])) {
		$info['headimg'] = $image['content'];
	}
	$image = account_weixin_http($username, WEIXIN_ROOT . '/misc/getqrcode?fakeid=' . $fakeid . '&style=1&action=download');
	if (!is_error($image) && !empty($image['content'])) {
		$info['qrcode'] = $image['content'];
	}
	preg_match('/(gh_[a-z0-9A-Z]+)/', $response['meta'], $match);
	$info['original'] = $match[1];
	preg_match('/名称([\s\S]+?)<\/li>/', $response['content'], $match);
	$info['name'] = trim(strip_tags($match[1]));
	preg_match('/微信号([\s\S]+?)<\/li>/', $response['content'], $match);
	$info['account'] = trim(strip_tags($match[1]));
	preg_match('/介绍([\s\S]+?)meta_content\">([\s\S]+?)<\/li>/', $response['content'], $match);
	$info['signature'] = trim(strip_tags($match[2]));
	preg_match('/认证情况([\s\S]+?)meta_content\">([\s\S]+?)<\/li>/', $response['content'], $match);
	$info['level_tmp'] = trim(strip_tags($match[2]));
	preg_match('/类型([\s\S]+?)meta_content\">([\s\S]+?)<\/li>/', $response['content'], $match);
	$info['type_temp'] = trim(strip_tags($match[2]));

		$info['level'] = 1;
	$is_key_secret = 1;
	if (strexists($info['type_temp'], '订阅号')) {
		if (strexists($info['level_tmp'], '微信认证')) {
			$info['level'] = 3;
		}
	} elseif (strexists($info['type_temp'], '服务号')) {
		$info['level'] = 2;
		if (strexists($info['level_tmp'], '微信认证')) {
			$info['level'] = 4;
		}
	}
	if ($is_key_secret == 1) {
		$authcontent = account_weixin_http($username, WEIXIN_ROOT . '/advanced/advanced?action=dev&t=advanced/dev&lang=zh_CN');
		preg_match_all("/value\:\"(.*?)\"/", $authcontent['content'], $match);
		$info['key'] = $match[1][2];
		$info['secret'] = $match[1][3];
		unset($match);
	}
	preg_match_all("/(?:country|province|city): '(.*?)'/", $response['content'], $match);
	$info['country'] = trim($match[1][0]);
	$info['province'] = trim($match[1][1]);
	$info['city'] = trim($match[1][2]);
	return $info;
}

function account_weixin_interface($username, $account) {
	global $_W;
	$response = account_weixin_http($username, WEIXIN_ROOT . '/advanced/callbackprofile?t=ajax-response&lang=zh_CN', 
		array(
			'url' => $_W['siteroot'].'api.php?id='.$account['id'], 
			'callback_token' => $account['token'],
			'encoding_aeskey' => $account['encodingaeskey'],
			'callback_encrypt_mode' => '0',
			'operation_seq' => '203038881',
	));
	if (is_error($response)) {
		return $response;
	}
	$response = json_decode($response['content'], true);
	if (!empty($response['base_resp']['ret'])) {
		return error($response['ret'], $response['msg']);
	}
	$response = account_weixin_http($username, WEIXIN_ROOT . '/misc/skeyform?form=advancedswitchform', array('f' => 'json', 'lang' => 'zh_CN', 'flag' => '1', 'type' => '2', 'ajax' => '1', 'random' => random(5, 1)));
	if (is_error($response)) {
		return $response;
	}
	return true;
}

function account_weixin_http($username, $url, $post = '') {
	global $_W;
	if (empty($_W['cache']['wxauth:'.$username.':token']) || empty($_W['cache']['wxauth:'.$username.':cookie'])) {
		cache_load('wxauth:'.$username.':token');
		cache_load('wxauth:'.$username.':cookie');
	}
	$auth = $_W['cache'];
	return ihttp_request($url . '&token=' . $auth['wxauth:'.$username.':token'], $post, array('CURLOPT_COOKIE' => $auth['wxauth:'.$username.':cookie'], 'CURLOPT_REFERER' => WEIXIN_ROOT . '/advanced/advanced?action=edit&t=advanced/edit&token='.$auth['wxauth:'.$username.':token']));
}

function account_weixin_userlist($pindex = 0, $psize = 1, &$total = 0) {
	global $_W;
	$url = WEIXIN_ROOT . '/cgi-bin/contactmanagepage?t=wxm-friend&lang=zh_CN&type=0&keyword=&groupid=0&pagesize='.$psize.'&pageidx='.$pindex;
	$response = account_weixin_http($_W['account']['username'], $url);
	$html = $response['content'];
	preg_match('/PageCount \: \'(\d+)\'/', $html, $match);
	$total = $match[1];
	preg_match_all('/"fakeId" : "([0-9]+?)"/', $html, $match);
	return $match[1];
}

function account_weixin_send($uid, $message = '') {
	global $_W;
	$username = $_W['account']['username'];
	if (empty($_W['cache']['wxauth'][$username])) {
		cache_load('wxauth:'.$username.':');
	}
	$auth = $_W['cache']['wxauth'][$username];
	$url = WEIXIN_ROOT . '/cgi-bin/singlesend?t=ajax-response&lang=zh_CN';
	$post = array(
		'ajax' => 1,
		'content' => $message,
		'error' => false,
		'tofakeid' => $uid,
		'token' => $auth['token'],
		'type' => 1,
	);
	$response = ihttp_request($url, $post, array(
		'CURLOPT_COOKIE' => $auth['cookie'],
		'CURLOPT_REFERER' => WEIXIN_ROOT . '/cgi-bin/singlemsgpage?token='.$auth['token'].'&fromfakeid='.$uid.'&msgid=&source=&count=20&t=wxm-singlechat&lang=zh_CN',
	));
}

function account_txweibo_login($username, $password, $verify = '') {
	$cookie = cache_load("txwall:$username");
	if (!empty($cookie)) {
		$response = ihttp_request('http://t.qq.com', '', array(
			'CURLOPT_COOKIE' => $cookie,
			'CURLOPT_REFERER' => 'http://t.qq.com/',
			"User-Agent" => "Mozilla/5.0 (Windows NT 5.1; rv:13.0) Gecko/20100101 Firefox/13.0",
		));
		if (!strexists($response['content'], '登录框')) {
			return $cookie;
		}
	}
	$loginsign = '';

	$loginui = 'http://ui.ptlogin2.qq.com/cgi-bin/login?appid=46000101&s_url=http%3A%2F%2Ft.qq.com';
	$response = ihttp_request($loginui);
	preg_match('/login_sig:"(.*?)"/', $response['content'], $match);
	$loginsign = $match[1];
	
	$checkloginurl = 'http://check.ptlogin2.qq.com/check?uin='.$username.'&appid=46000101&r='.TIMESTAMP;
	$response = ihttp_request($checkloginurl);
	$cookie = implode('; ', $response['headers']['Set-Cookie']);
	preg_match_all("/'(.*?)'/", $response['content'], $match);
	list($needVerify, $verify1, $verify2) = $match[1];
	if (!empty($needVerify)) {
		if (empty($verify)) {
			return error(1, '请输入验证码！');
		}
		$verify1 = $verify;
		$cookie .= '; ' . cache_load('txwall:verify');
	}
	$verify2 = pack('H*', str_replace('\x', '', $verify2));
	$temp = md5($password, true);
	$temp = strtoupper(md5($temp . $verify2));
	$password = strtoupper(md5($temp . strtoupper($verify1)));
	$loginurl = "http://ptlogin2.qq.com/login?u={$username}&p={$password}&verifycode={$verify1}&login_sig={$loginsign}&low_login_enable=1&low_login_hour=720&aid=46000101&u1=http%3A%2F%2Ft.qq.com&ptredirect=1&h=1&from_ui=1&dumy=&fp=loginerroralert&g=1&t=1&dummy=&daid=6&";
	$response = ihttp_request($loginurl, '', array(
		'CURLOPT_COOKIE' => $cookie,
		'CURLOPT_REFERER' => 'http://t.qq.com/',
		"User-Agent" => "Mozilla/5.0 (Windows NT 5.1; rv:13.0) Gecko/20100101 Firefox/13.0",
	));
	$info = explode("'", $response['content']);
	if ($info[1] != 0) {
		return error('1', $info[9]);
	}
	$response = ihttp_request($info[5]);
	$cookie = implode('; ', $response['headers']['Set-Cookie']);
	cache_write("txwall:$username", $cookie);
	return $cookie;
}

function account_yixin_login($username = '', $password = '', $imgcode = '') {
	$auth = cache_load('yxauth:'.$username.':');
	if (!empty($auth)) {
		$response = ihttp_request(YIXIN_ROOT . '/index', '', array('CURLOPT_COOKIE' => $auth['cookie']));
		if ($response['code'] == '200') {
			return $auth['cookie'];
		}
	}
	$loginurl = YIXIN_ROOT . '/rest/login';
	$post = array(
		'account' => $username,
		'password' => $password,
		'captcha' => $imgcode,
		'loginType' => 'YiXinUserId',
	);
	$response = ihttp_request($loginurl, $post, array(
		'CURLOPT_REFERER' => 'https://plus.yixin.im/',
	));
	if (is_error($response)) {
		return false;
	}
	$data = json_decode($response['content'], true);
	if ($data['code'] == '200') {
		cache_write('yxauth:'.$username.':cookie', implode('; ', $response['headers']['Set-Cookie']));
		return implode('; ', $response['headers']['Set-Cookie']);
	} else {
		return error('-1', $data['message']);
		return false;
	}
}

function account_yixin_basic($username) {
	global $wechat;
	$auth = cache_load('yxauth:'.$username.':');
	$response = ihttp_request(YIXIN_ROOT . '/set', '', array('CURLOPT_COOKIE' => $auth['cookie']));
	if (is_error($response)) {
		return array();
	}
	$info = array();
	preg_match('/icon\:\"(.*?)\"/', $response['content'], $match);
	$image = ihttp_request($match[1]);
	file_write('headimg_'.$wechat['acid'].'.jpg', $image['content']);
	preg_match('/qrCodeMix\:\"(.*?)\"/', $response['content'], $match);
	$image = ihttp_request($match[1]);
	file_write('qrcode_'.$wechat['acid'].'.jpg', $image['content']);
	preg_match('/signature\:\"(.*?)\"/', $response['content'], $match);
	$info['signature'] = $match[1];
	preg_match('/帐号名称<\/div>(.*?)<\/div>/', $response['content'], $match);
	$info['username'] = strip_tags($match[1]);
	return $info;
}


function uni_account_default($uniacid = 0) {
	global $_W;
	$uniacid = empty($uniacid) ? $_W['uniacid'] : intval($uniacid);
	$account = pdo_fetch("SELECT w.* FROM ".tablename('uni_account')." a LEFT JOIN ".tablename('account_wechats')." w ON a.default_acid = w.acid WHERE a.uniacid = :uniacid", array(':uniacid' => $uniacid), 'acid');
	$account['access_token'] = @iunserializer($account['access_token']);
	$account['jsapi_ticket'] = @iunserializer($account['jsapi_ticket']);
	$account['card_ticket'] = @iunserializer($account['card_ticket']);
	return $account;
}
