<?php
global $_W, $_GPC;
require_once MB_ROOT . '/source/Activity.class.php';
$user = $this->auth();

$id = $_GPC['actid'];
$id = intval($id);
$a = new Activity();
$activity = $a->getOne($id);
$prepare = $this->prepareActivity($activity, array('user' => $user));
if(is_error($prepare)) {
    $error = $prepare;
    if($error['errno'] != '-2') {
        exit($error['message']);
    }
}
if(!$this->checkSubscribe()) {
    exit('请关注我们后参加活动');
}

if($activity['type'] == 'direct') {
    $got = $a->getRecord($user['uid'], $activity['actid']);
    if(empty($got)) {
        if(empty($error)) {
            $ret = $a->grap($user, $activity);
            if(is_error($ret)) {
                exit($ret['message']);
            } elseif ($ret['status'] == 'none') {
                exit('这一轮没有抢到红包, 请期待我们下一次活动');
            } else {
                $got = $a->getRecord($user['uid'], $activity['actid']);
                $ret = $this->send($activity, $got, $user);
                if(is_error($ret)) {
                    exit('红包发放失败, 你可以在活动结束之前重新领取. 活动结束后无法领取, 请注意');
                    exit($ret['message']);
                }
                exit('success');
            }
        }
    } else {
        if($got['status'] == 'created') {
            $ret = $this->send($activity, $got, $user);
            if(is_error($ret)) {
                exit('红包发放失败, 你可以在活动结束之前重新领取. 活动结束后无法领取, 请注意');
                exit($ret['message']);
            }
            exit('success');
        }
    }
}

if($activity['type'] == 'shared') {
    require_once MB_ROOT . '/source/Shared.class.php';
    $s = new Shared($activity);
    $count = $s->helpsCount($user['uid']);
    if($count < $activity['tag']['helps']) {
        exit('还没达到领取礼品的条件');
    }
    $got = $a->getRecord($user['uid'], $activity['actid']);
    if(empty($got)) {
        if(empty($error)) {
            $ret = $a->grap($user, $activity);
            if(is_error($ret)) {
                exit($ret['message']);
            } elseif ($ret['status'] == 'none') {
                exit('这一轮没有抢到红包, 请期待我们下一次活动');
            } else {
                $got = $a->getRecord($user['uid'], $activity['actid']);
                $ret = $this->send($activity, $got, $user);
                if(is_error($ret)) {
                    exit('红包发放失败, 你可以在活动结束之前重新领取. 活动结束后无法领取, 请注意');
                    exit($ret['message']);
                }
                exit('success');
            }
        }
    } else {
        if($got['status'] == 'created') {
            $ret = $this->send($activity, $got, $user);
            if(is_error($ret)) {
                exit('红包发放失败, 你可以在活动结束之前重新领取. 活动结束后无法领取, 请注意');
                exit($ret['message']);
            }
            exit('success');
        }
    }
}

if($activity['type'] == 'game') {
    require_once MB_ROOT . '/source/Game.class.php';
    $rid = intval($_GPC['rid']);
    if(!empty($rid)) {
        $sql = 'SELECT * FROM ' . tablename('mbrl_records') . ' WHERE `id`=' . $rid;
        $record = pdo_fetch($sql);
        if(empty($record) || $record['activity'] != $activity['actid'] || $record['uid'] != $user['uid']) {
            exit('非法的访问');
        }
        $ret = $record;
    } else {
        if(empty($error)) {
            $g = new Game();
            $filters = array();
            $filters['uid'] = $user['uid'];
            $filters['activity'] = $activity['actid'];
            $filters['status'] = 'paid';
            $trades = $g->getAll($filters);
            if(empty($trades)) {
                exit('没有可用的' . $activity['tag']['label']);
            }
            $ret = $a->grap($user, $activity);
            if(!is_error($ret)) {
                $g->touch($trades[0]['id'], 'completed');
            }
        } else {
            exit($error['message']);
        }
    }
    if(is_error($ret)) {
        exit($ret['message']);
    } elseif ($ret['status'] == 'none') {
        exit('这次游戏没有获得红包, 再来一次 ? ? ?');
    } else {
        $ret = $this->send($activity, $ret, $user);
        if(is_error($ret)) {
            exit('红包发放失败, 你可以在活动结束之前重新领取. 活动结束后无法领取, 请注意');
            exit($ret['message']);
        }
        exit('success');
    }
}
exit('没有领取到红包');
