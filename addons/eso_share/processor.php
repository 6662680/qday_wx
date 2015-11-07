<?php
/**
 * 分享达人模块处理程序
 */
defined('IN_IA') or exit('Access Denied');

class eso_shareModuleProcessor extends WeModuleProcessor {
	public $name = 'eso_shareModuleProcessor';
	public $table_reply = 'eso_share_reply';
	public $table_list   = 'eso_share_list';

	public function isNeedInitContext() {
		return 0;
	}

	public function respond() {
		global $_W;
		$rid = $this->rule;
		$from= $this->message['from'];
		$tag = $this->message['content'];
		$tmparray = explode('#',$tag);

		if($tag=="分享排名"){
			$eso_sharer = $this->check();
			$sql = "SELECT * FROM " . tablename($this->table_reply) . " WHERE `rid`=:rid LIMIT 1";
			$row = pdo_fetch($sql, array(':rid' => $rid));
			if (empty($eso_sharer['name']) && empty($row['isname'])) {
				$this->beginContext();
				$message ="亲，您还未绑定个人信息，请按以下格式注册个人信息，以便我们发放奖品时能够联系到你。注册格式：姓名#电话，如张三#13923456789";
				return $this->respText($message);
			}else{
				$count=pdo_fetch("SELECT count(id) as dd FROM ".tablename($this->table_list)." WHERE rid=".$rid." and eso_sharenum >= ".$eso_sharer['eso_sharenum']);
				$eso_sharepm=$count['dd'];
				$message ="亲，你分享的图文（".$row['title']."）被点击的次数为".$eso_sharer['eso_sharenum']."次，目前排名是第".$eso_sharepm."名，继续加油哦~";
				return $this->respText($message);
			}
		}else{
			$eso_sharer = $this->check();
			if(count($tmparray)>1){
				$insert = array(
					'rid' => $rid,
					'from_user' => $from,
					'name' => $tmparray[0],
					'tel' => $tmparray[1]
				);
				if(empty($eso_sharer)){
					pdo_insert($this->table_list, $insert);
					//推送分享图文内容
					$sql = "SELECT * FROM " . tablename($this->table_reply) . " WHERE `rid`=:rid LIMIT 1";
					$row = pdo_fetch($sql, array(':rid' => $rid));
					if (empty($row['id'])) {
						return array();
					}
					$now = time();
					if($now >= $row['start_time'] && $now <= $row['end_time']){
						$this->endContext();
						return $this->respNews(array(
							'Title' => $row['title'],
							'Description' => htmlspecialchars_decode($row['description']),
							'PicUrl' => $_W['attachurl'] . $row['picture'],
							'Url' => $this->createMobileUrl('eso_share', array('id' => $rid, 'from_user' => base64_encode(authcode($this->message['from'], 'ENCODE')))),
						));
					}else{
						$message = "亲，分享达人活动已结束了！";
						return $this->respText($message);
					}

				}else{
					pdo_update($this->table_list, array('name' => $tmparray[0],'tel' => $tmparray[1]), array('from_user' => $from));
				}
			}else{
				$eso_sharer = $this->check();
				$sql = "SELECT * FROM " . tablename($this->table_reply) . " WHERE `rid`=:rid LIMIT 1";
				$row = pdo_fetch($sql, array(':rid' => $rid));
				if (empty($eso_sharer['name']) && empty($row['isname'])) {
					$this->beginContext();
					$message ="亲，您还未绑定个人信息，请按以下格式注册个人信息，以便我们发放奖品时能够联系到你。注册格式：姓名#电话，如张三#13923456789";
					return $this->respText($message);
				}else{
					if (empty($row['id'])) {
						return array();
					}
					$now = time();
					if($now >= $row['start_time'] && $now <= $row['end_time']){
						$this->endContext();
						return $this->respNews(array(
							'Title' => $row['title'],
							'Description' => htmlspecialchars_decode($row['description']),
							'PicUrl' => $_W['attachurl'] . $row['picture'],
							'Url' => $this->createMobileUrl('eso_share', array('id' => $rid, 'from_user' => base64_encode(authcode($this->message['from'], 'ENCODE')))),
						));
					}else{
						$message = "亲，分享达人活动已结束了！";
						return $this->respText($message);
					}

				}
			}

		}
	}

	public function isNeedSaveContext() {
		return false;
	}
	private function check() {
		global $_W;
		$rid = $this->rule;
		$from= $this->message['from'];
		$eso_sharer = pdo_fetch("SELECT * FROM ".tablename($this->table_list)." WHERE from_user = '".$from."' and rid = '".$rid."' limit 1" );
		return $eso_sharer;

	}

}