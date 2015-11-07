<?php
				global $_W, $_GPC;
		$fee = intval($params['fee']);
		pdo_update('jufeng_wcy_order', array('status' => 2), array('id' => $params['tid']));
		if ($params['from'] == 'return') {
			$order = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_order')." WHERE id = '{$params['tid']}'");
				$orderfoods = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_order_foods')." WHERE orderid = '{$params['tid']}'", array(), 'foodsid');
				$foods = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE id IN ('".implode("','", array_keys($orderfoods))."')");
				$pcate = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND id = '{$foods[0]['pcate']}'");
				$sms = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_sms')." WHERE weid = '{$_W['uniacid']}'");
			if($pcate['email']){
								$body = "<h3>{$pcate['name']}，您有一条订单</h3> <br />";
						if (!empty($foods)) {
							foreach ($foods as $row) {
								if($row['preprice']){$rowprice = $row['preprice'];}else{$rowprice = $row['oriprice'];}
					$body .= "{$row['title']}X{$orderfoods[$row['id']]['total']}{$row['unit']}，".$orderfoods[$row['id']]['total']*$rowprice."元<br />";
							}
						}
						$body .= "<br />总价格：{$order['price']}元<br />";
						$body .= "<h3>订单详情</h3> <br />";
						$body .= "订餐号：{$order['ordersn']}<br />";
						$body .= "联系电话：{$order['mobile']} <br />";
						$body .= "送餐时间：{$order['time']}<br />";
						$body .= "送餐地址：{$order['address']} <br />";
						$body .= "支付方式：在线支付<br />";
						$body .= "订单备注：{$order['other']} <br />";
				$this->sendmail($pcate['name'].'，您有一条订单',$body,$pcate['email'],$sms['smtp'],$sms['email'],$sms['emailpsw']);
				$this->sendmail($pcate['name'].'，您有一条订单',$body,$sms['email'],$sms['smtp'],$sms['email'],$sms['emailpsw']);
				}
			if ($params['type'] == 'credit2') {
				message('支付成功！现在跳转至查询订单页面。', $this->createMobileUrl('myorder'), 'success');
			} else {
				message('支付成功！现在跳转至查询订单页面。', $this->createMobileUrl('myorder'), 'success');
			}
		}
		?>