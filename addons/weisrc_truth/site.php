<?php
/**
 * 真心话
 *

 */
defined('IN_IA') or exit('Access Denied');
define('RES', '../addons/weisrc_truth/template/themes/');
include "../addons/weisrc_truth/model.php";

class weisrc_truthModuleSite extends WeModuleSite
{
    public $cur_version = '20140917';
    public $modulename = 'weisrc_truth';

    public $_debug = '1'; //default:0
    public $_weixin = '1'; //default:1

    public $_appid = '';
    public $_appsecret = '';
    public $_accountlevel = '';

    public $_weid = '';
    public $_fromuser = '';
    public $_nickname = '';
    public $_headimgurl = '';

    public $_auth2_openid = '';
    public $_auth2_nickname = '';
    public $_auth2_headimgurl = '';

    function __construct()
    {
        global $_W, $_GPC;
        $this->_fromuser = $_W['fans']['from_user'];//debug
        if ($_SERVER['HTTP_HOST'] == '127.0.0.1') {
            $this->_fromuser = 'debug';
        }
        $this->_weid = $_W['uniacid'];
        $account = account_fetch($this->_weid);

        $this->_auth2_openid = 'auth2_openid_'.$_W['uniacid'];
        $this->_auth2_nickname = 'auth2_nickname_'.$_W['uniacid'];
        $this->_auth2_headimgurl = 'auth2_headimgurl_'.$_W['uniacid'];

        $this->_appid = '';
        $this->_appsecret = '';
        $this->_accountlevel = $account['level']; //是否为高级号

        if ($this->_accountlevel == 4) {
            $this->_appid = $account['key'];
            $this->_appsecret = $account['secret'];
        }

        if (!empty($this->_appid) && !empty($this->_appsecret)) {
            require_once IA_ROOT . '/framework/class/account.class.php';
            $acc = WeAccount::create($this->_weid);
            $_W['account']['jssdkconfig'] = $acc->getJssdkConfig();
            $accountInfo = $acc->fetchAccountInfo();
            $_W['account']['access_token'] = $accountInfo['access_token'];
            $_W['account']['jsapi_ticket'] = $accountInfo['jsapi_ticket'];
        }
    }

