<?php
/**
 */
global $_W, $_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($op == 'display') {
	if (!empty($_GPC['displayorder'])) {
		foreach ($_GPC['displayorder'] as $id => $displayorder) {
			$update = array('displayorder' => $displayorder);
			pdo_update('fineness_article_category', $update, array('id' => $id));
		}
		message('分类排序更新成功！', 'refresh', 'success');
	}
	$children = array();
	$category = pdo_fetchall("SELECT * FROM ".tablename('fineness_article_category')." WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid, displayorder DESC, id");
	foreach ($category as $index => $row) {
		if (!empty($row['parentid'])){
			$children[$row['parentid']][] = $row;
			unset($category[$index]);
		}
	}

} elseif ($op == 'post') {
	load()->func('tpl');
	$parentid = intval($_GPC['parentid']);
	$id = intval($_GPC['id']);
	if(!empty($id)) {
		$category = pdo_fetch("SELECT * FROM ".tablename('fineness_article_category')." WHERE id = '$id' AND uniacid = {$_W['uniacid']} ");
		if(empty($category)) {
			message('分类不存在或已删除', '', 'error');
		}
	} else {
		$category = array(
			'displayorder' => 0,
		);
	}
	if (!empty($parentid)) {
		$parent = pdo_fetch("SELECT id, name FROM ".tablename('fineness_article_category')." WHERE id = '$parentid'");
		if (empty($parent)) {
            message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('category', array('do' => 'display')), 'error');
		}
	}

	if (checksubmit('submit')) {
		if (empty($_GPC['cname'])) {
			message('抱歉，请输入分类名称！');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $_GPC['cname'],
			'displayorder' => intval($_GPC['displayorder']),
			'parentid' => intval($parentid),
			'description' => $_GPC['description'],
			'template' => $_GPC['template'],
            'templatefile' => "themes/list".$_GPC['template'],
			'thumb' => $_GPC['thumb'],
            'createtime' => TIMESTAMP
		);

		if (!empty($id)) {
			unset($data['parentid']);
			pdo_update('fineness_article_category', $data, array('id' => $id));
		} else {
			pdo_insert('fineness_article_category', $data);
		}
        message('更新分类成功！', $this->createWebUrl('category', array('do' => 'display')), 'success');
	}
} elseif ($op == 'fetch') {
	$category = pdo_fetchall("SELECT id, name FROM ".tablename('fineness_article_category')." WHERE parentid = '".intval($_GPC['parentid'])."' ORDER BY id ASC, displayorder ASC, id ASC ");
	message($category, '', 'ajax');
} elseif ($op == 'delete') {
	load()->func('file');
	$id = intval($_GPC['id']);
	$category = pdo_fetch("SELECT id, parentid FROM ".tablename('fineness_article_category')." WHERE id = '$id'");
	if (empty($category)) {
        message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('do' => 'display')), 'error');
	}

	pdo_delete('fineness_article_category', array('id' => $id, 'parentid' => $id), 'OR');
    message('文章更新成功！', $this->createWebUrl('category', array('do' => 'display')), 'success');
}

include $this->template('category');