<?php

/**
 * 幸运拆礼盒模块定义
 *
 * @author 情天
 */
defined('IN_IA') or exit('Access Denied');
class stonefish_chailiheModuleSite extends WeModuleSite {	
	public $table_reply      = 'stonefish_chailihe_reply';
	public $table_list       = 'stonefish_chailihe_userlist';	
	public $table_data       = 'stonefish_chailihe_data';
	public $table_gift       = 'stonefish_chailihe_gift';
	public $table_giftmika   = 'stonefish_chailihe_giftmika';
	public function doMobilelisthome() {
		//这个操作被定义用来呈现 微站首页导航图标
		$this->doMobilelistentry();	
	}
	public function getTiles($keyword = '') {
		global $_GPC,$_W;

		$weid = $_W['uniacid'];
		$urls = array();
		$list = pdo_fetchall("SELECT name, id FROM ".tablename('rule')." WHERE weid = ".$weid." and module = 'stonefish_chailihe'".(!empty($keyword) ? " AND name LIKE '%{$keyword}%'" : ''));
		if (!empty($list)) {
			foreach ($list as $row) {
			    $reply = pdo_fetch("SELECT title FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $row['id']));
				$urls[] = array('title'=>$reply['title'], 'url'=> $_W['siteroot']."app/".substr($this->createMobileUrl('chailihe', array('rid' => $row['id'])),true),2);
			}
		}
		return $urls;
	}
    //入口列表
	public function doMobilelistentry() {
		global $_GPC,$_W;
		$weid = $_W['uniacid'];
		$time = time();
		$from_user = $_W['fans']['from_user'];
		$page_from_user = base64_encode(authcode($from_user, 'ENCODE'));

		$cover_reply = pdo_fetch("SELECT * FROM ".tablename("cover_reply")." WHERE weid = :weid and module = 'stonefish_chailihe'", array(':weid' => $weid));
		$reply = pdo_fetchall("SELECT * FROM ".tablename($this->table_reply)." WHERE weid = :weid and status = 1 and start_time<".$time."  and end_time>".$time." ORDER BY `end_time` DESC", array(':weid' => $weid));
		foreach ($reply as $mid => $replys) {
			$reply[$mid]['num'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_list)." WHERE weid = :weid and rid = :rid", array(':weid' => $weid, ':rid' => $replys['rid']));
			$reply[$mid]['is'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_list)." WHERE weid = :weid and rid = :rid and from_user = :from_user", array(':weid' => $weid, ':rid' => $replys['rid'], ':from_user' => $from_user));
			$picture = toimage($replys['picture']);
		}

