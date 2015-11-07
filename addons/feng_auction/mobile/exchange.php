<?php
	if (empty($_GPC['id'])) {
        message('抱歉，参数错误！', '', 'error');
    }
	$id = intval($_GPC['id']);
	$goods = pdo_fetch("SELECT * FROM ".tablename('auction_goodslist')." WHERE uniacid = '{$weid}' and id = '{$id}' ");
	$record = pdo_fetchall("SELECT id FROM ".tablename('auction_record')." WHERE uniacid = '{$weid}' and sid = '{$goods['id']}' ");
	include $this->template('exchange');
?>