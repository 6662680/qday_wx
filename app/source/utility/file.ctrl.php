<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$do   = in_array($_GPC['do'], array('upload')) ? $_GPC['do'] : 'upload';
$type = in_array($_GPC['type'], array('image','audio')) ? $_GPC['type'] : 'image';

$result = array('error' => 1, 'message' => '');

if ($do == 'upload') {
	if($type == 'image'){
		$result = array(
			'jsonrpc' => '2.0',
			'id' => 'id',
			'error' => array('code' => 1, 'message'=>''),
		);
		
		load()->model('setting');
		$uploadsetting = setting_load('upload');
		$uploadsetting = $uploadsetting['upload'];
		
		$thumb = empty($uploadsetting['image']['thumb']) ? 0 : 1;
		$width = intval($uploadsetting['image']['width']);
		
		load()->func('file');
		
		if (!empty($_FILES['file']['name'])) {
			if ($_FILES['file']['error'] != 0) {
				$result['error']['message'] = '上传失败，请重试！';
				die(json_encode($result));
			}
			
			$_W['uploadsetting'] = array();
			$_W['uploadsetting']['image']['folder'] = 'images/' . $_W['uniacid'];
			$_W['uploadsetting']['image']['extentions'] = $_W['config']['upload']['image']['extentions'];
			$_W['uploadsetting']['image']['limit'] = $_W['config']['upload']['image']['limit'];
			
			$file = file_upload($_FILES['file']);
			if (is_error($file)) {
				$result['error']['message'] = $file['message'];
				die(json_encode($result));
			}
			
			$result['result'] = $file['path'];
			$result['id'] = $file['path'];
			
			pdo_insert('core_attachment', array(
				'uniacid' => $_W['uniacid'],
				'uid' => $_W['uid'],
				'filename' => $_FILES['file']['name'],
				'attachment' => $file['path'],
				'type' => 1,
				'createtime' => TIMESTAMP,
			));
			unset($result['error']);
			die(json_encode($result));
		} else {
			$result['error']['message'] = '请选择要上传的图片！';
			die(json_encode($result));
		}
	}
}