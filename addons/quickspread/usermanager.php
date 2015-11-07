<?php

require_once(APP_PHP . 'wechatutil.php');

class UserManager {
  private static $t_sys_mc_fans = 'mc_mapping_fans';
  private static $t_sys_mc_member = 'mc_members';
  private static $t_local_fans = 'quickspread_fans';
  private static $t_sys_qr = 'qrcode';
  private static $t_black  = 'quickspread_blacklist';
  private static $t_qr = 'quickspread_qr';
  private static $t_scene_id = 'quickspread_scene_id';
  private static $t_credit = 'quickspread_credit';
  private static $t_follow = 'quickspread_follow';
  private static $t_channel = 'quickspread_channel';
  private static $t_active_ch = 'quickspread_active_channel';

  // 微信服务器保留图片3天，保险起见减去1个小时的提前量
  private static $WECHAT_MEDIA_EXPIRE_SEC = 255600; //(3 * 24 * 60 * 60 - 1 * 60 * 60) seconds; 3 days
  //private static $WECHAT_MEDIA_EXPIRE_SEC = 1; //(3 * 24 * 60 * 60 - 1 * 60 * 60) seconds;
  // 用户OpenID 
  private $uid = 0;

  function __construct($uid) {
    $this->uid = $uid;
  }

  /**
   * @brief 获取当前weid可用的下一个SceneID
   */
  public function getNextAvaliableSceneID() {
    global $_W;
    $scene_id = pdo_fetchcolumn('SELECT scene_id FROM ' . tablename(self::$t_scene_id) . ' WHERE weid=:weid',
      array(':weid'=>$_W['weid']));
    WeUtility::logging('sc', $scene_id);
    if (empty($scene_id)) {
      $scene_id = 2;
      WeUtility::logging('sc emtpy', $scene_id);
      pdo_insert(self::$t_scene_id, array('weid'=>$_W['weid'], 'scene_id'=>$scene_id));
    } else {
      WeUtility::logging('sc ok', $scene_id);
      $scene_id++;
      pdo_update(self::$t_scene_id, array('scene_id'=>$scene_id), array('weid'=>$_W['weid']));
    } 
    return $scene_id;
  }

  /**
   * @brief 获取uid用户的当前推广QR
   */
  public function getQR($channel, $from_user) {
    global $_W;
    $qr = pdo_fetch("SELECT * FROM " . tablename(self::$t_qr)
      . " WHERE from_user=:uid AND channel=:channel AND from_user=:from_user AND weid=:weid ORDER BY createtime DESC LIMIT 1",
      array(":uid"=>$this->uid, ":channel"=>$channel, ":from_user"=>$from_user, ":weid"=>$_W['weid']));
    // 简单起见，当图片在微信服务器失效后（一般为3天），直接删除这一条规则, 由调用者负责具体后继处理方式
    if (!empty($qr) and $qr['createtime'] + self::$WECHAT_MEDIA_EXPIRE_SEC  < time()) {
      pdo_delete(self::$t_qr, array("weid"=>$_W['weid'], "scene_id"=>$qr['scene_id']));
      unset($qr);
      $qr = null;
    } 
    return $qr;
  }

  /**
   * @brief 根据scene_id获取二维码信息 
   */
  public function getQRByScene($scene_id) {
    global $_W;
    $qr = pdo_fetch("SELECT * FROM " . tablename(self::$t_qr) . " WHERE scene_id=:scene_id AND weid=:weid",
      array(":scene_id"=>$scene_id, ":weid"=>$_W['weid']));
    return $qr;
  }

  /**
   * @param qr_type 二维码类型,永久/临时
   * @param qr_url  带二维码传单在本机相对于$_W['attachurl']路径的存储地址
   * @param scene_id 二维码的SCENE_ID
   *
   * @brief 定义用户正在使用的二维码
   */
  /*
  public function setQR($scene_id, $qr_url, $media_id, $channel=0) {
    $oldqr = $this->getQR($channel);
    WeUtility::logging('cc1');
    if (empty($oldqr)) {
      WeUtility::logging('cc2');
      $this->newQR($scene_id, $qr_url, $media_id, $channel);
      WeUtility::logging('cc3');
    } else {
      WeUtility::logging('cc4');
      $this->updateQR($scene_id, $qr_url, $media_id, $channel);
      WeUtility::logging('cc5');
    }
  }
   */

