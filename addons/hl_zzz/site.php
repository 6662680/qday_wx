<?php

/**
 * 送粽子模块
 *
 * [皓蓝] www.weixiamen.cn 5517286@qq.com
 */
defined('IN_IA') or exit('Access Denied');

class Hl_zzzModuleSite extends WeModuleSite {

    public function doWebFormDisplay() {
        global $_W, $_GPC;
        $result = array('error' => 0, 'message' => '', 'content' => '');
        $result['content']['id'] = $GLOBALS['id'] = 'add-row-news-' . $_W['timestamp'];
        $result['content']['html'] = $this->template('item', TEMPLATE_FETCH);
        exit(json_encode($result));
    }

    public function doWebAwardlist() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        if (checksubmit('delete')) {
            pdo_delete('zzz_user', " id  IN  ('" . implode("','", $_GPC['select']) . "')");
            message('删除成功！', $this->createWebUrl('awardlist', array('id' => $id, 'page' => $_GPC['page'])));
        }
        if (!empty($_GPC['wid'])) {
            $wid = intval($_GPC['wid']);
            pdo_update('zzz_user', array('status' => intval($_GPC['status'])), array('id' => $wid));
            message('标识领奖成功！', $this->createWebUrl('awardlist', array('id' => $id, 'page' => $_GPC['page'])));
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 50;
        $where = '';
        $condition = array(
            'mobile' => " AND `b`.`mobile` LIKE '%" . $_GPC['profilevalue'] . "%'",
            'realname' => " AND `b`.`realname` LIKE '%" . $_GPC['profilevalue'] . "%'",
            'nickname' => " AND `b`.`nickname` LIKE '%" . $_GPC['profilevalue'] . "%'"
        );
        if (!empty($_GPC['profile'])) {
            $where .= $condition[$_GPC['profile']];
        }

        $sql = 'SELECT `a`.`id`, `a`.`friendcount`, `a`.`points`, `a`.`createtime`, `b`.`realname`, `b`.`nickname`, `b`.`mobile` FROM ' .
                tablename('zzz_user') . ' AS `a` LEFT JOIN ' . tablename('mc_mapping_fans') . ' AS `f` ON `f`.`fanid` = `a`.`fanid` LEFT
                JOIN ' . tablename('mc_members') . " AS `b` ON `b`.`uid` = `f`.`uid`  WHERE `a`.`rid` = :rid $where ORDER BY `a`.`points`
                DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array(':rid' => $id);
        $list = pdo_fetchall($sql, $params);
        if (!empty($list)) {
            $sql = 'SELECT COUNT(*) FROM ' . tablename('zzz_user') . ' AS `a` LEFT JOIN ' . tablename('mc_mapping_fans') . ' AS `f` ON `f`.`fanid` =
                    `a`.`fanid` LEFT JOIN ' . tablename('mc_members') . ' AS `b` ON `b`.`uid` = `f`.`uid` WHERE `a`.`rid` = :rid' . $where;
            $total = pdo_fetchcolumn($sql, $params);
            $pager = pagination($total, $pindex, $psize);
        }
        include $this->template('awardlist');
    }

