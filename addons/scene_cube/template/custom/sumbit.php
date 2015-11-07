<?php
/**
 * 一张独立图片存放在page thumb中
 * 场景独立参数，存在param中
 * 排序id越大
 *
 *
 */
defined('IN_IA') or exit('Access Denied');

/*
if(empty($_W['fans'])){
	$return=array(
		'data'=>200,
		'success'=>false,
		'message'=>'提交数据，无会员信息'
	);
	die(json_encode($return));
}*/
$insert=array(
    'weid'=>$_W['weid'],
    'list_id'=>$id,
    'from_user'=>$_W['fans']['from_user'],
    'create_time'=>time(),
);
$insert['setting'] = array();
foreach($_GPC AS $k=>$v) {
	if (substr($k,0,2) == "k_") {
		if ($_GPC['k'.$k]){
			$insert['setting'][$_GPC['k'.$k]] = $v;
		}else{
			$insert['setting'][$k] = $v;
		}
	}
}
$insert['setting'] = json_encode($insert['setting']);

$book=pdo_fetch("select * from ".tablename('scene_cube_book')." where weid=:weid AND list_id=:list_id AND from_user=:from_user AND status=0",array(':weid'=>$_W['weid'],':list_id'=>$id,':from_user'=>$_W['fans']['from_user']));
if($book==false){
    $temp=pdo_insert('scene_cube_book',$insert);
}else{
    $temp=pdo_update('scene_cube_book',$insert,array('id'=>$book['id']));
}
if($temp==false){
    $return=array(
        'data'=>200,
        'success'=>false,
        'message'=>'数据提交失败'
    );
    die(json_encode($return));

}else{
    if($_GPC['isyuyue']==0){
        pdo_update('scene_cube_list',array('isyuyue'=>1),array('id'=>$_GPC['id']));
    }
    $return=array(
        'data'=>200,
        'success'=>true,
        'message'=>'数据提交成功'
    );
    die(json_encode($return));
}
