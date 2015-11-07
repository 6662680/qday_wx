<?php
/**
 * 女神来了导出
 *
 */
if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');
global $_GPC,$_W;
$rid= intval($_GPC['rid']);
$uniacid = $_W['uniacid'];
if(empty($rid)){
    message('抱歉，传递的参数错误！','', 'error');              
}
	$afrom_user = $_GPC['afrom_user'];
	$tfrom_user = $_GPC['tfrom_user'];
	$keyword = $_GPC['keyword'];
	$Where = "";
	if (!empty($keyword)){			
			$Where .= " AND nickname LIKE '%{$keyword}%'";				
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

		
		//取得分享点击详细数据
		$list = pdo_fetchall('SELECT * FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid '.$Where.'  ORDER BY `ip` DESC, `createtime` ASC', array(':uniacid' => $uniacid) );
	
	//$list = pdo_fetchall('SELECT * FROM '.tablename($this->table_log).' WHERE rid =:rid  and uniacid= :uniacid '.$where.' ORDER BY `createtime` DESC, `ip` ASC', array(':rid' => $rid,':uniacid'=>$_W['uniacid']));	
	    
$tableheader = array('ID', '投票人', '投票时间','投票IP' ,'被投票人','主题宣言','联系方式', '拉票人');
$html = "\xEF\xBB\xBF";
foreach ($tableheader as $value) {
	$html .= $value . "\t ,";
}
$html .= "\n";
foreach ($list as $mid => $value) {
	$fuser = $this->_getuser($value['rid'], $value['tfrom_user']);
	$auser = $this->_auser($value['rid'], $value['afrom_user']);
	
	$html .= $value['id'] . "\t ,";	
	$html .= $value['nickname'] . "\t ,";	
	$html .= date('Y-m-d H:i:s', $value['createtime']) . "\t ,";	
	$html .= $value['ip'] . "\t ,";	
	$html .= $fuser['nickname'] . "\t ,";	
	$html .= $fuser['photoname'] . "\t ,";	
	$html .= $fuser['mobile'] . "\t ,";	
	$html .= $auser['nickname'] . "\t ,";	
	$html .= "\n";
}
$html .= "\n";

$vote = pdo_fetchall("SELECT distinct(ip) FROM ".tablename($this->table_log)." WHERE uniacid = :uniacid  $Where  ", array(':uniacid' => $uniacid));				
				
$tvtotal = array();
foreach ($vote as $v) {
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_log)." WHERE uniacid= :uniacid  $Where AND ip = :ip order by `ip` desc ", array(':uniacid' => $uniacid, ':ip' => $v['ip']));
	$tvtotal[$v[ip]] .= $total;
	
}
arsort($tvtotal);
	$html .= '统计情况' . "\t ,";	
	$html .= '排行' . "\t ,";	
	$html .= '相同IP' . "\t ,";	
	$html .= '地区' . "\t ,";	
	$html .= '投票次数' . "\t ,";	
	$html .= "\n";
	$n = 0;
	
foreach ($tvtotal as $mid => $t) {
	
	if ($n == 15) {break;}
	$ip = GetIpLookup($mid);
	$ip = $ip['country'].'  '.$ip['province'].'  '.$ip['city'].'  '.$ip['district'].'  '.$ip['ist'];
	$html .= '' . "\t ,";	
	$html .= $n +1 . "\t ,";	
	$html .= $mid . "\t ,";	
	$html .= $ip . "\t ,";	
	$html .= $t . '次' . "\t ,";	
	$html .= "\n";
	$n++;
}


$now = date('Y-m-d H:i:s', time());
if ($keyword) {
	$k = $keyword.' 的';
}
$filename =$k.'投票记录情况'.'_'.$rid.'_'.$now;

header("Content-type:text/csv");
header("Content-Disposition:attachment; filename=".$filename.".csv");

echo $html;
exit();
