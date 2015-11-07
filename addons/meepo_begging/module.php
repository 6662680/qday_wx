<?php
/**
 * 网络乞讨模块定义
 *
 * @author meepo
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Meepo_beggingModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		$row = $settings;
        if (checksubmit()) {
            $cfg = $_POST;
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
		load()->func('tpl');
		include $this->template('setting');
	}

}