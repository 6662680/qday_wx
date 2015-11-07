<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
define('IN_API', true);
require_once './framework/bootstrap.inc.php';
load()->model('reply');
load()->app('common');
load()->classs('wesession');
$hash = $_GPC['hash'];
if(!empty($hash)) {
	$id = pdo_fetchcolumn("SELECT acid FROM " . tablename('account') . " WHERE hash = :hash", array(':hash' => $hash));
}
if(empty($id)) {
	$id = intval($_GPC['id']);
}
if (!empty($id)) {
	$_W['account'] = account_fetch($id);
}
if(empty($_W['account'])) {
	exit('initial error hash or id');
}
if(empty($_W['account']['token'])) {
	exit('initial missing token');
}

$_W['acid'] = $_W['account']['acid'];
$_W['from'] == 'api';
$_W['uniacid'] = $_W['account']['uniacid'];
$_W['uniaccount'] = uni_fetch($_W['uniacid']);
$_W['account']['groupid'] = $_W['uniaccount']['groupid'];
$_W['account']['qrcode'] = "{$_W['attachurl']}qrcode_{$_W['acid']}.jpg?time={$_W['timestamp']}";
$_W['account']['avatar'] = "{$_W['attachurl']}headimg_{$_W['acid']}.jpg?time={$_W['timestamp']}";

$_W['modules'] = uni_modules();

$engine = new WeEngine();
$settings = setting_load('copyright');
if (!empty($settings['copyright']['status'])) {
	$engine->died('抱歉，站点已关闭，关闭原因：' . $settings['copyright']['reason']);
}

if($_W['isajax'] && $_W['ispost'] && $_GPC['flag'] == 1) {
	$engine->encrypt();
}
if($_W['isajax'] && $_W['ispost'] && $_GPC['flag'] == 2) {
	$engine->decrypt();
}
load()->func('compat.biz');
$_W['isajax'] = false;
$engine->start();


class WeEngine {
	
	private $account = null;
	
	private $modules = array();
	
	public $keyword = array();
	
