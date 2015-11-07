<?php
session_start();
/**
 * 好声音模块微站定义
 *
 * @url http://www.qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

class Xhw_voiceModuleSite extends WeModuleSite {

    public function Checkedservername() {
       
    }

    public function Checkeduseragent() {
        $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
            message('非法访问，请通过微信打开！');
            die();
        }
    }

    public function doCheckedMobile($id) {
        global $_GPC, $_W;
        $openid = $_W['fans']['from_user'];
        $sql = "SELECT * FROM " . tablename(xhw_voice) . " WHERE weid = '{$_W['uniacid']}' AND id = '$id'";
        $arr = pdo_fetch($sql);
        $follow_url = $arr['follow_url'];
        if (empty($openid)) {
            echo "<script>alert('请仔细阅读活动说明');location.href='" . $follow_url . "';</script>";
            die();
        } else {
            $sql = "SELECT * FROM " . tablename(mc_mapping_fans) . " WHERE openid = '$openid'";
            $arr = pdo_fetch($sql);
            if ($arr['follow'] != '1') {
                echo "<script>alert('请仔细阅读活动说明');location.href='" . $follow_url . "';</script>";
                die();
            }
        }
    }

    public function doMobileIndex() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $this->Checkeduseragent();
        $pindex = max(1, intval($_GPC['page']));
        $id = $_GPC['id'];
        $sql = "SELECT * FROM " . tablename(xhw_voice) . " WHERE weid = '{$_W['uniacid']}' AND id = '$id'";
        $arr = pdo_fetchall($sql);
        $logo = $arr['0']['logo'];
        $photo = "attachment/" . $arr['0']['photo'];
        $share_title = $arr['0']['share_title'];
        $share_desc = $arr['0']['share_desc'];
        $rule_url = $arr['0']['rule_url'];
        $rules_url = $arr['0']['rules_url'];
        $submit_url = $arr['0']['submit_url'];
        $title = $arr['0']['title'];
        $adimg = explode("|", $arr['0']['adimg']);
        $adimglink = explode("|", $arr['0']['adimglink']);
        $cnzz = $arr['0']['cnzz'];
        $bnum = $arr['0']['bnum'];
        $hot = $arr['0']['hot'];
        if ($arr['0']['starttime'] - time() > 0) {
            $time = $arr['0']['starttime'] - time();
        } elseif ($arr['0']['endtime'] - time() > 0) {
            $time = $arr['0']['endtime'] - time();
        } else {
            $time = 0;
            $url = $this->createmobileUrl('top', array('do' => 'top', 'id' => $id));
            echo "<script>alert('活动已经结束啦，来看看排名吧');location.href='$url';</script>";
            die;
        }
        $psize = $arr['0']['anum'];
        $condition = "weid = '{$_W['uniacid']}' AND rid = '$id' AND pass='1'";
        $paixu = "-num";
        if ($hot) {
            $_SESSION['list'] = "-id";
        } elseif (!isset($_GPC['page'])) {
            unset($_SESSION['list']);
        }
        if (isset($_SESSION['list'])) {
            $paixu = $_SESSION['list'];
        }
        $sql = "SELECT * FROM " . tablename("xhw_voice_reg") . " WHERE $condition ORDER BY $paixu LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $list = pdo_fetchall($sql);
 
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(xhw_voice_reg) . " WHERE $condition");
        $pager = pagination($total, $pindex, $psize);
        if(empty($psize)){
            $psize = 1;
        }
        $pageend = ceil($total / $psize);
        if ($total / $psize != 0 && $total >= $psize) {
            $pageend++;
        }
        $sql = "SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE $condition ORDER BY -sharenum LIMIT 0,$bnum";
        $toplist = pdo_fetchall($sql);
        $lognum = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(xhw_voice_log) . " WHERE rid='$id'");

        include $this->template('index');
    }

    public function doMobileItem() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $this->Checkeduseragent();
        $isappinstalled = $_GPC['isappinstalled'];
        $id = $_GPC['id'];
        $rid = $_GPC['rid'];
        if (!is_numeric($id)) {
            $id = pdo_fetchcolumn("SELECT id FROM " . tablename(xhw_voice_reg) . " WHERE weid = '{$_W['uniacid']}' AND rid = '$rid' AND nickname = '$id'");
        }
        if (!is_numeric($id)) {
            $link = $this->createmobileUrl('index', array('do' => 'index', 'id' => $rid));
            echo "<script>alert('找不到您搜索的人，请重新搜索该用户ID或名字');location.href='$link';</script>";
            die();
        }
        if ($isappinstalled == "0") {
            if (isset($_SESSION['time' . $id])) {
                if ($_SESSION['time' . $id] <= time()) {
                    session_destroy();
                    $_SESSION['time' . $id] = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
                    $arr = pdo_fetchall("SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE id = '{$id}'");
                    $sharenum = intval($arr[0]['sharenum']);
                    $sharenum = $sharenum + 1;
                    $data = array('sharenum' => $sharenum);
                    pdo_update('xhw_voice_reg', $data, array('id' => $id));
                }
            } else {
                $_SESSION['time' . $id] = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
                $arr = pdo_fetchall("SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE id = '{$id}'");
                $sharenum = intval($arr[0]['sharenum']);
                $sharenum = $sharenum + 1;
                $data = array('sharenum' => $sharenum);
                pdo_update('xhw_voice_reg', $data, array('id' => $id));
            }
        }
        if ($rid) {
            $sql = "SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE id='$id' AND rid = '$rid' AND weid = '{$_W['uniacid']}'";
        } else {
            $sql = "SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE id='$id'";
        }
        $arr = pdo_fetchall($sql);
        if (!$arr) {
            $link = $this->createmobileUrl('index', array('do' => 'index', 'id' => $_GPC['rid']));
            echo "<script>alert('您访问的歌曲不存在');location.href='$link';</script>";
            die();
        }
        $title = $arr[0]['title'];
        $mp3 = $arr[0]['mp3'];
        $nickname = $arr[0]['nickname'];
        $avatar = $arr[0]['avatar'];
        $num = $arr[0]['num'];
        $rid = $arr[0]['rid'];
        $sharenum = $arr[0]['sharenum'];
        if ($arr[0]['pass'] == '0') {
            $link = $this->createmobileUrl('index', array('do' => 'index', 'id' => $rid));
            echo "<script>alert('该歌曲还在审核中');location.href='$link';</script>";
            die();
        }
        $sql = "SELECT * FROM " . tablename(xhw_voice) . " WHERE weid = '{$_W['uniacid']}' AND id = '$rid'";
        $arr = pdo_fetchall($sql);
        $logo = $arr['0']['logo'];
        $rule_url = $arr['0']['rule_url'];
        $rules_url = $arr['0']['rules_url'];
        $submit_url = $arr['0']['submit_url'];
        $title_a = $arr['0']['title'];
        $cnzz = $arr['0']['cnzz'];
        $bnum = $arr['0']['bnum'];
        //朋友圈人气排行榜
        $condition = "weid = '{$_W['uniacid']}' AND rid = '$rid' AND pass='1'";
        $sql = "SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE $condition ORDER BY -sharenum LIMIT 0,$bnum";
        $toplist = pdo_fetchall($sql);
        $votelink = $this->createmobileUrl('logpost', array('do' => 'logpost', 'id' => $id, 'rid' => $rid));
        include $this->template('item');
    }

    public function doMobileTop() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $this->Checkeduseragent();
        $pindex = max(1, intval($_GPC['page']));
        $id = $_GPC['id'];
        $psize = 1000;
        $pxid = ($pindex - 1) * $psize;
        $condition = "weid = '{$_W['uniacid']}' AND rid = '$id' AND pass='1'";
        $paixu = "-num";
        $sql = "SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE $condition ORDER BY $paixu LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $list = pdo_fetchall($sql);

        $sql = "SELECT * FROM " . tablename(xhw_voice) . " WHERE weid = '{$_W['uniacid']}' AND id = '$id'";
        $arr = pdo_fetchall($sql);
        $logo = $arr['0']['logo'];
        $photo = "attachment/" . $arr['0']['photo'];
        $share_title = $arr['0']['share_title'];
        $share_desc = $arr['0']['share_desc'];
        $rules_url = $arr['0']['rules_url'];
        $submit_url = $arr['0']['submit_url'];
        $title = $arr['0']['title'];
        $cnzz = $arr['0']['cnzz'];
        if ($arr['0']['endtime'] - time() > 0) {
            $time = $arr['0']['endtime'] - time();
        } else {
            $time = 0;
        }
        include $this->template('top');
    }

    public function doMobileMy() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $this->Checkeduseragent();
        $id = $_GPC['id'];
        require_once IA_ROOT . "/addons/xhw_voice/jssdk.class.php";
        $weixin = new jssdk($jie = '0', $url = '');
        $wx = $weixin->get_sign();
        $sql = "SELECT * FROM " . tablename(xhw_voice_setting) . " WHERE weid = '{$_W['uniacid']}'";
        $arr = pdo_fetch($sql);
        $openidpass = $arr['openidpass'];
        if (empty($_GPC['code']) && $openidpass) {
            $weixin->get_code($_W['siteurl']);
        }
        if ($openidpass) {
            $followpass = $arr['followpass'];
            $code = $_GPC['code'];
            if ($code) {
                $data = $weixin->get_openid($code);
                $openid = $data['openid'];
                if ($followpass != '1') {
                    $sql = "SELECT * FROM " . tablename(mc_mapping_fans) . " WHERE openid = '$openid'";
                    $arr = pdo_fetch($sql);
                    if ($arr['follow'] != '1') {
                        echo "<script>alert('请仔细阅读活动说明');location.href='" . $follow_url . "';</script>";
                        die();
                    }
                }
            }
            if (empty($openid)) {
                $this->doCheckedMobile($id);
            }
        } else {
            $this->doCheckedMobile($id);
            $openid = $_W['fans']['from_user'];
        }
        $condition = "openid = '$openid' AND rid = '$id'";
        $sql = "SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE $condition ORDER BY -num LIMIT 0 , 30"; //按大小排序
        $list = pdo_fetchall($sql);
        $nickname = $list['0']['nickname'];
        $num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(xhw_voice_reg) . " WHERE $condition");
        $sql = "SELECT * FROM " . tablename(xhw_voice) . " WHERE weid = '{$_W['uniacid']}' AND id = '$id'";
        $arr = pdo_fetchall($sql);
        $logo = $arr['0']['logo'];
        $photo = "attachment/" . $arr['0']['photo'];
        $share_title = $arr['0']['share_title'];
        $share_desc = $arr['0']['share_desc'];
        $submit_url = $arr['0']['submit_url'];
        $rules_url = $arr['0']['rules_url'];
        $title = $arr['0']['title'];
        $cnzz = $arr['0']['cnzz'];
        include $this->template('my');
    }

    public function doMobilegetuser() {
        global $_W, $_GPC;
        $id = $_GPC['id'];
        $rid = $_GPC['rid'];
        $link = $this->createmobileUrl('logpost', array('do' => 'logpost', 'id' => $id, 'rid' => $rid));
        require_once IA_ROOT . "/addons/xhw_voice/jssdk.class.php";
        $weixin = new jssdk($jie = '0', $url = '');
        $wx = $weixin->get_sign();
        $weixin->get_code($link);
    }

    public function doMobilelogpost() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $this->Checkeduseragent();
        $rid = $_GPC['rid'];
        $sql = "SELECT * FROM " . tablename(xhw_voice_setting) . " WHERE weid = '{$_W['uniacid']}'";
        $arr = pdo_fetch($sql);
        $openidpass = $arr['openidpass'];
        if (empty($_GPC['code']) && $openidpass) {
            require_once IA_ROOT . "/addons/xhw_voice/jssdk.class.php";
            $weixin = new jssdk($jie = '0', $url = '');
            $wx = $weixin->get_sign();
            $weixin->get_code($_W['siteurl']);
        }
        if ($openidpass) {
            $followpass = $arr['followpass'];
            $code = $_GPC['code'];
            if ($code) {
                require_once IA_ROOT . "/addons/xhw_voice/jssdk.class.php";
                $weixin = new jssdk($jie = '0', $url = '');
                $wx = $weixin->get_sign();
                $data = $weixin->get_openid($code);
                $openid = $data['openid'];
                if ($followpass != '1') {
                    $sql = "SELECT * FROM " . tablename(mc_mapping_fans) . " WHERE openid = '$openid'";
                    $arr = pdo_fetch($sql);
                    if ($arr['follow'] != '1') {
                        echo "<script>alert('请仔细阅读活动说明');location.href='" . $follow_url . "';</script>";
                        die();
                    }
                }
            }
            if (empty($openid)) {
                $this->doCheckedMobile($rid);
            }
        } else {
            $this->doCheckedMobile($rid);
            $openid = $_W['fans']['from_user'];
        }
        $id = $_GPC['id'];
        $openid = $_W['fans']['from_user'];
        $sql = "SELECT * FROM " . tablename(xhw_voice) . " WHERE weid = '{$_W['uniacid']}' AND id = '$rid'";
        $arr = pdo_fetchall($sql);
        $mynum = $arr['0']['mynum'];
        $day = $arr['0']['day'];
        $follow_url = $arr['0']['follow_url'];
        $url = $this->createmobileUrl('item', array('do' => 'item', 'id' => $id));
        if ($arr['0']['starttime'] - time() > 0) {
            echo "<script>alert('活动还未开始!');location.href='$follow_url';</script>";
            die;
        } elseif ($arr['0']['endtime'] - time() < 0) {
            echo "<script>alert('活动已经结束!');location.href='$url';</script>";
            die;
        }
        $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        if ($day) {
            $today = 1;
        }
        $tomorrow = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
        $sql = "SELECT COUNT(*) FROM " . tablename(xhw_voice_log) . " WHERE openid = '{$openid}' AND rid = '{$rid}' AND numid = '{$id}' AND time > " . $today; //查询是否有投票记录
        if (pdo_fetchcolumn($sql)) {
            if ($day) {
                echo "<script>alert('您已经投过了，只能为同一个投票一次!');location.href='$url';</script>"; //不可重复投票限制
                die;
            }
            echo "<script>alert('您已经投过啦，每天可投一次,明天再来吧!');location.href='$url';</script>";
            die;
        }
        $mylognum = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('xhw_voice_log') . " WHERE openid = '{$openid}' AND rid = '{$rid}' AND time > " . $today . ' AND time < ' . $tomorrow);
        if ($mynum == "0") {
            $mynum = "100000";
        }
        if ($mylognum >= $mynum) {
            echo "<script>alert('您今天已达投票上限,明天再来吧!');location.href='$url';</script>";
            die;
        } else {
            $data = array(
                'rid' => $rid,
                'openid' => $openid,
                'numid' => $id,
                'ip' => $_W['clientip'],
                'time' => time()
            );
            $arr = pdo_fetchall("SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE id = '{$id}' AND pass = '1'");
            if (empty($arr)) {
                echo "<script>alert('还未审核通过，禁止投票！');location.href='$url';</script>";
                die();
            }
            pdo_insert(xhw_voice_log, $data);
            $num = intval($arr[0]['num']);
            $num = $num + 1;
            $data = array('num' => $num);
            pdo_update('xhw_voice_reg', $data, array('id' => $id));
            echo "<script>alert('投票成功，感谢您的支持！');location.href='$url';</script>";
            die;
        }
        include $this->template('item');
    }

    public function doMobilereg() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $this->Checkeduseragent();
        $weid = $_W['uniacid'];
        $id = $_GPC['id'];
        $openid = $_W['fans']['from_user'];
        $sql = "SELECT * FROM " . tablename('xhw_voice_reg') . " WHERE `id` = '$id' AND `openid` = '$openid'";
        $arr = pdo_fetchall($sql);
        if ($arr) {
            $phone = $arr[0]['phone'];
            $nickname = $arr[0]['nickname'];
        } else {
            $list = pdo_fetch("SELECT b.mobile,b.nickname,b.realname FROM " . tablename('mc_mapping_fans') . " a, " . tablename('mc_members') . " b WHERE a.openid = '$openid' AND a.uid=b.uid");
            $phone = $list['mobile'];
            $nickname = $list['realname'];
        }
        $title = $arr[0]['title'];
        $time = time();
        $sql = "SELECT * FROM " . tablename(xhw_voice) . " WHERE weid = '$weid' AND endtime > '$time'";
        $list = pdo_fetchall($sql);
        include $this->template('reg');
    }

    public function doMobilepost() {
        global $_W, $_GPC;
        $weid = $_W['uniacid'];
        $id = $_GPC['id'];
        $sql = "SELECT * FROM " . tablename('xhw_voice_reg') . " WHERE `id` = $id";
        $arr = pdo_fetchall($sql);
        if ($arr[0]['pass']) {
            echo "<script>alert('您的投稿已经通过审核，请勿修改');location.href='" . $this->createmobileUrl('item', array('do' => 'item', 'id' => $id)) . "';</script>";
            die();
        }
        $title = $_GPC['title'];
        $phone = $_GPC['phone'];
        $nickname = $_GPC['nickname'];
        $rid = $_GPC['rid'];
        if (empty($title) || empty($phone) || empty($nickname)) {
            echo "<script>alert('歌曲名、昵称和手机号不能为空，请重新填写');location.href='" . $this->createmobileUrl('reg', array('do' => 'reg', 'id' => $id)) . "';</script>";
            die();
        }
        $openid = $_W['fans']['from_user'];
        $data = array('title' => $title, 'phone' => $phone, 'nickname' => $nickname, 'rid' => $rid, 'time' => time());
        pdo_update('xhw_voice_reg', $data, array('id' => $id, 'openid' => $openid));
        echo "<script>alert('感谢您的参与，我们会尽快完成审核');location.href='" . $this->createmobileUrl('index', array('do' => 'index', 'id' => $rid)) . "';</script>";
        die();
        include $this->template('reg');
    }

    public function doMobileout() {
        $this->Checkedservername();
        $this->Checkeduseragent();
        include $this->template('out');
    }

    public function doMobileShare() {
        $this->Checkeduseragent();
        global $_W, $_GPC;
        $id = $_GPC['id'];
        $rid = $_GPC['rid'];
        $from = $_GPC['from'];
        $link = $this->createmobileUrl($from, array('do' => $from, 'id' => $id));
        $sql = "SELECT * FROM " . tablename(xhw_voice) . " WHERE weid = '{$_W['uniacid']}' AND id = '$rid'";
        $arr = pdo_fetch($sql);
        $sharenum = $arr['sharenum'] + 1;
        pdo_update(xhw_voice, array('sharenum' => $sharenum), array('id' => $rid));

        header("location: " . $link);
    }

    public function dowebproject() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition = "weid = '{$_W['uniacid']}'";
        if (!empty($_GPC['keywords'])) {
            $condition .= " AND id = '{$_GPC['keywords']}'";
        }
        $sql = "SELECT * FROM " . tablename(xhw_voice) . " WHERE $condition ORDER BY id LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $list = pdo_fetchall($sql);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(xhw_voice) . " WHERE $condition");
        $pager = pagination($total, $pindex, $psize);
        include $this->template('project');
    }

    public function dowebaddproject() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $id = (int) $_GPC['id'];
        load()->func('tpl');
        if ($id) {
            $subject = pdo_fetch("SELECT * FROM " . tablename(xhw_voice) . " WHERE id={$id} ORDER BY id DESC LIMIT 1");
        }
        // 删除
        if ($_GPC['op'] == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id FROM " . tablename(xhw_voice) . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，信息不存在或是已经被删除！');
            }
            pdo_delete(xhw_voice, array('id' => $id));
            message('删除成功！', referer(), 'success');
        }

        if (checksubmit()) {
            $data = array(
                'title' => $_GPC['title'],
                'photo' => $_GPC['photo'],
                'logo' => $_GPC['logo'],
                'smalltext' => $_GPC['smalltext'],
                'share_title' => $_GPC['share_title'],
                'share_desc' => $_GPC['share_desc'],
                //'starttime' => strtotime($_GPC['datelimit-start']),
                //'endtime' => strtotime($_GPC['datelimit-end']),
                'follow_url' => $_GPC['follow_url'],
                'rule_url' => $_GPC['rule_url'],
                'rules_url' => $_GPC['rules_url'],
                'submit_url' => $_GPC['submit_url'],
                'mynum' => $_GPC['mynum'],
                'anum' => $_GPC['anum'],
                'bnum' => $_GPC['bnum'],
                'day' => $_GPC['day'],
                'hot' => $_GPC['hot'],
                'cnzz' => $_GPC['cnzz']
            );
            if (!empty($id)) {
                pdo_update(xhw_voice, $data, array('id' => $id));
            } else {
                $data['weid'] = $_W['uniacid'];
                pdo_insert(xhw_voice, $data);
                $id = pdo_insertid();
            }

            message('更新成功！', referer(), 'success');
        }
        include $this->template('addproject');
    }

    public function dowebad() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $id = (int) $_GPC['id'];
        load()->func('tpl');
        if ($id) {
            $subject = pdo_fetch("SELECT * FROM " . tablename(xhw_voice) . " WHERE id={$id} ORDER BY id DESC LIMIT 1");
        }
        if (checksubmit()) {
            $data = array(
                'adpic' => $_GPC['adpic'],
                'adlink' => $_GPC['adlink'],
                'ad' => $_GPC['ad'],
                'adpass' => $_GPC['adpass']
            );
            pdo_update(xhw_voice, $data, array('id' => $id));
            message('更新成功！', referer(), 'success');
        }
        include $this->template('ad');
    }

    public function dowebsetting() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $weid = $_W['uniacid'];
        $url = $_SERVER['HTTP_REFERER'];
        $subject = pdo_fetch("SELECT * FROM " . tablename(xhw_voice_setting) . " WHERE weid = '{$weid}' ORDER BY id DESC LIMIT 1");
        if (checksubmit()) {
            if ($_GPC['settingpass']) {
                if (empty($_GPC['appid']) || empty($_GPC['appsecret']) || empty($_GPC['accesskey']) || empty($_GPC['secretkey']) || empty($_GPC['name'])) {
                    echo "<script>alert('启用高级接口时各个参数都不能为空');location.href='" . $url . "';</script>";
                    die();
                }
            }
            $data = array(
                'appid' => $_GPC['appid'],
                'appsecret' => $_GPC['appsecret'],
                'accesskey' => $_GPC['accesskey'],
                'secretkey' => $_GPC['secretkey'],
                'name' => $_GPC['name'],
                'link' => $_GPC['link'],
                'settingpass' => $_GPC['settingpass'],
                'openidpass' => $_GPC['openidpass'],
                'followpass' => $_GPC['followpass']
            );
            if (empty($subject)) {
                $data['weid'] = $weid;
                pdo_insert(xhw_voice_setting, $data);
            } else {
                pdo_update(xhw_voice_setting, $data, array('weid' => $weid));
            }
            message('更新成功！', referer(), 'success');
        }
        include $this->template('setting');
    }

    public function dowebadimg() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $id = (int) $_GPC['id'];
        load()->func('tpl');
        if ($id) {
            $subject = pdo_fetch("SELECT * FROM " . tablename(xhw_voice) . " WHERE id={$id} ORDER BY id DESC LIMIT 1");
            $adimg = explode("|", $subject['adimg']);
            $adimglink = explode("|", $subject['adimglink']);
        }
        if (checksubmit()) {
            $data = array(
                'adimg' => $_GPC['adimg1'] . "|" . $_GPC['adimg2'] . "|" . $_GPC['adimg3'] . "|" . $_GPC['adimg4'] . "|" . $_GPC['adimg5'],
                'adimglink' => $_GPC['adimglink1'] . "|" . $_GPC['adimglink2'] . "|" . $_GPC['adimglink3'] . "|" . $_GPC['adimglink4'] . "|" . $_GPC['adimglink5']
            );
            pdo_update(xhw_voice, $data, array('id' => $id));
            message('更新成功！', referer(), 'success');
        }
        include $this->template('adimg');
    }

    public function dowebvote() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        if ($_GPC['id'] == "") {
            $condition = "weid = '{$_W['uniacid']}' AND pass='1'";
        } else {
            $condition = "weid = '{$_W['uniacid']}' AND pass='1' AND rid='{$_GPC['id']}'";
        }
        if (!empty($_GPC['keywords'])) {
            $condition .= " AND id = '{$_GPC['keywords']}'";
        }
        $sql = "SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE $condition ORDER BY -num LIMIT " . ($pindex - 1) * $psize . ',' . $psize; //按大小排序
        $list = pdo_fetchall($sql);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(xhw_voice_reg) . " WHERE $condition");
        $pager = pagination($total, $pindex, $psize);
 
        include $this->template('vote');
    }

    public function doweblog() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        if ($_GPC['numid']) {
            $condition = "rid='{$_GPC['id']}' AND numid='{$_GPC['numid']}'";
        } elseif ($_GPC['openid']) {
            $condition = "rid='{$_GPC['id']}' AND openid='{$_GPC['openid']}'";
        } else {
            $condition = "rid='{$_GPC['id']}'";
        }
        if ($_GPC['op'] == 'delete') {
            pdo_delete(xhw_voice_log, array('rid' => $_GPC['id']));
            message('投票记录删除成功！', referer(), 'success');
        }
        $sql = "SELECT * FROM " . tablename(xhw_voice_log) . " WHERE $condition ORDER BY -id LIMIT " . ($pindex - 1) * $psize . ',' . $psize; //按大小排序
        $list = pdo_fetchall($sql);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(xhw_voice_log) . " WHERE $condition");
        $pager = pagination($total, $pindex, $psize);

        include $this->template('log');
    }

    public function dowebvoice() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        if ($_GPC['id'] == "") {
            $condition = "weid = '{$_W['uniacid']}' AND pass='0'";
        } else {
            $condition = "weid = '{$_W['uniacid']}' AND pass='0' AND rid='{$_GPC['id']}'";
        }
        if (!empty($_GPC['keywords'])) {
            $condition .= " AND id = '{$_GPC['keywords']}'";
        }
        $sql = "SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE $condition ORDER BY -id LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $list = pdo_fetchall($sql);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(xhw_voice_reg) . " WHERE $condition");
        $pager = pagination($total, $pindex, $psize);


        include $this->template('voice');
    }

    public function doWebpost() {
        global $_W, $_GPC;
        $this->Checkedservername();
        $weid = $_W['uniacid'];
        $id = (int) $_GPC['id'];
        load()->func('tpl');
        if ($id) {
            $subject = pdo_fetch("SELECT * FROM " . tablename(xhw_voice_reg) . " WHERE id={$id} ORDER BY id DESC LIMIT 1");
        }
        $setting = pdo_fetch("SELECT * FROM " . tablename(xhw_voice_setting) . " WHERE weid = '{$_W['uniacid']}' ORDER BY id DESC LIMIT 1");

        // 删除
        if ($_GPC['op'] == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id FROM " . tablename(xhw_voice_reg) . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，信息不存在或是已经被删除！');
            }
            pdo_delete(xhw_voice_reg, array('id' => $id));
            message('删除成功！', referer(), 'success');
        }
        $arr = pdo_fetch("SELECT * FROM " . tablename('account_wechats') . " WHERE uniacid = :uniacid", array(':uniacid' => $weid));
        $tokenarr = iunserializer($arr['access_token']);
        $access_token = $tokenarr['token'];



        if ($_GPC['token'] == '1') {
            $appid = $arr['key'];
            $secret = $arr['secret'];
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret;
            $result = json_decode(file_get_contents($url), 1);
            $access_token = $result["access_token"];
            if ($access_token) {
                $record = array();
                $record['token'] = $access_token;
                $record['expire'] = time() + 3600;
                $row = array();
                $row['access_token'] = iserializer($record);
                pdo_update('account_wechats', $row, array('uniacid' => $weid));
            }
            message('刷新token成功', referer(), 'success');
        }
        // 通过
        if ($_GPC['op'] == 'pass') {
            $id = intval($_GPC['id']);
            $rid = intval($_GPC['rid']);
            $row = pdo_fetch("SELECT id FROM " . tablename(xhw_voice_reg) . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，信息不存在或是已经被删除！');
            }
            pdo_update(xhw_voice_reg, array('pass' => '1', 'rid' => $rid), array('id' => $id));
            message('审核通过！', referer(), 'success');
        }

        if (checksubmit()) {
            $data = array(
                'title' => $_GPC['title'],
                'mp3' => $_GPC['mp3'],
                'nickname' => $_GPC['nickname'],
                'num' => $_GPC['num'],
                'sharenum' => $_GPC['sharenum'],
                'avatar' => $_GPC['avatar'],
                'phone' => $_GPC['phone'],
                'pass' => $_GPC['pass'],
                'rid' => $_GPC['rid']
            );

            if (!empty($id)) {
                pdo_update(xhw_voice_reg, $data, array('id' => $id));
            } else {
                $data['weid'] = $_W['uniacid'];
                pdo_insert(xhw_voice_reg, $data);
                $id = pdo_insertid();
            }

            message('更新成功！', referer(), 'success');
        }
        include $this->template('post');
    }
 

}
