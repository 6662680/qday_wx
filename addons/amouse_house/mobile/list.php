<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-3-13
 * Time: 下午5:09
 * To change this template use File | Settings | File Templates.
 */
$pindex= max(1, intval($_GPC['page']));
$psize= 20;
$weid=$_W['uniacid'];
$condition= " WHERE weid='{$weid}' ";
$list= pdo_fetchall('SELECT * FROM '.tablename('amouse_newflats')." $condition ORDER BY createtime DESC LIMIT ".($pindex -1) * $psize.','.$psize); //分页
$total= pdo_fetchcolumn('SELECT COUNT(id) FROM '.tablename('amouse_newflats').$condition);
//$pager = cpagination($total, $pindex, $psize);
$pager = pagination($total, $pindex, $psize);
$pageend=ceil($total/$psize);
if($total/$psize!=0 && $total>=$psize){
    $pageend++;
}
//$url =   $_W['siteroot']."app/".substr($this->createMobileUrl('index'),2);
include $this->template('house/new_house_list');