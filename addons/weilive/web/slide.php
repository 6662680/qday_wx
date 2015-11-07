<?php
	if(checksubmit('submit')) {
		if (!empty($_GPC['slide-new'])) {
			foreach ($_GPC['slide-new'] as $index => $row) {
				if (empty($row)) {
					continue;
				}
				$data = array(
					'weid' => $weid,
					'slide' => $_GPC['slide-new'][$index],
					'url' => $_GPC['url-new'][$index],
					'listorder' => $_GPC['listorder-new'][$index],
				);
				pdo_insert('weilive_slide', $data);
			}
		}
		if (!empty($_GPC['attachment'])) {
			foreach ($_GPC['attachment'] as $index => $row) {
				if (empty($row)) {
					continue;
				}
				$data = array(
					'weid' => $weid,
					'slide' => $_GPC['attachment'][$index],
					'url' => $_GPC['url'][$index],
					'listorder' => $_GPC['listorder'][$index],
					'isshow' => $_GPC['isshow'][$index],
				);
				pdo_update('weilive_slide', $data, array('id' => $index));
			}
		}
		message('幻灯片更新成功！', $this->createWebUrl('slide'));
	}
	
	if($op=='delete'){
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch("SELECT * FROM " . tablename('weilive_slide') . " WHERE id = :id", array(':id' => $id));
			if (empty($item)) {
				message('图片不存在或是已经被删除！');
			}
			pdo_delete('weilive_slide', array('id' => $item['id']));
		} else {
			$item['attachment'] = $_GPC['attachment'];
		}
		//file_delete($item['attachment']);
		message('删除成功！', referer(), 'success');
	}
	$photos = pdo_fetchall("SELECT * FROM " . tablename('weilive_slide') . " WHERE weid = :weid ORDER BY listorder DESC", array(':weid' => $weid));
	include $this->template('web/slide');
?>