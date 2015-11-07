<?php
	$actid = $_GPC['actid'];
	if(checksubmit('submit')){
		$complain = pdo_fetch("select s.title as storetitle, a.storeid, a.title as acttitle from ".tablename('weilive_activity')." as a left join ".tablename('weilive_stores')." as s on a.weid = s.weid and a.storeid = s.id where a.ischeck = 1 and a.isopen = 1 and a.id = ".$actid);
		$insert = array(
			'weid'=>$weid,
			'actid'=>$actid,
			'storeid'=>$complain['storeid'],
			'acttitle'=>$complain['acttitle'],
			'storetitle'=>$complain['storetitle'],
			'mobile'=>$_GPC['mobile'],
			'content'=>trim($_GPC['content']),
			'createtime'=>time(),
		);
		$temp = pdo_insert('weilive_complain', $insert);
		if($temp){
			message('投诉成功，感谢您宝贵的意见！', $this->createMobileUrl('activity', array('id'=>$actid, 'op'=>'detail')), 'success');
		} else {
			message('投诉失败！');
		}
	}
	include $this->template('complain');
?>