	public $message = array();

	
	public function __construct() {
		global $_W;
		$this->account = WeAccount::create($_W['account']);
		$this->modules = array_keys($_W['modules']);
		$this->modules[] = 'cover';
		$this->modules[] = 'default';
		$this->modules = array_unique($this->modules);
	}

	
	public function encrypt() {
		global $_W;
		if(empty($this->account)) {
			exit('Miss Account.');
		}
		$timestamp = TIMESTAMP;
		$nonce = random(5);
		$token = $_W['account']['token'];
		$signkey = array($token, TIMESTAMP, $nonce);
		sort($signkey, SORT_STRING);
		$signString = implode($signkey);
		$signString = sha1($signString);

		$_GET['timestamp'] = $timestamp;
		$_GET['nonce'] = $nonce;
		$_GET['signature'] = $signString;
		$postStr = file_get_contents('php://input');
		if(!empty($_W['account']['encodingaeskey']) && strlen($_W['account']['encodingaeskey']) == 43 && !empty($_W['account']['key']) && $_W['setting']['development'] != 1) {
			$data = $this->account->encryptMsg($postStr);
			$array = array('encrypt_type' => 'aes', 'timestamp' => $timestamp, 'nonce' => $nonce, 'signature' => $signString, 'msg_signature' => $data[0], 'msg' => $data[1]);
		} else {
			$data = array('', '');
			$array = array('encrypt_type' => '', 'timestamp' => $timestamp, 'nonce' => $nonce, 'signature' => $signString, 'msg_signature' => $data[0], 'msg' => $data[1]);
		}
		exit(json_encode($array));
	}

	
	public function decrypt() {
		global $_W;
		if(empty($this->account)) {
			exit('Miss Account.');
		}
		$postStr = file_get_contents('php://input');
		if(!empty($_W['account']['encodingaeskey']) && strlen($_W['account']['encodingaeskey']) == 43 && !empty($_W['account']['key']) && $_W['setting']['development'] != 1) {
			$resp = $this->account->local_decryptMsg($postStr);
		} else {
			$resp = $postStr;
		}
		exit($resp);
	}

	
	public function start() {
		global $_W;
		if(empty($this->account)) {
			exit('Miss Account.');
		}
		if(!$this->account->checkSign()) {
			exit('Check Sign Fail.');
		}
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
			$row = array();
			$row['isconnect'] = 1;
			pdo_update('account', $row, array('acid' => $_W['acid']));
			exit($_GET['echostr']);
		}
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
			$postStr = file_get_contents('php://input');
						if(!empty($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
				$postStr = $this->account->decryptMsg($postStr);
			}
			$message = $this->account->parse($postStr);
			
			$this->message = $message;
			if(empty($message)) {
				WeUtility::logging('waring', 'Request Failed');
				exit('Request Failed');
			}
			$_W['openid'] = $message['from'];
			$_W['fans'] = array('from_user' => $_W['openid']);
			
			$this->booking($message);
			if($message['event'] == 'unsubscribe') {
				$this->receive(array(), array(), array());
				exit();
			}
			$sessionid = md5($message['from'] . $message['to'] . $_W['uniacid']);
			session_id($sessionid);
			WeSession::start($_W['uniacid'], $_W['openid']);
			
			$_SESSION['openid'] = $_W['openid'];
			
			WeUtility::logging('trace', $message);
			$pars = $this->analyze($message);
			$pars[] = array(
				'message' => $message,
				'module' => 'default',
				'rule' => '-1',
			);
			$hitParam['rule'] = -2;
			$hitParam['module'] = '';
			$hitParam['message'] = $message;

			$hitKeyword = array();
			$response = array();
			foreach($pars as $par) {
				if(empty($par['module'])) {
					continue;
				}
				$par['message'] = $message;
				$response = $this->process($par);
				if($this->isValidResponse($response)) {
					$hitParam = $par;
					if(!empty($par['keyword'])) {
						$hitKeyword = $par['keyword'];
					}
					break;
				}
			}
			if($hitParam['module'] == 'default' && is_array($response) && is_array($response['params'])) {
				foreach($response['params'] as $par) {
					if(empty($par['module'])) {
						continue;
					}
					$response = $this->process($par);
					if($this->isValidResponse($response)) {
						$hitParam = $par;
						if(!empty($par['keyword'])) {
							$hitKeyword = $par['keyword'];
						}
						break;
					}
				}
			}
			WeUtility::logging('params', $hitParam);
			WeUtility::logging('response', $response);
			$resp = $this->account->response($response);
			$resp = $this->clip($resp, $hitParam);
						if(!empty($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
				$resp = $this->account->encryptMsg($resp);
				$resp = $this->account->xmlDetract($resp);
			}
			echo $resp;
			ob_flush();
			flush();
			$this->receive($hitParam, $hitKeyword, $response);
			ob_end_clean();
			exit();
		}
		WeUtility::logging('waring', 'Request Failed');
		exit('Request Failed');
	}

