<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-5
 * Time: 下午11:22
 * To change this template use File | Settings | File Templates.
 */

global $_W,$_GPC;
$from_user = $_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = intval($_GPC['id']);
if($op == 'list'){
    $mid=intval($_GPC['id']);
    if(empty($id)) message('您要编辑的资料不存在！');
    $card = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_card')." WHERE mid=".$mid." " );
}
if($op == 'ajax'){
    $mid = intval($_GPC['id']);
    $type = intval($_GPC['type']);
    $visible = $_GPC['visible'];
    $card = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_card')." WHERE mid=".$mid." " );
    if($type == 1){
        $card['mobile'] = $visible;
    }
    if($type == 2){
        $card['email'] = $visible;
    }
    if($type == 3){
        $card['weixin'] = $visible;
    }
    if($type == 4){
        $card['address'] = $visible;
    }if($type == 5){
        $card['qq'] = $visible;
    }
    $flag = pdo_update('amouse_weicard_card',$card,array('id'=>$card['id']));
    $ret = array(
        'success' => true,
        'desc' => null,
        'data' => null,
        'type' =>0
    );
    echo json_encode($ret);
    exit();
}
include $this->template('qianxian/setting');