<?php

	$title = '商家类别';

	if ($op == 'display') {
		if (!empty($_GPC['displayorder'])) {
			foreach ($_GPC['displayorder'] as $id => $displayorder) {
				pdo_update('weilive_category', array('displayorder' => $displayorder), array('id' => $id));
			}
			message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
		}
		$children = array();
		$category = pdo_fetchall("SELECT * FROM " . tablename('weilive_category') . " WHERE weid = ".$weid." ORDER BY parentid ASC, displayorder DESC");
		foreach ($category as $index => $row) {
			if (!empty($row['parentid'])) {
				$children[$row['parentid']][] = $row;
				unset($category[$index]);
			}
		}
		include $this->template('web/category');
	} elseif ($op == 'post') {
		$parentid = intval($_GPC['parentid']);
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$category = pdo_fetch("SELECT * FROM " . tablename('weilive_category') . " WHERE id = '$id'");
		} else {
			$category = array(
				'displayorder' => 0,
			);
		}

		if (!empty($parentid)) {
			$parent = pdo_fetch("SELECT id, name FROM " . tablename('weilive_category') . " WHERE id = '$parentid'");
			if (empty($parent)) {
				message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
			}
		}
		if (checksubmit('submit')) {
			if (empty($_GPC['name'])) {
				message('抱歉，请输入分类名称！');
			}

			$data = array(
				'weid' => $weid,
				'name' => $_GPC['name'],
				'logo' => $_GPC['logo'],
				'displayorder' => intval($_GPC['displayorder']),
				'isfirst' => intval($_GPC['isfirst']),
				'parentid' => intval($parentid),
			);

			if (!empty($id)) {
				unset($data['parentid']);
				pdo_update('weilive_category', $data, array('id' => $id));
			} else {
				pdo_insert('weilive_category', $data);
				$id = pdo_insertid();
			}
			message('更新分类成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
		}
		include $this->template('web/category');
	} elseif ($op == 'delete') {
		$id = intval($_GPC['id']);
		$category = pdo_fetch("SELECT id, parentid FROM " . tablename('weilive_category') . " WHERE id = '$id'");
		if (empty($category)) {
			message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display')), 'error');
		}
		pdo_delete('weilive_category', array('id' => $id, 'parentid' => $id), 'OR');
		message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
	}
?> 
