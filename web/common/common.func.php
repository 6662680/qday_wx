<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function url($segment, $params = array()) {
	return wurl($segment, $params);
}


function message($msg, $redirect = '', $type = '') {
	global $_W, $_GPC;
	if($redirect == 'refresh') {
		$redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
	}
	if($redirect == 'referer') {
		$redirect = referer();
	}
	if($redirect == '') {
		$type = in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'info';
	} else {
		$type = in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'success';
	}
	if ($_W['isajax'] || !empty($_GET['isajax']) || $type == 'ajax') {
		if($type != 'ajax' && !empty($_GPC['target'])) {
			exit("
<script type=\"text/javascript\">
parent.require(['jquery', 'util'], function($, util){
	var url = ".(!empty($redirect) ? 'parent.location.href' : "''").";
	var modalobj = util.message('".$msg."', '', '".$type."');
	if (url) {
		modalobj.on('hide.bs.modal', function(){\$('.modal').each(function(){if(\$(this).attr('id') != 'modal-message') {\$(this).modal('hide');}});top.location.reload()});
	}
});
</script>");
		} else {
			$vars = array();
			$vars['message'] = $msg;
			$vars['redirect'] = $redirect;
			$vars['type'] = $type;
			exit(json_encode($vars));
		}
	}
	if (empty($msg) && !empty($redirect)) {
		header('location: '.$redirect);
	}
	$label = $type;
	if($type == 'error') {
		$label = 'danger';
	}
	if($type == 'ajax' || $type == 'sql') {
		$label = 'warning';
	}
	include template('common/message', TEMPLATE_INCLUDEPATH);
	exit();
}


function checklogin() {
	global $_W;
	if (empty($_W['uid'])) {
		message('抱歉，您无权进行该操作，请先登录！', url('user/login'), 'warning');
	}
	return true;
}


function checkaccount() {
	global $_W;
	if (empty($_W['uniacid'])) {
		message('这项功能需要你选择特定公众号才能使用！', url('account/display'), 'info');
	}
}

function buildframes($types = array('platform'), $modulename = '') {
	global $_W;
	$ms = include IA_ROOT . '/web/common/frames.inc.php';

	load()->model('module');
	$frames = array();
	$modules = uni_modules();
    //print_r($module);die();
	if(!empty($modules)) {
		$sysmods = system_modules();
		foreach($modules as $m) {
			if(in_array($m['name'], $sysmods)) {
				continue;
			}
			$frames[$m['type']][] = $m;
		}
	}
	$types = module_types();
	if(!empty($frames)) {
		foreach($frames as $type => $fs) {
			$items = array();
			if(!empty($fs)) {
				foreach($fs as $m) {
					$items[] = array(
						'title' => $m['title'],
						'url' => url('home/welcome/ext', array('m' => $m['name']))
					);
				}
			}
			$ms['ext'][] = array(
				'title' => $types[$type]['title'],
				'items' => $items
			);
		}
	}
	if(in_array('solution', $types)) {
		load()->model('module');
		$error = module_solution_check($modulename);
		if(is_error($error)) {
		} else {
			$module = module_fetch($modulename);
			$entries = module_entries($modulename, array('menu'));
						if($_W['role'] == 'operator') {
				foreach($entries as &$entry1) {
					foreach($entry1 as $index2 => &$entry2) {
						$url_arr = parse_url($entry2['url']);
						$url_query = $url_arr['query'];
						parse_str($url_query, $query_arr);
						$eid = intval($query_arr['eid']);
						$data = pdo_fetch('SELECT * FROM ' . tablename('modules_bindings') . ' WHERE eid = :eid', array(':eid' => $eid));
						$ixes = pdo_fetchcolumn('SELECT id FROM ' . tablename('solution_acl') . ' WHERE uid = :uid AND module = :module AND do = :do AND state = :state', array('uid' => $_W['uid'], ':module' => $modulename, ':do' => $data['do'], 'state' => $data['state']));
						if(empty($ixes)) {
							unset($entry1[$index2]);
						}
					}
				}
			}
			if($entries['menu']) {
				$menus = array('title' => '业务功能菜单');
				foreach($entries['menu'] as $menu) {
					$menus['items'][] =  array('title' => $menu['title'], 'url' => $menu['url']);
				}
				$ms['solution'][] = $menus;
			}
		}
	}
	if (empty($_W['isfounder'])) {
		$urls = array();
		$permurls = pdo_fetchall("SELECT url FROM ".tablename('users_permission')." WHERE uid = :uid AND uniacid = :uniacid", array(':uid' => $_W['uid'], ':uniacid' => $_W['uniacid']));
		if (!empty($permurls)) {
			foreach ($permurls as $row) {
				$urls[] = $row['url'];
			}
		}
		if (!empty($urls)) {
			foreach ($ms as $name => $section) {
				$hassection = false;
				foreach ($section as $i => $menus) {
					$hasitems = false;
					foreach ($menus['items'] as $j => $menu) {
						$_W['setting']['permurls']['menus'][] = ltrim($menu['url'], './index.php?');
						if (!in_array(rtrim(ltrim($menu['url'], './index.php?'), '&'), $urls)) {
							unset($ms[$name][$i]['items'][$j]);
						} else {
							$hasitems = true;
							$hassection = true;
						}
					}
					if (!$hasitems) {
						unset($ms[$name][$i]);
					}
				}
				if (!$hassection) {
					unset($ms[$name]);
				} else {
					$_W['setting']['permurls']['sections'][] = $name;
				}
			}
		}
	}
	$_W['setting']['permurls']['urls'] = $urls;
	return $ms;
}

function system_modules() {
	return array(
		'basic', 'news', 'music', 'userapi', 'recharge', 
		'custom', 'images', 'video', 'voice', 'chats'
	);
}
