<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
	if($_W['isajax']) {
			//$uniacid = $_W['acid'];//当前公众号ID	
			
			$rid = $_GPC['rid'];
						
					
			if (!empty($rid)) {
				$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			}
			
			
	
			include $this->template('subscribe');
			exit();
		}