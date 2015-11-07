<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
	
		$rb = array();
		if ($reply['tmyushe'] == 1) {
			//预设
			$ybbsreply = pdo_fetchall("SELECT * FROM ".tablename($this->table_bbsreply)." WHERE uniacid = :uniacid AND rid = :rid AND status = '9' order by `id` desc ",  array(':uniacid' => $uniacid,':rid' => $rid));		
			foreach ($ybbsreply as $r) {
				$rb[] .= $r['nickname'] . ' : ' . cutstr($r['content'], '15');
			}
		}			
		
		//评论
		$bbsreply = pdo_fetchall("SELECT * FROM ".tablename($this->table_bbsreply)." WHERE uniacid = :uniacid AND tfrom_user = :tfrom_user AND rid = :rid order by `id` desc ",  array(':uniacid' => $uniacid,':tfrom_user' => $tfrom_user,':rid' => $rid));
		if (empty($bbsreply)) {
			//预设
			$ybbsreply = pdo_fetchall("SELECT * FROM ".tablename($this->table_bbsreply)." WHERE uniacid = :uniacid AND rid = :rid AND status = '9' order by `id` desc ",  array(':uniacid' => $uniacid,':rid' => $rid));		
			foreach ($ybbsreply as $r) {
				$rb[] .= $r['nickname'] . ' : ' . cutstr($r['content'], '15');
			}
		} else {
			foreach ($bbsreply as $r) {
				$rb[] .= $r['nickname'] . ' : ' . cutstr($r['content'], '15');
			}
		}
		
		echo json_encode($rb);
		exit();	