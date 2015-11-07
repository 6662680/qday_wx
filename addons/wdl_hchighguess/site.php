<?php

defined('IN_IA') or exit('Access Denied');

class wdl_hchighguessModuleSite extends WeModuleSite {

    public function doMobileIndex() {
    
       
        global $_GPC, $_W;
        $weid = $_W['uniacid'];
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        $ridcookie = "wdl_hchighguess_rid" . $_W['weid'];
        $rid = empty($_GPC['rid']) ? $_COOKIE[$ridcookie] : $_GPC['rid'];
        $reply = pdo_fetch("SELECT * FROM " . tablename('wdl_hchighguess_reply') . " WHERE rid = :rid", array(':rid' => $rid));
      
        $from_user = $_W['fans']['from_user'];
     
     
        load()->model('mc');
        $uid = mc_openid2uid($from_user);
        setcookie($ridcookie, $rid, time() + 3600 * 240);
        $oauth_openid = "wdl_hchighguess_fromuser" . $_W['weid'];
        $urlcookie = "wdl_hchighguess_url" . $_W['weid'];
        if (empty($_COOKIE[$oauth_openid])) {
            $url = $_SERVER['REQUEST_URI'];
            setcookie($urlcookie, $url, time() + 3600 * 240);
            $this->CheckCookie();
        } else {
            if (!empty($_COOKIE[$urlcookie])) {
                $url = $_COOKIE[$urlcookie];
                setcookie($urlcookie, '', time() + 3600 * 240);
                header("location:$url");
            }
        }

        if($op=='display'){
             $fans = pdo_fetch("select follow from ".tablename('mc_mapping_fans')." where openid=:openid and uniacid=:uniacid limit 1",array(':openid'=>$_W['fans']['from_user'],':uniacid'=>$_W['uniacid']));
             if (empty($from_user) || empty($fans) || empty($fans['follow'])) {
               //message('要关注才能玩哦，亲！', $reply['gzurl'], 'error');
               header("location: ".$reply['gzurl']);
               exit;
             }
        
            $info = mc_fetch($uid, array('realname', 'mobile', 'avatar'));
            //$info = pdo_fetch("select realname, mobile, avatar from " . tablename('fans') . " where weid = " . $weid . " and from_user ='" . $from_user . "'");
            $myinfo = array('weid' => $weid, 'rid' => $rid, 'from_user' => $from_user, 'realname' => $info['realname'], 'mobile' => $info['mobile'], 'avatar' => $info['avatar'], 'createtime' => time());
            $isregister = pdo_fetch("select * from " . tablename('wdl_hchighguess_member') . " where from_user = '" . $from_user . "' and weid = " . $weid . " and rid = " . $rid);
            if (empty($isregister)) {
                pdo_insert('wdl_hchighguess_member', $myinfo);
                $mid = pdo_InsertId();
            } else {
                $updateinfo = array();
                $mid = $isregister['id'];
            }
            
        }
        $wx['url'] = $_W['siteroot']."app/".substr( $this->createMobileUrl('index',array('rid'=>$rid)),2);
        
        $myinfo = pdo_fetch("select * from " . tablename('wdl_hchighguess_member') . " where from_user = '" . $from_user . "' and weid = " . $weid . " and rid = " . $rid);
        if ($op == 'finish') {
            $imgstr = $_GPC['image'];
            $imgdata = substr($imgstr, strpos($imgstr, ",") + 1);
            $decodedData = base64_decode($imgdata);
            $sname = time() . rand(1000, 9999) . 'v.png';
            $path = IA_ROOT . "/attachment/images/wdl_hchighguess";
            $fname = $path . "/" . $sname;
            load()->func('file');
            mkdirs($path);

            $fp = fopen($fname, 'wb');
            fwrite($fp, $decodedData);
            fclose($fp);
            $myimage = array('weid' => $weid, 'rid' => $rid, 'mid' => $myinfo['id'], 'wid' => intval($_GPC['qid']), 'image' => "images/wdl_hchighguess/" . $sname, 'createtime' => time());
            pdo_insert('wdl_hchighguess_images', $myimage);
            echo pdo_InsertId();
            exit;
        }
        if ($op == 'drawword') {
            $wid = intval($_GPC['wid']);
            include $this->template('drawword');
            exit;
        }
        if ($op == 'myimage') {
            $mid = intval($_GPC['mid']);
            $member = pdo_fetch("select * from ".tablename('wdl_hchighguess_member')." where id = ".$mid);
            $imgid = intval($_GPC['imgid']);
            $isregister = pdo_fetch("select * from " . tablename('wdl_hchighguess_member') . " where from_user = '" . $from_user . "' and weid = " . $weid . " and rid = " . $rid);
            if (empty($isregister)) {
                //$info = pdo_fetch("select realname, mobile, avatar from " . tablename('fans') . " where weid = " . $weid . " and from_user ='" . $from_user . "'");
                $info = mc_fetch($uid, array('realname', 'mobile', 'avatar'));
                $memberinfo = array('weid' => $weid, 'rid' => $rid, 'from_user' => $from_user, 'realname' => $info['realname'], 'mobile' => $info['mobile'], 'avatar' => $info['avatar'], 'createtime' => time());
                pdo_insert('wdl_hchighguess_member', $memberinfo);
                $infoid = pdo_InsertId();
            } else {
                $infoid = $isregister['id'];
            }
            $myimage = pdo_fetch("select * from " . tablename('wdl_hchighguess_images') . " where id = " . $imgid);
            $selectword = pdo_fetch("select * from " . tablename('wdl_hchighguess_words') . " where id = " . $myimage['wid'] . " and isopen = 1");

            if (!empty($selectword)) {
                $selectlog = pdo_fetchall("select * from " . tablename('wdl_hchighguess_selectlog') . " where weid = " . $weid . " and wid = " . $selectword['id'] . " and imgid = " . $imgid);
            } else {
                echo "<script>alert('该词条已被删除！！');window.location.href = '" . $_W['siteroot'] . "app/" . $this->createMobileUrl('index', array('rid' => $rid)) . "';</script>";
                exit;
            }


            if (!empty($mid) && $mid != $infoid) {
                
                $other = 1;
                $isselect = pdo_fetch("select * from " . tablename('wdl_hchighguess_selectlog') . " where from_user ='" . $from_user . "' and weid = " . $weid . " and wid = " . $selectword['id'] . " and imgid = " . $imgid);
                if (!empty($selectword['words'])) {
                    $words = explode("#", $selectword['words']);
                    $wordss = array();
                    foreach ($words as $key => $w) {
                        $wordss[$key]['word'] = $w;
                        $wordss[$key]['id'] = $selectword['id'];
                    }
                }
                
                if (empty($isselect)) {
                    include $this->template('selectimage');
                    exit;
                } 
                
                $selectlog = pdo_fetchall("select * from " . tablename('wdl_hchighguess_selectlog') . " where weid = " . $weid . " and wid = " . $selectword['id'] . " and imgid = " . $imgid);
                $total = pdo_fetchcolumn("select count(id) from " . tablename('wdl_hchighguess_selectlog') . " where weid = " . $weid . " and wid = " . $selectword['id'] . " and imgid = " . $imgid . " and word = '" . $selectword['word'] . "'");
                $alltotal = pdo_fetchcolumn("select count(id) from " . tablename('wdl_hchighguess_selectlog') . " where weid = " . $weid . " and wid = " . $selectword['id'] . " and imgid = " . $imgid);
                $total = empty($total) ? 0 : $total;
                if (empty($alltotal)) {
                    $unique = 0;
                } else {
                    $unique = intval($total / $alltotal * 100);
                } 
                include $this->template('myfinished');
                exit;
            } 
            
            if (!empty($selectlog)) {
                
                if (!empty($selectword['words'])) {
                    $words = explode("#", $selectword['words']);
                    $wordss = array();
                    foreach ($words as $key => $w) {
                        $wordss[$key]['word'] = $w;
                        $wordss[$key]['id'] = $selectword['id'];
                    }
                } 
                
                $total = pdo_fetchcolumn("select count(id) from " . tablename('wdl_hchighguess_selectlog') . " where weid = " . $weid . " and wid = " . $selectword['id'] . " and imgid = " . $imgid . " and word = '" . $selectword['word'] . "'");
                $alltotal = pdo_fetchcolumn("select count(id) from " . tablename('wdl_hchighguess_selectlog') . " where weid = " . $weid . " and wid = " . $selectword['id'] . " and imgid = " . $imgid);
                $total = empty($total) ? 0 : $total;
                if (empty($alltotal)) {
                    $unique = 0;
                } else {
                    $unique = intval($total / $alltotal * 100);
                } 
                $wx['url'] = $_W['siteroot']."app/".substr( $this->createMobileUrl('index',array('rid'=>$rid)),2);
                include $this->template('myfinished');
                exit;
            }
            
            $wx['url'] = $_W['siteroot']."app/".substr( $this->createMobileUrl('index',array('rid'=>$rid, 'op'=>'myimage','imgid'=>$imgid,'mid'=>$infoid)),2);
            include $this->template('myimage');
            exit;
        }


        if ($op == 'selectimage') {
            $wid = intval($_GPC['wid']);
            $imgid = $_GPC['imgid'];
            $myimage = pdo_fetch("select image from " . tablename('wdl_hchighguess_images') . " where id = " . $imgid);
            if ($_GPC['opp'] == 'selected') {
                $selectlog = array('weid' => $weid, 'wid' => $wid, 'imgid' => $imgid, 'from_user' => $from_user, 'realname' => $myinfo['realname'], 'image' => $myinfo['avatar'], 'word' => $_GPC['word'], 'createtime' => time());
                $isselect = pdo_fetch("select * from " . tablename('wdl_hchighguess_selectlog') . " where from_user ='" . $from_user . "' and weid = " . $weid . " and wid = " . $wid . " and imgid = " . $imgid);
                if (empty($isselect)) {
                    pdo_insert('wdl_hchighguess_selectlog', $selectlog);
                } 
                $url = $this->createMobileUrl('index', array('op' => 'myimage', 'imgid' => $imgid, 'rid' => $rid, 'mid' => $_GPC['mid']));
                header("location:$url");
            }
        } 
        
        $words = pdo_fetchall("select * from " . tablename('wdl_hchighguess_words') . " where weid = " . $weid . " and isopen = 1");
        include $this->template('index');
    }

