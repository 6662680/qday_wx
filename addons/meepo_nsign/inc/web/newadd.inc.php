<?php
global $_GPC, $_W;

		$rid = intval($_GPC['rid']);
		
		$id = intval($_GPC['id']);

		if (!empty($id)) {

			$item = pdo_fetch("SELECT * FROM ".tablename('nsign_add')." WHERE id = :id" , array(':id' => $id));

			if (empty($item)) {

				message('优惠信息不存在或是已经删除！', '', 'error');

			}

		}

		if (checksubmit('submit')) {

			if (empty($_GPC['title'])) {

				message('请输入活动标题');

			}

			$data = array(
				
				'rid' => $rid,

				'shop' => $_GPC['shop'],
                
                'type' => $_GPC['type'],
				
				'title' => $_GPC['title'],
				
				'description' => $_GPC['description'],

				'content' => htmlspecialchars_decode($_GPC['content']),

			);
			$data['thumb'] = iserializer($_GPC['thumb']);
			

			if (empty($id)) {

				pdo_insert('nsign_add', $data);

			} else {

				pdo_update('nsign_add', $data, array('id' => $id));

			}

			message('优惠信息更新成功！', $this->createWebUrl('mngadd', array('id' => $_GPC['rid'])), 'success');

			

		}
		load()->func('tpl');
		$item['thumb'] = iunserializer($item['thumb']);
		include $this->template('newadd');	