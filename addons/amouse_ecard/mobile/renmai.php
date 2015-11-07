<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-6
 * Time: 上午9:51
 * To change this template use File | Settings | File Templates.
 */

global $_W,$_GPC;
$from_user = $_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = $_GPC['id'];
$cid = $_GPC['cid'];
if($op == 'list'){
    $renmai = pdo_fetchall( " SELECT m.qianming, m.id, m.company, m.job, m.realname, m.headimg FROM ".tablename('amouse_weicard_mycollect')." AS c LEFT JOIN ".tablename('amouse_weicard_member')." AS m ON m.id=c.collect_mid WHERE c.mid=".$id." AND c.weid=".$_W['uniacid']." AND m.weid=".$_W['uniacid']);

}
if($op == 'mycollect'){
    $renmai = pdo_fetchall( " SELECT m.qianming, m.id, m.company, m.job, m.realname, m.headimg FROM ".tablename('amouse_weicard_mycollect')." AS c LEFT JOIN ".tablename('amouse_weicard_member')." AS m ON m.id=c.mid WHERE c.collect_mid=".$id."  AND c.weid=".$_W['uniacid']." AND m.weid=".$_W['uniacid'] );
}

$renmaiList = array();
foreach($renmai as $k=>$v){
    $renmai[$k]['zimu'] = $this->getfirstchar($v['realname']);
    $length = sizeof($renmaiList);
}
$renmaiList = $this->sysSortArray($renmai,"zimu","SORT_ASC","company","SORT_DESC","realname","SORT_ASC","SORT_STRING");

include $this->template('qianxian/renmai');