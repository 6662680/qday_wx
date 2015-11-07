<?php

defined('IN_IA') or exit('Access Denied');

//ini_set('display_errors','on');
//error_reporting(E_ALL);
define('ROOT_PATH', str_replace('site.php', '', str_replace('\\', '/', __FILE__)));
define('INC_PATH',ROOT_PATH.'inc/');
define('TEMPLATE_PATH','../../addons/meepo_nsign/template/style/');
class Meepo_nsignModuleSite extends WeModuleSite {	

	public function getProfileTiles() {
		
	}
	public function getThisMonth($date){
	
		$firstday = date("Y-m-01",strtotime($date));
		
		$lastday = date("Y-m-d",strtotime("$firstday +1 month -1 day"));
		
		return array($firstday,$lastday);
		
	}
	public function getLastMonth($date){
	
		$timestamp=strtotime($date);
		
		$firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
		
		$lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
		
		return array($firstday,$lastday);
		
	}
	public function getNextMonth($date){
	
		$timestamp=strtotime($date);
		
		$arr=getdate($timestamp);
		
		if($arr['mon'] == 12){
		
			$year=$arr['year'] +1;
			
			$month=$arr['mon'] -11;
			
			$firstday=$year.'-0'.$month.'-01';
			
			$lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
		
		}
		else{
		
			$firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)+1).'-01'));
			
			$lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
		
		}
		
		return array($firstday,$lastday);
		
	}
	
}