<?php
/**
 * 微足浴模块订阅器
 *
 * @author author by fan
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Foot_bathModuleReceiver extends WeModuleReceiver {
	public function receive() {
		$type = $this->message['type'];
		//这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看情天文档来编写你的代码
	}
}