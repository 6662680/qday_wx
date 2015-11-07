<?php
defined('IN_IA') or exit('Access Denied');

require IA_ROOT . '/addons/quickspread/define.php';
require APP_PHP . 'wechatutil.php';
require APP_PHP . 'wechatapi.php';
require APP_PHP . 'usermanager.php';
require APP_PHP . 'wechatservice.php';
require APP_PHP . 'data_template.php';
require_once APP_PHP . 'responser.php';

class QuickSpreadModuleSite extends WechatService {
  private static $t_black = 'quickspread_blacklist';
  private static $t_qr = 'quickspread_qr';
  private static $t_follow = 'quickspread_follow';
  private static $t_credit = 'quickspread_credit';
  private static $t_channel = 'quickspread_channel';
  private static $t_active_ch = 'quickspread_active_channel';

  private function getSysInfo() {
    global $_W;
    load()->model('activity');
    $sysInfo = array();
    $setting = uni_setting($_W['uniacid'], array('creditnames', 'creditbehaviors', 'uc'));
    $sysInfo['behavior'] = $setting['creditbehaviors'];
    $sysInfo['creditnames'] = $setting['creditnames'];
    return $sysInfo;
  }

  private function getUserInfo($uid = null) {
    global $_W;
    load()->model('mc');
    if (empty($uid)) {
      $uid = $_W['member']['uid'];
    } else {
      $uid = mc_openid2uid($uid);
    }
    $userInfo = array();
    $filter = array();
    $filter['status'] = 1;
    $userInfo['coupons'] = activity_coupon_owned($uid, $filter);
    $userInfo['tokens'] = activity_token_owned($uid, $filter);
    $userInfo['credits'] = mc_credit_fetch($_W['member']['uid'], '*');
    return $userInfo;
  }

  private function getSpreadInfo($openid) {
    global $_W;
    load()->model('mc');
    if (empty($openid)) {
      return null;
    }
    $userInfo = array();
    $mgr = new UserManager('');
    $mylist = $mgr->getFollowList($openid);
    $mylist2 = $mgr->getFollowList2($mylist);
    $userInfo['level1']['count'] = count($mylist);
    $userInfo['level2']['count'] = count($mylist2);
    $userInfo['level1']['credit'] = 0;
    $userInfo['level2']['credit'] = 0;
    foreach ($mylist as $c) {
      $userInfo['level1']['credit'] += $c['credit'];
    }
    foreach ($mylist2 as $c) {
      $userInfo['level2']['credit'] += $c['credit'];
    }
    return $userInfo;
  }



  private function refreshUserInfo($from_user) {
      $follower = $from_user;
      $qr_mgr = new UserManager('');
      $userInfo = $qr_mgr->getUserInfo($follower);
      if (empty($userInfo) or empty($userInfo['nickname']) or empty($userInfo['avatar'])) {
        $weapi = new WechatAPI();
        $userInfo = $weapi->getUserInfo($follower);
        $qr_mgr->saveUserInfo($userInfo);
      }
      WeUtility::logging('refresh', $userInfo);
  }


  public function doMobileFollow() {
    global $_W;
    $this->refreshUserInfo($_W['fans']['from_user']);
    $fans = WechatUtil::fans_search($_W['fans']['from_user'], array('avatar', 'nickname', 'createtime', 'credit1'));
    $mgr = new UserManager('');
    $mylist = $mgr->getFollowList($_W['fans']['from_user']);
    $is_follow = true;
    $title = "我的下线";
    include $this->template('follow');
  }

  public function doMobileCredit() {
    global $_W;
    $this->refreshUserInfo($_W['fans']['from_user']);
    $fans = WechatUtil::fans_search($_W['fans']['from_user'], array('avatar', 'nickname', 'createtime', 'credit1'));
    $mylist = pdo_fetchall("SELECT createtime, type, credit FROM " . tablename(self::$t_credit) . " WHERE from_user=:from_user AND weid=:weid ORDER BY createtime DESC",
      array(':from_user'=>$_W['fans']['from_user'], ':weid'=>$_W['weid']));

    $title = "积分查询";
    include $this->template('credit');
  }

  public function doMobileTop() {
    global $_W;

    //$this->getSysInfo();
    //$this->getUserInfo($_W['fans']['from_user']);
    //$this->getSpreadInfo($_W['fans']['from_user']);

    $this->refreshUserInfo($_W['fans']['from_user']);
    $fans = WechatUtil::fans_search($_W['fans']['from_user'], array('avatar', 'nickname', 'createtime', 'credit1'));
    $mgr = new UserManager('');
    $mylist = $mgr->getTopUser(16);
    $title = "排行榜";
    include $this->template('top');
  }


