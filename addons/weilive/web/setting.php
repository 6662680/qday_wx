<?php
	
	$setting = pdo_fetch("SELECT * FROM " . tablename('weilive_setting') . " WHERE weid = :weid ", array(':weid' => $weid));
	if (checksubmit('submit')) {
		$distance = is_numeric($_GPC['distance'])?$_GPC['distance']:message('请输入合法数字');
		$pagesize = is_numeric(intval($_GPC['pagesize']))?intval($_GPC['pagesize']):message('请输入合法数字');
		$pwd = $_GPC['pwd']!=''?$_GPC['pwd']:message('请输入密码');
		$data = array(
			'weid' => $weid,
			'gzurl' => $_GPC['gzurl'],
			'logo' => $_GPC['logo'],
			'helpurl' => $_GPC['helpurl'],
			'title' => $_GPC['title'],
			'description' => trim($_GPC['description']),
			'distance' => $distance,
			'pagesize' => $pagesize,
			'mobile' => $_GPC['mobile'],
			'pwd' => $pwd,
			'createtime' => TIMESTAMP
		);
		if (empty($setting)) {
			pdo_insert('weilive_setting', $data);
		} else {
			unset($data['dateline']);
			pdo_update('weilive_setting', $data, array('weid' => $weid));
		}
		message('操作成功', $this->createWebUrl('setting'), 'success');
	}
	
	include $this->template('web/setting');

?>