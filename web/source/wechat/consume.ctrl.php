<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan isNOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
$dos = array('account', 'record');
$do = in_array($do, $dos) ? $do : 'account';

if($do == 'account') {
	$accounts = uni_accounts();
	if(!empty($accounts)) {
		foreach($accounts as $key => $li) {
			if($li['level'] < 3) {
				unset($accounts[$key]);
			}
		}
	}
	template('wechat/consume');
}

if($do == 'record') {
	$accounts = uni_accounts();
	$acid = intval($_GET['__acid']);
	if(empty($acid)) {
		$acid = intval($_GPC['__acid']);
	}
	if(!$acid || empty($accounts[$acid])) {
		message('公众号不存在', url('wechat/consume'), 'error');
	} else {
		isetcookie('__acid', $acid, 86400 * 3);
	}
	$op = empty($_GPC['op']) ? 'list' : $_GPC['op'];
	if($op == 'list') {
		$condition = ' WHERE acid = :acid';
		$parma[':acid'] = $acid;
		$cid = intval($_GPC['cid']);
		$card_id = trim($_GPC['card_id']);
		if($cid > 0) {
			$coupon = pdo_fetch('SELECT title,card_id FROM ' . tablename('coupon') . ' WHERE acid = :acid AND id = :id', array(':acid' => $acid, ':id' => $cid));
			$card_id = $coupon['card_id'];
		} else {
			$coupon = pdo_fetch('SELECT title FROM ' . tablename('coupon') . ' WHERE acid = :acid AND card_id = :card_id', array(':acid' => $acid, ':card_id' => $card_id));
		}
		if(!empty($card_id)) {
			$condition .= ' AND card_id = :card_id';
			$parma[':card_id'] = $card_id;
		}

		$code = trim($_GPC['code']);
		if(!empty($code)) {
			$condition .= " AND code LIKE '%{$code}%'";
		}
		$status = intval($_GPC['status']);
		if($status > 0) {
			$condition .= " AND status = :status";
			$parma[':status'] = $status;
		}
		$outer_id = intval($_GPC['outer_id']);
		if(!empty($outer_id)) {
			$condition .= " AND outer_id = :oid";
			$parma[':oid'] = $outer_id;
		}
		$nickname = trim($_GPC['nickname']);
		if(!empty($nickname)) {
			$condition .= " AND openid IN (SELECT openid FROM " . tablename('mc_mapping_fans') ." WHERE acid = {$acid} AND nickname LIKE '%{$nickname}%')";
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('coupon_record') . $condition, $parma);
		$data = pdo_fetchall('SELECT * FROM ' . tablename('coupon_record') . $condition . ' ORDER BY id DESC LIMIT ' .($pindex - 1) * $psize.','.$psize, $parma);
		if(!empty($data)) {
			foreach($data as &$da) {
				if(!empty($da['openid'])) {
					$openids[] = $da['openid'];
				}
				if(!empty($da['friend_openid'])) {
					$openids[] = $da['friend_openid'];
				}
				if(!empty($da['card_id'])) {
					$card_ids[] = $da['card_id'];
				}
				if($da['outer_id'] > 0) {
					$outer_ids[] = $da['outer_id'];
				}
			}

			if(!empty($openids)) {
				$openids_str = "'" . implode("','", $openids) . "'";
				$nicknames = pdo_fetchall('SELECT nickname,openid FROM ' . tablename('mc_mapping_fans') . "WHERE acid = {$acid} AND openid IN ({$openids_str})", array(), 'openid');
			}
			if(!empty($outer_ids)) {
				$outer_str = implode(',', $outer_ids);
				$outers = pdo_fetchall('SELECT name,qrcid FROM ' . tablename('qrcode') . "WHERE acid = {$acid} AND type = 'card' AND qrcid IN ({$outer_str})", array(), 'qrcid');
			}
			if(!empty($card_ids)) {
				$card_str = implode("','", $card_ids);
				$card_str = "'" . $card_str . "'";
				$cards = pdo_fetchall('SELECT card_id,title FROM ' . tablename('coupon') . "WHERE acid = {$acid} AND card_id IN ({$card_str})", array(), 'card_id');
			}
		}
		$pager = pagination($total, $pindex, $psize);
	}

	if($op == 'unavailable') {
		if($_W['role'] == 'operator') {
			message('您属于公众号操作员，没有使用该功能的权限', referer(), 'error');
		}
		$id = intval($_GPC['id']);
		$del = intval($_GPC['del']);
		$record = pdo_fetch('SELECT code,status FROM ' . tablename('coupon_record') . ' WHERE acid = :acid AND id = :id', array(':acid' => $acid, ':id' => $id));

		if(empty($record)) {
			message('对应code码不存在', referer(), 'error');
		}
		if($record['status'] == 1) {
			load()->classs('coupon');
			$acc = new coupon($acid);
			$status = $acc->UnavailableCode(array('code' => $record['code']));
			if(is_error($status)) {
				message($status['message'], '', 'error');
			} else {
				pdo_update('coupon_record', array('status' => 2, 'usetime' => TIMESTAMP), array('acid' => $acid, 'code' => $record['code'], 'id' => $id));
			}
		}
		if($del == 1) {
			pdo_delete('coupon_record', array('acid' => $acid, 'id' => $id));
			message('删除卡券领取状态成功', referer(), 'success');
		}
		message('更改卡券领取状态成功', referer(), 'success');
	}

	if($op == 'consume') {
		$id = intval($_GPC['id']);
		$record = pdo_fetch('SELECT code,status FROM ' . tablename('coupon_record') . ' WHERE acid = :acid AND id = :id', array(':acid' => $acid, ':id' => $id));

		if(empty($record)) {
			message('对应code码不存在', referer(), 'error');
		}
		$pwd = trim($_GPC['pdw']);
		if(empty($pwd)) {
			message('请输入店员密码', referer(), 'error');
		}
		$sql = 'SELECT * FROM ' . tablename('activity_coupon_password') . " WHERE `uniacid` = :uniacid AND `password` = :password";
		$clerk = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':password' => $pwd));
		if(empty($clerk)) {
			message('店员密码错误', referer(), 'error');
		}
		if($record['status'] == 1) {
			load()->classs('coupon');
			$acc = new coupon($acid);
			$status = $acc->ConsumeCode(array('code' => $record['code']));
			if(is_error($status)) {
				message($status['message'], '', 'error');
			} else {
				pdo_update('coupon_record', array('status' => 3, 'clerk_name' => $clerk['name'], 'usetime' => TIMESTAMP), array('acid' => $acid, 'code' => $record['code'], 'id' => $id));
			}
		}
		message('核销卡券成功', referer(), 'success');
	}

	template('wechat/consume');
}
