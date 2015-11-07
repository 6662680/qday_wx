<?php
	$advs = pdo_fetchall("select * from " . tablename('auction_adv') . " where enabled=1 and weid= '{$_W['uniacid']}'");
	foreach ($advs as &$adv) {
		if (substr($adv['link'], 0, 5) != 'http:') {
			$adv['link'] = "http://" . $adv['link'];
		}
	}
	unset($adv);
	$pindex = 1;
	$psize = 10;
	$nowtime = TIMESTAMP;
	$list = pdo_fetchall("SELECT * FROM ".tablename('auction_goodslist')." WHERE uniacid = '{$weid}' and g_status = 2 and start_time < $nowtime and end_time > $nowtime ORDER BY id DESC");
	foreach ($list as $key => $value) {
		$list[$key]['bili'] = (TIMESTAMP-$value['start_time'])/($value['end_time']-$value['start_time'])*100;
	}
	$listt = pdo_fetchall("SELECT * FROM ".tablename('auction_goodslist')." WHERE uniacid = '{$weid}' and g_status = 2 and start_time > $nowtime ORDER BY id DESC");
	foreach ($listt as $keyy => $valuee) {
		$listt[$keyy]['bili'] = 0;
	}
	include $this->template('index');
?>