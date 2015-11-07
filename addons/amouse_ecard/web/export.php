<?php
header("Content-type:text/csv;");
header("Content-Disposition:attachment;filename=weicard.csv");
header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
header('Expires:0');   
header('Pragma:public');
$weid=$_W['uniacid'];
$result = pdo_fetchAll("SELECT * FROM ".tablename('amouse_weicard_member')." WHERE  weid=".$weid." ORDER BY id DESC ");
$title= "名字".","."公司".","."职务".","."部门".","."电话".","."时间"."\n";

if(isWin()){
	echo iconv('utf-8', 'gb2312', $title);
	foreach ($result as $key => $value) {
		$temp =$value['realname']
		.",".$value['company']
        .",".$value['job']
        .",".$value['department']
		.",".$value['mobile']
        .",".date("Y-m-d G:i",$value['createtime'])."\n";
		echo iconv('utf-8', 'gb2312', $temp);
	}
}else{
	echo $title;
	foreach ($result as $key => $value) {
		echo $value['realname'].",";
		echo $value['company'].",";
		echo $value['job'].",";
		echo $value['department'].",";
        echo $value['mobile'].",";
		echo date("Y-m-d G:i",$value['createtime'])."\n";
	}
}

function isWin(){
     global $_SERVER;
     $agent = $_SERVER['HTTP_USER_AGENT'];
     $os = false;
     if (eregi('win', $agent)){ 
         $os = "win";
     }else if (eregi('teleport', $agent)){
         $os = 'teleport';
     }else if (eregi('flashget', $agent)){ 
         $os = 'flashget';
     }else if (eregi('webzip', $agent)){
         $os = 'webzip';
     }else if (eregi('offline', $agent)){ 
         $os = 'offline';
     }else {
     }
     return $os;
}

?>