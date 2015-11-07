<?php
		
	$id = intval($_GPC['id']);
	if ($op == 'post') {
		$starttime = strtotime($_GPC['starttime']);
		$endtime   = strtotime($_GPC['endtime']);
		if (!empty($starttime) && $starttime==$endtime) {
			$endtime = $endtime+86400-1;
		}
		if (!empty($id)) {
			$sql             = "select * from".tablename('weilive_prize')."where id=".$id;
			$item            = pdo_fetch($sql);
			$activation_code = iunserializer($item['activation_code']);
			$acode           = @implode("\n", $activation_code);
			//print_r($activationcode);
		}
		$data = array(
			'weid'            => $weid,
			'title'           => $_GPC['title'],
			'integral'        => $_GPC['integral'],
			'number'          => $_GPC['number'],
			'thumb'          => $_GPC['thumb'],
			'createtime'      => $_W['timestamp'],
			'starttime'       => $starttime,
			'endtime'         => $endtime,
			'inkind'          => intval($_GPC['inkind']),
			'description'     => $_GPC['description'],
			'activation_code' => '',
			'activation_url'  => '',
			);
		if ($_GPC['inkind']==1) {
				
			$activationcode          = explode("\n", $_GPC['activation_code']);
			$data['activation_code'] = iserializer($activationcode);
			$data['activation_url']  = $_GPC['activation_url'];
			$data['number']          = count($activationcode);
			//print_r($_GPC['activation_code']);exit;
		}
		if ($_GPC['inkind']==2) {
				
			$data['activation_url']  = $_GPC['activation_url'];
			//print_r($_GPC['activation_code']);exit;
		}
		if ($_W['ispost']) {
				
			if (empty($id)) {
				pdo_insert('weilive_prize',$data);
				message('添加成功',$this->createWebUrl('award',array('op' => 'display')),'success');
			}else{

				pdo_update('weilive_prize',$data,array('id' => $id));
				message('更新成功',$this->createWebUrl('award',array('op' => 'display')),'success');
			}
		}
	}elseif ($op == 'display') {
		$sql = "select * from".tablename('weilive_prize')."where weid = ".$weid." order by inkind asc, createtime desc";
		$list = pdo_fetchall($sql);
	}elseif ($op == 'delete') {
		pdo_delete('weilive_prize',array('id' => $id));
		message('删除成功',referer(),'success');
	}
	include $this->template('web/award');
?>