  public function newQR($scene_id, $qr_url, $media_id, $channel) {
    global $_W;
    $params = array("weid"=>$_W['weid'], "from_user"=>$this->uid, "scene_id"=>$scene_id, "qr_url"=>$qr_url, "media_id"=>$media_id, "channel"=>$channel, "createtime"=>time());
    $sys_params = array(
      "uniacid"=>$_W['weid'], 'acid'=>$_W['weid'], "qrcid"=>$scene_id, "model"=>2, "name"=>$this->uid, "keyword"=>"qr",
      "expire"=>0, "createtime"=>time(), "status"=>1, "ticket"=>$media_id);
    $ret = pdo_insert(self::$t_qr, $params);
    $ret = pdo_insert(self::$t_sys_qr, $sys_params);
    WeUtility::logging('cc2.2');
    if (!empty($ret)) {
      //pdo_insert(self::$t_sys_qr, array('weid'=>$_W['weid'], 'qrcid'=>$scene_id, 'name'=>'友乐聚', 'keyword'=>'qr')); 
    }
    return $ret;
  }

  public function updateQR($scene_id, $qr_url, $media_id, $channel) {
    global $_W;
    $ret = pdo_update(self::$t_qr, array("scene_id"=>$scene_id, "qr_url"=>$qr_url, "media_id"=>$media_id, "channel"=>$channel), array("from_user"=>$this->uid, "weid"=>$_W['weid']));
    return $ret;
  }

  public function getActiveChannel() {
    global $_W;
    //$ret = pdo_fetch("SELECT * FROM " . tablename(self::$t_active_ch) . " WHERE weid=:weid AND from_user=:uid",
    //  array(":weid"=>$_W['weid'], ":uid"=>$this->uid));

    // $ret = pdo_fetchcolumn("SELECT MAX(channel) FROM " . tablename(self::$t_channel) . " WHERE weid=:weid", array(":weid"=>$_W['weid']));
    $ret = pdo_fetch("SELECT * FROM " . tablename(self::$t_channel) . " WHERE weid=:weid AND active=1 LIMIT 1", array(":weid"=>$_W['weid']));
    if (empty($ret)) {
      $ret = pdo_fetch("SELECT * FROM " . tablename(self::$t_channel) . " WHERE weid=:weid LIMIT 1", array(":weid"=>$_W['weid']));
    }
    return $ret;
  }

  public function getChannel($channel) {
    global $_W;
    $ret = pdo_fetch("SELECT * FROM " . tablename(self::$t_channel) . " WHERE weid=:weid AND channel=:channel",
      array(":weid"=>$_W['weid'], ":channel"=>$channel));
    $ret = WechatUtil::decode_channel_param($ret, $ret['bgparam']);
    return $ret;
  }

  public function newChannel($channel_params) {
    global $_W;
    extract($channel_params);
    $ret = pdo_insert(self::$t_channel, array("title"=>$title, "thumb"=>$thumb, "desc"=>$desc, "url"=>$url, "click_credit"=>$click_credit, "weid"=>$_W['weid']));
    return $ret;
  }

  public function updateChannel($channel_params) {
    global $_W;
    extract($channel_params);
    $ret = pdo_update(self::$t_channel,
      array("title"=>$title, "thumb"=>$thumb, "desc"=>$desc, "url"=>$url, "click_credit"=>$click_credit),
      array("channel"=>$channel, "weid"=>$_W['weid']));
    return $ret;
  }

  public function delChannel($channel) {
    global $_W;
    $ret = pdo_delete(self::$t_channel, array("channel"=>$channel, "weid"=>$_W['weid']));
    return $ret;
  }

  public function processSubscribe($follower_uid, $channel) {
    // validate if the first time subscribe
    if (!$this->alreadyFollow($follower_uid, $channel)) {
      $this->addFollow($follower_uid, $channel);
    }
  }

