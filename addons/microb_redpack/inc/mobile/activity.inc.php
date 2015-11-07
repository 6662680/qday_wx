<?php
global $_W, $_GPC;
$modulePublic = '../addons/microb_redpack/static/';
require_once MB_ROOT . '/source/Activity.class.php';
$forceSubscribe = true;

$user = $this->auth();

$id = $_GPC['actid'];
$id = intval($id);
$a = new Activity();
$activity = $a->getOne($id);
$prepare = $this->prepareActivity($activity, array('user' => $user));
if(is_error($prepare)) {
    $error = $prepare;
}
$footer_off = true;
$_W['page']['title'] = $activity['title'];

$_share = array();
$_share['title'] = $activity['share']['title'];
$_share['desc'] = $activity['share']['content'];
$_share['imgUrl'] = tomedia($activity['share']['image']);
$_share['link'] =  $_W['siteroot'].'app/' . substr($this->createMobileUrl('activity', array('actid'=>$activity['actid'])), 2);

$got = $a->getRecord($user['uid'], $id);
$filters = array();
$filters['activity'] = $id;
$filters['status'] = 'complete';
$recents = $a->getRecords($filters, 1, 5, $total);

if($activity['type'] == 'direct') {
    //直接发红包
    include $this->template('activity-direct');
}
if($activity['type'] == 'game') {
    //红包游戏
    require_once MB_ROOT . '/source/Game.class.php';
    $g = new Game();
    $game = $activity['tag'];
    $game['quantity'] = $g->calcQuantity($activity['actid'], $user['uid']);
    $game['already'] = !empty($got);
    include $this->template('activity-game');
}
if($activity['type'] == 'shared') {
    $_share['link'] =  $_W['siteroot'].'app/' . substr($this->createMobileUrl('shared', array('actid'=>$activity['actid'], 'owner'=>$user['uid'])), 2);
    //分享集红包
    require_once MB_ROOT . '/source/Shared.class.php';
    $s = new Shared($activity);
    $help = array();
    $help['total'] = $activity['tag']['helps'];
    $help['already'] = $s->helpsCount($user['uid']);
    $help['isok'] = $help['already'] >= $help['total'];
    $activity['tag']['progress'] = htmlspecialchars_decode($activity['tag']['progress']);
    $activity['tag']['progress'] = str_replace('{left}', $help['total'] - $help['already'], $activity['tag']['progress']);
    include $this->template('activity-shared');
}
