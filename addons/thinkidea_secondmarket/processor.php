<?php
/**
 * 二手市场模块处理程序
 *
 * @author 
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class thinkidea_SecondmarketModuleProcessor extends WeModuleProcessor {
	public $tablename = 'thinkidea_secondmarket_reply';
	public function respond() {
		global $_GPC, $_W;
		$rid = $this->rule;
		$message = $this->message;
		$from = $message['from'];
		$profile = fans_search($from);
		
		$sql = "SELECT * FROM " . tablename($this->tablename) . " WHERE `rid`=:rid LIMIT 1";
		$row = pdo_fetch($sql, array(':rid' => $rid));
		if (empty($row['id'])) {
			return array();
		}
		
		return $this->respNews(array(
			'Title' => $row['title'],
			'Description' => $row['description'],
			'PicUrl' => $row['avatar'],
			'Url' => $this->createMobileUrl('list', array('rid' => $rid)),
		));
	}
}