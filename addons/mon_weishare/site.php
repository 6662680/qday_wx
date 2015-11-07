<?php
/**
 */
defined ( 'IN_IA' ) or exit ( 'Access Denied' );
define ( 'APP_PUBLIC', './source/modules/activity/' );
class Mon_WeiShareModuleSite extends WeModuleSite {
	public $table_share = "weishare";
	public $weishare_reply = "weishare_reply";
	public $weishare_user = "weishare_user";
	public $weishare_firend = "weishare_firend";
	public $weishare_authsetting = "weishare_setting";
	
	/**
	 * 分享管理
	 */
	public function doWebShare() {
		global $_W, $_GPC;
		$operation = ! empty ( $_GPC ['op'] ) ? $_GPC ['op'] : 'display';
		
		if ($operation == 'post') { // 添加
			$id = intval ( $_GPC ['id'] );
			
			if (! empty ( $id )) {
				$item = pdo_fetch ( "SELECT * FROM " . tablename ( $this->table_share ) . " WHERE id = :id", array (
						':id' => $id 
				) );
				if (empty ( $item )) {
					message ( '抱歉，分享删除或或不存在！', '', 'error' );
				}
				
				$item ['endtime'] = date ( "Y-m-d  H:i", $item ['endtime'] );
			}
			if (checksubmit ( 'submit' )) {
				
				if (empty ( $_GPC ['cardname'] )) {
					message ( '请输入卡片名称!' );
				}
				
				if (empty ( $_GPC ['tip'] )) {
					message ( '请输入卡片提示语!' );
				}
				
				if (empty ( $_GPC ['count'] )) {
					message ( '请输入卡片数量!' );
				}
				
				if (empty ( $_GPC ['endtime'] )) {
					message ( '请输入活动结束时间!' );
				}
				
				if ($_GPC ['limittype'] == 0) {
					if (empty ( $_GPC ['totallimit'] )) {
						
						message ( '请输总助力限制次数!' );
					}
				}
				
				if ($_GPC ['limittype'] == 1) {
					if (empty ( $_GPC ['helplimit'] )) {
						
						message ( '请输入好友助力限制次数!' );
					}
				}
				
				if (empty ( $_GPC ['background'] )) {
					message ( '请选择活动背景颜色!' );
				}
				
				if (empty ( $_GPC ['title'] )) {
					message ( '请输入活动名称!' );
				}
				
				if (empty ( $_GPC ['thumb'] )) {
					message ( '请上传活动图片!' );
				}
				
				if (empty ( $_GPC ['image'] )) {
					message ( '请上传活动背景图片!' );
				}
				
				if (empty ( $_GPC ['unit'] )) {
					message ( '请输入积分单位!' );
				}
				
				if (empty ( $_GPC ['max'] )) {
					message ( '请输入得分极限!' );
				}
				if (empty ( $_GPC ['start'] )) {
					message ( '请输入初始分值!' );
				}
				
				if (empty ( $_GPC ['image'] )) {
					message ( '请选择活动背景！' );
				}
				
				if ($_GPC ['steptype'] == 0) {
					if (empty ( $_GPC ['step'] )) {
						message ( '请输入固定积分值!' );
					}
				}
				
				if ($_GPC ['steptype'] == 1) {
					if (empty ( $_GPC ['steprandom'] )) {
						message ( '请输入随机积分值' );
					}
				}
				
				if (empty ( $_GPC ['rule'] )) {
					message ( '请输入活动规则！' );
				}
				
				if (empty ( $_GPC ['shareTitle'] )) {
					
					message ( "请输入活动标题" );
				}
				
				if (empty ( $_GPC ['shareIcon'] )) {
					
					message ( "请选择活动分享图标" );
				}
				
				if (empty ( $_GPC ['shareContent'] )) {
					
					message ( "请输入活动分享内容" );
				}
				
				$data = array (
						'weid' => $_W ['uniacid'],
						'cardname' => $_GPC ['cardname'],
						'tip' => $_GPC ['tip'],
						'count' => $_GPC ['count'],
						'background' => $_GPC ['background'],
						'title' => $_GPC ['title'],
						'limittype' => $_GPC ['limittype'],
						'max' => $_GPC ['max'],
						'unit' => $_GPC ['unit'],
						'start' => $_GPC ['start'],
						'step' => $_GPC ['step'],
						'steprandom' => $_GPC ['steprandom'],
						'steptype' => $_GPC ['steptype'],
						'thumb' => $_GPC ['thumb'],
						'helplimit' => $_GPC ['helplimit'],
						'totallimit' => $_GPC ['totallimit'],
						'url' => $_GPC ['url'],
						'endtime' => strtotime ( $_GPC ['endtime'] ),
						'image' => $_GPC ['image'],
						'rule' => htmlspecialchars_decode ( $_GPC ['rule'] ),
						'copyright' => $_GPC ['copyright'],
						'showu'=>$_GPC['showu'],
						'sortcount'=>$_GPC['sortcount'],
						'createtime' => TIMESTAMP,
						'shareTitle' => $_GPC ['shareTitle'],
						'shareIcon' => $_GPC ['shareIcon'],
						'shareContent' => $_GPC ['shareContent'] 
				);
				if (! empty ( $id )) {
					pdo_update ( $this->table_share, $data, array (
							'id' => $id 
					) );
				} else {
					pdo_insert ( $this->table_share, $data );
				}
				message ( '更新分享活动成功！', $this->createWebUrl ( 'share', array (

						'op' => 'display' 
				) ), 'success' );
			}
		} elseif ($operation == 'display') {
			$pindex = max ( 1, intval ( $_GPC ['page'] ) );
			
			$psize = 20;
			
			$list = pdo_fetchall ( "SELECT * FROM " . tablename ( $this->table_share ) . " WHERE weid = '{$_W['uniacid']}'  ORDER BY createtime DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize );
			$total = pdo_fetchcolumn ( 'SELECT COUNT(*) FROM ' . tablename ( $this->table_share ) . " WHERE weid = '{$_W['uniacid']}'" );
			$pager = pagination ( $total, $pindex, $psize );
		} elseif ($operation == 'delete') {
			$id = intval ( $_GPC ['id'] );
			
			// 删除活动
			
			pdo_delete ( $this->weishare_firend, array (
					'sid' => $id 
			) ); // 删除用户表
			
			pdo_delete ( $this->weishare_user, array (
					'sid' => $id 
			) ); // 删除用户表
			
			pdo_delete ( $this->table_share, array (
					'id' => $id 
			) );
			
			message ( '删除成功！', referer (), 'success' );
		}

		load()->func('tpl');
		include $this->template ( 'share' );
	}
	
	/**
	 * hareUser
	 */
	public function doWebShareUser() {
		global $_W, $_GPC;
		
		$template = "shareuser";
		$operation = ! empty ( $_GPC ['op'] ) ? $_GPC ['op'] : 'display';
		
		$share = pdo_fetch ( "SELECT * FROM " . tablename ( $this->table_share ) . " WHERE id = :id", array (
				':id' => $_GPC ['sid'] 
		) );
		
		if ($operation == 'post') { // 添加
			$id = intval ( $_GPC ['id'] );
			
			if (! empty ( $id )) {
				$item = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_user ) . " WHERE id = :id", array (
						':id' => $id 
				) );
				if (empty ( $item )) {
					message ( '抱歉，领卡删除或或不存在！', '', 'error' );
				}
				
				$template = "editshareuser";
			}
			if (checksubmit ( 'submit' )) {
				
				if (empty ( $_GPC ['helpcount'] )) {
					message ( '请输入助力次数!' );
				}
				
				if (empty ( $_GPC ['income'] )) {
					message ( '请输入金额!' );
				}
				
				$data = array (
						'helpcount' => $_GPC ['helpcount'],
						'income' => $_GPC ['income'] 
				);
				
				if (! empty ( $id )) {
					pdo_update ( $this->weishare_user, $data, array (
							'id' => $id 
					) );
				}
				message ( '更新领卡用户信息成功！', $this->createWebUrl ( 'shareuser', array (
						
						'op' => 'display',
						'sid' => $_GPC ['sid'] 
				) ), 'success' );
			}
		} else if ($operation == 'display') {
			$pindex = max ( 1, intval ( $_GPC ['page'] ) );
			
			$tel = $_GPC ['tel'];
			$psize = 50;
			
			$list = pdo_fetchall ( "SELECT * FROM " . tablename ( $this->weishare_user ) . " WHERE sid = '{$_GPC['sid']}' and tel like :tel ORDER BY income desc, createtime DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array (
					":tel" => "%{$tel}%" 
			) );
			$total = pdo_fetchcolumn ( 'SELECT COUNT(*) FROM ' . tablename ( $this->weishare_user ) . " WHERE sid = '{$_GPC['sid']}' and tel like :tel ", array (
					":tel" => "%{$tel}%" 
			) );
			
