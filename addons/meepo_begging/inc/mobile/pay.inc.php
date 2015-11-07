<?php
global $_W,$_GPC;
$uid = $this->checkauth();

$begid = $_GPC['begid'];
$sql = "SELECT * FROM ".tablename('meepo_begging_user')." WHERE id = :id limit 1";
$params = array(':id'=>$begid);
$begging = pdo_fetch($sql,$params);

$parmas = array();
$params['tid'] = $begid;
$params['user'] = $_W['openid'];
$params['fee'] = floatval($begging['money']);
$params['title'] = '一分也是爱，不要嫌少';
$params['ordersn'] = 'MEEPO_BEGGING_'.random(5,1);
$params['virtual'] = true;

$this->pay($params);