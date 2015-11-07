<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
error_reporting(0);
define('IN_MOBILE', true);
$input = file_get_contents('php://input');
if (!empty($input) && empty($_GET['out_trade_no'])) {
	$obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
	$data = json_decode(json_encode($obj), true);
	if (empty($data)) {
		exit('fail');
	}
	if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
				exit('fail');
	}
	$get = $data;
} else {
	$get = $_GET;
}
require '../../framework/bootstrap.inc.php';
$_W['uniacid'] = $_W['weid'] = $get['attach'];

$setting = uni_setting($_W['uniacid'], array('payment'));
if(is_array($setting['payment'])) {
	$wechat = $setting['payment']['wechat'];
	if(!empty($wechat)) {
		ksort($get);
		$string1 = '';
		foreach($get as $k => $v) {
			if($v != '' && $k != 'sign') {
				$string1 .= "{$k}={$v}&";
			}
		}
		$wechat['signkey'] = ($wechat['version'] == 1) ? $wechat['key'] : $wechat['signkey'];
		$sign = strtoupper(md5($string1 . "key={$wechat['signkey']}"));
		if($sign == $get['sign']) {
			$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniontid`=:uniontid';
			$params = array();
			$params[':uniontid'] = $get['out_trade_no'];
			$log = pdo_fetch($sql, $params);
			if(!empty($log) && $log['status'] == '0') {
				$log['tag'] = iunserializer($log['tag']);
				$log['tag']['transaction_id'] = $get['transaction_id'];
				
				$record = array();
				$record['status'] = '1';
				$record['tag'] = iserializer($log['tag']);
				pdo_update('core_paylog', $record, array('plid' => $log['plid']));
				
				$site = WeUtility::createModuleSite($log['module']);
				if(!is_error($site)) {
					$method = 'payResult';
					if (method_exists($site, $method)) {
						$ret = array();
						$ret['weid'] = $log['weid'];
						$ret['uniacid'] = $log['uniacid'];
						$ret['result'] = 'success';
						$ret['type'] = $log['type'];
						$ret['from'] = 'notify';
						$ret['tid'] = $log['tid'];
						$ret['uniontid'] = $log['uniontid'];
						$ret['user'] = $log['openid'];
						$ret['fee'] = $log['fee'];
						$ret['tag'] = $log['tag'];
						$site->$method($ret);
						exit('success');
					}
				}
			}
		}
	}
}
exit('fail');
