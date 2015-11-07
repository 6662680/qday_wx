<?php
global $_W, $_GPC;
$modulePublic = '../addons/microb_redpack/static/';
$footer_off = true;
$_W['page']['title'] = $activity['title'];

require_once MB_ROOT . '/source/Shared.class.php';
require_once MB_ROOT . '/source/Fans.class.php';
$f = new Fans();
$s = new Shared();

$uid = intval($_GPC['owner']);
$owner = $f->getOne($uid);
if(empty($owner)) {
    message('访问错误', $activity['guide'], 'info');
}

$got = $s->getOneRecord($owner['uid']);

$user = $this->auth();
$activity = $this->getActivity(true, array('user' => $user));
if(is_error($activity)) {
    message($activity['message']);
}
if($owner['uid'] == $user['uid']) {
    header('Location: ' . $this->createMobileUrl('get'));
    exit;
}

$_share = array();
$_share['title'] = $activity['stitle'];
$_share['desc'] = $activity['content'];
$_share['imgUrl'] = tomedia($activity['image']);
$_share['link'] =  $_W['siteroot'].'app/' . substr(substr($this->createMobileUrl('entry', array('owner'=>$user['uid'])), 2), 0, -39);

if($activity['type'] == 'direct') {
    //直接发红包
} else {
    //分享集红包
    $help = array();
    $help['total'] = $activity['helps'];
    $help['already'] = $s->helpsCount($owner['uid']);
    $help['ok'] = $help['already'] >= $activity['helps'];
    $help['rank'] = $s->getHelpRank($owner['uid'], $user['uid']);
    
    $activity['label'] = str_replace('{nickname}', $owner['nickname'], $activity['label']);
}
include $this->template('entry');
