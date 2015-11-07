<?php

/**
 * 微餐饮模块定义
 */

defined('IN_IA') or exit('Access Denied');
class jufeng_wcyModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		load()->func('tpl');
		include $this->template('form');
	}	
	public function fieldsFormSubmit($rid = 0) {
		global $_GPC, $_W;
		if (!empty($_GPC['title'])) {
			$data = array(
				'title' => $_GPC['title'],
				'description' => $_GPC['description'],
				'picurl' => $_GPC['thumb-old'],

				'url' => create_url('mobile/module/list', array('name' => 'jufeng_wcy', 'weid' => $_W['weid'])),

			);
			if (!empty($_GPC['thumb'])) {

				$data['picurl'] = $_GPC['thumb'];

				file_delete($_GPC['thumb-old']);

			}

			$this->saveSettings($data);

		}

		return true;

	}
	public function settingsDisplay($settings) {
		global $_GPC, $_W;
		if(checksubmit()) {
			$cfg = array(

				'jufeng_wcymodel' => $_GPC['jufeng_wcymodel'],

				'noticeemail' => $_GPC['noticeemail'],
			);
			if($this->saveSettings($cfg)) {
				message('保存成功', 'refresh');
			}
		}
		include $this->template('setting');

	}

}

