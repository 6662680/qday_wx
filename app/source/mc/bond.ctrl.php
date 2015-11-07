<?php
/**
 * [SxxPro System] Copyright (c) 2014 SxxPro.CC
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('app');
$dos = array('display', 'credits', 'address', 'card', 'mycard', 'mobile', 'email', 'barcode', 'qrcode');
$do = in_array($do, $dos) ? $do : 'display';
load()->func('tpl');
load()->model('user');


if ($do == 'credits') {
	$where = '';
	$params = array(':uid' => $_W['member']['uid']);
	$pindex = max(1, intval($_GPC['page']));
	$psize  = 15;
	
	if (empty($starttime) || empty($endtime)) {
		$starttime =  strtotime('-1 month');
		$endtime = time();
	}
	if ($_GPC['time']) {
		$starttime = strtotime($_GPC['time']['start']);
		$endtime = strtotime($_GPC['time']['end']) + 86399;
		$where = ' AND `createtime` >= :starttime AND `createtime` < :endtime';
		$params[':starttime'] = $starttime;
		$params[':endtime'] = $endtime;
	}
	
	$sql = 'SELECT `realname`, `avatar` FROM ' . tablename('mc_members') . " WHERE `uid` = :uid";
	$user = pdo_fetch($sql, array(':uid' => $_W['member']['uid']));
	if ($_GPC['credittype']) {
		
		if ($_GPC['type'] == 'order') {
			$sql = 'SELECT * FROM ' . tablename('mc_credits_recharge') . " WHERE `uid` = :uid $where LIMIT " . ($pindex - 1) * $psize. ',' . $psize;
			$orders = pdo_fetchall($sql, $params);
			foreach ($orders as &$value) {
				$value['createtime'] = date('Y-m-d', $value['createtime']);
				$value['fee'] = number_format($value['fee'], 2);
				if ($value['status'] == 1) {
					$orderspay += $value['fee'];
				}
				unset($value);
			}
			
			$ordersql = 'SELECT COUNT(*) FROM ' .tablename('mc_credits_recharge') . "WHERE `uid` = :uid {$where}";
			$total = pdo_fetchcolumn($ordersql, $params);
			$orderpager = pagination($total, $pindex, $psize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
			template('mc/bond');
			exit();
		}
		$where .= " AND `credittype` = '{$_GPC['credittype']}'";
	}
	
	
	$sql = 'SELECT `num` FROM ' . tablename('mc_credits_record') . " WHERE `uid` = :uid $where";
	$nums = pdo_fetchall($sql, $params);
	$pay = $income = 0;
	foreach ($nums as $value) {
		if ($value['num'] > 0) {
			$income += $value['num'];
		} else {
			$pay += abs($value['num']);
		}
	}
	$pay = number_format($pay, 2);
	$income = number_format($income, 2);
	
	$sql = 'SELECT * FROM ' . tablename('mc_credits_record') . " WHERE `uid` = :uid {$where} ORDER BY `createtime` DESC LIMIT " . ($pindex - 1) * $psize.','. $psize;
	$data = pdo_fetchall($sql, $params);
	foreach ($data as $key=>$value) {
		$data[$key]['credittype'] = $creditnames[$data[$key]['credittype']]['title'];
		$data[$key]['createtime'] = date('Y-m-d H:i', $data[$key]['createtime']);
		$data[$key]['num'] = number_format($value['num'], 2);
	}
	
	$pagesql = 'SELECT COUNT(*) FROM ' .tablename('mc_credits_record') . "WHERE `uid` = :uid {$where}";
	$total = pdo_fetchcolumn($pagesql, $params);
	$pager = pagination($total, $pindex, $psize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
}


if ($do == 'address') {

	if (checksubmit('submit')) {
		$address = $_GPC['address'];
		if (pdo_update('mc_member_address', $address, array('id' => intval($_GPC['addid']), 'uid' => $_W['fans']['uid']))) {
			message('修改收货地址成功', url('mc/bond/address'), 'success');
		} else {
			message('修改收货地址失败，请稍后重试', url('mc/bond/address'), 'error');
		}
	}

	$where = ' WHERE 1';
	$params = array(':uniacid' => $_W['uniacid'], ':uid' => $_W['fans']['uid']);
	if (!empty($_GPC['addid'])) {
		$where .= ' AND `id` = :id';
		$params[':id'] = intval($_GPC['addid']);
	}
	$where .= ' AND `uniacid` = :uniacid AND `uid` = :uid';
	$sql = 'SELECT * FROM ' . tablename('mc_member_address') . $where;
	if (empty($params[':id'])) {
		$psize = 10;
		$pindex = max(1, intval($_GPC['page']));
		$sql .= ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
		$addresses = pdo_fetchall($sql, $params);
		$sql = 'SELECT COUNT(*) FROM ' . tablename('mc_member_address') . $where;
		$total = pdo_fetchcolumn($sql, $params);
		$pager = pagination($total, $pindex, $psize);
	} else {
		$address = pdo_fetch($sql, $params);
	}

}


if ($do == 'card') {
	$mcard = pdo_fetch('SELECT * FROM ' . tablename('mc_card_members') . ' WHERE uniacid = :uniacid AND uid = :uid', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if(!empty($mcard)) {
		header('Location:' . url('mc/bond/mycard'));
	}
	
	$sql = 'SELECT * FROM ' . tablename('mc_card') . "WHERE `uniacid` = :uniacid AND `status` = '1'";
	$setting = pdo_fetch($sql, array(':uniacid' => $_W['uniacid']));

	if (!empty($setting)) {
		$setting['color'] = iunserializer($setting['color']);
		$setting['background'] = iunserializer($setting['background']);
		$setting['fields'] = iunserializer($setting['fields']);
	} else {
		message('公众号尚未开启会员卡功能', url('mc'), 'error');
	}
	if(!empty($setting['fields'])) {
		$fields = array();
		foreach($setting['fields'] as $li) {
			if($li['bind'] == 'birth') {
				$fields[] = 'birthyear';
				$fields[] = 'birthmonth';
				$fields[] = 'birthday';
			} elseif($li['bind'] == 'reside') {
				$fields[] = 'resideprovince';
				$fields[] = 'residecity';
				$fields[] = 'residedist';
			} else {
				$fields[] = $li['bind'];
			}
		}
		$member_info = mc_fetch($_W['member']['uid'], $fields);
	}
	if (checksubmit('submit')) {
		$data = array();
		if (!empty($setting['fields'])) {
			foreach ($setting['fields'] as $row) {
				if (!empty($row['require']) && empty($_GPC[$row['bind']])) {
					message('请输入'.$row['title'].'！');
				}
				$data[$row['bind']] = $_GPC[$row['bind']];
			}
		}
		
		$sql = 'SELECT COUNT(*)  FROM ' . tablename('mc_card_members') . " WHERE `uid` = :uid AND `cid` = :cid AND uniacid = :uniacid";
		$count = pdo_fetchcolumn($sql, array(':uid' => $_W['member']['uid'], ':cid' => $_GPC['cardid'], ':uniacid' => $_W['uniacid']));
		if ($count >= 1) {
			message('抱歉,您已经领取过该会员卡.', referer(), 'error');
		}
		
 		$cardsn = $_GPC['format'];
		preg_match_all('/(\*+)/', $_GPC['format'], $matchs);
		if (!empty($matchs)) {
			foreach ($matchs[1] as $row) {
				$cardsn = str_replace($row, random(strlen($row), 1), $cardsn);
			}
		}
		preg_match('/(\#+)/', $_GPC['format'], $matchs);
		$length = strlen($matchs[1]);
		$pos = strpos($_GPC['format'], '#');
		$cardsn = str_replace($matchs[1], str_pad($_GPC['snpos']++, $length - strlen($number), '0', STR_PAD_LEFT), $cardsn);
		pdo_update('mc_card', array('snpos' => $_GPC['snpos']), array('uniacid' => $_W['uniacid'], 'id' => $_GPC['cardid']));
		
		$record = array(
				'uniacid' => $_W['uniacid'],
				'uid' => $_W['member']['uid'],
				'cid' => $_GPC['cardid'],
				'cardsn' => $cardsn,
				'status' => '1',
				'createtime' => TIMESTAMP
		);
		$check = mc_check($data);
		if(is_error($check)) {
			message($check['message'], '', 'error');
		}
		if(pdo_insert('mc_card_members', $record)) {
			if(!empty($data)){
				mc_update($_W['member']['uid'], $data);
			}
			message('领取会员卡成功.', url('mc/bond/mycard'), 'success');
		} else {
			message('领取会员卡失败.', referer(), 'error');
		}
	}
}


if ($do == 'mycard') {
	$mcard = pdo_fetch('SELECT * FROM ' . tablename('mc_card_members') . ' WHERE uniacid = :uniacid AND uid = :uid', array(':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
	if(empty($mcard)) {
		header('Location:' . url('mc/bond/card'));
	}
	if (!empty($mcard['status'])) {
		$setting = pdo_fetch('SELECT * FROM ' . tablename('mc_card') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
		if(!empty($setting)) {
			$setting['color'] = iunserializer($setting['color']);
			$setting['background'] = iunserializer($setting['background']);
			$setting['business'] = iunserializer($setting['business']) ? iunserializer($setting['business']) : array();
		}
	}
}


if ($do == 'barcode') {
	$cardsn = $_W['member']['uid'];
	$barcode_path = '../framework/library/barcode/';
		require_once($barcode_path . 'class/BCGFontFile.php');
	require_once($barcode_path . 'class/BCGColor.php');
	require_once($barcode_path . 'class/BCGDrawing.php');
	require_once($barcode_path . 'class/BCGcode39.barcode.php');
	$color_black = new BCGColor(0, 0, 0);
	$color_white = new BCGColor(255, 255, 255);
	
	$drawException = null;
	try {
		$code = new BCGcode39();
		$code->setScale(2);
		$code->setThickness(30);
		$code->setForegroundColor($color_black);
		$code->setBackgroundColor($color_white);
		$code->setFont($font);
		$code->parse($cardsn);
	} catch(Exception $exception) {
		$drawException = $exception;
	}
	
	$drawing = new BCGDrawing('', $color_white);
	if($drawException) {
		$drawing->drawException($drawException);
	} else {
		$drawing->setBarcode($code);
		$drawing->draw();
	}
	header('Content-Type: image/png');
	header('Content-Disposition: inline; filename="barcode.png"');
	$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}


if ($do == 'qrcode') {
	require_once('../framework/library/qrcode/phpqrcode.php');
	$errorCorrectionLevel = "L";
	$matrixPointSize = "8";
	$cardsn = $_W['member']['uid'];
	QRcode::png($cardsn, false, $errorCorrectionLevel, $matrixPointSize);
}

if($do == 'mobile') {
	$profile = mc_fetch($_W['member']['uid'], array('mobile'));
	$mobile_exist = empty($profile['mobile']) ? 0 : 1;
	if(checksubmit('submit')) {
		if($mobile_exist == 1) {
			$oldmobile = trim($_GPC['oldmobile']) ? trim($_GPC['oldmobile']) : message('请填写原手机号');
			$password = trim($_GPC['password']) ? trim($_GPC['password']) : message('请填写密码');
			$mobile = trim($_GPC['mobile']) ? trim($_GPC['mobile']) : message('请填写新手机号');
			if(!preg_match(REGULAR_MOBILE, $mobile)) {
				message('新手机号格式有误', '', 'error');
			}
			$info = pdo_fetch('SELECT uid, password, salt FROM ' . tablename('mc_members') . ' WHERE uniacid = :uniacid AND mobile = :mobile AND uid = :uid', array(':uniacid' => $_W['uniacid'], ':mobile' => $oldmobile, ':uid' => $_W['member']['uid']));
			if(!empty($info)) {
				if($info['password'] == md5($password . $info['salt'] . $_W['config']['setting']['authkey'])) {
					pdo_update('mc_members', array('mobile' => $mobile), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
					message('修改手机号成功', url('mc/home'), 'success');
				} else {
					message('密码输入错误', '', 'error');
				}
			} else {
				message('原手机号输入错误', '', 'error');
			}
		} else {
			$mobile = trim($_GPC['mobile']) ? trim($_GPC['mobile']) : message('请填写手机号');
			if(!preg_match(REGULAR_MOBILE, $mobile)) {
				message('手机号格式有误', '', 'error');
			}
			$is_exist = pdo_fetch('SELECT uid FROM ' . tablename('mc_members') . ' WHERE uniacid = :uniacid AND mobile = :mobile AND uid != :uid', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile, ':uid' => $_W['member']['uid']));
			if(!empty($is_exist)) {
				message('该手机号已被绑定,换个手机号试试', '', 'error');
			}
			pdo_update('mc_members', array('mobile' => $mobile), array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid']));
			message('修改手机号成功', url('mc/home'), 'success');
		}
	}
}

if($do == 'email') {
	$profile = mc_fetch($_W['member']['uid'], array('uid', 'email', 'salt'));
	if ($_W['member']['email'] == md5($_W['openid']).'@qdaygroup.com') {
		$reregister = true;
	}
	if(checksubmit('submit')) {
		$type = intval($_GPC['type']);
		$data = array();
		if ($type == 1) {
			if ($reregister) {
				$data['email'] = $_GPC['email'];
				$emailexists = pdo_fetch("SELECT uid FROM ".tablename('mc_members')." WHERE email = :email AND uniacid = :uniacid AND uid != :uid ", array(':email' => $data['email'], ':uniacid' => $_W['uniacid'], ':uid' => $_W['member']['uid']));
				if (!empty($emailexists['uid'])) {
					message('抱歉，该E-Mail地址已经被注册，请更换。', '', 'error');
				}
			}
			if (empty($_GPC['password'])) {
				message('请输入您的密码', '', 'error');
			}
			$data['password'] = md5($_GPC['password'] . $profile['salt'] . $_W['config']['setting']['authkey']);
			pdo_update('mc_members', $data, array(
				'uid' => $profile['uid']
			));
			message('修改帐号密码成功！', url('mc/home'), 'success');
		} else {
			$data['username'] = $_GPC['username'];
			$data['password'] = $_GPC['oldpassword'];
			if (empty($data['username']) || empty($data['password'])) {
				message('抱歉，用户名或是密码没有填写。', '', 'error');
			}

			$pars_tmp[':uniacid'] = $_W['uniacid'];
			if(preg_match(REGULAR_MOBILE, $data['username'])) {
				$sql_tmp .= ' AND `mobile`=:mobile';
				$pars_tmp[':mobile'] = $data['username'];
			} else {
				$sql_tmp .= ' AND `email`=:email';
				$pars_tmp[':email'] = $data['username'];
			}
			$member = pdo_fetch("SELECT `uid`,`salt`,`password` FROM " . tablename('mc_members') . " WHERE `uniacid`=:uniacid " . $sql_tmp, $pars_tmp);
			if (empty($member)) {
				message('抱歉，用户不存或是已经被删除', '', 'error');
			}
			
			$hash = md5($data['password'] . $member['salt'] . $_W['config']['setting']['authkey']);
			if($member['password'] != $hash) {
				message('抱歉，您输入的密码有误', '', 'error');
			}
			
			pdo_update('mc_mapping_fans', array('uid' => $member['uid']), array(
				'acid' => $_W['acid'],
				'openid' => $_W['openid'],
			));

						$member_old = mc_fetch($_W['member']['uid']);
			$member_new = mc_fetch($member['uid']);
			if(!empty($member_old) && !empty($member_new)) {
				$ignore = array('email', 'password', 'uid', 'uniacid', 'salt', 'credit1', 'credit2', 'credit3','credit4','credit5');
				$profile_update = array();
				foreach($member_old as $key => $value) {
					if(!in_array($key, $ignore)) {
						if(empty($member_new[$key]) && !empty($member_old[$key])) {
							$profile_update[$key] = $member_old[$key];
						}
					}
				}
				$profile_update['credit1'] = $member_old['credit1'] + $member_new['credit1'];
				$profile_update['credit2'] = $member_old['credit2'] + $member_new['credit2'];
				$profile_update['credit3'] = $member_old['credit3'] + $member_new['credit3'];
				$profile_update['credit4'] = $member_old['credit4'] + $member_new['credit4'];
				$profile_update['credit5'] = $member_old['credit5'] + $member_new['credit5'];
				pdo_update('mc_members', $profile_update, array('uid' => $member['uid'], 'uniacid' => $_W['uniacid']));
				pdo_delete('mc_members', array('uid' => $_W['member']['uid'], 'uniacid' => $_W['uniacid']));
								pdo_update('activity_coupon_record', array('uid' => $member['uid']), array('uid' => $_W['member']['uid'], 'uniacid' => $_W['uniacid']));
				pdo_update('activity_exchange_trades', array('uid' => $member['uid']), array('uid' => $_W['member']['uid'], 'uniacid' => $_W['uniacid']));
				pdo_update('activity_exchange_trades_shipping', array('uid' => $member['uid']), array('uid' => $_W['member']['uid'], 'uniacid' => $_W['uniacid']));
								pdo_update('mc_credits_record', array('uid' => $member['uid']), array('uid' => $_W['member']['uid'], 'uniacid' => $_W['uniacid']));
			}
			message('重新绑定帐号成功！', url('mc/home'), 'success');
		}
	}
}
template('mc/bond');