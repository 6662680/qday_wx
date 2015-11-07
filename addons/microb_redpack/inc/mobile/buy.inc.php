<?php
global $_W, $_GPC;
require_once MB_ROOT . '/source/Activity.class.php';
require_once MB_ROOT . '/source/Game.class.php';

if(false) {
    $debug = array();
    $debug['tid'] = '1';
    $debug['result'] = 'success';
    $debug['from'] = 'return';
    $debug['type'] = 'wechat';
    $this->payResult($debug);
    exit;
}

$user = $this->auth();
$id = $_GPC['actid'];
$id = intval($id);
$a = new Activity();
$activity = $a->getOne($id);
if(empty($activity) || $activity['type'] != 'game') {
    exit('访问错误');
}

$g = new Game();
$order = array();
$order['activity'] = $activity['actid'];
$order['uid'] = $user['uid'];
$tid = $g->create($order);
if(is_error($tid)) {
    exit('访问错误');
}
$trade = array();
$trade['tid'] = $tid;
$trade['title'] = "购买{$activity['tag']['label']} x1";
$trade['fee'] = $activity['tag']['price'];
$this->pay($trade);
