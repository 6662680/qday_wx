<?php
		global $_W, $_GPC;
	$ccate2 = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND ccate = '{$_GPC['ccate']}' ");
		$pcate2 = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND (id = '{$_GPC['pcate']}' OR id = '{$ccate2[0]['pcate']}') ORDER BY parentid ASC, displayorder DESC");
		if (checksubmit('submit')) {
			$cart = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_cart')." WHERE weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'", array(), 'foodsid');
			if (!empty($cart)) {
				$foods = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE (pcate = '{$_GPC['pcate']}' OR pcate = '{$pcate2[0]['id']}') AND id IN ('".implode("','", array_keys($cart))."')");
				
				if (!empty($foods)) {
					foreach ($foods as $row) {
						if (empty($cart[$row['id']]['total'])) {
							continue;
						}
						if($row['preprice']){
						$price += (floatval($row['preprice']) * intval($cart[$row['id']]['total']));}
						else{$price += (floatval($row['oriprice']) * intval($cart[$row['id']]['total']));}
					}
				}
				//变更菜品热度
				if (!empty($foods)) {
					foreach ($foods as $row) {
						if (empty($cart[$row['id']]['total'])) {
							continue;
						}
						pdo_query("UPDATE ".tablename('jufeng_wcy_foods')." SET hits = :hits WHERE id = :id", array(':hits' => $row['hits'] + $cart[$row['id']]['total'], ':id' => $row['id']));
					}
				}
				if ($_GPC['paytype'] == 2) {
					$data = array(
					'weid' => $_W['uniacid'],
					'from_user' => $_W['fans']['from_user'],
					'mobile' => $_GPC['mobile'],
					'pcate' => $pcate2[0]['id'],
					'address' => $_GPC['address'],
					'ordersn' => date('md') . random(5, 1),
					'price' => $price,
					'status' => 2,
					'paytype' => intval($_GPC['paytype']),
					'other' => $_GPC['other'],
					'time' => $_GPC['time'],
					'createtime' => TIMESTAMP,
				);
				pdo_insert('jufeng_wcy_order', $data);
				$orderid = pdo_insertid();
				//插入订单菜品
				foreach ($foods as $row) {
					if (empty($row)) {
						continue;
					}
					pdo_insert('jufeng_wcy_order_foods', array(
						'weid' => $_W['uniacid'],
						'foodsid' => $row['id'],
						'orderid' => $orderid,
						'total' => $cart[$row['id']]['total'],
						'createtime' => TIMESTAMP,
					));
				}
				//清空我的菜单
				pdo_delete('jufeng_wcy_cart', array('weid' => $_W['uniacid'], 'from_user' => $_W['fans']['from_user']));
					
						$fansdata = array(
				'mobile'   => $_GPC['mobile'],
				'address'   => $_GPC['address'],
			);
						fans_update($_W['fans']['from_user'], $fansdata);
						$sms = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_sms')." WHERE weid = '{$_W['uniacid']}'");
						if($pcate2[0]['email']){
								$body = "<h3>{$pcate2[0]['name']}，您有一条订单</h3> <br />";
						if (!empty($foods)) {
							foreach ($foods as $row) {
								if($row['preprice']){$rowprice = $row['preprice'];}else{$rowprice = $row['oriprice'];}
								$body .= "{$row['title']}X{$cart[$row['id']]['total']}{$row['unit']}，".$cart[$row['id']]['total']*$rowprice."元<br />";
							}
						}
						$body .= "<br />总价格：{$price}元<br />";
						$body .= "<h3>【{$pcate2[0]['name']}】订单详情</h3> <br />";
						$body .= "订餐号：{$data['ordersn']}<br />";
						$body .= "联系电话：{$_GPC['mobile']} <br />";
						$body .= "送餐时间：{$data['time']}<br />";
						$body .= "送餐地址：{$data['address']} <br />";
						$body .= "支付方式：餐到付款<br />";
						$body .= "订单备注：{$data['other']} <br />";
				$this->sendmail("{$pcate2[0]['name']}，您有一条订单",$body,$pcate2[0]['email'],$sms['smtp'],$sms['email'],$sms['emailpsw']);
				$this->sendmail("{$pcate2[0]['name']}，您有一条订单",$body,$sms['email'],$sms['smtp'],$sms['email'],$sms['emailpsw']);
				}
	  message('提交订单成功，现在跳转至查询订单页面。', $this->createMobileUrl('myorder',array('pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate'])), 'success');
				} 
				else if ($_GPC['paytype'] == 1){
					$data = array(
					'weid' => $_W['uniacid'],
					'from_user' => $_W['fans']['from_user'],
					'mobile' => $_GPC['mobile'],
					'pcate' => $pcate2[0]['id'],
					'address' => $_GPC['address'],
					'ordersn' => date('md') . random(5, 1),
					'price' => $price,
					'status' => 1,
					'paytype' => intval($_GPC['paytype']),
					'other' => $_GPC['other'],
					'time' => $_GPC['time'],
					'createtime' => TIMESTAMP,
				);
				pdo_insert('jufeng_wcy_order', $data);
				$orderid = pdo_insertid();
				//插入订单菜品
				foreach ($foods as $row) {
					if (empty($row)) {
						continue;
					}
					pdo_insert('jufeng_wcy_order_foods', array(
						'weid' => $_W['uniacid'],
						'foodsid' => $row['id'],
						'orderid' => $orderid,
						'total' => $cart[$row['id']]['total'],
						'createtime' => TIMESTAMP,
					));
				}
				//清空我的菜单
				pdo_delete('jufeng_wcy_cart', array('weid' => $_W['uniacid'], 'from_user' => $_W['fans']['from_user']));
				$fansdata = array(
				'mobile'   => $_GPC['mobile'],
				'address'   => $_GPC['address'],
			);
						fans_update($_W['fans']['from_user'], $fansdata);
					message('提交订单成功，现在跳转至付款页面...', $this->createMobileUrl('pay', array('orderid' => $orderid,'pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate'])), 'success');}
			} 
	}
		$cart = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_cart')." WHERE weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'", array(), 'foodsid');
		if (!empty($cart)) {
			$foods = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE (pcate = '{$_GPC['pcate']}' OR pcate = '{$pcate2[0]['id']}') AND id IN ('".implode("','", array_keys($cart))."')");
		}
		$pcatefoods = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND pcate = '{$pcate2[0]['id']}' ");
				$pricetotal =0;
			foreach ($pcatefoods as $row1) {
		$pcatecart = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_cart')." WHERE from_user = :from_user AND weid = '{$_W['uniacid']}' AND foodsid = '{$row1['id']}'", array(':from_user' => $_W['fans']['from_user']));
		$pcatetotal += $pcatecart['total'];
			$eachprice = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND id = '{$pcatecart['foodsid']}'");
			if($eachprice['preprice']){$pricetotal += $eachprice['preprice']*$pcatecart['total'];}
			else{$pricetotal += $eachprice['oriprice']*$pcatecart['total'];}
			}
			$between = $pcate2[0]['sendprice']-$pricetotal;
		$profile = fans_search($_W['fans']['from_user'], array('realname', 'resideprovince', 'residecity', 'residedist', 'address', 'mobile'));
		include $this->template('cart');
		?>