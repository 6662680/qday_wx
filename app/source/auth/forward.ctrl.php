<?php
/**
 * [WeiZan System] Copyright (c) 2014 WeiZan.Com
 * WeiZan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$_W['setting']['authmode'] = empty($_W['setting']['authmode']) ? 1 : $_W['setting']['authmode'];

if($_GPC['__auth']) {
	$auth = @json_decode(base64_decode($_GPC['__auth']), true);
	if(is_array($auth) && !empty($auth['openid']) && !empty($auth['acid']) && !empty($auth['time']) && !empty($auth['hash'])) {
		if(($_W['setting']['authmode'] == 2 && abs($auth['time'] - TIMESTAMP) < 180) || $_W['setting']['authmode'] == 1) {
			$fan = mc_fansinfo($auth['openid'], $auth['acid'], $_W['uniacid']);
			if(!empty($fan)) {
				$hash = md5("{$auth['openid']}{$auth['time']}{$fan['salt']}{$_W['config']['setting']['authkey']}");
				if($auth['hash'] == $hash) {
					if ($_W['setting']['authmode'] == 2) {
						$rec = array();
						do{
							$rec['salt'] = random(8);
						} while ($rec['salt'] == $fan['salt']);
						pdo_update ('mc_mapping_fans', $rec, array('uniacid' => $_W['uniacid'], 'acid' => $auth['acid'], 'openid' => $auth['openid']));
					}
					$_SESSION['uniacid'] = $_W['uniacid'];
					$_SESSION['acid'] = $auth['acid'];
					$_SESSION['openid'] = $auth['openid'];
					$member = mc_fetch($fan['uid']);
					if (!empty($member)) {
						$_SESSION['uid'] = $fan['uid'];
					}
				}
			}
		}
	}
}

$forward = @base64_decode($_GPC['forward']);
if(empty($forward)) {
	$forward = url('mc');
} else {
	$forward = (strexists($forward, 'http://') || strexists($forward, 'https://')) ? $forward : $_W['siteroot'] . 'app/' . $forward;
}
if(strexists($forward, '#')) {
	$pieces = explode('#', $forward, 2);
	$forward = $pieces[0];
}
$forward = str_replace('&wxref=mp.weixin.qq.com', '', $forward);
$forward .= '&wxref=mp.weixin.qq.com#wechat_redirect';
header('location:' . $forward);
