<?php
	
/**
 * 女神来了导出
 */
if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');
global $_GPC,$_W;
$rid= intval($_GPC['rid']);
$uniacid = $_W['uniacid'];
if(empty($rid)){
    message('抱歉，传递的参数错误！','', 'error');              
}

 // $params = array(':rid' => $rid);
 // $list = pdo_fetchall("SELECT * FROM " . tablename('wdl_comeon_fans') . " WHERE rid = :rid " . $where . " ORDER BY points DESC ", $params);
	$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
	if(!empty($zj)){
	    $where = 'And status=1';
	}
	if ($rid>0){
	    $list = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE rid =:rid  and uniacid= :uniacid '.$where.' ORDER BY `photosnum` + `xnphotosnum` + `hits` + `xnhits` DESC', array(':rid' => $rid,':uniacid'=>$uniacid));	
	}else{
	    $list = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid '.$where.' ORDER BY `photosnum` + `xnphotosnum` + `hits` + `xnhits` DESC', array(':uniacid'=>$uniacid));	
	}
 
      /**  $awards = pdo_fetchall("select * from ".tablename('wdl_comeon_award')." where rid=:rid ",array(":rid"=>$rid));
        foreach($list as &$row){
            $awardnames = array();    
            foreach($awards as $award){
                if($row['points']>=$award['point']){
                      $awardnames[] = $award['name'];
                }
            } 
           $row['awardnames'] = $awardnames;
           
            if(!empty($row['awardid'])){
                $row['awardname'] = pdo_fetchcolumn("select name from ".tablename('wdl_comeon_award')." where id=:id limit 1 ",array(":id"=>$row['awardid']));
            }
        }
        unset($row);
        	**/				 

$tableheader = array('排名',  '姓名', '手机号','微信号' ,'QQ号', '邮箱','地址' , '宣言','参赛照片' , '真实票数', '虚拟票数', '真实人气', '虚拟人气', '分享数', 'IP', '报名时间', '简介');
$html = "\xEF\xBB\xBF";
foreach ($tableheader as $value) {
	$html .= $value . "\t ,";
}
$html .= "\n";
foreach ($list as $mid => $value) {
	$sharenum = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_data)." WHERE uniacid = :uniacid and tfrom_user = :tfrom_user and rid = :rid", array(':uniacid' => $uniacid,':tfrom_user' => $value['from_user'],':rid' => $rid));
	if(empty($value['realname'])){
		$value['realname']=$value['nickname'];
	}else {
		$value['realname']=$value['realname'];
	}
	if(empty($value['weixin'])){
		$value['weixin']=$value['from_user'];
	}else {
		$value['weixin']=$value['weixin'];
	}
	$p = $mid + 1;
	$html .= $p . "\t ,";	
	$html .= $value['realname'] . "\t ,";	
	$html .= $value['mobile'] . "\t ,";	
	$html .= $value['weixin'] . "\t ,";	
	$html .= $value['qqhao'] . "\t ,";	
	$html .= $value['email'] . "\t ,";	
	$html .= $value['address'] . "\t ,";	
	$html .= $value['photoname'] . "\t ,";
	$html .= $value['photo'] . "\t ,";	
	$html .= $value['photosnum'] . "\t ,";	
	$html .= $value['xnphotosnum'] . "\t ,";	
	$html .= $value['hits'] . "\t ,";
	$html .= $value['xnhits'] . "\t ,";	
	$html .= $sharenum . "\t ,";	
	$html .= $value['createip'] . "\t ,";	
	$html .= date('Y年m月d日 H:i:s',$value['createtime']) . "\t ,";	
	$html .= $value['description'] . "\t ,";	
	$html .= "\n";
}
$filename = $reply['title'].'_'.$rid.'_'.$now;

header("Content-type:text/csv");
header("Content-Disposition:attachment; filename=".$filename.".csv");

echo $html;
exit();
