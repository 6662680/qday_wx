<?php 
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan isNOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

if(!empty($_W['uniacid'])) {
		$sql = 'SELECT * FROM ' . tablename('core_queue') . ' WHERE `uniacid`=:uniacid AND type = 2 ORDER BY `qid` ASC LIMIT 15';
	$pars = array();
	$pars[':uniacid'] = $_W['uniacid'];
	$cards = pdo_fetchall($sql, $pars);
	if(!empty($cards)) {
		load()->classs('coupon');
		foreach($cards as $li) {
			if(!empty($li['acid']) && !empty($li['message'])) {
				$acc = new coupon($li['acid']);
				$code = $acc->DecryptCode(array('encrypt_code' => $li['message']));
				if(is_error($code)) {
					continue;
				} else {
					$sumecode = $acc->ConsumeCode(array('code' => $code['code']));
					if(is_error($sumecode)) {
						continue;
					} else{
						pdo_delete('core_queue', array('uniacid' => $_W['uniacid'], 'id' => $li['id']));
						pdo_update('coupon_record', array('status' => 3), array('acid' => $li['acid'], 'code' => $code['code'], 'card_id' => $li['params']));
					}
				}
			}
		}
	}

	$sql = 'SELECT * FROM ' . tablename('core_queue') . ' WHERE `uniacid`=:uniacid AND type = 1 ORDER BY `qid` ASC LIMIT 50';
	$pars = array();
	$pars[':uniacid'] = $_W['uniacid'];
	$messages = pdo_fetchall($sql, $pars);
	
	if (!empty($messages)) {
		
		$qids = '';
		foreach($messages as &$message) {
			$message['message'] = iunserializer($message['message']);
			$message['params'] = iunserializer($message['params']);
			$message['response'] = iunserializer($message['response']);
			$message['keyword'] = iunserializer($message['keyword']);
		
			$qids .= $message['qid'] . ',';
		}
		
		$qids = trim($qids, ',');
		$sql = 'DELETE FROM ' . tablename('core_queue') . " WHERE `qid` IN ({$qids})";
		pdo_query($sql);
		
		load()->model('module');
		$modules = uni_modules();
		$core = array();
		$core['name'] = 'core';
		$core['subscribes'] = array('core');
		array_unshift($modules, $core);
		
		foreach($messages as $msg) {
			foreach($modules as $m) {
				if(!empty($m['subscribes'])) {
					if ($m['name'] == 'core' 
					|| (empty($msg['message']['event']) && in_array($msg['message']['type'], $m['subscribes'])) 
					|| (!empty($msg['message']['event']) && in_array($msg['message']['event'], $m['subscribes']))) {
						$obj = WeUtility::createModuleReceiver($m['name']);
						$obj->message = $msg['message'];
						$obj->params = $msg['params'];
						$obj->response = $msg['response'];
						$obj->keyword = $msg['keyword'];
						$obj->module = $m;
						$obj->uniacd = $msg['uniacid'];
						$obj->acid = $msg['acid'];
						if(method_exists($obj, 'receive')) {
							@$obj->receive();
						}
					}
				}
			}
		}
	}
}