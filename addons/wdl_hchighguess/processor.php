<?php
/**
 * 我画你猜模块处理程序
 *
 */
defined('IN_IA') or exit('Access Denied');

class wdl_hchighguessModuleProcessor extends WeModuleProcessor {
	
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看情天文档来编写你的代码
		$reply = pdo_fetch("SELECT * FROM ".tablename('wdl_hchighguess_reply')." WHERE rid = :rid", array(':rid' => $this->rule));
		if (!empty($reply)) {
			$response[] = array(
				'title' => $reply['title'],
				'description' => $reply['description'],
				'picurl' => $reply['cover'],
				'url' => $this->createMobileUrl('index', array('rid' => $reply['rid'])),
			);
			return $this->respNews($response);
		}
	}
}