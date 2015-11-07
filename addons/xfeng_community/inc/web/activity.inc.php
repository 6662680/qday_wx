<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区活动
 */
defined('IN_IA') or exit('Access Denied');
	global $_W,$_GPC;
	$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$id = intval($_GPC['id']);
	if($op == 'post'){
		//查所有小区信息
		$regions = pdo_fetchAll("SELECT * FROM".tablename('xcommunity_region')."WHERE weid=:weid",array(":weid" => $_W['weid']));
		if (!empty($id)) {
			$item = pdo_fetch("SELECT * FROM".tablename('xcommunity_activity')."WHERE id=:id",array(':id' => $id));
			$regs = unserialize($item['regionid']);
			$starttime = !empty($item['starttime']) ? date('Y-m-d',$item['starttime']) : date('Y-m-d',timestamp);
			$endtime = !empty($item['endtime']) ? date('Y-m-d',$item['endtime']) : date('Y-m-d',timestamp);
		}
		if ($_W['ispost']) {
			$starttime = strtotime($_GPC['birth']['start']);
			$endtime   = strtotime($_GPC['birth']['end']);
			if (!empty($starttime) && $starttime==$endtime) {
				$endtime = $endtime+86400-1;
			}

			$data = array(
				'weid'       => $_W['weid'],
				'title'      => $_GPC['title'],
				'starttime'  => $starttime,
				'endtime'    => $endtime,
				'enddate'    => $_GPC['enddate'],
				'picurl'     => $_GPC['picurl'],
				'number'     => !empty($_GPC['number'])?$_GPC['number']:'1',
				'content'    => htmlspecialchars_decode($_GPC['content']),
				'status'     => $_GPC['status'],
				'createtime' => TIMESTAMP,
				'regionid'   => serialize($_GPC['regionid']),
			);
			if (empty($_GPC['id'])) {
				pdo_insert('xcommunity_activity',$data);
			}else{
				pdo_update('xcommunity_activity',$data,array('id' => $_GPC['id']));
			}
			message('更新成功',referer(),'success');
		}
	}elseif($op == 'display'){
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 20;
		$condition = '';
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
		}
		$list = pdo_fetchAll("SELECT * FROM".tablename('xcommunity_activity')."WHERE weid='{$_W['weid']}' $condition LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total =pdo_fetchcolumn("SELECT COUNT(*) FROM".tablename('xcommunity_activity')."WHERE weid='{$_W['weid']}'");
		$pager  = pagination($total, $pindex, $psize);
	}elseif($op == 'delete'){
		pdo_delete('xcommunity_activity',array('id' => $id));
		message('删除成功',referer(),'success');
	}elseif ($op == 'res') {
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 20;
		$condition = '';
		$params = array();
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND title LIKE :keyword";
			$params[':keyword'] = "%{$_GPC['keyword']}%";
		}
		$list = pdo_fetchAll("SELECT * FROM".tablename('xcommunity_res')." WHERE weid='{$_W['weid']}' $condition LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM".tablename('xcommunity_res')."WHERE weid='{$_W['weid']}'");
		$pager  = pagination($total, $pindex, $psize);
		$row = array();
		foreach ($list as $key => $value) {
			$rows = pdo_fetch("SELECT * FROM".tablename('xcommunity_activity')."WHERE id=:id",array(':id' => $value['id']));
			$row[]= array(
					'truename'   => $value['truename'],
					'mobile'     => $value['mobile'],
					'num'        => $value['num'],
					'sex'        => $value['sex'],
					'createtime' => $value['createtime'],
					'title'      => $rows['title'],
					'id'         => $value['id'],
				);
		}
		if (checksubmit('delete')) {
			pdo_delete('xcommunity_res', " id  IN  ('".implode("','", $_GPC['select'])."')");
			message('删除成功！',referer(),'success');
		}
	}
	include $this->template('activity');