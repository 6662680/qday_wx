<?php
	global $_GPC, $_W;
	load()->func('tpl');
		$category = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC", array(), 'id');
		if (!empty($category)) {
			$children = '';
			foreach ($category as $cid => $cate) {
				if (!empty($cate['parentid'])) {
					$children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
				}
			}
		}

		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'post') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，菜品不存在或是已经删除！', '', 'error');
				}
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['title'])) {
					message('请输入菜品名称！');
				}
				if (empty($_GPC['pcate'])) {
					message('请选择店铺及菜系！');
				}
				$data = array(
					'weid' => intval($_W['uniacid']),
					'title' => $_GPC['title'],
					'pcate' => intval($_GPC['pcate']),
					'ccate' => intval($_GPC['ccate']),
					'status' => intval($_GPC['status']),
					'ishot' => intval($_GPC['ishot']),
					'preprice' => $_GPC['preprice'],
					'oriprice' => $_GPC['oriprice'],
					'hits' => intval($_GPC['hits']),
					'unit' => $_GPC['unit'],
					'thumb' => $_GPC['thumb'],
					'createtime' => TIMESTAMP,
				);
				if (!empty($_FILES['thumb']['tmp_name'])) {
					file_delete($_GPC['thumb_old']);
					$upload = file_upload($_FILES['thumb']);
					if (is_error($upload)) {
						message($upload['message'], '', 'error');
					}
					$data['thumb'] = $upload['path'];
				}
				if (empty($id)) {
					pdo_insert('jufeng_wcy_foods', $data);
				} else {
					unset($data['createtime']);
					pdo_update('jufeng_wcy_foods', $data, array('id' => $id));
				}
				message('菜品更新成功！', $this->createWebUrl('foods', array('op' => 'display')), 'success');
			}
		} else if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
			}

			if (!empty($_GPC['cate_2'])) {
				$cid = intval($_GPC['cate_2']);
				$condition .= " AND ccate = '{$cid}'";
			} elseif (!empty($_GPC['cate_1'])) {
				$cid = intval($_GPC['cate_1']);
				$condition .= " AND pcate = '{$cid}'";
			}

			if (isset($_GPC['status'])) {
				$condition .= " AND status = '".intval($_GPC['status'])."'";
			}

			$list = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_foods')." WHERE weid = '{$_W['uniacid']}' $condition ORDER BY status DESC, ishot DESC, hits DESC, id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('jufeng_wcy_foods') . " WHERE weid = '{$_W['uniacid']}'");
			$pager = pagination($total, $pindex, $psize);
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id, thumb FROM ".tablename('jufeng_wcy_foods')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，菜品不存在或是已经被删除！');
			}
			load()->func('file');
			if (!empty($row['thumb'])) {
				file_delete($row['thumb']);
			}
			pdo_delete('jufeng_wcy_foods', array('id' => $id));
			message('删除成功！', referer(), 'success');
		}
		include $this->template('foods');
				?>