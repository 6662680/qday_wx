<?php

/**
 * 拔河抽奖模块
 *
 */
defined('IN_IA') or exit('Access Denied');
define('RES', '../addons/hl_tug/style/');

class hl_tugModuleSite extends WeModuleSite {

    public function doWebFormDisplay() {
        global $_W, $_GPC;
        load()->func('tpl');
        $result = array('error' => 0, 'message' => '', 'content' => '');
        $result['content']['id'] = $GLOBALS['id'] = 'add-row-news-' . $_W['timestamp'];
        $result['content']['html'] = $this->template('item', TEMPLATE_FETCH);
        exit(json_encode($result));
    }

    public function doWebAwardlist() {
        global $_GPC, $_W;
        checklogin();
        $id = intval($_GPC['id']);
        if (checksubmit('delete')) {
            pdo_delete('hl_tug_winner', " id  IN  ('" . implode("','", $_GPC['select']) . "')");
            message('删除成功！', $this->createWebUrl('awardlist', array('id' => $id, 'page' => $_GPC['page'])));
        }
        if (!empty($_GPC['wid'])) {
            $wid = intval($_GPC['wid']);

            pdo_update('hl_tug_winner', array('status' => intval($_GPC['status'])), array('id' => $wid));
            message('标识领奖成功！', $this->createWebUrl('awardlist', array('id' => $id, 'page' => $_GPC['page'])));
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 50;
        $where = '';
        $starttime = !empty($_GPC['start']) ? strtotime($_GPC['start']) : TIMESTAMP;
        $endtime = !empty($_GPC['start']) ? strtotime($_GPC['end']) : TIMESTAMP;
        if (!empty($starttime) && $starttime == $endtime) {
            $endtime = $endtime + 86400 - 1;
        }
        $condition = array(
            'isregister' => array(
                '',
                " AND b.nickname <> ''",
                " AND b.nickname = ''",
            ),
            'qq' => " AND b.qq ='{$_GPC['profilevalue']}'",
            'mobile' => " AND b.mobile ='{$_GPC['profilevalue']}'",
            'nickname' => " AND b.nickname ='{$_GPC['profilevalue']}'",
            'starttime' => " AND a.createtime >= '$starttime'",
            'endtime' => " AND a.createtime <= '$endtime'",
        );
        if (!isset($_GPC['isregister'])) {
            $_GPC['isregister'] = 1;
        }
        $where .= $condition['isregister'][$_GPC['isregister']];

        if (!empty($_GPC['profile'])) {
            $where .= $condition[$_GPC['profile']];
        }
        if (!empty($_GPC['award'])) {
            $where .= $condition[$_GPC['award']];
        }
        /*
          if (!empty($starttime)) {
          $where .= $condition['starttime'];
          }
          if (!empty($endtime)) {
          $where .= $condition['endtime'];
          }
         */
        $sql = "SELECT a.id,a.status,(a.endtime -a.createtime  ) as usertime ,a.createtime, b.nickname, b.mobile, b.qq FROM " . tablename('hl_tug_winner') . " AS a 
                            LEFT JOIN " . tablename('mc_mapping_fans '). " AS c ON c.openid = a.openid	      
                            LEFT JOIN " . tablename('mc_members') . " AS b ON c.uid = b.uid
                       WHERE a.rid = '$id' $where  ORDER BY usertime ASC LIMIT " . ($pindex - 1) * $psize . ",{$psize}";
        $list = pdo_fetchall($sql);

        if (!empty($list)) {
            $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('hl_tug_winner') . " AS a
	          LEFT JOIN " . tablename('mc_mapping_fans '). " AS c ON c.openid = a.openid	      
                            LEFT JOIN " . tablename('mc_members') . " AS b ON c.uid = b.uid
                             WHERE a.rid = '$id' $where");
            $pager = pagination($total, $pindex, $psize);
        }
        load()->func('tpl');
        include $this->template('awardlist');
    }

    public function doWebDelete() {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        $sql = "SELECT id FROM " . tablename('hl_tug_award') . " WHERE `id`=:id";
        $row = pdo_fetch($sql, array(':id' => $id));
        if (empty($row)) {
            message('抱歉，奖品不存在或是已经被删除！', '', 'error');
        }
        if (pdo_delete('hl_tug_award', array('id' => $id))) {
            message('删除奖品成功', '', 'success');
        }
    }

    public function doWebClear() {
        global $_W, $_GPC;
        $rid = intval($_GPC['rid']);
        //$sql = "DELETE FROM " . tablename('hl_tug_award') . " WHERE `rid`=:rid and weid=".$_W['uniacid'];
        //$row = pdo_query($sql, array(':id'=>$id));

        if (pdo_delete('hl_tug_winner', array('rid' => $rid))) {
            message('清空数据成功', '', 'success');
        }
    }

    public function doMobileIndex() {
        global $_GPC, $_W;
        $this->CheckCookie();
        
        $oauth_openid = "hl_bug5" . $_W['uniacid'];
        $cookies= iunserializer($_COOKIE[$oauth_openid]);
        
        $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
            echo "404 error";
            exit;
        }


        $id = intval($_GPC['id']);
        $from_user = $_W['fans']['from_user'];
        //checkauth();
        if (empty($from_user)) {
            message('非法访问，请重新发送消息进入拔河！');
        }
 
        $reply = pdo_fetch("SELECT * FROM " . tablename('hl_tug_reply') . " WHERE rid = '$id' LIMIT 1");


        if (intval($_GPC['whoteam'])) {
            $jnum = pdo_fetchcolumn("select count(id) FROM " . tablename('hl_tug_winner') . " WHERE  rid = '$id' and whoteam>0 ");
            $whoteam = intval($_GPC['whoteam']);   
            if($whoteam==3){
                $whoteam = rand(1,2);
            }
            if ($jnum > $reply['joinlimit']) {
                //$whoteam = 5;
            }
            $data = array('whoteam' => $whoteam);
            pdo_update('hl_tug_winner', $data, array('from_user' => $from_user, 'rid' => $id));

            $url = $this->createMobileUrl('hl_tug', array('id' => $_GPC['id']));
            header("location:$url");
        }
        if (empty($reply)) {
            message('不存在此活动，请重新发送消息进入拔河！');
        }
        $profiles = pdo_fetch("select * FROM " . tablename('hl_tug_winner') . " WHERE from_user='$from_user' AND rid = '$id' LIMIT 1 ");
 
        $weid = $_W['uniacid'];
     
        if (!$profiles) {
            load()->model('mc');
            $p = mc_fetch($_W['member']['uid'],array('avatar'));
            $avatar = $p['avatar'];
            $insert = array(
                'rid' => $id,
                'weid' => $weid,
                'from_user' => $_W['fans']['from_user'],
                'createtime' => TIMESTAMP,
                'pic' => $cookies['headimgurl'],
                'uname' =>$cookies['nickname'],
            );
            pdo_insert('hl_tug_winner', $insert);
            $profiles['whoteam'] = 0;
        }
        load()->func('tpl');
        include $this->template('index');
    }

