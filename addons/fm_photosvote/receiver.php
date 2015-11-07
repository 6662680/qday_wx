<?php
/**
 * 女神来了模块订阅器
 *
 */


defined('IN_IA') or exit('Access Denied');

class Fm_photosvoteModuleReceiver extends WeModuleReceiver {
	public function receive() {
		global $_W, $_GPC;
		//load()->func('communication');
		$type = $this->message['type'];
		$uniacid = $_W['uniacid'];
		$acid = $_W['acid'];
		//$uniacid = $_W['acid'];
		
		//$rid = intval($this->params['rule']);		
		//$rid = $this->rule;
		
		$openid = $this->message['from'];
		
		
		//file_put_contents(IA_ROOT.'/receive.txt', iserializer($cfg));


		$event = $this->message['event'];
		//file_put_contents(IA_ROOT.'/receive.txt', iserializer($event));
		//	exit;
		
		$cfg = $this->module['config'];
		file_put_contents(IA_ROOT.'/receive.txt', iserializer($event));
		if ($event == 'unsubscribe') {
			$record = array(
				'updatetime'=> TIMESTAMP,
				'follow' 	=> '0',
				'unfollowtime'=> TIMESTAMP
			);
			pdo_update('mc_mapping_fans', $record, array('openid' => $openid, 'acid' => $acid, 'uniacid' => $uniacid));

			if ($cfg['isopenjsps']) {
				$fmvotelog = pdo_fetchall("SELECT * FROM ".tablename('fm_photosvote_votelog')." WHERE from_user = :from_user and uniacid = :uniacid LIMIT 1", array(':from_user' => $openid,':uniacid' => $uniacid));
				
				foreach ($fmvotelog as $log) {
					$fmprovevote = pdo_fetch("SELECT * FROM ".tablename('fm_photosvote_provevote')." WHERE from_user = :from_user and uniacid = :uniacid LIMIT 1", array(':from_user' => $log['tfrom_user'],':uniacid' => $uniacid));
					pdo_update('fm_photosvote_provevote', array(
						'lasttime' => TIMESTAMP,
						'photosnum' => $fmprovevote['photosnum'] - 1,
						'hits' => $fmprovevote['hits'] - 1,					
					), array(
						'from_user' => $log['tfrom_user'],
						'uniacid' => $uniacid,
					));
				}
				
				pdo_delete('fm_photosvote_votelog', array('from_user' => $openid));
				pdo_delete('fm_photosvote_bbsreply', array('from_user' => $openid));

			}
			
			
		}elseif($event == 'subscribe'){
			$wechats = pdo_fetch("SELECT * FROM ".tablename('account_wechats') . " WHERE uniacid = :uniacid ", array(':uniacid' => $_W['uniacid']));
			$token =iunserializer($wechats['access_token']);

			$arrlog = pdo_fetch("SELECT * FROM ".tablename('mc_mapping_fans') . " WHERE uniacid = :uniacid AND openid = :openid", array(':uniacid' => $_W['uniacid'],':openid' => $openid));

			$access_token=$token['token'];
			$expire =$token['expire'];
			if(time()>=$expire || empty($access_token)){
			$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$wechats['key']."&secret=".$wechats['secret'];  
			$html=file_get_contents($url); 
			$arr = json_decode($html,true);
			$access_token=$arr['access_token'];
			$record = array();
			$record['token'] = $access_token;//保存全局票据
			$record['expire'] =time() + 3600;
			$row = array();
			$row['access_token'] = iserializer($record);//序列化保存
			pdo_update('account_wechats', $row, array('uniacid' => $_W['uniacid']));
			}
			$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";  
			$html=file_get_contents($url); 
			$re = @json_decode($html, true);


			if(!empty($arrlog)){
				$data = array(
				'nickname' => $re['nickname'],
				'unionid' => $re['unionid'],
				);
				pdo_update('mc_mapping_fans',$data, array('openid'=>$openid));
			}else{
				$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
				$nickname=$re['nickname'];
				$data = array(
				'uniacid' => $_W['uniacid'],
				'nickname' => $re['nickname'],
				'avatar' => $re['headimgurl'],
				'groupid' => $default_groupid,
				'createtime' => TIMESTAMP,
				);
				pdo_insert('mc_members', $data);
				$id = pdo_insertid();
				$data = array(
				'nickname' => $re['nickname'],
				'unionid' => $re['unionid'],
				'uid' => $id,
				);
				pdo_update('mc_mapping_fans',$data, array('openid'=>$openid));
			}
				
		}

	}
	
		
}
