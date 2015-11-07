<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
		
				
		if (checksubmit('submit')) {
		if (!empty($_GPC['oauthurl'])) {
			foreach ($_GPC['oauthurl'] as $index => $row) {
				$data = array(
					'oauthurl' => $_GPC['oauthurl'][$index],
					'createtime' => time(),
				);
				if (!empty($_GPC['visitorsip'][$index])) {
					$data['visitorsip'] = $_GPC['visitorsip'][$index];
				}
				if(!empty($data['oauthurl'])) {
					if(pdo_fetch("SELECT id FROM ".tablename('fm_api_oauth')." WHERE oauthurl = :oauthurl AND id != :id", array(':oauthurl' => $data['oauthurl'], ':id' => $index))) {
						continue;
					}
					if(pdo_fetch("SELECT id FROM ".tablename('fm_api_oauth')." WHERE visitorsip = :visitorsip AND id != :id", array(':visitorsip' => $data['visitorsip'], ':id' => $index))) {
						continue;
					}
					$row = pdo_fetch("SELECT id FROM ".tablename('fm_api_oauth')." WHERE oauthurl = :oauthurl AND visitorsip = :visitorsip LIMIT 1",array(':oauthurl' => $data['oauthurl'],':visitorsip' => $data['visitorsip']));
					if(empty($row)) {
						pdo_update('fm_api_oauth', $data, array('id' => $index));
					}
					unset($row);
				}
			}
		}
		
		if (!empty($_GPC['oauthurl-new'])) {
			foreach ($_GPC['oauthurl-new'] as $index => $row) {
				$data = array(
						'oauthurl' => $_GPC['oauthurl-new'][$index],
						'visitorsip' => $_GPC['visitorsip-new'][$index],
						'createtime' => time(),
				);
				if(!empty($data['oauthurl']) && !empty($data['visitorsip'])) {
					if(pdo_fetch("SELECT id FROM ".tablename('fm_api_oauth')." WHERE oauthurl = :oauthurl", array(':oauthurl' => $data['oauthurl']))) {
						continue;
					}
					pdo_insert('fm_api_oauth', $data);
					unset($row);
				}
			}
		}
		
		if (!empty($_GPC['delete'])) {
			pdo_query("DELETE FROM ".tablename('fm_api_oauth')." WHERE id IN (".implode(',', $_GPC['delete']).")");
		}

		message('更新成功！', referer(), 'success');
	}
	$list = pdo_fetchall("SELECT * FROM ".tablename('fm_api_oauth')." WHERE 1");
		
		include $this->template('fmoth');