    public function doMobileIntroduce() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $fromuser = $_W['fans']['from_user'];
        $zzz = pdo_fetch("SELECT * FROM " . tablename('zzz_reply') . " WHERE rid = '$id'");
        $sql = 'SELECT * FROM ' . tablename('zzz_user') . ' WHERE `rid` =:rid AND `fanid` = :fanid';
        $params = array(':rid' => $id, ':fanid' => $_W['fans']['fanid']);
        $myuser = pdo_fetch($sql, $params);
        include $this->template('introduce');
    }

    public function doMobileLottery() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $sql = 'SELECT * FROM ' . tablename('zzz_reply') . ' WHERE `rid` = :rid';
        $params = array(':rid' => $id);
        $zzz = pdo_fetch($sql, $params);
        if (empty($zzz)) {
            message('非法访问，请重新发送消息进入！');
        }

        checkauth();
        load()->model("mc");
        $profile = mc_require($_W['member']['uid'], array('nickname', 'mobile'), '需要完善资料后才能继续.');
        $startgame = 1;
        if ($zzz['start_time'] > TIMESTAMP) {
            $startgame = 0;
            $str = "活动没开始";
        }
        if ($zzz['end_time'] < TIMESTAMP) {
            $startgame = 0;
            $str = "活动已结束";
        }
        if (empty($_W['fans']['fanid'])) {
            $sql = 'SELECT `fanid` FROM ' . tablename('mc_mapping_fans') . ' WHERE `uid` = :uid';
            $params = array(':uid' => $_W['member']['uid']);
            $fansId = pdo_fetchcolumn($sql, $params);
            if (empty($fansId)) {
                message('必须关注公众号才可以进入游戏', $this->createMobileUrl('introduce', array('id' => $id)), 'error');
            }
            $_W['fans']['fanid'] = $fansId;
        }

        $sql = 'SELECT * FROM ' . tablename('zzz_user') . ' WHERE `rid` = :rid AND `fanid` = :fanid';
        $params = array(':rid' => $id, ':fanid' => $_W['fans']['fanid']);
        $myuser = pdo_fetch($sql, $params);
        // 用户不存在插入一条数据
        if (empty($myuser)) {
            $zzz_user = array(
                'rid' => $id,
                'count' => 0,
                'points' => 0,
                'fanid' => $_W['fans']['fanid'],
                'createtime' => TIMESTAMP
            );
            pdo_insert('zzz_user', $zzz_user);
        }
        $myph = '';
        if (!empty($myuser)) {
            $sql = 'SELECT count(*) FROM ' . tablename('zzz_user') . ' WHERE `rid` = :rid AND `points` > :points';
            $params = array(':rid' => $id, ':points' => $myuser['points']);
            $ph = pdo_fetchcolumn($sql, $params);
            $myph = intval($ph) + 1;
        }
        // 分享增加体力
        $shareFid = intval($_GPC['shareuid']);
        if (!empty($shareFid)) {
            $sql = 'SELECT `id` FROM ' . tablename('zzz_share') . ' WHERE `rid` = :rid AND `fanid` = :fanid AND `sharefid` = :sharefid';
            $params = array(':rid' => $id, ':fanid' => $_W['fans']['fanid'], 'sharefid' => $shareFid);
            $shareInfo = pdo_fetchcolumn($sql, $params);
            if (empty($shareInfo)) {
                pdo_insert('zzz_share', array('rid' => $id, 'fanid' => $_W['fans']['fanid'], 'sharefid' => $shareFid));
                pdo_update('zzz_user', array('sharevalue' => $myuser['sharevalue'] + $zzz['sharevalue']), array('fanid' => $shareFid, 'rid' => $id));
            }
        }
        $energylimit = ($zzz['maxlottery'] + $zzz['prace_times']) * 10;
        include $this->template('gamex');
    }

    public function doMobileGetplayer() {
        header("Content-type: application/json");
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $sql = 'SELECT * FROM ' . tablename('zzz_reply') . ' WHERE `rid` =:rid';
        $params = array('rid' => $id);
        $zzz = pdo_fetch($sql, $params);

        $sql = 'SELECT COUNT(*) FROM ' . tablename('zzz_winner') . ' WHERE `fanid` = :fanid AND `rid` = :rid AND `createtime` > :createtime';
        $params[':fanid'] = $_W['fans']['fanid'];
        $params[':createtime'] = strtotime(date('Ymd'));
        $totals = pdo_fetchcolumn($sql, $params);

        $sql = 'SELECT `id`, `points`, `sharevalue` FROM ' . tablename('zzz_user') . ' WHERE `fanid` = :fanid AND `rid` = :rid';
        $params = array(':fanid' => $_W['fans']['fanid'], ':rid' => $id);
        $myuser = pdo_fetch($sql, $params);

        $arr_times = $this->get_today_times($totals, $zzz['maxlottery'], $zzz['prace_times'], $myuser['count']);

        $arr = array('power' => $myuser['points']);
        $arr['weekPower'] = rand(1, 6);
        $arr['ranking'] = 56;
        $arr['weekRanking'] = 57;
        $arr['energy'] = $arr_times['today_has'] * 10;

        if (empty($arr['energy']) && !empty($myuser['sharevalue'])) {
            $arr['energy'] = $myuser['sharevalue'];
        }
        message($arr, '', 'ajax');
    }

    public function doMobilePowerup() {
        header("Content-type: application/json");
        global $_GPC, $_W;
        if (empty($_W['fans']['fanid'])) {
            message(array('result' => '非法参数1！', 'error' => true));
        }
        $id = intval($_GPC['id']);
        $sql = 'SELECT * FROM ' . tablename('zzz_reply') . ' WHERE `rid` = :rid';
        $params = array(':rid' => $id);
        $zzz = pdo_fetch($sql, $params);
        if (empty($zzz)) {
            message(array('result' => '非法参数2！', 'error' => true));
        }

        $sql = 'SELECT COUNT(*) FROM ' . tablename('zzz_winner') . ' WHERE `fanid` = :fanid AND `rid` = :rid AND `createtime` > :createtime';
        $params[':fanid'] = $_W['fans']['fanid'];
        $params[':createtime'] = strtotime(date('Ymd'));
        $totals = pdo_fetchcolumn($sql, $params);

        $sql = 'SELECT `id`, `points`, `count`, `sharevalue` FROM ' . tablename('zzz_user') . ' WHERE `fanid` = :fanid AND `rid` = :rid';
        $params = array(':fanid' => $_W['fans']['fanid'], ':rid' => $id);
        $myuser = pdo_fetch($sql, $params);

        $arr_times = $this->get_today_times($totals, $zzz['maxlottery'], $zzz['prace_times'], $myuser['count']);

        $arr = array();
        $arr['powerUpResult']['type'] = rand(1, 2);
        $arr['powerUpResult']['value'] = rand(100, 300);
        $arr['weekPower'] = 1;
        $arr['power'] = $myuser['points'];
        $arr['weekRanking'] = 55;
        $arr['energy'] = 20;

        if ($arr_times['today_has'] <= 0 && empty($myuser['sharevalue'])) {
            $arr['powerUpResult']['value'] = 0;
            message($arr, '', 'ajax');
        }
        $data = array(
            'rid' => $id,
            'point' => $arr['powerUpResult']['value'],
            'fanid' => $_W['fans']['fanid'],
            'createtime' => TIMESTAMP
        );
        pdo_insert('zzz_winner', $data);

        if ($totals >= $zzz['maxlottery']) {
            pdo_query('UPDATE ' . tablename('zzz_user') . " SET `sharevalue` = `sharevalue` - 10 , `points` = `points` + " . $arr['powerUpResult']['value'] .
                        " WHERE `fanid` = " . $_W['fans']['fanid'] . ' AND `rid` = ' . $id);
        } else {
            pdo_query("UPDATE " . tablename('zzz_user') . " SET `points` = `points` + " . $arr['powerUpResult']['value'] . " WHERE `fanid` = "
                        . $_W['fans']['fanid'] . ' AND `rid` = ' . $id);
        }
        message(array('result' => $arr, 'success' => true), '', 'ajax');
    }

    public function doMobileRank() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $sql = 'SELECT * FROM ' . tablename('zzz_reply') . ' WHERE `rid` = :rid';
        $params = array(':rid' => $id);
        $zzz = pdo_fetch($sql, $params);
        $showurl = empty($zzz['guzhuurl']) ? 0 : 1;
        $str = '';
        if (!empty($_W['fans']['fanid'])) {
            $showurl = 0;
            $sql = 'SELECT * FROM ' . tablename('zzz_user') . ' WHERE  `fanid` = :fanid AND `rid` = :rid';
            $params[':fanid'] = $_W['fans']['fanid'];
            $myuser = pdo_fetch($sql, $params);
            if (!empty($myuser)) {
                $sql = 'SELECT COUNT(*) FROM ' . tablename('zzz_user') . ' WHERE `rid` = :rid AND `points` > :points';
                $params = array(':rid' => $id, ':points' => $myuser['points']);
                $ph = pdo_fetchcolumn($sql, $params);
                $myph = intval($ph) + 1;
                if ($myph < 11) {
                    $str = intval($myuser['points'] / 2000) . '个';
                } else {
                    $str = $myph . "名";
                }
            }
        }

        $sql = 'SELECT `u`.`points`, `mc`.`nickname`, `mc`.`realname` FROM ' . tablename('zzz_user') . ' AS `u` LEFT JOIN ' . tablename('mc_mapping_fans')
                . ' AS `fans` ON `fans`.`fanid` = `u`.`fanid` LEFT JOIN ' . tablename('mc_members') . ' AS `mc` ON `fans`.`uid` = `mc`.`uid` WHERE `u`.`rid`
                = :rid ORDER BY `u`.`points` DESC LIMIT 0, 20';
        $params = array(':rid' => $id);
        $allph = pdo_fetchall($sql, $params);
        foreach ($allph as $k => $v) {
            $allph[$k]['zz'] = intval($v['points'] / 2000);
            $allph[$k]['ypoints'] = intval($v['points'] % 2000);
        }

        include $this->template('rank');
    }

    // 点击量统计
    public function doMobileUcount() {
        global $_GPC, $_W;
        $effective = true;
        $msg = "输送体力未成功";
        $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
            $effective = false;
            $msg = "只能在微信中输送哦!";
        }

        $id = intval($_GPC['id']);
        $uid = intval($_GPC['uid']);
        if (!$uid) {
            $effective = false;
        }
        $url = $this->createMobileUrl('rank', array('id' => $id));
        $user = pdo_fetch("SELECT * FROM " . tablename('zzz_user') . " WHERE id = '{$uid}' and rid=" . $id . " LIMIT 1");
        if ($user) {
            $member = fans_search($user['from_user']);
            if ($uid && $effective) {
                if (!isset($_COOKIE["hlzzzx"])) {
                    setcookie('hlzzzx', 1, TIMESTAMP + 86400);
                    $data = array(
                        'count' => $user['count'] + 1,
                        'friendcount' => $user['friendcount'] + 1,
                    );
                    pdo_update('zzz_user', $data, array('id' => $uid, 'rid' => $id));
                    $msg = '你已成功为' . $member['nickname'] . '输送体力！';
                } else {
                    $msg = '一天只能输送一次体力哦!';
                }
            }
        }
        message($msg, $url);
    }


    /**
     * @param $userhad     用户今天已使用
     * @param $maxlottery  每天系统送
     * @param $prace_times 每天最多奖励次数
     * @param $friedsend   分享赠送
     * @return array       计算结果
     */
    private function get_today_times($userhad, $maxlottery, $prace_times, $friedsend) {
        $arr = array(
            'today_has' => 0,
            'todayalltimes' => $friedsend,
        );
        if ($userhad >= ($maxlottery + $prace_times)) {
            $arr['today_has'] = 0;
            return $arr;
        }
        if (($userhad >= $maxlottery) && !$friedsend) {
            $arr['today_has'] = 0;
            return $arr;
        }
        if (($userhad + $friedsend) >= ($prace_times + $maxlottery)) {
            $arr['today_has'] = $prace_times + $maxlottery - $userhad;
            return $arr;
        }
        if ($userhad < $maxlottery) {
            if ($friedsend < $prace_times) {
                $arr['today_has'] = $maxlottery + $friedsend - $userhad;
            } else {
                $arr['today_has'] = $maxlottery + $prace_times - $userhad;
            }
        } else {
            if ($friedsend + $userhad > $maxlottery + $prace_times) {
                $arr['today_has'] = $maxlottery + $prace_times - $userhad;
            } else {
                $arr['today_has'] = $friedsend;
            }
        }
        return $arr;
    }

    public function doWebCvs() {
        global $_GPC, $_W;
        set_time_limit(0);
        $fname = "ZZZ_" . date('Ymd_Hi', TIMESTAMP) . ".csv";

        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=" . $fname);
        header("Content-Transfer-Encoding: binary ");
        ob_start();
        $header_str = iconv("utf-8", 'gb2312', "编号,分数,朋友,时间,昵称,姓名,手机\n");
        $file_str = "";
 

        $sql = "select u.id,u.points,u.friendcount,FROM_UNIXTIME(u.createtime) as time,b.nickname,b.realname,b.mobile from " . tablename('zzz_user') . " as u LEFT JOIN " . tablename('mc_mapping_fans')." f on f.openid = u.from_user
                  LEFT JOIN " . tablename('mc_members') . " b ON b.uid = f.uid WHERE u.rid='{$_GPC['id']}' order BY u.points desc Limit 1000 ";

        $result = pdo_fetchall($sql);

        if ($result) {
            foreach ($result as $row) {

                $file_str.= $row['id'] . ',' . iconv("utf-8", 'gb2312', $row['points']) . ',' . $row['friendcount'] . ',' . $row['time'] . ',' . iconv("utf-8", 'gb2312', $row['nickname']) . ',' . iconv("utf-8", 'gb2312', $row['realname']) . ',' . iconv("utf-8", 'gb2312', $row['mobile']) . "\n";
            }
        } else {
            echo "nonono!!!";
        }
        ob_end_clean();
        echo $header_str;
        echo $file_str;
    }

}
