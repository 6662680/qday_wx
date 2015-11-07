<?php
		global $_W, $_GPC;
		if(empty($_W['fans']['from_user'])){
			checkauth();
			}
		$orderid = intval($_GPC['orderid']);
		$order = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_order')." WHERE id = :id", array(':id' => $orderid));
		if ($order['status'] != '1') {
			message('抱歉，您的订单已经付款或是被关闭，请查看订单。', $this->createMobileUrl('myorder'), 'error');
		}
		if (checksubmit()) {
			if ($order['price'] == '0') {
				$this->payResult(array('tid' => $orderid, 'from' => 'return', 'type' => 'credit2'));
				exit;
			}
		}
		$params['tid'] = $orderid;
		$params['user'] = $_W['fans']['from_user'];
		$params['fee'] = $order['price'];
		$params['title'] = $_W['account']['name'];
		$params['ordersn'] = $order['ordersn'];
		$params['virtual'] = 1;
		include $this->template('pay');
					?>