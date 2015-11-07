<?php
/**
 * 肩周炎抽奖模块
 *
 * [微动力] www.weixiamen.cn 5517286
 */
defined('IN_IA') or exit('Access Denied');

class hl_periarthritisModule extends WeModule {
	public $tablename = 'hl_periarthritis';

	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		load()->func('tpl');
		if (!empty($rid)) {
			$item = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			
		} 
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0) {
		return true;
	}

	public function fieldsFormSubmit($rid = 0) {
		global $_GPC, $_W;
		load()->func('file');
		$id = intval($_GPC['id']);
		$insert = array(
			'rid' => $rid,
			'weid' => $_W['weid'],
			'title' => $_GPC['title'],
			'picture' => $_GPC['picture'],
			'content' => $_GPC['content'],
			'gzurl' => $_GPC['gunzhu'],
			'shaketimes' => intval($_GPC['shaketimes']),
			
		);
		if (empty($id)) {
			pdo_insert($this->tablename, $insert);
		} else {
			if (!empty($_GPC['picture'])) {
				file_delete($_GPC['picture-old']);
			} else {
				unset($insert['picture']);
			}
			pdo_update($this->tablename, $insert, array('id' => $id));
		}
		
		
	}

	public function ruleDeleted($rid = 0) {
		global $_W;
		load()->func('file');
		$replies = pdo_fetchall("SELECT id, picture FROM ".tablename($this->tablename)." WHERE rid = '$rid'");
		$deleteid = array();
		if (!empty($replies)) {
			foreach ($replies as $index => $row) {
				file_delete($row['picture']);
				$deleteid[] = $row['id'];
			}
		}
		pdo_delete($this->tablename, "id IN ('".implode("','", $deleteid)."')");
		return true;
	}
}
