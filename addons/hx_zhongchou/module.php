<?php
/**
 * 众筹模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Hx_zhongchouModule extends WeModule {

	public function settingsDisplay($settings) {
        global $_GPC, $_W;
        
        if (checksubmit()) {
            $cfg = array(
                'noticeemail' => $_GPC['noticeemail'],
                'shopname' => $_GPC['shopname'],
                'address' => $_GPC['address'],
                'phone' => $_GPC['phone'],
                'officialweb' => $_GPC['officialweb'],
                'description'=>  htmlspecialchars_decode($_GPC['description'])
            );
            if (!empty($_GPC['logo'])) {
                $cfg['logo'] = $_GPC['logo'];
            }
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
		load()->func('tpl');
		include $this->template('setting');
    }

}