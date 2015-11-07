<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
$reply = pdo_fetch('SELECT * FROM '.tablename($this->table_reply).' WHERE uniacid= :uniacid AND rid =:rid ', array(':uniacid' => $uniacid, ':rid' => $rid) );
		
		
			
			if (checksubmit('delete')) {
				pdo_delete($this->table_users, " id IN ('".implode("','", $_GPC['select'])."')");
				message('删除成功！', create_url('site/module', array('do' => 'members', 'name' => 'fm_photosvote', 'rid' => $rid, 'page' => $_GPC['page'], 'foo' => 'display')));
			}
			$where = '';
			//!empty($_GPC['keyword']) && $where .= " AND nickname LIKE '%{$_GPC['keywordnickname']}%'";
			if (!empty($_GPC['keyword'])) {
				$keyword = $_GPC['keyword'];
				
				$where .= " AND (id LIKE '%{$keyword}%' OR nickname LIKE '%{$keyword}%' OR mobile LIKE '%{$keyword}%' OR photoname LIKE '%{$keyword}%') ";					
				//$where .= " OR nickname LIKE '%{$keyword}%'";
				//$where .= " OR mobile LIKE '%{$keyword}%'";
				//$where .= " OR photoname LIKE '%{$keyword}%'";
			}
			!empty($rid) && $where .= " AND rid = '{$rid}'";

			$where .= " AND status = '1'";
			
			$pindex = max(1, intval($_GPC['page']));
			$psize = 15;

			//取得用户列表
			$members = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid '.$where.' order by `id` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $uniacid) );
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid '.$where.' ', array(':uniacid' => $uniacid));
			$pager = pagination($total, $pindex, $psize);
			$sharenum = array();
			foreach ($members as $mid => $m) {
				$sharenum[$mid] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_data)." WHERE uniacid = :uniacid and tfrom_user = :tfrom_user and rid = :rid", array(':uniacid' => $uniacid,':tfrom_user' => $m['from_user'],':rid' => $rid)) + pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_data)." WHERE uniacid = :uniacid and fromuser = :fromuser and rid = :rid", array(':uniacid' => $uniacid,':fromuser' =>$m['from_user'], ':rid' => $rid)) + $m['sharenum'];
			}
		
			include $this->template('members');
