<?php 
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$dos = array('wechat', 'system', 'database');
$do = in_array($do, $dos) ? $do : 'wechat';
load()->func('tpl');

$params = array();
$where  = '';
$order = ' ORDER BY `id` DESC';
if ($_GPC['time']) {
		$starttime = strtotime($_GPC['time']['start']);
	$endtime = strtotime($_GPC['time']['end']);
	$timewhere = ' AND `createtime` >= :starttime AND `createtime` < :endtime';
	$params[':starttime'] = $starttime;
	$params[':endtime'] = $endtime;
}

if ($do == 'wechat') {
	$path = IA_ROOT . '/data/logs/';
	$files = glob($path . '*');
	if (!empty($_GPC['searchtime'])) {
		$searchtime = $_GPC['searchtime'] . '.log';
	} else {
		$searchtime = date('Ymd', time()) . '.log';
	}
	$tree = array();
	foreach ($files as $key=>$file) {
		if (!preg_match('/\/[0-9]+\.log/', $file)) {
			continue;
		}
		$pathinfo = pathinfo($file);
		array_unshift($tree, $pathinfo['filename']);
		if (strexists($file, $searchtime)) {
			$contents = file_get_contents($file);
		}
	}
}

if ($do == 'system') {
	$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
	$where .= " WHERE `type` = '1'";
	$sql = 'SELECT * FROM ' . tablename('core_performance') . " $where $timewhere $order LIMIT " . ($pindex - 1) * $psize .','. $psize;
	$list = pdo_fetchall($sql, $params);
	foreach ($list as $key=>$value) {
		$list[$key]['type'] = '系统日志';
		$list[$key]['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
	}
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('core_performance'). $where . $timewhere , $params);
	$pager = pagination($total, $pindex, $psize);
}

if ($do == 'database') {
	$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
	$where .= " WHERE `type` = '2'";
	$sql = 'SELECT * FROM ' . tablename('core_performance') . " $where $timewhere $order LIMIT " . ($pindex - 1) * $psize .','. $psize;
	$list = pdo_fetchall($sql, $params);
	foreach ($list as $key=>$value) {
		$list[$key]['type'] = '数据库日志';
		$list[$key]['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
	}
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('core_performance'). $where . $timewhere , $params);
	$pager = pagination($total, $pindex, $psize);
}

template('system/logs');