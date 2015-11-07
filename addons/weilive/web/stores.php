<?php

	$children = array();
	$category = pdo_fetchall("SELECT * FROM " . tablename('weilive_category') . " WHERE weid = ".$weid." ORDER BY parentid ASC, displayorder DESC", array(), 'id');
	if (!empty($category)) {
		$children = array();
		foreach ($category as $cid => $cate) {
			if (!empty($cate['parentid'])) {
				$children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
			}
		}
	} else {
		message('请先添加分类！', $this->createWebUrl('category', array('op' => 'post')), 'success');
	}

	if ($op == 'post') {
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch("SELECT * FROM " . tablename('weilive_stores') . " WHERE id = :id", array(':id' => $id));
			if (empty($item)) {
				message('抱歉，商家不存在或是已经删除！', '', 'error');
			}
		}
		if (checksubmit('submit')) {
			$data = array(
				'weid' => intval($weid),
				//'cityid' => intval($_GPC['cityid']),
				'displayorder' => intval($_GPC['displayorder']),
				'title' => trim($_GPC['title']),
				'logo' => trim($_GPC['logo']),
				'description' => trim($_GPC['description']),
				'pcate' => intval($_GPC['pcate']),
				'ccate' => intval($_GPC['ccate']),
				'business_time' => $_GPC['business_time'],
				'pwd' => $_GPC['pwd'],
				'level' => intval($_GPC['level']),
				'tel' => trim($_GPC['tel']),
				'location_p' => trim($_GPC['location_p']),
				'location_c' => trim($_GPC['location_c']),
				'location_a' => trim($_GPC['location_a']),
				'place' => trim($_GPC['place']),
				'lng' => trim($_GPC['lng']),
				'lat' => trim($_GPC['lat']),
				'status' => intval($_GPC['status']),
				'isfirst' => intval($_GPC['isfirst']),
				'dateline' => TIMESTAMP,
			);

			if (empty($data['title'])) {
				message('请输入商家名称！');
			}
			if (empty($data['pcate'])) {
				message('请选择商家分类！');
			}
			// if (!checkDatetime($data['starttime'])) {
				// message('请输入正确的时间格式！');
			// }
			// if (!checkDatetime($data['endtime'])) {
				// message('请输入正确的时间格式！');
			// }

//                if (empty($data['ccate'])) {
//                    message('请选择商家二级分类！');
//                }


			if (empty($id)) {
				pdo_insert('weilive_stores', $data);
			} else {
				unset($data['dateline']);
				pdo_update('weilive_stores', $data, array('id' => $id));
			}
			message('数据更新成功！', $this->createWebUrl('stores', array('op' => 'display')), 'success');
		}
	} elseif ($op == 'display') {
		if (!empty($_GPC['displayorder'])) {
			foreach ($_GPC['displayorder'] as $id => $displayorder) {
				pdo_update('weilive_stores', array('displayorder' => $displayorder), array('id' => $id));
			}
			message('排序更新成功！', $this->createWebUrl('stores', array('op' => 'display')), 'success');
		}

		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$condition = '';
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
		}

		if (!empty($_GPC['category_id'])) {
			$cid = intval($_GPC['category_id']);
			$condition .= " AND pcate = '{$cid}'";
		}

		if (isset($_GPC['status'])) {
			$condition .= " AND status = '" . intval($_GPC['status']) . "'";
		}

		$list = pdo_fetchall("SELECT * FROM " . tablename('weilive_stores') . " WHERE checked = 1 and weid = ".$weid." $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('weilive_stores') . " WHERE weid = ".$weid." $condition");

		$pager = pagination($total, $pindex, $psize);
	} elseif ($op == 'delete') {
		$id = intval($_GPC['id']);
		$row = pdo_fetch("SELECT * FROM " . tablename('weilive_stores') . " WHERE id = :id", array(':id' => $id));
		if (empty($row)) {
			message('抱歉，数据不存在或是已经被删除！');
		}
		pdo_delete('weilive_stores', array('id' => $id));
		pdo_delete('weilive_comment', array('storeid' => $id));
		pdo_delete('weilive_activity', array('storeid' => $id));
		message('删除成功！', referer(), 'success');
	} elseif ($op == 'check') {
		if (!empty($_GPC['displayorder'])) {
			foreach ($_GPC['displayorder'] as $id => $displayorder) {
				pdo_update('weilive_stores', array('displayorder' => $displayorder), array('id' => $id));
			}
			message('排序更新成功！', $this->createWebUrl('stores', array('op' => 'display')), 'success');
		}

		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$condition = '';
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
		}

		if (!empty($_GPC['category_id'])) {
			$cid = intval($_GPC['category_id']);
			$condition .= " AND pcate = '{$cid}'";
		}

		if (isset($_GPC['status'])) {
			$condition .= " AND status = '" . intval($_GPC['status']) . "'";
		}

		$list = pdo_fetchall("SELECT * FROM " . tablename('weilive_stores') . " WHERE weid = ".$weid." AND checked=0 $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('weilive_stores') . " WHERE checked=0 and weid = ".$weid." $condition");

		$pager = pagination($total, $pindex, $psize);
	} else if ($op == 'checkdetail') {
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch("SELECT * FROM " . tablename('weilive_stores') . " WHERE id = :id", array(':id' => $id));
			if (empty($item)) {
				message('抱歉，商家不存在或是已经删除！', '', 'error');
			}
		}
		if (checksubmit('submit')) {
			$data = array(
				'checked' => intval($_GPC['checked']),
				'status' => intval($_GPC['status']),
				'title' => $_GPC['title'],
			);
			pdo_update('weilive_stores', $data, array('id' => $id));
			message('数据更新成功！', $this->createWebUrl('stores', array('op' => 'check')), 'success');
		}
	}
	
	$hosts = pdo_fetchall("select id, realname from ".tablename('weilive_shophost')." where weid = ".$weid);
	$host = array();
	foreach($hosts as $h){
		$host[$h['id']] = $h['realname'];
	}
	
	include $this->template('web/stores');

?>