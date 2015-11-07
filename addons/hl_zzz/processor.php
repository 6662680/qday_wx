<?php
/**
 * 送粽子模块
 *
 * [皓蓝] www.weixiamen.cn 5517286@qq.com
 */
defined('IN_IA') or exit('Access Denied');

class Hl_zzzModuleProcessor extends WeModuleProcessor {
	
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$sql = "SELECT * FROM " . tablename('zzz_reply') . " WHERE `rid`=:rid LIMIT 1";
		$row = pdo_fetch($sql, array(':rid' => $rid));
		if (empty($row['id'])) {
			return array();
		}
		$title = pdo_fetchcolumn("SELECT name FROM ".tablename('rule')." WHERE id = :rid LIMIT 1", array(':rid' => $rid));
		return $this->respNews(array(
			'Title' => $title,
			'Description' => $row['description'],
			'PicUrl' => $_W['attachurl'] . $row['picture'],
			'Url' => $this->createMobileUrl('introduce', array('id' => $rid)),
		));
	}
}
