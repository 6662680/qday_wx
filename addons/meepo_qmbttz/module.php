<?php
/**
 * 冰桶挑战模块定义
 *
 * @author meepo
 * @url http://bbs.b2ctui.com/forum.php
 */
defined('IN_IA') or exit('Access Denied');

class Meepo_qmbttzModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		$setting = $_W['account']['modules'][$this->_saveing_params['mid']]['config'];
		include $this->template('rule');
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		global $_GPC, $_W;
		load()->func('file');
		if (!empty($_GPC['title'])) {
			$data = array(
				'title' => $_GPC['title'],
				'description' => $_GPC['description'],
				'picurl' => $_GPC['thumb-old'],
				'url' => $this->createMobileUrl('list'),
			);
			if (!empty($_GPC['thumb'])) {
				$data['picurl'] = $_GPC['thumb'];
				file_delete($_GPC['thumb-old']);
			}
			$this -> saveSettings($data);
		}
		return true;
	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
	}


}