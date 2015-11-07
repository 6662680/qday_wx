<?php
/**
 * 微生活模块定义
 *
 */
defined('IN_IA') or exit('Access Denied');
session_start();
class WeiLiveModuleSite extends WeModuleSite {
	
	//首页
	public function doMobileIndex(){
		$this->__mobile(__FUNCTION__);
	}
	
	//城市导航
	public function doMobilePosition(){
		$this->__mobile(__FUNCTION__);
	}
	//
	public function doMobileList(){
		$this->__mobile(__FUNCTION__);
	}
	
	//活动
	public function doMobileActivity(){
		$this->__mobile(__FUNCTION__);
	}
	
	//个人中心
	public function doMobileHome(){
		$this->__mobile(__FUNCTION__);
	}
	
	//我的商店
	public function doMobileMyShop(){
		$this->__mobile(__FUNCTION__);
	}
	
	//我的商店
	public function doMobileKill(){
		$this->__mobile(__FUNCTION__);
	}
	
	//兑换
	public function doMobileCredit() {
		$this->__mobile(__FUNCTION__);
	}
	
	//奖品
	public function doMobileAward() {
		$this->__mobile(__FUNCTION__);
	}

	//投诉
	public function doMobileComplain(){
		$this->__mobile(__FUNCTION__);
	}
	
//以下为后台管理＝＝＝＝＝＝＝＝＝＝＝＝＝＝
	
	//分类管理
	public function doWebCategory(){
		$this->__web(__FUNCTION__);
	}
	
	//商家管理
	public function doWebStores(){
		$this->__web(__FUNCTION__);
	}
	
	//活动管理
	public function doWebActivity(){
		$this->__web(__FUNCTION__);
	}
	
	//活动管理
	public function doWebSetting(){
		$this->__web(__FUNCTION__);
	}
	
	//评论管理
	public function doWebComment(){
		$this->__web(__FUNCTION__);
	}
	
	//兑换管理
	public function doWebCredit(){
		$this->__web(__FUNCTION__);
	}
	
	//奖品管理
	public function doWebAward(){
		$this->__web(__FUNCTION__);
	}
	
	//店主管理1
	public function doWebHostManager(){
		$this->__web(__FUNCTION__);
	}
	
	//投诉管理
	public function doWebComplain(){
		$this->__web(__FUNCTION__);
	}
	
	//店主管理2
	public function doWebHost(){
		$this->__web(__FUNCTION__);
	}
	
	//幻灯片管理
	public function doWebSlide() {
		$this->__web(__FUNCTION__);
	}
	
	//粉丝管理
	public function doWebFansManager() {
		$this->__web(__FUNCTION__);
	}
	
	public function __web($f_name){
		global $_W,$_GPC;
		checklogin();
		$weid = $_W['uniacid'];
		load()->func('tpl');
		$op = $operation = $_GPC['op']?$_GPC['op']:'display';
		include_once  'web/'.strtolower(substr($f_name,5)).'.php';
	}
	
	public function __mobile($f_name){
		global $_W,$_GPC;
		$weid = $_W['uniacid'];
		$op = $_GPC['op']?$_GPC['op']:'display';
		$setting = pdo_fetch("SELECT * FROM " . tablename('weilive_setting') . " WHERE weid = :weid ", array(':weid' => $weid));
		if(empty($setting['gzurl'])){
			message('没有设置关注引导链接！或没有填写授权APPID');
		}
		$setting['pwd'] = '';
		$oauth_openid="wsh_openid".$weid;

		if (empty($_COOKIE[$oauth_openid])) {
			//$this->CheckCookie();
		}
		include_once  'mobile/'.strtolower(substr($f_name,8)).'.php';
	}
	
	private function fileUpload($file, $type) {
		global $_W;
		set_time_limit(0);
		$_W['uploadsetting'] = array();
		$_W['uploadsetting']['images']['folder'] = 'image';
		$_W['uploadsetting']['images']['extentions'] = array('jpg', 'png', 'gif');
		$_W['uploadsetting']['images']['limit'] = 50000;
		$result = array();
		$upload = file_upload($file, 'image');
		if (is_error($upload)) {
			message($upload['message'], '', 'ajax');
		}
		$result['url'] = $upload['url'];
		$result['error'] = 0;
		$result['filename'] = $upload['path'];
		return $result;
	}
	
