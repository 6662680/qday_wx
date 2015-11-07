<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-5
 * Time: 下午11:32
 * To change this template use File | Settings | File Templates.
 */

global $_W,$_GPC;
$from_user = $_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = $_GPC['id'];
$cid = $_GPC['cid'];
$member = pdo_fetch("SELECT * FROM".tablename('amouse_weicard_member')." WHERE id=".$id);
$card = pdo_fetch("SELECT * FROM " . tablename('amouse_weicard_card')." WHERE id=".$cid);
$linkUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('share', array('id'=>$member['id'],'cid' =>$card['id'],'weid' =>$_W['uniacid']),true);
$shareimg = toimage($member['headimg']);

if($op == 'list'){
    $list = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_companyinfo')." WHERE mid=".$id." " );
    $images=explode('|&$|',$list['img']);
    $content=explode('|&$|',$list['content']);
    $length = sizeof($images);
    include $this->template('qianxian/company_list');
    exit();
}
if($op == 'edit'){
    $list = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_companyinfo')." WHERE mid=".$id." " );
    $images=explode('|&$|',$list['img']);
    $content=explode('|&$|',$list['content']);
    $length = sizeof($images);
    include $this->template('qianxian/company_edit');
    exit();
}
if($op == 'post'){
    $data = array(
        'weid' => $_W['uniacid'],
        'from_user' =>$from_user,
        'mid' => $id,
        'cid' => $cid,
    );
    $data['img'] = implode('|&$|',$_GPC['headimg']);
    $data['content'] = implode('|&$|',$_GPC['content']);
    $companyId = $_GPC['companyId'];
    if(!empty($companyId)){
        $data['id'] = $companyId;
        pdo_update('amouse_weicard_companyinfo',$data,array('id'=>$companyId));
        message('保存成功',$this->createMobileUrl('indexEdit',array('id'=>$id,'cid'=>$cid),true));
    }
    $flag = pdo_insert('amouse_weicard_companyinfo',$data);
    if($flag == false){
        message('保存失败，请返回重试');
    }else{
        message('保存成功',$this->createMobileUrl('indexEdit',array('id'=>$id,'cid'=>$cid,'wid'=>$from_user),true));
    }
}