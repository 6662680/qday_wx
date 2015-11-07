<?php
global $_GPC, $_W;
checkauth();		
		$rid = intval($_GPC['rid']);
		
		$uid = $_W['member']['uid'];
		
		$realname = $_POST['realname'];
		
		$mobile = $_POST['mobile'];
		
		if(!empty($realname) && !empty($mobile)){
		
			$info = array(
		
				'realname' => $realname,
				
				'mobile' => $mobile,
		
			);
			
			mc_update($uid, $info);
			
			$status = 1;
			
			$url = $this->createMobileUrl('index', array('rid' => $rid));
			
			$tip = '注册成功';
		
		}
		else{
		
			$status = 0;
			
			$tip = '注册失败';
		
		}

		$data = array(

			'msg' => $tip,
			
			'status' => $status,
			
			'url' => $url,
		
		);
		
		$msg = json_encode($data);
		
		//print_r($_POST['realname']);
		
		die($msg);