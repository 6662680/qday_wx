<?php
/**
 * [SxxPro System] Copyright (c) 2014 qdaygroup.com
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
if ($action != 'cash') {
	checkauth();
}
load()->model('activity');
$filter = array();
$coupons = activity_coupon_owned($_W['member']['uid'], $filter);
$tokens = activity_token_owned($_W['member']['uid'], $filter);

$setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'uc', 'payment', 'passport'));
$behavior = $setting['creditbehaviors'];
$creditnames = $setting['creditnames'];
$credits = mc_credit_fetch($_W['member']['uid'], '*');


$sql = 'SELECT `status` FROM ' . tablename('mc_card') . " WHERE `uniacid` = :uniacid";
$cardstatus = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));


