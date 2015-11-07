<?php
/**
 * 微名片
 *
 
 */
defined('IN_IA') or exit('Access Denied');

class Lxy_buscardModuleProcessor extends WeModuleProcessor {    
	public $table_reply = 'lxy_bussiness_card_reply';
	
	public function respond() {   	
    	
    	global $_W;
    	$rid = $this->rule;
    	$sql = "SELECT * FROM " . tablename($this->table_reply) . " WHERE `rid`=:rid LIMIT 1";
    	$row = pdo_fetch($sql, array(':rid' => $rid));
    	if (empty($row['id'])) {
    		return $this->respText("请确认您要展示的名片规则已维护") ;
    	}
    	return $this->respNews(array(
    				'Title' => $row['title'],
    				'Description' => htmlspecialchars_decode($row['description']),
    				'PicUrl' => $_W['attachurl'] . $row['picture'],
    				'Url' =>$this->createMobileUrl('viewcard',array('id'=>$row['cid'])) ,
    		));
    		 
    		
   }
 
}

