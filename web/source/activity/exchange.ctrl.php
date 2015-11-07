<?php
/**
 * [WeEngine System] Copyright (c) 2014 qdaygroup.com
 * WeEngine is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('activity');
load()->model('mc');
$dos = array(
		'gift', 			'trade',			'shipping'	);
$do = in_array($do, $dos) ? $do : 'gift';
$op = $_GPC['op'];

$types = array(
		1,			2,			3,			4,			5,	);

$creditnames = array();
$unisettings = uni_setting($uniacid, array('creditnames'));
foreach ($unisettings['creditnames'] as $key=>$credit) {
	if (!empty($credit['enabled'])) {
		$creditnames[$key] = $credit['title'];
	}
}
if (empty($creditnames)) {
	message('未启用积分，请设置.', url('mc/credit/display', array()),'error');
}
load()->func('tpl');

if($do == 'gift') { 	$_W['page']['title'] = '礼品管理 - 礼品管理 - 积分兑换';
	$op = in_array($op, array('display', 'post', 'delete')) ? $op : 'display';
	$type = in_array(intval($_GPC['type']), array('1','2','3','5')) ? intval($_GPC['type']) : 0;
	if (!empty($_GPC['id'])) {
		$title = array('0'=>'礼品管理','1'=>'编辑折扣券兑换','2'=>'编辑代金券兑换','3'=>'编辑真实物品兑换','5'=>'编辑活动参与次数兑换');
	}else {
		$title = array('0'=>'礼品管理','1'=>'添加折扣券兑换','2'=>'添加代金券兑换','3'=>'添加真实物品兑换','5'=>'添加活动参与次数兑换');
	}
	$_W['page']['title'] = $title[$type].' - 礼品管理 - 积分兑换';

	if ($op == 'display') {
		$activities = array();
		$modules = uni_modules();
		foreach($modules as $value) {
			if($value['type'] == 'activity'){
				$activities[$value['mid']]= $value;
			}
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$where = ' WHERE uniacid=:uniacid ';
		$params = array(':uniacid'=>$_W['uniacid']);
		
		$type=intval($_GPC['type']);
		if (!empty($type)) {
			$where .= ' AND type=:type';
			$params[':type'] = $type;
		}
		$title = $_GPC['keyword'];
		if (!empty($title)) {
			$where .= " AND title LIKE '%{$title}%'";
		}
		$list = pdo_fetchall('SELECT * FROM '.tablename('activity_exchange')." $where ORDER BY id ASC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('activity_exchange'). $where , $params);
		$pager = pagination($total, $pindex, $psize);
		foreach ($list as &$row) {
			$extra = iunserializer($row['extra']);
			$row['extra'] = $extra;
			$type = intval($row['type']);
			$row['thumb'] = tomedia($row['thumb']);
			if ($type == 1 || $type == 2){
				$coupon = pdo_fetch("SELECT dosage,amount FROM ".tablename('activity_coupon')." WHERE `couponid`=:couponid",array(':couponid' => $extra['id']));
				$row['num'] = intval($coupon['dosage']);
				$row['total'] = intval($coupon['amount']);
			} elseif($type==3){
			} elseif($type==4){
			} elseif($type==5){
				$row['total'] = '无限制';
			}
		}
			} elseif($op == 'post'){
		$id = intval($_GPC['id']);
				$type = intval($_GPC['type']);
		
		if (empty($type) || !in_array($type, $types)) {
			message('未指定积分兑换类型.', url('activity/exchange/gift', array()),'error');
		}
		
		if (checksubmit()) {
			$data = array(
				'title'=>$_GPC['title'],
				'credittype'=>$_GPC['credittype'],
				'credit'=>$_GPC['credit'],
				'thumb'=>$_GPC['thumb'],
				'pretotal'=> intval($_GPC['pretotal']),
				'total'=>intval($_GPC['total']),
				'type' => $type,
				'description' => $_GPC['description']
			);
		
						if (in_array($type, array(1,2,5))){
				$data['total'] = 0;
			}

			$extra = $_GPC['extra'];
			if ($type == 1){
				$coupons = pdo_fetchall("SELECT couponid,title FROM ".tablename('activity_coupon')." WHERE `type` = 1 AND `endtime` >= :endtime AND `uniacid`=:uniacid",array(':uniacid' => $_W['uniacid'],':endtime' => TIMESTAMP),'couponid');
				$extra['title'] = $coupons[$extra['id']]['title'];
			} elseif($type == 2) {
				$cashes  = pdo_fetchall("SELECT couponid,title FROM ".tablename('activity_coupon')." WHERE `type` = 2 AND `endtime` >= :endtime AND `uniacid`=:uniacid",array(':uniacid' => $_W['uniacid'],':endtime' => TIMESTAMP),'couponid');
				$extra['title'] = $cashes[$extra['id']]['title'];
			} elseif($type == 3) {
			} elseif($type == 4) {
			} elseif($type == 5) {
				$activities = array();
				$_W['modules'] = uni_modules();
				foreach ($_W['modules'] as $key => $value) {
					if($value['type'] == 'activity'){
						$activities[$key]= $value;
					}
				}
				$extra['title'] = $activities[$extra['name']]['title'];
					
				$period = intval($extra['period']);
				if ($period == 0) {
					$extra['period'] = intval($_GPC['period']);
				}
			}
			$data['extra'] = iserializer($extra);
		
			if (empty($id)) {
				$data['uniacid'] = $_W['uniacid'];
				$data['type'] = intval($_GPC['type']);
				$data['createtime'] = TIMESTAMP;
				pdo_insert('activity_exchange', $data);
			} else {
				pdo_update('activity_exchange', $data, array('id'=>$id, 'uniacid'=>$_W['uniacid']));
			}
			message('成功',url('activity/exchange/gift', array()),'success');
		} else {
			if (!empty($id)){
				$item = pdo_fetch('SELECT * FROM '.tablename('activity_exchange').' WHERE id=:id AND uniacid=:uniacid',array(':id'=>$id, ':uniacid'=>$_W['uniacid']));
				if (empty($item)) {
					message('未找到指定兑换礼品或已删除.',url('activity/exchange/gifts', array()),'error');
				} else {
					$item['extra'] = iunserializer($item['extra']);
				}
			}else{
				$item = array();
				$item['type'] = $type;
			}
			if($type == 1){
				$coupons = pdo_fetchall("SELECT couponid,title FROM ".tablename('activity_coupon')." WHERE `type` = 1 AND `endtime` >= :endtime AND `uniacid`=:uniacid",array(':uniacid' => $_W['uniacid'],':endtime' => TIMESTAMP),'couponid');
				if (empty($coupons)) {
					message('当前时间范围没有任何可用的优惠券.请到 【会员营销】<a href="' . url('activity/coupon/post') . '">【优惠券】</a>里添加优惠券');
				}
			} elseif($type == 2){
				$cashes  = pdo_fetchall("SELECT couponid,title FROM ".tablename('activity_coupon')." WHERE `type` = 2 AND `endtime` >= :endtime AND `uniacid`=:uniacid",array(':uniacid' => $_W['uniacid'],':endtime' => TIMESTAMP),'couponid');
				if (empty($cashes)) {
					message('当前时间范围没有任何可用的代金券.请到 【会员营销】<a href="' . url('activity/token/post') . '">【折扣券】</a>里添加折扣券');
				}
			} elseif($type == 5){
				$activities = array();
				$_W['modules'] = uni_modules();
				foreach ($_W['modules'] as $key=>$value) {
					if($value['type'] == 'activity'){
						$activities[$key]= $value;
					}
				}
			}
		}
	} elseif($op == 'delete'){
		$id = intval($_GPC['id']);
		if (!empty($id)){
			$item = pdo_fetch('SELECT * FROM '.tablename('activity_exchange').' WHERE id=:id AND uniacid=:uniacid',array(':id'=>$id, ':uniacid'=>$_W['uniacid']));
		}
		if (empty($item)) {
			message('删除失败,指定兑换不存在或已删除.');
		}
		pdo_delete('activity_exchange', array('id'=>$id, 'uniacid'=>$_W['uniacid']));
		message('删除成功.', referer(),'success');
	}
	template('activity/exchange_gift');
	
}

if ($do == 'trade'){ 	$_W['page']['title'] = '折扣券兑换记录 - 兑换记录 - 积分兑换';
	$type = intval($_GPC['type']);
	$type = in_array($type, $types) ? $type : 1;
	$starttime = empty($_GPC['time']['start']) ? strtotime('-1 month') : strtotime($_GPC['time']['start']);
	$endtime = empty($_GPC['time']['end']) ? TIMESTAMP : strtotime($_GPC['time']['end']) + 86399;
	
	$pindex = max(1, intval($_GPC['page']));
	$psize = 30;
	
	$where = " WHERE uniacid=:uniacid AND createtime>=:starttime AND createtime<:endtime AND type=:type";
	$params = array(
			':uniacid' => $_W['uniacid'],
			':starttime' => $starttime,
			':endtime' => $endtime,
			':type' => $type
	);
	$uid = intval($_GPC['uid']);
	if (!empty($uid)) {
		$where .= ' AND uid=:uid';
		$params[':uid'] = $uid;
	}
	$list = pdo_fetchall("SELECT * FROM ".tablename('activity_exchange_trades')." $where ORDER BY tid DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('activity_exchange_trades'). $where , $params);
	$pager = pagination($total, $pindex, $psize);
	
	if (!empty($list)) {
		$uids = array();
		foreach ($list as $row) {
			$uids[] = $row['uid'];
		}
		load()->model('mc');
		$members = mc_fetch($uids, array('uid', 'realname'));
		foreach ($list as &$row) {
			$row['extra'] = iunserializer($row['extra']);
			$row['realname'] = $members[$row['uid']]['realname'];
			$row['thumb'] = tomedia($row['thumb']);
		}
	}
	template('activity/exchange_trade');
}

if ($do == 'shipping'){
	$_W['page']['title'] = '实物礼品兑换记录 - 发送记录 - 积分兑换';
	$op = in_array($op, array('display','post','deliver','delete')) ? $op : 'display';
	if ($op == 'display') {
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$starttime = empty($_GPC['time']['start']) ? strtotime('-1 month') : strtotime($_GPC['time']['start']);
		$endtime = empty($_GPC['time']['end']) ? TIMESTAMP : strtotime($_GPC['time']['end']) + 86399;
		$where = " WHERE uniacid=:uniacid AND createtime>=:starttime AND createtime<:endtime ";
		$params = array(
				':uniacid' => $_W['uniacid'],
				':starttime' => $starttime,
				':endtime' => $endtime
		);
		$uid = intval($_GPC['uid']);
		if (!empty($uid)) {
			$where .= ' AND uid=:uid';
			$params[':uid'] = $uid;
		}
		$list = pdo_fetchall("SELECT * FROM ".tablename('activity_exchange_trades_shipping')." $where ORDER BY status ASC, id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('activity_exchange_trades_shipping'). $where , $params);
		$pager = pagination($total, $pindex, $psize);
		
		if (!empty($list)) {
			$uids = array();
			foreach ($list as $row) {
				$uids[] = $row['uid'];
			}
			
			$members = mc_fetch($uids, array('uid', 'realname'));
			foreach ($list as &$row) {
				$row['exchange'] = activity_exchange_info($row['exid']);
				$row['realname'] = $members[$row['uid']]['realname'];
				$row['thumb'] = tomedia($row['thumb']);
			}
		}
			} elseif ($op == 'delete'){
		$id = intval($_GPC['id']);
		if (!empty($id)){
			$shipping = activity_exchange_shipping($id);
		}
		if (empty($shipping)) {
			message('未找到指定兑换礼品或已删除.');
		}
		if ($shipping['uniacid']!=$_W['uniacid'] || $shipping['status']!=2) {
			message('没有权限删除当前兑换礼品.');
		}
		pdo_delete('activity_exchange_trades_shipping', array('id'=>$id));
		message('礼品删除成功.',url('activity/exchange/shipping',array('op'=>'display')),'success');
	} elseif ($op == 'post'){
		
		$id = intval($_GPC['id']);
		if (!empty($id)){
			$shipping = activity_exchange_shipping($id);
		}
		if (empty($shipping)) {
			message('未找到指定兑换礼品或已删除.',url('activity/exchange/shipping'),'error');
		}
		if (checksubmit('submit')) { 			$data = array(
					'name'=>$_GPC['realname'],
					'mobile'=>$_GPC['mobile'],
					'province'=>$_GPC['province'],
					'city'=>$_GPC['city'],
					'district'=>$_GPC['district'],
					'address'=>$_GPC['address'],
					'zipcode'=>$_GPC['zipcode'],
			);
			pdo_update('activity_exchange_trades_shipping', $data, array('id'=>$id));
			message('实物礼品邮递信息修改成功.',url('activity/exchange/shipping',array('op'=>'post','id'=>$id)),'success');
		}elseif (checksubmit('deliver')) { 			$data = array(
					'name'=>$_GPC['realname'],
					'mobile'=>$_GPC['mobile'],
					'province'=>$_GPC['province'],
					'city'=>$_GPC['city'],
					'district'=>$_GPC['district'],
					'address'=>$_GPC['address'],
					'zipcode'=>$_GPC['zipcode'],
					'status'=>1,
			);
			pdo_update('activity_exchange_trades_shipping', $data, array('id'=>$id));
			message('实物礼品发货成功.',url('activity/exchange/shipping',array('op'=>'post','id'=>$id)),'success');
		}else{
			$exchange = activity_exchange_info($shipping['exid']);
			$member = mc_fetch($shipping['uid']);
		}
			}
	
	template('activity/exchange_shipping');
}