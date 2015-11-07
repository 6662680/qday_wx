<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
error_reporting(0);

global $_W;

load()->func('file');

if (!in_array($do, array('upload', 'fetch', 'browser', 'delete'))) {
	exit('Access Denied');
}

$result = array(
	'error' => 1,
	'message' => '',
	'data' => ''
);

$type = $_COOKIE['__fileupload_type'];;
$type = in_array($type, array('image','audio')) ? $type : 'image';

$option = array();
$option = array_elements(array('uploadtype', 'global', 'dest_dir', 'width', 'thumb'), $_POST);
$option['width'] = intval($option['width']);
$option['global'] = !empty($_COOKIE['__fileupload_global']);
if (!empty($option['global']) && empty($_W['isfounder'])) {
	$result['message'] = '没有向 global 文件夹上传文件的权限.';
	die(json_encode($result));
}

$dest_dir = $_COOKIE['__fileupload_dest_dir'];
if (preg_match('/^[a-zA-Z0-9_\/]{0,50}$/', $dest_dir, $out)) {
	$dest_dir = trim($dest_dir, '/');
	$pieces = explode('/', $dest_dir);
	if(count($pieces) > 3){
		$dest_dir = '';
	}
} else {
	$dest_dir = '';
}

$setting = $_W['setting']['upload'][$type];

$uniacid = intval($_W['uniacid']);

if (!empty($option['global'])) {
	$setting['folder'] = "{$type}s/global";
	if (!empty($dest_dir)) {
		$setting['folder'] .= '/'.$dest_dir.'/';
	}
} else {
	$setting['folder'] = "{$type}s/{$uniacid}";
	if(empty($dest_dir)){
		$setting['folder'] .= '/'.date('Y/m/');
	} else {
		$setting['folder'] .= '/'.$dest_dir.'/';
	}
}


if ($do == 'fetch') {
	
	$url = trim($_GPC['url']);
	
	load()->func('communication');
	$resp = ihttp_get($url);
	if (is_error($resp)) {
		$result['message'] = '提取文件失败, 错误信息: '.$resp['message'];
		die(json_encode($result));
	}
	
	if (intval($resp['code']) != 200) {
		$result['message'] = '提取文件失败: 未找到该资源文件.';
		die(json_encode($result));
	}
	
	$ext = '';
	if ($type == 'image') {
		switch ($resp['headers']['Content-Type']){
			case 'application/x-jpg':
			case 'image/jpeg':
				$ext = 'jpg';
				break;
			case 'image/png':
				$ext = 'png';
				break;
			case 'image/gif':
				$ext = 'gif';
				break;
			default:
				$result['message'] = '提取资源失败, 资源文件类型错误.';
				die(json_encode($result));
				break;
		}
	} else {
		$result['message'] = '提取资源失败, 仅支持图片提取.';
		die(json_encode($result));
	}
	
	
	if (intval($resp['headers']['Content-Length']) > $setting['limit'] * 1024) {
		$result['message'] = '上传的媒体文件过大('.sizecount($size).' > '.sizecount($setting['limit'] * 1024);
		die(json_encode($result));
	}
	
	$originname = pathinfo($url, PATHINFO_BASENAME);
	
	$filename = file_random_name(ATTACHMENT_ROOT .'/'. $setting['folder'], $ext);
	$pathname = $setting['folder'] . $filename;
	$fullname = ATTACHMENT_ROOT . '/' . $pathname;
	
	if (file_put_contents($fullname, $resp['content']) == false) {
		$result['error']['message'] = '提取失败.';
		die(json_encode($result));
	}
}


