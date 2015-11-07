<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
$afrom_user = $_GPC['afrom_user'];
		$tfrom_user = $_GPC['tfrom_user'];
		
		$keyword = $_GPC['keyword'];
		
		$Where = "";
		if (!empty($keyword)){
			
			$Where .= " AND content LIKE '%{$keyword}%' OR nickname LIKE '%{$keyword}%'";				
			$Where .= " OR ip LIKE '%{$keyword}%'";	
			$t = pdo_fetchall("SELECT from_user FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and  rid = :rid and nickname LIKE '%{$keyword}%' ", array(':uniacid' => $uniacid, ':rid' => $rid));
			foreach ($t as $row) {
				$Where .= " OR tfrom_user LIKE '%{$row['from_user']}%'";
			}
		}
		if (!empty($tfrom_user)){
		$Where .= " AND `tfrom_user` = '{$tfrom_user}'";		
		}
		if (!empty($afrom_user)){
			$Where .= " AND `afrom_user` = '{$afrom_user}'";		
		}
		if (!empty($rid)){
			$Where .= " AND `rid` = $rid";		
		}

		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		//取得分享点击详细数据
		$messages = pdo_fetchall('SELECT * FROM '.tablename($this->table_bbsreply).' WHERE uniacid= :uniacid '.$Where.'  order by `createtime` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $uniacid) );
		
		//查询分享人姓名电话结束
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_bbsreply).' WHERE uniacid= :uniacid '.$Where.'  order by `createtime` desc ', array(':uniacid' => $uniacid));
		$pager = pagination($total, $pindex, $psize);
		include $this->template('message');
