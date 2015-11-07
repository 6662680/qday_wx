<?php
/**
 * community模块处理程序
 *
 * @author 
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Xfeng_communityModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W;
		echo $content = $this->message['content'];
		exit;
	}
}