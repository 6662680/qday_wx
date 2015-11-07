<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-6
 * Time: 上午10:03
 * To change this template use File | Settings | File Templates.
 */

global $_W,$_GPC;
$this->checkBrower();
$from_user =$_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = intval($_GPC['id']);
if(empty($id)){
    message("查找失败，返回重试");
}
if($op == 'erji'){
    $renmai = pdo_fetchall( " SELECT m.qianming, m.id, m.company, m.job, m.realname, m.headimg FROM ".tablename('amouse_weicard_mycollect')." AS c LEFT JOIN ".tablename('amouse_weicard_member')." AS m ON m.id=c.collect_mid WHERE c.mid=".$id." AND c.weid=".$_W['weid']." AND m.weid=".$_W['uniacid']." order by rand() LIMIT 10   " );
    $member = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_member')." WHERE id=".$id." " );
    include $this->template('qianxian/recommed_erji');
    exit();
}

if($op == 'list'){
    $renmai = pdo_fetchall( " SELECT m.qianming, m.id, m.company, m.job, m.realname, m.headimg FROM ".tablename('amouse_weicard_mycollect')." AS c LEFT JOIN ".tablename('amouse_weicard_member')." AS m ON m.id=c.collect_mid WHERE c.mid=".$id." AND c.weid=".$_W['uniacid']." AND m.weid=".$_W['uniacid']."  " );
}
//分享功能
/*$appid = $_W['account']['key'];
$secret = $_W['account']['secret'];
require_once "jssdk.php";
$jssdk = new JSSDK($appid, $secret);
$signPackage = $jssdk->GetSignPackage();*/

include $this->template('qianxian/recommed');