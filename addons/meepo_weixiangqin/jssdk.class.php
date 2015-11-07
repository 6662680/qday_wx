
<?php
/**
* 微信jssdk和Oauth功能类,请勿删除本人版权,本人保留本页面所有版权
* 包含分享签名,获取openid,获取用户信息,获取全局票据等
* Author yoby 微信logove qq280594236
* 
*2015-1-21 更新
*/


class jssdk
{
private $appid;
private $url;
private $appsn;
function __construct($appid,$secret,$isjie=0,$urlx=''){
   $this->appid=$appid;
   $this->appsn=$secret;
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
     //$mem=memcache_init();//此处是sae写法
    //$ticket =memcache_get($mem,'ticket');
     //if (empty($ticket)){
           $accessToken = $this->get_access_token();
		  

      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$accessToken;
             $content2 = ihttp_request($url);
			$result = json_decode($content2['content'], true);
            $ticket = $result['ticket'];
         
           

    return $ticket;
  } 
public function get_access_token(){
          load()->func('communication');
             $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsn;
             $content2 = ihttp_request($url);
            //$result =$this->get_curl($url);
			
            //print_r($url);
			
			$info = json_decode($content2['content'], true);
			$access_token = $info['access_token'];
           
    return $access_token;
} 
public function get_sign() {
    $jsapi_ticket = $this->get_jsapi_ticket();
    $url = $this->url;
    $timestamp = time();
    $nonceStr =$this->get_randstr();
    $string = "jsapi_ticket=".$jsapi_ticket."&noncestr=".$nonceStr."&timestamp=".$timestamp."&url=".$url;

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
    public  function get_code($redirect_uri, $scope='snsapi_base',$state=1){//snsapi_userinfo
        if($redirect_uri[0] == '/'){
            $redirect_uri = substr($redirect_uri, 1);
        }
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