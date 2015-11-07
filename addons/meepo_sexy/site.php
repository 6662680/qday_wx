<?php
defined('IN_IA') or exit('Access Denied');
define('CURRENT_VERSION', 0.1);

class Meepo_sexyModuleSite extends WeModuleSite {

	public function doWebSet(){
		global $_W,$_GPC;
		$id = $_GPC['id'];
		$weid = $_W['weid'];
		
		
		$settings = pdo_fetch("SELECT * FROM ".tablename('meepo_sexy_set')." WHERE weid=:weid limit 1",array(':weid'=>$weid));
		
		if(checksubmit()){
		
			$data = array(
				
				'weid'=>$weid,
				'name'=>$_GPC['school_name'],
				'url' => $_GPC['school_url'],
				'num' => $_GPC['school_num'],
				
			
			);
			
			if(empty($settings)){
				pdo_insert('meepo_sexy_set',$data);
			}else{
				pdo_update('meepo_sexy_set',$data,array('id'=>$settings['id']));
			}
			message('操作成功',$this->createWebUrl('set'));
			
		}
		
		include $this->template('set');
	
	}
}