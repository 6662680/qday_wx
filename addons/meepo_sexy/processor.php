<?php
/**
 * 校花校草模块处理程序
 *
 * @author meepo
 * @url http://meepo.com.cn/forum.php
 */
defined('IN_IA') or exit('Access Denied');

class Meepo_sexyModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微动力文档来编写你的代码
		global $_W;
		$settings = pdo_fetch("SELECT * FROM ".tablename('meepo_sexy_set')." WHERE weid=:weid limit 1",array(':weid'=>$_W['weid']));
		if(empty($settings)){
			return $this->respText('管理员尚未设置相关信息，请联系管理员');
		}
		$xiaohua_url = $settings['url'].$settings['num'].'/0/1';
		$xiaocao_url = $settings['url'].$settings['num'].'/1/1';
		session_start();
		
		if(!strncasecmp($_SESSION['sel'], "校花", 6)&&!strncasecmp($content, "P", 1)){
		  $lines = $_SESSION['pages']*8+8;
		  $_SESSION['pages'] = $_SESSION['pages']+1;
		  $b = array();
		  $res = file_get_contents($xiaohua_url);
		  $xiaohua = json_decode($res,true);
		  for($id=$lines;$id<$lines+8;$id++){
			$pic = "http://www.facejoking.com/pic/{$xiaohua[data][$id][pid]}.jpg";
			$url = "http://www.facejoking.com/people/{$xiaohua[data][$id][pid]}";
			$name = $xiaohua[data][$id][name];
			$rank = $xiaohua[data][$id][rank];
			if(!$name) break;
			$b[]=array("title"=>'TOP'.$rank.' '.$name,"url"=>$url,"picurl"=>$pic,"description"=>'');
		  }
		  if($xiaohua[data][$id][rank]) $b[]=array("title"=>'请回复 P 查看下一页',"url"=>'',"picurl"=>'',"description"=>'');
			else session_destroy();
			return $this->respNews($b); 
		 }else if($content == '校花'){
		  $_SESSION['sel'] = '校花';
		  $_SESSION['pages'] = 0;
		  $b = array();
		  $res = file_get_contents("http://www.facejoking.com/api/top/13003/0/1");
		  $xiaohua = json_decode($res,true);
		  $b[]=array("title"=>$settings['name'].'校花TOP100：(来自facejoking.com，非本平台观点)',"url"=>'',"picurl"=>'',"description"=>'');
		  for($id=0;$id<8;$id++){
			$pic = "http://www.facejoking.com/pic/{$xiaohua[data][$id][pid]}.jpg";
			$url = "http://www.facejoking.com/people/{$xiaohua[data][$id][pid]}";
			$name = $xiaohua[data][$id][name];
			$rank = $xiaohua[data][$id][rank];
			$b[]=array("title"=>'TOP'.$rank.' '.$name,"url"=>$url,"picurl"=>$pic,"description"=>'');
		  }
		  $b[]=array("title"=>'请回复 P 查看下一页',"url"=>'',"picurl"=>'',"description"=>'');
			return $this->respNews($b); 

		}
		if(!strncasecmp($_SESSION['sel'], "校草", 6)&&!strncasecmp($content, "P", 1)){
		  $lines = $_SESSION['pages']*8+8;
		  $_SESSION['pages'] = $_SESSION['pages']+1;
		  $b = array();
		  $res = file_get_contents($xiaocao_url);
		  $xiaohua = json_decode($res,true);
		  for($id=$lines;$id<$lines+8;$id++){
			$pic = "http://www.facejoking.com/pic/{$xiaohua[data][$id][pid]}.jpg";
			$url = "http://www.facejoking.com/people/{$xiaohua[data][$id][pid]}";
			$name = $xiaohua[data][$id][name];
			$rank = $xiaohua[data][$id][rank];
			if(!$name) break;
			$b[]=array("title"=>'TOP'.$rank.' '.$name,"url"=>$url,"picurl"=>$pic,"description"=>'');
		  }
		  if($xiaohua[data][$id][rank]) $b[]=array("title"=>'请回复 P 查看下一页',"url"=>'',"picurl"=>'',"description"=>'');
			else session_destroy();
			return $this->respNews($b); 
		  }
		else if($content == '校草'){
		  $_SESSION['sel'] = '校草';
		  $_SESSION['pages'] = 0;
		  $b = array();
		  $res = file_get_contents($xiaocao_url);
		  $xiaohua = json_decode($res,true);
		  $b[]=array("title"=>$settings['name'].'校草TOP100：(来自facejoking.com，非本平台观点)',"url"=>'',"picurl"=>'',"description"=>'');
		  for($id=0;$id<8;$id++){
			$pic = "http://www.facejoking.com/pic/{$xiaohua[data][$id][pid]}.jpg";
			$url = "http://www.facejoking.com/people/{$xiaohua[data][$id][pid]}";
			$name = $xiaohua[data][$id][name];
			$rank = $xiaohua[data][$id][rank];
			$b[]=array("title"=>'TOP'.$rank.' '.$name,"url"=>$url,"picurl"=>$pic,"description"=>'');
		  }
		  $b[]=array("title"=>'请回复 P 查看下一页',"url"=>'',"picurl"=>'',"description"=>'');
			return $this->respNews($b); 

		}
		else return $this->respText('error');
	}
}