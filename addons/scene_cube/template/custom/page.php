<?php
/**
 * 一张独立图片存放在page thumb中
 * 场景独立参数，存在param中
 * 排序id越大
 *
 *
 */
defined('IN_IA') or exit('Access Denied');

$op=$_GPC['op'];
if($op=='sucai'){
    include $this->template($list['iden'].'/sucai');
    exit;
}
$id=intval($_GPC['id']);
if($id>0){
    $item=pdo_fetch('select * from '.tablename('scene_cube_page').' where id=:id AND list_id=:list_id AND weid=:weid',array(':weid'=>$_W['weid'],':list_id'=>$list_id,':id'=>$id));
}
if($_GPC['op']=='del'){
    if($item!=false){
        $temp=pdo_delete('scene_cube_page',array('id'=>$item['id']));
    }
    if($temp==false){
        $this->message('数据提交失败');
    }else{
        $this->message('数据提交成功',$this->createWeburl('listpage',array('list_id'=>$list_id)),'success');
    }
}
//保存数据
if($_W['ispost']){
    $insert=array(
        'weid'=>$weid,
        'list_id'=>$list_id,
        'listorder'=>intval($_GPC['listorder']),
        'm_type'=>intval($_GPC['m_type']),
        'thumb'=>$_GPC['thumb'],
    );
    if($insert['m_type']==1){
        $data=$_GPC['first'];
    }elseif($insert['m_type']==2){
        $data=$_GPC['second'];
    }elseif($insert['m_type']==3){
        $data=$_GPC['third'];
    }elseif($insert['m_type']==4){
        $data=$_GPC['map'];
    }elseif($insert['m_type']==5){
        $data=$_GPC['fifth'];
    }elseif($insert['m_type']==6){
        $data=$_GPC['sixth'];
    }elseif($insert['m_type']==7){
        $data=$_GPC['seventh'];
    }elseif($insert['m_type']==8){
        $data=$_GPC['eighth'];
    }elseif($insert['m_type']==9){
        $data=$_GPC['ninth'];
    }elseif($insert['m_type']==10){
        $data=$_GPC['tenth'];
    }

    if(!empty($data)){
        $insert['param']=iserializer($data);
    }
    if($item==false){
        $temp=pdo_insert('scene_cube_page',$insert);
    }else{
        $temp=pdo_update('scene_cube_page',$insert,array('id'=>$item['id']));
    }
    if($temp==false){
        $this->message('数据提交失败');
    }else{
        $this->message('数据提交成功',$this->createWeburl('listpage',array('list_id'=>$list_id)),'success');
    }
}

if($item==false){
    $item=array(
        'listorder'=>0,
        'thumb'=>$_W['siteroot'].'addons/scene_cube/style/img/default_bg.jpg',
    );
    $data=array(

    );
}else{
    $data=iunserializer($item['param']);
}

if(empty($data['btnimg'])){
    $data['btnimg']=$_W['siteroot'].'addons/scene_cube/style/img/default_btn.png';
}

include $this->template($list['iden'].'/page');