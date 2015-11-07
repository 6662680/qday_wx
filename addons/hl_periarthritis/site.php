<?php
/**
 * 肩周炎抽奖模块
 *
 * [微动力] www.weixiamen.cn 5517286
 */
defined('IN_IA') or exit('Access Denied');

class hl_periarthritisModuleSite extends WeModuleSite {
	
	public $tablename = 'hl_periarthritis';
	
	public function doMobileindex() {
		global $_W, $_GPC;
		$rid = intval($_GPC['rid']);
		$reply = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid", array(':rid' => $rid));
		if (empty($reply)) {
			message('不存在或是已经被删除！');
		}
		$reply['content'] = strip_tags($reply['content']);
		$reply['content'] = str_replace(PHP_EOL, '', $reply['content']);   
		$reply['content'] = cutstr($reply['content'], 50, true);
		
		$useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
		/* include $this->template('shake');
		exit; */
		if(strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false ){
			include $this->template('tip');
		}else{
			include $this->template('shake');
		}
		
	}

}
