<?php
defined('IN_IA') or exit('Access Denied');

include_once "function.php";

class Eso_RunmanModuleSite extends WeModuleSite{

	public $reply = array();

	function __construct()
	{
		global $_GPC,$_W;
		define('SYS_TIME', time()); //时间戳
		define('CSS_PATH', $_W['siteroot'].'addons/eso_runman/template/css/');
		define('JS_PATH', $_W['siteroot'].'addons/eso_runman/template/js/');
		define('IMG_PATH', $_W['siteroot'].'addons/eso_runman/template/images/');
		define('JSONJSSDKCONFIG', json_encode($_W['account']['jssdkconfig']));
		define('ATTACHMENT_PATH', IA_ROOT .'/'. $_W['config']['upload']['attachdir'] .'/');
		define('NOW_PATH', $_W['siteroot'].'addons/eso_runman/template/mobile/');
	}

    /**
     * 管理 （跳转）
     */
    public function doWebIndex(){
        global $_GPC;
        if (isset($_GPC['eid'])) {
            gourl(create_url('site/entry', array('do' => 'manage', 'm' => 'eso_runman', 'rid' => $_GPC['id'])));
        }else{
            message('参数错误！');
        }
    }

	/**
	 * 列表
	 */
	public function doWebManage(){
		global $_GPC, $_W;
		load()->model('reply');
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$sql = "uniacid = :uniacid AND `module` = :module";
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$params[':module'] = 'eso_runman';

		if (isset($_GPC['keywords'])) {
			$sql .= ' AND `name` LIKE :keywords';
			$params[':keywords'] = "%{$_GPC['keywords']}%";
		}
		$list = reply_search($sql, $params, $pindex, $psize, $total);
		$pager = pagination($total, $pindex, $psize);

		if (!empty($list)) {
			foreach ($list as &$item) {
				$condition = "`rid`={$item['id']}";
				$item['keywords'] = reply_keywords_search($condition);
				$vote = pdo_fetch("SELECT * FROM " . tablename("eso_runman_reply")." WHERE rid = :rid ", array(':rid' => $item['id']));
				$item['title'] = $vote['title'];
				$item['join'] = $vote['join'];
				$item['view'] = $vote['view'];

				$limits = "活动时间: " . date('Y-m-d H:i:s', $vote['starttime'])." 至 ".date('Y-m-d H:i:s', $vote['endtime']);
				$item['limits'] = $limits;

				if ($vote['endtime'] < SYS_TIME) {
					$item['status'] = '<span class="label label-default ">活动结束</span>';
				} elseif ($vote['starttime'] > SYS_TIME) {
					$item['status'] = '<span class="label label-default ">活动未开始</span>';
				} else {
					$item['status'] = '<span class="label label-success">活动正常</span>';
				}
				//
				$item['starttime'] = $vote['starttime']?date('Y-m-d H:i:s', $vote['starttime']):'无限制';
				$item['endtime'] = $vote['endtime']?date('Y-m-d H:i:s', $vote['endtime']):'无限制';
			}
		}
		include $this->template('manage');
	}

	/**
	 * 管理（参与用户）
	 */
	public function doWebManaged(){
		global $_GPC, $_W;
		$row = pdo_fetch("SELECT * FROM " . tablename('eso_runman_reply') . " WHERE rid = :rid", array(':rid' => intval($_GPC['rid'])));
		if (empty($row)) {
			message('活动不存在！');
		}
		if ($_GPC['type']=='del' && $_GPC['id'] > 0) {
			pdo_delete('eso_runman_users', array('rid'=>$row['rid'],'id'=>intval($_GPC['id'])));
			message('删除成功！', $this->createWebUrl('managed',array('rid'=>$row['rid'])), 'success');
		}
		$pindex = max(1,intval($_GPC['page']));
		$psize = 20;
		$condition = " 1=1 ";
		if (!empty($_GPC['keyval'])) {
			$condition .= " AND (`title` like '%".$_GPC['keyval']."%' OR `openid` like '%".$_GPC['keyval']."%') ";
		}
		$condition = $condition." AND `rid`=".$row['rid'];
		$lists = pdo_fetchall("SELECT * FROM ".tablename("eso_runman_users")." WHERE {$condition} ORDER BY indate desc,id desc LIMIT ".($pindex-1)*$psize.','.$psize);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename("eso_runman_users")." WHERE ".$condition);
		$pager = pagination($total, $pindex, $psize);

