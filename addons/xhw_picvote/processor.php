<?php
/**
 * 图片投票模块微站定义
 *
 * @author 小黑屋
 * @url http://www.qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

class xhw_picvoteModuleProcessor extends WeModuleProcessor {
	public function respond() {
	if(!$this->inContext && strpos($this->message['content'], '+') !== false){
		$this->beginContext(60);//锁定60秒
   		$_SESSION['ok']= 1;	
   		$_SESSION['content']= $this->message['content'];
   		$_SESSION['rand']=random(4,true);
   		return $this->respText("为防止恶意刷票，请回复验证码：".$_SESSION['rand']);	
		}elseif($_SESSION['ok']== 1){
			if($this->message['content']!=$_SESSION['rand']){
				return $this->respText("验证码错误，请重新回复验证码：".$_SESSION['rand']);	
			}else{
		global $_W;
		$rid = $this->rule;
		$openid = $this->message['from'];
		$arr = pdo_fetch("SELECT * FROM " . tablename('xhw_picvote') . " WHERE rid = :rid", array(':rid' => $rid));
		$rid = $arr['id'];
		$id=$arr['id'];
		$mynum=$arr['mynum'];
		$day=$arr['day'];
		if($arr['starttime']-time()>0){
			return $this->respText("您好，活动将在".date("Y-m-d H:i:s", $arr['starttime'])."时开放投票，来先睹为快吧 => <a href='mobile.php?act=module&do=index&id=".$id."&name=xhw_picvote&weid=".$_W['uniacid']."'>活动首页</a>" ); 
		}elseif($arr['endtime']-time()<0){
			return $this->respText("您好，活动已经于".date("Y-m-d H:i:s", $arr['endtime'])."时结束，来看看排行榜吧 => <a href='mobile.php?act=module&do=top&id=".$id."&name=xhw_picvote&weid=".$_W['uniacid']."'>投票排行榜</a>"); 
		}
		$numid=explode("+",$_SESSION['content']);
		$numid=(int)$numid['1'];
		$this->endContext();
		$arr = pdo_fetch("SELECT * FROM " . tablename('xhw_picvote_reg') . " WHERE id = :id AND rid = :rid", array(':id' => $numid,':rid' => $rid));
		if(empty($arr)){return $this->respText("好像没有这个人哦，请核对ID号是否有误，去活动首页找找吧 => <a href='mobile.php?act=module&do=index&id=".$id."&name=xhw_picvote&weid=".$_W['uniacid']."'>活动首页</a>" );}
		if($arr['pass']!='1'){
			return $this->respText("您投票的用户还未通过审核，请稍后再试!要不去活动首页给其他人投票吧 => <a href='mobile.php?act=module&do=index&id=".$id."&name=xhw_picvote&weid=".$_W['uniacid']."'>活动首页</a>" );
		}
		$today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		if($day){$today =1;}
		$data=array(':openid' => $openid,':rid' => $rid,':numid' => $numid,':time' => $today);
		$arr = pdo_fetch("SELECT * FROM ".tablename('xhw_picvote_log') . " WHERE openid = :openid AND rid = :rid AND numid = :numid AND time > :time", $data);
		if(!empty($arr)){
			if($day){return $this->respText("您已经投过了，只能为同一个投票一次!" ); }
			return $this->respText("您今天已经帮TA投过啦，明天再来给TA投票吧!" ); }
        $data=array(':openid' => $openid,':rid' => $rid,':time' => $today);
        $mylognum = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('xhw_picvote_log') . " WHERE  openid = :openid AND rid = :rid AND time > :time", $data);
        if($mynum=="0"){$mynum="100000";}
        if($mylognum>=$mynum){return $this->respText("您今天已达投票上限,明天再来吧!" ); }

        	$data = array('rid' => $rid,'openid' => $openid,'numid' => $numid,'time' => time());
			pdo_insert(xhw_picvote_log, $data);	
			$arr=pdo_fetch("SELECT * FROM ".tablename('xhw_picvote_reg')." WHERE id = :id", array(':id' => $numid));
			$num=intval($arr['num'])+1;
			$data = array('num'=>$num);
			pdo_update('xhw_picvote_reg', $data, array('id' => $numid ));
      return $this->respText("您已经成功为 ".$numid."号 ".$arr['nickname']." 投了一票! => <a href='mobile.php?act=module&do=index&id=".$id."&name=xhw_picvote&weid=".$_W['uniacid']."'>活动首页</a>" );
			}
	 
		}elseif(!$this->inContext){
		global $_W;
		$rid = $this->rule;
		if($rid) {
			$arr = pdo_fetch("SELECT * FROM " . tablename('xhw_picvote') . " WHERE rid = :rid", array(':rid' => $rid));
			if($arr) {
				$news = array(
					'title' => $arr['title'],
					'description' =>$arr['smalltext'],
					'picurl' =>$arr['photo'],
					'url' => $this->createMobileUrl('index', array('id' => $arr['id'])),
				);
				return $this->respNews($news);
			}
		}
		return null;
		}
}
}