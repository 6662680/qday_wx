<?php
/**
 * 一张独立图片存放在page thumb中
 * 场景独立参数，存在param中
 * 排序id越大
 *
 *
 */

defined('IN_IA') or exit('Access Denied');

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
    if(isset($this->typeArr[$insert['m_type']])){
        $data=$_GPC[$this->typeArr[$insert['m_type']]['type']];
    }

    if(!empty($data)){
        $insert['param']=iserializer($data);
    }
    if($item==false){
        $temp=pdo_insert('scene_cube_page',$insert);
    }else{
        $temp=pdo_update('scene_cube_page',$insert,array('id'=>$item['id']));
    }
    if($temp===false){
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