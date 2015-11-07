<?php
/**
 * 肩周炎抽奖模块
 *
 * [微动力] www.weixiamen.cn 5517286
 */
defined('IN_IA') or exit('Access Denied');

class hl_periarthritisModuleProcessor extends WeModuleProcessor {

	public $tablename = 'hl_periarthritis';
	
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$sql = "SELECT * FROM ".tablename($this->tablename)." WHERE `rid`=:rid LIMIT 1";
		$row = pdo_fetch($sql, array(':rid' => $rid));
		if (empty($row['id'])) {
			return array();
		}
		
		
		
		
		$array=array(
			'Title' => $row['title'],
			'Description' => $row['description'],
			'PicUrl' => $_W['attachurl'] . $row['picture'],
			'Url' => $this->createMobileUrl('index', array('rid' => $rid)),
		);
		
		return $this->respNews($array);
	}
}
