<?php
/**
 * 模块处理程序
 * @author 史中营
 * @QQ 214983937
 * @url wx.mamani.cn
 */
defined('IN_IA') or exit('Access Denied');
class Amouse_HouseModuleProcessor extends WeModuleProcessor {
	

	public function respond() {
        $content = $this->message['content'];
	}
	
	public function isNeedSaveContext() {
		return false;
	}
}