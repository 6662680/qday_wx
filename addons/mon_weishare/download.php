<?php
if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');
global $_GPC,$_W;
$sid= intval($_GPC['sid']);
if(empty($sid)){
    message('抱歉，传递的参数错误！','', 'error');              
}

  $params = array(':sid' => $sid);
  $list = pdo_fetchall("SELECT * FROM " . tablename('weishare_user') . " WHERE sid = :sid  ORDER BY income DESC ", $params);
 
     
$tableheader = array('openid',  iconv("UTF-8", "GB2312", '手机号' ),iconv("UTF-8", "GB2312", '积分' ),iconv("UTF-8", "GB2312", '助力次数' ) ,iconv("UTF-8", "GB2312", '注册时间' ));
$html = "\xEF\xBB\xBF";
foreach ($tableheader as $value) {
	$html .= $value . "\t ,";
}
$html .= "\n";
foreach ($list as $value) {
	$html .= $value['from_user'] . "\t ,";
	 $html .= $value['tel'] . "\t ,";	
	$html .= $value['income'] . "\t ,";	
	$html .= $value['helpcount'] . "\t ,";	
	
        
	$html .= date('Y-m-d H:i:s', $value['createtime']) . "\n";	
}


header("Content-type:text/csv");
header("Content-Disposition:attachment; filename=全部数据.xls");

echo $html;
exit();
