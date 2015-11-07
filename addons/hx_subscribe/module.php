<?php
/**
 * 推荐关注模块定义
 *
 * @author 华轩科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Hx_subscribeModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		$creditnames = uni_setting($_W['uniacid'], array('creditnames'));
		if($creditnames) {
			foreach($creditnames['creditnames'] as $index=>$creditname) {
				if($creditname['enabled'] == 0) {
					unset($creditnames['creditnames'][$index]);
				}
			}
			$scredit = implode(', ', array_keys($creditnames['creditnames']));
		} else {
			$scredit = '';
		}
		if(checksubmit()) {
			$cfg = array(
				'credit_type' => $_GPC['credit_type'],
				'credit_subscribe' => intval($_GPC['credit_subscribe']),
				'credit_lever_1' => intval($_GPC['credit_lever_1']),
				'credit_lever_2' => intval($_GPC['credit_lever_2']),
				'out_limit' => intval($_GPC['out_limit']),
				'start_num' => intval($_GPC['start_num']),
				);
			if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
		}
		if (empty($settings['credit_type'])) {
			$settings['credit_type'] = 'credit1';
		}
		if (empty($settings['credit_subscribe'])) {
			$settings['credit_subscribe'] = '5';
		}
		if (empty($settings['credit_lever_1'])) {
			$settings['credit_lever_1'] = '2';
		}
		if (empty($settings['credit_lever_2'])) {
			$settings['credit_lever_2'] = '1';
		}
		if (empty($settings['out_limit'])) {
			$settings['out_limit'] = '100';
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}

}