<?php
		global $_GPC, $_W;
		if(empty($_GPC['op'])){ $operation = 'display';}
		else{$operation = $_GPC['op'];}
		if ($operation == 'display') {
			$print = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_print')." WHERE weid = '{$_W['uniacid']}'");
			for($i=0;$i<count($print);$i++){
			$print[$i][0] = pdo_fetch("SELECT name FROM ".tablename('jufeng_wcy_category')." WHERE id = '{$print[$i]['cateid']}'");
			}
		}
elseif ($operation == 'status') {
			$id = intval($_GPC['id']);
			if(!empty($id)) {
				$print = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_print')." WHERE id = '$id'");
				$print['name'] = pdo_fetch("SELECT name FROM ".tablename('jufeng_wcy_category')." WHERE id = '{$print['cateid']}'");
				include 'HttpClient.class.php';
			define('FEIE_HOST','115.28.225.82');
            define('FEIE_PORT',80);
			$deviceno = $print['deviceno'];
			if(!empty($_GPC['time'])) {
			$print['number'] = $this->queryOrderNumbersByTime($deviceno,$_GPC['time']);
			}
			$print['status'] = $this->queryPrinterStatus($deviceno);
			}
		}

		elseif ($operation == 'post') {
			$id = intval($_GPC['id']);
			$category = pdo_fetchall("SELECT id,name FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND parentid = 0");
			if(!empty($id)) {
				$print = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_print')." WHERE id = '$id'");
			} 		
			if (checksubmit('submit')) {
				if (empty($_GPC['deviceno']) || empty($_GPC['key']) || empty($_GPC['printtime'])) {
					message('抱歉，请正确输入打印机机器号、打印机key、打印联数！');
				}
				$data = array(
					'weid' => $_W['uniacid'],
					'cateid' => $_GPC['cateid'],
					'deviceno' => $_GPC['deviceno'],
					'key' => $_GPC['key'],
					'printtime' => $_GPC['printtime'],
					'qr' => $_GPC['qr'],
					'enabled' => $_GPC['enabled'],
					
				);
				if (!empty($id)) {
					pdo_update('jufeng_wcy_print', $data, array('id' => $id));
				} else {
					pdo_insert('jufeng_wcy_print', $data);
					$id = pdo_insertid();
				}
				message('更新打印机成功！', $this->createWebUrl('print', array('op' => 'display')), 'success');
			}
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$print = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_print')." WHERE id = '$id'");
			if (empty($print)) {
				message('抱歉，打印机不存在或是已经被删除！', $this->createWebUrl('print', array('op' => 'display')), 'error');
			}
			pdo_delete('jufeng_wcy_print', array('id' => $id));
			message('打印机删除成功！', $this->createWebUrl('print', array('op' => 'display')), 'success');
		}
		include $this->template('print');
		?>