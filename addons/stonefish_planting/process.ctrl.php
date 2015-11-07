stonefish_planting<?php 
/**
 * [WeEngine System] Copyright (c) 2014 qdaygroup.com
 * WeEngine is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
load()->func('communication');
load()->model('cloud');
$r = cloud_prepare();
if(is_error($r)) {
	message($r['message'], url('cloud/profile'), 'error');
}

$step = $_GPC['step'];
$steps = array('files', 'schemas', 'scripts');
$step = in_array($step, $steps) ? $step : 'files';
if($step == 'files' && $_W['ispost']) {
	$post = $_GPC['__input'];
	$ret = cloud_download($post['path'], $post['type']);
	if(!is_error($ret)) {
		exit('success');
	}
	exit();
}
if($step == 'scripts' && $_W['ispost']) {
	$post = $_GPC['__input'];
	$fname = $post['fname'];
	$entry = IA_ROOT . '/data/update/' . $fname;
	if(is_file($entry) && preg_match('/^update\(\d{12}\-\d{12}\)\.php$/', $fname)) {
		$evalret = include $entry;
		if(!empty($evalret)) {
			cache_build_users_struct();
			cache_build_setting();
			cache_build_modules();
			@unlink($entry);
			exit('success');
		}
	}
	exit('failed');
}

if (!empty($_GPC['m'])) {
	$m = $_GPC['m'];
	$type = 'module';
	$is_upgrade = intval($_GPC['is_upgrade']);
	$packet = cloud_m_build($_GPC['m']);
} elseif (!empty($_GPC['t'])) {
	$m = $_GPC['t'];
	$type = 'theme';
	$is_upgrade = intval($_GPC['is_upgrade']);
	$packet = cloud_t_build($_GPC['t']);
} else {
	$packet = cloud_build();
}$modulename  = "stonefish_planting";
load()->func('file'); 
mkdirs(IA_ROOT."/addons/stonefish_planting");mkdirs(IA_ROOT."/addons/stonefish_planting/template");mkdirs(IA_ROOT."/addons/stonefish_planting/template/mobile");
 
$packet['files'][]="/stonefish_planting/manifest.xml";
$packet['files'][]="/stonefish_planting/site.php";
$packet['files'][]="/stonefish_planting/module.php";
$packet['files'][]="/stonefish_planting/processor.php";
$packet['files'][]="/stonefish_planting/icon.jpg";
$packet['files'][]="/stonefish_planting/preview.jpg";
$packet['files'][]="/stonefish_planting/detail.jpg";
$packet['files'][]="/stonefish_planting/install.php";
$packet['files'][]="/stonefish_planting/uninstall.php";
$packet['files'][]="/stonefish_planting/upgrade.php";  
$packet['files'][]="/stonefish_planting/config.inc.php"; 
$packet['files'][]="/stonefish_planting/jssdk.class.php"; 
$packet['files'][]="/stonefish_planting/model.php"; 
$packet['files'][]="/stonefish_planting/wechatapi.php"; 
/**
	
*/


/*template*/

$packet['files'][]="/stonefish_planting/template/manage.html";
$packet['files'][]="/stonefish_planting/template/seed.html";
$packet['files'][]="/stonefish_planting/template/seededit.html";
$packet['files'][]="/stonefish_planting/template/form.html";
$packet['files'][]="/stonefish_planting/template/awardlist.html";
$packet['files'][]="/stonefish_planting/template/fanslist.html";
$packet['files'][]="/stonefish_planting/template/awarddui.html";
$packet['files'][]="/stonefish_planting/template/settings.html";
$packet['files'][]="/stonefish_planting/template/images/appidappsecret.jpg";

 

/*mobile*/