    public function doWebWords() {
        global $_GPC, $_W;
        $weid = $_W['weid'];
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        if ($op == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 30;
            $list = pdo_fetchall("select * from " . tablename('wdl_hchighguess_words') . " where weid = " . $weid . " order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $total = pdo_fetchcolumn("select count(id) from " . tablename('wdl_hchighguess_words') . " where weid = " . $weid);
            $pager = pagination($total, $pindex, $psize);
        } if ($op == 'sort') {
            $op = 'display';
            $sort = array('word' => $_GPC['word']);
            $list = pdo_fetchall("select * from " . tablename('wdl_hchighguess_words') . " where weid = " . $weid . " and word like '%" . $sort['word'] . "%' order by createtime desc");
        } if ($op == 'post') {
            $id = $_GPC['id'];
            if (intval($id)) {
                $item = pdo_fetch("select * from " . tablename('wdl_hchighguess_words') . " where id = " . $id);
            } if (checksubmit('submit')) {
                $newwords = array('weid' => $_W['weid'], 'word' => $_GPC['word'], 'words' => trim($_GPC['words']), 'isopen' => $_GPC['isopen'], 'createtime' => time());
                if (intval($id)) {
                    $temp = pdo_update('wdl_hchighguess_words', $newwords, array('id' => $id));
                    if ($temp) {
                        message('提交成功！', $this->createWebUrl('words', array('op' => 'display')), 'success');
                    } else {
                        message('提交失败！', $this->createWebUrl('words', array('op' => 'post', 'id' => $id)), 'error');
                    }
                } else {
                    $temp = pdo_insert('wdl_hchighguess_words', $newwords);
                    if ($temp) {
                        message('提交成功！', $this->createWebUrl('words', array('op' => 'display')), 'success');
                    } else {
                        message('提交失败！', $this->createWebUrl('words', array('op' => 'post', 'id' => $id)), 'error');
                    }
                }
            }
        } if ($op == 'delete') {
            $temp = pdo_delete('wdl_hchighguess_words', array('id' => $_GPC['id']));
            if ($temp) {
                message('删除成功！', $this->createWebUrl('words', array('op' => 'display')), 'success');
            } else {
                message('删除失败！', $this->createWebUrl('words', array('op' => 'post', 'id' => $id)), 'error');
            }
        } include $this->template('web/words');
    }

    public function doMobileUserinfo() {
        global $_GPC, $_W;
        $weid = $_W['weid'];
        if ($_GPC['code'] == "authdeny") {
            $url = $_W['siteroot'] . "app/" . $this->createMobileUrl('index', array());
            header("location:$url");
            exit('authdeny');
        }
          load()->func('communication');
        
        if (isset($_GPC['code'])) {
            $appid = $_W['account']['key'];
            $secret = $_W['account']['secret'];
            $serverapp = $_W['account']['level'];
            if ($serverapp != 4) {
                $cfg = $this->module['config'];
                $appid = $cfg['appid'];
                $secret = $cfg['secret'];
            } 
            
            $state = $_GPC['state'];
            $rid = $_GPC['rid'];
            $code = $_GPC['code'];
            $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code";
            $content = ihttp_get($oauth2_code);
            $token = @json_decode($content['content'], true);
            if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
                echo '<h1>获取微信公众号授权' . $code . '失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
                exit;
            } 
            
            $from_user = $token['openid'];
            //load()->model('mc');
            ///$uid = mc_openid2uid($from_user);
            //$profile = mc_fetch($uid, 'follow');
             $fans = pdo_fetch("select follow from ".tablename('mc_mapping_fans')." where openid=:openid and uniacid=:uniacid limit 1",array(':openid'=>$from_user,':uniacid'=>$_W['uniacid']));
  
            if (!empty($fans) && !empty($fans['follow'])) {
                
                $state = 1;
                
            } else {
                
                $url = $_W['siteroot'] . "app/" . $this->createMobileUrl('userinfo', array());
                $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
                header("location:$oauth2_code");
                
            } 
            
            if ($state == 1) {
                $oauth2_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret . "";
                $content = ihttp_get($oauth2_url);
                $token_all = @json_decode($content['content'], true);
                if (empty($token_all) || !is_array($token_all) || empty($token_all['access_token'])) {
                    echo '<h1>获取微信公众号授权失败[无法取得access_token], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
                    exit;
                } $access_token = $token_all['access_token'];
                $oauth2_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $from_user . "&lang=zh_CN";
                
            } else {
                
                $access_token = $token['access_token'];
                $oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $from_user . "&lang=zh_CN";
                
            } 
            
            $content = ihttp_get($oauth2_url);
            $info = @json_decode($content['content'], true);
            if (empty($info) || !is_array($info) || empty($info['openid']) || empty($info['nickname'])) {
                echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！<h1>';
                exit;
            }
            if (!empty($_W['fans']['from_user'])) {
                load()->model('mc');
                $uid = mc_openid2uid($_W['fans']['from_user']);
                mc_update($uid, array('nickname' => $info['nickname'], 'realname' => $info['nickname'], 'avatar' => $info['headimgurl']));
            }
            
            setcookie("wdl_hchighguess_fromuser" . $_W['weid'], $info['openid'], time() + 3600 * 240);
            $url = $this->createMobileUrl('index');
            header("location:$url");
            exit;
            
        } else {
            
            echo '<h1>网页授权域名设置出错!</h1>';
            exit;
        }
    }

    private function CheckCookie() {
        global $_W;
        $oauth_openid = "wdl_hchighguess_fromuser" . $_W['weid'];
        if (empty($_COOKIE[$oauth_openid])) {
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
            $url = $_W['siteroot'] . "app/" . $this->createMobileUrl('userinfo', array());
            $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
            header("location:$oauth2_code");
            exit;
        }
    }

}

function char($key = 0) {
    $charA = array('0' => 'A.', '1' => 'B.', '2' => 'C.', '3' => 'D.', '4' => 'E.', '5' => 'F.', '6' => 'G.', '7' => 'H.', '8' => 'I.', '9' => 'J.');
    return $charA[$key];
}