	public function doMobileUploadImage() {
		global $_W;
		load()->func('file');
		if (empty($_FILES['file']['name'])) {
			$result['message'] = '请选择要上传的文件！';
			exit(json_encode($result));
		}

		if ($file = $this->fileUpload($_FILES['file'], 'image')) {
			if ($file['error']) {
				exit('0');
				//exit(json_encode($file));
			}
			$result['url'] = $_W['config']['upload']['attachdir'] . $file['filename'];
			$result['error'] = 0;
			$result['filename'] = $file['filename'];
			exit(json_encode($result));
		}
	}
	
	public function doMobileUserinfo() {
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		//用户不授权返回提示说明
		if ($_GPC['code']=="authdeny"){
		    $url = $_W['siteroot'].$this->createMobileUrl('index', array());
			header("location:$url");
			exit('authdeny');
		}
		//高级接口取未关注用户Openid
		if (isset($_GPC['code'])){
		    //第二步：获得到了OpenID
		    $appid = $_W['account']['key'];
		    $secret = $_W['account']['secret'];
			$serverapp = $_W['account']['level'];
			if ($serverapp!=2) {
				//不给设置
				$cfg = $this->module['config'];
			    $appid = $cfg['appid'];
			    $secret = $cfg['secret'];
			}//借用的
			$state = $_GPC['state'];
			//1为关注用户, 0为未关注用户
			
		    $rid = $_GPC['rid'];
			//查询活动时间
			$code = $_GPC['code'];
		    $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
		    //exit($oauth2_code);
			$content = ihttp_get($oauth2_code);
		    $token = @json_decode($content['content'], true);
			if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
				echo '<h1>获取微信公众号授权'.$code.'失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';
				exit;
			}
		    $from_user = $token['openid'];
			//再次查询是否为关注用户
			//$profile  = fans_search($from_user, array('follow'));
			
			$profile = pdo_fetch("select * from ".tablename('mc_mapping_fans')." where uniacid = ".$_W['uniacid']." and openid = '".$from_user."'");
			
			//关注用户直接获取信息
			if ($profile['follow']==1){
			    $state = 1;
			}else{
				//未关注用户跳转到授权页
				$url = $_W['siteroot'].$this->createMobileUrl('userinfo', array());
				$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";				
				header("location:$oauth2_code");
			}
			//未关注用户和关注用户取全局access_token值的方式不一样
			if ($state==1){
			    $oauth2_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";
			    $content = ihttp_get($oauth2_url);
			    $token_all = @json_decode($content['content'], true);
			    if(empty($token_all) || !is_array($token_all) || empty($token_all['access_token'])) {
				    echo '<h1>获取微信公众号授权失败[无法取得access_token], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';
				    exit;
			    }
				$access_token = $token_all['access_token'];
				$oauth2_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";
			}else{
			    $access_token = $token['access_token'];
				$oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";
			}
			
			//使用全局ACCESS_TOKEN获取OpenID的详细信息			
			$content = ihttp_get($oauth2_url);
			$info = @json_decode($content['content'], true);
			if(empty($info) || !is_array($info) || empty($info['openid'])  || empty($info['nickname']) ) {
				echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！<h1>';
				exit;
			}
			
			if (!empty($info["headimgurl"])) {
				$row['avatar'] = $info["headimgurl"];
			//	$filedata=GrabImage($info['headimgurl']);
			//	file_write($info['avatar'], $filedata);
			} else {
				//$info['headimgurl']='avatar_11.jpg';
			}
			
			if(!empty($profile)){
				$row = array(
					'uniacid' => $_W['uniacid'],
					'nickname'=>$info["nickname"],
					'realname'=>$info["nickname"]
				);
				if($profile['uid']==0){
					pdo_insert('mc_members', $row);
					$uid = pdo_InsertId();
					pdo_update('mc_mapping_fans', array('uid'=>$uid), array('uniacid'=>$profile['uniacid'], 'openid'=>$profile['openid']));
				} else {
					pdo_update('mc_members', $row, array('uid'=>$profile['uid']));	
				}
			} else {
				
			}
			
			setcookie("wsh_openid".$_W['uniacid'], $info['openid'], time()+3600*240);
			$url = $this->createMobileUrl('index');
			//die('<script>location.href = "'.$url.'";</script>');
			header("location:$url");
			exit;
		}else{
			echo '<h1>网页授权域名设置出错!</h1>';
			exit;		
		}
	
	}
	
	private function CheckCookie() {
		global $_W;
		//return ;
		$oauth_openid="wsh_openid".$_W['uniacid'];
		if (empty($_COOKIE[$oauth_openid])) {
			$appid = $_W['account']['key'];
			$secret = $_W['account']['secret'];
			//是否为高级号
			$serverapp = $_W['account']['level'];	
			if ($serverapp!=2) {
				$cfg = $this->module['config'];
				//借用的
				$appid = $cfg['appid'];
			    $secret = $cfg['secret'];
				if(empty($appid) || empty($secret)){
					return ;
				}
			}
			$url = $_W['siteroot'].$this->createMobileUrl('userinfo', array());
			$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";				
			//exit($oauth2_code);
			header("location:$oauth2_code");
			exit;
		}
	}
}
/*
$url=$this->createMobileUrl('index');
			die('<script>location.href = "'.$url.'";</script>');
			header("location:$url");
			exit;
		*/