			$ftotal = pdo_fetchcolumn ( 'SELECT COUNT(*) FROM ' . tablename ( $this->weishare_firend ) . " WHERE sid = '{$_GPC['sid']}'" );
			
			$pager = pagination ( $total, $pindex, $psize );
		} elseif ($operation == 'delete') {
			$id = intval ( $_GPC ['id'] );
			
			// 删除活动
			
			pdo_delete ( $this->weishare_firend, array (
					'sid' => $id 
			) ); // 删除助力好友表
			
			pdo_delete ( $this->weishare_firend, array (
					'sid' => $id 
			) ); // 删除助力好友表
			
			pdo_delete ( $this->weishare_user, array (
					'id' => $id 
			) );
			
			message ( '删除成功！', referer (), 'success' );
		}
		
		include $this->template ( $template );
	}
	
	/**
	 * 授权设置
	 */
	public function doWebSetting() {
		global $_W, $_GPC;
		
		$setting = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_authsetting ) . " where weid=:weid", array (
				":weid" => $_W ['uniacid']
		) );
		
		if (checksubmit ( 'submit' )) {
			
			$data = array (
					'appid' => $_GPC ['appid'],
					'weid' => $_W ['uniacid'],
					'secret' => $_GPC ['secret'] 
			);
			if (! empty ( $setting )) {
				pdo_update ( $this->weishare_authsetting, $data, array (
						'id' => $setting ['id'] 
				) );
			} else {
				pdo_insert ( $this->weishare_authsetting, $data );
			}
			message ( '更新授权接口成功！', $this->createWebUrl ( 'setting', array (

					'op' => 'display' 
			) ), 'success' );
		}
		
		include $this->template ( 'autho_setting' );
	}
	public function doWebQuery() {
		global $_W, $_GPC;
		$kwd = $_GPC ['keyword'];
		$sql = 'SELECT * FROM ' . tablename ( $this->table_share ) . ' WHERE `weid`=:weid AND `title` LIKE :title';
		$params = array ();
		$params [':weid'] = $_W ['uniacid'];
		$params [':title'] = "%{$kwd}%";
		$ds = pdo_fetchall ( $sql, $params );
		foreach ( $ds as &$row ) {
			$r = array ();
			$r ['title'] = $row ['title'];
			$r ['rule'] = $row ['rule'];
			$r ['thumb'] = $row ['thumb'];
			$r ['id'] = $row ['id'];
			$row ['entry'] = $r;
		}
		include $this->template ( 'query' );
	}

	public function  doMobileAuth2(){
		global $_GPC,$_W;

		$au=$_GPC['au'];
		$id=$_GPC['id'];
		$uid=$_GPC['uid'];
		$code = $_GPC ['code'];

		$userOpenid = $this->getopenid ( $code, null );
		
		if($au=="0"){//用户自己share
			$redirect_uri= $_W ['siteroot'] .'app'.str_replace( './','/',$this->createMobileUrl ( 'share', array (
					'id' => $id,
					'openid'=>$userOpenid
				),true ));

		}else if($au=='1'){//好友share

			$redirect_uri= $_W ['siteroot'] .'app'.str_replace( './','/',$this->createMobileUrl ( 'firendshare', array (
					'sid' => $id,
					'openid'=>$userOpenid,
					'uid'=>$uid
				) ,true));

		}

		header ( "location: $redirect_uri" );
	}

	public function  doMobileAuth(){

		global $_GPC,$_W;

		$au=$_GPC['au'];
		$id=$_GPC['id'];
		$uid=$_GPC['uid'];

		$redirect_uri= $_W ['siteroot'] ."app".str_replace( './','/',$this->createMobileUrl ( 'Auth2', array (
				'id' => $id,
				'au'=>$au,
				'uid'=>$uid
			),true ));
		
		$setting = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_authsetting ) . " where weid=:weid", array (
			":weid" => $_W ['uniacid']
		) );
		
		$appid = $setting ['appid'];
		$secret = $setting ['secret'];
		$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode ( $redirect_uri ) . "&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
		header ( "location: $oauth2_code" );

	}
	public function auth($redirect_uri) {
		global $_GPC, $_W;
		$appid = $_W ['account'] ['key'];
		$secret = $_W ['account'] ['secret'];
		$setting = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_authsetting ) . " where weid=:weid", array (
				":weid" => $_W ['uniacid']
		) );
		$appid = $setting ['appid'];
		$secret = $setting ['secret'];


		$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode ( $redirect_uri ) . "&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
		header ( "location: $oauth2_code" );
	}
	public function getopenid($code, $errorUrl) {
		global $_GPC, $_W;
		
		$setting = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_authsetting ) . " where weid=:weid", array (
				":weid" => $_W ['uniacid']
		) );
		
		
		
		
		$appid = $setting ['appid'];
		$secret = $setting ['secret'];
		
		load()->func('communication');
		
		$oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code";
		$content = ihttp_get ( $oauth2_code );
		$token = @json_decode ( $content ['content'], true );
		
		if (empty ( $token ) || ! is_array ( $token ) || empty ( $token ['access_token'] ) || empty ( $token ['openid'] )) {
			
			if (! empty ( $errorUrl )) {
				header ( "location: $errorUrl" );
			} else {
				echo '<h1>获取微信公众号授权' . $code . '失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content ['meta'] . '<h1>';
				exit ();
			}
		}
		
		return $token ['openid'];
	}
	
	/**
	 * 好友分享点进去页面
	 */
	public function doMobilefirendshare() {
		global $_GPC, $_W;
		$user_agent = $_SERVER ['HTTP_USER_AGENT'];
		if (strpos ( $user_agent, 'MicroMessenger' ) === false) {
			echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			exit ();
		}
		$uid = $_GPC ['uid'];
		$sid = $_GPC ['sid'];
		$firdOpenId = $_GPC['openid'];

		$share = pdo_fetch ( "SELECT * FROM " . tablename ( $this->table_share ) . " WHERE id = :id", array (
			':id' => $sid
		) );

		if (empty ( $share )) {

			echo "活动删除不存在!";
			exit ();
		}

		$shareUser = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_user ) . " WHERE id=:uid  and sid = :sid", array (
			':sid' => $share ['id'],
			':uid' => $uid
		) );

		$shareFirend = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_firend ) . " WHERE uid = :uid and openid=:openid and TO_DAYS( DATE_FORMAT( FROM_UNIXTIME(  `createtime` ) ,  '%Y-%m-%d' ) ) = TO_DAYS( NOW( ) ) ", array (
			':uid' => $uid,
			":openid" => $firdOpenId
		) ); // 查询该用户是否已经给他助力了

		$limitType = $share ['limittype'];

		$limitCount = $share ['helplimit']; // 每天限制次数
		$totalCount = $share ['totallimit']; // 总限制次数
		$firendHelpCount = 0;

		if ($limitType == 1) {
			$firendHelpCount = pdo_fetchcolumn ( "SELECT count(*) FROM " . tablename ( $this->weishare_firend ) . " WHERE uid = :uid and openid=:openid and TO_DAYS( DATE_FORMAT( FROM_UNIXTIME(  `createtime` ) ,  '%Y-%m-%d' ) ) = TO_DAYS( NOW( ) ) ", array (
				':uid' => $uid,
				":openid" => $firdOpenId
			) );
		} else if ($limitType == 0) {

			$firendHelpCount = pdo_fetchcolumn ( "SELECT count(*) FROM " . tablename ( $this->weishare_firend ) . " WHERE uid = :uid and openid=:openid", array (
				':uid' => $uid,
				":openid" => $firdOpenId
			) );
		}

		$leftLimitCount = $limitCount - $firendHelpCount;

		$leftTotalCount = $totalCount - $firendHelpCount; // 总限制次数

		if ($firdOpenId == $shareUser ['from_user']) { // fisrtid是分享人自己

			$isallow = true;
			$resText = "";

			if (TIMESTAMP > $share ['endtime']) {
				$resText = "活动已结束!";
				$isallow = false;
			}

			include $this->template ( "m_share" );
		} else {

			if (TIMESTAMP > $share ['endtime']) {

				$end = true;
			}

			// 查询助力好友是否已经注册过
			$dbRegistUser = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_user ) . " WHERE from_user=:openid  and sid = :sid", array (
				':sid' => $share ['id'],
				':openid' => $firdOpenId
			) );

			include $this->template ( "firend_share" );
		}



	}
	
	/**
	 * 分享
	 */
	public function doMobileShare() {
		global $_GPC, $_W;
		
		$user_agent = $_SERVER ['HTTP_USER_AGENT'];
		if (strpos ( $user_agent, 'MicroMessenger' ) === false) {
			echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			exit ();
		}


		$userOpenid = $_GPC ['openid'];
		
		
		$id = $_GPC ['id'];

		$share = pdo_fetch ( "SELECT * FROM " . tablename ( $this->table_share ) . " WHERE id = :id", array (
					':id' => $_GPC ['id'] 
			) );
			
			if (empty ( $share )) {
				message ( '抱歉，分享删除或或不存在！', '', 'error' );
			}
			
			$shareUser = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_user ) . " WHERE from_user=:from_user  and sid = :sid", array (
					':sid' => $share ['id'],
					':from_user' => $userOpenid 
			) );
			
			$isallow = true;
			$resText = "";
			
			$total = pdo_fetchcolumn ( 'SELECT COUNT(*) FROM ' . tablename ( $this->weishare_user ) . " WHERE sid = '{$share['id']}' " );
			
			if (TIMESTAMP > $share ['endtime']) {
				$resText = "活动已结束!";
				$isallow = false;
			}
			
			if (empty ( $shareUser )) {
				
				if ($total >= $share ['count']) {
					$resText = "对不起卡片数量已领完!";
					$isallow = false;
				}
				
				$template = "regist_card"; // 注册
			} else {
				
				$template = "m_share"; // 我的信息
			}

		
		include $this->template ( $template );
	}
	
	/**
	 * 注册领卡
	 */
	public function doMobileRegist() {
		global $_GPC, $_W;
		
		$sid = $_GPC ['sid'];
		$tel = $_GPC ['tel'];
		
		$openid = $_GPC ["openid"];
		
		$res = "";
		
		$user_agent = $_SERVER ['HTTP_USER_AGENT'];
		if (strpos ( $user_agent, 'MicroMessenger' ) === false) {
			echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			exit ();
		}
		
		$shareUser = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_user ) . " WHERE tel = :tel and sid=:sid", array (
				':tel' => $tel,
				':sid' => $sid 
		) );
		
		if (! empty ( $shareUser )) {
			$res = "手机号码已存在请重新填写手机号!";
		} else {
			
			$share = pdo_fetch ( "SELECT * FROM " . tablename ( $this->table_share ) . " WHERE id = :id", array (
					':id' => $sid 
			) );
			
			if (empty ( $share )) {
				message ( "分享活动删除或不存在!" );
			}
			
			$data = array (
					'from_user' => $openid,
					'sid' => $sid,
					'income' => $share ['start'],
					'tel' => $_GPC ['tel'],
					'createtime' => TIMESTAMP 
			);
			pdo_insert ( $this->weishare_user, $data );
			$res = "领卡成功!";
		}
		
		include $this->template ( "regist_result" );
	}
	
	/**
	 * 积分排名查看
	 */
	public function doMobilesort() {
		global $_GPC, $_W;
		
		$user_agent = $_SERVER ['HTTP_USER_AGENT'];
		if (strpos ( $user_agent, 'MicroMessenger' ) === false) {
			echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			exit ();
		}
		
		$sid = $_GPC ['id'];
		$uid=$_GPC["uid"];
		
		$share = pdo_fetch ( "SELECT * FROM " . tablename ( $this->table_share ) . " WHERE id = :id", array (
				':id' => $sid 
		) );
		
		if (empty ( $share )) {
			
			echo "活动删除或不存在";
			exit ();
		}
		
		$sortcount=$share['sortcount'];
		
		
		$shareUser = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_user ) . " WHERE id=:uid  and sid = :sid", array (
					':sid' => $share ['id'],
					':uid' => $uid 
			) );
		
		
		
		$list = pdo_fetchall("SELECT @rownum:=@rownum+1 AS rowno ,u.*  FROM (SELECT @rownum:=0 ) r, " . tablename($this->weishare_user) . " u WHERE u.sid=:sid  ORDER BY income DESC limit 0,".$sortcount, array(
				":sid" => $sid
		));
		
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->weishare_user) . " WHERE sid=:sid", array(
				":sid" => $sid
		));
		
		
		include $this->template ( "sort" );
	}
	
	/**
	 * 下载用户数据
	 */
	public function  doWebdownload(){
		
		require_once 'download.php';
		
	}
	
	/**
	 * 助力
	 */
	public function doMobileHelp() {
		global $_GPC, $_W;
		
		$user_agent = $_SERVER ['HTTP_USER_AGENT'];
		if (strpos ( $user_agent, 'MicroMessenger' ) === false) {
			echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			exit ();
		}
		
		
		$sid = $_GPC ['sid'];
		$uid = $_GPC ['uid'];
		$code = $_GPC ['code'];
		
		if (empty ( $code )) { // 第一步
			
			$url = $_W ['siteroot'] .app.str_replace('./','/',$this->createMobileUrl ( 'help', array (
					'sid' => $sid,
					'uid' => $uid 
			),true ));
			
			$this->auth ( $url );
		} else {
			
			$firendOpenid = $this->getopenid ( $code, null ); // 助力人 openid
			
			$resText = "";
			
			$share = pdo_fetch ( "SELECT * FROM " . tablename ( $this->table_share ) . " WHERE id = :id", array (
					':id' => $sid 
			) );
			
			$shareUser = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_user ) . " WHERE id=:uid  and sid = :sid", array (
					':sid' => $share ['id'],
					':uid' => $uid 
			) );
			
			$firendHelpCount = 0;
			
			$limitType = $share ['limittype'];
			$limitCount = $share ['helplimit']; // 每天限制次数
			$totalCount = $share ['totallimit'];
			
			if ($limitType == 1) {
				$firendHelpCount = pdo_fetchcolumn ( "SELECT count(*) FROM " . tablename ( $this->weishare_firend ) . " WHERE uid = :uid and openid=:openid and TO_DAYS( DATE_FORMAT( FROM_UNIXTIME(  `createtime` ) ,  '%Y-%m-%d' ) ) = TO_DAYS( NOW( ) ) ", array (
						':uid' => $uid,
						":openid" => $firendOpenid 
				) );
			} else if ($limitType == 0) {
				
				$firendHelpCount = pdo_fetchcolumn ( "SELECT count(*) FROM " . tablename ( $this->weishare_firend ) . " WHERE uid = :uid and openid=:openid", array (
						':uid' => $uid,
						":openid" => $firendOpenid 
				) );
			}
			
			$leftLimitCount = $limitCount - $firendHelpCount;
			
			$leftTotalCount = $totalCount - $firendHelpCount; // 总限制次数
			
			if ($limitType == 0 && $leftTotalCount == 0) { // 没个好友稚嫩改之一次
				
				$resText = "哥们助力次数已用完!";
			}
			if ($leftLimitCount == 0 && $limitType == 1) {
				$resText = "哥们今天助力次数已用完!";
			} else if (($limitType == 0 && $leftTotalCount > 0) || ($limitType == 1 && $leftLimitCount > 0)) {
				
				$max = $share ['max'];
				$income = $shareUser ['income'];
				if ($income > $max) {
					$resText = "助力成功!";
				} else {
					
					if ($share ['steptype'] == 0) {
						$income = $income + $share ['step'];
					} else if ($share ['steptype'] == 1) {
						
						$radmon = rand ( 0, $share ['steprandom'] * 100 ) / 100;
						
						$income = $income + $radmon;
					}
					
					if ($income > $max) {
						$income = $max;
					}
					
					$data = array (
							'uid' => $uid,
							'openid' => $firendOpenid,
							'sid' => $sid,
							'createtime' => TIMESTAMP 
					);
					pdo_insert ( $this->weishare_firend, $data ); // 记录助力人
					
					$updatedata = array (
							'helpcount' => $shareUser ['helpcount'] + 1,
							'income' => $income 
					);
					
					// 更新user 表数据
					pdo_update ( $this->weishare_user, $updatedata, array (
							'id' => $uid 
					) );
					
					$resText = "助力一次成功!";
				}
			}
		}
		
		include $this->template ( "help_result" );
	}
	
	/**
	 * 规则
	 */
	public function doMobileRule() {
		global $_GPC;
		
		global $_W;
		
		
		
		
		
		$share = pdo_fetch ( "SELECT * FROM " . tablename ( $this->table_share ) . " WHERE id = :id", array (
				':id' => $_GPC ['id'] 
		) );
		
		
			$shareUser = pdo_fetch ( "SELECT * FROM " . tablename ( $this->weishare_user ) . " WHERE id=:uid  and sid = :sid", array (
					':sid' => $share ['id'],
					':uid' => $_GPC["uid"]
			) );
		
		
		
		if (empty ( $share )) {
			message ( '抱歉，分享删除或或不存在！', '', 'error' );
		}
		
		include $this->template ( "rule" );
	}
}