<?php
/**
 * 朋友圈发语音模块处理程序
 *
 * @author 
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Czt_voiceModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看情天文档来编写你的代码
	}
}