/**
 * 生成分页数据
 * @param int $currentPage 当前页码
 * @param int $totalCount 总记录数
 * @param string $url 要生成的 url 格式，页码占位符请使用 *，如果未写占位符，系统将自动生成
 * @param int $pageSize 分页大小
 * @return string 分页HTML
 */
function pagination1($tcount, $pindex, $psize = 15, $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => '')) {
	global $_W;
	$pdata = array(
		'tcount' => 0,
		'tpage' => 0,
		'cindex' => 0,
		'findex' => 0,
		'pindex' => 0,
		'nindex' => 0,
		'lindex' => 0,
		'options' => ''
	);
	if($context['ajaxcallback']) {
		$context['isajax'] = true;
	}

	$pdata['tcount'] = $tcount;
	$pdata['tpage'] = ceil($tcount / $psize);
	if($pdata['tpage'] <= 1) {
		return '';
	}
	$cindex = $pindex;
	$cindex = min($cindex, $pdata['tpage']);
	$cindex = max($cindex, 1);
	$pdata['cindex'] = $cindex;
	$pdata['findex'] = 1;
	$pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
	$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
	$pdata['lindex'] = $pdata['tpage'];

	if($context['isajax']) {
		if(!$url) {
			$url = $_W['script_name'] . '?' . http_build_query($_GET);
		}
		$pdata['faa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['findex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['paa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['pindex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['naa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['nindex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['laa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['lindex'] . '\', ' . $context['ajaxcallback'] . ')"';
	} else {
		if($url) {
			$pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
			$pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
			$pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
			$pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
		} else {
			$_GET['page'] = $pdata['findex'];
			$pdata['faa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['pindex'];
			$pdata['paa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['nindex'];
			$pdata['naa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['lindex'];
			$pdata['laa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
		}
	}

	$html = '<div class="pagination pagination-centered"><ul>';
	if($pdata['cindex'] > 1) {
		$html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
		$html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
	}
	//页码算法：前5后4，不足10位补齐
	if(!$context['before'] && $context['before'] != 0) {
		$context['before'] = 5;
	}
	if(!$context['after'] && $context['after'] != 0) {
		$context['after'] = 4;
	}

	if($context['after'] != 0 && $context['before'] != 0) {
		$range = array();
		$range['start'] = max(1, $pdata['cindex'] - $context['before']);
		$range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
		if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
			$range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
			$range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
		}
		for ($i = $range['start']; $i <= $range['end']; $i++) {
			if($context['isajax']) {
				$aa = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $i . '\', ' . $context['ajaxcallback'] . ')"';
			} else {
				if($url) {
					$aa = 'href="?' . str_replace('*', $i, $url) . '"';
				} else {
					$_GET['page'] = $i;
					$aa = 'href="?' . http_build_query($_GET) . '"';
				}
			}
			//$html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
		}
	}

	if($pdata['cindex'] < $pdata['tpage']) {
		$html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
		$html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
	}
	$html .= '</ul></div>';
	return $html;
}

function checkDatetime($str, $format = "H:i"){
	$str_tmp = date('Y-m-d').' '.$str;
	$unixTime = strtotime($str_tmp);
	$checkDate = date($format, $unixTime);
	if ($checkDate == $str) {
		return 1;
	} else {
		return 0;
	}
}

/**
*求两个已知经纬度之间的距离,单位为米
*@param lng1,lng2 经度
*@param lat1,lat2 纬度
*@return float 距离，单位千米
*@author www.phpernote.com
**/
function getDistance($lng1,$lat1,$lng2,$lat2){
    //将角度转为狐度
    $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
    $radLat2=deg2rad($lat2);
    $radLng1=deg2rad($lng1);
    $radLng2=deg2rad($lng2);
    $a=$radLat1-$radLat2;
    $b=$radLng1-$radLng2;
    $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
    return sprintf("%.2f", $s/1000);
}

function haha($hehe){
	$phone = $hehe;
	$mphone = substr($phone,3,4);
	$lphone = str_replace($mphone,"****",$phone);
	return $lphone;
}