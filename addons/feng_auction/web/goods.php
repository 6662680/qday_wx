<?php
	$ops = array('display', 'edit', 'delete'); // 只支持此 3 种操作.
	$op = in_array($_GPC['op'], $ops) ? $_GPC['op'] : 'display';
	//商品列表显示
	if($op == 'display'){
		$this->Judge();
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = '';
		$goodses = pdo_fetchall("SELECT * FROM ".tablename('auction_goodslist')." WHERE uniacid = '{$weid}' $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('auction_goodslist') . " WHERE uniacid = '{$weid}' ");
		$pager = pagination($total, $pindex, $psize);

		include $this->template('goods_display');
	}
	//商品编辑
	if ($op == 'edit') {
		$id = intval($_GPC['id']);
		if(!empty($id)){
			$sql = 'SELECT * FROM '.tablename('auction_goodslist').' WHERE id=:id AND uniacid=:uniacid LIMIT 1';
			$params = array(':id'=>$id, ':uniacid'=>$weid);
			$goods = pdo_fetch($sql, $params);
			
			if(empty($goods)){
				message('未找到指定的商品.', $this->createWebUrl('goods'));
			}
		}
		
		if (checksubmit()) {
			$data = $_GPC['goods']; // 获取打包值
			empty($data['title']) && message('请填写商品标题');
			empty($data['picarr']) && message('请上传商品图片');
			empty($data['end_time']) && message('请填写商品结束时间');
			empty($data['sh_price']) && message('请填写商品起拍价格');
			empty($data['bond']) && message('请填写商品保证金');
			empty($data['add_price']) && message('请填写商品默认加价价格');
			empty($data['start_time']) && message('请填写商品开始时间');
			empty($data['content']) && message('请填写商品详情');
			$data['content'] = htmlspecialchars_decode($data['content']);
			$data['start_time'] = strtotime($data['start_time']);
			$data['end_time'] =strtotime($data['end_time']);
			if (time()<$data['start_time']) {
				$data['st_price'] = $data['sh_price'];
			}

			if(empty($goods)){
				$data['st_price'] = $data['sh_price'];
				$data['uniacid'] = $weid;
				$data['createtime'] = TIMESTAMP;
				$ret = pdo_insert(auction_goodslist, $data);
				if (!empty($ret)) {
					$id = pdo_insertid();
				}
			} else {
				$ret = pdo_update(auction_goodslist, $data, array('id'=>$id));
			}
			
			if (!empty($ret)) {
				message('商品信息保存成功', $this->createWebUrl('goods', array('op'=>'edit', 'id'=>$id)), 'success');
			} else {
				message('商品信息保存失败');
			}
		}
		
		include $this->template('goods_edit');
	}
	
	if($op == 'delete') {
		$id = intval($_GPC['id']);
		if(empty($id)){
			message('未找到指定商品分类');
		}
		$result = pdo_delete(auction_goodslist, array('id'=>$id, 'uniacid'=>$weid));
		if(intval($result) == 1){
			message('删除商品成功.', $this->createWebUrl('goods'), 'success');
		} else {
			message('删除商品失败.');
		}
	}
?>