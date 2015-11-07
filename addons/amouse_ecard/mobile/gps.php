<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-6
 * Time: 上午8:49
 * To change this template use File | Settings | File Templates.
 */

global $_W,$_GPC;
$this->checkBrower();
$from_user = $_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = $_GPC['id'];
$cid = $_GPC['cid'];
if($op == 'list'){
    $member = pdo_fetch(" SELECT * FROM ".tablename('amouse_weicard_member')." WHERE id=".$id );
    include $this->template('gps');
}