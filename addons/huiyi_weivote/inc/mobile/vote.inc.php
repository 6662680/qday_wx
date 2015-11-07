<?php
/**
 * 微信投票
 * @author yyy
 * @url
 */
defined('IN_IA') or exit('Access Denied');

function Mobile_GetWeivoteVote($theThis, $uniacid, $vid) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    //投票数据
    $weivote_vote = pdo_fetch("SELECT * FROM ".tablename($theThis -> table_vote)." WHERE id = '$vid' AND uniacid = '$uniacid' LIMIT 1");
    if (empty($weivote_vote)) {
        //message('非法访问，请重新发送消息进入页面！');
        $rd -> setCode(200);
        $rd -> setNode('Mobile_GetWeivoteVote');
        $rd -> setMsg('服务器暂时无法响应您的请求！');
        return $rd;
    }
    $rd -> setData($weivote_vote);
    return $rd;
}

function Mobile_GetWeivoteOption($theThis, $uniacid, $vid, $oid)  {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    $sql = "SELECT * FROM ".tablename($theThis -> table_option)." WHERE vid = '$vid' AND id = '$oid' LIMIT 1";
    $weivote_option = pdo_fetch($sql);
    if (empty($weivote_option)) {
        $rd -> setCode(200);
        $rd -> setNode('Mobile_GetWeivoteOption');
        $rd -> setMsg('系统中没有这个选项呢');
        return $rd;
    }
    $rd -> setData($weivote_option);
    return $rd;
}

function Mobile_GetWeivoteOptions($theThis, $uniacid, $vid, $voteKey = "", $page_switch = 1, $page_size = 10, $page_no = 1, $oid = '')  {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    //关键字部分
    $where = "";
    if(!empty($voteKey) && $voteKey != '') {
        if (is_numeric($voteKey)) {
            $where .= " and ( code = ".$voteKey." or title like '%".$voteKey."%' )";
        } else {
            $where .= " and title like '%".$voteKey."%'";
        }
    }
    //选项范围部分
    if ($oid != '') {
        $where .= " AND id in (". $oid .") ";
    }
    //排序
    $where .= " order by code asc ";


    //选项总数
    $weivote_options_count = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($theThis -> table_option)." WHERE vid = " . $vid);

    //分页
    $rd = Mobile_GetPageParam($page_switch, $page_size, $page_no, $weivote_options_count);
    $page_count = $rd -> getData('page_count');
    $page_no = $rd -> getData('page_no');
    $page_start = $rd -> getData('page_start');
    $page_end = $rd -> getData('page_end');
    $page_nos = $rd -> getData('page_nos');
    $where .= $rd -> getData('where');

    $sql = "SELECT * FROM ".tablename($theThis -> table_option)." WHERE vid = " . $vid . $where;
    $weivote_options = pdo_fetchall($sql);

    $weivote_options_index = array();
    foreach ($weivote_options as $weivote_option) {
        $weivote_options_index[$weivote_option['id']] = $weivote_option;
    }

    $json_data = array(
        'weivote_options' => $weivote_options,
        'weivote_options_count' => $weivote_options_count,
        'weivote_options_index' => $weivote_options_index,
        //分页部分数据
        'page_count' => $page_count,
        'page_no' => $page_no,
        'page_start' => $page_start,
        'page_end' => $page_end,
        'page_nos' => $page_nos,
    );
    $rd -> setData($json_data);
    return $rd;
}

function Mobile_GetWeivoteOptionsView($theThis, $uniacid, $weivote_vote, $weivote_options_index) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    $weivote_options_view = array();
    foreach ($weivote_options_index as $oid => $weivote_option) {

        $view = array(
            'id' => $oid,
            'code' => $weivote_option['code'],
            'title' => $weivote_option['title'],
            'picture' => $weivote_option['picture'],
            'log_count' => $weivote_option['log_count'],
            'proportion' => intval(doubleval($weivote_option['log_count'])/$weivote_vote['log_count']*10000)/100,
        );
        array_push($weivote_options_view, $view);
    }

    $rd -> setData($weivote_options_view);
    return $rd;

}

