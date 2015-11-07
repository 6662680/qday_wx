<?php
global $_GPC, $_W;
		
		$rid = intval($_GPC['rid']);
		
		$fromuser = $_W['fans']['from_user'];
		
		$condition = '';
		
		if ($_POST['sel'] == 'all'){
		
            $condition = '';
		
		}
		else{
		
            $condition .= " AND type = '{$_POST['sel']}' ";
		
		}

		
		$add = pdo_fetchall("SELECT * FROM ".tablename('nsign_add')." WHERE rid = :rid $condition ORDER BY id DESC", array(':rid' => $rid ));

		if (!empty($rid)) {
		
			$reply = pdo_fetch("SELECT * FROM ".tablename('nsign_reply')." WHERE rid = :rid ", array(':rid' => $rid));			
 		
		}
		
		$Picurl = $_W['attachurl'].$reply['picture'];
		
		$data = array(

			'msg' => $add,
			
			'status' => 1,
		
		);
		
		$msg = json_encode($data);
		
		return $msg;