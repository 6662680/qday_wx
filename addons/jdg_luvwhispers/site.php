<?php
/**
 * 情话模块微站定义
 *
 * @author on3
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

require 'mydata.class.php';
require_once 'emoji.php';

class Jdg_luvwhispersModuleSite extends WeModuleSite {

	public $table_fans = 'lw_fans';
	public $table_comments = 'lw_comments';
	public $table_commentslike = 'lw_commentslike';
	public $table_report = 'lw_report';

	function __construct(){
        load()->func('pdo');
	}

	public function doWebUserlist(){
		global $_W,$_GPC;
		$pageIndex = max(1, intval($_GPC['page']));
		$pageSize = 50;
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_fans)." WHERE `uniacid`=:uniacid  ",array(':uniacid'=>$_W['uniacid']));
		if($_GPC['foo']=='change'){
			pdo_update($this->table_fans,array('isblack'=>$_GPC['doit']),array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
			message('哎呦,状态修改成功了哦',referer(),'success');
		}
		$list = pdo_fetchall('SELECT * FROM'.tablename($this->table_fans)." WHERE uniacid = :uniacid GROUP BY createtime DESC LIMIT " . ($pageIndex - 1) * $pageSize . ',' . $pageSize,array(':uniacid'=>$_W['uniacid']));
		$pager = pagination($total, $pageIndex, $pageSize);
		include $this->template('userlist');
	}

	public function doWebReportlist(){
		global $_W,$_GPC;
		$pageIndex = max(1, intval($_GPC['page']));
		$pageSize = 50;
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_report)." WHERE `uniacid`=:uniacid  ",array(':uniacid'=>$_W['uniacid']));
		$list = pdo_fetchall("SELECT t1.*,content,isok FROM ".tablename($this->table_report)." AS t1 LEFT JOIN ".tablename($this->table_comments)." AS t2 ON t1.swnoId = t2.id WHERE t1.uniacid=:uniacid GROUP BY t1.createtime DESC LIMIT " . ($pageIndex - 1) * $pageSize . ',' . $pageSize,array(':uniacid'=>$_W['uniacid']));
		$pager = pagination($total, $pageIndex, $pageSize);
		include $this->template('reportlist');
	}

	public function doWebTalklist(){
		global $_W,$_GPC;
		$pageIndex = max(1, intval($_GPC['page']));
		$pageSize = 50;
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_comments)." WHERE `uniacid`=:uniacid  ",array(':uniacid'=>$_W['uniacid']));
		if($_GPC['foo']=='change'){
			if(empty($_GPC['id'])){
				message('缺失重要的参数','','error');
			}
			pdo_update($this->table_comments,array('isok'=>$_GPC['doit']),array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
			message('哎呦,状态修改成功了哦',referer(),'success');
		}
		if($_GPC['foo']=='delete'){
			if(empty($_GPC['id'])){
				message('缺失重要的参数','','error');
			}
			pdo_delete($this->table_comments,array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
			message('哎呦,删除了哦..',referer(),'success');
		}
		$list = pdo_fetchall('SELECT * FROM'.tablename($this->table_comments)." WHERE uniacid = :uniacid GROUP BY createtime DESC LIMIT " . ($pageIndex - 1) * $pageSize . ',' . $pageSize,array(':uniacid'=>$_W['uniacid']));
		foreach ($list as $key => $value) {
			$temp = pdo_fetch('SELECT * FROM'.tablename($this->table_fans)." WHERE uniacid = :uniacid AND openid = :openid",array(':uniacid'=>$_W['uniacid'],':openid'=>$value['openid']));
			$list[$key]['nickname'] = $temp['nickname'];
		}
		$pager = pagination($total, $pageIndex, $pageSize);
		include $this->template('talklist');
	}

	public function doMobileShowdetail() {
		global $_W,$_GPC;
		$this->checkOpenid();
		$swnoId = $_GPC['swnoId'];
		if(!empty($swnoId)){
			$userId = pdo_fetchcolumn('SELECT openid FROM'.tablename($this->table_comments)." WHERE id = :id AND uniacid = :uniacid",array(':uniacid'=>$_W['uniacid'],':id'=>$swnoId));
		}
		$config = $this->module['config'];
		$config['ischeck'] = $config['ischeck']?$config['ischeck']:0;
		$like = $this->createMobileUrl('like',array('foo'=>'like'));
		$unlike = $this->createMobileUrl('like',array('foo'=>'unlike'));
		$report_url = $this->createMobileUrl('report');
		if($config['ischeck']==0){
				$condition = "";
			}else{
				$condition = "AND isok =1";
			}
		if(empty($swnoId)){
			exit(json_encode(array('retCode'=>0)));
		}
		$ajax_url =$this->createMobileUrl('Showdetail',array('foo'=>'showit','swnoId'=>$swnoId));
		$myajax_url =$this->createMobileUrl('Showdetail',array('foo'=>'showchildit','swnoId'=>$swnoId));
		$addajax_url =$this->createMobileUrl('Showdetail',array('foo'=>'addit','swnoId'=>$swnoId));
		if($_GPC['foo']=='showit'){
			$list = pdo_fetch('SELECT t1.id,parentid,t1.openid,toUser,content,nickname,headimgurl,nowColor,t1.limit,t1.uniacid FROM'.tablename($this->table_comments)." AS t1 JOIN ".tablename($this->table_fans)." AS t2 ON t1.openid = t2.openid WHERE t1.uniacid = :uniacid AND t1.id = :id AND parentid=0",array(':uniacid'=>$_W['uniacid'],':id'=>$swnoId));
			$result[] = $this->packageInfo($list);
			$datas = new Mydata();
		$datas->result = $result;
		$data = array('retCode'=>200,'message'=>'','value'=>$datas,'accessToken'=>'','executeTime'=>0,'weixinMessageType'=>'','picUrl'=>null,'controllerReturn'=>0,'finished'=>false);
		$data = json_encode($data);
		exit($data);
		}
		if($_GPC['foo']=='showchildit'){
			$list = pdo_fetchall('SELECT t1.id,parentid,t1.openid,toUser,content,nickname,headimgurl,nowColor,t1.limit,t1.uniacid,t1.createtime FROM'.tablename($this->table_comments)." AS t1 JOIN ".tablename($this->table_fans)." AS t2 ON t1.openid = t2.openid WHERE t1.uniacid = :uniacid AND t2.isblack !=1 $condition AND parentid = :parentid",array(':uniacid'=>$_W['uniacid'],':parentid'=>$swnoId));
			$result = array();
		foreach ($list as $key => $value) {
            $user = pdo_fetch('SELECT * FROM'.tablename($this->table_fans)." WHERE uniacid = :uniacid AND openid = :openid",array(':uniacid'=>$_W['uniacid'],':openid'=>$value['openid']));
			$result[] =array('swnocoId'=>$value['id'],'swnocoPic'=>$user['headimgurl'],'swnoId'=>$value['parentid'],'swnocoContent'=>$value['content'],'statusCode'=>0,'createTime'=>date('Y/m/d H:i:s',$value['createtime']),'lastUpdTime'=>date('Y/m/d H:i:s',$value['createtime']));
		}
		$data = array('retCode'=>"200",'message'=>'','value'=>$result,'accessToken'=>'','executeTime'=>0,'weixinMessageType'=>'','picUrl'=>null,'controllerReturn'=>0,'finished'=>false);
		$data = json_encode($data);
		exit($data);
		}
		if($_GPC['foo']=='addit'){
			$data=$_GPC['data'];
			$openid = pdo_fetchcolumn('SELECT openid FROM'.tablename($this->table_comments)." WHERE uniacid = :uniacid AND id = :id",array(':uniacid'=>$_W['uniacid'],':id'=>$data['swnoId']));
			$user = $this->getUserInfo($openid);
			$insert['uniacid'] = $_W['uniacid'];
			$insert['createtime'] = TIMESTAMP;
			$insert['openid'] = $_W['fans']['from_user'];
			$insert['parentid'] = $data['swnoId'];
			$insert['content'] = emoji_html_to_unified($data['swnocoContent']);
			$insert['limit'] = 0;
			$insert['isok']=1;
			$insert['nowColor'] = " ";
			$insert['toUser']=$user['nickname'];
			pdo_insert($this->table_comments,$insert);
			$id = pdo_insertid();
			if($id==0){
				exit(json_encode(array('retCode'=>0,'message'=>'哎呦,系统正忙..请稍后再试...')));
			}else{
				exit(json_encode(array('retCode'=>200)));
			}
		}
		include $this->template('detail');
	}


	public function doMobileReport(){
		global $_W,$_GPC;
		$userId = $_GPC['data']['userId'];
		$swnoId = $_GPC['data']['swnoId'];
		if(empty($userId)||empty($_W['fans']['from_user'])){
			exit(json_encode(array('retCode'=>0)));
		}
		if($userId==$_W['fans']['from_user']){
			exit(json_encode(array('retCode'=>1)));
		}
  		$data = pdo_fetch('SELECT * FROM'.tablename($this->table_report)." WHERE uniacid = :uniacid AND swnoId = :swnoId AND reporter = :reporter",array(':uniacid'=>$_W['uniacid'],':swnoId'=>$swnoId,':reporter'=>$_W['fans']['from_user']));
  		if(!empty($data)){
  			exit(json_encode(array('retCode'=>2)));
  		}
  		pdo_insert($this->table_report,array('uniacid'=>$_W['uniacid'],'swnoId'=>$swnoId,'openid'=>$userId,'reporter'=>$_W['fans']['from_user'],'createtime'=>TIMESTAMP));
  		$id = pdo_insertid();
  		if($id == 0){
  			exit(json_encode(array('retCode'=>3)));
  		}else{
  			exit(json_encode(array('retCode'=>200)));
  		}
	}


	public function doMobileMyindex() {
		global $_W,$_GPC;
		$this->checkOpenid();
		$myurl = $this->createMobileUrl('myindex',array('foo'=>'showall'));
		$like = $this->createMobileUrl('like',array('foo'=>'like'));
		$unlike = $this->createMobileUrl('like',array('foo'=>'unlike'));
		$report_url = $this->createMobileUrl('report');
		$config = $this->module['config'];
		$config['ischeck'] = $config['ischeck']?$config['ischeck']:0;
		$key = $_GPC['key'];
		if($config['ischeck']==0){
				$condition = "";
			}else{
				$condition = "AND isok =1";
			}
		if($_GPC['foo']=='showall'){
			$toUserName = $_GPC['data']['toUser'];
			$pageNum = intval($_GPC['data']['pageNum']);
			$pageSize = intval($_GPC['data']['pageSize']);
			if($_GPC['data']['limit']=='yes'){
				$pageNum =1;
				$pageSize = 5;
				$totalpage = 1;
				$list = pdo_fetchall('SELECT t1.*,(2*count(t2.id)+sumlike) AS sum,t3.nickname FROM (SELECT a1.*,count(a2.id) AS sumlike FROM '.tablename($this->table_comments)." AS a1 LEFT JOIN ".tablename($this->table_commentslike)." AS a2 ON a1.id = a2.swnoId WHERE a1.uniacid =".$_W['uniacid']." AND a1.parentid = 0 GROUP BY a1.id) AS t1 LEFT JOIN ".tablename($this->table_comments)." AS t2 ON t1.id = t2.parentid JOIN ".tablename($this->table_fans)."AS t3 ON t1.openid = t3.openid WHERE t1.uniacid = :uniacid AND t1.parentid=0 GROUP BY t1.id ORDER BY sum DESC LIMIT " . ($pageNum - 1) * $pageSize . ',' . $pageSize,array(':uniacid'=>$_W['uniacid']));
			}else if(!empty($toUserName)){
				$totalpage = pdo_fetchcolumn('SELECT COUNT(*) AS sum FROM'.tablename($this->table_comments)." WHERE uniacid = :uniacid AND parentid = 0 AND toUser like '%".$toUserName."%'",array(':uniacid'=>$_W['uniacid']));
				$list = pdo_fetchall('SELECT t1.id,parentid,t1.openid,toUser,content,nickname,headimgurl,nowColor,t1.limit,t1.uniacid FROM'.tablename($this->table_comments)." AS t1 JOIN ".tablename($this->table_fans)." AS t2 ON t1.openid = t2.openid WHERE t1.uniacid = :uniacid AND t2.isblack !=1 $condition AND parentid=0 AND toUser like '%".$toUserName."%' ORDER BY t1.createtime DESC",array(':uniacid'=>$_W['uniacid']));
			}else{
				$totalpage = pdo_fetchcolumn('SELECT COUNT(*) AS sum FROM'.tablename($this->table_comments)." WHERE uniacid = :uniacid AND parentid = 0",array(':uniacid'=>$_W['uniacid']));
				$list = pdo_fetchall('SELECT t1.id,parentid,t1.openid,toUser,content,nickname,headimgurl,nowColor,t1.limit,t1.uniacid FROM'.tablename($this->table_comments)." AS t1 JOIN ".tablename($this->table_fans)." AS t2 ON t1.openid = t2.openid WHERE t1.uniacid = :uniacid AND t2.isblack !=1 $condition AND parentid=0 ORDER BY t1.createtime DESC LIMIT " . ($pageNum - 1) * $pageSize . ',' . $pageSize,array(':uniacid'=>$_W['uniacid']));
			}
			$result = array();
		foreach ($list as $key => $value) {
			$result[] = $this->packageInfo($value);
		}
		$datas = new Mydata();
		$datas->result = $result;
		$datas->pageSize= $pageSize;
		$datas->pageNum = $pageNum;
		$datas->pageAmount = round(($totalpage+$pageSize-1)/$pageSize);
		$data = array('retCode'=>200,'message'=>'','value'=>$datas,'accessToken'=>'','executeTime'=>0,'weixinMessageType'=>'','picUrl'=>null,'controllerReturn'=>0,'finished'=>false);
		$data = json_encode($data);
		exit($data);
		}
		include $this->template('qingquan');
	}

	public function doMobileSearch() {
		global $_W,$_GPC;
		$this->checkOpenid();
		$search_url = $this->createMobileUrl('Searchnum');
		include $this->template('search');
	}

	public function doMobileSearchnum() {
		global $_W,$_GPC;
		$this->checkOpenid();
		$search_urlnum = $this->createMobileUrl('Searchnum',array('foo'=>'searchit'));
		if($_GPC['foo']=='searchit'){
			$toUser = $_GPC['data']['toUser'];
			$all = pdo_fetchcolumn('SELECT COUNT(*) AS sum FROM'.tablename($this->table_comments)." WHERE uniacid = :uniacid AND toUser like '%".$toUser."%'",array(':uniacid'=>$_W['uniacid']));
			exit(json_encode(array('retCode'=>200,'message'=>'','value'=>array('collegeNum'=>$all,'collegesNum'=>$all,'toUser'=>$toUser,'univName'=>''),'accessToken'=>'','executeTime'=>0,'weixinMessageType'=>'','picUrl'=>null,'controllerReturn'=>0,'finished'=>false)));
		}
		include $this->template('searchnum');
	}

	public function doMobileAdd() {
		global $_W,$_GPC;
		$this->checkOpenid();
		$ajax_url =$this->createMobileUrl('add',array('foo'=>'addit'));
		$nexturl = $this->createMobileUrl('myindex');
		if($_GPC['foo']=='addit'){
			$data=$_GPC['data'];
			$data['content'] = emoji_html_to_unified($data['content']);
			$data['toUser'] = emoji_html_to_unified($data['toUser']);
			$data['uniacid'] = $_W['uniacid'];
			$data['createtime'] = TIMESTAMP;
			$data['openid'] = $_W['fans']['from_user'];
			pdo_insert($this->table_comments,$data);
			$id = pdo_insertid();
			if($id==0){
				exit(json_encode(array('retCode'=>0,'message'=>'哎呦,系统正忙..请稍后再试...')));
			}else{
				exit(json_encode(array('retCode'=>200)));
			}
		}
		include $this->template('add');
	}

	public function doMobileLike() {
		global $_W,$_GPC;
		$swnoId = $_GPC['data']['swnoId'];
		if(empty($swnoId)){
			exit(json_encode(array('message'=>'重要参数缺失..')));
		}
		if($_GPC['foo']=='like'){
			pdo_insert($this->table_commentslike,array('uniacid'=>$_W['uniacid'],'openid'=>$_W['fans']['from_user'],'createtime'=>TIMESTAMP,'swnoId'=>$swnoId));
			exit(json_encode(array('retCode'=>200)));
		}
		if($_GPC['foo']=='unlike'){
			pdo_delete($this->table_commentslike,array('uniacid'=>$_W['uniacid'],'openid'=>$_W['fans']['from_user'],'swnoId'=>$swnoId));
			exit(json_encode(array('retCode'=>200)));
		}
	}


	public function doMobileMy() {
		global $_W,$_GPC;
		$this->checkOpenid();
		$ajax_url_my = $this->createMobileUrl('my',array('foo'=>'showme'));
		$config = $this->module['config'];
		$config['ischeck'] = $config['ischeck']?$config['ischeck']:0;
		$like = $this->createMobileUrl('like',array('foo'=>'like'));
		$unlike = $this->createMobileUrl('like',array('foo'=>'unlike'));
		if($config['ischeck']==0){
				$condition = "";
			}else{
				$condition = "AND isok =1";
			}
		if($_GPC['foo']=='showme'){
			$pageNum = intval($_GPC['data']['pageNum']);
			$pageSize = intval($_GPC['data']['pageSize']);
			$totalpage = pdo_fetchcolumn('SELECT COUNT(*) AS sum FROM'.tablename($this->table_comments)." WHERE uniacid = :uniacid AND parentid = 0",array(':uniacid'=>$_W['uniacid']));
			$list = pdo_fetchall('SELECT t1.id,parentid,t1.openid,toUser,content,nickname,headimgurl,nowColor,t1.limit,t1.uniacid FROM'.tablename($this->table_comments)." AS t1 JOIN ".tablename($this->table_fans)." AS t2 ON t1.openid = t2.openid WHERE t1.uniacid = :uniacid AND t2.isblack !=1 $condition AND parentid=0 AND t1.openid = :openid ORDER BY t1.createtime DESC LIMIT " . ($pageNum - 1) * $pageSize . ',' . $pageSize,array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['fans']['from_user']));
			$relist = pdo_fetchall('SELECT t1.id,parentid,t1.openid,toUser,content,nickname,headimgurl,nowColor,t1.limit,t1.uniacid FROM'.tablename($this->table_comments)." AS t1 JOIN ".tablename($this->table_fans)." AS t2 ON t1.openid = t2.openid WHERE t1.uniacid = :uniacid AND t2.isblack !=1 $condition AND parentid!=0 AND t1.openid = :openid ORDER BY t1.createtime DESC LIMIT " . ($pageNum - 1) * $pageSize . ',' . $pageSize,array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['fans']['from_user']));
			foreach ($relist as $key => $value) {
				$temp = pdo_fetch('SELECT t1.id,parentid,t1.openid,toUser,content,nickname,headimgurl,nowColor,t1.limit,t1.uniacid FROM'.tablename($this->table_comments)." AS t1 JOIN ".tablename($this->table_fans)." AS t2 ON t1.openid = t2.openid WHERE t1.uniacid = :uniacid AND t2.isblack !=1 $condition AND t1.id = :id AND parentid = 0 ORDER BY t1.createtime DESC LIMIT " . ($pageNum - 1) * $pageSize . ',' . $pageSize,array(':uniacid'=>$_W['uniacid'],":id"=>$value['parentid']));
				if(!in_array($temp, $list)&&!empty($temp)){
					$list[] = $temp;
				}
			}
			$result = array();
		foreach ($list as $key => $value) {
			$result[] = $this->packageInfo($value);
		}
		$datas = new Mydata();
		$datas->result = $result;
		$datas->pageSize= $pageSize;
		$datas->pageNum = $pageNum;
		$datas->pageAmount = round(($totalpage+$pageSize-1)/$pageSize);
		$data = array('retCode'=>200,'message'=>'','value'=>$datas,'accessToken'=>'','executeTime'=>0,'weixinMessageType'=>'','picUrl'=>null,'controllerReturn'=>0,'finished'=>false);
		$data = json_encode($data);
		exit($data);
		}
		include $this->template('my');
	}

	private function getUserInfo($o){
		global $_W,$_GPC;
		$user='';
		load()->model('account');
		load()->func('communication');
		if(empty($o)){
			message('重要参数丢失..','','error');
			exit();
		}
		if(empty($_W['account']['key'])||empty($_W['account']['secret'])){
			return $user;
		}else{
			//$access_token = account_weixin_token($_W['account']);
			 load()->classs('weixin.account');
			 $access_token = WeixinAccount::create($_W['uniacid'])->fetch_token();

			$content = ihttp_get(sprintf('https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN',$access_token,$o));
			if($content['code']!=200){//网络异常..
				//message('抱歉网络不稳..');
				return $user;
			}else{
				$record = @json_decode($content['content'], true);
				if($record['errcode']!=0){//各种传参错误造成的不能拉取用户信息直接返回fans表用户信息
					//message('拉取失败..');//调试用查看报错提示
					return $user;
				}
			}
			$record = @json_decode($content['content'], true);
			$user = $record;
			unset($record);
			$user['nickname'] = emoji_html_to_unified($user['nickname']);
			return $user;
		}
	}

	private function packageInfo($o){
		global $_W;
		if($o['limit']==1){
				$myname = '某人('.substr($o['openid'], -4).")";
			}else{
				$myname =$o['nickname'];
			}
			$islike = pdo_fetch('SELECT * FROM'.tablename($this->table_commentslike)." WHERE uniacid = :uniacid AND openid = :openid AND swnoId = :swnoId",array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['fans']['from_user'],':swnoId'=>$o['id']));
			$islike = $islike?true:false;
			$likesum = pdo_fetchcolumn('SELECT COUNT(*) FROM'.tablename($this->table_commentslike)." WHERE uniacid = :uniacid AND swnoId = :swnoId",array(':uniacid'=>$_W['uniacid'],':swnoId'=>$o['id']));
			$likesum = $likesum?$likesum:null;
			$replysum = pdo_fetchcolumn('SELECT COUNT(*) AS sum FROM'.tablename($this->table_comments)." WHERE uniacid = :uniacid AND parentid = :parentid",array(':uniacid'=>$_W['uniacid'],':parentid'=>$o['id']));
			$replysum  = $replysum?$replysum:0;
			return array('swnoId'=>$o['id'],
				'toUser'=>emoji_unified_to_html($o['toUser']),
				'userId'=>$o['openid'],
				'userName'=>$o['nickname'],
				'fromUserName'=>$o['nickname'],
				'univId'=>0,
				'univName'=>$myname,
				'content'=>emoji_unified_to_html($o['content']),
				'limit'=>$o['limit'],
				'nowColor'=>$o['nowColor'],
				'statusCode'=>0,
				'swnoScore'=>0,
				'createTime'=>$o['createtime'],
				'lastUpdTime'=>$o['createtime'],
				'praiseNum'=>$likesum,
				'commentNum'=>$replysum,
				'stickie'=>0,//置顶
				'praised'=>$islike,
				'uniacid'=>$o['uniacid']);
	}

	private function checkOpenid(){
		global $_W,$_GPC;
		$openid = $_W['openid'];
		$config = $this->module['config'];
		if(!empty($config['url'])){
			if(!strexists($config['url'],'http')){
				$config['url'] = 'http://'.$config['url'];
			}
		}
       	if(empty($openid)){
        	if(!empty($config['url'])){
        		message('..请先关注'.$_W['account']['name'].'才能继续访问..',$config['url'],'error');
        	}
        	message('..请先关注'.$_W['account']['name'].'才能继续访问..',url('home',array('i'=>$_W['uniacid'])),'error');
		}
		load()->model('mc');//手机端用户的必要加载项
		$fans = mc_fansinfo($openid);
		$profile = mc_fetch($_W['member']['uid']);
		if($fans['follow']!=1){
			if(empty($config['url'])){
				message('..请先关注'.$_W['account']['name'].'才能继续访问..',url('home',array('i'=>$_W['uniacid'])),'error');
			}
				message('..请先关注'.$_W['account']['name'].'才能继续访问..',$config['url'],'error');
		}
		$record = pdo_fetch('SELECT * FROM'.tablename($this->table_fans)." WHERE uniacid = :uniacid AND openid = :openid",array(':uniacid'=>$_W['uniacid'],':openid'=>$openid));
		if(empty($record)){
			$user = $this->getUserInfo($openid);
			if(empty($user)){
				if(empty($profile['nickname'])){
					$user['nickname'] = '匿名'.substr($openid, -4);
				}else{
					$user['nickname'] = $profile['nickname'];
				}
			if(empty($profile['avatar'])){
				$user['headimgurl'] = $_W['siteroot'].'addons/luvwhispers/template/style/images/noheader.png';
			}else{
				$user['headimgurl'] = $profile['avatar'];
			}
		}
			pdo_insert($this->table_fans,array('uniacid'=>$_W['uniacid'],'nickname'=>$user['nickname'],'headimgurl'=>$user['headimgurl'],'openid'=>$openid,'createtime'=>TIMESTAMP));
		}else{
			if($record['isblack']==1){
				message('哎呦,已被管理员封禁了呦..',url('home',array('i'=>$_W['uniacid'])),'error');
			}
			if (TIMESTAMP-$record['createtime']>=604800) {
				$user = $this->getUserInfo($openid);
				if(!empty($user['nickname'])||empty($user['headimgurl'])){
					pdo_update($this->table_fans,array('nickname'=>$user['nickname'],'headimgurl'=>$user['headimgurl'],'createtime'=>TIMESTAMP),array('uniacid'=>$_W['uniacid'],'openid'=>$openid));
				}
			}
		}
	}
}