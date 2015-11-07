<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan isNOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */

defined('IN_IA') or exit('Access Denied');
load()->func('tpl');

$id = $uniacid = intval($_GPC['uniacid']);
if(!empty($id)) {
	$state = uni_permission($uid, $id);
	if($state != 'founder' && $state != 'manager') {
		message('没有该公众号操作权限！');
	}
} else {
	if(empty($_W['isfounder']) && is_error($permission = uni_create_permission($_W['uid'], 1))) {
		message($permission['message'], '' , 'error');
		if(is_error($permission = uni_create_permission($_W['uid'], 2))) {
			message($permission['message'], '' , 'error');
		}
	}
}


$step = intval($_GPC['step']) ? intval($_GPC['step']) : 1;
if($step == 1) {

} elseif($step == 2) {
	if(!empty($uniacid)) {
		$unidata = pdo_fetch('SELECT * FROM ' . tablename('uni_account') . ' WHERE uniacid = :uniacid', array(':uniacid' => $uniacid));
		$name = $unidata['name'];
		$description = $unidata['description'];
	} else {
		$name = trim($_GPC['uni_name']);
		$description = trim($_GPC['uni_description']);
		isetcookie('uni_name', '', -10000);
		isetcookie('uni_description', '', -10000);
	}

	if(checksubmit('submit') || checksubmit('back', 1)) {
		$name = trim($_GPC['name']) ? trim($_GPC['name']) : message('抱歉，名称为必填项请返回填写！');
		$description = trim($_GPC['description']);
	}
} elseif($step == 3) {
	$uniacid = intval($_GPC['uniacid']);
	if(empty($uniacid)) {
		if(!empty($_GPC['tag'])) {
			$name = trim($_GPC['name']);
			$description = trim($_GPC['description']);
			isetcookie('uni_name', '', -1000);
			isetcookie('uni_description', '', -1000);
		} else {
			$name = trim($_GPC['name']);
			$description = trim($_GPC['description']);
			if(empty($name)) {
				$name = trim($_GPC['uni_name']) ? trim($_GPC['uni_name']) : message('抱歉，名称为必填项请返回填写！');
				$description = trim($_GPC['uni_description']);
			}
			isetcookie('uni_name', $name);
			isetcookie('uni_description', $description);
		}
	}
	if(checksubmit('submit')) {
		load()->func('file');
		if (intval($_GPC['type']) == '2') {
			$type = 'yixin';
		} elseif (intval($_GPC['type']) == '3') {
			$type = 'alipay';
		} else {
			$type = 'wechat';
		}
		$username = trim($_GPC['wxusername']);
		$password = md5(trim($_GPC['wxpassword']));
		if(!empty($username) && !empty($password)) {
			if ($type == 'wechat') {
				$loginstatus = account_weixin_login($username, $password, trim($_GPC['verify']));
				if(is_error($loginstatus)) {
					message($loginstatus['message'], url('account/post-step', array('uniacid' => $uniacid, 'step' => 2)), 'error');
				}
				$basicinfo = account_weixin_basic($username);
			} elseif ($_GPC['type'] == 'yixin') {
				$loginstatus = account_yixin_login($username, $password, trim($_GPC['verify']));
				if(is_error($loginstatus)) {
					message($loginstatus['message'], url('account/post-step', array('uniacid' => $uniacid, 'step' => 2)), 'error');
				}
				$basicinfo = account_yixin_basic($username);
			}
			if (empty($basicinfo['name'])) {
				message('一键获取信息失败,请手动设置公众号信息！', url('account/post-step/', array('uniacid' => $uniacid, 'step' => 3)), 'error');
			}

			if(empty($uniacid)) {
				$data = array(
					'name' => $name,
					'description' => $description,
					'groupid' => 0
				);
				$state = pdo_insert('uni_account', $data);
				if(!$state) message('添加公众号失败');
				$uniacid = pdo_insertid();
								$template = pdo_fetch('SELECT id,title FROM ' . tablename('site_templates') . " WHERE name = 'default'");
				$styles['uniacid'] = $uniacid;
				$styles['templateid'] = $template['id'];
				$styles['name'] = $template['title'] . '_' . random(4);
				pdo_insert('site_styles', $styles);
				$styleid = pdo_insertid();
								$multi['uniacid'] = $uniacid;
				$multi['title'] = $data['name'];
				$multi['quickmenu'] = iserializer(array('template' => 'default', 'enablemodule' => array()));
				$multi['styleid'] = $styleid;
				pdo_insert('site_multi', $multi);
				$multi_id = pdo_insertid();

				$unisettings['creditnames'] = array('credit1' => array('title' => '积分', 'enabled' => 1), 'credit2' => array('title' => '余额', 'enabled' => 1));
				$unisettings['creditnames'] = iserializer($unisettings['creditnames']);
				$unisettings['creditbehaviors'] = array('activity' => 'credit1', 'currency' => 'credit2');
				$unisettings['creditbehaviors'] = iserializer($unisettings['creditbehaviors']);
				$unisettings['uniacid'] = $uniacid;
				$unisettings['default_site'] = $multi_id; 				$unisettings['sync'] = iserializer(array('switch' => 0, 'acid' => ''));
				pdo_insert('uni_settings', $unisettings);

				pdo_insert('mc_groups', array('uniacid' => $uniacid, 'title' => '默认会员组', 'isdefault' => 1));
				$account_users = array('uniacid' => $uniacid, 'uid' => $_W['uid'], 'role' => 'manager');
				pdo_insert('uni_account_users', $account_users);
				load()->model('module');
				module_build_privileges();
			}
						$account['username'] = trim($_GPC['wxusername']);
			$account['password'] = md5($_GPC['wxpassword']);
			$account['lastupdate'] = TIMESTAMP;
			$account['name'] = trim($basicinfo['name']);
			$account['account'] = trim($basicinfo['account']);
			$account['original'] = trim($basicinfo['original']);
			$account['signature'] = trim($basicinfo['signature']);
			$account['key'] = trim($basicinfo['key']);
			$account['secret'] = trim($basicinfo['secret']);
			$account['type'] = intval($_GPC['type']);
			$account['level'] = $basicinfo['level'];
		} else {
			message('请填写公众平台用户名和密码', url('account/post-step', array('uniacid' => $uniacid, 'step' => 2)), 'error');
		}
		$acid = account_create($uniacid, $account);
		if(is_error($acid)) {
			message('添加公众号信息失败', '', url('account/post-step/', array('uniacid' => $uniacid, 'step' => 2), 'error'));
		}
		isetcookie('uni_name', '', -10000);
		isetcookie('uni_description', '', -10000);

				if (!empty($basicinfo['headimg'])) {
			file_write('headimg_'.$acid.'.jpg', $basicinfo['headimg']);
		}
		if (!empty($basicinfo['qrcode'])) {
			file_write('qrcode_'.$acid.'.jpg', $basicinfo['qrcode']);
		}
	}
	
	if(!empty($acid)) {
		$account = account_fetch($acid);
	}
	
	if (!empty($loginstatus)) {
				if ($type == 'wechat') {
			$account['id'] = $acid;
			$result = account_weixin_interface($account['username'], $account);
			if (is_error($result)) {
				$error = $result['message'];
			}
			if (!empty($result)) {
				pdo_update('account', array('isconnect' => 1), array('acid' => $acid));
			}
		}
	}
} elseif($step == 4) {
	$uniacid = intval($_GPC['uniacid']);
	$acid = intval($_GPC['acid']);
	$account = account_fetch($acid);
	$flag = intval($_GPC['flag']);
	
	if(checksubmit('submit') && $flag == 1) {
		$update['name'] = trim($_GPC['cname']);
		if(empty($update['name'])) {
			message('公众号名称必须填写');
		}

				if(empty($uniacid)) {
			$name = trim($_GPC['name']) ? trim($_GPC['name']) : message('抱歉，名称为必填项请返回填写！');
			$description = trim($_GPC['description']);
			$data = array(
				'name' => $name,
				'description' => $description,
				'groupid' => 0
			);
			$state = pdo_insert('uni_account', $data);
			if(!$state) message('添加公众号失败');
			$uniacid = pdo_insertid();
						$template = pdo_fetch('SELECT id,title FROM ' . tablename('site_templates') . " WHERE name = 'default'");
			$styles['uniacid'] = $uniacid;
			$styles['templateid'] = $template['id'];
			$styles['name'] = $template['title'] . '_' . random(4);
			pdo_insert('site_styles', $styles);
			$styleid = pdo_insertid();
						$multi['uniacid'] = $uniacid;
			$multi['title'] = $data['name'];
			$multi['quickmenu'] = iserializer(array('template' => 'default', 'enablemodule' => array()));
			$multi['styleid'] = $styleid;
			pdo_insert('site_multi', $multi);
			$multi_id = pdo_insertid();

			$unisettings['creditnames'] = array('credit1' => array('title' => '积分', 'enabled' => 1), 'credit2' => array('title' => '余额', 'enabled' => 1));
			$unisettings['creditnames'] = iserializer($unisettings['creditnames']);
			$unisettings['creditbehaviors'] = array('activity' => 'credit1', 'currency' => 'credit2');
			$unisettings['creditbehaviors'] = iserializer($unisettings['creditbehaviors']);
			$unisettings['uniacid'] = $uniacid;
			$unisettings['default_site'] = $multi_id; 			$unisettings['sync'] = iserializer(array('switch' => 0, 'acid' => ''));
			pdo_insert('uni_settings', $unisettings);

			pdo_insert('mc_groups', array('uniacid' => $uniacid, 'title' => '默认会员组', 'isdefault' => 1));
			$account_users = array('uniacid' => $uniacid, 'uid' => $_W['uid'], 'role' => 'manager');
			pdo_insert('uni_account_users', $account_users);
			load()->model('module');
			module_build_privileges();
		}
		load()->func('file');
		$update['account'] = trim($_GPC['account']);
		$update['original'] = trim($_GPC['original']);
		$update['level'] = intval($_GPC['level']);
		$update['key'] = trim($_GPC['key']);
		$update['secret'] = trim($_GPC['secret']);
		$update['type'] = intval($_GPC['type']);
		$update['encodingaeskey'] = trim($_GPC['encodingaeskey']);
		if(empty($account)) {
			$acid = account_create($uniacid, $update);
			if(is_error($acid)) {
				message('添加公众号信息失败', '', url('account/post-step/', array('uniacid' => intval($_GPC['uniacid']), 'step' => 3), 'error'));
			}
			$oauth = uni_setting($uniacid, array('oauth'));
			if($acid && !empty($update['key']) && !empty($update['secret']) && empty($oauth['oauth']['account']) && $update['level'] == 4) {
				pdo_update('uni_settings', array('oauth' => iserializer(array('status' => 1, 'account' => $acid))), array('uniacid' => $uniacid));
			}

			if (!empty($_FILES['qrcode']['tmp_name'])) {
				$_W['uploadsetting'] = array();
				$_W['uploadsetting']['image']['folder'] = '';
				$_W['uploadsetting']['image']['extentions'] = array('jpg');
				$_W['uploadsetting']['image']['limit'] = $_W['config']['upload']['image']['limit'];
				$upload = file_upload($_FILES['qrcode'], 'image', "qrcode_{$acid}");
			}
			if (!empty($_FILES['headimg']['tmp_name'])) {
				$_W['uploadsetting'] = array();
				$_W['uploadsetting']['image']['folder'] = '';
				$_W['uploadsetting']['image']['extentions'] = array('jpg');
				$_W['uploadsetting']['image']['limit'] = $_W['config']['upload']['image']['limit'];
				$upload = file_upload($_FILES['headimg'], 'image', "headimg_{$acid}");
			}
		} else {
			pdo_update('account', array('type' => intval($_GPC['type']), 'hash' => ''), array('acid' => $acid, 'uniacid' => $uniacid));
	
			if($update['type'] == 1) {
				unset($update['type']);
				pdo_update('account_wechats', $update, array('acid' => $acid, 'uniacid' => $uniacid));
			} else if($update['type'] == 2) {
				unset($update['type']);
				unset($update['encodingaeskey']);
				pdo_update('account_yixin', $update, array('acid' => $acid, 'uniacid' => $uniacid));
	
			}else if($update['type'] == 3) {
				unset($update['type']);
				pdo_update('account_alipay', $update, array('acid' => $acid, 'uniacid' => $uniacid));
			}

			$oauth = uni_setting($uniacid, array('oauth'));
			if($acid && !empty($update['key']) && !empty($update['secret']) && empty($oauth['oauth']['account'])) {
				pdo_update('uni_settings', array('oauth' => iserializer(array('status' => 1, 'account' => $acid))), array('uniacid' => $uniacid));
			}
			if (!empty($_FILES['qrcode']['tmp_name'])) {
				$_W['uploadsetting'] = array();
				$_W['uploadsetting']['image']['folder'] = '';
				$_W['uploadsetting']['image']['extentions'] = array('jpg');
				$_W['uploadsetting']['image']['limit'] = $_W['config']['upload']['image']['limit'];
				$upload = file_upload($_FILES['qrcode'], 'image', "qrcode_{$acid}");
			}
			if (!empty($_FILES['headimg']['tmp_name'])) {
				$_W['uploadsetting'] = array();
				$_W['uploadsetting']['image']['folder'] = '';
				$_W['uploadsetting']['image']['extentions'] = array('jpg');
				$_W['uploadsetting']['image']['limit'] = $_W['config']['upload']['image']['limit'];
				$upload = file_upload($_FILES['headimg'], 'image', "headimg_{$acid}");
			}
		}
	}

		if (empty($_W['isfounder'])) {
		$group = pdo_fetch("SELECT * FROM ".tablename('users_group')." WHERE id = '{$_W['user']['groupid']}'");
		$group['package'] = uni_groups((array)iunserializer($group['package']));
	} else {
		$group['package'] = uni_groups();
		$group['package'][-1] = array('id' => -1, 'name' => '所有服务');
	}
	$allow_group = array_keys($group['package']);
	$allow_group[] = 0;

	if(!$acid) {
		message('未填写公众号信息', '', url('account/post-step/', array('uniacid' => intval($_GPC['uniacid']), 'step' => 3), 'error'));
	}
	
	if(checksubmit('submit') && $flag == 2) {
		$groupid = intval($_GPC['groupid']);
		
		if(!in_array($groupid, $allow_group)) {
			message('您所在的用户组没有使用该服务套餐的权限');
		}
		pdo_update('uni_account', array('groupid' => $groupid), array('uniacid' => $uniacid));
		
		if($_GPC['isexpire'] == '1') {
			strtotime($_GPC['endtime']) > TIMESTAMP ? '' : message('服务套餐过期时间必须大于当前时间', '', 'error');
			$uniaccount['groupdata'] = iserializer(array('isexpire' => 1, 'oldgroupid' => '', 'endtime' => strtotime(trim($_GPC['endtime']))));
		} else {
			$uniaccount['groupdata'] = iserializer(array('isexpire' => 0, 'oldgroupid' => '', 'endtime' => TIMESTAMP));
		}
				$notify['sms']['balance'] = intval($_GPC['balance']);
		$notify['sms']['signature'] = trim($_GPC['signature']);
		$uniaccount['notify'] = iserializer($notify);
				
		pdo_update('uni_settings', $uniaccount, array('uniacid' => $uniacid));
		header('Location:' . url('account/post-step/', array('uniacid' => $uniacid, 'step' => 5, 'acid' => $acid)));
		exit;
	}	
	
} elseif($step == 5) {
	$uniacid = intval($_GPC['uniacid']);
	$acid = intval($_GPC['acid']);
	$isexist = pdo_fetch('SELECT uniacid FROM ' . tablename('uni_account') . ' WHERE uniacid = ' . $uniacid);
	if(empty($isexist)) {
		message('非法访问');
	}
	$account = account_fetch($acid);
}

template('account/post-step');
