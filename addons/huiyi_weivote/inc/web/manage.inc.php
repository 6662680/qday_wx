<?php
/**
 * 微信投票
 * @author yyy
 * @url
 */
defined('IN_IA') or exit('Access Denied');

function Web_ManageVoteList($theThis, $uniacid, $pindex, $psize, $condition) {

    $list = pdo_fetchall("SELECT * FROM ".tablename($theThis->table_vote)." WHERE uniacid = '{$uniacid}' $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($theThis->table_vote) . " WHERE uniacid = '{$uniacid}' $condition");
    $pager = pagination($total, $pindex, $psize);

    $rd = new ReturnData("");
    $rd -> addData('list', $list);
    $rd -> addData('total', $total);
    $rd -> addData('pager', $pager);
    //$title = var_export($rd, true);
    return  $rd;
}


function Web_ManageVoteAdd($theThis, $uniacid) {
    global $_W,$_GPC;
    $rd = new ReturnData("");
    $id = intval($_GPC['id']);

    //处理投票信息
    $data = array(

        'uniacid' => $uniacid,
        'state' => intval($_GPC['state']),
        'create_time' => TIMESTAMP,

        'title' => $_GPC['title'],
        'picture' => $_GPC['picture'],
        'description' => $_GPC['description'],
        'intro' => htmlspecialchars_decode($_GPC['intro']),
        'rule' => htmlspecialchars_decode($_GPC['rule']),

        'max_vote_day' => intval($_GPC['max_vote_day']),
        'max_vote_count' => intval($_GPC['max_vote_count']),
        'type_vote' => intval($_GPC['type_vote']),//可否重复投选
        'name_state' => intval($_GPC['name_state']),


        'view_state' => intval($_GPC['view_state']),//投票前预览,0可以,1不可
        'follow_switch' => intval($_GPC['name_state']),//关注模式切换,0使用链接,1使用描述

        'interval_time' => intval($_GPC['interval_time']),//投票时间间隔
        'once_vote' => intval($_GPC['once_vote']),//是否是一次性活动,0不是,1是

        'default_tips' => $_GPC['default_tips'],

        'start_time' => empty($_GPC['time']['start']) ? TIMESTAMP : strtotime($_GPC['time']['start']),
        'end_time' => empty($_GPC['time']['end']) ? TIMESTAMP + 86399*7 : strtotime($_GPC['time']['end']),

        'follow_url' => $_GPC['follow_url'],
        'follow_desc' => htmlspecialchars_decode($_GPC['follow_desc']),
        'page_size' => intval($_GPC['page_size']),
        'page_switch' => intval($_GPC['page_switch']),

        'ad' => htmlspecialchars_decode($_GPC['ad']),
    );

    if (empty($id)) {
        pdo_insert($theThis->table_vote, $data);
    } else {
        if (!empty($_GPC['picture'])) {
            //file_delete($_GPC['picture-old']);
        } else {
            unset($data['picture']);
        }
        pdo_update($theThis->table_vote, $data, array('id' => $id));
    }
    return  $rd;
}


function Web_ManageVoteDelete($theThis, $uniacid, $id) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    $item = pdo_fetch("SELECT * FROM ".tablename($theThis->table_vote)." WHERE id = :id and uniacid=:uniacid" , array(':id' => $id,':uniacid'=>$uniacid));
    if (empty($item)) {
        $rd -> setCode(200);
    }
    //不做删除图片处理
    //if (!empty($item['thumb'])) {
    //    file_delete($item['thumb']);
    //}
    pdo_delete($theThis->table_vote, array('id' => $item['id']));
    return  $rd;
}

function Web_ManageOptionList($theThis, $uniacid, $vid) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    $vote = pdo_fetch("SELECT * FROM ".tablename($theThis->table_vote)." WHERE id = :id and uniacid=:uniacid" , array(':id' => $vid,':uniacid'=>$uniacid));
    $list = pdo_fetchall("SELECT * FROM ".tablename($theThis->table_option)." WHERE uniacid = '{$uniacid}' and vid = '{$vid}' ORDER BY code ASC");
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($theThis->table_option) . " WHERE uniacid = '{$uniacid}' and vid = '{$vid}' ");

    $rd -> addData('vote', $vote);
    $rd -> addData('list', $list);
    $rd -> addData('total', $total);
    return  $rd;
}

function Web_ManageOptionAdd($theThis, $uniacid, $vid) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    //选项
    //更新
    if (!empty($_GPC['option-code'])) {
        foreach ($_GPC['option-code'] as $index => $code) {
            if (empty($code)) {
                continue;
            }

            //处理标题和描述
            $update = array(
                'code' => $code,
                'title' => $_GPC['option-title'][$index],
                'picture' => $_GPC['option-picture'][$index],
                'description' => htmlspecialchars_decode($_GPC['option-description'][$index]),
                'state' => intval($_GPC['option-state'][$index]),
            );

            pdo_update($theThis->table_option, $update, array('id' => $index));
        }
    }

    //添加
    if (!empty($_GPC['option-code-new'])) {
        foreach ($_GPC['option-code-new'] as $index => $code) {
            if (empty($code)) {
                continue;
            }

            //处理标题和描述
            $insert = array(
                'uniacid' => $uniacid,
                'state' => intval($_GPC['state']),
                'create_time' => TIMESTAMP,

                'vid' => $vid,
                'code' => $code,
                'title' => $_GPC['option-title-new'][$index],
                'picture' => $_GPC['option-picture-new'][$index],
                'description' => htmlspecialchars_decode($_GPC['option-description-new'][$index]),
            );

            pdo_insert($theThis->table_option, $insert);
        }
    }

    return  $rd;
}