  // TODO: 需要对t_sys_mc_member表增加一个索引, 否则每次都要对mc_fans表全表扫描
  public function getFollowList($leader_uid) {
    global $_W;
    $select = "a.createtime createtime, c.nickname, avatar, openid as from_user, credit";
    $list = pdo_fetchall(" SELECT $select FROM " . tablename(self::$t_follow) . " a "
      . " JOIN " .  tablename(self::$t_sys_mc_fans) . " b "
      . " JOIN " . tablename(self::$t_sys_mc_member) . " c "
      . " WHERE b.uid=c.uid AND b.uniacid = :uniacid AND c.uniacid = :uniacid "
      . " AND a.follower=b.openid AND a.leader=:leader AND a.weid=:uniacid",
     array(':leader'=>$leader_uid, ':uniacid'=>$_W['weid']));
    return $list;
  }

  public function getFollowList2($mylist) {
    global $_W;
    $mylist2 = array();
    $select = "a.createtime createtime, c.nickname, avatar, openid as from_user, credit";
    $from_user_list = "'xxxooo'";
    foreach($mylist as $l) {
      $from_user_list .= ",'" . $l['from_user'] . "'";
    }
    if (count($mylist) > 0) {
      $mylist2 = pdo_fetchall(" SELECT $select FROM " . tablename(self::$t_follow) . " a "
        . " JOIN " .  tablename(self::$t_sys_mc_fans) . " b "
        . " JOIN " . tablename(self::$t_sys_mc_member) . " c "
        . " WHERE a.leader in (" . $from_user_list . ") AND a.weid=:uniacid"
        . " AND b.uid=c.uid AND b.uniacid = :uniacid AND c.uniacid = :uniacid "
        . " AND a.follower=b.openid",
        array(':uniacid'=>$_W['weid']));
    }
    return $mylist2;
  }

  public function getQualityUser($pindex, $psize,$keyword) {
    global $_W;
    $condition = " a.uniacid=:uniacid ";
    $pars = array(':uniacid'=>$_W['uniacid']);
    if(!empty($keyword)){
            $condition.=" and ( a.openid like :keyword or b.nickname like :keyword)";
        $pars[':keyword'] = "%{$keyword}%";
    }
    $select =  'a.followtime createtime, b.nickname, avatar, openid as from_user, COUNT(c.follower) follower_count, SUM(c.credit) follower_credit, credit1';
    $list = pdo_fetchall(" SELECT " . $select . " FROM " 
      .                 tablename(self::$t_sys_mc_fans) . " a "
      . " LEFT JOIN " . tablename(self::$t_sys_mc_member) . " b "
      . "      ON b.uid=a.uid AND b.uniacid = :uniacid AND a.uniacid = :uniacid "
      . " LEFT JOIN " . tablename(self::$t_follow) . " c "
      . "      ON c.leader = a.openid AND c.weid = a.uniacid "
      . " WHERE $condition "
      . " GROUP BY a.openid "
      . " HAVING follower_count > 0 "
      . " ORDER BY follower_count DESC"
      . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize,
      $pars
    );

    return $list;
  }

  public function getQualityUserCount($keyword) {
    global $_W;
    $condition = " a.uniacid=:uniacid ";
    $pars = array(':uniacid'=>$_W['uniacid']);
    if(!empty($keyword)){
          $condition.=" and ( a.openid like :keyword or b.nickname like :keyword)";
        $pars[':keyword'] = "%{$keyword}%";
    }
    
    $c = pdo_fetchcolumn("SELECT COUNT(*) FROM ( SELECT COUNT(c.follower) follower_count FROM " 
      .                 tablename(self::$t_sys_mc_fans) . " a "
      . " LEFT JOIN " . tablename(self::$t_sys_mc_member) . " b "
      . "      ON b.uid=a.uid AND b.uniacid = :uniacid AND a.uniacid = :uniacid " 
      . " LEFT JOIN " . tablename(self::$t_follow) . " c "
      . "      ON c.leader = a.openid AND c.weid = a.uniacid " 
      . " WHERE $condition "
      . " GROUP BY a.openid "
      . " HAVING follower_count > 0 "
      . " ) t ",
      $pars
    );
    return $c;
  }