  public function doWebSpread() {
    global $_W, $_GPC;

    $op = empty($_GPC['op']) ? 'leaflet' : $_GPC['op'];

    if ($op == 'delete') {
      $ret = pdo_query("DELETE FROM " . tablename(self::$t_channel) . " WHERE weid=:weid AND channel=:channel",
        array(":weid"=>$_W['weid'], ":channel"=>intval($_GPC['channel'])));
      message("删除成功", referer(), "success");
    } else if ($op == 'leaflet') {
      $mylist = pdo_fetchall("SELECT * FROM " . tablename(self::$t_channel) . " WHERE weid=:weid", array(":weid"=>$_W['weid']));
    } else if ($op == 'active') {
      $channel = intval($_GPC['channel']);
      $item = pdo_fetch("SELECT * FROM " . tablename(self::$t_channel) . " WHERE weid=:weid AND active=1", array(":weid"=>$_W['weid']));
      // 若为激活新频道，则清空QR缓存
      if (!empty($item) and $item['channel'] != $channel) {
        // $this->clearQRCache($item['channel']);
        pdo_update(self::$t_channel, array('active'=>0));
        pdo_update(self::$t_channel, array('createtime'=>time()), array('weid'=>$_W['weid'], 'channel'=>$item['channel']));
        pdo_update(self::$t_channel, array('createtime'=>time(), 'active'=>1), array('weid'=>$_W['weid'], 'channel'=>$channel));
        message('重新设定当前活跃传单成功', referer(), 'success');
      }
      message('设定当前活跃传单成功', referer(), 'success');
    } else if ($op == 'post') {
      load()->func('tpl');
      $item = array();
      if (!empty($_GPC['channel'])) {
        $item = pdo_fetch("SELECT * FROM " . tablename(self::$t_channel) . " WHERE weid=:weid AND channel=:channel",
          array(":weid"=>$_W['weid'], ":channel"=>$_GPC['channel']));
      }
      $item = WechatUtil::decode_channel_param($item, $item['bgparam']);
      if (checksubmit('submit')) {
        $path_parts = explode('.', $_GPC['bg']);
        $suffix = end($path_parts);
        if (strcasecmp('jpg', $suffix) != 0) {
          message('传单背景图必须是jpg格式。不支持png等其他格式。', referer(), 'error');
        }
        if (strpos($_GPC['bg'], 'http://') === FALSE) {
          // valid
        } else {
          message('传单背景图必须从本地上传，不能使用网络图片。您可以先将网络图片保存到本地，然后再上传。', referer(), 'error');
        }

        $bgparam = WechatUtil::encode_channel_param($_GPC);
        if (!empty($_GPC['channel'])) {
          pdo_update(self::$t_channel,
            array(
              'createtime'=>time(),
              'title'=>$_GPC['title'],
              'thumb'=>$_GPC['thumb'],
              'bg'=>$_GPC['bg'],
              'bgparam'=> $bgparam,
              'desc'=>$_GPC['desc'],
              'url'=>$_GPC['url'],
              'click_credit'=>$_GPC['click_credit'],
              'sub_click_credit'=>$_GPC['sub_click_credit'],
              'newbie_credit'=>$_GPC['newbie_credit'],
              'weid'=>$_W['weid']),
            array('channel'=>$_GPC['channel']));
          // 清空QR缓存
          $this->clearQRCache($_GPC['channel']);
          message('更新传单成功', referer(), 'success');
        } else {
          $list_count = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(self::$t_channel) . " WHERE weid=:weid", array(":weid"=>$_W['weid']));
          $active = ($list_count == 0);
          pdo_insert(self::$t_channel,
            array('title'=>$_GPC['title'],
            'createtime'=>time(),
            'thumb'=>$_GPC['thumb'],
            'bg'=>$_GPC['bg'],
            'bgparam'=> $bgparam,
            'active'=>$active,
            'desc'=>$_GPC['desc'],
            'url'=>$_GPC['url'],
            'click_credit'=>$_GPC['click_credit'],
            'sub_click_credit'=>$_GPC['sub_click_credit'],
            'newbie_credit'=>$_GPC['newbie_credit'],
            'weid'=>$_W['weid']));
          message('新建传单成功', referer(), 'success');
        }
      }
    } else if ($op == 'log' or $op == 'qualitylog') {
      $pindex = max(1, intval($_GPC['page']));
      $psize = 20;
      $mgr = new UserManager('');
      $keyword = $_GPC['keyword'];
      if ($op == 'qualitylog') {
         $mylist = $mgr->getQualityUser($pindex, $psize,$keyword); 
         $total = $mgr->getQualityUserCount($keyword);
      } else if ($op == 'log') {
         $mylist = $mgr->getAllUser($pindex, $psize,$keyword);
         $total = $mgr->getAllUserCount($keyword);
      }
      $pager = pagination($total, $pindex, $psize);
    } else if ($op == 'user') { // 下线详情+二级下线详情
      $from_user = $_GPC['from_user'];
      $uplevel = pdo_fetch("SELECT * FROM " . tablename(self::$t_follow) . " WHERE weid=:weid AND follower=:follower",
        array(":weid"=>$_W['weid'], ":follower"=>$from_user));
      $fans = WechatUtil::fans_search($from_user, array('avatar', 'nickname', 'createtime', 'credit1'));
      $mgr = new UserManager('');
      $mylist = $mgr->getFollowList($from_user);
      $mylist2 = $mgr->getFollowList2($mylist);
    } else if ('black_remove' == $op) {
        $from_user = $_GPC['from_user'];
        pdo_delete(self::$t_black, array('from_user'=>$from_user, 'weid'=>$_W['weid']));
        message('删除成功', $this->createWebUrl('Spread', array('op'=>'black')), 'success');
    } else if ('black' == $op) {
      if (!empty($_GPC['from_user'])) {
        $from_user = $_GPC['from_user'];
        $b = pdo_fetch("SELECT * FROM " . tablename(self::$t_black) . " WHERE from_user=:f AND weid=:w LIMIT 1", array(':f'=>$from_user, ':w'=>$_W['weid']));
        if (empty($b)) {
          pdo_insert(self::$t_black, array('from_user'=>$from_user, 'weid'=>$_W['weid'], 'access_time'=>time()));
        }
        message('添加黑名单成功', referer(), 'success');
      }
      $list =  pdo_fetchall("SELECT * FROM " . tablename(self::$t_black) . " WHERE weid=:w", array(':w'=>$_W['weid']));
    } else {
      message('error!', '', 'error');
    }
    include $this->template('spread');
  }

  private function clearQRCache($ch)
  {
    global $_W;
    //pdo_query("DELETE FROM " . tablename(self::$t_qr) . " WHERE weid=:weid AND channel=:channel",
    //  array(':weid'=>$_W['weid'], ':channel'=>$ch));
  }

  public function doWebUserDetail() {
    global $_W, $_GPC;
    include $this->template('user_detail');
  }

  public function doWebBlackList() {
    global $_W, $_GPC;
    include $this->template('black_list');
  }

  public function doMobileRunTask() {
    global $_W, $_GPC;
    ignore_user_abort(true);
    $qr = new QRResponser();
    $qr->respondText($_GPC['from_user']);
    exit(0);
  }

  private function userlink($u) {
    return "<a style='color:black' href='" . $this->CreateWebUrl('Spread', array('from_user' => $u, 'op' => 'user')) . "'>" . $u . "</a>";
  }

   public function doMobileClearQR() {
    global $_W;
    echo "开始清理QR数据库过期数据<br>";
    $ret = pdo_query("DELETE FROM " . tablename(self::$t_qr));
    print_r($ret);
    echo "<br>";
    $ret = pdo_query("DELETE FROM " . tablename(self::$t_follow));
    print_r($ret);
    echo "<br>";
    $ret = pdo_query("DELETE FROM " . tablename(self::$t_credit));
    print_r($ret);
    echo "<br>";
    $ret = pdo_query("DELETE FROM " . tablename(self::$t_channel));
    print_r($ret);
    echo "<br>";
    $ret = pdo_query("DELETE FROM " . tablename(self::$t_black));
    print_r($ret);
    echo "<br>";
    echo "<br>";
    $ret = pdo_fetchall("SELECT * FROM " . tablename(self::$t_qr) . " WHERE weid={$_W['weid']}");
    print_r($ret);

    $ret = pdo_fetchall("SELECT * FROM " . tablename(self::$t_channel) . " WHERE weid={$_W['weid']}");
    print_r($ret);
  }
}
