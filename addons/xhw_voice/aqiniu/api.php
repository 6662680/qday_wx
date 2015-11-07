<?php
header ( "Content-type:text/html;charset=utf-8" );
define ( 'IN_PHP', true );
define ( 'IN_IA' , true );
require_once("qiniu/http.php");
require_once("qiniu/auth_digest.php");
require_once("qiniu/utils.php");
require_once("qiniu/mysql.class.php");
require_once("../data/config.php");
//require_once("qiniu/rs.php");

$db = new db_mysql ( $config['db']['host'].":".$config['db']['port'], $config['db']['username'] , $config['db']['password'] , $config['db']['database'] , $config['db']['charset'] );
//$db = new db_mysql ( 'localhost:3306', 'root' , 'mima' , 'w9' , 'utf8' );

$ty_conf = $db->getone("select * from  ims_xhw_voice_setting where weid=".$_POST['weid']);

$accessKey = $_POST['accesskey'];
$secretKey = $_POST['secretkey'];

$access_tonken = $_POST['access_token'];
$media_id = $_POST['mediaid'];
$targetUrl = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_tonken."&media_id=".$media_id;

$times = time();

$destBucket = $_POST['name'];
$destKey = $_POST['title'].$times.".amr";


$encodedUrl = Qiniu_Encode($targetUrl);

$destEntry = "$destBucket:$destKey";
$encodedEntry = Qiniu_Encode($destEntry);

$apiHost = "http://iovip.qbox.me";
$apiPath = "/fetch/$encodedUrl/to/$encodedEntry";
$requestBody = "";

$mac = new Qiniu_Mac($accessKey, $secretKey);
$client = new Qiniu_MacHttpClient($mac);

list($ret, $err) = Qiniu_Client_CallWithForm($client, $apiHost . $apiPath, $requestBody);
if ($err !== null) {
    echo "failed\n";
    var_dump($err);
} else {
    $bucket = $destBucket;
	$key= $destKey;
	Qiniu_SetKeys($accessKey,$secretKey);
	$entry = Qiniu_Encode($_POST['name'].':'.$_POST['title'].$times.'.mp3');
	$url = "avthumb/mp3|saveas/$entry";
	$fops=$url;

	$notifyURL = "";
	$force = 0;
	$encodedBucket = urlencode($bucket);
	$encodedKey = urlencode($key);
	$encodedFops = urlencode($fops);
	$encodedNotifyURL = urlencode($notifyURL);
	$apiHost = "http://api.qiniu.com";
	$apiPath = "/pfop/";
	$requestBody = "bucket=$encodedBucket&key=$encodedKey&fops=$encodedFops&notifyURL=$encodedNotifyURL";
	if ($force !== 0) {
	    $requestBody .= "&force=1";
	}
	$mac = new Qiniu_Mac($accessKey, $secretKey);
	$client = new Qiniu_MacHttpClient($mac);
	list($ret, $err) = Qiniu_Client_CallWithForm($client, $apiHost . $apiPath, $requestBody);
	if ($err !== null) {
	    echo "failed\n";
	    var_dump($err);
	} else {
		echo "success\n";
		var_dump($ret);
		$arr = array('mp3'=>"http://".$ty_conf['link']."/".$_POST['title'].$times.".mp3");
		$db->update('ims_xhw_voice_reg',$arr,"id=".$_POST['id']);
			echo "<script language='javascript'>
			location.href='/web/index.php?c=site&a=entry&id=".$_POST['id']."&rid=".$_POST['rid']."&do=post&m=xhw_voice';
			</script>";
		//Qiniu_RS_Delete($_POST['name'],$_POST['title'].".amr");
	}
}