		include $this->template('managed');
	}


	/**
	 * 提交管理
	 */
	public function doWebExpiry(){
		global $_GPC, $_W;
		$row = pdo_fetch("SELECT * FROM " . tablename('eso_runman_reply') . " WHERE rid = :rid", array(':rid' => intval($_GPC['rid'])));
		if (empty($row)) {
			message('活动不存在！');
		}
		if ($_GPC['id'] > 0) {
			if ($_GPC['type'] == 'del') {
				pdo_delete("eso_runman_submit", array('id'=>$_GPC['id'], 'rid'=>$row['rid']));
			}elseif ($_GPC['type'] == '2') {
				pdo_update("eso_runman_submit", array('exchange'=>0), array('id'=>$_GPC['id'], 'rid'=>$row['rid']));
			}else{
				pdo_update("eso_runman_submit", array('exchange'=>1), array('id'=>$_GPC['id'], 'rid'=>$row['rid']));
			}
			message('操作成功！', $this->createWebUrl('expiry',array('rid'=>$row['rid'])), 'success');
		}
		$pindex = max(1,intval($_GPC['page']));
		$psize = 20;
		$condition = " `type`='submit' ";
		if (!empty($_GPC['keyval'])) {
			$condition .= " AND `setting` like '%".$_GPC['keyval']."%' ";
		}
		$condition = $condition." AND `rid`=".$row['rid'];
		$lists = pdo_fetchall("SELECT * FROM ".tablename("eso_runman_submit")." WHERE {$condition} ORDER BY indate desc,id desc LIMIT ".($pindex-1)*$psize.','.$psize);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename("eso_runman_submit")." WHERE ".$condition);
		$pager = pagination($total, $pindex, $psize);
		//
		include $this->template('expiry');
	}

	/**
	 *
	 */
	public function doMobileIndex(){
        global $_GPC, $_W;
		$row = pdo_fetch("SELECT * FROM " . tablename('eso_runman_reply') . " WHERE rid = :rid", array(':rid' => intval($_GPC['rid'])));
		if (empty($row)) {
			message('活动不存在！');
		}
		if ($row['starttime'] > SYS_TIME) {
			message('活动尚未开始！');
		}
		if ($row['endtime'] < SYS_TIME) {
			message('活动已经结束！');
		}
        $setting = string2array($row['setting']);
        $this->reply = $row;
		//
		$user = $this->getuser();
		$user['setting'] = string2array($user['setting']);
		//
		$uuser = array("title"=>$setting, "id"=>0);
		$uuid = $_GPC['uid'];
		if (empty($uuid)) {
			$uuid = $_COOKIE['runman_uid'.$row['id']];
		}else{
			if ($uuid != $user['id']) {
				setcookie('runman_uid'.$row['id'], $uuid, time() + 94608000);
			}else{
				$uuid = $_COOKIE['runman_uid'.$row['id']];
			}
		}
		if ($uuid != $user['id']) {
			if ($uuid > 0) {
				$ruser = pdo_fetch("SELECT title,id FROM " . tablename('eso_runman_users') . " WHERE id = :id", array(':id'=>intval($uuid)));
				if ($ruser) {
					$uuser = $ruser;
				}
			}
		}
		if ($_GPC['uid'] != $user['id']) {
			gourl(get_link('uid').'&uid='.$user['id']);
		}
		pdo_update('eso_runman_reply', array('view'=>$this->reply['view'] + 1), array('id'=>$this->reply['id']));
		$nickname = $user['title'];
		//
		$row['share_title'] = str_replace('@会员名', $user['title'], $row['share_title']);
		$row['share_desc'] = str_replace('@会员名', $user['title'], $row['share_desc']);
		//
		$isplus = pdo_fetch("SELECT * FROM " . tablename('eso_runman_plus') . " WHERE rid = :rid AND val = :val",
			array(':rid' => $row['rid'], ':val'=>$user['id']));
		//
		include $this->template('index');
    }

	public function doMobileUsername() {
		global $_GPC, $_W;
		$row = pdo_fetch("SELECT * FROM " . tablename('eso_runman_reply') . " WHERE rid = :rid", array(':rid' => intval($_GPC['rid'])));
		if (empty($row)) {
			message('活动不存在！');
		}
		if ($row['starttime'] > SYS_TIME) {
			message('活动尚未开始！');
		}
		if ($row['endtime'] < SYS_TIME) {
			message('活动已经结束！');
		}
		$setting = string2array($row['setting']);
		$this->reply = $row;
		//
		$user = $this->getuser();
		setcookie('run_username_'.intval($_GPC['rid']), $_GPC['username'], time() + 94608000);
		pdo_update('eso_runman_users', array('title'=>$_GPC['username'],'mobile'=>$_GPC['usernumber']), array('id'=>$user['id']));
		exit();
	}

	public function doMobileAttention() {
		global $_GPC, $_W;
		$row = pdo_fetch("SELECT * FROM " . tablename('eso_runman_reply') . " WHERE rid = :rid", array(':rid' => intval($_GPC['rid'])));
		if (empty($row)) {
			message('活动不存在！');
		}
		if ($row['starttime'] > SYS_TIME) {
			message('活动尚未开始！');
		}
		if ($row['endtime'] < SYS_TIME) {
			message('活动已经结束！');
		}
		$setting = string2array($row['setting']);
		$this->reply = $row;
		//
		$user = $this->getuser();
		if ($user['one'] == 0) {
			$addnum = 1;
			if ($setting['attention']['min'] || $setting['attention']['max']) {
				$addnum = intval(rand($setting['attention']['min'], $setting['attention']['max']));
			}
			pdo_update('eso_runman_users', array('one'=>1,'val'=>$user['val']+$addnum), array('id'=>$user['id']));
		}
		exit();
	}

	public function doMobileForwarding() {
		global $_GPC, $_W;
		$row = pdo_fetch("SELECT * FROM " . tablename('eso_runman_reply') . " WHERE rid = :rid", array(':rid' => intval($_GPC['rid'])));
		if (empty($row)) {
			message('活动不存在！');
		}
		if ($row['starttime'] > SYS_TIME) {
			message('活动尚未开始！');
		}
		if ($row['endtime'] < SYS_TIME) {
			message('活动已经结束！');
		}
		$setting = string2array($row['setting']);
		$this->reply = $row;
		//
		$user = $this->getuser();
		if ($user['one'] == 1) {
			$addnum = 1;
			if ($setting['shareplus']['min'] || $setting['shareplus']['max']) {
				$addnum = intval(rand($setting['shareplus']['min'], $setting['shareplus']['max']));
			}
			pdo_update('eso_runman_users', array('one'=>2,'val'=>$user['val']+$addnum), array('id'=>$user['id']));
		}
		exit();
	}

	public function doMobileExtended() {
		global $_GPC, $_W;
		$row = pdo_fetch("SELECT * FROM " . tablename('eso_runman_reply') . " WHERE rid = :rid", array(':rid' => intval($_GPC['rid'])));
		if (empty($row)) {
			message('活动不存在！');
		}
		if ($row['starttime'] > SYS_TIME) {
			message('活动尚未开始！');
		}
		if ($row['endtime'] < SYS_TIME) {
			message('活动已经结束！');
		}
		$setting = string2array($row['setting']);
		$this->reply = $row;
		//
		$user = $this->getuser();
        $user['setting'] = string2array($user['setting']);
        $user['setting']['title'] = $_GPC['title'];
		$uarr = array();
		$uarr['val'] = $user['val'] - 1;
		if ($_GPC['type'] == '1'){
			pdo_insert('eso_runman_plus', array('rid'=>$row['rid'], 'val'=>$user['id']));
            pdo_update('eso_runman_users', array('setting'=>array2string($user['setting'])), array('id'=>$user['id']));
		}
		pdo_update('eso_runman_users', $uarr, array('id'=>$user['id']));
		exit();
	}

    public function doMobileSubmit() {
        global $_GPC, $_W;
        $row = pdo_fetch("SELECT * FROM " . tablename('eso_runman_reply') . " WHERE rid = :rid", array(':rid' => intval($_GPC['rid'])));
        if (empty($row)) {
            message('活动不存在！');
        }
        if ($row['starttime'] > SYS_TIME) {
            message('活动尚未开始！');
        }
        if ($row['endtime'] < SYS_TIME) {
            message('活动已经结束！');
        }
        $setting = string2array($row['setting']);
        $this->reply = $row;
        //
        $user = $this->getuser();
        $user['setting'] = string2array($user['setting']);
        //
        $isplus = pdo_fetch("SELECT * FROM " . tablename('eso_runman_plus') . " WHERE rid = :rid AND val = :val",
            array(':rid' => $row['rid'], ':val'=>$user['id']));
        //
        if (empty($isplus)) {
            message('未中奖！');
        }
        $iarr = array();
        $iarr['type'] = 'submit';
        $iarr['rid'] = $row['rid'];
        $iarr['openid'] = $user['openid'];
        $iarr['title'] = $user['setting']['title'];
        $iarr['indate'] = SYS_TIME;
        $iarr['setting'] = array();
        $iarr['setting']['realname'] = $_GPC['realname'];
        $iarr['setting']['mobile'] = $_GPC['mobile'];
        $iarr['setting'] = array2string($iarr['setting']);
        pdo_insert('eso_runman_submit', $iarr);
        pdo_delete('eso_runman_plus', array('rid'=>$row['rid'], 'val'=>$user['id']));
        exit();
    }

	public function doMobileNickname() {
		global $_GPC, $_W;
		$uid = ($_GPC['uid'] > 0)?intval($_GPC['uid']):$_W['member']['uid'];
		$fans = pdo_fetch('SELECT acid,openid FROM '.tablename('mc_mapping_fans').' WHERE openid = :openid AND uid = :uid',
			array(':openid' => $_W['openid'], ':uid' => $uid));
		if ($fans) {
			$acc = WeAccount::create($fans['acid']);
			if (method_exists($acc,'fetchAccountInfo')) {
				$accinfo = $acc->fetchAccountInfo();
				//
				load()->func('communication');
				$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$accinfo['key'].'&secret='.$accinfo['secret'].'&code='.$_GPC['code'].'&grant_type=authorization_code';
				$response = ihttp_get($url);
				if (!is_error($response)) {
					$accessinfo = json_decode($response['content'], true);
					//
					$access_token2 = $accessinfo['access_token'];
					$url2 = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token2.'&openid='.$fans['openid'].'&lang=zh_CN';
					$response2 = ihttp_get($url2);
					if (!is_error($response2)) {
						$userinfo = json_decode($response2['content'], true);
						$niemmo = $userinfo['nickname'];
						$userdata = array(
							'nickname' => $userinfo['nickname'],
							'gender' => $userinfo['sex'],
							'avatar' => $userinfo['headimgurl'],
							'resideprovince' => $userinfo['province'],
							'residecity' => $userinfo['city'],
							'nationality' => $userinfo['country'],
						);
						if (empty($niemmo)) {
							message('授权失败！');
						}
						pdo_update('mc_members', $userdata, array('uid'=>$uid));
						gourl(urwdo('index','',1));
					}else{
						message('授权失败-2!');
					}
				}else{
					message('授权失败-1!');
				}
			}
		}else{
			message('参数错误！');
		}
	}

	/** ***********************************************************************************/
	/** ***********************************************************************************/
	/** ***********************************************************************************/

	private function warmmkdirs($path) {
		if(!is_dir($path)) {
			$this->warmmkdirs(dirname($path));
			mkdir($path);
		}
		return is_dir($path);
	}
	/**
	 * 根据 增暖值规则
	 * @param $array
	 * @param null $str
	 * @return float|int
	 */
	private function getregular($array, $str = null) {
		$inorder = array();
		$_varr = $array;
		foreach ($_varr as $key => $val) {
			$inorder[$key] = $val['money'];
		}
		array_multisort($inorder, SORT_DESC, $_varr);
		if ($str === null) {
			return $_varr;
		}
		$str = intval($str);
		$rnum = 1;
		foreach ($_varr as $val) {
			if ($str > $val['money']) {
				$rnum = intval(rand($val['min'], $val['max']));
				break;
			}
		}
		return $rnum;
	}

	/**
	 * 身份识别
	 * @return bool
	 */
	private function cateauth() {
		global $_GPC,$_W;
		if (defined('MING_INIT_MINGAUTH')) {
			return true;
		} define('MING_INIT_MINGAUTH', true);

		if (value($_COOKIE, 'run_openid_'.intval($_GPC['rid']))) {
			$_W['openid'] = value($_COOKIE, 'run_openid_'.intval($_GPC['rid']));
			$_W['member']['uid'] = value($_COOKIE, 'run_user_uid');
			$_W['member']['mobile'] = value($_COOKIE, 'run_user_mobile'.intval($_GPC['rid']));
			$_W['member']['email'] = value($_COOKIE, 'run_user_email'.intval($_GPC['rid']));
			return true;
		}
		//
		$openid = md5(uniqid());
		setcookie('run_openid_'.intval($_GPC['rid']), $openid, time() + 94608000);
		setcookie('run_user_uid'.intval($_GPC['rid']), 0, time() + 94608000);
		setcookie('run_user_mobile'.intval($_GPC['rid']), 0, time() + 94608000);
		setcookie('run_user_email'.intval($_GPC['rid']), 0, time() + 94608000);

		$_W['openid'] = $openid;
		$_W['member']['uid'] = 0;
		$_W['member']['mobile'] = 0;
		$_W['member']['email'] = 0;
		return true;

		//
		/*
		if(!empty($_W['member']) && (!empty($_W['member']['mobile']) || !empty($_W['member']['email']))) {
			$this->_upuseruid();
			return true;
		}
		if(!empty($_W['openid']) && empty($_W['member'])) {
			$frow = db_getone("SELECT uid FROM ".tablename('mc_mapping_fans'), array('openid'=>$_W['openid']));
			if (!empty($frow)) {
				$mrow = db_getone("SELECT uid,mobile,email FROM ".tablename('mc_members'), array('uid'=>$frow['uid']));
				if (!empty($mrow)) {
					$_W['member']['uid'] = $mrow['uid'];
					$_W['member']['mobile'] = $mrow['mobile'];
					$_W['member']['email'] = $mrow['email'];
					$this->_upuseruid();
					return true;
				}
			}
		}
		checkauth();
		$this->_upuseruid();
		*/
	}

	/**
	 * 身份识别（续）
	 */
	private function _upuseruid() {
		global $_W;
		if (defined('CATE_INIT_UPUSERUID')) {
			return true;
		} define('CATE_INIT_UPUSERUID', true);
		//
		$red_user_uid = value($_COOKIE, 'red_user_uid');
		setcookie('red_user_uid', $_W['member']['uid'], time() + 94608000);
		//
		if ($red_user_uid && $red_user_uid != $_W['member']['uid']) {
			$this->getuser();
		}
	}
	/**
	 * 获取会员资料
	 * @return bool
	 */
	private function getuser() {
		global $_GPC,$_W;
		$this->cateauth();
		//
		if (empty($_W['openid']) && $_W['member']['mobile']) {
			$_W['openid'] = $_W['member']['mobile'];
		}
		$user = pdo_fetch("SELECT * FROM ".tablename('eso_runman_users')." WHERE rid = :rid AND openid = :openid",
			array('openid'=>$_W['openid'], 'rid'=>intval($_GPC['rid'])));
		if (empty($user)) {
			$title = $this->nickname();
			if (empty($title)) $title = "匿名";
			$user = array('title'=>$title, 'indate'=>SYS_TIME, 'rid'=>intval($_GPC['rid']), 'openid'=>$_W['openid']);
			$user['mobile'] = $_W['member']['mobile'];
			$user['img'] = $this->nickname(0,'avatar');
			if (empty($this->reply['rid'])){
				$this->reply = pdo_fetch("SELECT * FROM " . tablename('eso_runman_reply') . " WHERE rid = :rid", array(':rid' => intval($_GPC['rid'])));
			}
			if (!empty($this->reply['rid'])){
				$setting = string2array($this->reply['setting']);
				$user['defaultval'] = 1;
				if ($setting['default']['min'] || $setting['default']['max']) {
					$user['defaultval'] = intval(rand($setting['default']['min'], $setting['default']['max']));
					$user['val'] = $user['defaultval'];
				}
			}
			pdo_insert('eso_runman_users', $user, true);
			$user['id'] = pdo_insertid();
			//
			if ($this->reply['id'] > 0) {
				pdo_update('eso_runman_reply', array('join'=>$this->reply['join'] + 1), array('id'=>$this->reply['id']));
			}
		}else{
			$title = $this->nickname();
			if (empty($title)) $title = "匿名";
			if ($title != $user['title']) {
				$user['title'] = $title;
				$user['mobile'] = $_W['member']['mobile'];
				$user['img'] = $this->nickname(0,'avatar');
				db_update('eso_runman_users', array('title'=>$title), array('id'=>$user['id']));
			}
		}
		return $user;
	}

	/**
	 * 获取用户称呼
	 */
	private function nickname($uid = 0, $ty = '') {
		global $_W;
		return value($_COOKIE, 'run_username_'.intval($this->reply['rid']));
		//
		load()->model('mc');
		//获取会员资料
		$user = mc_fetch($uid?$uid:$_W['member']['uid'], array('nickname', 'mobile', 'email', 'avatar'));
		if ($ty) {
			return $user[$ty];
		}
		$niemmo = '';
		if ($user) {
			$niemmo = $user['nickname'];
			if(empty($niemmo)) {
				$niemmo = $user['mobile'];
			}
			if(empty($niemmo)) {
				//$niemmo = cutstr($user['email'], 10);
				//
				$fans = pdo_fetch('SELECT acid,openid FROM '.tablename('mc_mapping_fans').' WHERE openid = :openid AND uid = :uid',
					array(':openid' => $_W['openid'], ':uid' => $user['uid']));
				if ($fans) {
					$acc = WeAccount::create($fans['acid']);
					if (method_exists($acc,'fetchAccountInfo')) {
						$accinfo = $acc->fetchAccountInfo();
						$access_token = iunserializer($accinfo['access_token']);
						$accesstoken = $access_token['token'];
						//
						load()->func('communication');
						$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$accesstoken.'&openid='.$fans['openid'].'&lang=zh_CN';
						$response = ihttp_get($url);
						if (!is_error($response)) {
							$userinfo = json_decode($response['content'], true);
							$niemmo = $userinfo['nickname'];
							$userdata = array(
								'nickname' => $userinfo['nickname'],
								'gender' => $userinfo['sex'],
								'avatar' => $userinfo['headimgurl'],
								'resideprovince' => $userinfo['province'],
								'residecity' => $userinfo['city'],
								'nationality' => $userinfo['country'],
							);
							if (empty($userinfo['nickname'])) {
								$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$accinfo['key'].'&redirect_uri='.urlencode(urwdo('nickname','',1).'&uid='.$user['uid']).'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
								gourl($url);
							}
							pdo_update('mc_members', $userdata, array('uid'=>$user['uid']));
						}
					}
				}
			}
		}
		return $niemmo;
	}

}

?>