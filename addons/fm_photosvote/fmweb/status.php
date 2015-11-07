<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
//echo $rid;
		$insert = array(
			'status' => intval($_GPC['status'])
		);
		
		pdo_update($this->table_reply,$insert,array('rid' => $rid));
		if ($_GPC['status'] == 1) {
			$msg = '开启活动成功！';
		} else {
			$msg = '暂停活动成功！';
		}
		message($msg, referer(), 'success');
	