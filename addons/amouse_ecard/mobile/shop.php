<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-5
 * Time: 下午10:55
 * To change this template use File | Settings | File Templates.
 */
global $_W,$_GPC;
$from_user =$_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = $_GPC['id'];
$cid = $_GPC['cid'];
if($op == 'list'){
    $card = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_card')." WHERE id=".$cid." " );
    header("Location: ".$card['shopUrl']."");
    exit();
}
if($op == 'edit'){
    $card = pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_card')." WHERE id=".$cid." " );
    include $this->template('qianxian/shop_edit');
    exit();
}
if($op == 'post'){
    $shopName = $_GPC['shopName'];
    $shopIcon = $_GPC['shopIcon'];
    $shopUrl = $_GPC['shopUrl'];
    $card = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_card')." WHERE id=".$cid." " );
    $card['shopName'] = $shopName;
    $card['shopIcon'] = $shopIcon;
    $card['shopUrl'] = $shopUrl;
    pdo_update('amouse_weicard_card',$card,array('id'=>$card['id']));
    message('保存成功！',$this->createMobileUrl('Index', array('op' => 'list','weid'=>$_W['weid'],'id'=>$id,'cid'=>$cid),true), 'success');
    include $this->template('qianxian/company_edit');
}