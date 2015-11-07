<?php
	$this->Judge();
	if (empty($_GPC['id'])) {
        message('抱歉，参数错误！', '', 'error');
    }
	$id = intval($_GPC['id']);
	$kf_url = $share_data['url'];
	$goods = pdo_fetch("SELECT * FROM ".tablename('auction_goodslist')." WHERE uniacid = '{$weid}' and id = '{$id}' ");
	if($goods['end_time']!=$goods['start_time']){
	$goods['bili'] = (TIMESTAMP-$goods['start_time'])/($goods['end_time']-$goods['start_time'])*100;
	}
	if ($goods['bili']>100) {
		$goods['bili'] = 100;
	}
	$pindex = 1;
	$psize = 10;
	$list = pdo_fetchall("SELECT * FROM ".tablename('auction_record')." WHERE uniacid = '{$weid}' and sid = '{$id}' ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
	include $this->template('details');
?>