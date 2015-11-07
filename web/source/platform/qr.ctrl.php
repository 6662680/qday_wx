<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */

defined('IN_IA') or exit('Access Denied');

$dos = array('display', 'post', 'list', 'del', 'delsata', 'extend', 'SubDisplay');
$do = !empty($_GPC['do']) && in_array($do, $dos) ? $do : 'list';
load()->model('account');
if($do == 'list') {
	$_W['page']['title'] = '管理二维码 - 二维码管理 - 高级功能';
	$acidarr = uni_accounts($_W['uniacid']);
	$acid = intval($_GPC['acid']);
	$keyword = trim($_GPC['keyword']);
	$wheresql = " WHERE uniacid = {$_W['uniacid']} AND type = 'scene'";
	if($acid > 0) {
		$wheresql .= " AND acid = {$acid} ";
	}
	if(!empty($keyword)) {
		$wheresql .= " AND name LIKE '%{$keyword}%'";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$list = pdo_fetchall("SELECT * FROM ".tablename('qrcode'). $wheresql . ' ORDER BY `id` DESC LIMIT '.($pindex - 1) * $psize.','. $psize);
	foreach ($list as $key=>$value) {
		$list[$key]['uniname'] = $acidarr[$value['acid']]['name'];
	}
	if (!empty($list)) {
		foreach ($list as $index => &$qrcode) {
			$qrcode['showurl'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($qrcode['ticket']);
			$qrcode['endtime'] = $qrcode['createtime'] + $qrcode['expire'];
			if (TIMESTAMP > $qrcode['endtime']) {
				$qrcode['endtime'] = '<font color="red">已过期</font>';
			}else{
				$qrcode['endtime'] = date('Y-m-d H:i:s',$qrcode['endtime']);
			}
			if ($qrcode['model'] == 2) {
				$qrcode['modellabel']="永久";
				$qrcode['expire']="永不";
				$qrcode['endtime'] = '<font color="green">永不</font>';
			} else {
				$qrcode['modellabel']="临时";
			}
		}
	}
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('qrcode') . $wheresql);
	$pager = pagination($total, $pindex, $psize);
		pdo_query("UPDATE ".tablename('qrcode')." SET status = '0' WHERE uniacid = '{$_W['uniacid']}' AND model = '1' AND createtime < '{$_W['timestamp']}' - expire");
	template('platform/qr-list');
}

if($do == 'del') {
	if ($_GPC['scgq']) {
		$list = pdo_fetchall("SELECT id FROM ".tablename('qrcode')." WHERE uniacid = '{$_W['uniacid']}' AND status = '0' AND type='scene'", array(), 'id');
		if (!empty($list)) {
			pdo_query("DELETE FROM ".tablename('qrcode')." WHERE id IN (".implode(',', array_keys($list)).")");
			pdo_query("DELETE FROM ".tablename('qrcode_stat')." WHERE qid IN (".implode(',', array_keys($list)).")");
		}
		message('执行成功<br />删除二维码：'.count($list), url('platform/qr/list'),'success');
	}else{
		$id = $_GPC['id'];
		pdo_delete('qrcode', array('id' =>$id, 'uniacid' => $_W['uniacid']));
		pdo_delete('qrcode_stat',array('qid' => $id, 'uniacid' => $_W['uniacid']));
		message('删除成功',url('platform/qr/list'),'success');
	}
}

if($do == 'post') {
	$_W['page']['title'] = '生成二维码 - 二维码管理 - 高级功能';
	$acidarr = uni_accounts($_W['uniacid']);
	load()->func('communication');
	if(checksubmit('submit')){
				$barcode = array(
				'expire_seconds' => '',
				'action_name' => '',
				'action_info' => array(
					'scene' => array('scene_id' => ''),
				),
		);
		$qrctype = intval($_GPC['qrc-model']);
		$acid = intval($_GPC['acid']);
		$uniacccount = WeAccount::create($acid);
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$qrcrow = pdo_fetch("SELECT * FROM ".tablename('qrcode')." WHERE uniacid = {$_W['uniacid']} AND id = '{$id}'");
			$update = array(
					'keyword' => $_GPC['keyword'],
					'name' => $_GPC['scene-name'],
					'status' => '1',
			);
			if ($qrcrow['model'] == 1) {
				$barcode['action_info']['scene']['scene_id'] = $qrcrow['qrcid'];
				$barcode['expire_seconds'] = intval($_GPC['expire-seconds']);
				$barcode['action_name'] = 'QR_SCENE';
				$result = $uniacccount->barCodeCreateDisposable($barcode);
				$update['ticket'] = $result['ticket'];
				$update['expire'] = $result['expire_seconds'];
				$update['url'] = $result['url'];
				$update['createtime'] = TIMESTAMP;
			} elseif ($qrcrow['model'] == 2) {
				$barcode['action_info']['scene']['scene_id'] = $qrcrow['qrcid'];
				$barcode['action_name'] = 'QR_LIMIT_SCENE';
				$result = $uniacccount->barCodeCreateFixed($barcode);
				$update['ticket'] = $result['ticket'];
				$update['url'] = $result['url'];
				$update['createtime'] = TIMESTAMP;
			}
			pdo_update('qrcode', $update, array('uniacid' => $_W['uniacid'], 'id' => $id));
			message('恭喜，更新带参数二维码成功！', url('platform/qr/list'), 'success');
		}
	
		if ($qrctype == 1) {
			$qrcid = pdo_fetchcolumn("SELECT qrcid FROM ".tablename('qrcode')." WHERE acid = :acid AND model = '1' ORDER BY qrcid DESC LIMIT 1", array(':acid' => $acid));
			$barcode['action_info']['scene']['scene_id'] = !empty($qrcid) ? ($qrcid+1) : 100001;
			$barcode['expire_seconds'] = intval($_GPC['expire-seconds']);
			$barcode['action_name'] = 'QR_SCENE';
			$result = $uniacccount->barCodeCreateDisposable($barcode);
		} else if ($qrctype == 2) {
			$qrcid = pdo_fetchcolumn("SELECT qrcid FROM ".tablename('qrcode')." WHERE acid = :acid AND model = '2' ORDER BY qrcid DESC LIMIT 1", array(':acid' => $acid));
			$barcode['action_info']['scene']['scene_id'] = !empty($qrcid) ? ($qrcid+1) : 1;
			if ($barcode['action_info']['scene']['scene_id'] > 100000) {
				message('抱歉，永久二维码已经生成最大数量，请先删除一些。');
			}
			$barcode['action_name'] = 'QR_LIMIT_SCENE';
			$result = $uniacccount->barCodeCreateFixed($barcode);
		} else {
			message('抱歉，此公众号暂不支持您请求的二维码类型！');
		}
		
		if(!is_error($result)) {
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'acid' => $acid,
				'qrcid' => $barcode['action_info']['scene']['scene_id'],
				'keyword' => $_GPC['keyword'],
				'name' => $_GPC['scene-name'],
				'model' => $_GPC['qrc-model'],
				'ticket' => $result['ticket'],
				'url' => $result['url'],
				'expire' => $result['expire_seconds'],
				'createtime' => TIMESTAMP,
				'status' => '1',
				'type' => 'scene',
			);
			pdo_insert('qrcode', $insert);
			message('恭喜，生成带参数二维码成功！', url('platform/qr/list', array('name' => 'qrcode')), 'success');
		} else {
			message("公众平台返回接口错误. <br />错误代码为: {$result['errorcode']} <br />错误信息为: {$result['message']}");
		}
	}
	$id = intval($_GPC['id']);
	$row = pdo_fetch("SELECT * FROM ".tablename('qrcode')." WHERE uniacid = {$_W['uniacid']} AND id = '{$id}'");
	template('platform/qr-post');
}

