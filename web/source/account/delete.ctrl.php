<?php
/**
 * [WeiZan System] Copyright (c) 2014 WeiZan.Com
 * WeiZan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$id = intval($_GPC['uniacid']);

$account = pdo_fetch("SELECT * FROM ".tablename('uni_account')." WHERE uniacid = :uniacid", array(':uniacid' => $id));
if (empty($account)) {
	message('抱歉，帐号不存在或是已经被删除', url('account/display'), 'error');
}

$state = uni_permission($uid, $id);
if($state != 'founder' && $state != 'manager') {
	message('没有该公众号操作权限！', url('accound/display'), 'error');
}

if($_GPC['uniacid'] == $_W['uniacid']) {
	isetcookie('__uniacid', '');
}

$modules = array();
$rules = pdo_fetchall("SELECT id, module FROM ".tablename('rule')." WHERE uniacid = '{$id}'");
if (!empty($rules)) {
	foreach ($rules as $index => $rule) {
		$deleteid[] = $rule['id'];
		if (empty($modules[$rule['module']])) {
			$file = IA_ROOT . '/framework/builtin/'.$rule['module'].'/module.php';
			if (file_exists($file)) {
				include_once $file;
			}
			$modules[$rule['module']] = WeUtility::createModule($rule['module']);
		}
		if (method_exists($modules[$rule['module']], 'ruleDeleted')) {
			$modules[$rule['module']]->ruleDeleted($rule['id']);
		}
	}
	pdo_delete('rule', "id IN ('".implode("','", $deleteid)."')");
}

$subaccount = pdo_fetchall("SELECT acid FROM ".tablename('account')." WHERE uniacid = :uniacid", array(':uniacid' => $id));
if (!empty($subaccount)) {
	foreach ($subaccount as $account) {
		@unlink(IA_ROOT . '/attachment/qrcode_'.$account['acid'].'.jpg');
		@unlink(IA_ROOT . '/attachment/headimg_'.$account['acid'].'.jpg');
	}
	$acid = intval($_GPC['acid']);
	if (empty($acid)) {
		load()->func('file');
		rmdirs(IA_ROOT . '/attachment/images/' . $id);
		@rmdir(IA_ROOT . '/attachment/images/' . $id);
		rmdirs(IA_ROOT . '/attachment/audios/' . $id);
		@rmdir(IA_ROOT . '/attachment/audios/' . $id);
	}
}

$tables = pdo_fetchall("SHOW TABLES;");
if (!empty($tables)) {
	foreach ($tables as $table) {
		$table = array_shift($table);
		if (strpos($table, $GLOBALS['_W']['config']['db']['tablepre']) !== 0) {
			continue;
		}
		$tablename = str_replace($GLOBALS['_W']['config']['db']['tablepre'], '', $table);
		if (pdo_fieldexists($tablename, 'uniacid')) {
			pdo_delete($tablename, array( 'uniacid'=> $id));
		}

		if (pdo_fieldexists($tablename, 'weid')) {
			pdo_delete($tablename, array('weid' => $id));
		}
	}
}
message('公众帐号信息删除成功！', url('account/display'), 'success');
