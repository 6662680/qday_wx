<?php
		global $_GPC, $_W;
		load()->func('tpl');
		if(empty($_GPC['op'])){ $operation = 'display';}
		else{$operation = $_GPC['op'];}
		if ($operation == 'display') {
			
			$children = array();
			$category = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
			foreach ($category as $index => $row) {
				if (!empty($row['parentid'])){
					$children[$row['parentid']][] = $row;
					unset($category[$index]);
				}
			}
			include $this->template('category');
		} 
		
		elseif ($operation == 'post') {
			$parentid = intval($_GPC['parentid']);
			$id = intval($_GPC['id']);
			load()->model('mc');
			$groups = mc_groups();
			$shoptype = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_shoptype')." WHERE weid = '{$_W['uniacid']}'");
			if(!empty($id)) {
				$category = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE id = '$id'");
			} else {
				$category = array(
					'displayorder' => 0,
				);
			}
			$ptime1 = $category['time1'];
			$ptime2 = $category['time2'];
			$ptime3 = $category['time3'];
			$ptime4 = $category['time4'];
			if (!empty($parentid)) {
				$parent = pdo_fetch("SELECT id, name FROM ".tablename('jufeng_wcy_category')." WHERE id = '$parentid'");
				if (empty($parent)) {
					message('抱歉，店铺不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
				}
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['catename'])) {
					message('抱歉，请输入名称！');
				}
				$data = array(
					'weid' => $_W['uniacid'],
					'name' => $_GPC['catename'],
					'displayorder' => intval($_GPC['displayorder']),
					'parentid' => intval($parentid),
					'shouji' => $_GPC['shouji'],
					'sendprice' => $_GPC['sendprice'],
					'total' => $_GPC['total'],
					'typeid' => $_GPC['typeid'],
					'enabled' => $_GPC['enabled'],
					'description' => $_GPC['description'],
					'email' => $_GPC['email'],
					'time1' => $_GPC['time1'],
					'time2' => $_GPC['time2'],
					'time3' => $_GPC['time3'],
					'time4' => $_GPC['time4'],
					'thumb' => $_GPC['thumb'],
					'address' => $_GPC['address'],
					'loc_x' => $_GPC['loc_x'],
					'loc_y' => $_GPC['loc_y'],
					'mbgroup' => $_GPC['mbgroup'],
					
				);
				 if (!empty($_FILES['thumb']['tmp_name'])) {

                    file_delete($_GPC['thumb_old']);

                    $upload = file_upload($_FILES['thumb']);

                    if (is_error($upload)) {

                        message($upload['message'], '', 'error');

                    }

                    $data['thumb'] = $upload['path'];

                }
				if (!empty($id)) {
					unset($data['parentid']);
					pdo_update('jufeng_wcy_category', $data, array('id' => $id));
				} else {
					pdo_insert('jufeng_wcy_category', $data);
					$id = pdo_insertid();
				}
				message('更新成功！', $this->createWebUrl('category', array('op' => 'post','id' => $id)), 'success');
			}
			include $this->template('category');
		} 
		else if ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$category = pdo_fetch("SELECT id, parentid FROM ".tablename('jufeng_wcy_category')." WHERE id = '$id'");
			if (empty($category)) {
				message('抱歉，店铺或菜系不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display')), 'error');
			}
			pdo_delete('jufeng_wcy_category', array('id' => $id, 'parentid' => $id), 'OR');
			message('删除成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
		}
		?>