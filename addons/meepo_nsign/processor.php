<?php
/*
 * 
 *
 */
defined('IN_IA') or exit('Access Denied');

class Meepo_nsignModuleProcessor extends WeModuleProcessor {

	public function isNeedInitContext() {
		return 0;
	}
	
	public function respond() {
	
		global $_GPC, $_W;
		
		$rid = $this->rule;
		
		$message = $this->message;

		$from = $message['from'];
		
		$profile = fans_search($from);
		
		$sql = "SELECT * FROM " . tablename('nsign_reply') . " WHERE `rid`=:rid LIMIT 1";
		
		$row = pdo_fetch($sql, array(':rid' => $rid));
		
		if (empty($row['id'])) {
		
			return array();
		}
		
		return $this->respNews(array(
		
			'Title' => $row['title'],
			
			'Description' => $tips.$row['description'],
			
			'PicUrl' => $row['picture'],
			
			'Url' => $this->createMobileUrl('index', array('rid' => $rid)),
		
		));

	}

	public function isNeedSaveContext() {
		return false;
	}
}