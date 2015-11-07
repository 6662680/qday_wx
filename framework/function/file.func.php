<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function file_write($filename, $data) {
	global $_W;
	$filename = ATTACHMENT_ROOT . '/' . $filename;
	mkdirs(dirname($filename));
	file_put_contents($filename, $data);
	@chmod($filename, $_W['config']['setting']['filemode']);
	return is_file($filename);
}


function file_move($filename, $dest) {
	global $_W;
	mkdirs(dirname($dest));
	if (is_uploaded_file($filename)) {
		move_uploaded_file($filename, $dest);
	} else {
		rename($filename, $dest);
	}
	@chmod($filename, $_W['config']['setting']['filemode']);
	return is_file($dest);
}


function file_tree($path) {
	$files = array();
	$ds = glob($path . '/*');
	if (is_array($ds)) {
		foreach ($ds as $entry) {
			if (is_file($entry)) {
				$files[] = $entry;
			}
			if (is_dir($entry)) {
				$rs = file_tree($entry);
				foreach ($rs as $f) {
					$files[] = $f;
				}
			}
		}
	}
	return $files;
}


function mkdirs($path) {
	if (!is_dir($path)) {
		mkdirs(dirname($path));
		mkdir($path);
	}
	return is_dir($path);
}


function file_copy($src, $des, $filter) {
	$dir = opendir($src);
	@mkdir($des);
	while (false !== ($file = readdir($dir))) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir($src . '/' . $file)) {
				file_copy($src . '/' . $file, $des . '/' . $file, $filter);
			} elseif (!in_array(substr($file, strrpos($file, '.') + 1), $filter)) {
				copy($src . '/' . $file, $des . '/' . $file);
			}
		}
	}
	closedir($dir);
}


function rmdirs($path, $clean = false) {
	if (!is_dir($path)) {
		return false;
	}
	$files = glob($path . '/*');
	if ($files) {
		foreach ($files as $file) {
			is_dir($file) ? rmdirs($file) : @unlink($file);
		}
	}
	return $clean ? true : @rmdir($path);
}


function file_upload($file, $type = 'image', $name = '', $is_wechat = false) {
	$harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
	if (empty($file)) {
		return error(-1, '没有上传内容');
	}
	if (!in_array($type, array('image', 'thumb', 'voice', 'video', 'audio'))) {
		return error(-2, '未知的上传类型');
	}

	global $_W;
	$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
	$ext = strtolower($ext);
		if (!$is_wechat) {
		$setting = $_W['setting']['upload'][$type];
		if (!in_array(strtolower($ext), $setting['extentions']) || in_array(strtolower($ext), $harmtype)) {
			return error(-3, '不允许上传此类文件');
		}
		if (!empty($setting['limit']) && $setting['limit'] * 1024 < filesize($file['tmp_name'])) {
			return error(-4, "上传的文件超过大小限制，请上传小于 {$setting['limit']}k 的文件");
		}
	}
	$result = array();
	if (empty($name) || $name == 'auto') {
		$uniacid = intval($_W['uniacid']);
		$path = "{$type}s/{$uniacid}/" . date('Y/m/');
		mkdirs(ATTACHMENT_ROOT . '/' . $path);
		$filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $ext);

		$result['path'] = $path . $filename;
	} else {
		mkdirs(dirname(ATTACHMENT_ROOT . '/' . $name));
		if (!strexists($name, $ext)) {
			$name .= '.' . $ext;
		}
		$result['path'] = $name;
	}

	if (!file_move($file['tmp_name'], ATTACHMENT_ROOT . '/' . $result['path'])) {
		return error(-1, '保存上传文件失败');
	}

	$result['success'] = true;
	
	return $result; 
}


function file_random_name($dir, $ext){
	do {
		$filename = random(30) . '.' . $ext;
	} while (file_exists($dir . $filename));

	return $filename;
}


function file_delete($file) {
	global $_W;
	if (empty($file)) {
		return FALSE;
	}
	if (file_exists(ATTACHMENT_ROOT . '/' . $file)) {
		@unlink(ATTACHMENT_ROOT . '/' . $file);
	}
	return TRUE;
}


function file_image_thumb($srcfile, $desfile = '', $width = 0) {
	global $_W;

	if (!file_exists($srcfile)) {
		return error('-1', '原图像不存在');
	}
	if (intval($width) == 0) {
		load()->model('setting');
		$width = intval($_W['setting']['upload']['image']['width']);
	}
	if (intval($width) < 0) {
		return error('-1', '缩放宽度无效');
	}

	if (empty($desfile)) {
		$ext = pathinfo($srcfile, PATHINFO_EXTENSION);
		$srcdir = dirname($srcfile);
		do {
			$desfile = $srcdir . '/' . random(30) . ".{$ext}";
		} while (file_exists($desfile));
	}

	$des = dirname($desfile);
		if (!file_exists($des)) {
		if (!mkdirs($des)) {
			return error('-1', '创建目录失败');
		}
	} elseif (!is_writable($des)) {
		return error('-1', '目录无法写入');
	}

		$org_info = @getimagesize($srcfile);
	if ($org_info) {
		if ($width == 0 || $width > $org_info[0]) {
			copy($srcfile, $desfile);
			return str_replace(ATTACHMENT_ROOT . '/', '', $desfile);
		}
		if ($org_info[2] == 1) { 			if (function_exists("imagecreatefromgif")) {
				$img_org = imagecreatefromgif($srcfile);
			}
		} elseif ($org_info[2] == 2) {
			if (function_exists("imagecreatefromjpeg")) {
				$img_org = imagecreatefromjpeg($srcfile);
			}
		} elseif ($org_info[2] == 3) {
			if (function_exists("imagecreatefrompng")) {
				$img_org = imagecreatefrompng($srcfile);
				imagesavealpha($img_org, true);
			}
		}
	} else {
		return error('-1', '获取原始图像信息失败');
	}
		$scale_org = $org_info[0] / $org_info[1];
		$height = $width / $scale_org;
	if (function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled") && @$img_dst = imagecreatetruecolor($width, $height)) {
		imagealphablending($img_dst, false);
		imagesavealpha($img_dst, true);
		imagecopyresampled($img_dst, $img_org, 0, 0, 0, 0, $width, $height, $org_info[0], $org_info[1]);
	} else {
		return error('-1', 'PHP环境不支持图片处理');
	}
	if ($org_info[2] == 2) {
		if (function_exists('imagejpeg')) {
			imagejpeg($img_dst, $desfile);
		}
	} else {
		if (function_exists('imagepng')) {
			imagepng($img_dst, $desfile);
		}
	}

	imagedestroy($img_dst);
	imagedestroy($img_org);

	return str_replace(ATTACHMENT_ROOT . '/', '', $desfile);
}


