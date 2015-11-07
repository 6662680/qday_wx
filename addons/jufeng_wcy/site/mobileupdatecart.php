<?php
		global $_GPC, $_W;
		$result = array('status' => 0, 'message' => '');
		$operation = $_GPC['op'];
		$foodsid = intval($_GPC['foodsid']);
		$foods = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE id = :id", array(':id' => $foodsid));
		if (empty($foods)) {
			$result['message'] = '抱歉，该菜品不存在或是已经被删除了。';
			message($result, '', 'ajax');
		}
		$row = pdo_fetch("SELECT id, total FROM ".tablename('jufeng_wcy_cart')." WHERE from_user = :from_user AND weid = '{$_W['uniacid']}' AND foodsid = :foodsid", array(':from_user' => $_W['fans']['from_user'], ':foodsid' => $foodsid));
		if (empty($row['id'])) {
			if ($operation == 'add') {
			$data = array(
				'weid' => $_W['uniacid'],
				'foodsid' => $foodsid,
				'from_user' => $_W['fans']['from_user'],
				'total' => '1',
			);
			if(!empty($_W['fans']['from_user'])){pdo_insert('jufeng_wcy_cart', $data);}
			}
		} else {
			$row['total'] = $operation == 'reduce' ? ($row['total'] - 1) : ($row['total'] + 1);
			if (empty($row['total'])) {
				pdo_delete('jufeng_wcy_cart', array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid'], 'foodsid' => $foodsid));
			} else {
				$data = array(
					'total' => $row['total'],
				);
				if(!empty($_W['fans']['from_user'])){pdo_update('jufeng_wcy_cart', $data, array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid'], 'foodsid' => $foodsid));}
			}
		}
		$ccate = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND ccate = '{$_GPC['ccate']}' ");
		$category = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND (id = '{$_GPC['pcate']}' OR id = '{$ccate['pcate']}') ORDER BY parentid ASC, displayorder DESC");
		$pcatefoods = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND pcate = '{$category['id']}' ");
		$pricetotal =0;
	foreach ($pcatefoods as &$row) {
		$pcatecart = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_cart')." WHERE from_user = :from_user AND weid = '{$_W['uniacid']}' AND foodsid = '{$row['id']}'", array(':from_user' => $_W['fans']['from_user']));
		$pcatetotal += $pcatecart['total'];
		$price = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND id = '{$pcatecart['foodsid']}'");
			if($price['preprice']){$pricetotal += $price['preprice']*$pcatecart['total'];}
			else{$pricetotal += $price['oriprice']*$pcatecart['total'];}
			if($price['ccate'] == $foods['ccate']){
			$ccatenum['num'] += $pcatecart['total'];
			}
			}
			if($pricetotal < $category['sendprice']){
				$a =$category['sendprice']-$pricetotal;
			    $between = "差￥".$a."起送";$target = "#";}
			else{
				$between = "去结算";$target = "1";}
		        $result['status'] = 1;
		        $result['message'] = '菜品数据更新成功。';
		        $result['total'] = intval($data['total']);
				$result['pcatetotal'] = intval($pcatetotal);
				$result['pricetotal'] = intval($pricetotal);
				$result['between'] = $between;
				$result['target'] = $target;
				$result['ccatenum'] = $ccatenum['num'];
				$result['ccate'] = $foods['ccate'];
		message($result, '', 'ajax');
		?>