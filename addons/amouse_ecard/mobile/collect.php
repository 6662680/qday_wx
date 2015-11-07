<?php
/**
 * Created by IntelliJ IDEA.
 * User: shizhongying QQ:214983937
 * Date: 15-4-6
 * Time: 下午12:42
 * To change this template use File | Settings | File Templates.
 */
global $_W,$_GPC;
$from_user = $_W['openid'];
//检测是否已关注，已关注是否已创建名片,未关注跳转到关注链接
$my_cid = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_card')." WHERE from_user='".$from_user."' AND weid=".$_W['uniacid']);
if(empty($from_user) || empty($my_cid) ){
    $guanzhuUrl=pdo_fetchcolumn("SELECT guanzhuUrl FROM ".tablename('amouse_weicard_sysset')." WHERE weid=".$_W['uniacid']);
    $ret = array(
        'success' => true,
        'status' => 0,
        'guanzhuUrl' => $guanzhuUrl,
    );
    echo json_encode($ret);
    exit();
}else{
    $mid = $_GPC['mid'];
    $cid = $_GPC['cid'];
    $member = pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_member')." WHERE id=".$mid." " );
    $collect_mid = $mid;
    $collect_cid = $cid;
    $collect_from_user=$member['from_user'];
    $my_mid = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid']);

    //检测是否已经收藏过
    $iscollect = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_mycollect')." WHERE mid=".$my_mid." AND collect_mid=".$collect_mid);
    if(!empty($iscollect)){
        $ret = array(
            'success' => true,
            'status' => 3,
            'guanzhuUrl' => $guanzhuUrl,
        );
        echo json_encode($ret);
        exit();
    }

    $insert = array(
        'weid' => $_W['weid'],
        'mid' => $my_mid,
        'cid' => $my_cid,
        'from_user' => $from_user,
        'collect_mid' => $collect_mid,
        'collect_cid' => $collect_cid,
        'collect_from_user' => $member['from_user'],
        'createtime' => TIMESTAMP,
    );
    $flag = pdo_insert('amouse_weicard_mycollect',$insert);
    if($flag == false){
        $ret = array(
            'success' => true,
            'status' => 2,
            'guanzhuUrl' => $guanzhuUrl,
        );
        echo json_encode($ret);
        exit();
    }
    else{
        $ret = array(
            'success' => true,
            'status' => 1,
            'guanzhuUrl' => $guanzhuUrl,
        );
        echo json_encode($ret);
        exit();
    }

}