  public function getTopUser($topn = 20) {
    global $_W;
    $list = $this->getQualityUser(1, $topn);
    return $list;
  }

  public function getAllUser($pindex, $psize,$keyword) {
    global $_W;
      $condition = " a.uniacid=:uniacid ";
    $pars = array(':uniacid'=>$_W['uniacid']);
    if(!empty($keyword)){
            $condition.=" and ( a.openid like :keyword or b.nickname like :keyword)";
        $pars[':keyword'] = "%{$keyword}%";
    }
    
    $select =  'a.followtime createtime, b.nickname, avatar, openid as from_user, COUNT(c.follower) follower_count, SUM(c.credit) follower_credit, credit1';
    $list = pdo_fetchall(" SELECT " . $select . " FROM " 
      .                 tablename(self::$t_sys_mc_fans) . " a "
      . " LEFT JOIN " . tablename(self::$t_sys_mc_member) . " b "
      . "      ON b.uid=a.uid AND b.uniacid = :uniacid AND a.uniacid = :uniacid " 
      . " LEFT JOIN " . tablename(self::$t_follow) . " c "
      . "      ON c.leader = a.openid AND c.weid = a.uniacid " 
      . " WHERE $condition "
      . " GROUP BY a.openid "
      . " ORDER BY createtime DESC"
      . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize,
      $pars
    );
    return $list;
  }


  public function getAllUserCount($keyword) {
    global $_W;
      $condition = " a.uniacid=:uniacid ";
    $pars = array(':uniacid'=>$_W['uniacid']);
    if(!empty($keyword)){
        $condition.=" and ( a.openid like :keyword or b.nickname like :keyword)";
        $pars[':keyword'] = "%{$keyword}%";
    }
    
    $list = pdo_fetchcolumn("SELECT COUNT(*) FROM (SELECT a.uid FROM " 
      .                 tablename(self::$t_sys_mc_fans) . " a "
      . " LEFT JOIN " . tablename(self::$t_sys_mc_member) . " b "
      . "      ON b.uid=a.uid AND b.uniacid = :uniacid AND a.uniacid = :uniacid " 
      . " LEFT JOIN " . tablename(self::$t_follow) . " c "
      . "      ON c.leader = a.openid AND c.weid = a.uniacid " 
      . " WHERE $condition"
      . " GROUP BY a.openid "
      . ") t",
      $pars
    );
    return $list;
  }


  public function alreadyFollow($follower_uid, $channel) {
    global $_W;
    WeUtility::logging('alreadyFollow param', array($follower_uid, $channel));
    $ret = pdo_fetch("SELECT * FROM " . tablename(self::$t_follow) . " WHERE leader=:leader AND follower=:follower AND channel=:channel AND weid=:weid",
      array(":leader"=>$this->uid, ":follower"=>$follower_uid, ":channel"=>$channel, ":weid"=>$_W['weid']));
    WeUtility::logging('alreadyFollow done', $ret);
    return (empty($ret) ? false : true);
  }

  private function addSysCredit($from_user, $credit) {
    global $_W;
    $fans = WechatUtil::fans_search($from_user, array('uid'));
    $ret = pdo_query("UPDATE " . tablename(self::$t_sys_mc_member) . "  SET credit1 = credit1 + " . intval($credit) ." WHERE uid=:uid AND uniacid=:weid",
      array(":uid"=>$fans['uid'], ":weid"=>$_W['weid']));
    return $ret;
  }

  private function addLocalCredit($from_user, $credit, $type) {
    global $_W;
    $ret = pdo_insert(self::$t_credit, array('weid'=>$_W['weid'], 'from_user'=>$from_user, 'type'=>$type, 'credit'=>$credit, 'createtime'=>time()));
    return $ret;
  }