    //首页
    public function doMobileIndex()
    {
        global $_W, $_GPC;
        $from_user = $this->_fromuser;
        $weid = $this->_weid;

        $method = 'index';//method
        $authurl = $_W['siteroot'] .'app/'. $this->createMobileUrl($method, array(), true) . '&authkey=1';
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true);

        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->toAuthUrl($url);
                }
            }
        }

        $where = " WHERE weid={$weid} AND status=1 AND (from_user='' OR from_user='{$from_user}') ";
        $list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_question') . " {$where} ORDER BY rand() LIMIT 10", array(), 'id');

        $replylist = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_reply') . " WHERE weid=:weid AND from_user=:from_user ", array(':weid' => $weid, ':from_user' => $from_user), 'qid');

        $setting = pdo_fetch("select * from " . tablename($this->modulename . '_setting') . " where weid =:weid LIMIT 1", array(':weid' => $weid));

        //#share
        $share_image = empty($setting['share_image']) ? $_W['siteroot'] . './addons/weisrc_truth/template/themes/images/truth.jpg': tomedia($setting['share_image']);
        $share_title = empty($setting['share_title']) ? '和朋友交换真心话' : $setting['share_title'];
        $share_desc = empty($setting['share_desc']) ? '和朋友交换真心话' : $setting['share_desc'];
        $share_url = empty($setting['share_url']) ? $_W['siteroot'] . 'app/' . $this->createMobileUrl('index') : $setting['share_url'];

        include $this->template('truth');
    }

    public function doMobileQuestion()
    {
        global $_W, $_GPC;
        $from_user = $this->_fromuser;
        $weid = $this->_weid;
        $qid = intval($_GPC['id']);//问题编号

        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        }

        $question = pdo_fetch("select * from " . tablename($this->modulename . '_question') . " where weid =:weid AND id=:id LIMIT 1", array(':weid' => $_W['uniacid'], ':id' => $qid));

        if (empty($question)) {
            message('问题不存在呢!', $this->createMobileUrl('index', array(), true));
        } else {
            $titlte = "{$nickname}回答了真心话:" . $question['title'];
        }

        //当前用户回答
        $reply = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_reply') . " WHERE qid=:qid AND from_user=:from_user LIMIT 1", array(':qid' => $qid, ':from_user' => $from_user));
        $replys = pdo_fetchall("select * from " . tablename($this->modulename . '_reply') . " where parentid=:id LIMIT 1", array(':id' => $reply['id']));

        $setting = pdo_fetch("select * from " . tablename($this->modulename . '_setting') . " where weid =:weid LIMIT 1", array(':weid' => $weid));

        //#share
        $share_image = empty($setting['share_image']) ? $_W['siteroot'] . './addons/weisrc_truth/template/themes/images/truth.jpg': tomedia($setting['share_image']);
        $share_title = "{$nickname}回答了真心话:" . $question['title'];
        $share_desc = '来和朋友交换真心话把!';
        $share_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('reply', array('rid' => $reply['id']));
        include $this->template('question');
    }

    public function doMobileReply()
    {
        global $_W, $_GPC;
        $from_user = $this->_fromuser;
        $weid = $this->_weid;
        $rid = intval($_GPC['rid']);//回答编号

        $method = 'reply';//method
        $authurl = $_W['siteroot'] .'app/'. $this->createMobileUrl($method, array('rid' => $rid), true) . '&authkey=1';
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('rid' => $rid), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->toAuthUrl($url);
                }
            }
        }

        //分享的用户回答
        $reply = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_reply') . " WHERE id=:id LIMIT 1", array(':id' => $rid));
        if (empty($reply)) {
            message('信息不存在!', $this->createMobileUrl('index', array(), true));
        }

        //当前用户回答
        $curreply = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_reply') . " WHERE parentid=:parentid AND from_user=:from_user LIMIT 1", array(':parentid' => $reply['id'], ':from_user' => $from_user));

        //所属题目
        $qid = intval($reply['qid']);
        $question = pdo_fetch("select * from " . tablename($this->modulename . '_question') . " where weid =:weid AND id=:id LIMIT 1", array(':weid' => $_W['uniacid'], ':id' => $qid));

        if (empty($question)) {
            message('问题不存在呢!', $this->createMobileUrl('index', array(), true));
        } else {
            if (empty($curreply)) {
                $title = "和{$reply['nickname']}交换真心话";
            } else {
                $title = "{$curreply['nickname']}回答了真心话：如果死后在奈何桥看到孟婆，给你喝孟婆汤，你会说什么？";
            }
        }

        $setting = pdo_fetch("select * from " . tablename($this->modulename . '_setting') . " where weid =:weid LIMIT 1", array(':weid' => $weid));

        //#share
        $share_image = empty($setting['share_image']) ? $_W['siteroot'] . './addons/weisrc_truth/template/themes/images/truth.jpg': tomedia($setting['share_image']);
        $share_title = empty($setting['share_title']) ? '和朋友交换真心话' : $setting['share_title'];
        $share_desc = empty($setting['share_desc']) ? '和朋友交换真心话' : $setting['share_desc'];
        $share_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('reply', array('rid' => $rid));

        include $this->template('reply');
    }

    public function doMobileSendAnswer()
    {
        global $_W, $_GPC;
        $from_user = $this->_fromuser;
        $weid = $this->_weid;

        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        }

        if (checksubmit('btnsubmit')) {
            $url = $this->createMobileUrl('index', array(), true);
        }

        $rid = intval($_GPC['rid']);
        $answer = trim($_GPC['answer']);

        if (empty($answer)) {
            $result = array(
                'status' => 1
            );
            echo json_encode($result);
            exit;
        }

        $reply = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_reply') . " WHERE id=:id LIMIT 1", array(':id' => $rid));

        $data = array(
            'weid' => $weid,
            'qid' => $reply['qid'],
            'parentid' => $rid,
            'from_user' => $from_user,
            'nickname' => $nickname,
            'headimgurl' => $headimgurl,
            'content' => $answer,
            'dateline' => TIMESTAMP
        );
        pdo_insert($this->modulename . '_reply', $data);
        $result = array(
            'status' => 1
        );

        echo json_encode($result);
        exit;
    }

    public function doMobileSendReply()
    {
        global $_W, $_GPC;
        $from_user = $this->_fromuser;
        $weid = $this->_weid;

        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        }

        if (checksubmit('btnsubmit')) {
            $url = $this->createMobileUrl('index', array(), true);

            $query_question = trim($_GPC['q']);//问题
            $answer = trim($_GPC['a']);//答案

            if (empty($answer)) {
                message('这也太简洁了……要不再多说两句？', $this->createMobileUrl('index', array(), true));
            }

            //根据问题查出questionid
            $question = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_question') . " WHERE title=:title AND weid=:weid LIMIT 1", array(':title' => $query_question, ':weid' => $weid));

            if (empty($question)) {
                $data = array(
                    'weid' => $weid,
                    'from_user' => $from_user,
                    'title' => $query_question,
                    'displayorder' => 0,
                    'status' => 1,
                    'dateline' => TIMESTAMP
                );

                pdo_insert($this->modulename . '_question', $data);
                $qid = pdo_insertid();
            } else {
                $qid = $question['id'];
            }

            $reply = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_reply') . " WHERE id=:id AND from_user=:from_user LIMIT 1", array(':id' => $qid, ':from_user' => $from_user));

            if (!empty($reply)) {
                message('您已经回答过该问题拉!', $this->createMobileUrl('question', array('id' => $qid), true));
            }

            $data = array(
                'weid' => $weid,
                'qid' => $qid,
                'from_user' => $from_user,
                'parentopenid' => '',
                'nickname' => $nickname,
                'headimgurl' => $headimgurl,
                'content' => $answer,
                'sharecount' => 0,
                'status' => 1,
                'dateline' => TIMESTAMP
            );

            pdo_insert($this->modulename . '_reply', $data);
            $rid = pdo_insertid();
            header("location:" . $this->createMobileUrl('question', array('id' => $qid), true));
        }
    }

    public function doMobileUpdateShare()
    {
        global $_W, $_GPC;
        $from_user = $this->_fromuser;
        $weid = $this->_weid;
        $qid = intval($_GPC['qid']);

        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        }

        $reply = pdo_fetch("select * from " . tablename($this->modulename . '_reply') . " where qid =:qid AND from_user=:from_user LIMIT 1", array(':qid' => $qid, ':from_user' => $from_user));

        if (!empty($reply)) {
            pdo_update($this->modulename . '_reply', array('sharecount' => $reply['sharecount'] + 1), array('id' => $reply['id']));
        }

        $result = array('status' => 1);
        echo json_encode($result);
    }

    public function doWebQuestion()
    {
        global $_W, $_GPC;
        checklogin();
        load()->func('tpl');

        $url = $this->createWebUrl('question', array('op' => 'display'));
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        $totalcount = pdo_fetchcolumn("SELECT COUNT(1) as count FROM ".tablename($this->modulename . '_question')."  WHERE weid = :weid", array(':weid' => $_W['uniacid']));
        $nocheckcount = pdo_fetchcolumn("SELECT COUNT(1) as count FROM ".tablename($this->modulename . '_question')."  WHERE weid = :weid AND status=0", array(':weid' => $_W['uniacid']));
        $checkcount = pdo_fetchcolumn("SELECT COUNT(1) as count FROM ".tablename($this->modulename . '_question')."  WHERE weid = :weid AND status=1", array(':weid' => $_W['uniacid']));

        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("select * from " . tablename($this->modulename . '_question') . " where id=:id AND weid =:weid", array(':id' => $id, ':weid' => $_W['uniacid']));

            if (checksubmit('submit')) {
                $data = array(
                    'weid' => $_W['uniacid'],
                    'title' => trim($_GPC['title']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'status' => intval($_GPC['status']),
                    'dateline' => TIMESTAMP,
                );

                if (!empty($item)) {
                    pdo_update($this->modulename . '_question', $data, array('id' => $id, 'weid' => $_W['uniacid']));
                } else {
                    pdo_insert($this->modulename . '_question', $data);
                }
                message('操作成功', $url, 'success');
            }
        } elseif ($operation == 'display') {
            if (isset($_GPC['status'])) {
                $status = intval($_GPC['status']);
            } else {
                $status = -1;
            }

            if (checksubmit('submit')) { //排序
                if (is_array($_GPC['displayorder'])) {
                    foreach ($_GPC['displayorder'] as $id => $val) {
                        $data = array('displayorder' => intval($_GPC['displayorder'][$id]));
                        pdo_update($this->modulename . '_question', $data, array('id' => $id, 'weid' => $_W['uniacid']));
                    }
                }
                message('操作成功!', $url);
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $where = ' ';

            if ($status != -1) {
                if ($status == 0) {
                    $where = '';
                }
                $where .= " AND status={$status} ";
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_question') . " WHERE weid=".$_W['uniacid']." {$where} ORDER BY displayorder DESC,id DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}", array(), 'id');

            if (!empty($list)) {
                $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->modulename . '_question') . " WHERE weid=".$_W['uniacid']." {$where}");
                $pager = pagination($total, $pindex, $psize);
            }

        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $data = pdo_fetch("SELECT id FROM " . tablename($this->modulename . '_question') . " WHERE id = :id", array(':id' => $id));
            if (empty($data)) {
                message('抱歉，不存在或是已经被删除！', $this->createWebUrl('_question', array('op' => 'display')), 'error');
            }
            pdo_delete($this->modulename . '_question', array('id' => $id, 'weid' => $_W['uniacid']));
            message('删除成功！', $this->createWebUrl('question', array('op' => 'display')), 'success');
        } elseif ($operation == 'deleteall') {
            $rowcount = 0;
            $notrowcount = 0;
            foreach ($_GPC['idArr'] as $k => $id) {
                $id = intval($id);
                if (!empty($id)) {
                    $question = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_question') . " WHERE id = :id", array(':id' => $id));
                    if (empty($question)) {
                        $notrowcount++;
                        continue;
                    }
                    if ($question['parentid'] == 0) {
                        pdo_delete($this->modulename . '_question', array('parentid' => $id, 'weid' => $_W['weid']));
                    }
                    pdo_delete($this->modulename . '_question', array('id' => $id, 'weid' => $_W['weid']));
                    $rowcount++;
                }
            }
            $this->message("操作成功！共删除{$rowcount}条数据,{$notrowcount}条数据不能删除!", '', 0);
        } elseif ($operation == 'checkall') {
            $rowcount = 0;
            $notrowcount = 0;
            foreach ($_GPC['idArr'] as $k => $id) {
                $id = intval($id);
                if (!empty($id)) {
                    $question = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_question') . " WHERE id = :id", array(':id' => $id));
                    if (empty($question)) {
                        $notrowcount++;
                        continue;
                    }

                    $data = empty($question['status']) ? 1 : 0;
                    pdo_update($this->modulename . '_question', array('status' => $data), array("id" => $id, "weid" => $_W['weid']));
                    $rowcount++;
                }
            }
            $this->message("操作成功！共审核{$rowcount}条数据,{$notrowcount}条数据不能删除!!", '', 0);
        }

        include $this->template('question');
    }

    public function doWebSetting()
    {
        global $_W, $_GPC;
        checklogin();
        load()->func('tpl');

        $item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid", array(':weid' => $_W['uniacid']));
        if (!empty($item)) {
            if (!empty($item['share_image'])) {
                $share_image = tomedia($item['share_image']);
            }
        }

        if (checksubmit('submit')) {
            $data = array(
                'weid' => $_W['weid'],
                'share_title' => trim($_GPC['share_title']),
                'share_desc' => trim($_GPC['share_desc']),
                'share_cancel' => trim($_GPC['share_cancel']),
                'share_url' => trim($_GPC['share_url']),
                'follow_url' => trim($_GPC['follow_url'])
            );

            if (!empty($_GPC['share_image'])) {
                $data['share_image'] = $_GPC['share_image'];
                load()->func('file');
                file_delete($_GPC['share_image-old']);
            }

            if (!empty($item)) {
                pdo_update($this->modulename . '_setting', $data, array('weid' => $_W['uniacid']));
            } else {
                pdo_insert($this->modulename . '_setting', $data);
            }
            message('更新成功！', $this->createWebUrl('setting'), 'success');
        }
        include $this->template('setting');
    }

    function authorization()
    {
        $host = get_domain();
        return base64_encode($host);
    }

    function code_compare($a, $b)
    {
        if ($this->_debug == 1) {
            if ($_SERVER['HTTP_HOST'] == '127.0.0.1') {
                return true;
            }
        }
        if ($a != $b) {
            message(base64_decode("5a+55LiN6LW377yM5oKo5L2/55So55qE57O757uf5piv55Sx6Z2e5rOV5rig6YGT5Lyg5pKt55qE77yM6K+35pSv5oyB5q2j54mI44CC6LSt5Lmw6L2v5Lu26K+36IGU57O7UVExNTU5NTc1NeOAgg=="));
        }
    }

    function isWeixin()
    {
        if ($this->_weixin == 1) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            if (!strpos($userAgent, 'MicroMessenger')) {
                include $this->template('s404');
                exit();
            }
        }
    }

    //auth2
    public function toAuthUrl($url)
    {
        global $_W;
        $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->_appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_base&state=0#wechat_redirect";
        header("location:$oauth2_code");
    }

    public function oauth2($authurl)
    {
        global $_GPC, $_W;
        load()->func('communication');
        $state = $_GPC['state']; //1为关注用户, 0为未关注用户
        $code = $_GPC['code'];

        $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->_appid . "&secret=" . $this->_appsecret . "&code=" . $code . "&grant_type=authorization_code";

        $content = ihttp_get($oauth2_code);

        $token = @json_decode($content['content'], true);

        if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
            echo '<h1>获取微信公众号授权' . $code . '失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
            exit;
        }
        $from_user = $token['openid'];

        if ($this->_accountlevel != 4) { //普通号
            $authkey = intval($_GPC['authkey']);
            if ($authkey == 0) {
                $url = $authurl;
                $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->_appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
                header("location:$oauth2_code");
            }
        } else {
            //再次查询是否为关注用户

            $follow = pdo_fetchcolumn("SELECT follow FROM ".tablename('mc_mapping_fans')." WHERE openid = :openid AND acid = :acid", array(':openid' => $from_user, ':acid' => $_W['uniacid']));


            if ($follow == 1) { //关注用户直接获取信息
                $state = 1;
            } else { //未关注用户跳转到授权页
                $url = $authurl;
                $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->_appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
                header("location:$oauth2_code");
            }
        }

        //未关注用户和关注用户取全局access_token值的方式不一样
        if ($state == 1) {
            $oauth2_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->_appid . "&secret=" . $this->_appsecret . "";
            $content = ihttp_get($oauth2_url);
            $token_all = @json_decode($content['content'], true);
            if (empty($token_all) || !is_array($token_all) || empty($token_all['access_token'])) {
                echo '<h1>获取微信公众号授权失败[无法取得access_token], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
                exit;
            }
            $access_token = $token_all['access_token'];
            $oauth2_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $from_user . "&lang=zh_CN";
        } else {
            $access_token = $token['access_token'];
            $oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $from_user . "&lang=zh_CN";
        }

        //使用全局ACCESS_TOKEN获取OpenID的详细信息
        $content = ihttp_get($oauth2_url);
        $info = @json_decode($content['content'], true);
        if (empty($info) || !is_array($info) || empty($info['openid']) || empty($info['nickname'])) {
            echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>' . 'state:' . $state . 'nickname' . 'weid:';
            exit;
        }
        $headimgurl = $info['headimgurl'];
        $nickname = $info['nickname'];
        //设置cookie信息

        setcookie($this->_auth2_headimgurl, $headimgurl, time() + 3600 * 24);
        setcookie($this->_auth2_nickname, $nickname, time() + 3600 * 24);
        setcookie($this->_auth2_openid, $from_user, time() + 3600 * 24);
        return $info;
    }

    public function showMessage($msg, $status = 0)
    {
        $result = array('message' => $msg, 'status' => $status);
        echo json_encode($result);
        exit;
    }

    public function message($error, $url = '', $errno = -1) {
        $data = array();
        $data['errno'] = $errno;
        if (!empty($url)) {
            $data['url'] = $url;
        }
        $data['error'] = $error;
        echo json_encode($data);
        exit;
    }
}