	private function isValidResponse($response) {
		if(is_array($response)) {
			if($response['type'] == 'text' && !empty($response['content'])) {
				return true;
			}
			if($response['type'] == 'news' && !empty($response['items'])) {
				return true;
			}
			if(!in_array($response['type'], array('text', 'news', 'image'))) {
				return true;
			}
		}
		return false;
	}

	
	private function booking($message) {
		global $_W;
		$setting = uni_setting($_W['uniacid'], array('passport'));
		
		load()->model('mc');
		$fans = mc_fansinfo($message['from']);
		
		if(!empty($fans)) {
			$rec = array();
			if (!empty($fans['follow'])) {
				if ($message['event'] == 'unsubscribe') {
					$rec['follow'] = 0;
					$rec['followtime'] = 0;
					$rec['unfollowtime'] = $message['time'];
				}
			} else {
				if ($message['event'] != 'unsubscribe' && $message['event'] != 'ShakearoundUserShake') {
					$rec['follow'] = 1;
					$rec['followtime'] = $message['time'];
					$rec['unfollowtime'] = 0;
				}
			}
			
			$member = array();
			if(!empty($fans['uid'])){
				$member = mc_fetch($fans['uid']);
			}
			if (empty($member)) {
				if (!isset($setting['passport']) || empty($setting['passport']['focusreg'])) {
					$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
															$data = array(
						'uniacid' => $_W['uniacid'],
						'email' => md5($message['from']).'@qdaygroup.com',
						'salt' => random(8),
						'groupid' => $default_groupid,
						'createtime' => TIMESTAMP,
					);
					$data['password'] = md5($message['from'] . $data['salt'] . $_W['config']['setting']['authkey']);
					pdo_insert('mc_members', $data);
					$rec['uid'] = pdo_insertid();
				}
			}
			
			if(!empty($rec)){
				pdo_update('mc_mapping_fans', $rec, array(
					'acid' => $_W['acid'],
					'openid' => $message['from'],
					'uniacid' => $_W['uniacid']
				));
			}
		} else {
			$rec = array();
			$rec['acid'] = $_W['acid'];
			$rec['uniacid'] = $_W['uniacid'];
			$rec['uid'] = 0;
			$rec['openid'] = $message['from'];
			$rec['salt'] = random(8);
			if ($message['event'] == 'unsubscribe') {
				$rec['follow'] = 0;
				$rec['followtime'] = 0;
				$rec['unfollowtime'] = $message['time'];
			} else {
				$rec['follow'] = 1;
				$rec['followtime'] = $message['time'];
				$rec['unfollowtime'] = 0;
			}
						if (!isset($setting['passport']) || empty($setting['passport']['focusreg'])) {
								$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
												$data = array(
					'uniacid' => $_W['uniacid'],
					'email' => md5($message['from']).'@qdaygroup.com',
					'salt' => random(8),
					'groupid' => $default_groupid,
					'createtime' => TIMESTAMP,
				);
				$data['password'] = md5($message['from'] . $data['salt'] . $_W['config']['setting']['authkey']);
				pdo_insert('mc_members', $data);
				$rec['uid'] = pdo_insertid();
			}
			pdo_insert('mc_mapping_fans', $rec);
		}
	}

	
	private function clip($resp, $par) {
		$mapping = array(
			'[from]' => $par['message']['from'],
			'[to]' => $par['message']['to'],
			'[rule]' => $par['rule']
		);

		return str_replace(array_keys($mapping), array_values($mapping), $resp);
	}
	
	private function receive($par, $keyword, $response) {
		global $_W;
		if (in_array($this->message['event'], array('subscribe', 'unsubscribe')) || in_array($this->message['type'], array('subscribe', 'unsubscribe'))) {
			$modules = uni_modules();
			$core = array();
			$core['name'] = 'core';
			$core['subscribes'] = array('core');
			array_unshift($modules, $core);
			foreach($modules as $m) {
				if(!empty($m['subscribes'])) {
					if ($m['name'] == 'core' || in_array($this->message['type'], $m['subscribes']) 
						|| in_array($this->message['event'], $m['subscribes'])) {
						$obj = WeUtility::createModuleReceiver($m['name']);
						$obj->message = $this->message;
						$obj->params = $par;
						$obj->response = $response;
						$obj->keyword = $keyword;
						$obj->module = $m;
						$obj->uniacid = $_W['uniacid'];
						$obj->acid = $_W['acid'];
						if(method_exists($obj, 'receive')) {
							@$obj->receive();
						}
					}
				}
			}
		} else {
			$row = array();
			$row['uniacid'] = $_W['uniacid'];
			$row['acid'] = $_W['acid'];
			$row['dateline'] = $par['message']['time'];
			$row['message'] = iserializer($par['message']);
			$row['keyword'] = iserializer($keyword);
			unset($par['message']);
			unset($par['keyword']);
			$row['params'] = iserializer($par);
			$row['response'] = iserializer($response);
			$row['module'] = $par['module'];
			$row['type'] = 1;
			pdo_insert('core_queue', $row);
		}
	}

	
	private function analyze(&$message) {
		$params = array();
		if(in_array($message['type'], array('event', 'qr'))) {
			$params = call_user_func_array(array($this, 'analyze' . $message['type']), array(&$message));
			if(!empty($params)) {
				return (array)$params;
			}
		}
		if(!empty($_SESSION['__contextmodule']) && in_array($_SESSION['__contextmodule'], $this->modules)) {
			if($_SESSION['__contextexpire'] > TIMESTAMP) {
				if($_SESSION['__contextpriority'] < 255 && $message['type'] == 'text') {
					$params += $this->analyzeText($message, intval($_SESSION['__contextpriority']));
				}
				$params[] = array(
					'message' => $message,
					'module' => $_SESSION['__contextmodule'],
					'rule' => $_SESSION['__contextrule'],
					'priority' => $_SESSION['__contextpriority'],
					'context' => true
				);
				return $params;
			} else {
				unset($_SESSION);
				session_destroy();
			}
		}

		if(method_exists($this, 'analyze' . $message['type'])) {
			$temp = call_user_func_array(array($this, 'analyze' . $message['type']), array(&$message));
			if(!empty($temp) && is_array($temp)){
				$params += $temp;
			}
		} else {
			$params += $this->handler($message['type']);
		}

		return $params;
	}
	
