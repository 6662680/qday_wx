<?php
		global $_W, $_GPC;
		if(empty($_W['fans']['from_user'])){
			checkauth();
			}
			$ccate2 = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND ccate = '{$_GPC['ccate']}' ");
		$pcate2 = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND (id = '{$_GPC['pcate']}' OR id = '{$ccate2[0]['pcate']}') ORDER BY parentid ASC, displayorder DESC");
			$group = pdo_fetch("SELECT * FROM ".tablename('mc_members')." WHERE uniacid = '{$_W['uniacid']}' AND uid = '{$_W['member']['uid']}'");
		$groupid = $group['groupid'];
		if($groupid != $pcate2[0]['mbgroup']){message('您所在会员组没有对此店的操作权限！', '','error');}
		if($_GPC['op'] == 'delete'){
			pdo_delete('jufeng_wcy_order', array('id' => $_GPC['id']));
			pdo_delete('jufeng_wcy_order_foods', array('orderid' => $_GPC['id']));
			message('彻底删除订单成功！', $this->createMobileUrl('orderctr', array('pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate'])), 'success');
			}
			else if($_GPC['op'] == 'wancheng'){
			pdo_update('jufeng_wcy_order', array('status' => 0), array('id' => $_GPC['id']));
			message('订单转为已完成！', $this->createMobileUrl('orderctr', array('pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate'])), 'success');
			}
			else if($_GPC['op'] == 'yixia'){
			pdo_update('jufeng_wcy_order', array('status' => 2), array('id' => $_GPC['id']));
			message('订单转为已下单！', $this->createMobileUrl('orderctr', array('pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate'])), 'success');
			}
			else if($_GPC['op'] == 'jieshou'){
				pdo_update('jufeng_wcy_order', array('status' => 3), array('id' => $_GPC['id']));
				$item0 = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_order')." WHERE id = '{$_GPC['id']}'");
				$foodsid = pdo_fetchall("SELECT foodsid, total FROM ".tablename('jufeng_wcy_order_foods')." WHERE orderid = '{$item0['id']}'", array(), 'foodsid');
			$foods = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')."  WHERE id IN ('".implode("','", array_keys($foodsid))."')");
				$sms = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_sms')." WHERE weid = '{$_W['uniacid']}'");
				$print = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_print')." WHERE cateid = '{$pcate2[0]['id']}' AND enabled = 1");
				if($sms['smsnum'] && $sms['smspsw']){
		$body = '您向店家--'.$pcate2[0]['name'].'--预定餐号为'.$item0['ordersn'].'总价为'.$item0['price'].'元的订单已被确认，请留意接听外卖电话，如有疑问请电联'.$pcate2[0]['shouji'];
				$res = $this->sendSMS($sms['smsnum'],$sms['smspsw'],$item0['mobile'],$body);
				}
				foreach($print as $printrow){
				if($printrow['deviceno'] && $printrow['key'] && $printrow['printtime'] > 0){
									include 'HttpClient.class.php';
		$deviceno = $printrow['deviceno'];
		$key =$printrow['key'];
		$printtime = $printrow['printtime'];
        define('FEIE_HOST','115.28.225.82');
        define('FEIE_PORT',80);
		$orderInfo  = '<CB>'.$pcate2[0]['name'].'</CB><BR>';
		$orderInfo .= '--------------------------------<BR>';
		$orderInfo .= '--订餐号：'.$item0['ordersn'].'<BR>';
		$orderInfo .= '联系电话：'.$item0['mobile'].'<BR>';
		$orderInfo .= '送餐时间：'.$item0['time'].'<BR>';
		$orderInfo .= '送餐地址：'.$item0['address'].'<BR>';
		if($item0['paytype'] == 1){
		$orderInfo .= '支付方式：在线支付<BR>';
		}
		else if($item0['paytype'] == 2){
		$orderInfo .= '支付方式：餐到付款<BR>';
		}
		if($item0['other']){
		$orderInfo .= '----备注：'.$item0['other'].'<BR>';
		}
		$orderInfo .= '--------------------------------<BR>';
		foreach ($foods as $row) {
								if($row['preprice']){$rowprice = $row['preprice'];}else{$rowprice = $row['oriprice'];}
		$orderInfo .= $row['title'].'　X '.$foodsid[$row['id']]['total'].$row['unit'].'    '.$foodsid[$row['id']]['total']*$rowprice.'元<BR>';
							}
		$orderInfo .= '合计：'.$item0['price'].'元<BR>';	
		if($printrow['qr']){	
		$orderInfo .= '----------请扫描二维码----------';
		$orderInfo .= '<QR>'.$printrow['qr'].'</QR>';
		$orderInfo .= '<BR>';
		}
$msgJSON = $this->sendSelfFormatOrderInfo($deviceno,$key,$printtime,$orderInfo);
							}}
				message('订单转为已确认！请按时派送。', $this->createMobileUrl('orderctr', array('pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate'])), 'success');
				}
				else if($_GPC['op'] == 'quxiao'){
					$ordersn = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_order')." WHERE weid = '{$_W['uniacid']}' AND id = '{$_GPC['id']}' ");
			pdo_update('jufeng_wcy_order', array('status' => -1), array('id' => $_GPC['id']));
			$sms = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_sms')." WHERE weid = '{$_W['uniacid']}'");
			if($pcate2[0]['email']){
				$this->sendmail('订单取消提醒',"#友情提醒#\n" ."订餐人：" . $ordersn[0]['realname']. "（" . $ordersn[0]['mobile'] . "）" ."向贵店预定餐号：".$ordersn[0]['ordersn'] . "的订单已经取消！\n不需要对此订单进行派送。",$pcate2[0]['email'],$sms['smtp'],$sms['email'],$sms['emailpsw']);
				$this->sendmail('订单取消提醒',"#友情提醒#\n" ."订餐人：" . $ordersn[0]['realname']. "（" . $ordersn[0]['mobile'] . "）" ."向贵店预定餐号：".$ordersn[0]['ordersn'] . "的订单已经取消！\n不需要对此订单进行派送。",$sms['email'],$sms['smtp'],$sms['email'],$sms['emailpsw']);
				}
				if($sms['smsnum'] && $sms['smspsw']){
					if($ordersn[0]['paytype'] == 1){$body = '您向店家--'.$pcate2[0]['name'].'--预定餐号为'.$ordersn[0]['ordersn'].'的订单已经取消！相关金额我们会尽快退还到您的账户，如有疑问请电联'.$pcate2[0]['shouji'];}
					else if($ordersn[0]['paytype'] == 2){$body = '您向店家--'.$pcate2[0]['name'].'--预定餐号为'.$ordersn[0]['ordersn'].'的订单已经取消！如有疑问请电联'.$pcate2[0]['shouji'];}
				$res = $this->sendSMS($sms['smsnum'],$sms['smspsw'],$ordersn[0]['mobile'],$body);
				}
				message('取消订单成功。', $this->createMobileUrl('orderctr', array('pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate'])), 'success');
					}
		else{
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$list = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_order')." WHERE weid = '{$_W['uniacid']}' AND pcate = '{$pcate2[0]['id']}' AND from_user = '{$_W['fans']['from_user']}' AND status != -2 ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, array(), 'id');
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('jufeng_wcy_order') . " WHERE weid = '{$_W['uniacid']}' AND pcate = '{$pcate2[0]['id']}' AND from_user = '{$_W['fans']['from_user']}' AND status != -2 ");
		$pager = pagination($total, $pindex, $psize);
		if (!empty($list)) {
			foreach ($list as &$row) {
				$foodsid = pdo_fetchall("SELECT foodsid,total FROM ".tablename('jufeng_wcy_order_foods')." WHERE orderid = '{$row['id']}'", array(), 'foodsid');
				$foods = pdo_fetchall("SELECT id, pcate, title, thumb, preprice, oriprice, unit FROM ".tablename('jufeng_wcy_foods')."  WHERE id IN ('".implode("','", array_keys($foodsid))."')");
				$pcate3 = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND id = '{$foods[0]['pcate']}' ORDER BY parentid ASC, displayorder DESC");
				$row['pcate3'] = $pcate3;
				$row['foods'] = $foods;
				$row['total'] = $foodsid;
			}
		}//else
		}
		include $this->template('orderctr');
					?>