<?php
/**
* 微信jssdk和Oauth功能类,请勿删除本人版权,本人保留本页面所有版权
* 包含分享签名,获取openid,获取用户信息,获取全局票据等
* Author yoby 微信logove qq280594236
* 
*2015-2-6 更新 BY 小黑屋 QQ2871260394
*/
defined('IN_IA') or exit('Access Denied');
if (!function_exists('dump')) {
function dump($arr){
	echo '<pre>'.print_r($arr,TRUE).'</pre>';
}

}
define('APPID','');//设置借用appid
define('APPSN','');//设置借用apps

class jssdk extends WeModuleSite
{
private $appid;
private $url;
private $appsn;
function __construct($isjie=0,$urlx=''){
    global $_W;
   $this->appid=(!$isjie)?$_W['account']['key'] : APPID;
   $this->appsn=(!$isjie)?$_W['account']['secret'] :APPSN;
    $this->url = (empty($urlx))? "http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI] : $urlx;
  }
public function get_curl($url){
	$ch=curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$data  =  curl_exec($ch);
	curl_close($ch);
	return json_decode($data,1);
}
public function post_curl($url,$post=''){
	$ch=curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$data  =  curl_exec($ch);
	curl_close($ch);
	return json_decode($data,1);
}
private function get_randstr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }
public function get_jsapi_ticket() {
if(defined('SAE_TMP_PATH')){
     $mem=memcache_init();//此处是sae写法
    $ticket =memcache_get($mem,'ticket');
     if (empty($ticket)){
           $accessToken = $this->get_access_token();
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $result =$this->get_curl($url);
            $ticket = $result["ticket"];
           memcache_set($mem,'ticket', $ticket, 0, 3600);
        }
/*
拥有memcache的服务器写法
if(class_exists('memcache')){
     $mem = new Memcache;
     $mem->connect('localhost', 11211) or die ("连接memcache错误");
    $ticket =$mem->get('ticket');
   
     if (empty($ticket)){
             $accessToken = $this->get_access_token();
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $result =$this->get_curl($url);
            $ticket = $result["ticket"];
           $mem->set('ticket', $ticket, 0, 3600);
        }
    
    
    }
    */


}else{
  global $_W;
  $weid= $_W['uniacid'];
  $ticket =$_W['account']['jsapi_ticket']['ticket'];
  $expire =$_W['account']['jsapi_ticket']['expire'];
    if(!empty($ticket) && $expire > time()) {
     //未过期
  }else{
      $accessToken = $this->get_access_token();
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
       $result =json_decode(file_get_contents($url),1);
            $ticket = $result["ticket"];
      if ($ticket) {
        $data = json_decode(file_get_contents("jsapi_ticket.json"));
        $data->expire_time = time() + 3600;
        $data->jsapi_ticket = $ticket;
        file_put_contents("jsapi_ticket.json",json_encode($data));
        $record = array();
        $record['ticket'] = $ticket;//保存全局票据
        $record['expire'] = time() + 3600;
        $row = array();
        $row['jsapi_ticket'] = iserializer($record);//序列化保存
        pdo_update('account_wechats', $row, array('uniacid' => $weid));
      }
    } 


}

    return $ticket;
  } 
public function get_access_token(){
    if(defined('SAE_TMP_PATH')){
     $mem=memcache_init();//此处是sae写法
    $access_token =memcache_get($mem,'access_token');
     if (empty($access_token)){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsn;
            $result =$this->get_curl($url);
            $access_token = $result["access_token"];
           memcache_set($mem,'access_token', $access_token, 0, 3600);
        }
  /* 非sae的memcache写法
 if(class_exists('memcache')){
     $mem = new Memcache;
     $mem->connect('localhost', 11211) or die ("连接memcache错误");
    $access_token =$mem->get('access_token');
   
     if (empty($access_token)){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsn;
            $result =$this->get_curl($url);
            $access_token = $result["access_token"];
           $mem->set('access_token', $access_token, 0, 3600);
        }
    
    
    }
  */ 
    }else{
   global $_W;
  $weid= $_W['uniacid'];
  $access_token =$_W['account']['access_token']['token'];
  $expire =$_W['account']['access_token']['expire'];
	if(!empty($access_token) && $expire > time()) {
     //未过期
	}else {
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsn}";
      $result =json_decode(file_get_contents($url),1);
       $access_token = $result["access_token"];
      if ($access_token) {
        $data = json_decode(file_get_contents("access_token.json"));
        $data->expire_time = time() + 3600;
        $data->access_token = $access_token;
        file_put_contents("access_token.json",json_encode($data));
        $record = array();
			$record['token'] = $access_token;//保存全局票据
			$record['expire'] =time() + 3600;
			$row = array();
			$row['access_token'] = iserializer($record);//序列化保存
			pdo_update('account_wechats', $row, array('uniacid' => $weid));
       
      }
    } 

    }
    return $access_token;
} 
public function get_sign() {
    $jsapi_ticket = $this->get_jsapi_ticket();
    $url = $this->url;
    $timestamp = time();
    $nonceStr =$this->get_randstr();
    $string = "jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     =>  $this->appid,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }
    public  function get_code($redirect_uri, $scope='snsapi_base',$state=123){//snsapi_userinfo
       // if($redirect_uri[0] == '/'){
       //     $redirect_uri = substr($redirect_uri, 1);
      //  }
        $redirect_uri = urlencode($redirect_uri);
        $response_type = 'code';
        $appid = $this->appid;
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type='.$response_type.'&scope='.$scope.'&state='.$state.'#wechat_redirect';
        header('Location: '.$url, true, 301);
    }
    public  function get_openid($code){
        $grant_type = 'authorization_code';
        $appid = $this->appid;
        $appsn= $this->appsn;
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsn.'&code='.$code.'&grant_type='.$grant_type.'';
         $data =json_decode(file_get_contents($url),1);
  return $data;
    }
    public  function get_user($openid){
    	$accessToken = $this->get_access_token();
    	 $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$accessToken}&openid={$openid}&lang=zh_CN";
       $data  = json_decode(file_get_contents($url),1);
        return $data;
    }
    public function get_user1($accessToken,$openid){
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='. $accessToken . '&openid='. $openid .'&lang=zh_CN';
       $data  =json_decode(file_get_contents($url),1);
        return $data;
    }
}
?>