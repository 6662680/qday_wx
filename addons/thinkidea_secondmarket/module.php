<?php
/**
 * 二手市场模块定义
 *
 * @author 
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class thinkidea_SecondmarketModule extends WeModule {
	
	public $table_reply = 'thinkidea_secondmarket_reply';
	
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE acid = :acid AND rid = :rid ORDER BY `id` DESC", array(':acid' => $_W['uniacid'], ':rid' => $rid));
		}
		load()->func('tpl');
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0) {
		return '';
	}

	public function fieldsFormSubmit($rid) {
		global $_GPC, $_W;
		$id = intval($_GPC['reply_id']);
		$data = array(
			'rid' => $rid,
			'acid' => $_W['uniacid'],
			'title' => $_GPC['title'],
			'avatar' => $_GPC['avatar'],
			'description' => $_GPC['description'],
			'dateline' => time(),
		);
        
		if(empty($id)) {
			pdo_insert($this->table_reply, $data);
		}else {
			pdo_update($this->table_reply, $data, array('id' => $id));
		}
	}

	public function ruleDeleted($rid) {
		global $_W;
		load()->func('file');
		$replies = pdo_fetchall("SELECT id, avatar FROM ".tablename($this->table_reply)." WHERE rid = :rid", array(':rid' => $rid));
		$deleteid = array();
		if (!empty($replies)) {
			foreach ($replies as $index => $row) {
				file_delete($row['avatar']);
				$deleteid[] = $row['id'];
			}
		}
		pdo_delete($this->table_reply, "id IN ('".implode("','", $deleteid)."')");
		return true;
	}

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		load()->func('tpl');
		if(checksubmit()) {
			$cfg = array(
				'sider1' => $_GPC['sider1'],
				'sider2' => $_GPC['sider2'],
				'sider3' => $_GPC['sider3'],
			);
			$this->saveSettings($cfg);
			message('保存成功', 'refresh');
		}
		include $this->template('setting');
	}

}