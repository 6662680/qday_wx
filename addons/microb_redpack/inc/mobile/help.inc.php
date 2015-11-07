<?php
global $_W, $_GPC;

require_once MB_ROOT . '/source/Shared.class.php';
require_once MB_ROOT . '/source/Fans.class.php';
$f = new Fans();
$s = new Shared();

$uid = intval($_GPC['owner']);
$owner = $f->getOne($uid);
if(empty($owner)) {
    message('访问错误', $activity['guide'], 'info');
}

$user = $this->auth();
$activity = $this->getActivity(true, array('user' => $user));
if(is_error($activity)) {
    message($activity['message']);
}
if($owner['uid'] == $user['uid']) {
    exit('不能帮给自己发红包啊');
} else {
    $rank = $s->getHelpRank($owner['uid'], $user['uid']);
    if(!empty($rank)) {
        exit('您已经向这个好友发过红包啦');
    } else {
        $rec = array();
        $rec['from'] = $owner['uid'];
        $rec['to'] = $user['uid'];
        $rec['dateline'] = TIMESTAMP;
        $s->createHelp($rec);
        exit('success');
    }
}
