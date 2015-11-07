<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$list_praise = pdo_fetchall('SELECT * FROM '.tablename($this->table_reply).' WHERE uniacid= :uniacid order by `id` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $uniacid) );
		$pager = pagination($total, $pindex, $psize);
		
		if (!empty($list_praise)) {
			foreach ($list_praise as $mid => $list) {
				$count = pdo_fetch("SELECT count(id) as tprc FROM ".tablename($this->table_log)." WHERE rid= ".$list['rid']."");
				//$count1 = pdo_fetch("SELECT count(id) as share FROM ".tablename($this->table_log)." WHERE rid= ".$list['rid']." AND afrom_user != ''");
				$count1 = pdo_fetch("SELECT COUNT(id) as share FROM ".tablename($this->table_data)." WHERE uniacid = :uniacid and rid = :rid", array(':uniacid' => $uniacid,':rid' => $list['rid']));
				$count2 = pdo_fetch("SELECT count(id) as ysh FROM ".tablename($this->table_users)." WHERE rid= ".$list['rid']." AND status = '1' ");
				$count3 = pdo_fetch("SELECT count(id) as wsh FROM ".tablename($this->table_users)." WHERE rid= ".$list['rid']." AND status = '0' ");
				$count4 = pdo_fetch("SELECT count(id) as cyrs FROM ".tablename($this->table_users)." WHERE rid= ".$list['rid']."");
		        $list_praise[$mid]['user_tprc'] = $count['tprc'];//投票人次
		        $list_praise[$mid]['user_share'] = $count1['share'] + pdo_fetchcolumn("SELECT sum(sharenum) FROM ".tablename($this->table_users)." WHERE rid= ".$list['rid']."");//分享人数
		        $list_praise[$mid]['user_ysh'] = $count2['ysh'];//已审核
		        $list_praise[$mid]['user_wsh'] = $count3['wsh'];//未审核
		        $list_praise[$mid]['user_cyrs'] = $count4['cyrs'] + $list['xuninum'];//参与人数
				
				 $list_praise[$mid]['user_hits'] =   $list_praise[$mid]['user_cyrs'] +  $list['hits'] + pdo_fetchcolumn("SELECT sum(hits) FROM ".tablename($this->table_users)." WHERE rid= ".$list['rid']."") + pdo_fetchcolumn("SELECT sum(xnhits) FROM ".tablename($this->table_users)." WHERE rid= ".$list['rid']."");
				 //点击&参与
				//$count = pdo_fetch("SELECT count(id) as dd FROM ".tablename($this->table_data)." WHERE rid= ".$list['rid']."");
		       // $list_praise[$mid]['share_znum'] = $count['dd'];//分享人数
				
				$listpraise = pdo_fetchall('SELECT * FROM '.tablename($this->table_gift).' WHERE rid=:rid  order by `id`',array(':rid' => $list['rid']));
				if (!empty($listpraise)) {
			         $praiseinfo = '';
					 foreach ($listpraise as $row) {
					   $zigenum = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and rid = :rid and yaoqingnum>= :yaoqingnum", array(':uniacid' => $uniacid, ':rid' => $list['rid'], ':yaoqingnum' => $row['break']));
					   $praiseinfo = $praiseinfo.'奖品：'.$row['title'].'；总数为：'.$row['total'].'；已领奖品数为：'.$row['total_winning'].'；拥有奖品资格粉丝数：'.$zigenum.'；没有领取奖品粉丝数：'.($zigenum-$row['total_winning']).'；还剩：<b>'.($row['total']-$row['total_winning']).'</b>个奖品没有发放<br/>';
			        }
		        }
				$praiseinfo = substr($praiseinfo,0,strlen($praiseinfo)-5); 
				$list_praise[$mid]['praiseinfo'] = $praiseinfo;//奖品情况
			}
		}
		include $this->template('index');
