<?php
	$this->Judge();

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$now_time = TIMESTAMP;
	$goodses = pdo_fetchall("SELECT * FROM ".tablename('auction_goodslist')." WHERE uniacid = '{$weid}' and end_time < '{$now_time}' ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('auction_goodslist') . " WHERE uniacid = '{$weid}' and end_time < '{$now_time}' ");
	$pager = pagination($total, $pindex, $psize);
	if(!empty($goodses)){
		foreach ($goodses as $key => $value) {
			$member = pdo_fetch("SELECT mobile FROM".tablename('auction_member')."WHERE from_user = '{$value['q_user']}'");
			$goodses[$key]['mobile'] = $member['mobile'];
		}
    }
	include $this->template('sendout');
?>