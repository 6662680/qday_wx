<?php
global $_GPC, $_W;
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$category = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_shoptype')." WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder ASC");
			include $this->template('shoptype');
		} elseif ($operation == 'post') {
			$id = intval($_GPC['id']);
			
			if(!empty($id)) {
				$category = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_shoptype')." WHERE id = '$id'");
			} else {
				$category = array(
					'displayorder' => 0,
				);
			}			
			if (checksubmit('submit')) {
				if (empty($_GPC['typename'])) {
					message('抱歉，请输入分类名称！');
				}
				$data = array(
					'weid' => $_W['uniacid'],
					'name' => $_GPC['typename'],
					'displayorder' => intval($_GPC['displayorder']),
					'description' => $_GPC['description'],
					
				);
				if (!empty($id)) {
					pdo_update('jufeng_wcy_shoptype', $data, array('id' => $id));
				} else {
					pdo_insert('jufeng_wcy_shoptype', $data);
					$id = pdo_insertid();
				}
				message('更新分类成功！', $this->createWebUrl('shoptype', array('op' => 'display')), 'success');
			}
			include $this->template('shoptype');
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$category = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_shoptype')." WHERE id = '$id'");
			if (empty($category)) {
				message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('shoptype', array('op' => 'display')), 'error');
			}
			pdo_delete('jufeng_wcy_shoptype', array('id' => $id));
			message('分类删除成功！', $this->createWebUrl('shoptype', array('op' => 'display')), 'success');
		}
		?>