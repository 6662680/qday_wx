<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$_W['page']['title'] = '全局设置 - 附件设置 - 系统管理';
$dos = array('attachment');
$do = in_array($do, $dos) ? $do : 'attachment';

load()->model('setting');
load()->func('tpl');

if (checksubmit('submit')) {
	$harmtype = array('asp','php','jsp','js','css','php3','php4','php5','ashx','aspx','exe','cgi');
	$upload = $_GPC['upload'];
	
	$upload['image']['thumb'] = !empty($upload['image']['thumb']) ? 1 : 0;
	$upload['image']['width'] = intval(trim($upload['image']['width']));
	if(!empty($upload['image']['thumb']) && empty($upload['image']['width'])){
		message('请设置图片缩略宽度.');
	}
	$upload['image']['limit'] = max(0, intval(trim($upload['image']['limit'])));
	if(empty($upload['image']['limit'])){
		message('请设置图片上传支持的文件大小, 单位 KB.');
	}
	if(empty($upload['image']['extentions'])){
		message('请添加支持的图片附件后缀类型');
	}
	if(!empty($upload['image']['extentions'])){
		$upload['image']['extentions'] = explode("\n", $upload['image']['extentions']);
		foreach ($upload['image']['extentions'] as $key => &$row) {
			$row = trim($row);
			if(in_array($row, $harmtype)) {
				unset($upload['image']['extentions'][$key]);
				continue;
			}
		}
	}
	if(!is_array($upload['image']['extentions']) || count($upload['image']['extentions']) < 1){
		message('请添加支持的图片附件后缀类型');
	}
	$upload['audio']['limit'] = max(0, intval(trim($upload['audio']['limit'])));
	if(empty($upload['image']['limit'])){
		message('请设置音频视频上传支持的文件大小, 单位 KB.');
	}
	if(!empty($upload['audio']['extentions'])){
		$upload['audio']['extentions'] = explode("\n", $upload['audio']['extentions']);
		foreach ($upload['audio']['extentions'] as $key => &$row) {
			$row = trim($row);
			if(in_array($row, $harmtype)) {
				unset($upload['audio']['extentions'][$key]);
				continue;
			}
		}
	}
	if(!is_array($upload['audio']['extentions']) || count($upload['audio']['extentions']) < 1){
		message('请添加支持的音频视频附件后缀类型');
	}
	setting_save($upload, 'upload');
	
	message('更新设置成功！', url('system/attachment'));
}


$post_max_size = ini_get('post_max_size');
$upload_max_filesize = ini_get('upload_max_filesize');

$upload = setting_load('upload');
$upload = empty($upload['upload']) ? $_W['config']['upload'] : $upload['upload'];

$upload['image']['thumb'] = empty($upload['image']['thumb']) ? 0 : 1;
$upload['image']['width'] = intval($upload['image']['width']);
if(empty($upload['image']['width'])){
	$upload['image']['width'] = 800;
}
if(!empty($upload['image']['extentions']) && is_array($upload['image']['extentions'])){
	$upload['image']['extentions'] = implode("\n", $upload['image']['extentions']);
}
if(!empty($upload['audio']['extentions']) && is_array($upload['audio']['extentions'])){
	$upload['audio']['extentions'] = implode("\n", $upload['audio']['extentions']);
}

template('system/attachment');