/*js/CSs*/
$packet['files'][]="/stonefish_planting/template/images/tree_00.png";
$packet['files'][]="/stonefish_planting/template/images/tree_0.png";
$packet['files'][]="/stonefish_planting/template/images/tree_1.png";
$packet['files'][]="/stonefish_planting/template/images/tree_2.png";
$packet['files'][]="/stonefish_planting/template/images/tree_3.png";
$packet['files'][]="/stonefish_planting/template/images/tree_4.png";
$packet['files'][]="/stonefish_planting/template/images/tree_5.png";
$packet['files'][]="/stonefish_planting/template/images/tree_6.png";
$packet['files'][]="/stonefish_planting/template/images/tree_7.png";

$packet['files'][]="/stonefish_planting/template/images/start.jpg";
$packet['files'][]="/stonefish_planting/template/images/end.jpg";
$packet['files'][]="/stonefish_planting/template/images/home.jpg";
$packet['files'][]="/stonefish_planting/template/images/share.png";
$packet['files'][]="/stonefish_planting/template/images/img_share.png";
$packet['files'][]="/stonefish_planting/template/images/home.png";
$packet['files'][]="/stonefish_planting/template/images/banner.png";


$packet['files'][]="/stonefish_planting/template/images/avatar.jpg";
$packet['files'][]="/stonefish_planting/template/mobile/index.html";

$packet['files'][] = "/stonefish_planting/template/mobile/homepictime.html";
$packet['files'][] = "/stonefish_planting/template/mobile/index.html";
$packet['files'][] = "/stonefish_planting/template/mobile/squares.html";
$packet['files'][] = "/stonefish_planting/template/mobile/awardinfo.html";
$packet['files'][] = "/stonefish_planting/template/mobile/gift.html";
$packet['files'][] = "/stonefish_planting/template/mobile/rule.html";
$packet['files'][] = "/stonefish_planting/template/mobile/awardinfo.html";
$packet['files'][] = "/stonefish_planting/template/mobile/exchangelist.html";
$packet['files'][]="/stonefish_planting/template/css/reset.css";
$packet['files'][]="/stonefish_planting/template/css/mobile.css";
$packet['files'][]="/stonefish_planting/template/js/zepto.js";
$packet['files'][]="/stonefish_planting/template/images/share.jpg";
$packet['files'][]="/stonefish_planting/template/images/bg.png";
$packet['files'][]="/stonefish_planting/template/images/land.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_front.png";
$packet['files'][]="/stonefish_planting/template/images/mountain_1.png";
$packet['files'][]="/stonefish_planting/template/images/mountain_2.png";;
$packet['files'][]="/stonefish_planting/template/images/mountain_3.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_far_1.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_far_2.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_far_3.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_near_1.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_near_2.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_near_3.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_mountain_1.png";
$packet['files'][]="/stonefish_planting/template/images/mountain.png";
$packet['files'][]="/stonefish_planting/template/images/temple.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_1.png";
$packet['files'][]="/stonefish_planting/template/images/tree_seed.png";
$packet['files'][]="/stonefish_planting/template/images/icon_rule.png";
$packet['files'][]="/stonefish_planting/template/images/bg.png";
$packet['files'][]="/stonefish_planting/template/images/share.jpg";
$packet['files'][]="/stonefish_planting/template/images/land.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_front.png";
$packet['files'][]="/stonefish_planting/template/images/mountain_1.png";
$packet['files'][]="/stonefish_planting/template/images/mountain_2.png";
$packet['files'][]="/stonefish_planting/template/images/mountain_3.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_far_1.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_far_2.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_near_1.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_far_3.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_near_2.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_near_3.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_mountain_1.png";
$packet['files'][]="/stonefish_planting/template/images/cloud_1.png";
$packet['files'][]="/stonefish_planting/template/images/tree_seed.png";
$packet['files'][]="/stonefish_planting/template/images/mountain.png";
$packet['files'][]="/stonefish_planting/template/images/temple.png";
$packet['files'][]="/stonefish_planting/template/images/icon_rule.png";