    public function doMobilehl_tug() {
        $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
            echo "404 error";
            exit;
        }
        global $_GPC, $_W;
        $fromuser = $_W['fans']['from_user'];

        //checkauth();
        if (empty($fromuser)) {
            message('非法访问，请重新发送消息进入拔河！');
        }

        $id = intval($_GPC['id']);
        $reply = pdo_fetch("SELECT * FROM " . tablename('hl_tug_reply') . " WHERE rid = '$id' LIMIT 1");

        if (empty($reply)) {
            message('不存在此活动，请重新发送消息进入拔河！');
        }

        $profiles = pdo_fetch("select * FROM " . tablename('hl_tug_winner') . " WHERE from_user='$fromuser' AND rid = '$id' LIMIT 1 ");
        $whoteam = $profiles['whoteam'];
//        if ($profiles['whoteam'] > 0 || $profiles['whoteam'] < 3) {
//            $whoteam = 1;
//        } else {
//            $whoteam = 0;
//        }
        if ($reply['status'] == 1) {
            if ($reply['starttime'] > TIMESTAMP) {
                $reply['lefttime'] = $reply['starttime'] - TIMESTAMP;
            } else {
                $reply['lefttime'] = 3;
            }
        }
        if ($reply['shakestatus'] == 0) {

            $reply['lefttime'] = 1000000;
        }
        load()->func('tpl');
        include $this->template('tug');
    }

    public function doMobileRegister() {
        global $_GPC, $_W;
        $title = '信息登记';
        $id = $_GPC['id'];
        //$profile = pdo_fetch("SELECT realname,nickname,avatar,mobile FROM " . tablename('fans') . " WHERE from_user = '{$_W['fans']['from_user']}' AND weid=" . $_W['uniacid'] . "  LIMIT 1");
        load()->model('mc');
        $profile = mc_fetch($_W['member']['uid'],array('realname','nickname','avatar','mobile'));
        
        $member = fans_require($_W['fans']['from_user'], array('realname', 'mobile', 'nickname'));
        if (!empty($_GPC['submit'])) {

            $data = array(
                'realname' => $_GPC['realname'],
                'mobile' => $_GPC['mobile'],
                'nickname' => $_GPC['nickname'],
            );

            if (empty($data['realname'])) {
                die('<script>alert("请填写您的真实姓名！");location.reload();</script>');
            }
            if (empty($data['mobile'])) {
                die('<script>alert("请填写您的手机号码！");location.reload();</script>');
            }

            fans_update($_W['fans']['from_user'], $data);
            die('<script>alert("登记成功！");location.href = "' . $this->createMobileUrl('index', array('id' => $_GPC['id'])) . '";</script>');
        }
        load()->func('tpl');
        include $this->template('register');
    }

    //计次提交
    public function doMobilePostJson() {
        global $_GPC, $_W;
        if (empty($_W['fans']['from_user'])) {
            message('非法访问，请重新发送消息进入拔河！');
        }
        $from_user = $_W['fans']['from_user'];
        $id = intval($_GPC['id']);
        $count = intval($_GPC['ucount']);
        $reply = pdo_fetch("SELECT * FROM " . tablename('hl_tug_reply') . " WHERE rid = '$id' LIMIT 1");

        if (empty($reply)) {
            message('不存在此活动，请重新发送消息进入拔河！');
        }
        
        
        $sql = "UPDATE " . tablename('hl_tug_winner') . " SET count= $count  WHERE from_user='$from_user' AND rid = '$id' limit 1";
        pdo_query($sql);
        
        if ($reply['status'] == 1 && $reply['starttime']>0) {
            $lefttime = ($reply['starttime'] ) - TIMESTAMP;
            if($lefttime<=-$reply['timelimit']){
                //结束
                $profiles = pdo_fetch("select * FROM " . tablename('hl_tug_winner') . " WHERE from_user='$from_user' AND rid = '$id' LIMIT 1 ");
                $a = pdo_fetchcolumn("select sum(`count`) FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=1 ");
                $b = pdo_fetchcolumn("select sum(`count`) FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=2 ");
                $a = intval($a);
                $b = intval($b);
                $win_team = 0;
                if($a==$b){
                    $win_team = 3;
                    
                }else if ($a > $b) {
                    $win_team = $profiles['whoteam']==1?1:2;
                    
                } else {
                    $win_team = $profiles['whoteam']==2?1:2;
                }
                
                $count = $profiles['count'];
                $result =  pdo_fetchcolumn("select count(*) FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND count>".$count);
                $result++;
                $oauth_openid = "hl_bug5" . $_W['uniacid'];
                $cookies= iunserializer($_COOKIE[$oauth_openid]);
                die(json_encode(array("over"=>1, "result"=>$result,"count"=>$count,"nickname"=>$cookies['nickname'],"headimgurl"=>$cookies['headimgurl'],"win_team"=>$win_team)));
            }
        }
        die(json_encode(array("over"=>0,"lefttime"=>$lefttime)));
    }

    public function doMobileStarTime() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $reply = pdo_fetch("SELECT * FROM " . tablename('hl_tug_reply') . " WHERE rid = '$id' LIMIT 1");

        if (empty($reply)) {
            message('不存在此活动，请重新发送消息进入拔河！');
        }

        if ($reply['status'] == 1) {
            if ($reply['starttime'] > TIMESTAMP) {
                $reply['lefttime'] = $reply['starttime'] - TIMESTAMP;
            } else {
                $reply['lefttime'] = 1;
            }
        } else {

            $reply['lefttime'] = 1000000;
        }
        echo $reply['lefttime'];
        //echo 3;
    }

    public function doWebBigscreen() {
        global $_GPC, $_W;

        checklogin();
        $id = intval($_GPC['id']);
        $reply = pdo_fetch("SELECT * FROM " . tablename('hl_tug_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $id));

        $reply['keyword'] = pdo_fetchcolumn("SELECT content FROM " . tablename('rule_keyword') . " WHERE rid = '{$id}' LIMIT 1");
        pdo_update('hl_tug_reply', array('status' => 0), array('rid' => $id));
        pdo_update('hl_tug_winner', array('count' => 0, 'whoteam' => 0), array('rid' => $id));
        load()->func('tpl');
        include $this->template('tugscreen');
    }

    public function doWebAjax() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);

        $op = $_GET['op'];
        if ($id) {
            $reply = pdo_fetch("select * FROM " . tablename('hl_tug_reply') . " WHERE rid = '$id'  ");
        }
        if (!$reply) {
            exit;
        }
        if ($op == 'get_user_cnt') {
            $a = pdo_fetchcolumn("select count(id) FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=1 ");
            $b = pdo_fetchcolumn("select count(id) FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=2 ");
            echo '{"team1_cnt":' . $a . ',"team2_cnt":' . $b . '}';
        }

        if ($op == 'tug_start_game') {
            pdo_update('hl_tug_reply', array('status' => 1, 'starttime' => (TIMESTAMP + 5)), array('rid' => $id));
            pdo_update('hl_tug_winner', array('count' => 0), array('rid' => $id));
            echo '{"record_id":35}';
        }


        if ($op == 'tug_get_top') {
            $topinfo['team1'] = pdo_fetchall("select uname,pic FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=1 order by `count` DESC LIMIT 7");
            $topinfo['team2'] = pdo_fetchall("select uname,pic FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=2 order by `count` DESC LIMIT 7");

            $a = pdo_fetchcolumn("select sum(`count`) FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=1 ");
            $b = pdo_fetchcolumn("select sum(`count`) FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=2 ");

            $topinfo['result'] = $a > $b ? 1 : 2;
            $topinfo['info'] = $a . '--' . $b;
            die(json_encode(array('topinfo' => $topinfo)));
        }

        if ($op == 'over_tug') {

            $a = pdo_fetchcolumn("select sum(`count`) FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=1 ");
            $b = pdo_fetchcolumn("select sum(`count`) FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=2 ");

            $a=  intval($a);
            $b = intval($b);
            
            $team1_info = pdo_fetchall("select uname,pic FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=1 order by `count` DESC LIMIT 7");
            $team2_info = pdo_fetchall("select uname,pic FROM " . tablename('hl_tug_winner') . " WHERE rid = '$id' AND whoteam=2 order by `count` DESC LIMIT 7");

            $prize_setting = array(array('rank_start' => 1, 'rank_end' => 1), array('rank_start' => 2, 'rank_end' => 3), array('rank_start' => 4, 'rank_end' => 6), array('rank_start' => 7, 'rank_end' => 12));
            $result = $a > $b ? 1 : 2;
            if ($a > $b) {

                $result = 1;
                $top_info[] = array('uname' => $reply['teama'], 'pic' => $reply['teamapic']);
                $top_info[] = array('uname' => $reply['teamb'], 'pic' => $reply['teambpic']);
            } else {
                $top_info[] = array('uname' => $reply['teamb'], 'pic' => $reply['teambpic']);
                $top_info[] = array('uname' => $reply['teama'], 'pic' => $reply['teamapic']);
                $result = 2;
            }
            die(json_encode(array('prize_setting' => $prize_setting, 'top_info' => $top_info, 'team1_info' => $team1_info, 'team2_info' => $team2_info, 'result' => $result, 'game_over' => 1)));
        }
    }

    public function doMobileUserinfo() {
        global $_W, $_GPC;
        $weid = $_W['uniacid']; //当前公众号ID
        $id = $_GPC['id'];
        //用户不授权返回提示说明
        if ($_GPC['code'] == "authdeny") {

            $url = $_W['siteroot'] . "app/". $this->createMobileUrl('index', array('id' => $id));
            header("location:$url");
            exit('authdeny');
        }
        //高级接口取未关注用户Openid
        if (isset($_GPC['code'])) {
            //第二步：获得到了OpenID
            $appid = $_W['account']['key'];
            $secret = $_W['account']['secret'];
            $serverapp = $_W['account']['level'];
            if ($serverapp != 4) {
                $cfg = $this->module['config'];
                $appid = $cfg['appid'];
                $secret = $cfg['secret'];
                if (empty($appid) || empty($secret)) {
                    return;
                }
            }
            $state = $_GPC['state'];
            //1为关注用户, 0为未关注用户
            //查询活动时间
            $code = $_GPC['code'];
            $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code";
            load()->func('communication');
            $content = ihttp_get($oauth2_code);
            $token = @json_decode($content['content'], true);
            if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {

                echo '<h1>获取微信公众号授权' . $code . '失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
                exit;
            }
            $from_user = $token['openid'];
            //再次查询是否为关注用户
            $profile = pdo_fetch("select follow from " . tablename('mc_mapping_fans') . " where openid=:openid limit 1", array(":openid" => $from_user));
            
            //关注用户直接获取信息	
            if ($profile['follow'] == 1) {
                $state = 1;
            } else {
                //未关注用户跳转到授权页
                $url = $_W['siteroot'] ."app/".$this->createMobileUrl('userinfo', array('id' => $id));
                $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
                header("location:$oauth2_code");
            }
            //未关注用户和关注用户取全局access_token值的方式不一样
            if ($state == 1 && $serverapp != 4) {
                $access_token = account_weixin_token($_W['account']);
                $oauth2_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $from_user . "&lang=zh_CN";
            } else {
                $access_token = $token['access_token'];
                $oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $from_user . "&lang=zh_CN";
            }

            //使用全局ACCESS_TOKEN获取OpenID的详细信息			
            $content = ihttp_get($oauth2_url);
            $info = @json_decode($content['content'], true);
            if (empty($info) || !is_array($info) || empty($info['openid']) || empty($info['nickname'])) {
                echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！<h1>';
                exit;
            }

            if (!empty($info["headimgurl"])) {
                $info['avatar'] = 'avatar/' . $info["openid"] . '.jpg';
            } else {
                $info['headimgurl'] = 'avatar_11.jpg';
            }
         
            $oauth_openid = "hl_bug5" . $_W['uniacid'];
            setcookie($oauth_openid, iserializer($info), time() + 3600 * 240);
            $url = $this->createMobileUrl('index', array('id' => $id));
            //die('<script>location.href = "'.$url.'";</script>');
            header("location:$url");
            exit;
        } else {
            echo '<h1>网页授权域名设置出错!</h1>';
            exit;
        }
    }

    private function CheckCookie() {
        global $_W, $_GPC;
        //return;
        $id = $_GPC['id'];
        $oauth_openid = "hl_bug5" . $_W['uniacid'];
        if (empty($_COOKIE[$oauth_openid])) {
            $appid = $_W['account']['key'];
            $secret = $_W['account']['secret'];
            //是否为高级号
            $serverapp = $_W['account']['level'];
            if ($serverapp != 4) {
                $cfg = $this->module['config'];
                $appid = $cfg['appid'];
                $secret = $cfg['secret'];
                if (empty($appid) || empty($secret)) {
                   // return;
                    message('非认证服务号请借用，在后台填写借用的 Appid 和 Appsecret!');
                }
            }

            //借用的
            $url = $_W['siteroot'] ."app/". $this->createMobileUrl('userinfo', array('id' => $id));
            $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
            //exit($oauth2_code);
            header("location:$oauth2_code");

            exit;
        }
    }

}
