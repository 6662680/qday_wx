<?php
if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');
global $_GPC,$_W;

$hid=$_GPC['hid'];

$house=$this->findHouse($hid);


$list = pdo_fetchall( "select * from ".tablename($this->table_house_order)." where hid=:hid",array(":hid"=>$hid));



$tableheader = array(iconv("UTF-8", "GB2312", "姓名"),  iconv("UTF-8", "GB2312", "电话"), iconv("UTF-8", "GB2312","报名时间"));
$html = "\xEF\xBB\xBF";
foreach ($tableheader as $value) {
	$html .= $value . "\t ,";
}
$html .= "\n";
foreach ($list as $value) {
	$html .= iconv("UTF-8", "GB2312",$value['uname']) . "\t ,";
	 $html .= iconv("UTF-8", "GB2312",$value['tel']) . "\t ,";	

       
        
	$html .= date('Y-m-d H:i:s', $value['createtime']) . "\n";	
}


header("Content-type:text/csv");
header("Content-Disposition:attachment; filename=".$house['title'].".xls");

echo $html;
exit();
