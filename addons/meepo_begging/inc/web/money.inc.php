<?php

//乞讨来的饭钱管理
global $_W,$_GPC;
load()->model('mc');
$this->__init();

if($_GPC['type'] == 'wechat'){
	$id = $_GPC['id'];
	if(empty($id)){
		message('申请不存在或已删除');
	}

	$params = array(':id'=>$id);
	$sql = "SELECT * FROM ".tablename('meepo_begging_log')." WHERE id = :id AND uniacid = :unaicid";
	$item = pdo_fetch($sql,$params);

	if($_GPC['status']== '2'){
		
		load()->model('mc');
		$record['fee'] = $item['money'];
		$record['id'] = $item['id'];
		$record['openid'] = $item['openid'];
		
		$uid = $item['uid'];
		$user = mc_fetch($uid);
		if($this->send($record,$user)){
					//更新提现状态
			$data['user'] = $_GPC['user'];
			$data['time'] = time();
			$data['reason'] = $_GPC['reason'];
			$reason[$_GPC['status']] = $data;
			pdo_update('meepo_begging_log',array('status'=>$_GPC['status']),array('id'=>$id));
			message('操作成功',$this->createWebUrl('money'),'success');
		}
		message('红包发放失败',referer(),'error');
	}
}
if($_GPC['type'] == 'apply'){
	pdo_update('mc_members_tixian_log',array('status'=>$_GPC['status']),array('id'=>$id));
	message('操作成功',$this->createWebUrl('list'),'success');
}
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
if (!empty($_GPC['keyword'])) {
	$condition .= " AND m.nickname LIKE '%{$_GPC['keyword']}%'";
}
$sql = "SELECT b.*,m.avatar,m.nickname FROM ".tablename('meepo_begging_log')." as b LEFT JOIN ".tablename('mc_members')." as m ON b.uid = m.uid "
." WHERE b.uniacid = :uniacid {$condition} ORDER BY createtime DESC ". "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
$params = array(':uniacid'=>$_W['uniacid']);
$lists = pdo_fetchall($sql,$params);

$total = pdo_fetchcolumn(
	'SELECT COUNT(*) FROM ' . tablename('meepo_begging_log') . " as b "
	." left join ".tablename('mc_members')." as m on b.uid = m.uid "
	." WHERE b.uniacid = :uniacid {$condition} ", $params);
$pager = pagination($total, $pindex, $psize);
include $this->template('money');