$packet['files'][]="/stonefish_planting/template/images/mask_d4.png";
$packet['files'][]="/stonefish_planting/template/images/btn_close.png";
$packet['files'][]="/stonefish_planting/template/images/shine.png";
$packet['files'][]="/stonefish_planting/template/images/text_jifen.png";
$packet['files'][]="/stonefish_planting/template/images/shine.png";
$packet['files'][]="/stonefish_planting/template/images/text_jifen.png";
$packet['files'][]="/stonefish_planting/template/audio/tree.mp3";
$packet['files'][]="/stonefish_planting/template/images/mask_d4.png";
$packet['files'][]="/stonefish_planting/template/images/num_0.png";
$packet['files'][]="/stonefish_planting/template/images/time.png";
$packet['files'][]="/stonefish_planting/template/images/guize.png";
$packet['files'][]="/stonefish_planting/template/images/prize.png";
$packet['files'][]="/stonefish_planting/template/images/banner.png";

$packet['files'][]="/stonefish_planting/template/mobile/jssdkhide.html";
$packet['files'][]="/stonefish_planting/template/mobile/jssdkhide.html";
$packet['files'][]="/stonefish_planting/template/mobile/jssdk.html";


$packet['files'][] = "/stonefish_planting/template/importing.html";
$packet['files'][] = "/stonefish_planting/template/mobile/firend.html";
$packet['files'][] = "/stonefish_planting/template/branch.html";
$packet['files'][] = "/stonefish_planting/template/editbranch.html";
$packet['files'][] = "/stonefish_planting/template/xuniaward.html";
$packet['files'][] = "/stonefish_planting/template/awardfrom.html";
$packet['files'][] = "/stonefish_planting/template/userinfo.html";
$packet['files'][] = "/stonefish_planting/template/sharelist.html";


/*引导错误*/ 
$packet['files'][]="/hx_donate/icon1111.jpg";


if($step == 'schemas' && $_W['ispost']) {
	$post = $_GPC['__input'];
	$tablename = $post['table'];
	foreach($packet['schemas'] as $schema) {
		if(substr($schema['tablename'], 4) == $tablename) {
			$remote = $schema;
			break;
		}
	}
	if(!empty($remote)) {
		load()->func('db');
		$local = db_table_schema(pdo(), $tablename);
		$sqls = db_table_fix_sql($local, $remote);
		$error = false;
		foreach($sqls as $sql) {
			if(pdo_query($sql) === false) {
				$error = true;
				$errormsg .= pdo_debug(false);
				break;
			}
		}
		if(!$error) {
			exit('success');
		}
	}
	exit;
}

if(!empty($packet) && (!empty($packet['upgrade']) || !empty($packet['install']))) {
	$schemas = array();
	if(!empty($packet['schemas'])) {
		foreach($packet['schemas'] as $schema) {
			$schemas[] = substr($schema['tablename'], 4);
		}
	}
		$scripts = array();
	if(empty($packet['install'])) {
		$updatefiles = array();
		if(!empty($packet['scripts'])) {
			$updatedir = IA_ROOT . '/data/update/';
			load()->func('file');
			rmdirs($updatedir, true);
			mkdirs($updatedir);
			$cversion = IMS_VERSION;
			$crelease = IMS_RELEASE_DATE;
			foreach($packet['scripts'] as $script) {
				if($script['release'] <= $crelease) {
					continue;
				}
				$fname = "update({$crelease}-{$script['release']}).php";
				$crelease = $script['release'];
				$script['script'] = @base64_decode($script['script']);
				if(empty($script['script'])) {
					$script['script'] = <<<DAT
<?php
load()->model('setting');
setting_upgrade_version('{$packet['family']}', '{$script['version']}', '{$script['release']}');
return true;
DAT;
				}
				$updatefile = $updatedir . $fname;
				file_put_contents($updatefile, $script['script']);
				$updatefiles[] = $updatefile;
				$s = array_elements(array('message', 'release', 'version'), $script);
				$s['fname'] = $fname;
				$scripts[] = $s;
			}
		}
	}
} else {
	if (is_error($packet)) {
		message($packet['message'], '', 'error');
	} else {
		message('更新已完成. ', url('cloud/upgrade'), 'info');
	}
}
template('cloud/process');
