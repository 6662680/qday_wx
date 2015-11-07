<?php
/**
 * 微名片
 *
 */
defined('IN_IA') or exit('Access Denied');

class Lxy_buscardModule extends WeModule {

	
	public $cardtable='lxy_bussiness_card';
	public $coptable='lxy_bussiness_card_cop';
	public $classtable='lxy_bussiness_card_class';
	public $table_reply='lxy_bussiness_card_reply';
	
	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
		global $_W;
		$uniacid=$_W['uniacid'];
		$cards = pdo_fetchall("SELECT id,username FROM ".tablename($this->cardtable)." WHERE uniacid = :uniacid ORDER BY `id` DESC", array(':uniacid' => $uniacid));
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		}
		include $this->template('form');
	
	}
	
	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}
	
	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
		global $_GPC, $_W;
		$id = intval($_GPC['reply_id']);
		$insert = array(
				'rid' => $rid,
				'title' => $_GPC['title'],
				'picture' => $_GPC['picture'],				
				'description' => $_GPC['description'],
				'status' => $_GPC['status'],
				'cid' => $_GPC['users'],
		);
		if (empty($id)) {
			pdo_insert($this->table_reply, $insert);
		} else {
			if (!empty($_GPC['picture'])) {
				load()->func('file'); file_delete($_GPC['picture-old']);
			} else {
				unset($insert['picture']);
			}
			pdo_update($this->table_reply, $insert, array('id' => $id));
		}
	
	}
	
	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
		global $_W;
		$replies = pdo_fetchall("SELECT id, picture  FROM ".tablename($this->table_reply)." WHERE rid = '$rid'");
		$deleteid = array();
                load()->func('tpl');
		if (!empty($replies)) {
			foreach ($replies as $index => $row) {
				file_delete($row['picture']);
				$deleteid[] = $row['id'];
			}
		}
		pdo_delete($this->table_reply, "id IN ('".implode("','", $deleteid)."')");
		return true;
	}
	
	
}
