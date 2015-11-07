<?php

defined('IN_IA') or exit('Access Denied');

//添加通知
function add_announce($announce = array())
{
    $data = array(
        'weid' => $announce['weid'],
        'giftid' => $announce['giftid'],
        'from_user' => $announce['from_user'],
        'type' => $announce['type'],
        'title' => $announce['title'],
        'content' => $announce['content'],
        'levelid' => -1,
        'displayorder' => 0,
        'updatetime' => TIMESTAMP,
        'dateline' => TIMESTAMP,
    );
    pdo_insert('icard_announce', $data);
}

//用户会员卡
function get_user_card($from_user)
{
    global $_W;
    $sql = "SELECT * FROM " . tablename('icard_card') . " WHERE from_user=:from_user AND weid=:weid LIMIT 1";
    return pdo_fetch($sql, array(':from_user' => $from_user, ':weid' => $_W['weid']));
}

//会员卡积分设置
function get_card_score()
{
    global $_W;
    $sql = "SELECT * FROM " . tablename('icard_score') . " WHERE weid=:weid LIMIT 1";
    return pdo_fetch($sql, array(':weid' => $_W['weid']));
}

function get_domain()
{
    $host = $_SERVER['HTTP_HOST'];
    $host = strtolower($host);
    if (strpos($host, '/') !== false) {
        $parse = @parse_url($host);
        $host = $parse['host'];
    }
    $topleveldomaindb = array('com', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'biz', 'info', 'pro', 'name', 'museum', 'coop', 'aero', 'xxx', 'idv', 'mobi', 'cc', 'me');
    $str = '';
    foreach ($topleveldomaindb as $v) {
        $str .= ($str ? '|' : '') . $v;
    }
    $matchstr = "[^\.]+\.(?:(" . $str . ")|\w{2}|((" . $str . ")\.\w{2}))$";
    if (preg_match("/" . $matchstr . "/ies", $host, $matchs)) {
        $domain = $matchs['0'];
    } else {
        $domain = $host;
    }
    return $domain;
}

