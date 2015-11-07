<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

$advs = pdo_fetchall("SELECT link, thumb, times FROM " . tablename($this->table_advs) . " WHERE ismiaoxian = 1 AND issuiji = 1 AND uniacid= '{$uniacid}'  AND rid= '{$rid}' ORDER BY displayorder ASC");
			$advarr = array();
			foreach ($advs as $mid => $adv) {
				if (substr($adv['link'], 0, 5) != 'http:' && $adv['link'] != '#' && $adv['link']!='javascript::;' && $adv['link']!='') {
					$advarr['link'.$mid] .= "http://" . $adv['link'];
				}
				if (!$advarr['link'.$mid]) {
					$advarr['link'.$mid] = $_W['siteroot'] .'app/'.$this->createMobileUrl('photosvoteview', array('rid' => $rid));
				}
					$advarr['thumb'.$mid] .= $adv['thumb'];
					$advarr['times'.$mid] .= $adv['times'];
			}
			
			//unset($advarr);
//print_r($advarr);
			$totaladvs = count($advs)-1;
			$sjmid = rand(0,$totaladvs);
		
		
		$toye = $this->_stopllq('miaoxian');
		include $this->template($toye);
		