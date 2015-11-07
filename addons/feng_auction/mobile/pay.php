<?php
	if (empty($_GPC['id'])) {
        message('抱歉，参数错误！', '', 'error');
    }
	$orderid = intval($_GPC['id']);
	$order = pdo_fetch("SELECT * FROM " . tablename('auction_recharge') . " WHERE id ='{$orderid}'");

	$params['tid'] = $order['ordersn'];
	$params['user'] = $_W['fans']['from_user'];
	$params['fee'] = $order['price'];
	$params['title'] = $_W['account']['name'];
	$params['ordersn'] = $order['ordersn'];

	include $this->template('pay');
?>