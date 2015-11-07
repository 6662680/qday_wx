<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-6
 * Time: 上午11:47
 * To change this template use File | Settings | File Templates.
 */

global $_W,$_GPC;
$from_user = empty($_W['openid'])?$_GPC['wid']:$_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = $_GPC['id'];
$cid = $_GPC['cid'];
$member = pdo_fetch("SELECT * FROM".tablename('amouse_weicard_member')." WHERE id=".$id);
$card = pdo_fetch("SELECT * FROM " . tablename('amouse_weicard_card')." WHERE id=".$cid);
$linkUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('share', array('id'=>$member['id'],'cid' =>$card['id'],'weid' =>$_W['uniacid']),true);
$shareimg = toimage($member['headimg']);

if($op == 'list'){
    $list = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_presence')." WHERE mid=".$id." " );
    $images=explode('|&$|',$list['img']);
    $content=explode('|&$|',$list['content']);
    $length = sizeof($images);

    include $this->template('qianxian/presence');
    exit();
}
if($op == 'edit'){

    $list = pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_presence')." WHERE mid=".$id );
  $images=explode('|&$|',$list['img']);
    $content=explode('|&$|',$list['content']);
    $length = sizeof($images);
    include $this->template('qianxian/presence_edit');
    exit();
}
if($op == 'post'){
    $data = array(
        'weid' => $_W['weid'],
        'from_user' =>$from_user,
        'mid' => $id,
        'cid' => $cid,
    );
    $data['img'] = implode('|&$|',$_GPC['headimg']);
    $data['content'] = implode('|&$|',$_GPC['content']);
    $companyId = $_GPC['companyId'];
    if(!empty($companyId)){
        $data['id'] = $companyId;
        pdo_update('amouse_weicard_presence',$data,array('id'=>$companyId));
        message('保存成功',$this->createMobileUrl('indexEdit',array('id'=>$id,'cid'=>$cid)));
    }
    $flag = pdo_insert('amouse_weicard_presence',$data);
    if($flag == false){
        message('保存失败，请返回重试');
    }else{
        message('保存成功',$this->createMobileUrl('index',array('id'=>$id,'cid'=>$cid,'openid'=>$member[openid])));
    }
}