if($do == 'extend') {
	load()->func('communication');
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		$qrcrow = pdo_fetch("SELECT * FROM ".tablename('qrcode')." WHERE uniacid = {$_W['uniacid']} AND id = '{$id}' LIMIT 1");
		$update = array();
		if ($qrcrow['model'] == 1) {
			$uniacccount = WeAccount::create($qrcrow['acid']);
			$barcode['action_info']['scene']['scene_id'] = $qrcrow['qrcid'];
			$barcode['expire_seconds'] = 604800;
			$barcode['action_name'] = 'QR_SCENE';
			$result = $uniacccount->barCodeCreateDisposable($barcode);
			if(is_error($result)) {
				message($result['message'], '', 'error');
			}
			$update['ticket'] = $result['ticket'];
			$update['url'] = $result['url'];
			$update['expire'] = $result['expire_seconds'];
			$update['createtime'] = TIMESTAMP;
			pdo_update('qrcode', $update, array('id' => $id, 'uniacid' => $_W['uniacid']));
		}
		message('恭喜，延长临时二维码时间成功！', referer(), 'success');
	}
}

if($do == 'display') {
	$_W['page']['title'] = '扫描统计 - 二维码管理 - 高级功能';
	load()->func('tpl');
	$acidarr = uni_accounts($_W['uniacid']);

	$starttime = empty($_GPC['time']['start']) ? TIMESTAMP -  86399 * 30 : strtotime($_GPC['time']['start']);
	$endtime = empty($_GPC['time']['end']) ? TIMESTAMP + 6*86400: strtotime($_GPC['time']['end']);
	$where .= " AND createtime >= '$starttime' AND createtime < '$endtime'";
	intval($_GPC['acid']) > 0 && $where .= ' AND acid = ' . intval($_GPC['acid']);
	!empty($_GPC['keyword']) && $where .= " AND name LIKE '%{$_GPC['keyword']}%'";
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$list = pdo_fetchall("SELECT * FROM ".tablename('qrcode_stat')." WHERE uniacid = '{$_W['uniacid']}' $where ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','. $psize);

	if (!empty($list)) {
		$openid = array();
		foreach ($list as $index => &$qrcode) {
			if ($qrcode['type'] == 1) {
				$qrcode['type']="关注";
			} else {
				$qrcode['type']="扫描";
			}
			if(!in_array($qrcode['openid'], $openid)) {
				$openid[] = $qrcode['openid'];
			}
		}
		$openids = implode("','", $openid);
		$acid = intval($_GPC['acid']);
		$param[':uniacid'] = $_W['uniacid'];
		$condition = '';
		if($acid > 0) {
			$condition = ' AND acid = :acid';
			$param[':acid'] = $acid;
		}
		$nickname = pdo_fetchall('SELECT nickname, openid FROM ' . tablename('mc_mapping_fans') . " WHERE uniacid = :uniacid " .$condition." AND openid IN ('{$openids}')", $param, 'openid');
	}
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('qrcode_stat') . " WHERE uniacid = '{$_W['uniacid']}' $where");
	$pager = pagination($total, $pindex, $psize);
	template('platform/qr-display');
}

if($do == 'delsata') {
	$id = $_GPC['id'];
	$b = pdo_delete('qrcode_stat',array('id' =>$id, 'uniacid' => $_W['uniacid']));
	if ($b){
		message('删除成功',url('platform/qr/display'),'success');
	}else{
		message('删除失败',url('platform/qr/display'),'error');
	}
}
