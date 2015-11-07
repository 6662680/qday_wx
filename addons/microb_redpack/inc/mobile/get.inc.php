<?php
global $_W, $_GPC;
$modulePublic = '../addons/microb_redpack/static/';

$footer_off = true;
$_W['page']['title'] = $activity['title'];

$user = $this->auth();
$activity = $this->getActivity(true, array('user' => $user));
if(is_error($activity)) {
    message($activity['message']);
}

$_share = array();
$_share['title'] = $activity['stitle'];
$_share['desc'] = $activity['content'];
$_share['imgUrl'] = tomedia($activity['image']);
$_share['link'] =  $_W['siteroot'].'app/' . substr(substr($this->createMobileUrl('entry', array('owner'=>$user['uid'])), 2), 0, -39);

require_once MB_ROOT . '/source/Shared.class.php';
$s = new Shared();

$got = $s->getOneRecord($user['uid']);

if($activity['type'] == 'direct') {
    //直接发红包
} else {
    //分享集红包
    $help = array();
    $help['total'] = $activity['helps'];
    $help['already'] = $s->helpsCount($user['uid']);
    $help['ok'] = $help['already'] >= $activity['helps'];
}
include $this->template('get');
