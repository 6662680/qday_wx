<?php
	$ops = array('display', 'edit', 'delete'); // 只支持此 3 种操作.
	$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
	//商品列表显示
	if($op == 'display'){
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = '';
		$goodses = pdo_fetchall("SELECT * FROM ".tablename('auction_withdrawals')." WHERE uniacid = '{$weid}' $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		if (!empty($goodses)) {
			foreach ($goodses as $key => $value) {
				$member = pdo_fetch("SELECT * FROM ".tablename('auction_member')." WHERE uniacid = '{$weid}' and id = '{$value['uid']}'");
				$goodses[$key]['nickname'] = $member['nickname'];
				$goodses[$key]['mobile'] = $member['mobile'];
				$goodses[$key]['bankcard'] = $member['bankcard'];
				$goodses[$key]['bankname'] = $member['bankname'];
				$goodses[$key]['alipay'] = $member['alipay'];
				$goodses[$key]['aliname'] = $member['aliname'];
			}
		}
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('auction_withdrawals') . " WHERE uniacid = '{$weid}' $condition");
		$pager = pagination($total, $pindex, $psize);

		include $this->template('withdrawals');
	}
	
	if($op == 'edit') {
		$id = intval($_GPC['id']);
		if(empty($id)){
			message('未找到指定提现订单');
		}
		$status['status'] = 1;
		$result = pdo_update('auction_withdrawals', $status, array('id' => $id));
		if(intval($result) == 1){
			message('确认提现成功.', $this->createWebUrl('withdrawals'), 'success');
		} else {
			message('确认提现失败.');
		}
	}
?>