<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

class NewsModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W;
		$rid = $this->rule;		
		$sql = "SELECT * FROM " . tablename('news_reply') . " WHERE rid = :id ORDER BY displayorder DESC, id ASC LIMIT 8";
		$commends = pdo_fetchall($sql, array(':id'=>$rid));
		if (empty($commends)) {
			return false;
		}
		$news = array();
		foreach($commends as $c) {
			$row = array();
			$row['title'] = $c['title'];
			$row['description'] = $c['description'];
			!empty($c['thumb']) && $row['picurl'] = tomedia($c['thumb']);
			$row['url'] = empty($c['url']) ? $this->createMobileUrl('detail', array('id' => $c['id'])) : $c['url'];
			$news[] = $row;
		}
		return $this->respNews($news);
	}
}