	private function analyzeSubscribe(&$message) {
		global $_W;
		$params = array();
		$message['type'] = 'text';
		$message['redirection'] = true;
		if(!empty($message['scene'])) {
			$message['source'] = 'qr';
			$sceneid = floatval($message['scene']);
			$qr = pdo_fetch("SELECT `id`, `keyword` FROM " . tablename('qrcode') . " WHERE `qrcid` = '{$sceneid}' AND `uniacid` = '{$_W['uniacid']}'");
			if(!empty($qr)) {
				$message['content'] = $qr['keyword'];
				$params += $this->analyzeText($message);
			}
		}
		$message['source'] = 'subscribe';
		$setting = uni_setting($_W['uniacid'], array('welcome'));
						if(!empty($setting['welcome'])) {
			$message['content'] = $setting['welcome'];
			$params += $this->analyzeText($message);
		}

		return $params;
	}

	private function analyzeQR(&$message) {
		global $_W;
		$params = array();
		$message['type'] = 'text';
		$message['redirection'] = true;
		if(!empty($message['scene'])) {
			$message['source'] = 'qr';
			$sceneid = floatval($message['scene']);
			$qr = pdo_fetch("SELECT `id`, `keyword` FROM " . tablename('qrcode') . " WHERE `qrcid` = '{$sceneid}' AND `uniacid` = '{$_W['uniacid']}'");
			if(!empty($qr)) {
				$message['content'] = $qr['keyword'];
				$params += $this->analyzeText($message);
			}
		}

		return $params;
	}

	public function analyzeText(&$message, $order = 0) {
		global $_W;
		
		$pars = array();
		
		$order = intval($order);
		if(!isset($message['content'])) {
			return $pars;
		}
		
		$condition = <<<EOF
`uniacid` IN ( 0, {$_W['uniacid']} )
AND 
(
	( `type` = 1 AND `content` = :c1 )
	or
	( `type` = 2 AND instr(:c2, `content`) )
	or
	( `type` = 3 AND :c3 REGEXP `content` )
	or
	( `type` = 4 )
)
AND `status`=1
EOF;
		
		$params = array();
		$params[':c1'] = $message['content'];
		$params[':c2'] = $message['content'];
		$params[':c3'] = $message['content'];
		
		if (intval($order) > 0) {
			$condition .= " AND `displayorder` > :order";
			$params[':order'] = $order;
		}
		
		$keywords = reply_keywords_search($condition, $params);
		if(empty($keywords)) {
			return $pars;
		}
		foreach($keywords as $keyword) {
			$params = array(
				'message' => $message,
				'module' => $keyword['module'],
				'rule' => $keyword['rid'],
				'priority' => $keyword['displayorder'],
				'keyword' => $keyword
			);
			$pars[] = $params;
		}
		return $pars;
	}
	
