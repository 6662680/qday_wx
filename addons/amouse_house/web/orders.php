<?php
/*
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 *
 *  @auth shizhongying qq:214983937
 */

global $_W, $_GPC;
$weid= $_W['uniacid'];
load()->func('tpl');
$op= !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$meals= pdo_fetchall('SELECT * FROM '.tablename('heixiu_car_meal')."  ORDER BY id DESC");
$activitys= pdo_fetchall('SELECT * FROM '.tablename('heixiu_car_activity')." WHERE weid=:weid  ORDER BY id DESC",array(':weid'=>$weid));
$brands= pdo_fetchall("SELECT * FROM ".tablename('heixiu_car_brand'));

if($op == 'display') { //列表显示

    $starttime= empty($_GPC['date']['start']) ? strtotime('-1 month') : strtotime($_GPC['date']['start']);
    $endtime= empty($_GPC['date']['end']) ? TIMESTAMP : strtotime($_GPC['date']['end']) + 86399*2;
    $pindex= max(1, intval($_GPC['page']));
    $psize= 20;

    $condition= " WHERE a.weid=:weid AND a.createtime>=:starttime AND a.createtime<=:endtime";
    $paras= array(':weid' => $weid, ':starttime' => $starttime, ':endtime' => $endtime);

    $status = $_GPC['status'];
    if ($status != '') {
        $condition .= " AND a.status = '" . intval($status) . "'";
    }

    if(!empty($_GPC['username'])) {
        $condition .= " AND username LIKE '%".$_GPC['username']."%'";
    }
    if(!empty($_GPC['mobile'])) {
        $mobile =  $_GPC['mobile'] ;
        $condition .= " AND a.mobile = '{$mobile}'";
    }
    if(!empty($_GPC['address'])) {
        $condition .= " AND a.address LIKE '%".$_GPC['address']."%'";
    }

    if (!empty($_GPC['brandid'])) {
        $brandid = intval($_GPC['brandid']);
        $condition .= " AND a.brandid = '{$brandid}'";
    }
    if (!empty($_GPC['seriesid'])) {
        $cid = intval($_GPC['seriesid']);
        $condition .= " AND a.seriesid = '{$cid}'";
    }
    if (!empty($_GPC['yearsid'])) {
        $yearsid = intval($_GPC['yearsid']);
        $condition .= " AND a.styleid = '{$yearsid}'";
    }
    if (!empty($_GPC['aid'])) {
        $aid = intval($_GPC['aid']);
        $condition .= " AND a.aid = '{$aid}'";
    }

    if(!empty($_GPC['ordersn'])) {
        $ordersn= intval($_GPC['ordersn']);
        $condition .= " AND a.ordersn = :ordersn";
        $paras[':ordersn']= intval($ordersn);
    }
    if(!empty($status)) {
        $condition .= " AND a.status = :status";
        $paras[':status']= intval($status);
    }


    $list= pdo_fetchall("SELECT a.id,a.ordersn,a.username,a.mobile,a.appointmenttime,a.timezone,a.status,a.carprefix,a.carNo,a.address,a.brandid,a.seriesid,a.styleid,b.openid,a.aid FROM ".tablename('heixiu_car_order')."AS a LEFT JOIN" . tablename('mc_mapping_fans') ." AS b ON a.openid = b.openid".$condition." ORDER BY status DESC, createtime DESC LIMIT ".($pindex -1) * $psize.','.$psize, $paras);
    $total= pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename('heixiu_car_order')."AS a LEFT JOIN" . tablename('mc_mapping_fans') ." AS b ON a.openid=b.openid ".$condition, $paras);
    $pager= pagination($total, $pindex, $psize);
    $orders = array();
    foreach ($list as &$item) {
        $btitle= pdo_fetchcolumn('SELECT title FROM '.tablename('heixiu_car_brand')." WHERE id=:bid ORDER BY title ASC ", array(':bid' => $item['brandid']));
        $serietitle= pdo_fetchcolumn('SELECT title FROM '.tablename('heixiu_car_series')." WHERE id=:sid ORDER BY title DESC ", array(':sid' => $item['seriesid']));
        $styletitle= pdo_fetchcolumn('SELECT title FROM '.tablename('heixiu_car_style')." WHERE id=:id ORDER BY title DESC", array(':id' => $item['styleid']));
        $atitle= pdo_fetchcolumn('SELECT title FROM '.tablename('heixiu_car_activity')." WHERE id=:id ORDER BY title DESC", array(':id' => $item['aid']));
        $item['btitle'] =$btitle;
        $item['serietitle']=$serietitle;
        $item['styletitle']=$styletitle;
        $item['atitle']=$atitle;

        $orders[] = $item;
    }
    unset($item);
}elseif($op == 'detail') {

    $ret= getWeekRange2(date('Y-m-d', time()));
    $id= intval($_GPC['id']);
    $item= pdo_fetch("SELECT * FROM ".tablename('heixiu_car_order')." WHERE id = :id", array(':id' => $id));
    if(empty($item)) {
        message("抱歉，订单不存在!", referer(), "error");
    }
    $meal= pdo_fetch('SELECT title,content,type FROM '.tablename('heixiu_car_meal')." WHERE id=:id  ORDER BY id DESC", array(':id' => $item['mealid']));

    $brandtitle = pdo_fetchcolumn('SELECT title FROM '.tablename('heixiu_car_brand')." WHERE id=:id ", array(':id' =>$item['brandid']));

    $series = pdo_fetchcolumn('SELECT  title FROM '.tablename('heixiu_car_series')." WHERE id=:id ", array(':id' =>$item['seriesid']));

    $style= pdo_fetchcolumn('SELECT title FROM '.tablename('heixiu_car_style')." WHERE id=:id ", array(':id' =>$item['styleid']));
    $uid = pdo_fetchcolumn('SELECT uid FROM ' .tablename('mc_mapping_fans') . 'WHERE openid = :openid limit 1',array(':openid'=>$item['openid']));
    $nicknames = pdo_fetch("SELECT nickname FROM " . tablename('mc_members') . " WHERE `uid` = :uid limit 1", array(':uid' => $uid));

    $activity= pdo_fetch('SELECT title,content,price FROM '.tablename('heixiu_car_activity')." WHERE id=:id ORDER BY title DESC", array(':id' => $item['aid']));

    if(checksubmit('finish')) {
        $appointmenttime= $_GPC['appointmenttime'];
        $jsname= $_GPC['jsname'];
        $timezone= $_GPC['timezone'];
        pdo_update('heixiu_car_order', array('status' => 1, 'appointmenttime' => $appointmenttime, 'timezone' => $timezone, 'jsname' => $jsname), array('id' => $id));
        message('订单操作成功！', $this->createWebUrl('orders', array('op' => 'display', 'name' => 'zqwyx_heixiu')), 'success');
    }

    if(checksubmit('cancelpay')) {
        pdo_update('heixiu_car_order', array('status' => 0), array('id' => $id));
        message('取消订单付款操作成功！', $this->createWebUrl('orders', array('op' => 'display', 'name' => 'zqwyx_heixiu')), 'success');
    }
    if(checksubmit('close')) {
        pdo_update('heixiu_car_order', array('status' => -1), array('id' => $id));
        message('订单关闭操作成功！', $this->createWebUrl('orders', array('op' => 'display', 'name' => 'zqwyx_heixiu')), 'success');
    }
}elseif($op == 'post') {
    $id= intval($_GPC['id']);
    pdo_update('heixiu_car_order', array('jsname' => trim($_GPC['jsname'])), array('id' => $id));
    $result= array('success' => true, 'message' => '技师名称更新成功！');
    die(json_encode($result));
    //message('技师名称更新成功！', $this->createWebUrl('orders',array('op' =>'display')), 'success');
}elseif($op == 'del') { //删除
    if(isset($_GPC['delete'])) {
        $ids= implode(",", $_GPC['delete']);
        $sqls= "delete from  ".tablename('heixiu_car_order')."  where id in(".$ids.")";
        pdo_query($sqls);
        message('删除成功！', referer(), 'success');
    }
    $id= intval($_GPC['id']);
    $temp= pdo_delete("heixiu_car_order", array("weid" => $_W['uniacid'], 'id' => $id));
    message('删除数据成功！', $this->createWebUrl('orders', array('op' => 'display', 'name' => 'zqwyx_heixiu')), 'success');
}
include $this->template('web/orders');
?>