function Web_ManageOptionDelete($theThis, $uniacid) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    $id = intval($_GPC['id']);
    $item = pdo_fetch("SELECT * FROM ".tablename($theThis->table_option)." WHERE id = :id and uniacid=:uniacid" , array(':id' => $id,':uniacid'=>$uniacid));
    if (empty($item)) {
        message('抱歉，选项不存在或是已经被删除！', '', 'ajax');
    }
    pdo_delete($theThis->table_option, array('id' => $item['id']));
    message('删除选项成功', '', 'ajax');
}

//加载票数信息
function Web_ManageLogCount($theThis, $uniacid, $vid) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    $vote = pdo_fetch("SELECT * FROM ".tablename($theThis->table_vote)." WHERE id = :id and uniacid=:uniacid" , array(':id' => $vid,':uniacid'=>$uniacid));
    $list = pdo_fetchall("SELECT * FROM ".tablename($theThis->table_option)." WHERE uniacid = '{$uniacid}' and vid = '{$vid}' ORDER BY code ASC");
    $rd -> addData('vote', $vote);
    $rd -> addData('list', $list);
    return  $rd;
}
//更改票数信息
function Web_ManageLogCountAdd($theThis, $uniacid, $vid) {
//TODO
    global $_W,$_GPC;
    $rd = new ReturnData("");

    //总票数
    //更新
    $update = array(
        'log_count' => intval($_GPC['log_count']),
        'click_count' => intval($_GPC['click_count']),
    );
    pdo_update($theThis->table_vote, $update, array('id' => $vid));

    //票数
    //更新
    if (!empty($_GPC['option-code'])) {
        foreach ($_GPC['option-code'] as $index => $code) {
            if (empty($code)) {
                continue;
            }

            //处理标题和描述
            $update = array(
                'log_count' => $_GPC['option-log_count'][$index],
            );

            pdo_update($theThis->table_option, $update, array('id' => $index));
        }
    }
    return  $rd;
}

//冒泡排序
function Web_ManageOptionSort($array, $sortField, $sortType){
    $count = count($array);
    if ($count <= 0) return false;

    if ($sortType == null || $sortType != 'desc') {
        for($i=0; $i<$count; $i++){
            for($j=$count-1; $j>$i; $j--){

                if ($array[$j][$sortField] < $array[$j-1][$sortField]){
                    $tmp = $array[$j];
                    $array[$j] = $array[$j-1];
                    $array[$j-1] = $tmp;
                }

            }
        }
    } else {
        for($i=0; $i<$count; $i++){
            for($j=$count-1; $j>$i; $j--){

                if ($array[$j][$sortField] > $array[$j-1][$sortField]){
                    $tmp = $array[$j];
                    $array[$j] = $array[$j-1];
                    $array[$j-1] = $tmp;
                }

            }
        }
    }

    return $array;
}

function Web_ManageLog($theThis, $uniacid, $pindex, $psize, $condition, $vid) {

    $list = pdo_fetchall("SELECT * FROM ".tablename($theThis->table_log)." WHERE uniacid = '{$uniacid}' AND vid = '{$vid}' $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($theThis->table_log) . " WHERE uniacid = '{$uniacid}' AND vid = '{$vid}' $condition");
    $pager = pagination($total, $pindex, $psize);

    $rd = new ReturnData("");
    $rd -> addData('list', $list);
    $rd -> addData('total', $total);
    $rd -> addData('pager', $pager);
    //$title = var_export($rd, true);
    return  $rd;
}

function Web_ManageLogDelete($theThis, $uniacid, $id) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    $item = pdo_fetch("SELECT * FROM ".tablename($theThis->table_log)." WHERE id = :id and uniacid=:uniacid" , array(':id' => $id,':uniacid'=>$uniacid));
    if (empty($item)) {
        $rd -> setCode(200);
    }
    pdo_delete($theThis->table_log, array('id' => $item['id']));
    return  $rd;
}

function Web_ManageLogPlus($theThis, $uniacid, $vid, $oid, $num = 1) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    $weivote_vote = pdo_fetch("SELECT * FROM ".tablename($theThis->table_vote)." WHERE id = :id and uniacid=:uniacid" , array(':id' => $vid,':uniacid'=>$uniacid));
    $weivote_option = pdo_fetch("SELECT * FROM ".tablename($theThis->table_option)." WHERE id = :id and uniacid=:uniacid" , array(':id' => $oid,':uniacid'=>$uniacid));

    $update = array(
        'log_count' => $weivote_vote['log_count'] + $num,
    );
    if (!pdo_update($theThis->table_vote, $update, array('id' => $weivote_vote['id'])) ) {
        $rd -> setCode(200);
        $rd -> setMsg("系统超级繁忙~");
        $rd -> setNode('Web_LogCount');
        return $rd;
    }
    $update = array(
        'log_count' => $weivote_option['log_count'] + $num,
    );
    if (!pdo_update($theThis->table_option, $update, array('id' => $weivote_option['id'])) ) {
        $rd -> setCode(201);
        $rd -> setMsg("系统超级繁忙~~");
        $rd -> setNode('Web_LogCount');
        return $rd;
    }
    return $rd;
}