		//查询参与情况
		$usernum = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_list)." WHERE weid = :weid and from_user = :from_user", array(':weid' => $weid, ':from_user' => $from_user));

	    $user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			//echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			include $this->template('remindnotweixin');
		} else { 
			include $this->template('listentry');	
		}
	}
	
	function get_share($weid,$rid,$fromuser,$title,$iid) {
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT xuninum FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		    $listtotal = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE weid= :weid AND rid= :rid', array(':weid' => $weid,':rid' => $rid));//参与人数
			$listtotal = $listtotal+$reply['xuninum'];//总参与人数
        }
		if (!empty($iid)) {
		    $gift = pdo_fetch("SELECT lihetitle,title FROM ".tablename($this->table_gift)." WHERE id = :id", array(':id' => $iid));
            $lihetitle = $gift['lihetitle'];
			$gifttitle = $gift['title'];
		}
		if (!empty($fromuser)) {
		    $userinfo = pdo_fetch("SELECT realname FROM ".tablename($this->table_list)." WHERE weid= :weid AND rid= :rid AND from_user= :fromuser", array(':weid' => $weid,':rid' => $rid,':fromuser' => $fromuser));
			$realname = $userinfo['realname'];
		}
		$str = array('#参与人数#'=>$listtotal,'#参与人名#'=>$realname,'#礼盒名称#'=>$lihetitle,'#奖品名称#'=>$gifttitle);
		$result = strtr($title,$str);
        return $result;
    }
	
	public function doMobilechailihe() {
		//关健词触发页面显示。
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID		
		$rid = $_GPC['rid'];
		$serverapp = $_W['account']['level'];	//是否为高级号
		if($serverapp==4){
		    $fromuser = $_W['fans']['from_user'];
			$fromuser_my = $fromuser;
		}else{
		    if(!empty($_GPC['chufa'])){
				$fromuser_my = authcode(base64_decode($_GPC['from_user']), 'DECODE');
				if(!isset($_COOKIE["chailihe_chufa_openid"])){
				    //借用号通过关健词触发获取真实的OPENID
				    setcookie("chailihe_chufa_openid", $fromuser_my, time()+3600*24*7);
			    }
			}else{
			    if(!empty($_GPC['from_user_oauth'])){
				    $fromuser = authcode(base64_decode($_GPC['from_user_oauth']), 'DECODE');
				    if (isset($_COOKIE["chailihe_chufa_openid"])){
			            $fromuser_my = $_COOKIE["chailihe_chufa_openid"];
			        }
				}
			}
			if(empty($fromuser)){
			    $cfg = $this->module['config'];
			    $appid = $cfg['appid'];
			    $secret = $cfg['secret'];
				if(!empty($appid)&&!empty($secret)){
			        //取openid值
				    $url = $_W['siteroot']."app/".substr($this->createMobileUrl('oauth2', array('rid' => $rid,'viewtype' => 'chailiheopenid'),true),2);
				    $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_base&state=0#wechat_redirect";
		            header("location:$oauth2_code");
			        exit;
				}else{
				    $fromuser = $fromuser_my;
				}
			}			
		}
		$page_fromuser = base64_encode(authcode($fromuser, 'ENCODE'));
      	if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));			
			$number_num_day = $reply['number_num_day'];
			$picture = toimage($reply['picture']);
			$picbg01 = toimage($reply['picbg01']);
			$bgcolor = $reply['bgcolor'];
			$text01color = $reply['text01color'];
			$text02color = $reply['text02color'];
			$text03color = $reply['text03color'];
			$share_shownum = $reply['share_shownum'];
			$music = $reply['music'];
			$musicbg = $reply['musicbg'];
			if ($reply['number_num']==1){			   
			   $number_num_day = '每人只可领取1个礼盒';
			}else{
			   $number_num_day = '每天可领'.$number_num_day.'个礼盒';
			}					
			if ($reply['status']==0) {
				$statpraisetitle = '<h1>活动暂停！请稍候再试！</h1>';
			}
			if (time()<$reply['start_time']) {//判断活动是否已经开始
				$statpraisetitle = '<h1>活动未开始！</h1>';
			}elseif (time()>$reply['end_time']) {//判断活动是否已经结束
				$statpraisetitle = '<h1>活动已结束！</h1>';
			}			
 		}
		//判断是否为关注用户才能领取
		if($reply['subscribe']==1){
		    $subscribe=0;//默认没有关注没有办法领取
			$fans = pdo_fetch("SELECT uid,follow FROM ".tablename('mc_mapping_fans') ." Where openid=:openid and uniacid=:uniacid ORDER BY `fanid` DESC",array(":openid"=>$fromuser,":uniacid"=>$weid));
			$profile = mc_fetch($fans['uid'], array('avatar','nickname','realname','mobile','qq','email','address','gender','telephone','idcard','company','occupation','position'));
			if (empty($fromuser_my)) {
		        $userinfo = pdo_fetch("SELECT from_user,qq,realname,mobile,nickname,address,email FROM ".tablename($this->table_list)." WHERE weid= :weid AND rid= :rid AND from_user= :fromuser", array(':weid' => $weid,':rid' => $rid,':fromuser' => $fromuser));
				if(!empty($userinfo)){
				    $fromuser_my = $userinfo['from_user'];
					$fans['follow'] = 1;
				}else{
			        //借用的必需关注才能领取时关注通过关健词触发才能参与
				    $shareurl = $reply['shareurl'];
				    header("location:$shareurl");
			        exit;
				}
		    }
			
		    if ($fans['follow']==1) {
			    $subscribe=1;
		    }
		}else{
		    $subscribe=1;//默认没有关注可以领取
		}
		//判断是否为关注用户才能领取		
		//判断是否还可以领取礼盒
		if(!empty($fromuser)) {						
		    $giftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."'");//领取礼盒数总数
			$todaytimestamp = strtotime(date('Y-m-d'));
		    $daygiftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."' AND datatime >= '".$todaytimestamp."'");//领取礼盒数今日数
			$gift_num = $reply['number_num']-$giftnum;	//总数还有多少次机会
			$gift_num_day = $reply['number_num_day']-$daygiftnum;//今日还有多少次机会
			$giftlihe = 0;//默认没有机会
			if ($gift_num>=1) {	
                if($gift_num_day>=1){
				   	if($gift_num<$gift_num_day){
					    $giftlihe = $gift_num;
					}else{
					    $giftlihe = $gift_num_day;
					}
				}						
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
			if($reply['repeatzj']==0) {
			    $zhongjiangnum = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE weid= :weid and rid= :rid and zhongjiang>=1 and from_user= :from_user', array(':weid' => $weid,':rid' => $rid,':from_user' => $fromuser));
				if ($zhongjiangnum>=1){
				    $giftlihe = 0;//只能中奖一次，已没有机会了
				}
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
		}		
		//中奖用户列表
		$listshare = pdo_fetchall('SELECT a.*,b.lihetitle,b.title as jp FROM '.tablename($this->table_list).' as a left join '.tablename($this->table_gift).' as b on a.liheid=b.id  WHERE a.zhongjiang >= 1 and a.weid= :weid AND a.rid = :rid order by `sharetime` desc LIMIT '.$share_shownum.'', array(':weid' => $weid,':rid' => $rid));
		$gifttotal = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_gift)." WHERE rid= ".$rid."");//总礼盒数
		//虚拟人数据配置
		$now = time();
		if($now-$reply['xuninum_time']>$reply['xuninumtime']){
		    pdo_update($this->table_reply, array('xuninum_time' => $now,'xuninum' => $reply['xuninum']+mt_rand($reply['xuninuminitial'],$reply['xuninumending'])), array('rid' => $rid));
		}
		//虚拟人数据配置
		//参与活动人数
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE weid=:weid and rid=:rid', array(':weid' => $weid,':rid' => $rid));
		$listtotal = $total+$reply['xuninum'];//总参与人数
		//参与活动人数
		//整理数据进行页面显示
		$regurl= $_W['siteroot']."app/".substr($this->createMobileUrl('reglihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$mylihe= $_W['siteroot']."app/".substr($this->createMobileUrl('mylihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$gohome= $_W['siteroot']."app/".substr($this->createMobileUrl('chailihe', array('rid' => $rid,'from_user_oauth' => $page_fromuser),true),2);
		$shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('shareuserview', array('rid' => $rid),true),2);
		$guanzhu = $reply['shareurl'];//没有关注用户跳转引导页
		//查询分享标题以及内容变量
		$reply['sharetitle']= $this->get_share($weid,$rid,$fromuser,$reply['sharetitle']);
		$reply['sharecontent']= $this->get_share($weid,$rid,$fromuser,$reply['sharecontent']);
		//判断是否被屏蔽
		$mystatus = pdo_fetch("SELECT status FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid= '".$rid."' order by `status` asc");
		if(!empty($mystatus)){//查询是否有记录
			if($mystatus['status']==0){
				$message = "亲，".$reply['title']."活动中您可能有作弊行为已被管理员暂停了！请联系".$_W['account']['name'];
				include $this->template('reminds');
				exit;
		    }
		}
		//判断是否被屏蔽
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			//echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			include $this->template('remindnotweixin');
		} else { 
			include $this->template('chailihe');
		}
	}
	public function doMobilereglihe() {
		//分享集赞分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$rid  = $_GPC['rid'];//当前规则ID		
		$fromuser = authcode(base64_decode($_GPC['fromuser']), 'DECODE');
		$page_fromuser = $_GPC['fromuser'];
		$serverapp = $_W['account']['level'];	//是否为高级号
		if($serverapp==4){
			$fromuser_my = $fromuser;
			//取用户资料
			$fans = pdo_fetch("SELECT uid,follow FROM ".tablename('mc_mapping_fans') ." Where openid=:openid and uniacid=:uniacid ORDER BY `fanid` DESC",array(":openid"=>$fromuser_my,":uniacid"=>$weid));
			$profile = mc_fetch($fans['uid'], array('avatar','nickname','realname','mobile','qq','email','address','gender','telephone','idcard','company','occupation','position'));
		}else{
			$fromuser_my = $_COOKIE["chailihe_chufa_openid"];
			if(empty($fromuser_my)){
		        $userinfo = pdo_fetch("SELECT from_user,qq,realname,mobile,nickname,address,email FROM ".tablename($this->table_list)." WHERE weid= :weid AND rid= :rid AND from_user= :fromuser", array(':weid' => $weid,':rid' => $rid,':fromuser' => $fromuser));
				if(!empty($userinfo)){
				    $fromuser_my = $userinfo['from_user'];
					$profile  = $userinfo;
					$fans['follow'] = 1;
				}
		    }else{
			    //取用户资料
			    $fans = pdo_fetch("SELECT uid,follow FROM ".tablename('mc_mapping_fans') ." Where openid=:openid and uniacid=:uniacid ORDER BY `fanid` DESC",array(":openid"=>$fromuser_my,":uniacid"=>$weid));
			    $profile = mc_fetch($fans['uid'], array('avatar','nickname','realname','mobile','qq','email','address','gender','telephone','idcard','company','occupation','position'));
			}
		}
		//活动规则
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$picture = toimage($reply['picture']);
			$bgcolor = $reply['bgcolor'];
			$picbg01 = toimage($reply['picbg01']);
			$picbg02 = toimage($reply['picbg02']);
			$text01color = $reply['text01color'];
			$text02color = $reply['text02color'];
			$text03color = $reply['text03color'];			
			$shangjialogo = toimage($reply['shangjialogo']);
			if ($reply['status']==0) {
				$statpraisetitle = '<h1>活动暂停！请稍候再试！</h1>';
			}
			if (time()<$reply['start_time']) {//判断活动是否已经开始
				$statpraisetitle = '<h1>活动未开始！</h1>';
			}elseif (time()>$reply['end_time']) {//判断活动是否已经结束
				$statpraisetitle = '<h1>活动已结束！</h1>';
			}
		}
		//虚拟人数据配置
		$now = time();
		if($now-$reply['xuninum_time']>$reply['xuninumtime']){
		    pdo_update($this->table_reply, array('xuninum_time' => $now,'xuninum' => $reply['xuninum']+mt_rand($reply['xuninuminitial'],$reply['xuninumending'])), array('rid' => $rid));
		}
		//虚拟人数据配置
		//判断是否还可以领取礼盒
		if(!empty($fromuser)) {			
		    $giftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."'");//领取礼盒数总数
			$todaytimestamp = strtotime(date('Y-m-d'));
		    $daygiftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."' AND datatime >= '".$todaytimestamp."'");//领取礼盒数今日数
			$gift_num = $reply['number_num']-$giftnum;	//总数还有多少次机会
			$gift_num_day = $reply['number_num_day']-$daygiftnum;//今日还有多少次机会
			$giftlihe = 0;//默认没有机会
			if ($gift_num>=1) {	
                if($gift_num_day>=1){
				   	if($gift_num<$gift_num_day){
					    $giftlihe = $gift_num;
					}else{
					    $giftlihe = $gift_num_day;
					}
				}						
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
			if($reply['repeatzj']==0) {
			    $zhongjiangnum = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE weid= :weid and rid= :rid and zhongjiang>=1 and from_user= :from_user', array(':weid' => $weid,':rid' => $rid,':from_user' => $fromuser));
				if ($zhongjiangnum>=1){
				    $giftlihe = 0;//只能中奖一次，已没有机会了
				}
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
			//判断是否已领取过 判断是否弹出领取层
			$Needregister = 'true';
			$isrealname = 0;
			if($reply['isrealname']){
		        if($profile['realname']!=''){
				    $isrealname = 1;
				}
		    }else{
			    $isrealname = 1;
			}
			
			$ismobile = 0;
			if($reply['ismobile']){
		        if($profile['mobile']!=''){
				    $ismobile = 1;
				}			
		    }else{
			    $ismobile = 1;
			}
			
			$isqq = 0;
			if($reply['isqq']){
		        if($profile['qq']!=''){
				    $isqq = 1;
				}			
		    }else{
			    $isqq = 1;
			}
			
			$isemail = 0;
			if($reply['isemail']){
		        if($profile['email']!=''){
				    $isemail = 1;
				}			
		    }else{
			    $isemail = 1;
			}
			
			$isaddress = 0;
			if($reply['isaddress']){
		        if($profile['address']!=''){
				    $isaddress = 1;
				}			
		    }else{
			    $isaddress = 1;
			}
			
            if($giftnum>=1&&$isaddress==1&&$isemail==1&&$isqq==1&&$ismobile==1&&$isrealname==1){
			    $Needregister = 'false';			
			}
			if($reply['isinfo']){
			    $Needregister = 'false';	
			}
		}
		//判断是否为关注用户才能领取
		if($reply['subscribe']==1){
		    $subscribe=0;//默认没有关注没有办法领取
		    if ($fans['follow']==1) {
			    $subscribe=1;
		    }
		}else{
		    $subscribe=1;//默认没有关注可以领取
		}
		//判断是否为关注用户才能领取
		//礼盒列表
		$listlihe = pdo_fetchall('SELECT * FROM '.tablename($this->table_gift).'  WHERE rid = :rid order by `id`', array(':rid' => $rid));
		//随机出礼盒
		if($reply['randlihe']) {
		    $prize_arr = array();
		    $probalilty = 0;
			$todaytimestamp = strtotime(date('Y-m-d'));
		    foreach ($listlihe as $row) {
		        $yzgiftnum = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).'  WHERE weid='.$weid.' AND rid= '.$rid.' AND zhongjiang>=1 AND liheid='.$row['id'].'');
				$yzgiftnumday = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).'  WHERE weid='.$weid.' AND datatime >= '.$todaytimestamp.' AND rid= '.$rid.' AND zhongjiang>=1 AND liheid='.$row['id'].'');//中奖总数
				$row['daytotal']=$row['daytotal'];
                if($row['daytotal']==0) {
				    $row['daytotal']=99999999999;
				}
			    if($row['total']-$yzgiftnum>=1&&floatval($row['probalilty'])>0&&$row['daytotal']-$yzgiftnumday>=1){
			        $item = array(
			    	    'id'      => $row['id'],
				        'v'       => $row['total']-$yzgiftnum,
			        );
			        $prize_arr[] = $item;
			    }
		    }
		    foreach ($prize_arr as $key => $val) {   
   		        $arr[$val['id']] = $val['v'];   
		    }   
		    $liheid = $this->get_rand($arr); //根据概率获取id
			$listlihe = pdo_fetchall('SELECT * FROM '.tablename($this->table_gift).'  WHERE id = :id order by `id`', array(':id' => $liheid));
		}
		//随机出礼盒
		//礼盒列表		
		$shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('shareuserview', array('rid' => $rid),true),2);
		$regurl = $_W['siteroot']."app/".substr($this->createMobileUrl('reguser', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$mylihe = $_W['siteroot']."app/".substr($this->createMobileUrl('mylihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$gohome= $_W['siteroot']."app/".substr($this->createMobileUrl('chailihe', array('rid' => $rid,'from_user_oauth' => $page_fromuser),true),2);
        $musicpath = '../addons/stonefish_chailihe/template/images/music/';
        $telpass = '';//以后用于手机短信验证
		$guanzhu = $reply['shareurl'];//没有关注用户跳转引导页
		//查询分享标题以及内容变量
		$reply['sharetitle']= $this->get_share($weid,$rid,$fromuser,$reply['sharetitle']);
		$reply['sharecontent']= $this->get_share($weid,$rid,$fromuser,$reply['sharecontent']);
		//判断是否被屏蔽
		$mystatus = pdo_fetch("SELECT status FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid= '".$rid."' order by `status` asc");
		if(!empty($mystatus)){//查询是否有记录
			if($mystatus['status']==0){
				$message = "亲，".$reply['title']."活动中您可能有作弊行为已被管理员暂停了！请联系".$_W['account']['name'];
				include $this->template('reminds');
				exit;
		    }
		}
		//判断是否被屏蔽
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			//echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			include $this->template('remindnotweixin');
		} else { 
			if(!empty($statpraisetitle)){
			    include $this->template('remind');
			}else{
			    include $this->template('reglihe');
			}			
		}		
	}

	public function doMobilereguser() {
		//分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$rid  = $_GPC['rid'];//当前规则ID		
		$fromuser = authcode(base64_decode($_GPC['fromuser']), 'DECODE');
		$page_fromuser = $_GPC['fromuser'];
		//活动规则
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		}
		if($reply['isrealname']&&$reply['isinfo']==0){
		    if(empty($_GPC['info-name'])){
		        $usrInfo = array('errno'=>1,'error'=>'请输入真实姓名！');
			    $jsdata=json_encode($usrInfo);
		        echo $jsdata;
			    exit;
		    }
		}
		if($reply['ismobile']&&$reply['isinfo']==0){
		    if(empty($_GPC['info-tel'])){
		        $usrInfo = array('errno'=>1,'error'=>'请输入联系电话！');
			    $jsdata=json_encode($usrInfo);
		        echo $jsdata;
			    exit;
		    }else{
			    if(preg_match('/^1(3|5|8)\d{9}$/',$_GPC['info-tel'])){
				
				}else{
				    $usrInfo = array('errno'=>1,'error'=>'请输入正确的手机号');
			        $jsdata=json_encode($usrInfo);
		            echo $jsdata;
			        exit;
				}
			}
		}
		
		if($reply['isqq']&&$reply['isinfo']==0){
		    if(empty($_GPC['info-qqhao'])){
		        $usrInfo = array('errno'=>1,'error'=>'请输入QQ号码！');
			    $jsdata=json_encode($usrInfo);
		        echo $jsdata;
			    exit;
		    }
		}
		if($reply['isemail']&&$reply['isinfo']==0){
		    if(empty($_GPC['info-email'])){
		        $usrInfo = array('errno'=>1,'error'=>'请输入邮箱！');
			    $jsdata=json_encode($usrInfo);
		        echo $jsdata;
			    exit;
		    }
		}
		if($reply['isaddress']&&$reply['isinfo']==0){
		    if(empty($_GPC['info-address'])){
		        $usrInfo = array('errno'=>1,'error'=>'请输入联系址！');
			    $jsdata=json_encode($usrInfo);
		        echo $jsdata;
			    exit;
		    }
		}
		if($_GPC['info-prize']==0){
		    $usrInfo = array('errno'=>1,'error'=>'请选择礼盒');
			$jsdata=json_encode($usrInfo);
		    echo $jsdata;
			exit;
		}
		
		//检查是否还有机会领取
		if(!empty($fromuser)) {			
		    $giftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."'");//领取礼盒数总数
			$todaytimestamp = strtotime(date('Y-m-d'));
		    $daygiftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."' AND datatime >= '".$todaytimestamp."'");//领取礼盒数今日数
			$gift_num = $reply['number_num']-$giftnum;	//总数还有多少次机会
			$gift_num_day = $reply['number_num_day']-$daygiftnum;//今日还有多少次机会
			$giftlihe = 0;//默认没有机会
			if ($gift_num>=1) {	
                if($gift_num_day>=1){
				   	if($gift_num<$gift_num_day){
					    $giftlihe = $gift_num;
					}else{
					    $giftlihe = $gift_num_day;
					}
				}						
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
			if($reply['repeatzj']==0) {
			    $zhongjiangnum = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE weid= :weid and rid= :rid and zhongjiang>=1 and from_user= :from_user', array(':weid' => $weid,':rid' => $rid,':from_user' => $fromuser));
				if ($zhongjiangnum>=1){
				    $giftlihe = 0;//只能中奖一次，已没有机会了
				}
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
		}
		if($giftlihe==0){
		    $usrInfo = array('errno'=>1,'error'=>'今天已没有机会领取了');
			$jsdata=json_encode($usrInfo);
		    echo $jsdata;
			exit;
		}else{
		    //注册礼盒
		    $now = time();
		    $insertdata = array(
			    'weid'      => $weid,
			    'from_user' => $fromuser,
			    'rid'       => $rid,
			    'avatar'    => $_GPC['avatar'],
			    'nickname'  => $_GPC['nickname'],
			    'realname'  => $_GPC['info-name'],
			    'mobile'   => $_GPC['info-tel'],
				'qq'   => $_GPC['info-qqhao'],
				'email'   => $_GPC['info-email'],
				'address'   => $_GPC['info-address'],
			    'liheid'    => $_GPC['info-prize'],
 			    'sharetime' => $now,
			    'datatime'  => $now
		    );
		    pdo_insert($this->table_list, $insertdata);		
		    $dataid = pdo_insertid();//取id
			$cfg = $this->module['config'];
	        $appid = $cfg['appid'];
		    $secret = $cfg['secret'];
		    //同时更新到官方FANS表中
			if($reply['isfans']&&($_W['account']['level']==4 Or (empty($appid)&&empty($secret)))){
			    if($reply['isrealname']&&!empty($_GPC['info-name'])){
				    fans_update($fromuser, array(
					    'realname' => $_GPC['info-name'],					
		            ));
				}
				if($reply['ismobile']&&!empty($_GPC['info-tel'])){
				    fans_update($fromuser, array(
					    'mobile' => $_GPC['info-tel'],					
		            ));
				}				
				if($reply['isqq']&&!empty($_GPC['info-qqhao'])){
				    fans_update($fromuser, array(
					    'qq' => $_GPC['info-qqhao'],					
		            ));
				}
				if($reply['isemail']&&!empty($_GPC['info-email'])){
				    fans_update($fromuser, array(
					    'email' => $_GPC['info-email'],					
		            ));
				}
				if($reply['isaddress']&&!empty($_GPC['info-address'])){
				    fans_update($fromuser, array(
					    'address' => $_GPC['info-address'],					
		            ));
				}				
			}		    
		    $usrInfo = array('errno'=>0,'path'=>$_W['siteroot']."app/".substr($this->createMobileUrl('regliheshow', array('rid' => $rid,'fromuser' => $page_fromuser,'uid' => $dataid),true),2));
		    $jsdata=json_encode($usrInfo);
		    echo $jsdata;
		    exit;
		}
		
	}
	public function doMobileregliheshow() {
		//分享集赞分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$rid  = $_GPC['rid'];//当前规则ID		
		$fromuser = authcode(base64_decode($_GPC['fromuser']), 'DECODE');
		$page_fromuser = $_GPC['fromuser'];
		$uid = $_GPC['uid'];
		//活动规则
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$picture = toimage($reply['picture']);
			$picbg01 = toimage($reply['picbg01']);
			$picbg02 = toimage($reply['picbg02']);
			$bgcolor = $reply['bgcolor'];
			$text01color = $reply['text01color'];
			$text02color = $reply['text02color'];
			$text03color = $reply['text03color'];
			$shangjialogo = toimage($reply['shangjialogo']);
			if ($reply['status']==0) {
				$statpraisetitle = '<h1>活动暂停！请稍候再试！</h1>';
			}
			if (time()<$reply['start_time']) {//判断活动是否已经开始
				$statpraisetitle = '<h1>活动未开始！</h1>';
			}elseif (time()>$reply['end_time']) {//判断活动是否已经结束
				$statpraisetitle = '<h1>活动已结束！</h1>';
			}
		}
		//虚拟人数据配置
		$now = time();
		if($now-$reply['xuninum_time']>$reply['xuninumtime']){
		    pdo_update($this->table_reply, array('xuninum_time' => $now,'xuninum' => $reply['xuninum']+mt_rand($reply['xuninuminitial'],$reply['xuninumending'])), array('rid' => $rid));
		}
		//虚拟人数据配置
		//礼盒粉丝信息
		$listuser = pdo_fetch('SELECT * FROM '.tablename($this->table_list).'  WHERE id = :id', array(':id' => $uid));//礼盒信息
		$listgift = pdo_fetch('SELECT * FROM '.tablename($this->table_gift).'  WHERE id = :id', array(':id' => $listuser['liheid']));
		if(!empty($listuser)){
		    $myname=$listuser['realname'];
		}
		
		$shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('sharelihe', array('rid' => $rid,'fromuser' => $page_fromuser,'iid' => $uid),true),2);//分享URL
		$mylihe = $_W['siteroot']."app/".substr($this->createMobileUrl('mylihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);//我的礼盒
		$gohome= $_W['siteroot']."app/".substr($this->createMobileUrl('chailihe', array('rid' => $rid,'from_user_oauth' => $page_fromuser),true),2);
		$openlihe = $_W['siteroot']."app/".substr($this->createMobileUrl('openlihe', array('rid' => $rid,'fromuser' => $page_fromuser,'info-prize' => $uid),true),2);//自己拆开礼盒
        //查询分享标题以及内容变量
		$reply['sharetitle']= $this->get_share($weid,$rid,$fromuser,$reply['sharetitle'],$listuser['liheid']);
		$reply['sharecontent']= $this->get_share($weid,$rid,$fromuser,$reply['sharecontent'],$listuser['liheid']);
		//判断是否被屏蔽
		$mystatus = pdo_fetch("SELECT status FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid= '".$rid."' order by `status` asc");
		if(!empty($mystatus)){//查询是否有记录
			if($mystatus['status']==0){
				$message = "亲，".$reply['title']."活动中您可能有作弊行为已被管理员暂停了！请联系".$_W['account']['name'];
				include $this->template('reminds');
				exit;
		    }
		}
		//判断是否被屏蔽
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			//echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			include $this->template('remindnotweixin');
		} else { 
			if(!empty($statpraisetitle)){
			    include $this->template('remind');
			}else{
			    include $this->template('regliheshow');
			}			
		}

	}

	public function doMobilemylihe() {
		//分享集赞分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$rid  = $_GPC['rid'];//当前规则ID		
		$fromuser = authcode(base64_decode($_GPC['fromuser']), 'DECODE');
		$page_fromuser = $_GPC['fromuser'];
		//活动规则
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$picture = toimage($reply['picture']);
			$picbg01 = toimage($reply['picbg01']);
			$picbg03 = toimage($reply['picbg03']);
			$bgcolor = $reply['bgcolor'];
			$text01color = $reply['text01color'];
			$text02color = $reply['text02color'];
			$text03color = $reply['text03color'];
			$shangjialogo = toimage($reply['shangjialogo']);
			if ($reply['status']==0) {
				$statpraisetitle = '<h1>活动暂停！请稍候再试！</h1>';
			}
			if (time()<$reply['start_time']) {//判断活动是否已经开始
				$statpraisetitle = '<h1>活动未开始！</h1>';
			}elseif (time()>$reply['end_time']) {//判断活动是否已经结束
				$statpraisetitle = '<h1>活动已结束！</h1>';
			}
		}
		//虚拟人数据配置
		$now = time();
		if($now-$reply['xuninum_time']>$reply['xuninumtime']){
		    pdo_update($this->table_reply, array('xuninum_time' => $now,'xuninum' => $reply['xuninum']+mt_rand($reply['xuninuminitial'],$reply['xuninumending'])), array('rid' => $rid));
		}
		//虚拟人数据配置
		//查询是否有借用接口
		$cfg = $this->module['config'];
		$appid = $cfg['appid'];
		$secret = $cfg['secret'];
		$friends = 'false';
		if($reply['helpnum']>=1&&($_W['account']['level']==4 Or !empty($appid))){
		    $friends = 'true';
		}
		//我的姓名
		$mylihe = pdo_fetch('SELECT realname FROM '.tablename($this->table_list).' WHERE weid= :weid AND rid = :rid and from_user = :from_user order by `id` desc', array(':weid' => $weid,':rid' => $rid,':from_user' => $fromuser));
		if(!empty($mylihe)){
		    $myname=$mylihe['realname'];
		}
		//我的礼盒信息
		$listlihe = pdo_fetchall('SELECT a.*,b.gift,b.break,b.total,b.lihetitle,b.title FROM '.tablename($this->table_list).' as a left join '.tablename($this->table_gift).' as b on a.liheid=b.id  WHERE a.weid= :weid AND a.rid = :rid and from_user = :from_user order by `id` desc', array(':weid' => $weid,':rid' => $rid,':from_user' => $fromuser));
		//判断是否有礼盒
		if(empty($listlihe)){
		    $message='您没有领取过礼盒';
		    include $this->template('reminds');
			exit;
		}
		//判断是否有礼盒
		//判断是否还可以领取礼盒
		if(!empty($fromuser)) {			
		    $giftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."'");//领取礼盒数总数
			$todaytimestamp = strtotime(date('Y-m-d'));
		    $daygiftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."' AND datatime >= '".$todaytimestamp."'");//领取礼盒数今日数

			$gift_num = $reply['number_num']-$giftnum;	//总数还有多少次机会
			$gift_num_day = $reply['number_num_day']-$daygiftnum;//今日还有多少次机会
			$abovemax = 'true';//默认没有机会
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
			if($reply['repeatzj']==0) {
			    $zhongjiangnum = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE weid= :weid and rid= :rid and zhongjiang>=1 and from_user= :from_user', array(':weid' => $weid,':rid' => $rid,':from_user' => $fromuser));
				if ($zhongjiangnum>=1){
				    $gift_num_day = 0;//只能中奖一次，已没有机会了
				}
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
			if ($gift_num>=1) {	
                if($gift_num_day>=1){
				   	$abovemax = 'false';
				}						
			}			
		}
		
		//计算礼盒状态开始
		foreach ($listlihe as $row) {
			$break = $row['break']-$row['sharenum'];//还需要多少全拆开
			if($break<=0){
			    $break = 0;			
			}
			//是否打过开
			$openlihe = 'false';
			if($row['openlihe']==1){
                 $openlihe = 'true';
			}
			//是否打过开
			//是否被领完
			$rc = 'false';
		    $zgiftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND zhongjiang>=1 AND rid= ".$rid." AND liheid='".$row['liheid']."'");//领取礼盒数中奖数
			if($zgiftnum>$row['total'] and $row['openlihe']==0 and $break==0){
                 $rc = 'true';
			}
			//是否被领完
			if($row['break']==0){//不需要朋友帮拆则直接自己拆开
			    $prize = $prize.'{h:1,r:0,i:'.$openlihe.',rc:'.$rc.',my:1},';
			}else{
			    $prize = $prize.'{h:'.$row['sharenum'].',r:'.$break.',i:'.$openlihe.',rc:'.$rc.',my:0},';
			}
		}
		// i:true=>打开过 false=>未打开过
        // rc:true=>被领完了 false=>未被领完
		$prize = substr($prize,0,strlen($prize)-1);
		//计算礼盒状态完成
		$shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('sharelihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);//分享URL
		//还可以再领一个
		$againreglihe = $_W['siteroot']."app/".substr($this->createMobileUrl('reglihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		//打开礼盒
		$openliheurl = $_W['siteroot']."app/".substr($this->createMobileUrl('openlihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		//查看礼盒奖品
		$viewliheurl = $_W['siteroot']."app/".substr($this->createMobileUrl('viewlihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);	
		//帮助我拆礼盒的朋友
		$helpuser = $_W['siteroot']."app/".substr($this->createMobileUrl('helpview', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		//帮助我拆礼盒的朋友
		$gotohome = $_W['siteroot']."app/".substr($this->createMobileUrl('chailihe', array('rid' => $rid,'from_user_oauth' => $page_fromuser),true),2);
        //查询分享标题以及内容变量
		$reply['sharetitle']= $this->get_share($weid,$rid,$fromuser,$reply['sharetitle']);
		$reply['sharecontent']= $this->get_share($weid,$rid,$fromuser,$reply['sharecontent']);
		//判断是否被屏蔽
		$mystatus = pdo_fetch("SELECT status FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid= '".$rid."' order by `status` asc");
		if(!empty($mystatus)){//查询是否有记录
			if($mystatus['status']==0){
				$message = "亲，".$reply['title']."活动中您可能有作弊行为已被管理员暂停了！请联系".$_W['account']['name'];
				include $this->template('reminds');
				exit;
		    }
		}
		//判断是否被屏蔽
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			//echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			include $this->template('remindnotweixin');
		} else { 
			include $this->template('mylihe');
		}			
	}
	public function doMobilehelpview() {
		//分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$rid  = $_GPC['rid'];//当前规则ID		
		$fromuser = authcode(base64_decode($_GPC['fromuser']), 'DECODE');
		$page_fromuser = $_GPC['fromuser'];
		$uid = $_GPC['info-prize1'];//礼盒分享人ID
		//活动规则
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$picture = toimage($reply['picture']);
			$picbg01 = toimage($reply['picbg01']);
			$bgcolor = $reply['bgcolor'];
			$text01color = $reply['text01color'];
			$text02color = $reply['text02color'];
			$text03color = $reply['text03color'];
			$text04color = $reply['text04color'];
			$text05color = $reply['text05color'];
			$picnojiang = toimage($reply['picnojiang']);
            if ($reply['status']==0) {
				$statpraisetitle = '<h1>活动暂停！请稍候再试！</h1>';
			}
			if (time()<$reply['start_time']) {//判断活动是否已经开始
				$statpraisetitle = '<h1>活动未开始！</h1>';
			}elseif (time()>$reply['end_time']) {//判断活动是否已经结束
				$statpraisetitle = '<h1>活动已结束！</h1>';
			}			
		}
		//虚拟人数据配置
		$now = time();
		if($now-$reply['xuninum_time']>$reply['xuninumtime']){
		    pdo_update($this->table_reply, array('xuninum_time' => $now,'xuninum' => $reply['xuninum']+mt_rand($reply['xuninuminitial'],$reply['xuninumending'])), array('rid' => $rid));
		}
		//虚拟人数据配置
		//判断是否还可以领取礼盒
		if(!empty($fromuser)) {		
		    $giftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."'");//领取礼盒数总数
			$todaytimestamp = strtotime(date('Y-m-d'));
		    $daygiftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."' AND datatime >= '".$todaytimestamp."'");//领取礼盒数今日数
			$gift_num = $reply['number_num']-$giftnum;	//总数还有多少次机会
			$gift_num_day = $reply['number_num_day']-$daygiftnum;//今日还有多少次机会
			$giftlihe = 0;//默认没有机会
			if ($gift_num>=1) {	
                if($gift_num_day>=1){
				   	if($gift_num<$gift_num_day){
					    $giftlihe = $gift_num;
					}else{
					    $giftlihe = $gift_num_day;
					}
				}						
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
			if($reply['repeatzj']==0) {
			    $zhongjiangnum = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE weid= :weid and rid= :rid and zhongjiang>=1 and from_user= :from_user', array(':weid' => $weid,':rid' => $rid,':from_user' => $fromuser));
				if ($zhongjiangnum>=1){
				    $giftlihe = 0;//只能中奖一次，已没有机会了
				}
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
		}
		
		//礼盒信息
		$lihegift = pdo_fetch('SELECT b.lihetitle,b.id FROM '.tablename($this->table_list).' as a left join '.tablename($this->table_gift).' as b on a.liheid=b.id  WHERE a.id=:id', array(':id' => $uid));
		//帮助我拆礼盒的朋友
		$hleplist = pdo_fetchall('SELECT * FROM '.tablename($this->table_data).' WHERE uid=:uid ORDER BY `id` DESC LIMIT '.$reply['helpnum'].'', array(':uid' => $uid));;
	
		
		$againreglihe = $_W['siteroot']."app/".substr($this->createMobileUrl('reglihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('shareuserview', array('rid' => $rid),true),2);
		$liheduijiang = $_W['siteroot']."app/".substr($this->createMobileUrl('duijiang', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$mylihe = $_W['siteroot']."app/".substr($this->createMobileUrl('mylihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$gohome= $_W['siteroot']."app/".substr($this->createMobileUrl('chailihe', array('rid' => $rid,'from_user_oauth' => $page_fromuser),true),2);
		
		//查询分享标题以及内容变量
		$reply['sharetitle']= $this->get_share($weid,$rid,$fromuser,$reply['sharetitle'],$lihegift['id']);
		$reply['sharecontent']= $this->get_share($weid,$rid,$fromuser,$reply['sharecontent'],$lihegift['id']);
		//判断是否被屏蔽
		$mystatus = pdo_fetch("SELECT status FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid= '".$rid."' order by `status` asc");
		if(!empty($mystatus)){//查询是否有记录
			if($mystatus['status']==0){
				$message = "亲，".$reply['title']."活动中您可能有作弊行为已被管理员暂停了！请联系".$_W['account']['name'];
				include $this->template('reminds');
				exit;
		    }
		}
		//判断是否被屏蔽
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			//echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			include $this->template('remindnotweixin');
		} else { 
			include $this->template('helpview');		
		}
			
	}
	public function doMobilesharelihe() {
		//分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$rid  = $_GPC['rid'];//当前规则ID
		$serverapp = $_W['account']['level'];	//是否为高级号		
		$fromuser = authcode(base64_decode($_GPC['fromuser']), 'DECODE');
		$page_fromuser = $_GPC['fromuser'];
		if($serverapp==4){
		    $from_user = $_W['fans']['from_user'];			
		}else{
		    $from_user = authcode(base64_decode($_GPC['from_user']), 'DECODE');
			if(isset($_COOKIE["chailihe_oauth2_openid"])){
			    $from_user = $_COOKIE["chailihe_oauth2_openid"];
			}
		}
		$page_from_user = base64_encode(authcode($from_user, 'ENCODE'));
		$nickname  = $_COOKIE["chailihe_oauth2_nickname"];//访问昵称	
		$avatar  = $_COOKIE["chailihe_oauth2_avatar"];//访问头像	
		$opentype = $_GPC['opentype'];//礼盒打开方式0为访问,1为点击
		if(empty($opentype)){
		    $opentype = 0;//默认为访问即可拆开礼盒
		}
		$openlihe_is = 0;//默认没有拆过礼盒
		$uid = $_GPC['iid'];//分享ID
		$visitorsip = getip();
		$now = time();
		$oauth2istrue=0;		
		//活动规则
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$picture = toimage($reply['picture']);
			$picbg02 = toimage($reply['picbg02']);
			$picbg01 = toimage($reply['picbg01']);
			$bgcolor = $reply['bgcolor'];
			$text01color = $reply['text01color'];
			$text02color = $reply['text02color'];
			$text03color = $reply['text03color'];
			$shangjialogo = toimage($reply['shangjialogo']);
			if ($reply['status']==0) {
				$statpraisetitle = '<h1>活动暂停！请稍候再试！</h1>';
			}
			if (time()<$reply['start_time']) {//判断活动是否已经开始
				$statpraisetitle = '<h1>活动未开始！</h1>';
			}elseif (time()>$reply['end_time']) {//判断活动是否已经结束
				$statpraisetitle = '<h1>活动已结束！</h1>';
			}
		}
        //虚拟人数据配置
		if($now-$reply['xuninum_time']>$reply['xuninumtime']){
		    pdo_update($this->table_reply, array('xuninum_time' => $now,'xuninum' => $reply['xuninum']+mt_rand($reply['xuninuminitial'],$reply['xuninumending'])), array('rid' => $rid));
		}
		//虚拟人数据配置
		if ($serverapp==4) {
		    $oauth2istrue=1;
			$appid = $_W['account']['key'];
		    $secret = $_W['account']['secret'];
		}else{
		    //查询是否有借用接口
			$cfg = $this->module['config'];
			$appid = $cfg['appid'];
			$secret = $cfg['secret'];
			if(!empty($secret)){
			    $oauth2istrue=1;
			}
		}		
		if($oauth2istrue==1 and $reply['helpnum']>=1){
		    if(empty($from_user) OR empty($nickname) OR empty($avatar)){
		        //取不到openid 以及头像和昵称开启授权模式
			    $url = $_W['siteroot']."app/".substr($this->createMobileUrl('oauth2', array('rid' => $rid,'iid' => $uid,'fromuser' => $page_fromuser,'viewtype' => 'sharelihe'),true),2);
		        $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
		        header("location:$oauth2_code");
			    exit;
			}
		}
		if(empty($from_user)){
		    //取不到openid 开启借用模式取opendid
			if ($serverapp!=4) {//普通号
			    //查询是否有借用接口
			    $cfg = $this->module['config'];
			    $appid = $cfg['appid'];
			    $secret = $cfg['secret'];
				if(!empty($appid)&&!empty($secret)){
				    //取openid值 
				    $url = $_W['siteroot']."app/".substr($this->createMobileUrl('oauth2', array('rid' => $rid,'iid' => $uid,'fromuser' => $page_fromuser,'viewtype' => 'shareliheopenid'),true),2);
				    $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_base&state=0#wechat_redirect";
		            header("location:$oauth2_code");
					exit;
                }else{
				    $from_user = 'qi'.base64_encode($visitorsip.'opendid').'ip';//以IP地址为唯一值					
				}
			}
		}
		//查询是否需要关注才能帮其拆礼盒
		if($reply['opensubscribe']==1){
		    //取用户资料
			$fans = pdo_fetch("SELECT uid,follow FROM ".tablename('mc_mapping_fans') ." Where openid=:openid and uniacid=:uniacid ORDER BY `fanid` DESC",array(":openid"=>$from_user,":uniacid"=>$weid));	
			if ($fans['follow']!=1) {
			    //没有关注用户跳转引导页
				$openshare = $reply['openshare'];
		        header("location:$openshare");
				exit;
		    }
		}
		
		//礼盒粉丝信息
		$listuser = pdo_fetch('SELECT * FROM '.tablename($this->table_list).'  WHERE id = :id', array(':id' => $uid));//礼盒信息
		$listgift = pdo_fetch('SELECT * FROM '.tablename($this->table_gift).'  WHERE id = :id', array(':id' => $listuser['liheid']));
		
		//添加拆礼盒记录
		if($fromuser!=$from_user){//自己不能给自己拆礼盒
		    $isok = 1;//默认拆过礼盒		    
			if($reply['helpren']==0) {
			    if($reply['chainum']==0) {
			        $where = "and fromuser = '".$fromuser."'";
			    }else{
			        $where = "and uid = '".$uid."' and fromuser = '".$fromuser."'";
			    }
			}
		    $sharedata = pdo_fetch("SELECT * FROM ".tablename($this->table_data)." WHERE rid = '".$rid."' and from_user = '".$from_user."' and weid = '".$weid."' ".$where."" );
			if(empty($sharedata)){
			     $isok = 0;//没有拆过礼盒
			}else{
			    if($reply['helpren']==1 and $reply['chainum']==1) {
				    //所有参与人员不同礼盒是否拆过
					if($sharedata['fromuser']==$fromuser&&$sharedata['uid']!=$uid) {
						$isok = 0;//没有拆过礼盒
					}
			    }
			}
			//查询是否为互拆
			$share_data = pdo_fetch("SELECT id FROM ".tablename($this->table_data)." WHERE rid = '".$rid."' and from_user = '".$fromuser."' and fromuser = '".$from_user."' and weid = '".$weid."'" );
			if(empty($share_data)&&$isok==0) {
			    $isok = 0;//没有拆过礼盒
			}else{
			    if($reply['helpchai']==1&&$isok==0) {
				    $isok = 0;//允许互拆
				}else{
				    $isok = 1;//不允许互拆
				}
			}
			//查询是否为互拆			
		    if($isok == 0){
		        if($opentype==$reply['opentype']){
				    $insertdata = array(
		                'weid'           => $weid,
		                'fromuser'       => $fromuser,
					    'from_user'      => $from_user,
		                'avatar'         => $avatar,
		                'nickname'       => $nickname,
		                'rid'            => $rid,
 		                'uid'            => $uid,
		                'visitorsip'	 => $visitorsip,
		                'visitorstime'   => $now
		            );				
				    pdo_insert($this->table_data, $insertdata);
					$openlihe_is = 1;//已拆过礼盒 
		            $updatelist = array(
		                'sharenum'  => $listuser['sharenum']+1,
		                'sharetime' => $now
		            );
		            pdo_update($this->table_list,$updatelist,array('id' => $uid));
				}
		    }else{
			    $openlihe_is = 1;//已拆过礼盒
			}
		}else{
		    //跳转到自己的礼盒信息处
		    $mylihe = $_W['siteroot']."app/".substr($this->createMobileUrl('mylihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);//我的礼盒
		    header("location:$mylihe");
			exit;
		}
		//添加拆礼盒记录完成

		//拆礼盒用户信息
		$chainum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_data)." WHERE weid=".$weid." AND rid= ".$rid." AND uid='".$uid."'");//多少个朋友帮你拆过
		//first第一个/last最后一个/done成功拆开/opened拆开中
		$openedstyle = 'opened';
		$rest = $listgift['break']-$chainum;		
		if($chainum==0){
		   $openedstyle = 'first';		   
		}
		if(($listgift['break']-$chainum)==1){
		   $openedstyle = 'last';
		}
		if($openlihe_is==1){
		    $openedstyle = 'opened';
		}
		if($isok==1){
		    $openedstyle = 'isok';
		}
		if($listgift['break']<=$chainum){
		   $openedstyle = 'done';
		   $rest = 0;
		}
		//拆礼盒用户信息

		$shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('sharelihe', array('rid' => $rid,'iid' => $uid,'fromuser' => $page_fromuser),true),2);//分享URL
		if($oauth2istrue==1){
		    $reglihe =  $_W['siteroot']."app/".substr($this->createMobileUrl('shareuserview', array('rid' => $rid,'from_user' => $page_from_user),true),2);//分享URL
		}else{
		    $reglihe =  $_W['siteroot']."app/".substr($this->createMobileUrl('shareuserview', array('rid' => $rid),true),2);//分享URL
		}
		$openlihe = $_W['siteroot']."app/".substr($this->createMobileUrl('sharelihe', array('rid' => $rid,'iid' => $uid,'fromuser' => $page_fromuser,'from_user' => $page_from_user,'opentype' => 1),true),2);//点击打开礼盒
		//查询分享标题以及内容变量
		$reply['sharetitle']= $this->get_share($weid,$rid,$fromuser,$reply['sharetitle'],$listuser['liheid']);
		$reply['sharecontent']= $this->get_share($weid,$rid,$fromuser,$reply['sharecontent'],$listuser['liheid']);
		//判断是否被屏蔽
		$mystatus = pdo_fetch("SELECT status FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid= '".$rid."' order by `status` asc");
		if(!empty($mystatus)){//查询是否有记录
			if($mystatus['status']==0){
				$message = "亲，".$reply['title']."活动中您可能有作弊行为已被管理员暂停了！请联系".$_W['account']['name'];
				include $this->template('reminds');
				exit;
		    }
		}
		//判断是否被屏蔽
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			//echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			include $this->template('remindnotweixin');
		} else { 
			if(!empty($statpraisetitle)){
			    include $this->template('remind');
			}else{
			    include $this->template('sharelihe');
			}			
		}
		
	}
	public function doMobileopenlihe() {
		//分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$rid  = $_GPC['rid'];//当前规则ID		
		$fromuser = authcode(base64_decode($_GPC['fromuser']), 'DECODE');
		$page_fromuser = $_GPC['fromuser'];
		$uid = $_GPC['info-prize'];//礼盒分享人ID
		//活动规则
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$picture = toimage($reply['picture']);
			$picbg01 = toimage($reply['picbg01']);
			$bgcolor = $reply['bgcolor'];
			$text01color = $reply['text01color'];
			$text02color = $reply['text02color'];
			$text03color = $reply['text03color'];
			$text04color = $reply['text04color'];
			$text05color = $reply['text05color'];
			$picnojiang = toimage($reply['picnojiang']);
            if ($reply['status']==0) {
				$statpraisetitle = '<h1>活动暂停！请稍候再试！</h1>';
			}
			if (time()<$reply['start_time']) {//判断活动是否已经开始
				$statpraisetitle = '<h1>活动未开始！</h1>';
			}elseif (time()>$reply['end_time']) {//判断活动是否已经结束
				$statpraisetitle = '<h1>活动已结束！</h1>';
			}			
		}
		//虚拟人数据配置
		$now = time();
		if($now-$reply['xuninum_time']>$reply['xuninumtime']){
		    pdo_update($this->table_reply, array('xuninum_time' => $now,'xuninum' => $reply['xuninum']+mt_rand($reply['xuninuminitial'],$reply['xuninumending'])), array('rid' => $rid));
		}
		//虚拟人数据配置
		//判断是否还可以领取礼盒
		if(!empty($fromuser)) {			
		    $giftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."'");//领取礼盒数总数
			$todaytimestamp = strtotime(date('Y-m-d'));
		    $daygiftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."' AND datatime >= '".$todaytimestamp."'");//领取礼盒数今日数
			$gift_num = $reply['number_num']-$giftnum;	//总数还有多少次机会
			$gift_num_day = $reply['number_num_day']-$daygiftnum;//今日还有多少次机会
			$giftlihe = 0;//默认没有机会
			if ($gift_num>=1) {	
                if($gift_num_day>=1){
				   	if($gift_num<$gift_num_day){
					    $giftlihe = $gift_num;
					}else{
					    $giftlihe = $gift_num_day;
					}
				}						
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
			if($reply['repeatzj']==0) {
			    $zhongjiangnum = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE weid= :weid and rid= :rid and zhongjiang>=1 and from_user= :from_user', array(':weid' => $weid,':rid' => $rid,':from_user' => $fromuser));
				if ($zhongjiangnum>=1){
				    $giftlihe = 0;//只能中奖一次，已没有机会了
				}
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
		}
		//兑奖地点区域选择
		if($reply['awarding']==1) {
		    
		}else{
		    
		}				
		//礼盒信息
		$lihegift = pdo_fetch('SELECT a.zhongjiang,a.openlihe,b.* FROM '.tablename($this->table_list).' as a left join '.tablename($this->table_gift).' as b on a.liheid=b.id  WHERE a.id=:id', array(':id' => $uid));
		$zgiftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND zhongjiang>=1 AND rid= ".$rid." AND liheid='".$lihegift['id']."'");//领取礼盒数中奖数		
		$todaytimestamp = strtotime(date('Y-m-d'));
		$daygiftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND zhongjiang>=1 AND rid= ".$rid." AND liheid='".$lihegift['id']."' AND datatime >= '".$todaytimestamp."'");//今日领取礼盒数中奖数
		//中奖记录
		$probalilty = 1000*floatval($lihegift['probalilty']);
		$probaliltyno = 100000-$probalilty;
		$zhongjiang = $lihegift['zhongjiang'];
		if($lihegift['daytotal']==0){
		    $lihegift['daytotal']=99999999999;
		}
        if ($zgiftnum<$lihegift['total']&&$daygiftnum<$lihegift['daytotal']){
		    if ($zhongjiang==0){
		        $prize_arr = array(   
  		          '0' => array('id'=>0,'prize'=>'NO中奖','v'=>$probaliltyno),   
  		          '1' => array('id'=>1,'prize'=>'YES中奖','v'=>$probalilty), 
		        ); 
		        foreach ($prize_arr as $key => $val) {   
   		            $arr[$val['id']] = $val['v'];   
		        }   
		        $zhongjiang = $this->get_rand($arr); //根据概率获取奖项id
				//查询是否可以重复中奖，不允许时中奖用户其他礼盒都为不中奖
				if($reply['repeatzj']==0) {
			    	$zhongjiangnum = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE weid= :weid and rid= :rid and zhongjiang>=1 and from_user= :from_user', array(':weid' => $weid,':rid' => $rid,':from_user' => $fromuser));
					if ($zhongjiangnum>=1){
				    	$zhongjiang = 0;//只能中奖一次，已没有机会了
					}
				}
				//查询是否可以重复中奖，不允许时中奖用户其他礼盒都为不中奖
				//查询中奖是否为0 为0则中奖也为不中奖
				if($probalilty==0){
				    $zhongjiang = 0;
				}
				//查询中奖是否为0 为0则中奖也为不中奖
		        pdo_update($this->table_list,array('zhongjiang' => $zhongjiang),array('id' => $uid));
				//查询是是否为密卡类开始
				if($lihegift['inkind']==0){
				    $mikaid = pdo_fetch("SELECT * FROM ".tablename($this->table_giftmika)." WHERE status=0 and giftid=:giftid ORDER BY id asc",array(':giftid' => $lihegift['id']));
					pdo_update($this->table_list,array('mikaid' => $mikaid['id']),array('id' => $uid));
					pdo_update($this->table_giftmika,array('from_user' => $fromuser,'status' => 1),array('id' => $mikaid['id']));
				}
				//查询是是否为密卡类完成
		    }
		}
		pdo_update($this->table_list,array('openlihe' => 1),array('id' => $uid));

		$lihegift['awardpic'] = empty($lihegift['awardpic']) ? "../addons/stonefish_chailihe/template/images/award.jpg" : $lihegift['awardpic'];
		$awardpic = toimage($lihegift['awardpic']);
		
		$againreglihe = $_W['siteroot']."app/".substr($this->createMobileUrl('reglihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('shareuserview', array('rid' => $rid),true),2);
		$liheduijiang = $_W['siteroot']."app/".substr($this->createMobileUrl('duijiang', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$mylihe = $_W['siteroot']."app/".substr($this->createMobileUrl('mylihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$gohome= $_W['siteroot']."app/".substr($this->createMobileUrl('chailihe', array('rid' => $rid,'from_user_oauth' => $page_fromuser),true),2);
		$gotoduijiang = $_W['siteroot']."app/".substr($this->createMobileUrl('viewlihe', array('rid' => $rid,'info-prize2' => $uid,'fromuser' => $page_fromuser),true),2);
		//查询分享标题以及内容变量
		$reply['sharetitle']= $this->get_share($weid,$rid,$fromuser,$reply['sharetitle'],$lihegift['id']);
		$reply['sharecontent']= $this->get_share($weid,$rid,$fromuser,$reply['sharecontent'],$lihegift['id']);
		//判断是否被屏蔽
		$mystatus = pdo_fetch("SELECT status FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid= '".$rid."' order by `status` asc");
		if(!empty($mystatus)){//查询是否有记录
			if($mystatus['status']==0){
				$message = "亲，".$reply['title']."活动中您可能有作弊行为已被管理员暂停了！请联系".$_W['account']['name'];
				include $this->template('reminds');
				exit;
		    }
		}
		//判断是否被屏蔽
		//查询是否需要重新填写中奖资料
		if($reply['isrealname']){
		    $userinfos .= 'realname,';
		}
		if($reply['ismobile']){
		    $userinfos .= 'mobile,';
		}
		if($reply['isqq']){
		    $userinfos .= 'qq,';
		}
		if($reply['isemail']){
		    $userinfos .= 'email,';
		}
		if($reply['isaddress']){
		    $userinfos .= 'address,';
		}
		$userinfos = substr($userinfos,0,strlen($userinfos)-1);
		$userinfo = pdo_fetchall("SELECT ".$userinfos." FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid= '".$rid."'");
		foreach ($userinfo as $userinfolist) {
		    if($reply['isrealname']){
			    if(empty($userinfolist['realname'])){
				    $isinfo = true;
					break;
				}
			}
			if($reply['ismobile']){
			    if(empty($userinfolist['mobile'])){
				    $isinfo = true;
					break;
				}
			}
			if($reply['isqq']){
			    if(empty($userinfolist['qq'])){
				    $isinfo = true;
					break;
				}
			}
			if($reply['isemail']){
			    if(empty($userinfolist['email'])){
				    $isinfo = true;
					break;
				}
			}
			if($reply['isaddress']){
			    if(empty($userinfolist['address'])){
				    $isinfo = true;
					break;
				}
			}
		}
		$userinfosave = $_W['siteroot']."app/".substr($this->createMobileUrl('userinfosave', array('rid' => $rid,'uid' => $uid,'fromuser' => $page_fromuser),true),2);
		//查询是否需要重新填写中奖资料
		$serverapp = $_W['account']['level'];	//是否为高级号
		if($serverapp==4){
			$fromuser_my = $fromuser;
			//取用户资料
			$fans = pdo_fetch("SELECT uid,follow FROM ".tablename('mc_mapping_fans') ." Where openid=:openid and uniacid=:uniacid ORDER BY `fanid` DESC",array(":openid"=>$fromuser_my,":uniacid"=>$weid));
			$profile = mc_fetch($fans['uid'], array('avatar','nickname','realname','mobile','qq','email','address','gender','telephone','idcard','company','occupation','position'));
		}else{
			$fromuser_my = $_COOKIE["chailihe_chufa_openid"];
			if(empty($fromuser_my)){
		        $userinfo = pdo_fetch("SELECT from_user,qq,realname,mobile,nickname,address,email FROM ".tablename($this->table_list)." WHERE weid= :weid AND rid= :rid AND from_user= :fromuser", array(':weid' => $weid,':rid' => $rid,':fromuser' => $fromuser));
				if(!empty($userinfo)){
				    $fromuser_my = $userinfo['from_user'];
					$profile  = $userinfo;
					$fans['follow'] = 1;
				}
		    }else{
			    //取用户资料
			    $fans = pdo_fetch("SELECT uid,follow FROM ".tablename('mc_mapping_fans') ." Where openid=:openid and uniacid=:uniacid ORDER BY `fanid` DESC",array(":openid"=>$fromuser_my,":uniacid"=>$weid));
			    $profile = mc_fetch($fans['uid'], array('avatar','nickname','realname','mobile','qq','email','address','gender','telephone','idcard','company','occupation','position'));
			}
		}
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			//echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			include $this->template('remindnotweixin');
		} else { 
			if(!empty($statpraisetitle)){
			    include $this->template('remind');
			}else{
			    include $this->template('openlihe');
			}			
		}
			
	}
	function get_rand($proArr) {   
        $result = '';    
        //概率数组的总概率精度   
        $proSum = array_sum($proArr);    
        //概率数组循环   
        foreach ($proArr as $key => $proCur) {   
            $randNum = mt_rand(1, $proSum);   
            if ($randNum <= $proCur) {   
                $result = $key;   
                break;   
            } else {   
                $proSum -= $proCur;   
            }         
        }   
        unset ($proArr);    
        return $result;   
    }

	public function doMobileviewlihe() {
		//分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$rid  = $_GPC['rid'];//当前规则ID		
		$fromuser = authcode(base64_decode($_GPC['fromuser']), 'DECODE');
		$page_fromuser = $_GPC['fromuser'];
		$uid = $_GPC['info-prize2'];//礼盒分享人ID
		//活动规则
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$picture = toimage($reply['picture']);
			$picbg01 = toimage($reply['picbg01']);
			$bgcolor = $reply['bgcolor'];
			$text01color = $reply['text01color'];
			$text02color = $reply['text02color'];
			$text03color = $reply['text03color'];
			$text04color = $reply['text04color'];
			$text05color = $reply['text05color'];
			$picnojiang = toimage($reply['picnojiang']);
            if ($reply['status']==0) {
				$statpraisetitle = '<h1>活动暂停！请稍候再试！</h1>';
			}
			if (time()<$reply['start_time']) {//判断活动是否已经开始
				$statpraisetitle = '<h1>活动未开始！</h1>';
			}elseif (time()>$reply['end_time']) {//判断活动是否已经结束
				$statpraisetitle = '<h1>活动已结束！</h1>';
			}			
		}
		//虚拟人数据配置
		$now = time();
		if($now-$reply['xuninum_time']>$reply['xuninumtime']){
		    pdo_update($this->table_reply, array('xuninum_time' => $now,'xuninum' => $reply['xuninum']+mt_rand($reply['xuninuminitial'],$reply['xuninumending'])), array('rid' => $rid));
		}
		//虚拟人数据配置
		//判断是否还可以领取礼盒
		if(!empty($fromuser)) {					
		    $giftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."'");//领取礼盒数总数
			$todaytimestamp = strtotime(date('Y-m-d'));
		    $daygiftnum = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid." AND from_user='".$fromuser."' AND datatime >= '".$todaytimestamp."'");//领取礼盒数今日数
			$gift_num = $reply['number_num']-$giftnum;	//总数还有多少次机会
			$gift_num_day = $reply['number_num_day']-$daygiftnum;//今日还有多少次机会
			$giftlihe = 0;//默认没有机会
			if ($gift_num>=1) {	
                if($gift_num_day>=1){
				   	if($gift_num<$gift_num_day){
					    $giftlihe = $gift_num;
					}else{
					    $giftlihe = $gift_num_day;
					}
				}						
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
			if($reply['repeatzj']==0) {
			    $zhongjiangnum = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE weid= :weid and rid= :rid and zhongjiang>=1 and from_user= :from_user', array(':weid' => $weid,':rid' => $rid,':from_user' => $fromuser));
				if ($zhongjiangnum>=1){
				    $giftlihe = 0;//只能中奖一次，已没有机会了
				}
			}
			//查询是否可以重复中奖，不允许时中奖用户即使有领取礼盒机会也不能中领取礼盒
		}
		
		//中奖礼盒信息
		$lihegift = pdo_fetch('SELECT a.zhongjiang,a.mikaid,b.* FROM '.tablename($this->table_list).' as a left join '.tablename($this->table_gift).' as b on a.liheid=b.id  WHERE a.id=:id', array(':id' => $uid));		
		$zhongjiang = $lihegift['zhongjiang'];
		//查询是是否为密卡类开始
		if($lihegift['inkind']==0&&$zhongjiang==1&&$lihegift['mikaid']>=1){
			$mikaid = pdo_fetch("SELECT * FROM ".tablename($this->table_giftmika)." WHERE id=:id",array(':id' => $lihegift['mikaid']));
		}				
		//查询是是否为密卡类完成
		//兑奖地点区域选择
		if($reply['awarding']==1) {
		    
		}else{
		    
		}
		$lihegift['awardpic'] = empty($lihegift['awardpic']) ? "../addons/stonefish_chailihe/template/images/award.jpg" : $lihegift['awardpic'];
		$awardpic = toimage($lihegift['awardpic']);	
		$againreglihe = $_W['siteroot']."app/".substr($this->createMobileUrl('reglihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('shareuserview', array('rid' => $rid),true),2);
		$liheduijiang = $_W['siteroot']."app/".substr($this->createMobileUrl('duijiang', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$mylihe = $_W['siteroot']."app/".substr($this->createMobileUrl('mylihe', array('rid' => $rid,'fromuser' => $page_fromuser),true),2);
		$gohome= $_W['siteroot']."app/".substr($this->createMobileUrl('chailihe', array('rid' => $rid,'from_user_oauth' => $page_fromuser),true),2);
		$gotoduijiang = $_W['siteroot']."app/".substr($this->createMobileUrl('viewlihe', array('rid' => $rid,'info-prize2' => $uid,'fromuser' => $page_fromuser),true),2);
		
		//查询分享标题以及内容变量
		$reply['sharetitle']= $this->get_share($weid,$rid,$fromuser,$reply['sharetitle'],$lihegift['id']);
		$reply['sharecontent']= $this->get_share($weid,$rid,$fromuser,$reply['sharecontent'],$lihegift['id']);
		//判断是否被屏蔽
		$mystatus = pdo_fetch("SELECT status FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid= '".$rid."' order by `status` asc");
		if(!empty($mystatus)){//查询是否有记录
			if($mystatus['status']==0){
				$message = "亲，".$reply['title']."活动中您可能有作弊行为已被管理员暂停了！请联系".$_W['account']['name'];
				include $this->template('reminds');
				exit;
		    }
		}
		//判断是否被屏蔽
		//查询是否需要重新填写中奖资料
		if($reply['isrealname']){
		    $userinfos .= 'realname,';
		}
		if($reply['ismobile']){
		    $userinfos .= 'mobile,';
		}
		if($reply['isqq']){
		    $userinfos .= 'qq,';
		}
		if($reply['isemail']){
		    $userinfos .= 'email,';
		}
		if($reply['isaddress']){
		    $userinfos .= 'address,';
		}
		$userinfos = substr($userinfos,0,strlen($userinfos)-1);
		$userinfo = pdo_fetchall("SELECT ".$userinfos." FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid= '".$rid."'");
		foreach ($userinfo as $userinfolist) {
		    if($reply['isrealname']){
			    if(empty($userinfolist['realname'])){
				    $isinfo = true;
					break;
				}
			}
			if($reply['ismobile']){
			    if(empty($userinfolist['mobile'])){
				    $isinfo = true;
					break;
				}
			}			
			if($reply['isqq']){
			    if(empty($userinfolist['qq'])){
				    $isinfo = true;
					break;
				}
			}
			if($reply['isemail']){
			    if(empty($userinfolist['email'])){
				    $isinfo = true;
					break;
				}
			}
			if($reply['isaddress']){
			    if(empty($userinfolist['address'])){
				    $isinfo = true;
					break;
				}
			}
		}
		$userinfosave = $_W['siteroot']."app/".substr($this->createMobileUrl('userinfosave', array('rid' => $rid,'uid' => $uid,'fromuser' => $page_fromuser),true),2);
		//查询是否需要重新填写中奖资料
		$serverapp = $_W['account']['level'];	//是否为高级号
		if($serverapp==4){
			$fromuser_my = $fromuser;
			//取用户资料
			$fans = pdo_fetch("SELECT uid,follow FROM ".tablename('mc_mapping_fans') ." Where openid=:openid and uniacid=:uniacid ORDER BY `fanid` DESC",array(":openid"=>$fromuser_my,":uniacid"=>$weid));
			$profile = mc_fetch($fans['uid'], array('avatar','nickname','realname','mobile','qq','email','address','gender','telephone','idcard','company','occupation','position'));
		}else{
			$fromuser_my = $_COOKIE["chailihe_chufa_openid"];
			if(empty($fromuser_my)){
		        $userinfo = pdo_fetch("SELECT from_user,qq,realname,mobile,nickname,address,email FROM ".tablename($this->table_list)." WHERE weid= :weid AND rid= :rid AND from_user= :fromuser", array(':weid' => $weid,':rid' => $rid,':fromuser' => $fromuser));
				if(!empty($userinfo)){
				    $fromuser_my = $userinfo['from_user'];
					$profile  = $userinfo;
					$fans['follow'] = 1;
				}
		    }else{
			    //取用户资料
				$fans = pdo_fetch("SELECT uid,follow FROM ".tablename('mc_mapping_fans') ." Where openid=:openid and uniacid=:uniacid ORDER BY `fanid` DESC",array(":openid"=>$fromuser_my,":uniacid"=>$weid));
			    $profile = mc_fetch($fans['uid'], array('avatar','nickname','realname','mobile','qq','email','address','gender','telephone','idcard','company','occupation','position'));
			}
		}
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			//echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			include $this->template('remindnotweixin');
		} else { 
			include $this->template('openlihe');
		}
	}
	public function doMobileuserinfosave() {
		//分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$rid  = $_GPC['rid'];//当前规则ID
		$uid  = $_GPC['uid'];//礼盒ID		
		$fromuser = authcode(base64_decode($_GPC['fromuser']), 'DECODE');
		$page_fromuser = $_GPC['fromuser'];
		//活动规则
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		}
		//同时更新到官方FANS表中
	    if($reply['isrealname']&&!empty($_GPC['info-name'])){			
			if($reply['isfans']){
			    fans_update($fromuser, array(
				    'realname' => $_GPC['info-name'],
		        ));
			}
			pdo_update($this->table_list, array('realname' => $_GPC['info-name']), array('from_user' => $fromuser, 'weid' => $weid));
		}
		if($reply['ismobile']&&!empty($_GPC['info-tel'])){
			if($reply['isfans']){
			    fans_update($fromuser, array(
				    'mobile' => $_GPC['info-tel'],					
		        ));
			}
			pdo_update($this->table_list, array('mobile' => $_GPC['info-tel']), array('from_user' => $fromuser, 'weid' => $weid));
		}
		if($reply['isqq']&&!empty($_GPC['info-qqhao'])){
			if($reply['isfans']){
			    fans_update($fromuser, array(
				    'qq' => $_GPC['info-qqhao'],					
		        ));
			}
			pdo_update($this->table_list, array('qq' => $_GPC['info-qqhao']), array('from_user' => $fromuser, 'weid' => $weid));
		}
		if($reply['isemail']&&!empty($_GPC['info-email'])){
			if($reply['isfans']){
			    fans_update($fromuser, array(
				    'email' => $_GPC['info-email'],					
		        ));
			}
			pdo_update($this->table_list, array('email' => $_GPC['info-email']), array('from_user' => $fromuser, 'weid' => $weid));
		}
		if($reply['isaddress']&&!empty($_GPC['info-address'])){
			if($reply['isfans']){
			    fans_update($fromuser, array(
				    'address' => $_GPC['info-address'],
		        ));
			}
			pdo_update($this->table_list, array('address' => $_GPC['info-address']), array('from_user' => $fromuser, 'weid' => $weid));
		}
		//跳转到自己的礼盒信息处
		$mylihe = $_W['siteroot']."app/".substr($this->createMobileUrl('viewlihe', array('rid' => $rid,'info-prize2' => $uid,'fromuser' => $page_fromuser),true),2);
		header("location:$mylihe");
		exit;
	}	
	
	public function doMobileshareuserview() {
		//分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$rid  = $_GPC['rid'];//当前规则ID        
		$serverapp = $_W['account']['level'];	//是否为高级号
		if($serverapp==4) {		    
			$from_user = $_W['fans']['from_user'];
		    $page_from_user = base64_encode(authcode($from_user, 'ENCODE'));
		    //取得openid跳转出去
			$shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('chailihe', array('rid' => $rid,'from_user_oauth' => $page_from_user),true),2);//分享URL
			header("location:$shareurl");
			exit;
		}else{
		    if(!empty($_GPC['from_user'])){
			    //取得openid跳转出去
			    $shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('chailihe', array('rid' => $rid,'from_user_oauth' => $_GPC['from_user']),true),2);//分享URL
			    header("location:$shareurl");
			    exit;
			}else{
			    //取不到openid 开启借用模式取opendid
			    $cfg = $this->module['config'];
			    $appid = $cfg['appid'];
			    $secret = $cfg['secret'];
		        if(!empty($appid)&&!empty($secret)){
				    $url = $_W['siteroot']."app/".substr($this->createMobileUrl('oauth2', array('rid' => $rid,'viewtype' => 'chailiheopenid'),true),2);
				    $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_base&state=0#wechat_redirect";
		            header("location:$oauth2_code");
			        exit;
                }else{
				    if (!empty($rid)) {
			            $reply = pdo_fetch("SELECT shareurl FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));			
		            }
			        $shareurl = $reply['shareurl'];
		            header("location:$shareurl");
				    exit;
		        }
			}
		}		
		
	}
	public function doMobileoauth2() {
		global $_GPC,$_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$viewtype = $_GPC['viewtype'];
		$fromuser = authcode(base64_decode($_GPC['fromuser']), 'DECODE');
		$page_fromuser = $_GPC['fromuser'];
		$iid = $_GPC['iid'];
		$rid = $_GPC['rid'];	
		$serverapp = $_W['account']['level'];	//是否为高级号
		//借用还是本身为认证号
		if ($serverapp==4) {
		    $appid = $_W['account']['key'];
		    $secret = $_W['account']['secret'];
		}else{
		    $cfg = $this->module['config'];
			$appid = $cfg['appid'];
			$secret = $cfg['secret'];
		}
		//用户不授权返回提示说明
		if ($_GPC['code']=="authdeny"){
		    $url = $_W['siteroot']."app/".substr($this->createMobileUrl('oauth2shouquan', array('rid' => $rid),true),2);
			header("location:$url");
			exit;
		}
		//高级接口取未关注用户Openid
		if (isset($_GPC['code'])){
		    //第二步：获得到了OpenID
		    load()->func('communication');
		    $code = $_GPC['code'];			
		    $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
		    $content = ihttp_get($oauth2_code);
		    $token = @json_decode($content['content'], true);
			if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
				echo '<h1>获取微信公众号授权'.$code.'失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';
				exit;
			}
		    $from_user = $token['openid'];
			$page_from_user = base64_encode(authcode($from_user, 'ENCODE'));		
			//设置cookie信息
		    setcookie("chailihe_oauth2_openid", $from_user, time()+3600*24*7);
			if($viewtype=='shareliheopenid'){
			    $shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('sharelihe', array('rid' => $rid,'iid' => $iid,'fromuser' => $page_fromuser,'from_user' => $page_from_user),true),2);
				header("location:$shareurl");
			    exit;//只取OPENID
			}
			if($viewtype=='chailiheopenid'){
			    $shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('chailihe', array('rid' => $rid,'from_user_oauth' => $page_from_user),true),2);
				header("location:$shareurl");
			    exit;//只取OPENID
			}
			if($viewtype=='sharelihe'){
			    $access_token = $token['access_token'];
			    //使用全局ACCESS_TOKEN获取OpenID的详细信息		
			    $oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";
			    $content = ihttp_get($oauth2_url);
			    $info = @json_decode($content['content'], true);
			    if(empty($info) || !is_array($info) || empty($info['openid'])  || empty($info['nickname']) ) {
				    echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';
				    exit;
			    }
		        $avatar = $info['headimgurl'];
		        $nickname = $info['nickname'];			
			    //设置cookie信息
			    setcookie("chailihe_oauth2_avatar", $avatar, time()+3600*24*7);
		        setcookie("chailihe_oauth2_nickname", $nickname, time()+3600*24*7);
			    $shareurl = $_W['siteroot']."app/".substr($this->createMobileUrl('sharelihe', array('rid' => $rid,'iid' => $iid,'fromuser' => $page_fromuser,'from_user' => $page_from_user),true),2);
		        header("location:$shareurl");
			    exit;
			}
		}else{
			echo '<h1>不是高级认证号或网页授权域名设置出错!</h1>';
			exit;		
		}
	}
    public function doWebuserlist() {		
		global $_GPC, $_W;
		checklogin();
		$weid = $_W['uniacid'];//当前公众号ID
		$rid = intval($_GPC['id']);
		if(!empty($rid)){
		    $reply = pdo_fetch("SELECT title FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$guize = $reply['title'];
		}
		if (checksubmit('delete')) {
			//删除分享人分享的数据
			foreach ($_GPC['select'] as $value) {
				pdo_delete($this->table_list, array('id' => $value));
				pdo_delete($this->table_data, array('uid' => $value));
			}
			//删除分享人分享的数据
			message('删除成功！', create_url('site/entry', array('do' => 'userlist', 'm' => 'stonefish_chailihe', 'rid' => $rid, 'page' => $_GPC['page'])));
		}
		$where = '';
		!empty($_GPC['mobile']) && $where .= " AND a.mobile LIKE '%{$_GPC['mobile']}%'";
		!empty($_GPC['realname']) && $where .= " AND a.realname LIKE '%{$_GPC['realname']}%'";
		!empty($rid) && $where .= " AND a.rid = '{$rid}'";
		if(!empty($_GPC['status'])){
			if($_GPC['status']==1){
				$where .= " AND a.openlihe = 0";
			}
			if($_GPC['status']==2){
				$where .= " AND a.openlihe = 1";
			}
			if($_GPC['status']==3){
				$where .= " AND a.zhongjiang = 0";
			}
			if($_GPC['status']==4){
				$where .= " AND a.zhongjiang = 1";
			}
			if($_GPC['status']==5){
				$where .= " AND a.zhongjiang = 2";
			}
			if($_GPC['status']==6){
				$where .= " AND a.zhongjiang >=1 AND a.xuni = 0";
			}
		}

		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		//取得用户列表
		$list_praise = pdo_fetchall('SELECT a.*,b.lihetitle,b.title FROM '.tablename($this->table_list).' as a left join '.tablename($this->table_gift).' as b on a.liheid=b.id  WHERE a.weid= :weid '.$where.' order by `id` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid) );
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' as a left join '.tablename($this->table_gift).' as b on a.liheid=b.id  WHERE a.weid= :weid '.$where.' ', array(':weid' => $weid));
		$pager = pagination($total, $pindex, $psize);
		include $this->template('userlist');

	}
	
	public function doWebsharedata() {		
		global $_GPC, $_W;
		checklogin();
		$weid = $_W['uniacid'];//当前公众号ID
		$uid = intval($_GPC['uid']);
		$rid = intval($_GPC['id']);
		if(!empty($rid)){
		    $reply = pdo_fetch("SELECT title FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$guize = $reply['title'];
		}
		if (checksubmit('delete')) {
			pdo_delete($this->table_data, " id IN ('".implode("','", $_GPC['select'])."')");
			//重新计算分享数量
			foreach ($_GPC['select'] as $value) {
			    $userid = pdo_fetch("SELECT uid FROM ".tablename($this->table_data)." WHERE id = :id ", array(':id' => $value));
			    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_data).' WHERE uid=:uid',array(':uid' => $userid['uid']));
				pdo_delete($this->table_data, array('id' => $value));
				pdo_update($this->table_list, array('sharenum' => $total-1), array('id' => $userid['uid']));				
			}
			//重新计算分享数量
			message('删除成功！', create_url('site/entry/sharedata', array('m' => 'stonefish_chailihe', 'id' => $rid, 'page' => $_GPC['page'])));
		}
		if (!empty($uid)){
			$Where .= " AND `uid` = $uid";		
		}
		if (!empty($rid)){
			$Where .= " AND `rid` = $rid";		
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		//取得分享点击详细数据
		$list_praisedata = pdo_fetchall('SELECT * FROM '.tablename($this->table_data).' WHERE weid= :weid '.$Where.'  order by `visitorstime` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid) );
		//查询分享人姓名电话开始
		foreach ($list_praisedata as $mid => $list) {
		    $reply1 = pdo_fetch("SELECT realname,mobile FROM ".tablename($this->table_list)." WHERE weid = :weid and id = :id ", array(':weid' => $_W['uniacid'], ':id' => $list['uid']));
			$list_praisedata[$mid]['frealname'] = $reply1['realname'];
			$list_praisedata[$mid]['fmobile'] = $reply1['mobile'];			
		}
		//查询分享人姓名电话结束
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_data).' WHERE weid= :weid '.$Where.'  order by `visitorstime` desc ', array(':weid' => $weid));
		$pager = pagination($total, $pindex, $psize);
		include $this->template('sharedata');

	}
	
	public function doWebprizedata() {		
		global $_GPC, $_W;
		checklogin();
		$weid = $_W['uniacid'];//当前公众号ID
		$rid = intval($_GPC['id']);
		if(!empty($rid)){
		    $reply = pdo_fetch("SELECT title FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$guize = $reply['title'];
		}
		if (checksubmit('delete')) {
			//删除分享人分享的数据
			foreach ($_GPC['select'] as $value) {
				pdo_delete($this->table_list, array('id' => $value));
				pdo_delete($this->table_data, array('uid' => $value));
			}
			//删除分享人分享的数据
			message('删除成功！', create_url('site/entry', array('do' => 'prizedata', 'm' => 'stonefish_chailihe', 'id' => $rid, 'page' => $_GPC['page'])));
		}
		$where = '';
		!empty($_GPC['mobile']) && $where .= " AND a.mobile LIKE '%{$_GPC['mobile']}%'";
		!empty($_GPC['realname']) && $where .= " AND a.realname LIKE '%{$_GPC['realname']}%'";
		!empty($rid) && $where .= " AND a.rid = '{$rid}'";
		if(!empty($_GPC['status'])){
			if($_GPC['status']==4){
				$where .= " AND a.zhongjiang = 1";
			}
			if($_GPC['status']==5){
				$where .= " AND a.zhongjiang = 2";
			}
			if($_GPC['status']==6){
				$where .= " AND a.zhongjiang >=1 AND a.xuni = 0";
			}
		}

		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		//取得用户列表
		$list_praise = pdo_fetchall('SELECT a.*,b.lihetitle,b.title FROM '.tablename($this->table_list).' as a left join '.tablename($this->table_gift).' as b on a.liheid=b.id  WHERE a.zhongjiang>0 and a.weid= :weid '.$where.' order by `id` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid) );
		
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' as a left join '.tablename($this->table_gift).' as b on a.liheid=b.id  WHERE a.zhongjiang>0 and a.weid= :weid '.$where.' ', array(':weid' => $weid));
		$pager = pagination($total, $pindex, $psize);
		include $this->template('prizedata');

	}	
	
	public function doWebSetshow() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $status = intval($_GPC['status']);

        if (empty($id)) {
            message('抱歉，传递的参数错误！', '', 'error');
        }
        $temp = pdo_update($this->table_reply, array('status' => $status), array('id' => $id));
        message('状态设置成功！', referer(), 'success');
    }
	public function doWebEventlist() {		
		global $_GPC, $_W;
		//查询是否有商户网点权限
		$modules = uni_modules($enabledOnly = true);
		$modules_arr = array();
		$modules_arr = array_reduce($modules, create_function('$v,$w', '$v[$w["mid"]]=$w["name"];return $v;'));
		if(in_array('stonefish_branch',$modules_arr)){
		    $stonefish_branch = true;
		}
		//查询是否有商户网点权限
		$weid = $_W['uniacid'];//当前公众号ID
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$params = array();
        $params[':weid'] = $weid;
		if (!empty($_GPC['keyword'])) {
            $sql .= ' and `title` LIKE :keyword';
            $params[':keyword'] = "%{$_GPC['keyword']}%";
        }
		$list_praise = pdo_fetchall('SELECT * FROM '.tablename($this->table_reply).' WHERE weid= :weid '.$sql.' order by `id` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params );
		$pager = pagination($total, $pindex, $psize);
		
		if (!empty($list_praise)) {
			foreach ($list_praise as $mid => $list) {
				$count = pdo_fetch("SELECT count(id) as dd FROM ".tablename($this->table_list)." WHERE rid= ".$list['rid']."");
		        $list_praise[$mid]['user_znum'] = $count['dd'];//参与人数
				$count = pdo_fetch("SELECT count(id) as dd FROM ".tablename($this->table_data)." WHERE rid= ".$list['rid']."");
		        $list_praise[$mid]['share_znum'] = $count['dd'];//分享人数
				
				$listpraise = pdo_fetchall('SELECT * FROM '.tablename($this->table_gift).' WHERE rid=:rid  order by `id`',array(':rid' => $list['rid']));
				if (!empty($listpraise)) {
			         $praiseinfo = '';
					 foreach ($listpraise as $row) {
				       $count = pdo_fetch("SELECT count(id) as dd FROM ".tablename($this->table_list)." WHERE zhongjiang>=1 and liheid=".$row['id']." and rid= ".$row['rid']."");
					   $praiseinfo = $praiseinfo.'奖品：'.$row['title'].'；总数为：'.$row['total'].'；中奖率：'.$row['probalilty'].'%；中奖数为：'.$count['dd'].'；还剩：<b>'.($row['total']-$count['dd']).'</b>个奖品<br/>';
			        }
		        }
				$praiseinfo = substr($praiseinfo,0,strlen($praiseinfo)-5); 
				$list_praise[$mid]['praiseinfo'] = $praiseinfo;//奖品情况
				$nowtime = time();
                if ($list['start_time'] > $nowtime) {
                    $list_praise[$mid]['isshow'] = '<span class="label label-warning">未开始</span>';
                } elseif ($list['end_time'] < $nowtime) {
                    $list_praise[$mid]['isshow'] = '<span class="label label-default ">已结束</span>';
                } else {
                    if ($list['status'] == 1) {
                        $list_praise[$mid]['isshow'] = '<span class="label label-success">已开始</span>';
                    } else {
                        $list_praise[$mid]['isshow'] = '<span class="label label-default ">已暂停</span>';
                    }
                }
			}
		}
		include $this->template('event');

	}
	public function doWebdos( $id = 0) {
		global $_GPC;
		$rid = $_GPC['rid'];
		$id = $_GPC['id'];
		$praiselist = $_GPC['ac'];
		$insert = array(
			'status' => $_GPC['status']
		);
		
		pdo_update($this->table_list,$insert,array('id' => $id,'rid' => $rid));
		message('屏蔽操作成功！', url('site/entry/'.$praiselist.'', array('m' => 'stonefish_chailihe', 'id' => $rid, 'page' => $_GPC['page'])));
	}	
	public function doWebdosjiang( $id = 0) {
		global $_GPC;
		$rid = $_GPC['rid'];
		$id = $_GPC['id'];
		$praiselist = $_GPC['ac'];
		$insert = array(
			'zhongjiang' => $_GPC['status']
		);
		
		pdo_update($this->table_list,$insert,array('id' => $id,'rid' => $rid));
		message('已成功发放奖品！', create_url('site/entry/'.$praiselist.'', array('m' => 'stonefish_chailihe', 'id' => $rid, 'page' => $_GPC['page'])));
	}
	
	public function doWebAwardmika() {
	    global $_GPC, $_W;
		checklogin();
		$weid = $_W['uniacid'];//当前公众号ID
		$giftid = intval($_GPC['giftid']);
		$rid = intval($_GPC['id']);
		if (checksubmit('delete')) {
			//重新计算密卡数量
			foreach ($_GPC['select'] as $value) {
			    $gift_id = pdo_fetch("SELECT giftid FROM ".tablename($this->table_giftmika)." WHERE id = :id ", array(':id' => $value));
			    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_giftmika).' WHERE giftid=:giftid',array(':giftid' => $gift_id['giftid']));
				pdo_delete($this->table_giftmika, array('id' => $value));
				pdo_update($this->table_gift, array('total' => $total-1), array('id' => $gift_id['giftid']));				
			}
			//重新计算密卡数量
			message('删除成功！', create_url('site/entry/awardmika', array('m' => 'stonefish_chailihe', 'id' => $rid, 'page' => $_GPC['page'])));
		}
		$Where = '';
		if(!empty($rid)){
		    $reply = pdo_fetch("SELECT title FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$guize = $reply['title'];
			$Where .= " AND `rid` = '".$rid."'";
		}		
		!empty($giftid) && $where .= " AND a.giftid='".$giftid."'";
		!empty($_GPC['keywordtype']) && $where .= " AND a.typename LIKE '%{$_GPC['keywordtype']}%'";
		!empty($_GPC['keywordmika']) && $where .= " AND a.mika LIKE '%{$_GPC['keywordmika']}%'";
		!empty($_GPC['keywordid']) && $where .= " AND a.rid = '{$_GPC['keywordid']}'";
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		//取得所有密卡数据
		$list_giftmika = pdo_fetchall('SELECT a.* FROM '.tablename($this->table_giftmika).' as a left join '.tablename($this->table_reply).' as b on a.rid=b.rid WHERE b.weid='.$weid.' '.$where.' order by `id` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize);
		//查询礼盒相关开始
		foreach ($list_giftmika as $mid => $list) {
		    $gift = pdo_fetch("SELECT lihetitle,title FROM ".tablename($this->table_gift)." WHERE id = :id ", array(':id' => $list['giftid']));
			$list_giftmika[$mid]['lihetitle'] = $gift['lihetitle'];
			$list_giftmika[$mid]['title'] = $gift['title'];
			if(!empty($list['from_user'])){
			    $userinfo = pdo_fetch("SELECT realname,mobile FROM ".tablename($this->table_list)." WHERE from_user=:from_user and mikaid=:mikaid", array(':from_user' => $list['from_user'],':mikaid' => $list['id']));
				$list_giftmika[$mid]['realname'] = $userinfo['realname'].'('.$userinfo['mobile'].')';
			}
		}
		//查询礼盒相关结束
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_giftmika).' as a left join '.tablename($this->table_reply).' as b on a.rid=b.rid WHERE b.weid='.$weid.' '.$where.'');
		$pager = pagination($total, $pindex, $psize);
		include $this->template('mika');
	}
	public function doWebxuniprize() {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];//当前公众号ID
		$rid = $_GPC['id'];
		$action = $_GPC['action'];
		
		if($action=='add') {
		    //插入数据
		    $now = time();
			//查询已添加过的内定中奖人员
			$keyword = '内定中奖人员';
			$ndnum = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_list)." WHERE weid=:weid and rid=:rid and from_user LIKE '%{$keyword}%'",array(':weid' => $weid,':rid' => $rid));
			$ndnum = $ndnum+1;
			//查询已添加过的内定中奖人员
		    $insertdata = array(
			    'weid'      => $weid,
			    'from_user' => '内定中奖人员'.$ndnum,
				'avatar'    => './source/modules/stonefish_chailihe/template/images/avatar.png',
			    'nickname'  => $_GPC['nickname'],
				'realname'  => $_GPC['realname'],
				'mobile'    => $_GPC['mobile'],
				'qq'     => $_GPC['qq'],
				'email'     => $_GPC['email'],
				'address'   => $_GPC['address'],
			    'rid'       => $rid,			
 			    'zhongjiang'=> 1,
				'openlihe'  => 1,
				'liheid'    => $_GPC['liheid'],
				'sharetime' => $now,
			    'datatime'  => $now
		    );
		    pdo_insert($this->table_list, $insertdata);
			$uid = pdo_insertid();//取id
			//判断是否为密卡
			$inkind = pdo_fetch("SELECT inkind FROM ".tablename($this->table_gift)." WHERE id = :id", array(':id' => $_GPC['liheid']));
			if($inkind['inkind']==0){
			    $mikaid = pdo_fetch("SELECT id FROM ".tablename($this->table_giftmika)." WHERE status=0 and giftid=:giftid ORDER BY id asc",array(':giftid' => $_GPC['liheid']));
				pdo_update($this->table_list,array('mikaid' => $mikaid['id']),array('id' => $uid));
				pdo_update($this->table_giftmika,array('from_user' => '内定中奖人员'.$ndnum,'status' => 1),array('id' => $mikaid['id']));
			}
			//判断是否为密卡
		    message('成功添加虚拟中奖用户数据', create_url('site/entry/prizedata', array('m' => 'stonefish_chailihe', 'id' => $rid)));
		}
		if(!empty($rid)){
		    $reply = pdo_fetch("SELECT title FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$guize = $reply['title'];
		}
		//所有奖项
		$list_praise = pdo_fetchall("SELECT * FROM ".tablename($this->table_gift)." WHERE rid =:rid order by `id` desc", array(':rid' => $rid));
		foreach ($list_praise as $mid => $list) {
            $list_praise[$mid]['total_winning'] = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE weid=:weid and rid=:rid and liheid=:liheid and zhongjiang>=1',array(':weid' => $weid,':rid' => $rid,':liheid' => $list['id']));
		}
		include $this->template('xuniprize');
	}
    //导出数据
	public function doWebdownload(){
		require_once 'download.php';
	}
	//导出数据
	public function doWebdownloadmika(){
		require_once 'downloadmika.php';
	}
	public function doWebDelete() {
        global $_GPC, $_W;
        $rid = intval($_GPC['rid']);
        $rule = pdo_fetch("select id, module from " . tablename('rule') . " where id = :id and uniacid=:uniacid", array(':id' => $rid, ':uniacid' => $_W['uniacid']));
        if (empty($rule)) {
            message('抱歉，要修改的规则不存在或是已经被删除！');
        }
        if (pdo_delete('rule', array('id' => $rid))) {
            pdo_delete('rule_keyword', array('rid' => $rid));
            //删除统计相关数据
            pdo_delete('stat_rule', array('rid' => $rid));
            pdo_delete('stat_keyword', array('rid' => $rid));
            //调用模块中的删除
            $module = WeUtility::createModule($rule['module']);
            if (method_exists($module, 'ruleDeleted')) {
                $module->ruleDeleted($rid);
            }
        }
        message('活动删除成功！', referer(), 'success');
    }

    public function doWebDeleteAll() {
        global $_GPC, $_W;
        foreach ($_GPC['idArr'] as $k => $rid) {
            $rid = intval($rid);
            if ($rid == 0)
                continue;
            $rule = pdo_fetch("select id, module from " . tablename('rule') . " where id = :id and uniacid=:uniacid", array(':id' => $rid, ':uniacid' => $_W['uniacid']));
            if (empty($rule)) {
                $this->webmessage('抱歉，要修改的规则不存在或是已经被删除！');
            }
            if (pdo_delete('rule', array('id' => $rid))) {
                pdo_delete('rule_keyword', array('rid' => $rid));
                //删除统计相关数据
                pdo_delete('stat_rule', array('rid' => $rid));
                pdo_delete('stat_keyword', array('rid' => $rid));
                //调用模块中的删除
                $module = WeUtility::createModule($rule['module']);
                if (method_exists($module, 'ruleDeleted')) {
                    $module->ruleDeleted($rid);
                }
            }
        }
        $this->webmessage('选择中的活动删除成功！', '', 0);
    }
	public function webmessage($error, $url = '', $errno = -1) {
        $data = array();
        $data['errno'] = $errno;
        if (!empty($url)) {
            $data['url'] = $url;
        }
        $data['error'] = $error;
        echo json_encode($data);
        exit;
    }
}