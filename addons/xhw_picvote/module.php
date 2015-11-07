<?php
/**
 * 图片投票模块定义
 *
 * @author 小黑屋
 * @url http://www.qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

class xhw_picvoteModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		load()->func('tpl');
		$activity = pdo_fetchall("SELECT * FROM " . tablename('xhw_picvote') . " WHERE weid = :weid", array(':weid' => $_W['uniacid']));
		if($rid) {
			$activity = pdo_fetchall("SELECT * FROM " . tablename('xhw_picvote') . " WHERE rid = :rid", array(':rid' => $rid));
		}
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0) {
		return '';
	}

	public function fieldsFormSubmit($rid) {
		global $_GPC;
		$id = intval($_GPC['activity']);
		$d = $_GPC['datelimit'];
		$record = array();
		$record['rid'] = $rid;
	  $record['starttime'] = strtotime($d['start']);
    $record['endtime'] = strtotime($d['end']);
		$reply = pdo_fetch("SELECT * FROM " . tablename('xhw_picvote') . " WHERE id = :id", array(':id' => $id));
		if($reply) {
			pdo_update('xhw_picvote', $record, array('id' => $reply['id']));
		}
	}
	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
	}


}