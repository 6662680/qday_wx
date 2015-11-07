<?php
/**
 * [Fmoons System] Copyright (c) 2014 qdaygroup.com
 * Fmoons isNOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
 $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

	$rid = intval($_GPC['rid']);
if($operation == 'display') {
	load()->func('tpl');
	$rid = intval($_GPC['rid']);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = '';
	$params = array();
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND title LIKE :keyword";
		$params[':keyword'] = "%{$_GPC['keyword']}%";
	}
	
	$list = pdo_fetchall("SELECT * FROM ".tablename('site_article')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY displayorder DESC, id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('site_article') . " WHERE uniacid = '{$_W['uniacid']}'");
	$pager = pagination($total, $pindex, $psize);
	
	include $this->template('fmqf');
} elseif($operation == 'post') {
	load()->func('tpl');
	load()->func('file');
	$id = intval($_GPC['id']);
	$rid = intval($_GPC['rid']);
		$template = uni_templates();
	$pcate = $_GPC['pcate'];
	$ccate = $_GPC['ccate'];
	if (!empty($id)) {
		$item = pdo_fetch("SELECT * FROM ".tablename('site_article')." WHERE id = :id" , array(':id' => $id));
		$item['type'] = explode(',', $item['type']);
		$pcate = $item['pcate'];
		$ccate = $item['ccate'];
		if (empty($item)) {
			message('抱歉，文章不存在或是已经删除！', '', 'error');
		}
		$keywords = pdo_fetchcolumn('SELECT content FROM ' . tablename('rule_keyword') . ' WHERE id = :id AND uniacid = :uniacid ', array(':id' => $item['kid'], ':uniacid' => $_W['uniacid']));
		$item['credit'] = iunserializer($item['credit']) ? iunserializer($item['credit']) : array();
		if(!empty($item['credit']['limit'])) {
						$credit_num = pdo_fetchcolumn('SELECT SUM(credit_value) FROM ' . tablename('mc_handsel') . ' WHERE uniacid = :uniacid AND module = :module AND sign = :sign', array(':uniacid' => $_W['uniacid'], ':module' => 'article', ':sign' => md5(iserializer(array('id' => $id)))));
			if(is_null($credit_num)) $credit_num = 0;
			$credit_yu = (($item['credit']['limit'] - $credit_num) < 0) ? 0 : $item['credit']['limit'] - $credit_num;
		}
	} else {
		$item['credit'] = array();
	}
	if (checksubmit('submit')) {
		if (empty($_GPC['title'])) {
			message('标题不能为空，请输入标题！');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'iscommend' => intval($_GPC['option']['commend']),
			'ishot' => intval($_GPC['option']['hot']),
			'pcate' => intval($_GPC['category']['parentid']),
			'ccate' => intval($_GPC['category']['childid']),
			'template' => $_GPC['template'],
			'title' => $_GPC['title'],
			'description' => $_GPC['description'],
			'content' => htmlspecialchars_decode($_GPC['content']),
			'incontent' => intval($_GPC['incontent']),
			'source' => $_GPC['source'],
			'author' => $_GPC['author'],
			'displayorder' => intval($_GPC['displayorder']),
			'linkurl' => $_GPC['linkurl'],
			'createtime' => TIMESTAMP,
			'click' => intval($_GPC['click'])
		);
		if (!empty($_GPC['thumb'])) {
			$data['thumb'] = $_GPC['thumb'];
		} elseif (!empty($_GPC['autolitpic'])) {
			$match = array();
			preg_match('/attachment\/(.*?)(\.gif|\.jpg|\.png|\.bmp)/', $_GPC['content'], $match);
			if (!empty($match[1])) {
				$data['thumb'] = $match[1].$match[2];
			}
		} else {
			$data['thumb'] = '';
		}
				
		if (empty($id)) {
			
			pdo_insert('site_article', $data);
			$aid = pdo_insertid();
			
		} else {
			unset($data['createtime']);
			
			pdo_update('site_article', $data, array('id' => $id));
		}
		message('文章更新成功！', $this->createWebUrl('fmqf', array('op' => 'display', 'rid' => $rid)), 'success');
	} else {
		include $this->template('fmqf');
	}

} elseif($operation == 'fasong') {
	 global $_GPC, $_W;
		if($_W['isajax']) {
				$id = intval($_GPC['id']);
				$rid = intval($_GPC['rid']);
				$item = pdo_fetch("SELECT * FROM ".tablename('site_article')." WHERE id = :id" , array(':id' => $id));
				
				//$groups = pdo_fetchall("SELECT * FROM ".tablename('fm_autogroup_group')." WHERE uniacid = '{$_W['uniacid']}' ORDER BY gname ASC, id DESC ");
				
				
				include $this->template('fasong');
				exit();
		}
	
} elseif($operation == 'fasongstart') {
	 global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$gid = intval($_GPC['gid']);
		$rid = intval($_GPC['rid']);
		$uniacid = $_W['uniacid'];
		if(!$id){
		    message('文章不存在', $this->createWebUrl('fmqf', array('op' => 'display', 'rid' => $rid)), 'error');
		} else {	
		
			$item = pdo_fetch("SELECT * FROM ".tablename('site_article')." WHERE id = :id" , array(':id' => $id));
			if (!empty($item['linkurl'])) {
				$url = $item['linkurl'];
			}else {
				$url = $_W['siteroot'] . './app/index.php?c=site&a=site&do=detail&id='.$id.'&i='.$_W['uniacid'];
			}
			$to = $this->createWebUrl('sendMobileQfMsg', array('gid' => $gid,'rid' => $rid,'id' => $id,'url' => urlencode($url)));
			header("location:$to");
			exit;
		} 
} elseif($operation == 'delete') {
	load()->func('file');
	$id = intval($_GPC['id']);
	$row = pdo_fetch("SELECT id,rid,kid,thumb FROM ".tablename('site_article')." WHERE id = :id", array(':id' => $id));
	if (empty($row)) {
		message('抱歉，文章不存在或是已经被删除！');
	}
	if (!empty($row['thumb'])) {
		file_delete($row['thumb']);
	}
	pdo_delete('site_article', array('id' => $id));
	message('删除成功！', referer(), 'success');
} 


