<?php
	
/**
 * 来吧来吧
 *
 * @author ewei qq:22185157
 * @url 
 */
if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');
global $_GPC,$_W;
$rid= intval($_GPC['rid']);
if(empty($rid)){
    message('抱歉，传递的参数错误！','', 'error');              
}

  $params = array(':rid' => $rid);
  $list = pdo_fetchall("SELECT * FROM " . tablename('wdl_comeon_fans') . " WHERE rid = :rid " . $where . " ORDER BY points DESC ", $params);
 
        $awards = pdo_fetchall("select * from ".tablename('wdl_comeon_award')." where rid=:rid ",array(":rid"=>$rid));
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
        					 
foreach ($list as &$row) {
	if($row['status'] == 0){
		$row['status']='未领取';
	}elseif($row['status'] == 1){
		$row['status']='已中奖';
	}else{
		$row['status']='已领奖';
	}
}
$tableheader = array('ID',  '微信码', '手机号','被助力数' ,'状态', '中奖的奖品','领取的奖金' , '领奖时间', '参与时间');
$html = "\xEF\xBB\xBF";
foreach ($tableheader as $value) {
	$html .= $value . "\t ,";
}
$html .= "\n";
foreach ($list as $value) {
	$html .= $value['id'] . "\t ,";
	 $html .= $value['from_user'] . "\t ,";	
	$html .= $value['mobile'] . "\t ,";	
	$html .= $value['helps'] . "\t ,";	
        $html .= $value['status'] . "\t ,";	
        $html .=implode("/",$value['awardnames']) . "\t ,";	
        $html .=$value['awardname'] . "\t ,";	
         
        
	$html .= ($value['awardtime'] == 0 ? '' : date('Y-m-d H:i',$value['awardtime'])) . "\t ,";
        
	$html .= date('Y-m-d H:i:s', $value['createtime']) . "\n";	
}


header("Content-type:text/csv");
header("Content-Disposition:attachment; filename=全部数据.csv");

echo $html;
exit();
