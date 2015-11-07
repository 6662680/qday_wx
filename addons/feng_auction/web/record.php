<?php
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	
	$goodses = pdo_fetchall("SELECT * FROM ".tablename('auction_recharge')." WHERE uniacid = '{$weid}' ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('auction_recharge') . " WHERE uniacid = '{$weid}' ");
	
	$pager = pagination($total, $pindex, $psize);

	include $this->template('record');
?>