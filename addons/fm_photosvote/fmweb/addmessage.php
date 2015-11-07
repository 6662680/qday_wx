<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
$afrom_user = $_GPC['afrom_user'];
		$tfrom_user = $_GPC['tfrom_user'];
		
				
				
		if (checksubmit('submit')) {
		if (!empty($_GPC['content'])) {
			foreach ($_GPC['content'] as $index => $row) {
				$data = array(
					'content' => $_GPC['content'][$index],
					'rid' => $rid,
					'status' => '9',
					'createtime' => time(),
				);
				if (!empty($_GPC['nickname'][$index])) {
					$data['nickname'] = $_GPC['nickname'][$index];
				}
				if(!empty($data['content'])) {
					if(pdo_fetch("SELECT id FROM ".tablename($this->table_bbsreply)." WHERE content = :content AND id != :id", array(':content' => $data['content'], ':id' => $index))) {
						continue;
					}
					if(pdo_fetch("SELECT id FROM ".tablename($this->table_bbsreply)." WHERE nickname = :nickname AND id != :id", array(':nickname' => $data['nickname'], ':id' => $index))) {
						continue;
					}
					$row = pdo_fetch("SELECT id FROM ".tablename($this->table_bbsreply)." WHERE content = :content AND nickname = :nickname  AND rid = :rid   AND status = :status LIMIT 1",array(':content' => $data['content'],':nickname' => $data['nickname'],':rid' => $rid,':status' => '9'));
					if(empty($row)) {
						pdo_update($this->table_bbsreply, $data, array('id' => $index));
					}
					unset($row);
				}
			}
		}
		
		if (!empty($_GPC['content-new'])) {
			foreach ($_GPC['content-new'] as $index => $row) {
				$data = array(
						'uniacid' => $uniacid,
						'rid' => $rid,
						'content' => $_GPC['content-new'][$index],
						'nickname' => $_GPC['nickname-new'][$index],
						'status' => '9',
						'createtime' => time(),
				);
				if(!empty($data['content']) && !empty($data['nickname'])) {
					if(pdo_fetch("SELECT id FROM ".tablename($this->table_bbsreply)." WHERE content = :content", array(':content' => $data['content']))) {
						continue;
					}
					pdo_insert($this->table_bbsreply, $data);
					unset($row);
				}
			}
		}
		
		if (!empty($_GPC['delete'])) {
			pdo_query("DELETE FROM ".tablename($this->table_bbsreply)." WHERE id IN (".implode(',', $_GPC['delete']).")");
		}

		message('更新成功！', referer(), 'success');
	}
	$status = '9';
	$list = pdo_fetchall("SELECT * FROM ".tablename($this->table_bbsreply)." WHERE uniacid = :uniacid AND rid = :rid AND status = :status", array(':uniacid' => $uniacid, ':rid' => $rid, ':status' => $status));
		
		
		
		include $this->template('addmessage');
