<?php
/**
 * 微餐饮模块微站定义
 */
defined('IN_IA') or exit('Access Denied');
class jufeng_wcyModuleSite extends WeModuleSite {

	//----------------------------------------------------------------------------------------------------------------------------------------------
public function sendmail($_title='',$_content='',$_tomail="",$_Host="",$_Username="",$_Password=""){
		global $_W, $_GPC;
		if(trim($_Host)=="smtp.qq.com"){
			$_Host="ssl://smtp.qq.com";
			$_Port = 465;
			$_Authmode= 1;			
		}else{
			$_Port = 25;
		}
		if ($_Authmode==1) {
			if (!extension_loaded('openssl')) {
				return '请开启 php_openssl 扩展！';
			}
		}
		include_once 'class/class.phpmailer.php';
		try {
			$mail = new PHPMailer(true); //New instance, with exceptions enabled
			$body			  =$_content;
			$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

			$mail->IsSMTP();       
			$mail->Charset='UTF-8';			// tell the class to use SMTP
			$mail->SMTPAuth   = true;                  // enable SMTP authentication
			$mail->Port       = $_Port;                    // set the SMTP server port
			$mail->Host       = $_Host; // SMTP server
			$mail->Username   = $_Username;     // SMTP server username
			$mail->Password   = $_Password;            // SMTP server password
			if($_Authmode==1){
				$mailer->SMTPSecure = 'ssl';
			}
			//$mail->IsSendmail();  // tell the class to use Sendmail
			$mail->AddReplyTo($_Username,"First Last");
			$mail->From       = $_Username;
			$mail->FromName   = $_W['account']['name']."-微信订餐".date('m-d H:i');
			$to = $_tomail;
			$mail->AddAddress($to);
			$mail->Subject  = $_title;
			$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
			$mail->WordWrap   = 80; // set word wrap
			$mail->MsgHTML($body);
			$mail->IsHTML(true); // send as HTML
			$mail->Send();
			return true;
		} catch (phpmailerException $e) {
			return $e->errorMessage();
		}
	}	
public function sendSelfFormatOrderInfo($device_no,$key,$times,$orderInfo){
		$selfMessage = array(
			'clientCode'=>$device_no,  
			'printInfo'=>$orderInfo,
			'apitype'=>'php',
			'key'=>$key,
		    'printTimes'=>$times
		);
			return $this->sendSelfFormatMessage($selfMessage);
}
public function sendSelfFormatMessage($msgInfo){
	$client = new HttpClient(FEIE_HOST,FEIE_PORT);
	if(!$client->post('/FeieServer/printSelfFormatOrder',$msgInfo)){ //提交失败
		return 'faild';
	}
	else{
		return $client->getContent();
	}
}
public function queryOrderNumbersByTime($device_no,$date){
		$msgInfo = array(
	        'clientCode'=>$device_no,
		    'date'=>$date
		);
	$client = new HttpClient(FEIE_HOST,FEIE_PORT);
	if(!$client->post('/FeieServer/queryorderinfo',$msgInfo)){ //提交失败
		return 'faild';
	}
	else{
		$result = $client->getContent();
		return $result;
	}
}
public function queryPrinterStatus($device_no){
	$client = new HttpClient(FEIE_HOST,FEIE_PORT);
	if(!$client->get('/FeieServer/queryprinterstatus?clientCode='.$device_no)){ //请求失败
		return 'faild';
	}
	else{
		$result = $client->getContent();
		return $result;
	}
}
public function sendSMS($uid,$pwd,$mobile,$content,$time='',$mid='')
{
 $http = 'http://sms.shwsms.com/httpInterfaceSubmitAction.do';
 $data = array
  (
  'account'=>$uid,     //用户账号
  'password'=>strtolower(md5($pwd)), //MD5位32密码
  'mobile'=>$mobile,    //号码
  'content'=>$content,   //内容
  'time'=>$time,  //定时发送
  'mid'=>$mid      //子扩展号
  );
 $re= $this->postSMS($http,$data);   //POST方式提交
 if($re == '111' )
 {
  return "发送成功!";
 }
 else 
 {
  return "发送失败! 状态：".$re;
 }
}
public function postSMS($url,$data='')
{
 $row = parse_url($url);
 $host = $row['host'];
 $port = $row['port'] ? $row['port']:80;
 $file = $row['path'];
 while (list($k,$v) = each($data)) 
 {
  $post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
 }
 $post = substr( $post , 0 , -1 );
 $len = strlen($post);
 $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
 if (!$fp) {
  return "$errstr ($errno)\n";
 } else {
  $receive = '';
  $out = "POST $file HTTP/1.1\r\n";
  $out .= "Host: $host\r\n";
  $out .= "Content-type: application/x-www-form-urlencoded\r\n";
  $out .= "Connection: Close\r\n";
  $out .= "Content-Length: $len\r\n\r\n";
  $out .= $post;  
  fwrite($fp, $out);
  while (!feof($fp)) {
   $receive .= fgets($fp, 128);
  }
  fclose($fp);
  $receive = explode("\r\n\r\n",$receive);
  unset($receive[0]);
  return implode("",$receive);
 }
}
	public function doWebCategory() {
	include_once 'site/webcategory.php';
	}
	public function doWebShoptype() {
	include_once 'site/webshoptype.php';
	}
	public function doWebSettings() {
	include_once 'site/websettings.php';
		} 
	public function doWebFoods() {
	include_once 'site/webfoods.php';
	}
	public function doWebOrder() {
	include_once 'site/weborder.php';
	}
	public function doWebPrint() {
	include_once 'site/print.php';
	}
	public function doMobileUpdateCart() {
		include_once 'site/mobileupdatecart.php';
	}
	public function doMobileMyCart() {
		include_once 'site/mobilemycart.php';
	}
	public function payResult($params) {
		include_once 'site/payresult.php';
	}
    public function doMobiledianjia() {
	include_once 'site/mobiledianjia.php';
	}
	public function doMobilelist() {
    include_once 'site/mobilelist.php';
	}
	public function doMobilePay() {
	include_once 'site/mobilepay.php';
	}
	public function doMobileMyOrder() {
	include_once 'site/mobilemyorder.php';
	}
	public function doMobileClear() {
		global $_W, $_GPC;
		pdo_delete('jufeng_wcy_cart', array('weid' => $_W['uniacid'], 'from_user' => $_W['fans']['from_user']));
		message('清空菜单成功。', $this->createMobileUrl('list',array('pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate'])), 'success');
	}
	public function doMobileDetail() {
		global $_W, $_GPC;
			$foodsid = intval($_GPC['id']);
		$foods = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE id = :id", array(':id' => $foodsid));
		$foodscart = pdo_fetch("SELECT total FROM ".tablename('jufeng_wcy_cart')." WHERE foodsid = '{$foodsid}' AND from_user = '{$_W['fans']['from_user']}'");
		if (empty($foods)) {
			message('抱歉，菜品不存在或是已经被删除！');
		}
		include $this->template('detail');
	} 	
	public function doMobilelistctr() {
		include_once 'site/listctr.php';
	}
	public function doMobileorderctr() {
		include_once 'site/orderctr.php';
	}
}