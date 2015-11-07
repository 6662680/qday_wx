<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

class NewsModule extends WeModule {
	public $tablename = 'news_reply';
	public $replies = array();

	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		load()->func('tpl');
		$replies = pdo_fetchall("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `displayorder` DESC", array(':rid' => $rid));
		foreach($replies as &$reply) {
			if(!empty($reply['thumb'])) {
				$reply['src'] = tomedia($reply['thumb']);
			}
		}
		include $this->template('display');
	}
	
	public function fieldsFormValidate($rid = 0) {
		global $_GPC;
		if(empty($_GPC['titles'])) {
			return '必须填写有效的回复内容.';
		}
		foreach($_GPC['titles'] as $k => $v) {
			$row = array();
			if(empty($v)) {
				continue;
			}
			$row['title'] = $v;
			$row['id'] = $_GPC['id'][$k];
			$row['author'] = $_GPC['authors'][$k];
			$row['displayorder'] = $_GPC['displayorder'][$k];
			$row['thumb'] = $_GPC['thumbs'][$k];
			$row['description'] = $_GPC['descriptions'][$k];
			$row['content'] = $_GPC['contents'][$k];
			$row['url'] = $_GPC['urls'][$k];
			$row['incontent'] = intval($_GPC['incontent-flag'][$k]);
			$row['createtime'] = time();
			$this->replies[] = $row;
		}
		if(empty($this->replies)) {
			return '必须填写有效的回复内容.';
		}
		foreach($this->replies as &$r) {
			if (trim($r['title']) == '') {
				return '必须填写有效的标题.';
			}
			if (trim($r['thumb']) == '') {
				return '必须填写有效的封面链接地址.';
			}
			$r['content'] = htmlspecialchars_decode($r['content']);
		}
		return '';
	}
	
	public function fieldsFormSubmit($rid = 0) {
		$sql = 'SELECT `id` FROM ' . tablename($this->tablename) . " WHERE `rid` = :rid";
		$replies = pdo_fetchall($sql, array(':rid' => $rid), 'id');
		$replyids = array_keys($replies);
		foreach($this->replies as $reply) {
			if (in_array($reply['id'], $replyids)) {
				pdo_update($this->tablename, $reply, array('id' => $reply['id']));
			} else {
				$reply['rid'] = $rid;
				pdo_insert($this->tablename, $reply);
			}
			unset($replies[$reply['id']]);
		}
		if (!empty($replies)) {
			$replies = array_keys($replies);
			$replies = implode(',', $replies);
			$sql = 'DELETE FROM '. tablename($this->tablename) . " WHERE `id` IN ({$replies})";
			pdo_query($sql);
		}
		return true;
	}
	
	public function ruleDeleted($rid = 0) {
		pdo_delete($this->tablename, array('rid' => $rid));
		return true;
	}
}