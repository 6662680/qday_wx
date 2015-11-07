<?php
		global $_GPC, $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$condition = '';
		if (!empty($_GPC['ccate'])) {
			$cid = intval($_GPC['ccate']);
			$condition .= " AND ccate = '{$cid}'";
			$ccate = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND id = '{$_GPC['ccate']}' ");
			$category = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND id = '{$ccate['parentid']}' ");
			$sort = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND parentid = '{$ccate['parentid']}' ");
		} elseif (!empty($_GPC['pcate'])) {
			$cid = intval($_GPC['pcate']);
			$condition .= " AND pcate = '{$cid}'";
			$category = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND id = '{$_GPC['pcate']}' ");
			$sort = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND parentid = '{$_GPC['pcate']}' ");
		}
		$ptime1 = $category['time1'];
		$ptime2 = $category['time2'];
		$ptime3 = $category['time3'];
		$ptime4 = $category['time4'];
		$pcatefoods = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND pcate = '{$category['id']}' ");
		$pricetotal =0;
			foreach ($pcatefoods as &$row) {
		$pcatecart = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_cart')." WHERE from_user = :from_user AND weid = '{$_W['uniacid']}' AND foodsid = '{$row['id']}'", array(':from_user' => $_W['fans']['from_user']));
		$pcatetotal += $pcatecart['total'];
			$price = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND id = '{$pcatecart['foodsid']}'");
			if($price['preprice']){$pricetotal += $price['preprice']*$pcatecart['total'];}
			else{$pricetotal += $price['oriprice']*$pcatecart['total'];}
			$ccatenum[$price['ccate']]['num'] += $pcatecart['total'];
			$ccatenum[$price['ccate']]['id'] = $price['ccate'];
			}
			$between = $category['sendprice']-$pricetotal;
			
switch($_GPC['order']){
				default: $orderStr = 'ishot DESC';break;
				case '1': $orderStr = 'hits DESC';break;
				case '2': $orderStr = 'preprice ASC';break;
				case '3': $orderStr = 'title ASC';break;
				      }
			if($_GPC['order'] == 0){
$list = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' $condition ORDER BY $orderStr LIMIT ".($pindex - 1) * $psize.','.$psize);
			}
		
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('jufeng_wcy_foods') . " WHERE weid = '{$_W['uniacid']}' $condition");
		$pager = pagination($total, $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
		if (!empty($list)) {
			foreach ($list as &$row) {
			$foodsid = pdo_fetchall("SELECT foodsid,total FROM ".tablename('jufeng_wcy_cart')." WHERE foodsid = '{$row['id']}' AND from_user = '{$_W['fans']['from_user']}'", array(), 'foodsid');
			$row['foodsid'] = $foodsid;
			}
		}
		include $this->template('list');
					?>