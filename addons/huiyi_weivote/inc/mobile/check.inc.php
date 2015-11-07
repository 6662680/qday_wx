<?php
defined('IN_IA') or die('Access Denied');
function Mobile_CheckView($theThis, $uniacid)
{
    global $_W, $_GPC;
    $rd = new ReturnData('');
    $rd = Mobile_CheckServer($theThis, $uniacid);
    if (!$rd->getState()) {
        return $rd;
    }
    $checkedData = Mobile_CheckBrower($theThis, $uniacid);
    if (!$rd->getState()) {
        return $rd;
    }
    $rd->setNode('Mobile_CheckView');
    return $rd;
}
function Mobile_CheckSubmit($theThis, $uniacid, $isajax, $openid, $follow, $weivote_vote, $member, $oid, $clientip)
{
    global $_W, $_GPC;
    $rd = new ReturnData('');
    $checkedData = $theThis->Mobile_CheckAjax($isajax);
    if (!$rd->getState()) {
        return $rd;
    }
    $rd = Mobile_CheckServer($theThis, $uniacid);
    if (!$rd->getState()) {
        return $rd;
    }
    $rd = Mobile_CheckBrower($theThis, $uniacid);
    if (!$rd->getState()) {
        return $rd;
    }
    $rd = Mobile_CheckFans($theThis, $uniacid, $openid, $follow);
    if (!$rd->getState()) {
        return $rd;
    }
    $rd = Mobile_CheckVote($theThis, $uniacid, $openid, $follow, $weivote_vote, $member, $oid, $clientip);
    if (!$rd->getState()) {
        return $rd;
    }
    return $rd;
}
function Mobile_CheckAjax($isajax)
{
    global $_W, $_GPC;
    $rd = new ReturnData('');
    if (!$isajax) {
        $rd->setCode(200);
        $rd->setNode('Mobile_CheckAjax');
        $rd->setMsg('呵呵，访问出错了哦，请联系作者情天');
        return $rd;
    }
    $servername = $_SERVER['SERVER_NAME'];
    $httpreferer = $_SERVER['HTTP_REFERER'];
    //if (!(strpos($httpreferer, 'http://demo.qdaygroup.com/') === 0)) {
    //    $rd->setCode(201);
    //    $rd->setNode('Mobile_CheckAjax');
     //   $rd->setMsg('呵呵，访问出错了哦，请联系作者情天');
     //   return $rd;
    //}
    return $rd;
}
function Mobile_CheckServer($theThis, $uniacid)
{
    global $_W, $_GPC;
    $rd = new ReturnData('');
    $servername = $_SERVER['SERVER_NAME'];
    if (strpos($servername, 'demo.qdaygroup.com') !== false) {
    }// else {
      //  $rd->setCode(200);
      //  $rd->setNode('Mobile_CheckServer');
      //  $rd->setMsg($servername . ' 您的域名未获得授权，请联系《微信投票》作者:情天 ');
    //}
    return $rd;
}
function Mobile_CheckBrower($theThis, $uniacid)
{
    global $_W, $_GPC;
    $rd = new ReturnData('');
    $user_agent = addslashes($_SERVER['HTTP_USER_AGENT']);
    if (strpos($user_agent, 'MicroMessenger') === false && strpos($user_agent, 'Windows Phone') === false) {
        $rd->setCode(201);
        $rd->setNode('Mobile_CheckBrower');
        $rd->setMsg('请通过微信打开！');
    }
    return $rd;
}
function Mobile_CheckFans($theThis, $uniacid, $openid, $follow)
{
    global $_W, $_GPC;
    $rd = new ReturnData('');
    load()->model('account');
    $account = uni_fetch();
    if (empty($openid) || $openid == '') {
        $rd->setCode(200);
        $rd->setNode('Mobile_CheckFans');
        $rd->setMsg('请通过发送关键字到公众号 “' . $account['name'] . '” 参与活动！' . '~');
        return $rd;
    } else {
        if (empty($follow) || $follow == 0) {
            $rd->setCode(201);
            $rd->setNode('Mobile_CheckFans');
            $rd->setMsg('请先关注公众号 “' . $account['name'] . '”  , 并通过发送关键字参与活动！' . '~~');
            return $rd;
        }
    }
    return $rd;
}
function Mobile_CheckVote($theThis, $uniacid, $openid, $follow, $weivote_vote, $member, $oid, $clientip)
{
    global $_W, $_GPC;
    $rd = new ReturnData('');
    $rd = Mobile_CheckVoteState($theThis, $uniacid, $weivote_vote);
    if (!$rd->getState()) {
        return $rd;
    }
    $rd = Mobile_CheckVoteTime($theThis, $uniacid, $weivote_vote);
    if (!$rd->getState()) {
        return $rd;
    }
    $rd = Mobile_CheckVoteName($theThis, $uniacid, $weivote_vote, $member);
    if (!$rd->getState()) {
        return $rd;
    }
    $rd = Mobile_CheckVoteLog($theThis, $uniacid, $weivote_vote, $openid, $oid, $clientip);
    if (!$rd->getState()) {
        return $rd;
    }
    return $rd;
}
function Mobile_CheckVoteState($theThis, $uniacid, $weivote_vote)
{
    global $_W, $_GPC;
    $rd = new ReturnData('');
    if ($weivote_vote['state'] == 1) {
        $rd->setCode(200);
        $rd->setNode('Mobile_CheckVoteState');
        $rd->setMsg('本次投票活动已关闭!');
        return $rd;
    }
    if ($weivote_vote['state'] == 2) {
        $rd->setCode(201);
        $rd->setNode('Mobile_CheckVoteState');
        $rd->setMsg('本次投票活动已暂停!');
        return $rd;
    }
    return $rd;
}
function Mobile_CheckVoteTime($theThis, $uniacid, $weivote_vote)
{
    global $_W, $_GPC;
    $rd = new ReturnData('');
    if ($weivote_vote['start_time'] > TIMESTAMP) {
        $html = '本次投票活动尚未开始!';
        if ($weivote_vote['name_state'] == 1) {
            $html = $html . '<br><a class="ajax-link" data-dismiss="modal" href="ajax/reg.html">你可以先登记信息，抢先一步参与！</a>';
        }
        $rd->setCode(200);
        $rd->setNode('Mobile_CheckVoteTime');
        $rd->setMsg($html);
        return $rd;
    }
    if ($weivote_vote['end_time'] < TIMESTAMP) {
        $rd->setCode(201);
        $rd->setNode('Mobile_CheckVoteTime');
        $rd->setMsg('本次投票活动已结束!');
        $rd->setUrl('ajax/result.html');
        return $rd;
    }
    return $rd;
}
function Mobile_CheckVoteName($theThis, $uniacid, $weivote_vote, $member)
{
    global $_W, $_GPC;
    $rd = new ReturnData('');
    if ($weivote_vote['name_state'] == 1) {
        if (empty($member['realname']) || empty($member['mobile']) || empty($member['qq'])) {
            $rd->setCode(200);
            $rd->setNode('Mobile_CheckVoteName');
            $rd->setMsg('还差一步，请先登记信息!');
            $rd->setUrl('ajax/reg.html');
            return $rd;
        }
    }
    return $rd;
}
function Mobile_CheckVoteLog($theThis, $uniacid, $weivote_vote, $openid, $oid, $clientip)
{
    global $_W, $_GPC;
    $rd = new ReturnData('');
    $vid = $weivote_vote['id'];
    $oidcount = 0;
    $oidstr = '';
    if (!is_array($oid) && !(strpos($oid, ',') !== false)) {
        $oidstr = $oid;
        $oidcount = 1;
    } else {
        if (is_array($oid) && count($oid) > 0) {
            foreach ($oid as $oid_one) {
                $oidstr .= $oid_one . ',';
            }
            $oidstr = substr($oidstr, 0, strlen($oidstr) - 1);
            $oidcount = count($oid);
        }
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($theThis->table_option) . " WHERE vid = '{$vid}' AND id in (" . $oidstr . ')';
    $setting_option_count = pdo_fetchcolumn($sql);
    if ($setting_option_count != $oidcount) {
        $rd->setCode(200);
        $rd->setNode('Mobile_CheckVoteLog');
        $rd->setMsg('您的投票选项有误!');
        return $rd;
    }
    $sql = 'SELECT COUNT(*) FROM ' . tablename($theThis->table_log) . " WHERE from_user = '{$openid}' AND vid = '{$vid}' ";
    $fans_log_count = pdo_fetchcolumn($sql);
    if ($weivote_vote['once_vote'] != null && $weivote_vote['once_vote'] == 1 && $fans_log_count > 0) {
        $rd->setCode(201);
        $rd->setNode('Mobile_CheckVoteLog');
        $rd->setMsg('您已参加过本次活动!');
        $rd->setUrl('ajax/result.html');
        return $rd;
    }
    if ($weivote_vote['interval_time'] > 0) {
        $now_clientip = $clientip;
        $now_createtime = TIMESTAMP;
        $last_log = pdo_fetch('SELECT * FROM ' . tablename($theThis->table_log) . " WHERE vid = '{$vid}' and clientip = '{$now_clientip}' order by id desc LIMIT 1 ");
        if ($now_createtime <= $last_log['create_time'] + 60 * $weivote_vote['interval_time']) {
            $rd->setCode(202);
            $rd->setNode('Mobile_CheckVoteLog');
            $rd->setMsg('您参与的投票活动繁忙! 请 ' . $weivote_vote['interval_time'] . ' 分钟后参与投票!');
            return $rd;
        }
    }
    $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $tomorrow = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
    $sql = 'SELECT COUNT(*) FROM ' . tablename($theThis->table_log) . " WHERE from_user = '{$openid}' AND vid = '{$vid}' AND create_time > " . $today . ' AND create_time < ' . $tomorrow;
    $fans_log_today_count = pdo_fetchcolumn($sql);
    if ($fans_log_today_count >= $weivote_vote['max_vote_day']) {
        $rd->setCode(203);
        $rd->setNode('Mobile_CheckVoteLog');
        $rd->setMsg('您今天投票次数已用完!');
        $rd->setUrl('ajax/result.html');
        return $rd;
    }
    if ($oidcount < 1 || $oidcount > $weivote_vote['max_vote_day'] || $oidcount > $weivote_vote['max_vote_count'] - $fans_log_today_count) {
        $rd->setCode(204);
        $rd->setNode('Mobile_CheckVoteLog');
        $rd->setMsg('投票数量不符合要求，请重新选择!');
        return $rd;
    }
    if ($fans_log_count >= $weivote_vote['max_vote_count']) {
        $rd->setCode(205);
        $rd->setNode('Mobile_CheckVoteLog');
        $rd->setMsg('您的投票次数已用完!');
        $rd->setUrl('ajax/result.html');
        return $rd;
    }
    if ($weivote_vote['type_vote'] == 1) {
        $sql = 'SELECT COUNT(*) FROM ' . tablename($theThis->table_log) . " WHERE from_user = '{$openid}' AND vid = '{$vid}' AND create_time > " . $today . ' AND create_time < ' . $tomorrow . ' AND oid in (' . $oidstr . ')';
        $fans_log_count = pdo_fetchcolumn($sql);
        if ($fans_log_count != 0) {
            $rd->setCode(206);
            $rd->setNode('Mobile_CheckVoteLog');
            $rd->setMsg('已投过该选项，请投给其他选项!');
            return $rd;
        }
    }
    return $rd;
}
?>