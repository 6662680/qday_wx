<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台幻灯
 */
load()->func('file');
defined('IN_IA') or exit('Access Denied');
	global $_W,$_GPC;
	$op = !empty($_GPC['op'])?$_GPC['op']:'display';
	if ($op == 'display') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = '';
		$params = array();
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND title LIKE :keyword";
			$params[':keyword'] = "%{$_GPC['keyword']}%";
		}
		$list = pdo_fetchall("SELECT * FROM ".tablename("xcommunity_slide")." WHERE weid = '{$_W['weid']}' $condition ORDER BY displayorder DESC, id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename("xcommunity_slide") . " WHERE weid = '{$_W['weid']}' $condition");
		$pager = pagination($total, $pindex, $psize);
	} elseif ($op == 'post') {
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch("SELECT * FROM ".tablename("xcommunity_slide")." WHERE id = :id" , array(':id' => $id));
			if (empty($item)) {
				message('抱歉，幻灯片不存在或是已经删除！', '', 'error');
			}
		}
		if (checksubmit('submit')) {
			if (empty($_GPC['title'])) {
				message('标题不能为空，请输入标题！');
			}
			$data = array(
				'weid'         => $_W['weid'],
				'title'        => $_GPC['title'],
				'url'          => $_GPC['url'],
				'displayorder' => intval($_GPC['displayorder']),
			);
			if (!empty($_GPC['thumb'])) {
				$data['thumb'] = $_GPC['thumb'];
				file_delete($_GPC['thumb-old']);
			}
			if (empty($id)) {
				pdo_insert("xcommunity_slide", $data);
			} else {
				pdo_update("xcommunity_slide", $data, array('id' => $id));
			}
			message('幻灯片更新成功！', $this->createWebUrl('slide',array('op' => 'display')), 'success');
		}
	} elseif ($op == 'delete') {
		$id = intval($_GPC['id']);
		$row = pdo_fetch("SELECT id, thumb FROM ".tablename("xcommunity_slide")." WHERE id = :id", array(':id' => $id));
		if (empty($row)) {
			message('抱歉，幻灯片不存在或是已经被删除！');
		}
		if (!empty($row['thumb'])) {
			file_delete($row['thumb']);
		}
		pdo_delete("xcommunity_slide", array('id' => $id));
		message('删除成功！', referer(), 'success');
	}
	include $this->template('slide');