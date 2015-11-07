<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-5
 * Time: 下午11:31
 * To change this template use File | Settings | File Templates.
 */

global $_W,$_GPC;
//	$this->checkBrower();
$from_user = empty($_W['openid'])?$_GPC['wid']:$_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = intval($_GPC['id']);
$cid = intval($_GPC['cid']);
$member = pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid']." " );
$card = pdo_fetch(" SELECT * FROM ".tablename('amouse_weicard_card')." WHERE from_user='".$_W['openid']."' AND weid=".$_W['uniacid']." ");



include $this->template('qianxian/index_edit');