if ($do == 'upload') {
	
	if (empty($_FILES['file']['name'])) {
		$result['message'] = '上传失败, 请选择要上传的文件！';
		die(json_encode($result));
	}
	if ($_FILES['file']['error'] != 0) {
		$result['message'] = '上传失败, 请重试.';
		die(json_encode($result));
	}
	
	$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
	$ext = strtolower($ext);
	$size = intval($_FILES['file']['size']);
	$originname = $_FILES['file']['name'];
	
	
	if (!empty($_GPC['mediatype'])) {

		if ($type == 'audio') {
			if(in_array($ext, array('mp3','amr'))){
				$_GPC['mediatype'] = 'voice';
			} elseif (in_array($ext, array('mp4'))){
				$_GPC['mediatype'] = 'video';
			}
		}
		
		switch ($_GPC['mediatype']) {
			case 'image':
				$setting['extentions'] = array('jpg');
				$setting['limit'] = 1 * 1024;
				break;
			case 'thumb':
				$setting['extentions'] = array('jpg');
				$setting['limit'] = 64;
				break;
			case 'voice':
				$setting['extentions'] = array('mp3','amr');
				$setting['limit'] = 2 * 1024;
				break;
			case 'video':
				$setting['extentions'] = array('mp4');
				$setting['limit'] = 10 * 1024;
				break;
			default:
				$result['message'] = '媒体类型不正确。';
				die(json_encode($result));
		}
		
		if ($size > $setting['limit'] * 1024) {
			$result['message'] = "上传媒体({$_GPC['mediatype']})到微信服务器失败: ".sizecount($size)." > ".sizecount($setting['limit'] * 1024);
			die(json_encode($result));
		}
	}
	
	
	if ($size > $setting['limit'] * 1024) {
		$result['message'] = '上传的媒体文件过大: '.sizecount($size).' > '.sizecount($setting['limit'] * 1024);
		die(json_encode($result));
	}
	
	
	$filename = file_random_name(ATTACHMENT_ROOT .'/'. $setting['folder'], $ext);
	
	$file = file_upload($_FILES['file'], $type, $setting['folder'] . $filename);
	if (is_error($file)) {
		$result['message'] = $file['message'];
		die(json_encode($result));
	}
	
	$pathname = $file['path'];
	
	$fullname = ATTACHMENT_ROOT . '/' . $pathname;
	
		if (!empty($_GPC['mediatype'])){
		
		load()->model('account');
		$token = WeAccount::token(WeAccount::TYPE_WEIXIN);
		if (is_error($token)) {
			$result['message'] = $token['message'];
			die(json_encode($result));
		}
		
		$sendapi = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$token}&type={$_GPC['mediatype']}";
		$data = array(
			'media' => '@'.$fullname
		);
		
		load()->func('communication');
		$resp = ihttp_request($sendapi, $data);
		$resp = json_decode($resp['content'], true);
		
		if(is_error($resp)){
			$result['message'] = '多媒体上传到微信服务器错误, 错误代码: '. $resp['errno'].', 错误信息: '.$resp['message'].'.';
			die(json_encode($result));
		}
		
		if (empty($resp['type'])) {
			$result['message'] = '多媒体上传到微信服务器, 获取到错误的响应结果.';
			die(json_encode($result));
		}
		
		unset($result['error']);
		
		$result['type'] = $_GPC['mediatype'];
		
		if(!empty($resp['media_id'])){
			$result['media_id'] = $resp['media_id'];
		}
		if(!empty($resp['thumb_media_id'])){
			$result['media_id'] = $resp['thumb_media_id'];
		}
		
		if ($type == 'image') {
						$file['path'] = file_image_thumb($fullname, '', 300);
		}
		pdo_insert('core_wechats_attachment', array(
			'uniacid' => $uniacid,
			'uid' => $_W['uid'],
			'filename' => $originname,
			'attachment' => $file['path'],
			'media_id' => $result['media_id'],
			'type' => $resp['type'],
			'createtime' => $resp['created_at']
		));
		
		@unlink($fullname);
		die(json_encode($result));
	}

} 
if ($do == 'fetch' || $do == 'upload') {
	
		if($type == 'image'){
		
		$thumb = empty($setting['thumb']) ? 0 : 1; 		$width = intval($setting['width']);
		if (isset($option['width']) && !empty($option['width'])) {
			$width = intval($option['width']);
		}
		if ($thumb == 1 && $width > 0) {
			$thumbnail = file_image_thumb($fullname, '', $width);
			@unlink($fullname);
			if (is_error($thumbnail)) {
				$result['message'] = $thumbnail['message'];
				die(json_encode($result));
			} else {
				$filename = pathinfo($thumbnail, PATHINFO_BASENAME);
				$pathname = $thumbnail;
				$fullname = ATTACHMENT_ROOT .'/'.$pathname;
			}
		}
	}
	
	pdo_insert('core_attachment', array(
		'uniacid' => $uniacid,
		'uid' => $_W['uid'],
		'filename' => $originname,
		'attachment' => $pathname,
		'type' => $type == 'image' ? 1 : 2,
		'createtime' => TIMESTAMP,
	));
	
	$info = array(
		'name' => $originname,
		'ext' => $ext,
		'filename' => $pathname,
		'attachment' => $pathname,
		'url' => $_W['attachurl'] . $pathname,
		'is_image' => $type == 'image' ? 1 : 0,
		'filesize' => filesize($fullname),
			);
	
	if ($type == 'image') {
		$size = getimagesize($fullname);
		$info['width'] = $size[0];
		$info['height'] = $size[1];
	} else {
		$size = filesize($fullname);
		$info['size'] = sizecount($size);
	}
	
	die(json_encode($info));
}