  public function addFollow($follower_uid, $channel) {
    global $_W;

    WeUtility::logging('addFollow param', array($follower_uid, $channel));
    $ch = pdo_fetch('SELECT sub_click_credit, click_credit, newbie_credit FROM ' . tablename(self::$t_channel) . ' WHERE channel=:channel AND weid=:weid',
      array(':weid'=>$_W['weid'], ':channel'=>$channel));
    
    // 如果传单删除，则不记录任何推广关系
    if (empty($ch)) {
      return;
    }

    WeUtility::logging('addFollow ch',$ch);
    WeUtility::logging('begine record follow relationship',array('uid'=>$this->uid, 'f'=>$follower_uid));
    if ($this->uid != $follower_uid) {
      // 记录follow关系
      $ret = pdo_insert(self::$t_follow,
        array('weid'=>$_W['weid'], 'leader'=>$this->uid, 'follower'=>$follower_uid, 'channel'=>$channel, 'credit'=>$ch['click_credit'], 'createtime'=>time()));

      if (!$this->inBlackList($this->uid))
      {
        // 给leader积分
        $ret = $this->addLocalCredit($this->uid, $ch['click_credit'], '直接推广积分');
        $ret = $this->addSysCredit($this->uid, $ch['click_credit']);
      }

      // 给leader的上线送积分
      $uplevel = pdo_fetch("SELECT * FROM " . tablename(self::$t_follow) . " WHERE weid=:weid AND follower=:follower",
        array(":weid"=>$_W['weid'], ":follower"=>$this->uid));
      if (!empty($uplevel)) {
        if (!$this->inBlackList($uplevel['leader'])) {
          $ret = $this->addLocalCredit($uplevel['leader'], $ch['sub_click_credit'], '间接推广积分');
          $ret = $this->addSysCredit($uplevel['leader'], $ch['sub_click_credit']);
        }
      }
    }

    if ($this->isNewUser($follower_uid)) {
      // 新用户关注，送关注积分
      $ret = $this->addLocalCredit($follower_uid, $ch['newbie_credit'], '首次关注积分');
      $ret = $this->addSysCredit($follower_uid, $ch['newbie_credit']);
      $ret = $this->addNewUser($follower_uid);
    } else {
      // 如果用户中途取消关注，不予以理会，不再重复送分
    }

    WeUtility::logging('addFollow done');
    return $ret;
  }

  private function inBlackList($from_user) {
    global $_W;
    $b = pdo_fetch("SELECT * FROM " . tablename(self::$t_black) . " WHERE from_user=:f AND weid=:w LIMIT 1", array(':f'=>$from_user, ':w'=>$_W['weid']));
    if (!empty($b)) {
      $hit = 1 + $b['hit'];
      pdo_update(self::$t_black, array('hit'=>$hit), array('from_user'=>$from_user, 'weid'=>$_W['weid']));
    }
    return $b;
  }

  public function isNewUser($from_user) {
    global $_W;
    $ret = pdo_fetch("SELECT * FROM " . tablename(self::$t_local_fans) . " WHERE  from_user=:from_user AND weid=:weid",
      array(":from_user"=>$from_user, ":weid"=>$_W['weid']));
    return  (false === $ret) ? true : false;
  }

  public function addNewUser($from_user) {
    global $_W;
    $ret = pdo_insert(self::$t_local_fans, array('weid'=>$_W['weid'], 'from_user'=>$from_user, 'createtime'=>time()));
    return $ret;
  }

  public function getUserInfo($from_user) {
    $fans = WechatUtil::fans_search($from_user, array('nickname','avatar'));
    return $fans;
  }

  public function saveUserInfo($info) {
    if (!isset($info['subscribe']) || $info['subscribe'] != 1) {
      return;
    }
    WeUtility::logging('saveUserInfo', $info);
    $from_user = $info['openid'];
    load()->model('mc');
    $uid = mc_openid2uid($from_user);
    mc_update($uid,
      array('nickname'=>$info['nickname'],
      'gender'=>$info['sex'],
      'nationality'=>$info['country'],
      'resideprovince'=>$info['province'],
      'residecity'=>$info['city'],
      'avatar'=>$info['headimgurl']));
  }
}
