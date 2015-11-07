<?php
	
	if($op == 'display') {
		$list = pdo_fetchall("SELECT * FROM".tablename('weilive_prizecode')."WHERE weid = ".$weid." and inkind = 1");
	} elseif($op=='inkind2'){
		$list = pdo_fetchall("SELECT * FROM".tablename('weilive_prizecode')."WHERE weid = ".$weid." and inkind = 2");
	} elseif($op=='inkind3'){
		$list = pdo_fetchall("SELECT * FROM".tablename('weilive_prizecode')."WHERE weid = ".$weid." and inkind = 3");
	} elseif ($op == 'detail'){
		$item = pdo_fetch("SELECT * FROM".tablename('weilive_prizecode')."WHERE id='{$_GPC['id']}'");
		$row = pdo_fetch("SELECT f.*, m.address, m.mobile, m.email FROM".tablename("mc_mapping_fans")." as f left join ".tablename('mc_members')." as m on f.uid = m.uid WHERE f.openid='{$item['openid']}'");
	} elseif ($op == 'delete') {
		pdo_delete('weilive_prizecode',array('id' => $_GPC['id']));
		message("删除成功",referer(),'success');
	} elseif ($op == 'mail'){
		$id = intval($_GPC['id']);
		$status = $_GPC['status'];
		if($status == 1){
			if($_GPC['inkind2']==2){
				$temp = 1;
			} else {
				$temp = ihttp_email($_GPC['mail_to'], $_GPC['title1'].'的发货提醒', $_GPC['content']);
			}
			if ($temp) {
				//print_r("UPDATE ".tablename($this->prizecode)."SET status= '1' WHERE id = '{$id}' AND weid = '{$_W['weid']}'");exit;
				$sql = "UPDATE ".tablename('weilive_prizecode')."SET status= ".$status." WHERE id = '{$id}' AND weid = ".$weid;
				pdo_query($sql);
				message('发送成功',referer(),'success');
			} else {
				message('发送失败,请检查邮箱!',referer(),'success');
			}
		} else {
			message('提交成功',referer(),'success');
		}
	}
	 
	include $this->template('web/credit_request');
?>