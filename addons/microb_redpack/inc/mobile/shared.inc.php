<?php
global $_W, $_GPC;
$modulePublic = '../addons/microb_redpack/static/';
require_once MB_ROOT . '/source/Activity.class.php';
require_once MB_ROOT . '/source/Fans.class.php';
require_once MB_ROOT . '/source/Shared.class.php';
$a = new Activity();
$f = new Fans();

$user = $this->auth();

$id = $_GPC['actid'];
$id = intval($id);
$activity = $a->getOne($id);
$prepare = $this->prepareActivity($activity, array('user' => $user));
if(is_error($prepare)) {
    header('Location: ' . $this->createMobileUrl('activity', array('actid'=>$activity['actid'])));
    exit;
}
$uid = intval($_GPC['owner']);
$owner = $f->getOne($uid);
if(empty($owner)) {
    message('访问错误', $activity['guide'], 'info');
}
if($owner['uid'] == $user['uid']) {
    header('Location: ' . $this->createMobileUrl('activity', array('actid'=>$activity['actid'])));
    exit;
}
require_once MB_ROOT . '/source/Shared.class.php';
$s = new Shared($activity);

if($_W['ispost']) {
    $input = array();
    $input['owner'] = $owner['uid'];
    $input['helper'] = $user['uid'];
    $input['dateline'] = TIMESTAMP;
    $ret = $s->createHelp($input);
    if(is_error($ret)) {
        exit($ret['message']);
    } else {
        exit('success');
    }
}

$footer_off = true;
$_W['page']['title'] = $activity['title'];

$_share = array();
$_share['title'] = $activity['share']['title'];
$_share['desc'] = $activity['share']['content'];
$_share['imgUrl'] = tomedia($activity['share']['image']);
$_share['link'] =  $_W['siteroot'].'app/' . substr($this->createMobileUrl('shared', array('actid'=>$activity['actid'], 'owner'=>$user['uid'])), 2);

$got = $a->getRecord($user['uid'], $id);

//分享集红包
$help = array();
$help['total'] = $activity['tag']['helps'];
$help['already'] = $s->helpsCount($owner['uid']);
$help['isok'] = $help['already'] >= $help['total'];
$help['rank'] = $s->getHelpRank($owner['uid'], $user['uid']);
$activity['tag']['label'] = htmlspecialchars_decode($activity['tag']['label']);
$activity['tag']['label'] = str_replace('{nickname}', $owner['nickname'], $activity['tag']['label']);
$activity['tag']['request'] = htmlspecialchars_decode($activity['tag']['request']);
$activity['tag']['request'] = str_replace('{nickname}', $owner['nickname'], $activity['tag']['request']);
include $this->template('activity-shared-help');
