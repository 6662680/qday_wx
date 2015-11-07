<?php
global $_GPC, $_W;
checkauth();		
		$rid = intval($_GPC['rid']);
		
		$uid = $_W['member']['uid'];
		
		$current_date = date('Y-m-d');
		
		$showrank = isset($this->module['config']['showrank']) ? $this->module['config']['showrank'] : 10;

		$top = pdo_fetchall("SELECT * FROM ".tablename('nsign_record')." WHERE rid = :rid AND sign_time >= :current_date ORDER BY today_rank ASC LIMIT {$showrank}", array(':rid' => $rid, ':current_date' => strtotime($current_date) ));
		
		if (!empty($rid)) {
		
			$reply = pdo_fetch("SELECT * FROM ".tablename('nsign_reply')." WHERE rid = :rid ", array(':rid' => $rid));			
 		
		}
		
		$Picurl = $_W['attachurl'].$reply['picture'];
		
		include $this->template('top');