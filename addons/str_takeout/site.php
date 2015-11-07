<?php
/**
 * 微外卖模块微站定义
 * @author strday
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
class Str_takeoutModuleSite extends WeModuleSite {
	public function doWebConfig() {
		global $_W, $_GPC;
		$item = pdo_fetch('SELECT * FROM ' . tablename('str_config') . ' WHERE uniacid = :aid', array(':aid' => $_W['uniacid']));
		$flag = 0;
		if(!empty($item)) {
			$flag = 1;
		} else {
			$item['paytime_limit'] = 60;
		}
		if(checksubmit('submit')) {
			if(!empty($_GPC['paytime_limit'])) {
				$data['paytime_limit'] = intval($_GPC['paytime_limit']);
			} else {
				$data['paytime_limit'] = 60;
			}
			if($flag == 1) {
				pdo_update('str_config', $data, array('uniacid' => $_W['uniacid']));
			} else {
				$data['uniacid'] = $_W['uniacid'];
				pdo_insert('str_config', $data);
			}
			message('参数设置成功', $this->createWebUrl('config'), 'success');
		}
		include $this->template('config');
	}

	public function doWebStore() {
		global $_W, $_GPC;
		$op = empty($_GPC['op']) ? 'list' : trim($_GPC['op']);
		if($op == 'list') {
			$condition = ' uniacid = :aid';
			$params[':aid'] = $_W['uniacid'];
			if(!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
			}
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;

			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('str_store') . ' WHERE ' . $condition, $params);
			$lists = pdo_fetchall('SELECT * FROM ' . tablename('str_store') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
			$pager = pagination($total, $pindex, $psize);
			if(!empty($lists)) {
				foreach($lists as &$li) {
					$li['address'] = str_replace('+', ' ', $li['district']) . ' ' . $li['address'];
				}
			}
		}

		if($op == 'post') {
			load()->func('tpl');
			$id = intval($_GPC['id']);
			if($id) {
				$item = pdo_fetch('SELECT * FROM ' . tablename('str_store') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
				if(empty($item)) {
					message('门店信息不存在或已删除', 'referer', 'error');
				} else {
					$item['thumbs'] = iunserializer($item['thumbs']);
					$district_tmp = explode('+', $item['district']);
					if(is_array($district_tmp)) {
						$item['reside'] = array('province' => $district_tmp[0], 'city' => $district_tmp[1], 'district' => $district_tmp[2]);
					}
					$item['map'] = array('lat' => $item['location_x'], 'lng' => $item['location_y']);
					$item['business_hours'] = iunserializer($item['business_hours']);
				}
			}
			if(checksubmit('submit')) {
				$data = array(
					'title' => trim($_GPC['title']),
					'logo' => trim($_GPC['logo']),
					'telephone' => trim($_GPC['telephone']),
					'description' => htmlspecialchars_decode($_GPC['description']),
					'send_price' =>intval($_GPC['send_price']),
					'delivery_price' =>intval($_GPC['delivery_price']),
					'delivery_time' =>intval($_GPC['delivery_time']),
					'serve_radius' =>intval($_GPC['serve_radius']),
					'delivery_area' => trim($_GPC['delivery_area']),
					'district' => $_GPC['reside']['province'] . '+' . $_GPC['reside']['city'] . '+' . $_GPC['reside']['district'],
					'address' =>  trim($_GPC['address']),
					'location_x' => $_GPC['map']['lat'],
					'location_y' => $_GPC['map']['lng'],
					'email_notice' => intval($_GPC['email_notice']),
					'email' => trim($_GPC['email']),
					'displayorder' => intval($_GPC['displayorder']),
					'status' => intval($_GPC['status']),
				);
				if(!empty($_GPC['business_start_hours'])) {
					$hour = array();
					foreach($_GPC['business_start_hours'] as $k => $v) {
						$v = str_replace('：', ':', trim($v));
						if(!strexists($v, ':')) {
							$v .= ':00';
						}
						$end = str_replace('：', ':', trim($_GPC['business_end_hours'][$k]));
						if(!strexists($end, ':')) {
							$end.= ':00';
						}
						$hour[] = array('s' => $v, 'e' => $end);
					}
				}

				$data['business_hours'] = iserializer($hour);
				$data['thumbs'] = iserializer($_GPC['thumbs']);
				if($id) {
					pdo_update('str_store', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
				} else {
					$data['uniacid'] = $_W['uniacid'];
					pdo_insert('str_store', $data);
				}
				message('编辑门店信息成功', $this->createWebUrl('store', array('op' => 'list')), 'success');
			}
		}
		include $this->template('store');
	}

	public function doWebAjax() {
		global $_W, $_GPC;
		$op = trim($_GPC['op']);
		if($op == 'status_store') {
			$id = intval($_GPC['id']);
			$value = intval($_GPC['value']);
			$state = pdo_update('str_store', array('status' => $value), array('uniacid' => $_W['uniacid'], 'id' => $id));
			if($state !== false) {
				exit('success');
			}
			exit('error');
		}
		if($op == 'status_dish') {
			$id = intval($_GPC['id']);
			$value = intval($_GPC['value']);
			$state = pdo_update('str_dish', array('is_display' => $value), array('uniacid' => $_W['uniacid'], 'id' => $id));
			if($state !== false) {
				exit('success');
			}
			exit('error');
		}
	}
	public function doWebSwitch() {
		global $_W, $_GPC;
		$sid = intval($_GPC['sid']);
		isetcookie('__sid', $sid, 86400 * 7);
		header('location: ' . $this->createWebUrl('manage'));
		exit();
	}

	public function doWebManage() {
		global $_W, $_GPC;
		$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'cate_list';
		$sid = intval($_GPC['__sid']);
		$store = pdo_fetch('SELECT id, title FROM ' . tablename('str_store') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $sid));
		if(empty($store)) {
			message('门店信息不存在或已删除', $this->createWebUrl('store'), 'error');
		}
		$pay_types = array(
			'alipay' => '支付宝支付',
			'wechat' => '微信支付',
			'credit' => '余额支付',
			'delivery' => '餐到付款',
		);

		if($op == 'cate_list') {
			$condition = ' uniacid = :aid AND sid = :sid';
			$params[':aid'] = $_W['uniacid'];
			$params[':sid'] = $sid;
			if(!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
			}
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;

			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('str_dish_category') . ' WHERE ' . $condition, $params);
			$lists = pdo_fetchall('SELECT * FROM ' . tablename('str_dish_category') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params, 'id');
			if(!empty($lists)) {
				$ids = implode(',', array_keys($lists));
				$nums = pdo_fetchall('SELECT count(*) AS num,cid FROM ' . tablename('str_dish') . " WHERE uniacid = :aid AND cid IN ({$ids}) GROUP BY cid", array(':aid' => $_W['uniacid']), 'cid');
			}
			$pager = pagination($total, $pindex, $psize);
			if(checksubmit('submit')) {
				if(!empty($_GPC['ids'])) {
					foreach($_GPC['ids'] as $k => $v) {
						$data = array(
							'title' => trim($_GPC['title'][$k]),
							'displayorder' => intval($_GPC['displayorder'][$k])
						);
						pdo_update('str_dish_category', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
					}
					message('编辑成功', $this->createWebUrl('manage', array('op' => 'cate_list')), 'success');
				}
			}
			include $this->template('category');
		} elseif($op == 'cate_post') {
			if(checksubmit('submit')) {
				if(!empty($_GPC['title'])) {
					foreach($_GPC['title'] as $k => $v) {
						$v = trim($v);
						if(empty($v)) continue;
						$data['sid'] = $sid;
						$data['uniacid'] = $_W['uniacid'];
						$data['title'] = $v;
						$data['displayorder'] = intval($_GPC['displayorder'][$k]);
						pdo_insert('str_dish_category', $data);
					}
				}
				message('添加菜品分类成功', $this->createWebUrl('manage', array('sid' => $sid, 'op' => 'cate_list')), 'success');
			}
			include $this->template('category');
		} elseif($op == 'cate_del') {
			$id = intval($_GPC['id']);
			pdo_delete('str_dish_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $id));
			pdo_delete('str_dish', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'cid' => $id));
			message('删除菜品分类成功', $this->createWebUrl('manage', array('op' => 'cate_list')), 'success');
		} elseif($op == 'dish_list') {
			$condition = ' uniacid = :aid AND sid = :sid';
			$params[':aid'] = $_W['uniacid'];
			$params[':sid'] = $sid;
			if(!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
			}
			if(!empty($_GPC['cid'])) {
				$condition .= " AND cid = :cid";
				$params[':cid'] = intval($_GPC['cid']);
			}

			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;

			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('str_dish') . ' WHERE ' . $condition, $params);
			$lists = pdo_fetchall('SELECT * FROM ' . tablename('str_dish') . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
			$pager = pagination($total, $pindex, $psize);
			$category = pdo_fetchall('SELECT title, id FROM ' . tablename('str_dish_category') . ' WHERE uniacid = :aid AND sid = :sid', array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');

			include $this->template('dish');
		} elseif($op == 'dish_post') {
			load()->func('tpl');
			$category = pdo_fetchall('SELECT title, id FROM ' . tablename('str_dish_category') . ' WHERE uniacid = :aid AND sid = :sid ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':sid' => $sid));
			$id = intval($_GPC['id']);
			if($id) {
				$item = pdo_fetch('SELECT * FROM ' . tablename('str_dish') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
				if(empty($item)) {
					message('菜品不存在或已删除', $this->createWebUrl('manage', array('dish_list')), 'success');
				}
			} else {
				$item['total'] = -1;
			}
			if(checksubmit('submit')) {
				$data = array(
					'sid' => $sid,
					'uniacid' => $_W['uniacid'],
					'title' => trim($_GPC['title']),
					'price' => intval($_GPC['price']),
					'total' => intval($_GPC['total']),
					'sailed' => intval($_GPC['sailed']),
					'is_display' => intval($_GPC['is_display']),
					'cid' => intval($_GPC['cid']),
					'thumb' => trim($_GPC['thumb']),
					'displayorder' => intval($_GPC['displayorder']),
					'description' => trim($_GPC['description'])
				);
				if($id) {
					pdo_update('str_dish', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
				} else {
					pdo_insert('str_dish', $data);
				}
				message('编辑菜品成功', $this->createWebUrl('manage', array('op' => 'dish_list')), 'success');
			}
			include $this->template('dish');
		} elseif($op == 'order') {
			//获取模块的支付方式
			load()->func('tpl');
			$condition = ' WHERE uniacid = :aid AND sid = :sid';
			$params[':aid'] = $_W['uniacid'];
			$params[':sid'] = $sid;

			$status = intval($_GPC['status']);
			if($status) {
				$condition .= ' AND status = :stu';
				$params[':stu'] = $status;
			}
			$keyword = trim($_GPC['keyword']);
			if(!empty($keyword)) {
				$condition .= " AND (username LIKE '%{$keyword}%' OR mobile LIKE '%{$keyword}%')";
			}
			$pay_ty = trim($_GPC['pay_type']);
			if(!empty($pay_ty)) {
				$condition .= " AND pay_type = :pay_ty";
				$params[':pay_ty'] = $pay_ty;
			}
			$pay_sta = trim($_GPC['pay_status']);
			if(!empty($pay_sta)) {
				$condition .= " AND pay_status = :pay_sta";
				$params[':pay_sta'] = $pay_sta;
			}

			if(!empty($_GPC['addtime'])) {
				$starttime = strtotime($_GPC['addtime']['start']);
				$endtime = strtotime($_GPC['addtime']['end']) + 86399;
			} else {
				$starttime = strtotime('-15 day');
				$endtime = TIMESTAMP;
			}
			$condition .= " AND addtime > :start AND addtime < :end";
			$params[':start'] = $starttime;
			$params[':end'] = $endtime;

			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;

			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('str_order') .  $condition, $params);
			$data = pdo_fetchall('SELECT * FROM ' . tablename('str_order') . $condition . ' ORDER BY addtime DESC LIMIT '.($pindex - 1) * $psize.','.$psize, $params);
			$pager = pagination($total, $pindex, $psize);
			include $this->template('order');
		} elseif($op == 'orderdetail') {
			$id = intval($_GPC['id']);
			$order = pdo_fetch('SELECT * FROM ' . tablename('str_order') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
			if(empty($order)) {
				message('订单不存在或已经删除', $this->createWebUrl('manage', array('op' => 'order')), 'error');
			} else {
				$order['dish'] = iunserializer($order['dish']);
				$comment = pdo_fetchall('SELECT * FROM ' . tablename('str_dish_comment') .' WHERE uniacid = :aid AND oid = :id', array(':aid' => $_W['uniacid'], ':id' => $id), 'did');
			}
			include $this->template('order');
		} elseif($op == 'ajaxorder') {
			$oid = intval($_GPC['oid']);
			$status = intval($_GPC['status']);
			$column = trim($_GPC['column']);
			$is_exist = pdo_fetch('SELECT id FROM ' . tablename('str_order') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $oid));
			if(empty($is_exist)) exit('订单不存在');
			pdo_update('str_order', array($column => $status), array('uniacid' => $_W['uniacid'], 'id' => $oid));
			exit('success');
		} elseif($op == 'orderdel') {
			$id = intval($_GPC['id']);
			pdo_delete('str_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
			pdo_delete('str_dish_comment', array('uniacid' => $_W['uniacid'], 'oid' => $id));
			message('删除订单成功', $this->createWebUrl('manage', array('op' => 'order')), 'success');
		}
	}

	public function doMobileIndex() {
		global $_W, $_GPC;
		if($_W['ispost']) {
			$page = intval($_GPC['page']);
			$key = trim($_GPC['key']);
			$condition = ' WHERE uniacid = :aid AND status = 1';
			$params[':aid'] = $_W['uniacid'];
			if(!empty($key)) {
				$condition .= " AND title LIKE '%{$key}%'";
			}
			$data = pdo_fetchall('SELECT * FROM ' . tablename('str_store') . $condition . ' LIMIT ' . (($page - 1) * 10) . ', 10', $params);
			$str = '';
			if(!empty($data)) {
				foreach($data as &$da) {
					$da['business_hours_flag'] = 0;
					$da['business_hours'] = iunserializer($da['business_hours']);
					if(is_array($da['business_hours'])) {
						foreach($da['business_hours'] as $li) {
							$li_s_tmp = explode(':', $li['s']); //开始时间
							$li_e_tmp = explode(':', $li['e']); //结束时间
							$s_timepas = mktime($li_s_tmp[0], $li_s_tmp[1]);
							$e_timepas = mktime($li_e_tmp[0], $li_e_tmp[1]);
							$now = TIMESTAMP;
							if($now >= $s_timepas && $now <= $e_timepas) {
								$da['business_hours_flag'] = 1;
								break;
							}
						}
					}
					$href = $this->createMobileUrl('dish', array('sid' => $da['id']));
					$str .= '<li class="url" data-url='.$href.'>
								<div class="img_tt">
									<div>
										<div class="nopic"'.($da['logo']?'style="background-image:url('.tomedia($da['logo']).');background-size: 100% 100%;"' : '').'></div>
									</div>
								</div>
								<div class="main_info">	<i class="not_ico_rest"></i>'.
						($da['business_hours_flag'] ? '' : '<i class="ico_rest"></i>').
						'<h3>'.$da['title'].'</h3>
									<p class="sub_title">'.str_replace('+', '', $da['distirct']) . $da['address'].'</p>
									<div>
										<a href="tel:'.$da['telephone'].'">电话：'.$da['telephone'].'</a>
										<span class="ml13"></span>
									</div>
								</div>
							</li>';
				}
				exit(json_encode(array('code' => 1, 'page' => $page, 'str' => $str)));
			} else {
				exit(json_encode(array('code' => 2, 'page' => $page, 'str' => $str)));
			}
		}
		include $this->template('index');
	}

	public function doMobileDish() {
		global $_W, $_GPC;
		$sid = intval($_GPC['sid']);
		$store = pdo_fetch('SELECT delivery_price,business_hours,send_price FROM ' . tablename('str_store') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $sid));
		$store['business_hours_flag'] = 0;
		$store['business_hours'] = iunserializer($store['business_hours']);
		if(is_array($store['business_hours'])) {
			$hour_str = '';
			foreach($store['business_hours'] as $li) {
				$hour_str .= $li['s'] . '~' . $li['e'] . '、';
				$li_s_tmp = explode(':', $li['s']); //开始时间
				$li_e_tmp = explode(':', $li['e']); //结束时间
				$s_timepas = mktime($li_s_tmp[0], $li_s_tmp[1]);
				$e_timepas = mktime($li_e_tmp[0], $li_e_tmp[1]);
				$now = TIMESTAMP;
				if(!$store['business_hours_flag']) {
					if($now >= $s_timepas && $now <= $e_timepas) {
						$store['business_hours_flag'] = 1;
					}
				}
			}
			$hour_str = trim($hour_str, '、');
		}

		if(empty($store)) {
			message('门店信息不存在', $this->createMobileUrl('index'), 'error');
		}
		$category = pdo_fetchall('SELECT title, id FROM ' . tablename('str_dish_category') . ' WHERE uniacid = :aid AND sid = :sid ORDER BY displayorder DESC, id ASC', array(':aid' => $_W['uniacid'], ':sid' => $sid));
		//获取第一个分类下的菜品
		$first_cate = $category[0];
		$dish = pdo_fetchall('SELECT * FROM ' . tablename('str_dish') . ' WHERE uniacid = :aid AND sid = :sid AND cid = :cid', array(':aid' => $_W['uniacid'], ':sid' => $sid, ':cid' => $first_cate['id']));

		include $this->template('dish');
	}

	public function doMobileAjaxDish() {
		global $_W, $_GPC;
		$sid = intval($_GPC['sid']);
		$cid = intval($_GPC['cid']);
		$store = pdo_fetch('SELECT delivery_price,business_hours,send_price FROM ' . tablename('str_store') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $sid));
		$store['business_hours_flag'] = 0;
		$store['business_hours'] = iunserializer($store['business_hours']);
		if(is_array($store['business_hours'])) {
			$hour_str = '';
			foreach($store['business_hours'] as $li) {
				$hour_str .= $li['s'] . '~' . $li['e'] . '、';
				$li_s_tmp = explode(':', $li['s']); //开始时间
				$li_e_tmp = explode(':', $li['e']); //结束时间
				$s_timepas = mktime($li_s_tmp[0], $li_s_tmp[1]);
				$e_timepas = mktime($li_e_tmp[0], $li_e_tmp[1]);
				$now = TIMESTAMP;
				if(!$store['business_hours_flag']) {
					if($now >= $s_timepas && $now <= $e_timepas) {
						$store['business_hours_flag'] = 1;
					}
				}
			}
			$hour_str = trim($hour_str, '、');
		}

		$category = pdo_fetch('SELECT title, id FROM ' . tablename('str_dish_category') . ' WHERE uniacid = :aid AND sid = :sid AND id = :id', array(':aid' => $_W['uniacid'], ':sid' => $sid, ':id' => $cid));
		$dish = pdo_fetchall('SELECT * FROM ' . tablename('str_dish') . ' WHERE uniacid = :aid AND sid = :sid AND cid = :cid', array(':aid' => $_W['uniacid'], ':sid' => $sid, ':cid' => $cid));
		include $this->template('dish_model');
		exit();
	}
	public function doMobileStore() {
		global $_W, $_GPC;
		$sid = intval($_GPC['sid']);
		$store = pdo_fetch('SELECT * FROM ' . tablename('str_store') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $sid));
		$store['thumbs'] = iunserializer($store['thumbs']);
		$store['business_hours_flag'] = 0;
		$store['business_hours'] = iunserializer($store['business_hours']);
		if(is_array($store['business_hours'])) {
			$hour_str = '';
			foreach($store['business_hours'] as $li) {
				$hour_str .= $li['s'] . '~' . $li['e'] . '、';
				$li_s_tmp = explode(':', $li['s']); //开始时间
				$li_e_tmp = explode(':', $li['e']); //结束时间
				$s_timepas = mktime($li_s_tmp[0], $li_s_tmp[1]);
				$e_timepas = mktime($li_e_tmp[0], $li_e_tmp[1]);
				$now = TIMESTAMP;
				if(!$store['business_hours_flag']) {
					if($now >= $s_timepas && $now <= $e_timepas) {
						$store['business_hours_flag'] = 1;
					}
				}
			}
			$hour_str = trim($hour_str, '、');
		}
		$store['address'] = str_replace('+', '', $store['distirct']) . $store['address'];
		include $this->template('store');
	}

	public function doMobileOrder() {
		global $_W, $_GPC;
		checkauth();
		$sid = intval($_GPC['sid']);
		$store = pdo_fetch('SELECT * FROM ' . tablename('str_store') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $sid));
		if(empty($store)) {
			message('门店不存在', '', 'error');
		}
		$dishes = array();
		//得到有效的订单
		if(!empty($_GPC['dish'])) {
			foreach($_GPC['dish'] as $k => $v) {
				$k = intval($k);
				$v = intval($v);
				if($k && $v) {
					$dishes[$k] = $v;
				}
			}
		}
		//计算订单的价格
		if(!empty($dishes)) {
			$ids_str = implode(',', array_keys($dishes));
			$dish_info = pdo_fetchall('SELECT * FROM ' . tablename('str_dish') ." WHERE uniacid = :aid AND sid = :sid AND id IN ($ids_str)", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
		}
		include $this->template('order');
	}
	public function doMobileOrderConfirm() {
		global $_W, $_GPC;
		checkauth();
		if(!$_W['isajax']) {
			$sid = intval($_GPC['sid']);
			$store = pdo_fetch('SELECT * FROM ' . tablename('str_store') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $sid));
			if(empty($store)) {
				message('门店不存在', '', 'error');
			}
			$dish = array();
			if(!empty($_GPC['dish'])) {
				foreach($_GPC['dish'] as $key => $val) {
					$dish[$key] = intval($val);
				}
			}
			if(empty($dish)) {
				message('订单信息出错', '', 'error');
			}
			$dish = base64_encode(iserializer($dish));
			//送餐时间
			$minut = date('i', TIMESTAMP);
			if($minut <= 15) {
				$minut = 15;
			} elseif($minut >15 && $minut <= 30) {
				$minut = 30;
			} elseif($minut >30 && $minut <= 45) {
				$minut = 45;
			} elseif($minut >45 && $minut <= 60) {
				$minut = 60;
			}
			$now = mktime(date('H'), $minut);
			$now_limit = $now + 180*60;
			for($now; $now <= $now_limit; $now += 15 * 60) {
				$str .= '<a href="javascript:void(0);">'.date('H:i', $now).'</a>';
			}
			//收货人信息
			$member = mc_fetch($_W['member']['uid'], array('realname', 'mobile', 'address'));
		} else {
			$sid = intval($_GPC['sid']);
			$rand = trim($_GPC['rand_order']);
			$dish = iunserializer(base64_decode($_GPC['dish']));
			$out['errno'] = 1;
			$out['error'] = '';
			if(!$sid || empty($dish)) {
				$out['errno'] = 1;
				$out['error'] = '订单信息不存在或已失效';
			}
			$data['uniacid'] = $_W['uniacid'];
			$data['sid'] = $sid;
			$data['uid'] = $_W['member']['uid'];
			$data['address'] = trim($_GPC['address']);
			$data['mobile'] = trim($_GPC['mobile']);
			$data['username'] = trim($_GPC['username']);
			$data['note'] = trim($_GPC['note']);
			$data['pay_type'] = trim($_GPC['pay_type']);
			$data['delivery_time'] = trim($_GPC['delivery_time']);
			//计算订单的价格
			if(!empty($dish)) {
				$ids_str = implode(',', array_keys($dish));
				$dish_info = pdo_fetchall('SELECT * FROM ' . tablename('str_dish') ." WHERE uniacid = :aid AND sid = :sid AND id IN ($ids_str)", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
			}
			$price = 0;
			$num = 0;
			$dish_data = array();
			foreach($dish as $k => &$v) {
				$k = intval($k);
				$v = intval($v);
				if($k && $v) {
					$price += ($v * $dish_info[$k]['price']);
					$num += $v;
				}
				//更新菜品售出的份数
				pdo_query('UPDATE ' . tablename('str_dish') . " set sailed = sailed + {$v} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $k));
				$dish_data[$k] = array('id' => $k, 'title' => $dish_info[$k]['title'], 'price' => $dish_info[$k]['price'] * $v, 'num' => $v);
			}
			$delivery_price = pdo_fetchcolumn('SELECT delivery_price FROM ' . tablename('str_store') ." WHERE uniacid = :aid AND id = :sid", array(':aid' => $_W['uniacid'], ':sid' => $sid));
			$data['price'] = $price + $delivery_price;
			$sid = intval($_GPC['sid']);
			$data['num'] = $num;
			$data['dish'] = iserializer($dish_data);
			$data['addtime'] = TIMESTAMP;
			$data['status'] = 2;
			pdo_insert('str_order', $data);

			$id = pdo_insertid();
			if($id) {
				$out['errno'] = 0;
				$out['url'] = $this->createMobileUrl('pay', array('id' => $id));
			} else {
				$out['errno'] = 1;
				$out['error'] = '保存订单失败';
			}
			exit(json_encode($out));
		}
		include $this->template('orderconfirm');
	}
	public function doMobileOrderDetail() {
		global $_W, $_GPC;
		checkauth();
		$sid = intval($_GPC['sid']);
		$oid = intval($_GPC['id']);
		$store = pdo_fetch('SELECT * FROM ' . tablename('str_store') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $sid));
		if(empty($store)) {
			message('门店不存在', '', 'error');
		}

		$order = pdo_fetch('SELECT * FROM ' . tablename('str_order') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $oid));
		if(empty($order)) {
			message('订单信息不存在', '', 'error');
		}
		if($order['status'] == 2) {
			$paytime_limit = pdo_fetchcolumn('SELECT paytime_limit FROM ' . tablename('str_config') . ' WHERE uniacid = :aid', array(':aid' => $_W['uniacid']));
			if(empty($paytime_limit)) {
				$paytime_limit = 3600;
			} else {
				$paytime_limit = $paytime_limit * 60;
			}
			$limit_time = $order['addtime'] + $paytime_limit - TIMESTAMP;
			$minute = floor($limit_time / 60);
		}
		$pay_types = array(
			'alipay' => '支付宝支付',
			'wechat' => '微信支付',
			'credit' => '余额支付',
			'delivery' => '餐到付款',
		);

		$order['dish'] = iunserializer($order['dish']);
		include $this->template('orderdetail');
	}
	public function doMobileAjaxOrder() {
		global $_W, $_GPC;
		checkauth();
		$id = intval($_GPC['id']);
		$op = trim($_GPC['op']);

		$order = pdo_fetch('SELECT id FROM ' . tablename('str_order') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
		$out['errno'] = 0;
		$out['error'] = 0;
		if(empty($order)) {
			$out['errno'] = 1;
			$out['error'] = '订单不存在';
			exit(json_encode($out));
		}
		if($op == 'editstatus') {
			pdo_update('str_order', array('status' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
		} elseif($op == 'del') {
			pdo_delete('str_order', array('uniacid' => $_W['uniacid'], 'id' => $id));
			$out['error'] = $this->createMobileUrl('myorder');
		}
		exit(json_encode($out));
	}
	public function doMobilePay() {
		global $_W, $_GPC;
		checkauth();
		$id = intval($_GPC['id']);
		$order = pdo_fetch('SELECT * FROM ' . tablename('str_order') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
		if(empty($order)) {
			message('订单不存在或已删除', $this->createMobileUrl('myorder'), 'error');
		}
		if($order['status'] != 2) {
			message('该订单已付款或已关闭,正在跳转到我的订单...',$this->createMobileUrl('myorder'), 'info');
		}
		$params['module'] = "str_takeout";
		$params['tid'] = $order['id'];
		$params['ordersn'] = $order['id'];
		$params['user'] = $_W['member']['uid'];
		$params['fee'] = $order['price'];
		$params['title'] = $_W['account']['name'] . "外卖订单{$order['ordersn']}";
		include $this->template('pay');
	}
	public function payResult($params) {
		global $_W, $_GPC;
		$data = array('status' => $params['result'] == 'success' ? 3 : 2);
		$data['pay_type'] = $params['type'];
		if ($params['type'] == 'wechat') {
			//$data['transid'] = $params['tag']['transaction_id'];
		}
		if ($params['type'] == 'delivery') {
			$data['status'] = 3;
		}
		//pdo_update('str_order', $data, array('id' => $params['tid'], 'uniacid' => $_W['uniacid']));
		$order = pdo_fetch('SELECT * FROM ' . tablename('str_order') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $params['tid']));
		//获取store信息
		$store = pdo_fetch('SELECT email_notice, email FROM ' . tablename('str_store') . ' WHERE uniacid = :aid AND id = :id',  array(':aid' => $_W['uniacid'], ':id' => $order['sid']));
		if ($params['from'] == 'return') {
			//邮件提醒
			if (!empty($store['email_notice'])) {
				$body = "<h3>订餐菜品清单</h3> <br />";
				if (!empty($order)) {
					$order['dish'] = iunserializer($order['dish']);
					foreach ($order['dish'] as $row) {
						$body .= "名称：{$row['title']} ，数量：{$row['num']} 份<br />";
					}
				}
				$body .= "<br />总金额：{$order['price']}元 （已付款）<br />";
				$body .= "<h3>订餐用户详情</h3> <br />";
				$body .= "真实姓名：{$order['username']} <br />";
				$body .= "地区：{$order['address']}<br />";
				$body .= "手机：{$order['mobile']} <br />";
				load()->func('communication');
				ihttp_email($store['email'], '微外卖订单提醒', $body);
			}
			if($params['type'] == 'credit') {
				message('支付成功！', $_W['siteroot']."app/".$this->createMobileUrl('orderdetail', array('id' => $order['id'], 'sid' => $order['sid'])), 'success');
			} else {
				message('支付成功！',$_W['siteroot']."app/".$this->createMobileUrl('orderdetail', array('id' => $order['id'], 'sid' => $order['sid'])), 'success');
			}
		}
	}
	public function doMobileMyorder() {
		global $_W, $_GPC;
		checkauth();
		$uid = intval($_W['member']['uid']);
		if(!$_W['isajax']) {
			$now = pdo_fetchall('SELECT a.*, b.title  FROM ' . tablename('str_order') . ' AS a LEFT JOIN ' . tablename('str_store') . ' AS b on a.sid = b.id WHERE a.uniacid = :aid AND a.uid = :uid AND (a.status = 2 OR a.status = 3 OR a.status = 4 OR a.status = 6) ORDER BY a.addtime DESC LIMIT 5', array(':aid' => $_W['uniacid'], ':uid' => $uid), 'id');
			$now_count = pdo_fetchcolumn('SELECT COUNT(*)  FROM ' . tablename('str_order') . ' WHERE uniacid = :aid AND uid = :uid AND (status = 2 OR status = 3 OR status = 4 OR status = 6)', array(':aid' => $_W['uniacid'], ':uid' => $uid));
			if(!empty($now)) {
				ksort($now);
				$now_minid = min(array_keys($now));
			} else {
				$now_minid = 0;
			}


			$history = pdo_fetchall('SELECT a.*, b.title FROM ' . tablename('str_order') . ' AS a LEFT JOIN ' . tablename('str_store') . ' AS b on a.sid = b.id WHERE a.uniacid = :aid AND a.uid = :uid AND (a.status = 1 OR a.status = 5) ORDER BY a.addtime DESC LIMIT 5', array(':aid' => $_W['uniacid'], ':uid' => $uid), 'id');
			$history_count = pdo_fetchcolumn('SELECT COUNT(*)  FROM ' . tablename('str_order') . ' WHERE uniacid = :aid AND uid = :uid AND (status = 1 OR status = 5)', array(':aid' => $_W['uniacid'], ':uid' => $uid));
			if(!empty($history)) {
				ksort($history);
				$history_minid = min(array_keys($history));
			} else {
				$history_minid = 0;
			}

		} else {
			$minid = intval($_GPC['minid']);
			$type = trim($_GPC['type']);
			if($type == 'now') {
				$data = pdo_fetchall('SELECT a.*, b.title  FROM ' . tablename('str_order') . ' AS a LEFT JOIN ' . tablename('str_store') . ' AS b on a.sid = b.id WHERE a.id < :minid AND  a.uniacid = :aid AND a.uid = :uid AND (a.status = 2 OR a.status = 3 OR a.status = 4 OR a.status = 6) ORDER BY a.addtime DESC LIMIT 5', array(':aid' => $_W['uniacid'], ':uid' => $uid, ':minid' => $minid), 'id');
			} else {
				$data = pdo_fetchall('SELECT a.*, b.title FROM ' . tablename('str_order') . ' AS a LEFT JOIN ' . tablename('str_store') . ' AS b on a.sid = b.id WHERE  a.id < :minid AND a.uniacid = :aid AND a.uid = :uid AND (a.status = 1 OR a.status = 5) ORDER BY a.addtime DESC LIMIT 5', array(':aid' => $_W['uniacid'], ':uid' => $uid, ':minid' => $minid), 'id');
			}
			if(!empty($data)) {
				ksort($data);
				$minid = min(array_keys($data));
				$out['minid'] = $minid;
				foreach($data as $da) {
					$str .= '
						<li>
							<a href="'. $this->createMobileUrl('orderdetail', array('id' => $row['id'], 'sid' => $row['sid'])) .'">
								<div>';
					if($da['status'] == 1)
						$str .= '<div class="ico_status complete"><i></i>已完成</div>';
					elseif($da['status'] == 2)
						$str .= '<div class="ico_status pending"><i></i>待付款</div>';
					elseif($da['status'] == 3)
						$str .= '<div class="ico_status inhand"><i></i>待送餐</div>';
					elseif($da['status'] == 4)
						$str .= '<div class="ico_status confirm"><i></i>待收餐</div>';
					elseif($da['status'] == 5)
						$str .= '<div class="ico_status cancel"><i></i>已取消</div>';
					else
						$str .= '';
					$str .= '</div>
								<div>
									<h3 class="highlight">'.$da['title'].'</h3>
									<p>'.$da['num'].'份/￥'.$da['price'].'</p>
									<div>'.date('Y-m-d H:i', $da['addtime']).'</div>
								</div>
							</a>
						</li>
					';
				}
				$out['str'] = $str;
			} else {
				$out['minid'] = 0;
				$out['str'] = '';
			}
			exit(json_encode($out));
		}
		include $this->template('myorder');
	}

	public function doMobileComment() {
		global $_W, $_GPC;
		$id = intval($_GPC['id']);
		$order = pdo_fetch('SELECT id,dish FROM ' . tablename('str_order') . ' WHERE uniacid = :aid AND id = :id', array(':aid' => $_W['uniacid'], ':id' => $id));
		if(!$_W['isajax']) {
			if(empty($order)) {
				message('订单不存在或已经删除', $this->createMobileUrl('myorder'), 'error');
			}
			$dish = iunserializer($order['dish']);
			//获取评分记录
			$comment = pdo_fetchall('SELECT * FROM ' . tablename('str_dish_comment') .' WHERE uniacid = :aid AND oid = :id', array(':aid' => $_W['uniacid'], ':id' => $id), 'did');
		} else {
			$out['errno'] = 0;
			$out['error'] = 0;
			if(empty($order)) {
				$out['errno'] = 1;
				$out['error'] = '订单不存在或已经删除';
				exit(json_encode($out));
			}
			$comment = pdo_fetchall('SELECT * FROM ' . tablename('str_dish_comment') .' WHERE uniacid = :aid AND oid = :id', array(':aid' => $_W['uniacid'], ':id' => $id), 'did');

			if(!empty($comment)) {
				$out['errno'] = 1;
				$out['error'] = '该订单已经评价过';
				exit(json_encode($out));
			}
			$dish = iunserializer($order['dish']);
			$dish_ids = array_keys($dish);
			if(!empty($_GPC['score_data'])) {
				foreach($_GPC['score_data'] as $row) {
					if($row['id'] && in_array($row['id'], $dish_ids)) {
						$score = intval($row['score']);
						$data['uniacid'] = $_W['uniacid'];
						$data['oid'] = $id;
						$data['did'] = $row['id'];
						$data['score'] = $score;
						$data['uid'] = $_W['member']['uid'];
						pdo_insert('str_dish_comment', $data);
					}
				}
			}
			pdo_update('str_order', array('comment' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
			exit(json_encode($out));
		}
		include $this->template('comment');
	}
	public function doWebCron() {
		global $_W, $_GPC;
		$paytime_limit = pdo_fetchcolumn('SELECT paytime_limit FROM ' . tablename('str_config') . ' WHERE uniacid = :aid', array(':aid' => $_W['uniacid']));
		if(empty($paytime_limit)) {
			$paytime_limit = 3600;
		} else {
			$paytime_limit = $paytime_limit * 60;
		}
		pdo_query('UPDATE ' . tablename('str_order') . ' SET status = 5 WHERE uniacid = :aid AND addtime < :limittime', array(':aid' => $_W['uniacid'], ':limittime' => (TIMESTAMP - $paytime_limit)));
	}
	public function doMobileCron() {
		global $_W, $_GPC;
		$paytime_limit = pdo_fetchcolumn('SELECT paytime_limit FROM ' . tablename('str_config') . ' WHERE uniacid = :aid', array(':aid' => $_W['uniacid']));
		if(empty($paytime_limit)) {
			$paytime_limit = 3600;
		} else {
			$paytime_limit = $paytime_limit * 60;
		}
		pdo_query('UPDATE ' . tablename('str_order') . ' SET status = 5 WHERE uniacid = :aid AND addtime < :limittime', array(':aid' => $_W['uniacid'], ':limittime' => (TIMESTAMP - $paytime_limit)));
	}
	public function doWebSystem() {
		global $_W, $_GPC;
		include $this->template('system');
	}
}
