<?php
/**
 * 微相册模块处理程序
 *
 * @author WeEngine Team
 * @url http://www.qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

class We7_albumModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看情天文档来编写你的代码
		$reply = pdo_fetchall("SELECT * FROM ".tablename('album_reply')." WHERE rid = :rid", array(':rid' => $this->rule));
		if (!empty($reply)) {
			foreach ($reply as $row) {
				$albumids[$row['albumid']] = $row['albumid'];
			}
			$album = pdo_fetchall("SELECT id, title, thumb, content FROM ".tablename('album')." WHERE id IN (".implode(',', $albumids).")", array(), 'id');
			$response = array();
			foreach ($reply as $row) {
				$row = $album[$row['albumid']];
				$response[] = array(
					'title' => $row['title'],
					'description' => $row['content'],
					'picurl' => toimage($row['thumb']),
					'url' => $this->buildSiteUrl($this->createMobileUrl('detail', array('id' => $row['id']))),
				);
			}
			return $this->respNews($response);
		}
	}
}