function file_image_crop($src, $desfile, $width = 400, $height = 300, $position = 1) {
	if (!file_exists($src)) {
		return error('-1', '原图像不存在');
	}
	if (intval($width) <= 0 || intval($height) <= 0) {
		return error('-1', '裁剪尺寸无效');
	}
	if (intval($position) > 9 || intval($position) < 1) {
		return error('-1', '裁剪位置无效');
	}

	$des = dirname($desfile);
		if (!file_exists($des)) {
		if (!mkdirs($des)) {
			return error('-1', '创建目录失败');
		}
	} elseif (!is_writable($des)) {
		return error('-1', '目录无法写入');
	}
		$org_info = @getimagesize($src);
	if ($org_info) {
		if ($org_info[2] == 1) { 			if (function_exists("imagecreatefromgif")) {
				$img_org = imagecreatefromgif($src);
			}
		} elseif ($org_info[2] == 2) {
			if (function_exists("imagecreatefromjpeg")) {
				$img_org = imagecreatefromjpeg($src);
			}
		} elseif ($org_info[2] == 3) {
			if (function_exists("imagecreatefrompng")) {
				$img_org = imagecreatefrompng($src);
			}
		}
	} else {
		return error('-1', '获取原始图像信息失败');
	}

		if ($width == '0' || $width > $org_info[0]) {
		$width = $org_info[0];
	}
	if ($height == '0' || $height > $org_info[1]) {
		$height = $org_info[1];
	}
		switch ($position) {
		case 0 :
		case 1 :
			$dst_x = 0;
			$dst_y = 0;
			break;
		case 2 :
			$dst_x = ($org_info[0] - $width) / 2;
			$dst_y = 0;
			break;
		case 3 :
			$dst_x = $org_info[0] - $width;
			$dst_y = 0;
			break;
		case 4 :
			$dst_x = 0;
			$dst_y = ($org_info[1] - $height) / 2;
			break;
		case 5 :
			$dst_x = ($org_info[0] - $width) / 2;
			$dst_y = ($org_info[1] - $height) / 2;
			break;
		case 6 :
			$dst_x = $org_info[0] - $width;
			$dst_y = ($org_info[1] - $height) / 2;
			break;
		case 7 :
			$dst_x = 0;
			$dst_y = $org_info[1] - $height;
			break;
		case 8 :
			$dst_x = ($org_info[0] - $width) / 2;
			$dst_y = $org_info[1] - $height;
			break;
		case 9 :
			$dst_x = $org_info[0] - $width;
			$dst_y = $org_info[1] - $height;
			break;
		default:
			$dst_x = 0;
			$dst_y = 0;
	}
	if ($width == $org_info[0]) {
		$dst_x = 0;
	}
	if ($height == $org_info[1]) {
		$dst_y = 0;
	}

	if (function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled") && @$img_dst = imagecreatetruecolor($width, $height)) {
		imagecopyresampled($img_dst, $img_org, 0, 0, $dst_x, $dst_y, $width, $height, $width, $height);
	} else {
		return error('-1', 'PHP环境不支持图片处理');
	}
	if (function_exists('imagejpeg')) {
		imagejpeg($img_dst, $desfile);
	} elseif (function_exists('imagepng')) {
		imagepng($img_dst, $desfile);
	}
	imagedestroy($img_dst);
	imagedestroy($img_org);
	return true;
}


function file_lists($filepath, $subdir = 1, $ex = '', $isdir = 0, $md5 = 0, $enforcement = 0) {
	static $file_list = array();
	if ($enforcement) $file_list = array();
	$flags = $isdir ? GLOB_ONLYDIR : 0;
	$list = glob($filepath . '*' . (!empty($ex) && empty($subdir) ? '.' . $ex : ''), $flags);
	if (!empty($ex)) $ex_num = strlen($ex);
	foreach ($list as $k => $v) {
		$v = str_replace('\\', '/', $v);
		$v1 = str_replace(IA_ROOT . '/', '', $v);
		if ($subdir && is_dir($v)) {
			file_lists($v . '/', $subdir, $ex, $isdir, $md5);
			continue;
		}
		if (!empty($ex) && strtolower(substr($v, -$ex_num, $ex_num)) == $ex) {

			if ($md5) {
				$file_list[$v1] = md5_file($v);
			} else {
				$file_list[] = $v1;
			}
			continue;
		} elseif (!empty($ex) && strtolower(substr($v, -$ex_num, $ex_num)) != $ex) {
			unset($list[$k]);
			continue;
		}
	}
	return $file_list;
}
