<?php
global $_GPC, $_W;
		
		checklogin();
		
		$rid = intval($_GPC['id']);
		
		$condition = '';
		
		if (!empty($_GPC['shop'])) {
		
			$condition .= " AND shop = '{$_GPC['shop']}' ";
		
		}

		
		$pindex = max(1, intval($_GPC['page']));
		
		$psize = 20;

		$list = pdo_fetchall("SELECT * FROM ".tablename('nsign_add')." WHERE rid = '$rid' $condition ORDER BY id ASC LIMIT ".($pindex - 1) * $psize.','.$psize);

		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('nsign_add') . " WHERE rid = '$rid' $condition");

		$pager = pagination($total, $pindex, $psize);
		
		include $this->template('mgnadd');