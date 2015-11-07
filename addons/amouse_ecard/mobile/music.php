<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-6
 * Time: 上午10:57
 * To change this template use File | Settings | File Templates.
 */
global $_W,$_GPC;
//$this->checkBrower();
$from_user =  $_W['openid'];
$id = $_GPC['id'];
$cid=$_GPC['cid'];

$musics=pdo_fetchall("SELECT * FROM ".tablename('amouse_weicard_music')." WHERE weid=".$_W['uniacid']);

include $this->template('qianxian/music');
