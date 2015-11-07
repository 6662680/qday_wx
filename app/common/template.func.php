<?php
/**
 * [SxxPro System] Copyright (c) 2014 012WZ.COM
 * SxxPro is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

function template_compat($filename) {
	static $mapping = array(
		'home/home' => 'index',
		'header' => 'common/header',
		'footer' => 'common/footer',
		'slide' => 'common/slide',
	);
	if(!empty($mapping[$filename])) {
		return $mapping[$filename];
	}
	return '';
}

function template($filename, $flag = TEMPLATE_DISPLAY) {
	global $_W, $_GPC;
	$source = IA_ROOT . "/app/themes/{$_W['template']}/{$filename}.html";
	$compile = IA_ROOT . "/data/tpl/app/{$_W['template']}/{$filename}.tpl.php";
	if(!is_file($source)) {
		$compatFilename = template_compat($filename);
		if(!empty($compatFilename)) {
			return template($compatFilename, $flag);
		}
	}
	if(!is_file($source)) {
		$source = IA_ROOT . "/app/themes/default/{$filename}.html";
		$compile = IA_ROOT . "/data/tpl/app/default/{$filename}.tpl.php";
	}

	if(!is_file($source)) {
		exit("Error: template source '{$filename}' is not exist!");
	}
	$paths = pathinfo($compile);
	$compile = str_replace($paths['filename'], $_W['uniacid'] . '_' . intval($_GPC['t']) . '_' . $paths['filename'], $compile);

	if(DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
		template_compile($source, $compile);
	}
	switch ($flag) {
		case TEMPLATE_DISPLAY:
		default:
			extract($GLOBALS, EXTR_SKIP);
			include $compile;
			break;
		case TEMPLATE_FETCH:
			extract($GLOBALS, EXTR_SKIP);
			ob_clean();
			ob_start();
			include $compile;
			$contents = ob_get_contents();
			ob_clean();
			return $contents;
			break;
		case TEMPLATE_INCLUDEPATH:
			return $compile;
			break;
	}
}

function template_compile($from, $to) {
	$path = dirname($to);
	if (!is_dir($path)) {
		load()->func('file');		
		mkdirs($path);
	}
	$content = template_parse(file_get_contents($from));
	file_put_contents($to, $content);
}

function template_parse($str) {
	$str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
	$str = preg_replace('/{template\s+(.+?)}/', '<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template($1, TEMPLATE_INCLUDEPATH)) : (include template($1, TEMPLATE_INCLUDEPATH));?>', $str);
	$str = preg_replace('/{php\s+(.+?)}/', '<?php $1?>', $str);
	$str = preg_replace('/{if\s+(.+?)}/', '<?php if($1) { ?>', $str);
	$str = preg_replace('/{else}/', '<?php } else { ?>', $str);
	$str = preg_replace('/{else ?if\s+(.+?)}/', '<?php } else if($1) { ?>', $str);
	$str = preg_replace('/{\/if}/', '<?php } ?>', $str);
	$str = preg_replace('/{loop\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2) { ?>', $str);
	$str = preg_replace('/{loop\s+(\S+)\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2 => $3) { ?>', $str);
	$str = preg_replace('/{\/loop}/', '<?php } } ?>', $str);
	$str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)}/', '<?php echo $1;?>', $str);
	$str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\[\]\'\"\$]*)}/', '<?php echo $1;?>', $str);
	$str = preg_replace('/{url\s+(\S+)}/', '<?php echo url($1);?>', $str);
	$str = preg_replace('/{url\s+(\S+)\s+(array\(.+?\))}/', '<?php echo url($1, $2);?>', $str);
	$str = preg_replace_callback('/{data\s+(.+?)}/s', "moduledata", $str);
	$str = preg_replace('/{\/data}/', '<?php } } ?>', $str);
	$str = preg_replace_callback('/<\?php([^\?]+)\?>/s', "template_addquote", $str);
	$str = preg_replace('/{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)}/s', '<?php echo $1;?>', $str);
	$str = str_replace('{##', '{', $str);
	$str = str_replace('##}', '}', $str);
	$str = "<?php defined('IN_IA') or exit('Access Denied');?>" . $str;
	return $str;
}

function template_addquote($matchs) {
	$code = "<?php {$matchs[1]}?>";
	$code = preg_replace('/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\](?![a-zA-Z0-9_\-\.\x7f-\xff\[\]]*[\'"])/s', "['$1']", $code);
	return str_replace('\\\"', '\"', $code);
}


function moduledata($params = '') {
	if (empty($params[1])) {
		return '';
	}
	$params = explode(' ', $params[1]);
	if (empty($params)) {
		return '';
	}
	$data = array();
	foreach ($params as $row) {
		$row = explode('=', $row);
		$data[$row[0]] = str_replace(array("'", '"'), '', $row[1]);
	}
	$funcname = $data['func'];
	$assign = !empty($data['assign']) ? $data['assign'] : $funcname;
	$item = !empty($data['item']) ? $data['item'] : 'row';
	$data['limit'] = !empty($data['limit']) ? $data['limit'] : 10;
	if (empty($data['return']) || $data['return'] == 'false') {
		$return = false;
	} else {
		$return = true;
	}
	$data['index'] = !empty($data['index']) ? $data['index'] : 'iteration';
	if (!empty($data['module'])) {
		$modulename = $data['module'];
		unset($data['module']);
	} else {
		list($modulename) = explode('_', $data['func']);
	}
	$data['multiid'] = intval($_GET['t']);
	$data['uniacid'] = intval($_GET['i']);
	$data['acid'] = intval($_GET['j']);
	
	if (empty($modulename) || empty($funcname)) {
		return '';
	}
	$variable = var_export($data, true);
	$variable = preg_replace("/'(\\$[a-zA-Z_\x7f-\xff\[\]\']*?)'/", '$1', $variable);
	$php = "<?php \${$assign} = modulefunc('$modulename', '{$funcname}', {$variable}); ";
	if (empty($return)) {
		$php .= "if(is_array(\${$assign})) { \$i=0; foreach(\${$assign} as \$i => \${$item}) { \$i++; \${$item}['{$data['index']}'] = \$i; ";
	}
	$php .= "?>";
	return $php;
}

function modulefunc($modulename, $funcname, $params) {
	static $includes;

	$includefile = '';
	if (!function_exists($funcname)) {
		if (!isset($includes[$modulename])) {
			if (!file_exists(IA_ROOT . '/addons/'.$modulename.'/model.php')) {
				return '';
			} else {
				$includes[$modulename] = true;
				include_once IA_ROOT . '/addons/'.$modulename.'/model.php';
			}
		}
	}

	if (function_exists($funcname)) {
		return call_user_func_array($funcname, array($params));
	} else {
		return array();
	}
}


function site_navs($params = array()) {
	global $_W, $multi, $cid, $ishomepage;
	$condition = '';
	if(!$cid || !$ishomepage) {
		if (!empty($params['section'])) {
			$condition = " AND section = '".intval($params['section'])."'";
		}
		if(empty($params['multiid'])) {
			load()->model('account');
			$setting = uni_setting($_W['uniacid']);
			$multiid = $setting['default_site'];
		} else{
			$multiid = intval($params['multiid']);
		}
		$navs = pdo_fetchall("SELECT id, name, description, url, icon, css, position, module FROM ".tablename('site_nav')." WHERE position = '1' AND status = 1 AND uniacid = '{$_W['uniacid']}' AND multiid = '{$multiid}' $condition ORDER BY displayorder DESC, id DESC");
	} else {
		$condition = " AND parentid = '".$cid."'";
		$navs = pdo_fetchall("SELECT * FROM ".tablename('site_category')." WHERE enabled = '1' AND uniacid = '{$_W['uniacid']}' $condition ORDER BY displayorder DESC, id DESC");
	}
	if(!empty($navs)) {
		foreach ($navs as &$row) {
			if(!$cid || !$ishomepage) {
				if (!strexists($row['url'], 'tel:') && !strexists($row['url'], '://') && !strexists($row['url'], 'www') && !strexists($row['url'], 'i=')) {
					$row['url'] .= strexists($row['url'], '?') ?  '&i='.$_W['uniacid'] : '?i='.$_W['uniacid'];
				}
			} else {
				if(empty($row['linkurl']) || (!strexists($row['linkurl'], 'http://') && !strexists($row['linkurl'], 'https://'))) {
					$row['url'] = murl('site/site/list', array('cid' => $row['id']));
				} else {
					$row['url'] = $row['linkurl'];
				}
			}
			$row['css'] = unserialize($row['css']);
			if(empty($row['css']['icon']['icon'])){
				$row['css']['icon']['icon'] = 'fa fa-external-link';
			}
			$row['css']['icon']['style'] = "color:{$row['css']['icon']['color']};font-size:{$row['css']['icon']['font-size']}px;";
			$row['css']['name'] = "color:{$row['css']['name']['color']};";
			$row['html'] = '<a href="'.$row['url'].'" class="box-item">';
			$row['html'] .= '<i '.(!empty($row['icon']) ? "style=\"background:url({$_W['attachurl']}{$row['icon']}) no-repeat;background-size:cover;\" class=\"icon\"" : "class=\"fa {$row['css']['icon']['icon']} \" style=\"{$row['css']['icon']['style']}\"").'></i>';
			$row['html'] .= "<span style=\"{$row['css']['name']}\" title=\"{$row['name']}\">{$row['name']}</span></a>";
		}
		unset($row);
	}
	return $navs;
}

function site_article($params = array()) {
	global $_GPC, $_W;
	extract($params);
	$pindex = max(1, intval($_GPC['page']));
	if (!isset($limit)) {
		$psize = 10;
	} else {
		$psize = intval($limit);
		$psize = max(1, $limit);
	}
	$result = array();
	
	$condition = " WHERE uniacid = :uniacid ";
	$pars = array(':uniacid' => $_W['uniacid']);
	if (!empty($cid)) {
		$category = pdo_fetch("SELECT parentid FROM ".tablename('site_category')." WHERE id = :id", array(':id' => $cid));
		if (!empty($category['parentid'])) {
			$condition .= " AND ccate = :ccate ";
			$pars[':ccate'] = $cid;
		} else {
			$condition .= " AND pcate = :pcate ";
			$pars[':pcate'] = $cid;
		}
	}
	if ($iscommend == 'true') {
		$condition .= " AND iscommend = '1'";
	}
	if ($ishot == 'true') {
		$condition .= " AND ishot = '1'";
	}
	$sql = "SELECT * FROM ".tablename('site_article'). $condition. ' ORDER BY displayorder DESC, id DESC LIMIT ' . ($pindex - 1) * $psize .',' .$psize;
	$result['list'] = pdo_fetchall($sql, $pars);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('site_article') . $condition, $pars);
	$result['pager'] = pagination($total, $pindex, $psize);
	if (!empty($result['list'])) {
		foreach ($result['list'] as &$row) {
			if(empty($row['linkurl'])) {
				$row['linkurl'] = murl('site/site/detail', array('id' => $row['id'], 'uniacid' => $_W['uniacid']));
			}
			$row['thumb'] = tomedia($row['thumb']);
		}
	}
	return $result;
}

function site_category($params = array()) {
	global $_GPC, $_W;
	extract($params);
	if (!isset($parentid)) {
		$condition = "";
	} else {
		$parentid = intval($parentid);
		$condition = " AND parentid = '$parentid'";
	}
	$category = array();
	$result = pdo_fetchall("SELECT * FROM ".tablename('site_category')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY parentid ASC, displayorder ASC, id ASC ");
	if (!isset($parentid)) {
		if (!empty($result)) {
			foreach ($result as $row) {
				if(empty($row['linkurl'])) {
					$row['linkurl'] = url('site/site/list', array('cid' =>$row['id']));
				}
				$row['icon'] = tomedia($row['icon']);
				if (empty($row['parentid'])) {
					$category[$row['id']] = $row;
				} else {
					$category[$row['parentid']]['children'][$row['id']] = $row;
				}
			}
		}
	} else {
		foreach($result as $k => $v) {
			if(empty($result[$k]['linkurl'])) {
				$result[$k]['linkurl'] = url('site/site/list', array('cid' => $result[$k]['id']));
			}
			$result[$k]['icon'] = tomedia($result[$k]['icon']);
		}
		$category = $result;
	}
	return $category;
}

function site_slide_search($params = array()) {
	global $_W;
	if(empty($params['limit'])) {
		$params['limit'] = 4;
	}
	if(empty($params['multiid'])) {
		$multiid = pdo_fetchcolumn('SELECT default_site FROM ' . tablename('uni_settings') . ' WHERE uniacid = :id', array(':id' => $_W['uniacid']));
	} else{
		$multiid = intval($params['multiid']);
	}
	$sql = "SELECT * FROM " . tablename('site_slide') . " WHERE uniacid = '{$_W['uniacid']}' AND multiid = {$multiid} ORDER BY `displayorder` DESC, `id` DESC LIMIT " . intval($params['limit']);
	$list = pdo_fetchall($sql);
	if(!empty($list)) {
		foreach($list as &$row) {
			if (!strexists($row['url'], './')) {
				if (!strexists($row['url'], 'http')) {
					$row['url'] = 'http://' . $row['url'];
				}
			}
			$row['thumb'] = tomedia($row['thumb']);
		}
	}
	return $list;
}

function app_slide($params = array()) {
	return site_slide_search($params);
}