if ($do == 'delete') {
	
	$attachment = $_GPC['file'];
	
	if (preg_match('/[\.]{2}/', $attachment, $out)) {
		exit('failure');
	}
	
	if (empty($_W['role'])) {
		exit('failure');
	}
	if (empty($attachment) || !is_string($attachment)) {
		exit('请选择要删除的图片！');
	}
	if (preg_match('/[\.]{2}/', $attachment, $out)) {
		exit('非法的删除路径.！');
	}
	if (empty($_W['isfounder'])) {
		if(strexists($attachment, 'images/global')
		|| strexists($attachment, 'audios/global')){
			exit('没有删除 global 文件夹中图片的权限.');
		}
	}
	if (!file_exists(ATTACHMENT_ROOT . '/' . $attachment)) {
		exit('删除失败: 文件不存在.！');
	}
	if (empty($_W['isfounder'])) {
		$pieces = explode('/', $attachment);
		if (count($pieces) == 1) {
			if ($_W['role'] != 'manager') {
				exit('failure');
			}
		}
		if (count($pieces) < 4 || strval($pieces[1]) == 'global') {
			exit('failure');
		}
		if (is_numeric($pieces[1])) {
			if (intval($_W['uniacid']) != intval($pieces[1])) {
				exit('failure');
			}
		} else {
			exit('failure');
		}
	}
	
	load()->func('file');
	if (file_delete($attachment)) {
		if(empty($option['global'])){
			pdo_delete('core_attachment', array('uniacid'=>$uniacid, 'attachment'=>$attachment));
		}else{
			pdo_delete('core_attachment', array('attachment'=>$attachment));
		}
		exit('success');
	} else {
		exit('failure');
	}
}

