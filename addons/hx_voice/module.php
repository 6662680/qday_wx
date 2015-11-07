<?php
/**
 * 普通话在线考试模块定义
 *
 * @author 华轩科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Hx_voiceModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		if(checksubmit()) {
			$cfg = array(
                's_title' => $_GPC['s_title'],
                's_content' => $_GPC['s_content'],
                's_img' => $_GPC['s_img'],
            );
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
		}
		if (empty($settings['title'])) {
            $settings['s_title'] = '你的普通话什么等级？';
            $settings['s_content'] = '普通话等级测试，你敢来试试吗？';
            $settings['s_img'] = $_W['siteroot'].'addons/hx_voice/icon.jpg';
        }
		//这里来展示设置项表单
		include $this->template('setting');
	}

}