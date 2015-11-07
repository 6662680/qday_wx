<?php
/**
 * 朋友圈发语音模块微站定义
 *
 * @author 
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Czt_voiceModuleSite extends WeModuleSite {

	public function doMobileIndex() {
		//这个操作被定义用来呈现 功能封面
		global $_W, $_GPC;
		$settings=$this->module['config'];
		// var_dump($_W['account']['jssdkconfig']);die();
		$agent = $_SERVER['HTTP_USER_AGENT'];
    	//if(!preg_match('/MicroMessenger/i',$agent)) die();
    	if(preg_match('/Android/i',$agent)) $isAndroid='true';
    	else $isAndroid='false';
    	if ($_GPC['serverId']&&$_GPC['serverId']!='undefined'&&$_GPC['serverId']!='') include $this->template('record');
    	else include $this->template('index');
	}

	public function doMobileSaverecord() {
		header('Content-type: application/json');
		$data=json_encode(array('ret'=>0,'serverId'=>$_POST['serverId']));
		die($data);
	}

}