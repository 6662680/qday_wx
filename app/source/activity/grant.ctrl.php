<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$dos = array('display');
$do = in_array($_GPC['do'], $dos) ? $_GPC['do'] : 'display';

$module = trim($_GPC['m']);
$module = 'ewei_shopping';
if(empty($module)) {
	message('模块信息错误');
}

if(!empty($params['module'])) {
	$cards = pdo_fetchall('SELECT a.id,a.couponid,b.type,b.title,b.discount,b.condition,b.starttime,b.endtime FROM ' . tablename('activity_coupon_modules') . ' AS a LEFT JOIN ' . tablename('activity_coupon') . ' AS b ON a.couponid = b.couponid WHERE a.uniacid = :uniacid AND a.module = :modu AND b.condition <= :condition AND b.starttime <= :time AND b.endtime >= :time  ORDER BY a.id DESC', array(':uniacid' => $_W['uniacid'], ':modu' => $params['module'], ':time' => TIMESTAMP, ':condition' => $params['fee']), 'couponid');
	if(!empty($cards) && $_W['member']['uid']) {
		foreach($cards as $key => &$card) {
			$has = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('activity_coupon_record') . ' WHERE uid = :uid AND uniacid = :aid AND couponid = :cid AND status = 1', array(':uid' => $_W['member']['uid'], ':aid' => $_W['uniacid'], ':cid' => $card['couponid']));
			if($has > 0){
				if($card['type'] == '1') {
					$card['fee'] = sprintf("%.2f", ($params['fee'] * $card['discount']));
					$card['discount_cn'] = sprintf("%.2f", $params['fee'] * (1 - $card['discount']));
				} elseif($card['type'] == '2') {
					$card['fee'] = sprintf("%.2f", ($params['fee'] -  $card['discount']));
					$card['discount_cn'] = $card['discount'];
				}
			} else {
				unset($cards[$key]);
			}
		}
	}
}

