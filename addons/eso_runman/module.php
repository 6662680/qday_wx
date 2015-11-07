<?php
/**
 * 新年拼暖值 模块定义
 *
 */
defined('IN_IA') or exit('Access Denied');
include_once "function.php";

class Eso_RunmanModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
		global $_W;
		load()->func('tpl');
		$reply = pdo_fetch("SELECT * FROM ".tablename('eso_runman_reply')." WHERE rid = :rid", array(':rid' => $rid));
		if (empty($reply)) {
			$reply['starttime'] = time();
			$reply['endtime'] = time() + 2592000;
			$reply['setting'] = array();
		}else{
			$reply['setting'] = string2array($reply['setting']);
		}
		$sql = "SELECT * FROM " . tablename('uni_account');
		$uniaccounts = pdo_fetchall($sql);
		$accounts = array();
		if(!empty($uniaccounts)) {
			foreach($uniaccounts as $uniaccount) {
				$accountlist = uni_accounts($uniaccount['uniacid']);
				if(!empty($accountlist)) {
					foreach($accountlist as $account) {
						if(!empty($account['key'])
							&& !empty($account['secret'])
							&& in_array($account['level'], array(3, 4))) {
							$accounts[$account['acid']] = $account['name'];
						}
					}
				}
			}
		}
		include $this->template('form');
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
		global $_W, $_GPC;
		$reid = intval($_GPC['reply_id']);
		
		$data = array(
			'rid' => $rid,
			'title' => $_GPC['title'],
			'thumb' => $_GPC['thumb'],
			'description' => $_GPC['description'],
			'background' => $_GPC['background'],
			'content' => $_GPC['content'],
			'share_title' => $_GPC['share_title'],
			'share_url' => $_GPC['share_url'],
			'share_txt' => $_GPC['share_txt'],
			'share_desc' => $_GPC['share_desc'],
			'mp3' => $_GPC['mp3'],
			'setting' => array2string($_GPC['setting']),
			'regular' => $_GPC['regular'],
			'starttime' => strtotime($_GPC['datelimit']['start']),
			'endtime' => strtotime($_GPC['datelimit']['end'])
		);
		if (empty($reid)) {
			pdo_insert('eso_runman_reply', $data);
		} else {
			pdo_update('eso_runman_reply', $data, array('id' => $reid));
		}
	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则
		pdo_delete('eso_runman_reply', array('rid' => $rid));
		pdo_delete('eso_runman_users', array('rid' => $rid));
		pdo_delete('eso_runman_submit', array('rid' => $rid));
	}
}