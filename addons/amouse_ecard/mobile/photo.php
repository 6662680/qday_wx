<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-6
 * Time: 下午12:13
 * To change this template use File | Settings | File Templates.
 */
global $_W,$_GPC;
//$this->checkBrower();
$from_user = empty($_W['openid'])?$_GPC['wid']:$_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = $_GPC['id'];
$cid = $_GPC['cid'];
if($op == 'list'){
    $list = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_photo')." WHERE mid=".$id );
    $card = pdo_fetch( " SELECT bgimg FROM ".tablename('amouse_weicard_card')." WHERE mid=".$id );
    $thumb=explode('|',$list['thumb']);
    include $this->template('qianxian/photo');
    exit();
}
if($op == 'edit'){
    $list = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_photo')." WHERE mid=".$id );
    $thumb=explode('|',$list['thumb']);
    include $this->template('qianxian/photo_edit');
    exit();
}
if($op == 'post'){
    $data = array(
        'weid' => $_W['weid'],
        'title' => $_GPC['title'],
        'icon' => $_GPC['icon'],
        'mid' => $id,
        'cid' => $cid,
        'from_user' =>$from_user,
    );
    $data['thumb'] = implode('|',$_GPC['headimg']);
    $photoId = $_GPC['photoId'];
    if(!empty($photoId)){
        $data['id'] = $photoId;
        pdo_update('amouse_weicard_photo',$data,array('id'=>$data['id']));
        message('保存成功！',$this->createMobileUrl('indexEdit',array('op' =>'list','weid'=>$_W['uniacid'],'id'=>$id,'cid'=>$cid),true), 'success');
    }else{
        $flag = pdo_insert('amouse_weicard_photo',$data);
        if($flag == false){
            message('保存失败，请返回重试');
        }else{
            message('保存成功！',$this->createMobileUrl('indexEdit', array('op' => 'list','weid'=>$_W['uniacid'],'id'=>$id,'cid'=>$cid),true), 'success');
        }
    }
}
include $this->template('qianxian/photo_edit');