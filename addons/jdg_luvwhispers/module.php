<?php
/**
 * 情话模块定义
 *
 * @author on3
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
class Jdg_luvwhispersModule extends WeModule {

public function settingsDisplay($settings) {
		global $_W,$_GPC;
		$config = $this->module['config'];
		if(checksubmit()) {
			$config['ischeck']=empty($_GPC['ischeck'])?0:$_GPC['ischeck'];
			$config['url']=empty($_GPC['url'])?'':$_GPC['url'];
			$dat = $config;
			if($this->saveSettings($dat)) {
				message('参数设置成功', 'refresh');
			}else{
				message('参数设置错误','refresh');
			}
		}
		include $this->template('settings');
	}
}