<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan isNOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('app');
$dos = array('display', 'detail');
$do = in_array($do, $dos) ? $do : 'display';
$logo = pdo_fetchcolumn('SELECT logourl FROM  ' . tablename('coupon_setting') . ' WHERE uniacid = :aid AND acid = :cid', array(':aid' => $_W['uniacid'], ':cid' => $_W['acid']));
$colors = array(
	'Color010' => '#55bd47', 'Color020' => '#10ad61', 'Color030' => '#35a4de', 'Color040' => '#3d78da', 'Color050' => '#9058cb',
	'Color060' => '#de9c33', 'Color070' => '#ebac16', 'Color080' => '#f9861f', 'Color081' => '#f08500', 'Color082' => '#a9d92d',
	'Color090' => '#e75735', 'Color100' => '#d54036', 'Color101' => '#cf3e36'
);

if($do == 'display') {
	$type = trim($_GPC['type']) ? trim($_GPC['type']) : 'discount';
	$condition = ' WHERE acid = :acid AND type = :type AND is_display = 1 AND status = 3';
		$parma[':acid'] = $_W['acid'];
	$parma[':type'] = $type;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('coupon') . $condition, $parma);
	$data = pdo_fetchall('SELECT id,card_id,title,color,brand_name,date_info FROM ' . tablename('coupon') . $condition . ' ORDER BY id DESC LIMIT ' .($pindex - 1) * $psize.','.$psize, $parma);

	if(!empty($data)) {
		foreach($data as &$da) {
			$da['date_info'] = @iunserializer($da['date_info']);
			if($da['date_info']['time_type'] == 1) {
				$da['endtime'] = '有效期至:' . $da['date_info']['time_limit_end'];
			} else {
				$da['endtime'] = '领取后' . $da['date_info']['deadline'] . '天生效' . $da['date_info']['limit'] . '天内有效';
			}
		}
	}
	$pager = pagination($total, $pindex, $psize);
}
if($do == 'detail') {
	$id = intval($_GPC['id']);
	load()->classs('coupon');
	$acc = new coupon($_W['acid']);
	$status = $acc->AddCard($id);
	$out['errno'] = 0;
	$out['error'] = '';
	if(is_error($status)) {
		$out['errno'] = 1;
		$out['error'] = $status['message'];
		exit(json_encode($out));
	}
	$out['error'] = $status;
	exit(json_encode($out));
}
template('wechat/card');