function Mobile_GetPageParam($pageSwitch, $pageSize, $pageNo, $count) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    //分页部分
    $page_switch = $pageSwitch;
    $page_count = $page_no = $page_start = $page_end = 1;
    $where = "";

    $page_nos = array();
    if ($page_switch != 1) {
        //分页大小
        $page_size = $pageSize;
        if ($page_size == null || $page_size == "" || $page_size <= 0) {
            $page_size = 10;
        }
        //总页号
        $page_count = ceil($count/$page_size);

        $page_no = $pageNo;//当前页号
        if ($page_no < 1) {
            $page_no = 1;
        }
        if ($page_no > $page_count) {
            $page_no = $page_count;
        }

        //计算显示的起始页号和结束页号
        if ($page_no <= 3) {
            $page_start = 1;
        } else if ( ($page_count - $page_no) < 3){
            if ( ($page_count - $page_no) <= 0) {
                $page_start = 1;
            } else {
                $page_start = $page_count - 4;
            }
        } else {
            $page_start = $page_no - 2;
        }

        if ($page_no <= 3) {
            $page_end = $page_count;
        } else if ( ($page_count - $page_no) < 3){
            $page_end = $page_count;
        } else {
            $page_end = $page_no + 2;
        }
        //组装数组
        $page_nos = array();
        for ($i = $page_start; $i <= $page_end; $i++) {
            array_push($page_nos, $i);
        }
        //选项
        $page = " limit ".( ($page_no - 1) * $page_size ) . "," . $page_size;
        $where = $page;
    }/**分页参数-end**/

    $rd -> addData('where', $where);
    $rd -> addData('page_count', $page_count);
    $rd -> addData('page_no', $page_no);
    $rd -> addData('page_start', $page_start);
    $rd -> addData('page_end', $page_end);
    $rd -> addData('page_nos', $page_nos);

    return $rd;
}

function Mobile_ClickCount($theThis, $uniacid, $weivote_vote) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    $update = array(
        'click_count' => $weivote_vote['click_count'] + 1,
    );
    if (!pdo_update($theThis->table_vote, $update, array('id' => $weivote_vote['id'])) ) {
        $rd -> setCode(200);
        $rd -> setMsg("系统超级繁忙");
        $rd -> setNode('Mobile_ClickCount');

    }
    return $rd;
}

function Mobile_LogOptionCount($theThis, $uniacid, $weivote_vote, $weivote_option, $num = 1) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    $log_count = $weivote_option['log_count'] + $num;
    $sql_update = 'UPDATE ' . tablename($theThis->table_option) . ' SET log_count = ' . $log_count . ' WHERE id = ' . $weivote_option['id'];
    pdo_query($sql_update);

    return $rd;
}
function Mobile_LogVoteCount($theThis, $uniacid, $weivote_vote, $weivote_option, $num = 1) {
    global $_W,$_GPC;
    $rd = new ReturnData("");
    $update = array(
        'log_count' => $weivote_vote['log_count'] + $num,
    );
    if (!pdo_update($theThis->table_vote, $update, array('id' => $weivote_vote['id'])) ) {
        $rd -> setCode(200);
        $rd -> setMsg("系统超级繁忙~~~");
        $rd -> setNode('Mobile_LogOptionCount');
        return $rd;
    }

    return $rd;
}

function Mobile_GetMember($theThis, $uniacid, $id, $openid) {
    global $_W,$_GPC;
    $rd = new ReturnData("");

    load()->model('account');
    $account = uni_fetch();

    load()->model('mc');
    $fans = mc_fansinfo($openid, $uniacid);
    $member = mc_fetch($fans['uid'], array('realname', 'mobile', 'qq', 'msn'));

    if (empty($member)) {
        $rd -> setMsg("无法识别您的身份，请发送关键字到公众号 “" . $account['name'] . "” 参加活动");
        $rd -> setCode(200);
    }
    $rd -> addData('fans', $fans);
    $rd -> addData('member', $member);
    return $rd;
}

function Mobile_UpdateMember($theThis, $uniacid, $uid, $data) {
    global $_W,$_GPC;
    $rd = new ReturnData("");
    load()->model('mc');
    if (!mc_update($uid, $data)) {
        $rd -> setMsg("更新个人信息失败");
        $rd -> setCode(200);
    }
    return $rd;
}

function Mobile_GetOpenid() {
    global $_W,$_GPC;
    $openid = $_W['openid'];
    //$from_user = $_W['fans']['from_user'];
    //$openid = 'o-b_EjsC5P2vAi1SgNPsoUJhScTQ';
    return $openid;
}