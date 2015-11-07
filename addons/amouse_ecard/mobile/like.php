s<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-6
 * Time: 下午12:50
 * To change this template use File | Settings | File Templates.
 */

global $_W,$_GPC;
//$this->checkBrower();
$from_user = $_W['openid'];
//检测是否已关注，已关注是否已创建名片,未关注跳转到关注链接
$my_cid = pdo_fetchcolumn( " SELECT id FROM ".tablename('amouse_weicard_card')." WHERE from_user='".$from_user."' AND weid=".$_W['uniacid'] );
if(empty($from_user) || empty($my_cid) ){
    $guanzhuUrl = pdo_fetchcolumn( " SELECT guanzhuUrl FROM ".tablename('amouse_weicard_setting')." WHERE weid=".$_W['uniacid']  );
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
    $member = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_member')." WHERE id=".$mid." " );
    $zan_mid = $mid;
    $zan_cid = $cid;
    $collect_from_user = $member['from_user'];
    $my_mid = pdo_fetchcolumn( " SELECT id FROM ".tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid'] );
    $card = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_card')." WHERE id=".$cid." " );

    //检测是否已经点赞过
    $iscollect = pdo_fetchcolumn( " SELECT id FROM ".tablename('amouse_weicard_zan')." WHERE mid=".$my_mid." AND zan_mid=".$zan_mid );
    if(!empty($iscollect)){
        $ret = array(
            'success' => true,
            'status' => 3,
            'guanzhuUrl' => $guanzhuUrl,
            'zan' => $card['zan'],
        );
        echo json_encode($ret);
        exit();
    }

    $insert = array(
        'weid' => $_W['uniacid'],
        'mid' => $my_mid,
        'cid' => $my_cid,
        'from_user' => $from_user,
        'zan_mid' => $zan_mid,
        'zan_cid' => $zan_cid,
        'zan_from_user' => $member['from_user'],
        'createtime' => TIMESTAMP,
    );
    $flag = pdo_insert('amouse_weicard_zan',$insert);
    //点赞成功后到crad表去给点赞数量+1
    $card['zan'] = intval($card['zan']) +1;
    pdo_update('amouse_weicard_card',$card,array('id'=>$card['id']));
    if($flag == false){
        $ret = array(
            'success' => true,
            'status' => 2,
            'guanzhuUrl' => $guanzhuUrl,
            'zan' => $card['zan'],
        );
        echo json_encode($ret);
        exit();
    }
    else{
        $ret = array(
            'success' => true,
            'status' => 1,
            'guanzhuUrl' => $guanzhuUrl,
            'zan' => $card['zan'],
        );
        echo json_encode($ret);
        exit();
    }
}