<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$do = !empty($_GPC['do']) ? $_GPC['do'] : 'display';

if ($do == 'post') {
	$_W['page']['title'] = '编辑字段 - 用户设置 - 用户管理';
	$id = intval($_GPC['id']);

	if (checksubmit('submit')) {
		if (empty($_GPC['title'])) {
			message('抱歉，请填写资料名称！');
		}
		$data = array(
			'title' => $_GPC['title'],
			'description' => $_GPC['description'],
			'displayorder' => intval($_GPC['displayorder']),
			'available' => intval($_GPC['available']),
			'unchangeable' => intval($_GPC['unchangeable']),
			'showinregister' => intval($_GPC['showinregister']),
			'required' => intval($_GPC['required']),
		);
		if (empty($id)) {
			$data['field'] = trim($_GPC['field']);
			pdo_insert('profile_fields', $data);
		} else {
			pdo_update('profile_fields', $data, array('id' => $id));
		}
		message('更新粉丝字段成功！', url('user/fields'));
	}

	if (!empty($id)) {
		$item = pdo_fetch("SELECT * FROM ".tablename('profile_fields')." WHERE id = :id", array(':id' => $id));
	}
	template('user/fields');
} else {
	$_W['page']['title'] = '字段管理 - 用户设置 - 用户管理';
	if (checksubmit('submit')) {
		if (!empty($_GPC['displayorder'])) {
			foreach ($_GPC['displayorder'] as $id => $displayorder) {
				pdo_update('profile_fields', array(
					'displayorder' => intval($displayorder),
					'available' => intval($_GPC['available'][$id]),
					'showinregister' => intval($_GPC['showinregister'][$id]),
					'required' => intval($_GPC['required'][$id]),
				), array('id' => $id));
			}
		}
		message('资料设置更新成功！', referer(), 'success');
	}
	$fields = pdo_fetchall("SELECT * FROM ".tablename('profile_fields')." ORDER BY displayorder DESC");
	template('user/fields');
}