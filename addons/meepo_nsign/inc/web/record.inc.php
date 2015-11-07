<?php
global $_GPC, $_W;
checklogin();
$rid = intval($_GPC['id']);
$condition = '';
if (!empty($_GPC['username'])) {
	$condition .= " AND A.username = '{$_GPC['username']}' ";
}
if (!empty($_GPC['mobile'])) {
	$condition .= " AND B.mobile = '{$_GPC['mobile']}' ";
}
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$list = pdo_fetchall("SELECT A.id AS newid, A.*, B.mobile, B.credit1 AS allcredit FROM ".tablename('nsign_record')." A LEFT JOIN ".tablename('mc_members')." B ON A.uid = B.uid WHERE A.rid = '$rid' $condition ORDER BY allcredit DESC LIMIT ".($pindex - 1) * $psize.','.$psize);

$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('nsign_record') . " A WHERE A.rid = '$rid' ");
$pager = pagination($total, $pindex, $psize);
$memberlist = pdo_fetchall("SELECT distinct uid FROM ".tablename('nsign_record')."  WHERE rid = '$rid' ");
$membertotal = count($memberlist);
include $this->template('record');



