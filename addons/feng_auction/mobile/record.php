<?php
	$pro_mobile = pdo_fetch("SELECT mobile FROM ".tablename('auction_member')." WHERE uniacid = '{$weid}' and from_user ='{$_W['openid']}' ");
	if (empty($pro_mobile['mobile'])) {
		message('请完善您的资料！', $this->createMobileUrl('prodata'), 'warning');
	}
	$ar = pdo_fetchall("SELECT * FROM " . tablename('auction_record') . " WHERE uniacid = '{$weid}' and from_user ='{$_W['fans']['from_user']}' and bond > 0 ORDER BY createtime DESC ");
	$number=0;
	foreach($ar as $key=>$value) {
		$p_record[$number]=pdo_fetch("SELECT * FROM " . tablename('auction_goodslist') . " WHERE uniacid = '{$weid}' and id ='{$value['sid']}'");
		if ($p_record[$number]['end_time']<TIMESTAMP) {
			$p_record[$number]['state']=0;
			if (empty($p_record[$number]['q_uid'])) {
				$redata = pdo_fetch("SELECT * FROM " . tablename('auction_record') . " WHERE uniacid = '{$weid}' and sid ='{$p_record[$number]['id']}' ORDER BY createtime DESC limit 1");
				$data['q_uid']=$redata['nickname'];
				$data['q_user']=$redata['from_user'];
				pdo_update('auction_goodslist', $data, array('id' => $p_record[$number]['id']));
			}
		}else{
			$p_record[$number]['state']=1;
		}
		$number++;
	}
	include $this->template('record');
?>