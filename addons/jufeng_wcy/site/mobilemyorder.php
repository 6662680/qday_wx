<?php
		global $_W, $_GPC;
		if(empty($_W['fans']['from_user'])){
			checkauth();
			}
			if($_GPC['op'] == 'shanchu'){
			$ccate2 = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND ccate = '{$_GPC['ccate']}' ");
		$pcate2 = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND (id = '{$_GPC['pcate']}' OR id = '{$ccate2[0]['pcate']}') ORDER BY parentid ASC, displayorder DESC");
			pdo_update('jufeng_wcy_order', array('status' => -2), array('id' => $_GPC['id']));
			message('删除订单成功。', $this->createMobileUrl('myorder', array('pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate'])), 'success');
			}
		else{
		$ccate2 = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' AND ccate = '{$_GPC['ccate']}' ");
		
		$pcate2 = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND (id = '{$_GPC['pcate']}' OR id = '{$ccate2[0]['pcate']}') ORDER BY parentid ASC, displayorder DESC");
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$list = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_order')." WHERE weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}' AND status != -2 ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, array(), 'id');
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('jufeng_wcy_order') . " WHERE weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}' AND status != -2 ");
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
		include $this->template('order');
					?>