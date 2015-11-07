<?php
global $_GPC;

		$id = intval($_GPC['id']);

		$item = pdo_fetch("SELECT * FROM ".tablename('nsign_add')." WHERE id = :id" , array(':id' => $id));

		if (empty($item)) {

			message('抱歉，优惠内容不存在或是已经删除！', '', 'error');

		}

		if (!empty($item['thumb'])) {
			load()->func('file');
			file_delete($item['thumb']);

		}

		pdo_delete('nsign_add', array('id' => $item['id']));

		message('删除成功！', referer(), 'success');