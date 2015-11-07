<?php
/**
 * 微招聘模块处理程序
 *
 * 
 */
defined('IN_IA') or exit('Access Denied');

class Thinkidea_rencaiModuleProcessor extends WeModuleProcessor {
	
	public $tablename = 'thinkidea_rencai_reply';
	
	public function respond() {
		global $_W, $_GPC;
		$rid = $this->rule;		
		$row = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE acid = :acid AND rid = :rid", array(':acid' => $_W['uniacid'], ':rid' => $rid));
		if($row){
			$url = $this->createMobileUrl('UserCompanyProfile', array('uid' => $row['id']));
		}else{
			$url = $this->createMobileUrl('Index');
		}
		return $this->respNews(
			array(
					'title' => $row['title'],
					'description' => $row['description'],
					'picurl'      => $row['avatar'],
					'url' => $this->createMobileUrl('Index')
			)
		);
	}

}