if ($do == 'browser') { 	
	$path = strval($_GPC['path']);
	
	if (preg_match('/[\.]{2}/', $path, $out)) {
		$path = '';
	}
	
	if (!file_exists(ATTACHMENT_ROOT . '/' . $path)) {
		$path = '';
	}
	
	if (in_array($path, array('images', 'audios'))){
		if (empty($_W['isfounder'])) {
			$path = '';
		}
	}
	
	function file_compare($a, $b) {
		if ($a['level'] < $b['level']) {
			return -1;
		} elseif ($a['is_dir'] && !$b['is_dir']) {
			return -1;
		} elseif(!$a['is_dir'] && $b['is_dir']) {
			return 1;
		} elseif($a['is_dir'] && $b['is_dir']) {
			return strcmp($a['filename'], $b['filename']);
		} else {
			return $a['datetime'] < $b['datetime'] ? -1 : 1;
		}
	}
	
	if (empty($path)) {
		$path = $type.'s';
		if (empty($_W['isfounder'])) {
			$path .= '/'.$uniacid;
			if (!file_exists(ATTACHMENT_ROOT.'/'.$path)) {
				@mkdirs(ATTACHMENT_ROOT.'/'.$path);
			}
		}
	} else {
		if (empty($_W['isfounder'])) {
			$ps = explode('/', $path);
			if (count($ps) < 2) {
				$path = $type.'s/'.$uniacid;
			}
			if (count($ps) >= 2) {
				$_uniacid = intval($ps[1]);
				if ($_uniacid != $uniacid) {
					$path = $type.'s/'.$uniacid;;
				}
			}
		}
	}
	
	$file = '';
	$dir = '';
	
	$isdir = is_dir(ATTACHMENT_ROOT . '/' . $path);
	if ($isdir) {
		$dir = $path;
	} else {
		$dir = pathinfo(ATTACHMENT_ROOT.'/'.$path, PATHINFO_DIRNAME);
		$dir = str_replace(ATTACHMENT_ROOT, '', $dir);
		$dir = trim($dir, '/');
	}
	
	$pieces = explode('/', $dir);
	$level = count($pieces);
	
	$crumbs = array();
	
	for ($i = 0; $i < $level; $i++ ){
		$crumbs[] = array(
			'level' => $level - $i,
			'dir' => implode('/', $pieces),
			'attachment' => implode('/', $pieces),
			'filename' => $pieces[count($pieces) - 1],
			'is_dir' => true,
			'datetime' => date('Y-m-d H:i:s', filemtime($file))
		);
		unset($pieces[count($pieces) - 1]);
	}
	
	usort($crumbs, 'file_compare');
	if (empty($_W['isfounder'])) {
		unset($crumbs[0]);
	}
	
	$copy = array();
	foreach ($crumbs as $key => $value) {
		$copy[] = $value;
	}
	$self = null;
	if ($copy[count($copy) - 1]) {
		$self = $copy[count($copy) - 1];
	}
	$parent = null;
	if ($copy[count($copy) - 2]) {
		$parent = $copy[count($copy) - 2];
	}
	
	$files = array();
	if ($handle = opendir(ATTACHMENT_ROOT.'/'.$dir)) {
		while (false !== ($filename = readdir($handle))) {
				
			if($filename == '.') continue;
			if($filename == '..') continue;
				
			$file = ATTACHMENT_ROOT.'/'.$dir .'/'. $filename;
			$file = str_replace('//', '/', $file);
			if (is_dir($file)) {
				$files[] = array(
						'level' => $level,
						'dir' => $dir,
						'filename' => $filename,
						'attachment' => $dir.'/'.$filename,
						'is_dir' => true,
						'datetime' => date('Y-m-d H:i:s', filemtime($file)),
				);
			} else {
				$fileext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
				if (!in_array($fileext, $setting['extentions'])) {
					continue;
				}
				$entry = array();
				$entry['url'] = $_W['attachurl'].$dir.'/'.$filename;
				$entry['url'] = str_replace('//', '/', $entry['url']);
				$entry['url'] = str_replace(':/', '://', $entry['url']);
				$entry['filename'] = $dir . '/'. $filename;
				$entry['filename'] = str_replace('//', '/', $entry['filename']);
				$info = array(
						'level' => $level,
						'dir' => $dir,
												'filename' => $entry['filename'],
						'is_dir' => 0,
						'is_image' => in_array($fileext, array('jpg','gif','png','jpeg')) ? true : false,
						'filesize' => filesize($file),
												'url' => $entry['url'],
						'attachment' => $entry['filename'],
						'selected' => $entry['filename'] == $path ? 1 : 0,
												'datetime' => date('Y-m-d H:i:s', filemtime($file))
				);
				if ($info['is_image']) {
					$size = getimagesize(ATTACHMENT_ROOT.'/'.$dir.'/'.$filename);
					$info['width'] = $size[0];
					$info['height'] = $size[1];
				} else {
					$size = filesize(ATTACHMENT_ROOT.'/'.$dir.'/'.$filename);
					$info['size'] = sizecount($size);
				}
				$files[] = $info;
			}
		}
	}
	
	usort($files, 'file_compare');
	
	$result = array(
		'candelete' => !empty($_W['isfounder']) || $_W['role'] == 'manager',
		'parent' => $parent,
		'self' => $self,
		'crumbs' => $crumbs,
		'files' => $files
	);
	
	die(json_encode($result));
}
