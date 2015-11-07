<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
$reply = pdo_fetch('SELECT * FROM '.tablename($this->table_reply).' WHERE uniacid= :uniacid AND rid =:rid ', array(':uniacid' => $uniacid, ':rid' => $rid) );
		$foo = !empty($_GPC['foo']) ? $_GPC['foo'] : 'post';		
		if ($foo == 'display') {	
			
				$vote = pdo_fetchall("SELECT distinct(ip) FROM ".tablename($this->table_log)." WHERE uniacid = :uniacid AND rid = :rid  ", array(':uniacid' => $uniacid, ':rid' => $rid));
				$tvtotal = array();
				foreach ($vote as $v) {
					$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid  AND rid= :rid AND ip = :ip order by `ip` desc ', array(':uniacid' => $uniacid, ':rid' => $rid, ':ip' => $v['ip']));
					$tvtotal[$v[ip]] .= $total;
					
				}
				arsort($tvtotal);
				
			
		}elseif ($foo == 'post') {			
			$pindex = max(1, intval($_GPC['page']));
			$psize = 15;

			//取得ip详细数据
			$iplist = pdo_fetchall('SELECT * FROM '.tablename($this->table_iplist).' WHERE uniacid= :uniacid  AND  rid= :rid order by `createtime` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $uniacid, ':rid' => $rid));
			//$iparr = iunserializer($item['iparr']);
			
			
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_iplist).' WHERE uniacid= :uniacid  AND rid= :rid  order by `createtime` desc ', array(':uniacid' => $uniacid, ':rid' => $rid));
			$pager = pagination($total, $pindex, $psize);
			
			if (checksubmit('submit')) {
				if (!empty($_GPC['ipstart'])) {					
					foreach ($_GPC['ipstart'] as $index => $row) {
						$iparr = array(
								'ipstart' =>$_GPC['ipstart'][$index],
								'ipend' =>$_GPC['ipend'][$index]
								);
						$data = array(
							'iparr' =>iserializer($iparr),
							'rid' => $rid,
							'createtime' => time(),
						);
						if (!empty($_GPC['ipadd'][$index])) {
							$data['ipadd'] = $_GPC['ipadd'][$index];
						}
						if(!empty($data['iparr'])) {
							if(pdo_fetch("SELECT id FROM ".tablename($this->table_iplist)." WHERE iparr = :iparr AND id != :id", array(':iparr' => $data['iparr'], ':id' => $index))) {
								continue;
							}
							if(pdo_fetch("SELECT id FROM ".tablename($this->table_iplist)." WHERE ipadd = :ipadd AND id != :id", array(':ipadd' => $data['ipadd'], ':id' => $index))) {
								continue;
							}
							$row = pdo_fetch("SELECT id FROM ".tablename($this->table_iplist)." WHERE iparr = :iparr AND ipadd = :ipadd  AND rid = :rid   LIMIT 1",array(':iparr' => $data['iparr'],':ipadd' => $data['ipadd'],':rid' => $rid));
							if(empty($row)) {
								pdo_update($this->table_iplist, $data, array('id' => $index));
							}
							unset($row);
						}
					}
				}
				if (!empty($_GPC['ipstart-new'])) {
					foreach ($_GPC['ipstart-new'] as $index => $row) {
						$iparr = array(
								'ipstart' =>$_GPC['ipstart-new'][$index],
								'ipend' =>$_GPC['ipend-new'][$index]
								);
						$data = array(
								'uniacid' => $uniacid,
								'rid' => $rid,
								'iparr' =>iserializer($iparr),
								'ipadd' => $_GPC['ipadd-new'][$index],
								'createtime' => time(),
						);
						if(!empty($data['iparr']) && !empty($data['ipadd'])) {
							if(pdo_fetch("SELECT id FROM ".tablename($this->table_iplist)." WHERE iparr = :iparr", array(':iparr' => $data['iparr']))) {
								continue;
							}
							if(pdo_fetch("SELECT id FROM ".tablename($this->table_iplist)." WHERE ipadd = :ipadd", array(':ipadd' => $data['ipadd']))) {
								continue;
							}
							pdo_insert($this->table_iplist, $data);
							unset($row);
						}
					}
				}
				
				if (!empty($_GPC['delete'])) {
					pdo_query("DELETE FROM ".tablename($this->table_iplist)." WHERE id IN (".implode(',', $_GPC['delete']).")");
				}

				message('更新成功！', referer(), 'success');
			}
		
		}
		
		
		
		
		include $this->template('iplist');
