<?php
global $_GPC, $_W;
checklogin();
$rid = intval($_GPC['id']);
$condition = '';
if (!empty($_GPC['username'])) {
	$condition .= " AND A.name = '{$_GPC['username']}' ";
}
if (!empty($_GPC['mobile'])) {
	$condition .= " AND B.mobile = '{$_GPC['mobile']}' ";
}
if (!empty($_GPC['wid'])) {
	$wid = $_GPC['wid'];
	pdo_update('nsign_prize', array('status' => intval($_GPC['status'])), array('id' => $wid));
	message('操作成功！', $this->createWebUrl('winners', array('id' => $rid, 'page' => $_GPC['page'])));
}
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$list = pdo_fetchall("SELECT A.id AS newid, A.*, B.mobile FROM ".tablename('nsign_prize')." A LEFT JOIN ".tablename('mc_members')." B ON A.uid = B.uid WHERE A.rid = '$rid' $condition ORDER BY A.time DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('nsign_prize') . " A WHERE A.rid = '$rid' ");
$pager = pagination($total, $pindex, $psize);
include $this->template('winners');