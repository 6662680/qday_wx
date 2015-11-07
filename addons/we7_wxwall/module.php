<?php
/**
 * 微信墙模块
 *
 * [WeEngine System] Copyright (c) 2013 qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

class We7_wxwallModule extends WeModule {
	public $tablename = 'wxwall_reply';

	/**
	 * 规则表单附加额外字段
	 */
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		
		$accounts = uni_accounts();
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$reply['syncwall'] = unserialize($reply['syncwall']);
		} else {
			$reply = array(
				'isshow' => 0,
				'timeout' => 0,
			);
		}
		
		load()->func('tpl');
		include $this->template('form');
	}

	/**
	 * 保存规则前调用, 验证附加字段有效性
	 */
	public function fieldsFormValidate($rid = 0) {
		return true;
	}

	/**
	 * 规则保存成功后执行此方法,保存附加字段入库
	 */
	public function fieldsFormSubmit($rid = 0) {
		global $_GPC, $_W;
		$id = intval($_GPC['reply_id']);
		$insert = array(
			'rid' => $rid,
			'acid'=> $_GPC['acid'],
			'enter_tips' => $_GPC['enter-tips'],
			'quit_tips' => $_GPC['quit-tips'],
			'send_tips' => $_GPC['send-tips'],
			'timeout' => $_GPC['timeout'],
			'isshow' => intval($_GPC['isshow']),
			'quit_command' => $_GPC['quit-command'],
			'logo' => $_GPC['logo'],
			'background' => $_GPC['background'],
			'syncwall' => array(
				'tx' => array(
					'status' => intval($_GPC['walls']['tx']['status']),
					'subject' => $_GPC['walls']['tx']['subject'],
				),
			),
		);
		
		$insert['syncwall'] = serialize($insert['syncwall']);
		if (empty($id)) {
			pdo_insert($this->tablename, $insert);
		} else {
			pdo_update($this->tablename, $insert, array('id' => $id));
		}
	}

	/**
	 * 卸载模块时执行的附加数据库清理操作
	 */
	public function ruleDeleted($rid = 0) {

	}
}
