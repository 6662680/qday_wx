<?php

/**
 * 幸运拆礼盒模块定义
 *
 * @author 情天
 */
defined('IN_IA') or exit('Access Denied');

class Stonefish_chailiheModuleProcessor extends WeModuleProcessor {

	public $table_reply = 'stonefish_chailihe_reply';
	public $table_list  = 'stonefish_chailihe_userlist';

	
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$from= $this->message['from'];
		$weid = $_W['uniacid'];//当前公众号ID				
		//推送分享图文内容
		$sql = "SELECT title,description,start_time,end_time,picture,status FROM " . tablename($this->table_reply) . " WHERE `rid`=:rid LIMIT 1";
		$row = pdo_fetch($sql, array(':rid' => $rid));
		if ($row == false) {
            return $this->respText("活动已取消...");
        }
		//查询是否被屏蔽
		$lists = pdo_fetch("SELECT status FROM ".tablename($this->table_list)." WHERE from_user = '".$from."' and weid = '".$weid."' and rid= '".$rid."' order by `status` asc" );
		if(!empty($lists)){//查询是否有记录
			if($lists['status']==0){
				$message = "亲，".$row['title']."活动中您可能有作弊行为已被管理员暂停了！请联系".$_W['account']['name']."";
				return $this->respText($message);					
			}
		}
		//查询是否被屏蔽
		//查询是否中奖
		$lists = pdo_fetch("SELECT zhongjiang FROM ".tablename($this->table_list)." WHERE from_user = '".$from."' and weid = '".$weid."' and rid= '".$rid."' order by `zhongjiang` desc" );
		if(!empty($lists)){
			if($lists['zhongjiang']==1){
				$zhongjiang = "亲！恭喜中奖了，请点击查看！";
			}
		}
		//查询是否中奖
		//查询是否开始活动
		$now = time();
		if($now < $row['start_time']){
		    $message = "亲，".$row['title']."还没有开始，请于".date("Y-m-d H:i:s", $row['start_time']) ."参加活动";
			return $this->respText($message);
		}
		//查询是否开始活动
		//查询是否结束
		if($now > $row['end_time']){
		    $zhongjiang .= "亲，".$row['title']."活动已结束了！";
		}
		//查询是否结束
		//查询是否暂停
		if ($row['status']==0){
			$zhongjiang .= "亲，".$row['title']."活动暂停了！";
		}
		//查询是否暂停
		//转换图片路径
		$picture = toimage($row['picture']);		
		//转换图片路径
		//显示图文回复内容
		return $this->respNews(array(
			'Title' => $row['title'],
			'Description' => htmlspecialchars_decode($row['description']).$zhongjiang,
			'PicUrl' => $picture,
			'Url' => $this->createMobileUrl('chailihe', array('rid' => $rid, 'chufa' => 1, 'from_user' => base64_encode(authcode($from, 'ENCODE')))),
		));
	}
}