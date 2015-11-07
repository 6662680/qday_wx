<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
$indexpx = intval($_GPC['indexpx']);
		$indexpxf = intval($_GPC['indexpxf']);
		if (empty($page)){$page = 1;}
		$where = '';
		!empty($_GPC['keywordnickname']) && $where .= " AND nickname LIKE '%{$_GPC['keywordnickname']}%'";
		!empty($_GPC['keywordid']) && $where .= " AND rid = '{$_GPC['keywordid']}'";
		!empty($rid) && $where .= " AND rid = '{$rid}'";

		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$order = '';
		//0 按最新排序 1 按人气排序 3 按投票数排序
		if ($indexpx == '-1') {
			$order .= " `createtime` DESC";
		}elseif ($indexpx == '1') {
			$order .= " `hits` + `xnhits` DESC";
		}elseif ($indexpx == '2') {
			$order .= " `photosnum` + `xnphotosnum` DESC";
		}
		
		//0 按最新排序 1 按人气排序 3 按投票数排序  倒叙
		if ($indexpxf == '-1') {
			$order .= " `createtime` ASC";
		}elseif ($indexpxf == '1') {
			$order .= " `hits` + `xnhits` ASC";
		}elseif ($indexpxf == '2') {
			$order .= " `photosnum` + `xnphotosnum` ASC";
		}
		
		if (empty($indexpx) && empty($indexpxf)) {
			$order .= " `createtime` DESC";
		}
		
		
		//取得用户列表
		$list_praise = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid '.$where.' order by '.$order.' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $uniacid) );
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid '.$where.' ', array(':uniacid' => $uniacid));
		$pager = pagination($total, $pindex, $psize);
		include $this->template('rankinglist');