	private function analyzeEvent(&$message) {
		if (strtolower($message['event']) == 'subscribe') {
			return $this->analyzeSubscribe($message);
		}
		if (strtolower($message['event']) == 'click') {
			$message['content'] = strval($message['eventkey']);
			return $this->analyzeClick($message);
		}
		if (in_array($message['event'], array('pic_photo_or_album', 'pic_weixin', 'pic_sysphoto'))) {
			if (!empty($message['sendpicsinfo']['count'])) {
				foreach ($message['sendpicsinfo']['piclist'] as $item) {
					pdo_insert('menu_event', array(
						'uniacid' => $GLOBALS['_W']['uniacid'],
						'keyword' => $message['eventkey'],
						'type' => $message['event'],
						'picmd5' => $item,
					));
				}
			}
			return true;
		}
		if (!empty($message['eventkey'])) {
			$message['content'] = strval($message['eventkey']);
			$message['type'] = 'text';
			$message['redirection'] = true;
			$message['source'] = $message['event'];
			return $this->analyzeText($message);
		}
	}
	
	private function analyzeClick(&$message) {
		if(!empty($message['content']) || $message['content'] !== '') {
			$message['type'] = 'text';
			$message['redirection'] = true;
			$message['source'] = 'click';

			return $this->analyzeText($message);
		}

		return array();
	}
	
	private function analyzeImage(&$message) {
		load()->func('communication');
		if (!empty($message['picurl'])) {
			$response = ihttp_get($message['picurl']);
			if (!empty($response)) {
				$md5 = md5($response['content']);
				$event = pdo_fetch("SELECT keyword, type FROM ".tablename('menu_event')." WHERE picmd5 = '$md5'");
				if (!empty($event['keyword'])) {
					pdo_delete('menu_event', array('picmd5' => $md5));
					$message['content'] = $event['keyword'];
					$message['type'] = 'text';
					$message['redirection'] = true;
					$message['source'] = $event['type'];
					return $this->analyzeText($message);
				}
			}
			return $this->handler('image');
		}
	}
	
	private function analyzeVoice(&$message) {
		if (!empty($message['recognition'])) {
			$message['type'] = 'text';
			$message['redirection'] = true;
			$message['source'] = 'voice';
			$message['content'] = $message['recognition'];
			return $this->analyzeText($message);
		} else {
			return $this->handler('voice');
		}
	}

	
	private function handler($type) {
		if(empty($type)) {
			return array();
		}
		global $_W;
		$params = array();

		$setting = uni_setting($_W['uniacid'], array('default_message'));
						$df = $setting['default_message'];
		if(is_array($df) && isset($df[$type]) && in_array($df[$type], $this->modules)) {
			$params[] = array(
				'module' => $df[$type],
				'rule' => '-1',
			);
		}

		return $params;
	}

	
	private function process($param) {
		global $_W;
		if(empty($param['module']) || !in_array($param['module'], $this->modules)) {
			return false;
		}
		
		$processor = WeUtility::createModuleProcessor($param['module']);
		$processor->message = $param['message'];
		$processor->rule = $param['rule'];
		$processor->priority = intval($param['priority']);
		$processor->inContext = $param['context'] === true;
		$response = $processor->respond();
		if(empty($response)) {
			return false;
		}

		return $response;
	}
	
	
	public function died($content = '') {
		global $_W, $engine;
		if (empty($content)) {
			exit('');
		}
		$response['FromUserName'] = $engine->message['to'];
		$response['ToUserName'] = $engine->message['from'];
		$response['MsgType'] = 'text';
		$response['Content'] = htmlspecialchars_decode($content);
		$response['CreateTime'] = TIMESTAMP;
		$response['FuncFlag'] = 0;
		$xml = array2xml($response);
		if(!empty($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
			$resp = $engine->account->encryptMsg($xml);
			$resp = $engine->account->xmlDetract($resp);
		} else {
			$resp = $xml;
		}
		exit($resp);
	}
}