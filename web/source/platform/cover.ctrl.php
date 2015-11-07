<?php 
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('reply');
load()->func('tpl');

$dos = array('site', 'mc', 'card', 'module');
$do = in_array($do, $dos) ? $do : 'module';
$multiid = intval($_GPC['multiid']);
$entries = array();
$entries['site']['title'] = '微站入口设置';
if(!empty($multiid)) {
	$multi_title = pdo_fetchcolumn('SELECT title FROM ' . tablename('site_multi') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $multiid));
	$entries['site']['title'] = $multi_title . '入口设置';
}

$entries['site']['module'] = 'site';
$entries['site']['do'] = '';
$entries['site']['url'] = url('home', array('i' => $_W['uniacid'], 't' => $multiid));

$entries['mc']['title'] = '个人中心入口设置';
$entries['mc']['module'] = 'mc';
$entries['mc']['do'] = '';
$entries['mc']['url'] = url('mc/home', array('i' => $_W['uniacid']));

$entries['card']['title'] = '会员卡入口设置';
$entries['card']['module'] = 'card';
$entries['card']['do'] = '';
$entries['card']['url'] = url('mc/bond/card', array('i' => $_W['uniacid']));

if($do != 'module') {
	$entry = $entries[$do];
	if($do == 'site') {
		$_W['page']['title'] = '微站入口设置 - 微站访问入口 - 微站功能';
	}
	if($do == 'mc') {
		$_W['page']['title'] = '个人中心入口设置 - 会员中心访问入口- 会员中心';
	}
	if($do == 'card') {
		
		$sql = 'SELECT `status` FROM ' . tablename('mc_card') . " WHERE `uniacid` = :uniacid";
		$list = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));
		if ($list['status'] == 0) {
			message('会员卡功能未开启', url('mc/card'), 'error');
		}
		$_W['page']['title'] = '会员卡入口设置 - 会员中心访问入口- 会员中心';
	}
} else {
	$eid = intval($_GPC['eid']);
	if(empty($eid)) {
		message('访问错误');
	}
	$sql = 'SELECT * FROM ' . tablename('modules_bindings') . ' WHERE `eid`=:eid';
	$pars = array();
	$pars[':eid'] = $eid;
	$entry = pdo_fetch($sql, $pars);
	if(empty($entry) || $entry['entry'] != 'cover') {
		message('访问错误');
	}
	load()->model('module');
	$module = module_fetch($entry['module']);
	if(empty($module)) {
		message('访问错误');
	}
	$entry['url'] = murl('entry', array('do' => $entry['do'], 'm' => $entry['module']));
	$cover['title'] = $entry['title'];

		if($module['issolution']) {
		$solution = $module;
		define('FRAME', 'solution');
	} else {
		define('FRAME', 'ext');
		$types = module_types();
		define('ACTIVE_FRAME_URL', url('home/welcome/ext', array('m' => $entry['module'])));
	}
	$frames = buildframes(array(FRAME), $entry['module']);
	$frames = $frames[FRAME];
	}

$sql = "SELECT * FROM " . tablename('cover_reply') . ' WHERE `module` = :module AND `do` = :do AND uniacid = :uniacid AND multiid = :multiid';
$pars = array();
$pars[':module'] = $entry['module'];
$pars[':do'] = $entry['do'];
$pars[':uniacid'] = $_W['uniacid'];
$pars[':multiid'] = $multiid;
$cover = pdo_fetch($sql, $pars);

if(!empty($cover)) {
	$cover['saved'] = true;
	if(!empty($cover['thumb'])) {
		$cover['src'] = tomedia($cover['thumb']);
	}
	$reply = reply_single($cover['rid']);
} else {
	$cover['title'] = $entry['title'];
}
if(empty($reply)) {
	$reply = array();
}

if (checksubmit('submit')) {
	if(trim($_GPC['keywords']) == '') {
		message('必须输入触发关键字.');
	}
	
	$keywords = @json_decode(htmlspecialchars_decode($_GPC['keywords']), true);
	if(empty($keywords)) {
		message('必须填写有效的触发关键字.');
	}
	$rule = array(
		'uniacid' => $_W['uniacid'],
		'name' => $entry['title'],
		'module' => 'cover', 
		'status' => intval($_GPC['status']),
	);
	if(!empty($_GPC['istop'])) {
		$rule['displayorder'] = 255;
	} else {
		$rule['displayorder'] = range_limit($_GPC['displayorder'], 0, 254);
	}
	if (!empty($reply)) {
		$rid = $reply['id'];
		$result = pdo_update('rule', $rule, array('id' => $rid));
	} else {
		$result = pdo_insert('rule', $rule);
		$rid = pdo_insertid();
	}
	
	if (!empty($rid)) {
				$sql = 'DELETE FROM '. tablename('rule_keyword') . ' WHERE `rid`=:rid AND `uniacid`=:uniacid';
		$pars = array();
		$pars[':rid'] = $rid;
		$pars[':uniacid'] = $_W['uniacid'];
		pdo_query($sql, $pars);

		$rowtpl = array(
			'rid' => $rid,
			'uniacid' => $_W['uniacid'],
			'module' => 'cover',
			'status' => $rule['status'],
			'displayorder' => $rule['displayorder'],
		);
		foreach($keywords as $kw) {
			$krow = $rowtpl;
			$krow['type'] = range_limit($kw['type'], 1, 4);
			$krow['content'] = $kw['content'];
			pdo_insert('rule_keyword', $krow);
		}
		
		$entry = array(
			'uniacid' => $_W['uniacid'],
			'multiid' => $multiid,
			'rid' => $rid,
			'title' => $_GPC['title'],
			'description' => $_GPC['description'],
			'thumb' => $_GPC['thumb'],
			'url' => $entry['url'],
			'do' => $entry['do'],
			'module' => $entry['module'],
		);
		if (empty($cover['id'])) {
			pdo_insert('cover_reply', $entry);
		} else {
			pdo_update('cover_reply', $entry, array('id' => $cover['id']));
		}
		message('封面保存成功！', 'refresh', 'success');
	} else {
		message('封面保存失败, 请联系网站管理员！');
	}
}

template('platform/cover');