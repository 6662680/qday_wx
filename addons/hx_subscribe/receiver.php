<?php
/**
 * 推荐关注模块订阅器
 *
 * @author 华轩科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Hx_subscribeModuleReceiver extends WeModuleReceiver {
	public function receive() {
		global $_W,$_GPC;
		load()->model('mc');
		load()->func('communication');
		$event = $this->message['event'];
		$openid = $this->message['from'];
		$f_log = pdo_fetch("SELECT * FROM ".tablename('mc_mapping_fans') . " WHERE `uniacid` = '{$_W['uniacid']}' AND `openid` = '{$openid}'");
		if ($f_log['uid'] != 0) {
			pdo_update('hx_subscribe_data', array('uid'=>$f_log['uid']), array('openid' => $openid));
			$uid = $f_log['uid'];
		}else{
			$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
			$data = array(
				'uniacid' => $_W['uniacid'],
				'email' => md5($openid).'@qdaygroup.com',
				'salt' => random(8),
				'groupid' => $default_groupid,
				'createtime' => TIMESTAMP,
			);
			$data['password'] = md5($message['from'] . $data['salt'] . $_W['config']['setting']['authkey']);
			pdo_insert('mc_members', $data);
			$uid = pdo_insertid();
			pdo_update('mc_mapping_fans', array('uid'=>$uid),array('openid'=>$openid));
			pdo_update('hx_subscribe_data', array('uid'=>$uid), array('openid' => $openid));
		}
		$credit_type = isset($this->module['config']['credit_type']) ? $this->module['config']['credit_type'] : 'credit1';
		$credit_subscribe = isset($this->module['config']['credit_subscribe']) ? $this->module['config']['credit_subscribe'] : 5;
		$credit_lever_1 = isset($this->module['config']['credit_lever_1']) ? $this->module['config']['credit_lever_1'] : 2;
		$credit_lever_2 = isset($this->module['config']['credit_lever_2']) ? $this->module['config']['credit_lever_2'] : 1;
		if ($event == 'subscribe') {
			$s_log = pdo_fetch("SELECT * FROM " . tablename('hx_subscribe_data') . " WHERE `uniacid`='{$_W['uniacid']}' AND `openid`='{$openid}'");
			if (empty($s_log)) {//如果没记录
				$insert = array(
					'uniacid' => $_W['uniacid'],
					'openid' => $openid,
					'uid' => $uid,
					'from_uid' => '0',
					'sn' => time(),
					'follow' => '1',
					'article_id' => '0',
					'shouyi' => $credit_subscribe,
					'createtime' => TIMESTAMP,
					);
				pdo_insert('hx_subscribe_data',$insert);
				mc_credit_update($uid,$credit_type,$credit_subscribe,array('1','关注增加积分'));
			}else{//如果有记录
				if ($s_log['follow'] != 1) {//如果记录未关注
					$insert = array(
						'follow' => '1',
						//'shouyi' => $s_log['shouyi'] + $credit_subscribe,
						);
					pdo_update('hx_subscribe_data',$insert,array('id'=>$s_log['id']));
					mc_credit_update($uid,$credit_type,$credit_subscribe,array('1','关注增加积分'));
				}
				if (!empty($s_log['from_uid'])) {//如果来源ID不为空
					$from_user = pdo_fetch("SELECT * FROM " . tablename('hx_subscribe_data') . " WHERE `uniacid`='{$_W['uniacid']}' AND `uid`='{$s_log['from_uid']}'");
					if (!empty($from_user)) {
						$data = array(
							'shouyi' => $from_user['shouyi'] + $credit_lever_1,
							'zjrs' => $from_user['zjrs'] + 1,
							);
						pdo_update('hx_subscribe_data',$data,array('id'=>$from_user['id']));
						mc_credit_update($s_log['from_uid'],$credit_type,$credit_lever_1,array('1','推荐一级关注增加积分'));
						if (!empty($from_user['from_uid'])) {
							$from_user_2 = pdo_fetch("SELECT * FROM " . tablename('hx_subscribe_data') . " WHERE `uniacid`='{$_W['uniacid']}' AND `uid`='{$from_user['from_uid']}'");
							if (!empty($from_user_2)) {
								$data2 = array(
									'shouyi' => $from_user_2['shouyi'] + $credit_lever_2,
									'jjrs' => $from_user_2['jjrs'] + 1,
									);
								pdo_update('hx_subscribe_data',$data2,array('id'=>$from_user_2['id']));
								mc_credit_update($from_user['from_uid'],$credit_type,$credit_lever_2,array('1','推荐二级关注增加积分'));
							}
						}
					}
				}
			}
			//pdo_update('hx_subscribe_data',array('follow'=>1),array('openid'=>$openid));
		}
	}
}