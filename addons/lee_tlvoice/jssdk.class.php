<?php

defined('IN_IA') or exit('Access Denied');

class jssdk extends WeModuleSite
{
 

	public function getAccessToken() {
		global $_W,$_GPC;
		load()->func('communication');
		if(!empty($_W['account']['access_token'])
		&& is_array($_W['account']['access_token']) 
		&& !empty($_W['account']['access_token']['token']) 
		&& !empty($_W['account']['access_token']['expire']) 
		&& $_W['account']['access_token']['expire'] > TIMESTAMP) {
			return $_W['account']['access_token']['token'];
		}
		if (empty($_W['account']['key']) || empty($_W['account']['secret'])) {
			return error('-1', '未填写公众号的 appid 及 appsecret！');
		}
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$_W['account']['key']}&secret={$_W['account']['secret']}";
		$content = ihttp_get($url);
		if(is_error($content)) {
			message('获取微信公众号授权失败, 请稍后重试！错误详情: ' . $content['message']);
		}
		$token = @json_decode($content['content'], true);
		if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['expires_in'])) {
			$errorinfo = substr($content['meta'], strpos($content['meta'], '{'));
			$errorinfo = @json_decode($errorinfo, true);
			message('获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为: 错误代码-' . $errorinfo['errcode'] . '，错误信息-' . $errorinfo['errmsg']);
		}
		$record = array();
		$record['token'] = $token['access_token'];
		$record['expire'] = TIMESTAMP + $token['expires_in'] - 200;
		$row = array();
		$row['access_token'] = iserializer($record);
		pdo_update('account_wechats', $row, array('acid' => $_W['account']['acid']));
		
		$_W['account']['access_token'] = $record;
		return $record['token'];
	}

	public function getJsApiTicket(){
		if(!empty($_W['account']['jsapi_ticket'])
		&& is_array($_W['account']['jsapi_ticket'])
		&& !empty($_W['account']['jsapi_ticket']['ticket'])
		&& !empty($_W['account']['jsapi_ticket']['expire'])
		&& $_W['account']['jsapi_ticket']['expire'] > TIMESTAMP) {
			return $_W['account']['jsapi_ticket']['ticket'];
		}
		
		load()->func('communication');
		
		$access_token = $this->getAccessToken();
		if(is_error($access_token)){
			return $access_token;
		}
		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
		$content = ihttp_get($url);
			if(is_error($content)) {
			return error(-1, '调用接口获取微信公众号 jsapi_ticket 失败, 错误信息: ' . $content['message']);
		}
		$result = @json_decode($content['content'], true);
		if(empty($result) || intval(($result['errcode'])) != 0 || $result['errmsg'] != 'ok') {
			return error(-1, '获取微信公众号 jsapi_ticket 结果错误, 错误信息: ' . $result['errmsg']);
		}
		
		$record = array();
		$record['ticket'] = $result['ticket'];
		$record['expire'] = TIMESTAMP + $result['expires_in'] - 200;
		$row = array();
		$row['jsapi_ticket'] = iserializer($record);
		pdo_update('account_wechats', $row, array('acid' => $_W['account']['acid']));
		
		$_W['account']['jsapi_ticket'] = $record;
		return $record['ticket'];
	}

	public function getSign(){
		global $_W;
		
		$jsapiTicket = $this->getJsApiTicket();
		if(is_error($jsapiTicket)){
			$jsapiTicket = $jsapiTicket['message'];
		}
		$nonceStr = $this->createNonceStr();
		$timestamp = TIMESTAMP;
		$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		$string1 = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
		$signature = sha1($string1);

		$config = array(
			"appId"		=> $_W['account']['key'],
			"nonceStr"	=> $nonceStr,
			"timestamp" => "$timestamp",
			"signature" => $signature,
		);
		
		if(DEVELOPMENT) {
			$config['url'] = $url;
			$config['string1'] = $string1;
			$config['name'] = $_W['account']['name'];
		}
		
		return $config; 
	}
	
	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

}
?>