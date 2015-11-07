<?php
global $_GPC, $_W;
checkauth();		
		$rid = intval($_GPC['rid']);
		
		$uid = $_W['member']['uid'];
		
		$add = pdo_fetchall("SELECT * FROM ".tablename('nsign_add')." WHERE rid = :rid ORDER BY id DESC", array(':rid' => $rid ));
		foreach ($add as &$ad){
			$ad['thumb'] = iunserializer($ad['thumb']);
		}
		if (!empty($rid)) {
		
			$reply = pdo_fetch("SELECT * FROM ".tablename('nsign_reply')." WHERE rid = :rid ", array(':rid' => $rid));			
 		
		}
		
		$Picurl = $_W['attachurl'].$reply['picture'];
        
        $type = pdo_fetchall("SELECT DISTINCT type FROM ".tablename('nsign_add')." WHERE rid = :rid ", array(':rid' => $rid ));
		
		include $this->template('add');