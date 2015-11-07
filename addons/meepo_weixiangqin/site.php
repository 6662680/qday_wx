<?php
session_start();
function arrayChange($a)
{
	static $arr2;
	foreach ($a as $v) {
		if (is_array($v)) {
			arrayChange($v);
		} else {
			$arr2[] = $v;
		}
	}
	return $arr2;
}

defined('IN_IA') or exit('Access Denied');
define(EARTH_RADIUS, 6371);
define('RES', '../addons/meepo_weixiangqin/template');
define('MEEPORES', '../addons/meepo_weixiangqin/template/mobile/tpl');
define('RES2', '../addons/meepo_weixiangqin/template/style/');

class Meepo_weixiangqinModuleSite extends WeModuleSite
{
	public $modulename = 'meepo_weixiangqin';

	public function docheckurl()
	{
		global $_GPC, $_W;
		if (isset($_SESSION['views'])) {
			$website = $_SESSION['views'];
			//if (empty($website)) {
			//	die('链接失败，请咨询开发者！');
			//}
			unset($_SESSION['views']);
			if (!in_array($_W['siteroot'], $website)) {
				return 1;
			} else {
				return false;
			}
		} else {
			$content = file_get_contents('http://viveka.cn/addons/meepo_weixiangqin/check.php');
			//if (empty($content)) {
			//	die('链接失败，请咨询管理员！');
			//}
			$content = json_decode($content);
			if (!in_array($_W['siteroot'], $content)) {
				return 1;
			} else {
				$_SESSION['views'] = $content;
				return false;
			}
		}
	}

	public function doMobileAlllist()
	{
		global $_W;
		$_GPC;
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		} else {
			$url = $this->createMobileUrl('Errorjoin');
			header("location:$url");
			exit;
		}
		$weid = $_W['uniacid'];
		$slide = pdo_fetchall("SELECT * FROM " . tablename('meepoweixiangqin_slide') . " WHERE weid = :weid AND status=1 ORDER BY displayorder DESC,id DESC LIMIT 6", array(':weid' => $weid));
		$sujinum = rand();
		$openid = $_W['fans']['from_user'];
		if (!empty($_POST['curlat']) && !empty($_POST['curlng'])) {
			pdo_update("hnfans", array('lat' => $_POST['curlat'], 'lng' => $_POST['curlng']), array('weid' => $weid, 'from_user' => $openid));
		}
		$cfg = $this->module['config'];
		$appid = $cfg['appid'];
		$secret = $cfg['secret'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		if (!empty($openid)) {
			$sql = 'SELECT `follow`,`openid`,`uid` FROM ' . tablename('mc_mapping_fans') . ' WHERE `uniacid`=:uniacid AND `openid`=:openid';
			$pars = array();
			$pars[':uniacid'] = $weid;
			$pars[':openid'] = $openid;
			$fan = pdo_fetch($sql, $pars);
			if ($fan['follow'] != '1') {
				$url = empty($settings['url']) ? 'http://baidu.com' : $settings['url'];
				header("location:$url");
				exit;
			} else {
				$res = $this->getres2($openid);
				$cfg = $this->module['config'];
				if ($_W['fans']['from_user'] == "fromUser") {
				} else {
					if (empty($res['nickname'])) {
						$this->insertit();
					}
				}
				$tablename = tablename("hnfans");
				$psize = 5;
				$pindex = 1;
				$isshow = 1;
				$list2 = pdo_fetchall("SELECT *  FROM " . $tablename . " WHERE  weid={$weid} AND nickname!='' AND isshow={$isshow}  AND yingcang=1 ORDER BY time DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
				if (!empty($list2)) {
					foreach ($list2 as $row) {
						if (!empty($row['lat']) && !empty($row['lng'])) {
							if (!empty($res['lat']) && !empty($res['lng'])) {
								$juli[$row['id']] = "相距: " . $this->getDistance($res['lat'], $res['lng'], $row['lat'], $row['lng']) . "km";
							} else {
								$juli[$row['id']] = "";
							}
						} else {
							$juli[$row['id']] = "";
						}
					}
				}
			}
		} else {
			$url = empty($settings['url']) ? 'http://baidu.com' : $settings['url'];
			header("location:$url");
			exit;
		}
		include $this->template('alllist');
	}

	public function doMobileposition()
	{
		global $_W;
		$_GPC;
		$weid = $_W['uniacid'];
		$openid = $_W['openid'];
		if (!empty($_POST['curlat']) && !empty($_POST['curlng'])) {
			$res = $this->getres2($openid);
			if (empty($res['lat'])) {
				pdo_update("hnfans", array('lat' => $_POST['curlat'], 'lng' => $_POST['curlng']), array('weid' => $weid, 'from_user' => $openid));
			}
		}
	}

	private function insertit()
	{
		global $_W;
		$openid = $_W['openid'];
		$weid = $_W['uniacid'];
		$cfg = $this->module['config'];
		$appid = $cfg['appid'];
		$secret = $cfg['secret'];
		if (empty($appid) || empty($secret)) {
			die('管理员配置的参数有误');
		}
		load()->classs('weixin.account');
		$accObj = WeixinAccount::create($_W['account']['acid']);
		$access_token = $accObj->fetch_token();
		$token2 = $access_token;
		if (empty($token2)) {
			die('管理员配置的参数有误');
		} else {
			load()->func('communication');
			$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $token2 . '&openid=' . $openid . '&lang=zh_CN';
			$content2 = ihttp_request($url);
			$info = @json_decode($content2['content'], true);
			if (empty($info['nickname'])) {
				die('管理员配置的参数有误');
			} else {
				$row = array();
				$onoff = $this->getonoff($weid);
				$row = array('nickname' => $info["nickname"], 'realname' => $info["nickname"], 'avatar' => $info["headimgurl"], 'gender' => $info['sex'], 'time' => time());
				if ($onoff['status'] != '0') {
					$row['isshow'] = 0;
				} else {
					$row['isshow'] = 1;
				}
				if ($cfg['yingcang'] == '2') {
					$row['yingcang'] = 2;
				} else {
					$row['yingcang'] = 1;
				}
				if (!empty($info["country"])) {
					$row['nationality'] = $info["country"];
				}
				if (!empty($info["province"])) {
					$row['resideprovincecity'] = $info["province"] . $info["city"];
				}
				$res = $this->getres2($openid);
				if (!empty($res)) {
					pdo_update('hnfans', array('avatar' => $info["headimgurl"], 'nationality' => $info["country"], 'resideprovincecity' => $info["province"] . $info["city"], 'nickname' => $info["nickname"]), array('from_user' => $openid, 'weid' => $weid));
				} else {
					$row['weid'] = $weid;
					$row['from_user'] = $openid;
					pdo_insert('hnfans', $row);
				}
			}
		}
	}

	public function doMobileGetMoreAll()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$cfg = $this->module['config'];
		$openid = $_W['openid'];
		$julires = $this->getres2($openid);
		$page = intval($_GPC['page']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 5;
		$condition = '';
		$isshow = 1;
		$tablename = tablename("hnfans");
		$stores = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE weid = :weid AND nickname!='' AND isshow=:isshow AND yingcang=1 {$condition} ORDER BY time DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid, ':isshow' => $isshow));
		if (!empty($stores)) {
			foreach ($stores as $row) {
				if (!empty($row['lat']) && !empty($row['lng'])) {
					if (!empty($julires['lat']) && !empty($julires['lng'])) {
						$juli[$row['id']] = "相距: " . $this->getDistance($julires['lat'], $julires['lng'], $row['lat'], $row['lng']) . "km";
					} else {
						$juli[$row['id']] = "";
					}
				} else {
					$juli[$row['id']] = "";
				}
			}
		}
		$result_str = '';
		foreach ($stores as $row) {
			$result_str .= '<li class="indexItem"><span  class="linka" date="' . $row['id'] . '">';
			if (preg_match('/http:(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $row['avatar'] . '" alt="用户头像">';
			} elseif (preg_match('/images(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $_W['attachurl'] . $row['avatar'] . '" alt="用户头像">';
			} else {
				$result_str .= '<img src="../addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg" alt="用户头像">';
			}
			$result_str .= '<div class="itemc"><p class="hcolor" style="font-size:13px;">' . cutstr($row['realname'], 5, true);
			if ($row['gender'] == '1') {
				$result_str .= "&nbsp;&nbsp;男";
			} elseif ($row['gender'] == '2') {
				$result_str .= "&nbsp;&nbsp;女";
			} else {
				$result_str .= "&nbsp;&nbsp;保密";
			}
			$result_str .= '<font id="shopspostion" style="color:red;font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $juli[$row['id']] . '</font></p>
          <p class="lcolor" style="font-size:13px;">微信:' . cutstr($row['nickname'], 5, true) . '&nbsp;&nbsp;' . $row['resideprovincecity'] . '</p>
		  <input type="hidden" class="nickname' . $row['id'] . '" value="' . $row['nickname'] . '"/>
		  <input type="hidden" class="toopenid' . $row['id'] . '" value="' . $row['from_user'] . '"/>
		  <input type="hidden" class="openid' . $row['id'] . '" value="' . $openid . '"/>
        </div>
        <i class="arr"></i>
         </span>    
    	<div class="likebox">
    		<div class="likeit  fleft "><span class="hitlike" date="' . $row['id'] . '" title="' . $row['openid'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $row['love'] . '</span></div>
    		<div class="likeit letterit fright"><a class="hitmail"   href="' . $this->createMobileUrl('hitmail', array('toname' => $row['nickname'], 'toopenid' => $row['from_user'])) . '" target="__blank" style="color:#fff">聊一聊</a></div></div>
      
      </li><li class="dottedLine"></li>';
		}
		if ($result_str == '' || empty($stores)) {
			echo json_encode(0);
		} else {
			echo json_encode($result_str);
		}
	}

	public function doMobilemeepohot()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		$page = intval($_GPC['truepage']);
		$pindex = max(1, intval($_GPC['truepage']));
		$psize = 5;
		$condition = '';
		$gender = 1;
		$isshow = 1;
		$tablename = tablename("hnfans");
		$stores = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE weid = :weid AND nickname!='' AND yingcang=1 AND isshow=:isshow  AND  gender=:gender {$condition} ORDER BY love DESC,id DESC,time DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid, ':isshow' => $isshow, ':gender' => $gender));
		$julires = $this->getres2($openid);
		if (!empty($stores)) {
			foreach ($stores as $row) {
				if (!empty($row['lat']) && !empty($row['lng'])) {
					if (!empty($julires['lat']) && !empty($julires['lng'])) {
						$juli[$row['id']] = "相距: " . $this->getDistance($julires['lat'], $julires['lng'], $row['lat'], $row['lng']) . "km";
					} else {
						$juli[$row['id']] = "";
					}
				} else {
					$juli[$row['id']] = "";
				}
			}
		}
		$result_str = '';
		foreach ($stores as $row) {
			$result_str .= '<li class="indexItem"><span  class="linka" date="' . $row['id'] . '">';
			if (preg_match('/http:(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $row['avatar'] . '" alt="用户头像">';
			} elseif (preg_match('/images(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $_W['attachurl'] . $row['avatar'] . '" alt="用户头像">';
			} else {
				$result_str .= '<img src="../addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg" alt="用户头像">';
			}
			$result_str .= '<div class="itemc"><p class="hcolor" style="font-size:13px;">' . cutstr($row['realname'], 5, true);
			if ($row['gender'] == '1') {
				$result_str .= "&nbsp;&nbsp;男";
			} elseif ($row['gender'] == '2') {
				$result_str .= "&nbsp;&nbsp;女";
			} else {
				$result_str .= "&nbsp;&nbsp;保密";
			}
			$result_str .= '<font id="shopspostion" style="color:red;font-size:12px;">&nbsp;&nbsp;' . $juli[$row['id']] . '</font></p>
          <p class="lcolor" style="font-size:13px;">微信:' . cutstr($row['nickname'], 5, true) . '&nbsp;&nbsp;' . $row['resideprovincecity'] . '</p>
		  <input type="hidden" class="nickname' . $row['id'] . '" value="' . $row['nickname'] . '"/>
		  <input type="hidden" class="toopenid' . $row['id'] . '" value="' . $row['from_user'] . '"/>
		  <input type="hidden" class="openid' . $row['id'] . '" value="' . $openid . '"/>
        </div>
        <i class="arr"></i>
         </span>    
    	<div class="likebox">
    		<div class="likeit  fleft "><span class="hitlike" date="' . $row['id'] . '" title="' . $row['openid'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $row['love'] . '</span></div>
    		<div class="likeit letterit fright"><a class="hitmail"   href="' . $this->createMobileUrl('hitmail', array('toname' => $row['nickname'], 'toopenid' => $row['from_user'])) . '" target="__blank" style="color:#fff">聊一聊</a></div></div>
      
      </li><li class="dottedLine"></li>';
		}
		if ($result_str == '' || empty($stores)) {
			echo json_encode(0);
		} else {
			echo json_encode($result_str);
		}
	}

	public function doMobilemeeponew()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		$page = intval($_GPC['truepage']);
		$pindex = max(1, intval($_GPC['truepage']));
		$psize = 5;
		$condition = '';
		$gender = 2;
		$isshow = 1;
		$tablename = tablename("hnfans");
		$stores = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE weid = :weid AND nickname!='' AND isshow=:isshow AND yingcang=1 AND gender=:gender {$condition} ORDER BY love DESC,id DESC,time DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid, ':isshow' => $isshow, ':gender' => $gender));
		$julires = $this->getres2($openid);
		if (!empty($stores)) {
			foreach ($stores as $row) {
				if (!empty($row['lat']) && !empty($row['lng'])) {
					if (!empty($julires['lat']) && !empty($julires['lng'])) {
						$juli[$row['id']] = "相距: " . $this->getDistance($julires['lat'], $julires['lng'], $row['lat'], $row['lng']) . "km";
					} else {
						$juli[$row['id']] = "";
					}
				} else {
					$juli[$row['id']] = "";
				}
			}
		}
		$result_str = '';
		foreach ($stores as $row) {
			$result_str .= '<li class="indexItem"><span  class="linka" date="' . $row['id'] . '">';
			if (preg_match('/http:(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $row['avatar'] . '" alt="用户头像">';
			} elseif (preg_match('/images(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $_W['attachurl'] . $row['avatar'] . '" alt="用户头像">';
			} else {
				$result_str .= '<img src="../addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg" alt="用户头像">';
			}
			$result_str .= '<div class="itemc"><p class="hcolor" style="font-size:13px;">' . cutstr($row['realname'], 5, true);
			if ($row['gender'] == '1') {
				$result_str .= "&nbsp;&nbsp;男";
			} elseif ($row['gender'] == '2') {
				$result_str .= "&nbsp;&nbsp;女";
			} else {
				$result_str .= "&nbsp;&nbsp;保密";
			}
			$result_str .= '<font id="shopspostion" style="color:red;font-size:12px;">&nbsp;&nbsp;' . $juli[$row['id']] . '</font></p>
          <p class="lcolor" style="font-size:13px;">微信:' . cutstr($row['nickname'], 5, true) . '&nbsp;&nbsp;' . $row['resideprovincecity'] . '</p>
		  <input type="hidden" class="nickname' . $row['id'] . '" value="' . $row['nickname'] . '"/>
		  <input type="hidden" class="toopenid' . $row['id'] . '" value="' . $row['from_user'] . '"/>
		  <input type="hidden" class="openid' . $row['id'] . '" value="' . $openid . '"/>
        </div>
        <i class="arr"></i>
         </span>    
    	<div class="likebox">
    		<div class="likeit  fleft "><span class="hitlike" date="' . $row['id'] . '" title="' . $row['openid'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $row['love'] . '</span></div>
    		<div class="likeit letterit fright"><a class="hitmail"  href="' . $this->createMobileUrl('hitmail', array('toname' => $row['nickname'], 'toopenid' => $row['from_user'])) . '" target="__blank" style="color:#fff">聊一聊</a></div></div>
      
      </li><li class="dottedLine"></li>';
		}
		if ($result_str == '' || empty($stores)) {
			echo json_encode(0);
		} else {
			echo json_encode($result_str);
		}
	}

	public function doMobilemeepoilike()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		$page = intval($_GPC['truepage']);
		$pindex = max(1, intval($_GPC['truepage']));
		$psize = 5;
		$condition = '';
		$isshow = 1;
		$tablename = tablename("meepo_hongnianglikes");
		$myloves = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE weid = :weid  AND openid=:openid  {$condition} ORDER BY createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid, ':openid' => $openid));
		if (!empty($myloves)) {
			foreach ($myloves as $row) {
				$stores[] = $this->getres2($row['toopenid']);
			}
		} else {
			echo json_encode(0);
			exit;
		}
		$result_str = '';
		$julires = $this->getres2($openid);
		if (!empty($stores)) {
			foreach ($stores as $row) {
				if (!empty($row['lat']) && !empty($row['lng'])) {
					if (!empty($julires['lat']) && !empty($julires['lng'])) {
						$juli[$row['id']] = "相距: " . $this->getDistance($julires['lat'], $julires['lng'], $row['lat'], $row['lng']) . "km";
					} else {
						$juli[$row['id']] = "";
					}
				} else {
					$juli[$row['id']] = "";
				}
			}
		}
		foreach ($stores as $row) {
			$result_str .= '<li class="indexItem"><span  class="linka" date="' . $row['id'] . '">';
			if (preg_match('/http:(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $row['avatar'] . '" alt="用户头像">';
			} elseif (preg_match('/images(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $_W['attachurl'] . $row['avatar'] . '" alt="用户头像">';
			} else {
				$result_str .= '<img src="../addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg" alt="用户头像">';
			}
			$result_str .= '<div class="itemc"><p class="hcolor" style="font-size:13px;">' . cutstr($row['realname'], 5, true);
			if ($row['gender'] == '1') {
				$result_str .= "&nbsp;&nbsp;男";
			} elseif ($row['gender'] == '2') {
				$result_str .= "&nbsp;&nbsp;女";
			} else {
				$result_str .= "&nbsp;&nbsp;保密";
			}
			$result_str .= '<font id="shopspostion" style="color:red;font-size:12px;">&nbsp;&nbsp;' . $juli[$row['id']] . '</font></p>
          <p class="lcolor" style="font-size:13px;">微信:' . cutstr($row['nickname'], 5, true) . '&nbsp;&nbsp;' . $row['resideprovincecity'] . '</p>
		  <input type="hidden" class="nickname' . $row['id'] . '" value="' . $row['nickname'] . '"/>
		  <input type="hidden" class="toopenid' . $row['id'] . '" value="' . $row['from_user'] . '"/>
		  <input type="hidden" class="openid' . $row['id'] . '" value="' . $openid . '"/>
        </div>
        <i class="arr"></i>
         </span>    
    	<div class="likebox">
    		<div class="likeit  fleft "><span class="hitlike" date="' . $row['id'] . '" title="' . $row['openid'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $row['love'] . '</span></div>
    		<div class="likeit letterit fright"><a class="hitmail"  href="' . $this->createMobileUrl('hitmail', array('toname' => $row['nickname'], 'toopenid' => $row['from_user'])) . '" target="__blank" style="color:#fff">聊一聊</a></div></div>
      
      </li><li class="dottedLine"></li>';
		}
		if ($result_str == '' || empty($stores) || empty($myloves)) {
			echo json_encode(0);
		} else {
			echo json_encode($result_str);
		}
	}

	public function doMobilemeeposomelikeme()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		$page = intval($_GPC['truepage']);
		$pindex = max(1, intval($_GPC['truepage']));
		$psize = 5;
		$condition = '';
		$tablename = tablename("meepo_hongnianglikes");
		$myloves = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE  weid = :weid  AND toopenid=:toopenid {$condition} ORDER BY createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid, ':toopenid' => $openid));
		if (!empty($myloves)) {
			foreach ($myloves as $row) {
				$stores[] = $this->getres2($row['openid']);
			}
		} else {
			echo json_encode(0);
			exit;
		}
		$julires = $this->getres2($openid);
		if (!empty($stores)) {
			foreach ($stores as $row) {
				if (!empty($row['lat']) && !empty($row['lng'])) {
					if (!empty($julires['lat']) && !empty($julires['lng'])) {
						$juli[$row['id']] = "相距: " . $this->getDistance($julires['lat'], $julires['lng'], $row['lat'], $row['lng']) . "km";
					} else {
						$juli[$row['id']] = "";
					}
				} else {
					$juli[$row['id']] = "";
				}
			}
		}
		$result_str = '';
		foreach ($stores as $row) {
			$result_str .= '<li class="indexItem"><span  class="linka" date="' . $row['id'] . '">';
			if (preg_match('/http:(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $row['avatar'] . '" alt="用户头像">';
			} elseif (preg_match('/images(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $_W['attachurl'] . $row['avatar'] . '" alt="用户头像">';
			} else {
				$result_str .= '<img src="../addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg" alt="用户头像">';
			}
			$result_str .= '<div class="itemc"><p class="hcolor" style="font-size:13px;">' . cutstr($row['realname'], 5, true);
			if ($row['gender'] == '1') {
				$result_str .= "&nbsp;&nbsp;男";
			} elseif ($row['gender'] == '2') {
				$result_str .= "&nbsp;&nbsp;女";
			} else {
				$result_str .= "&nbsp;&nbsp;保密";
			}
			$result_str .= '<font id="shopspostion" style="color:red;font-size:12px;">&nbsp;&nbsp;' . $juli[$row['id']] . '</font></p>
          <p class="lcolor" style="font-size:13px;">微信:' . cutstr($row['nickname'], 5, true) . '&nbsp;&nbsp;' . $row['resideprovincecity'] . '</p>
		  <input type="hidden" class="nickname' . $row['id'] . '" value="' . $row['nickname'] . '"/>
		  <input type="hidden" class="toopenid' . $row['id'] . '" value="' . $row['from_user'] . '"/>
		  <input type="hidden" class="openid' . $row['id'] . '" value="' . $openid . '"/>
        </div>
        <i class="arr"></i>
         </span>    
    	<div class="likebox">
    		<div class="likeit  fleft "><span class="hitlike" date="' . $row['id'] . '" title="' . $row['openid'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $row['love'] . '</span></div>
    		<div class="likeit letterit fright"><a class="hitmail"  href="' . $this->createMobileUrl('hitmail', array('toname' => $row['nickname'], 'toopenid' => $row['from_user'])) . '" target="__blank" style="color:#fff">聊一聊</a></div></div>
      
      </li><li class="dottedLine"></li>';
		}
		if ($result_str == '' || empty($stores) || empty($myloves)) {
			echo json_encode(0);
		} else {
			echo json_encode($result_str);
		}
	}

	public function doMobilesousuomore()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		$page = intval($_GPC['truepage']);
		$pindex = max(1, intval($_GPC['truepage']));
		$psize = 5;
		$condition = '';
		if (!empty($_GPC['keyword'])) {
			$keword = trim($_GPC['keyword']);
		}
		$isshow = 1;
		$tablename = tablename("hnfans");
		$stores = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE nickname like '%{$keword}%' AND  weid = :weid  AND yingcang=1 AND isshow=:isshow     {$condition} ORDER BY love DESC,id DESC,time DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid, ':isshow' => $isshow));
		$julires = $this->getres2($openid);
		if (!empty($stores)) {
			foreach ($stores as $row) {
				if (!empty($row['lat']) && !empty($row['lng'])) {
					if (!empty($julires['lat']) && !empty($julires['lng'])) {
						$juli[$row['id']] = "相距: " . $this->getDistance($julires['lat'], $julires['lng'], $row['lat'], $row['lng']) . "km";
					} else {
						$juli[$row['id']] = "";
					}
				} else {
					$juli[$row['id']] = "";
				}
			}
		}
		$result_str = '';
		foreach ($stores as $row) {
			$result_str .= '<li class="indexItem"><span  class="linka" date="' . $row['id'] . '">';
			if (preg_match('/http:(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $row['avatar'] . '" alt="用户头像">';
			} elseif (preg_match('/images(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $_W['attachurl'] . $row['avatar'] . '" alt="用户头像">';
			} else {
				$result_str .= '<img src="../addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg" alt="用户头像">';
			}
			$result_str .= '<div class="itemc"><p class="hcolor" style="font-size:13px;">' . cutstr($row['realname'], 5, true);
			if ($row['gender'] == '1') {
				$result_str .= "&nbsp;&nbsp;男";
			} elseif ($row['gender'] == '2') {
				$result_str .= "&nbsp;&nbsp;女";
			} else {
				$result_str .= "&nbsp;&nbsp;保密";
			}
			$result_str .= '<font id="shopspostion" style="color:red;font-size:12px;">&nbsp;&nbsp;' . $juli[$row['id']] . '</font></p>
          <p class="lcolor" style="font-size:13px;">微信:' . cutstr($row['nickname'], 5, true) . '&nbsp;&nbsp;' . $row['resideprovincecity'] . '</p>
		  <input type="hidden" class="nickname' . $row['id'] . '" value="' . $row['nickname'] . '"/>
		  <input type="hidden" class="toopenid' . $row['id'] . '" value="' . $row['from_user'] . '"/>
		  <input type="hidden" class="openid' . $row['id'] . '" value="' . $openid . '"/>
        </div>
        <i class="arr"></i>
         </span>    
    	<div class="likebox">
    		<div class="likeit  fleft "><span class="hitlike" date="' . $row['id'] . '" title="' . $row['openid'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $row['love'] . '</span></div>
    		<div class="likeit letterit fright"><a class="hitmail"   href="' . $this->createMobileUrl('hitmail', array('toname' => $row['nickname'], 'toopenid' => $row['from_user'])) . '" target="__blank" style="color:#fff">聊一聊</a></div></div>
      
      </li><li class="dottedLine"></li>';
		}
		if ($result_str == '' || empty($stores)) {
			echo json_encode(0);
		} else {
			echo json_encode($result_str);
		}
	}

	public function doMobilemeepozuipei()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		$res = $this->getres2($openid);
		if (empty($res) || empty($res['uheightL']) || empty($res['uheightH'])) {
			echo json_encode(2);
			exit;
		}
		if ($res['gender'] == '0') {
			echo json_encode(3);
			exit;
		}
		$page = intval($_GPC['truepage']);
		$pindex = max(1, intval($_GPC['truepage']));
		$psize = 5;
		$condition = '';
		$isshow = 1;
		$tablename = tablename("hnfans");
		$stores = pdo_fetchall("SELECT * FROM " . $tablename . " WHERE  yingcang=1 AND weid=:weid  AND isshow=:isshow AND gender <>:gender  AND gender <>'0'  AND lxxingzuo=:lxxingzuo AND height>{$res['uheightL']} AND height<{$res['uheightH']} {$condition} ORDER BY time DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid, ':isshow' => $isshow, ':gender' => $res['gender'], ':lxxingzuo' => $res['lxxingzuo']));
		$julires = $this->getres2($openid);
		if (!empty($stores)) {
			foreach ($stores as $row) {
				if (!empty($row['lat']) && !empty($row['lng'])) {
					if (!empty($julires['lat']) && !empty($julires['lng'])) {
						$juli[$row['id']] = "相距: " . $this->getDistance($julires['lat'], $julires['lng'], $row['lat'], $row['lng']) . "km";
					} else {
						$juli[$row['id']] = "";
					}
				} else {
					$juli[$row['id']] = "";
				}
			}
		}
		$result_str = '';
		foreach ($stores as $row) {
			$result_str .= '<li class="indexItem"><span  class="linka" date="' . $row['id'] . '">';
			if (preg_match('/http:(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $row['avatar'] . '" alt="用户头像">';
			} elseif (preg_match('/images(.*)/', $row['avatar'])) {
				$result_str .= '<img src="' . $_W['attachurl'] . $row['avatar'] . '" alt="用户头像">';
			} else {
				$result_str .= '<img src="./addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg" alt="用户头像">';
			}
			$result_str .= '<div class="itemc"><p class="hcolor" style="font-size:13px;">' . cutstr($row['realname'], 5, true);
			if ($row['gender'] == '1') {
				$result_str .= "&nbsp;&nbsp;男";
			} elseif ($row['gender'] == '2') {
				$result_str .= "&nbsp;&nbsp;女";
			} else {
				$result_str .= "&nbsp;&nbsp;保密";
			}
			$result_str .= '<font id="shopspostion" style="color:red;font-size:12px;">&nbsp;&nbsp;' . $juli[$row['id']] . '</font></p>
          <p class="lcolor" style="font-size:13px;">微信:' . cutstr($row['nickname'], 5, true) . '&nbsp;&nbsp;' . $row['resideprovincecity'] . '</p>
		  <input type="hidden" class="nickname' . $row['id'] . '" value="' . $row['nickname'] . '"/>
		  <input type="hidden" class="toopenid' . $row['id'] . '" value="' . $row['from_user'] . '"/>
		  <input type="hidden" class="openid' . $row['id'] . '" value="' . $openid . '"/>
        </div>
        <i class="arr"></i>
         </span>    
    	<div class="likebox">
    		<div class="likeit  fleft "><span class="hitlike" date="' . $row['id'] . '" title="' . $row['openid'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $row['love'] . '</span></div>
    		<div class="likeit letterit fright"><a class="hitmail"   href="' . $this->createMobileUrl('hitmail', array('toname' => $row['nickname'], 'toopenid' => $row['from_user'])) . '" target="__blank" style="color:#fff">聊一聊</a></div></div>
      
      </li><li class="dottedLine"></li>';
		}
		if ($result_str == '' || empty($stores)) {
			echo json_encode(0);
		} else {
			echo json_encode($result_str);
		}
	}

	public function doMobileRegisterajax()
	{
		global $_W;
		$_GPC;
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		$cfg = $this->module['config'];
		$data = array();
		if (empty($_POST['openid'])) {
			$data['msg'] = '登录失效';
			$data['res'] = false;
		} else {
			$res = $this->getres2($_POST['openid']);
			if (empty($res)) {
				$data['msg'] = '未注册';
				$data['res'] = false;
			} else {
				$data['msg'] = '1';
				$data['res'] = true;
			}
		}
		die(json_encode($data));
	}

	public function doMobileRegister()
	{
		global $_W;
		$_GPC;
		$weid = $_W['weid'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$hnages = $settings['hnages'];
		if (!empty($hnages)) {
			$ages = explode(",", $hnages);
			$morenage = $ages[0];
		}
		$openid = $_W['openid'];
		if (empty($openid)) {
			die('请重新从微信进入！');
		}
		$res = $this->getres2($openid);
		$heightLs = array('140', '141', '142', '143', '144', '145', '146', '147', '148', '149', '150', '151', '152', '153', '154', '155', '156', '157', '158', '159', '160', '161', '162', '163', '164', '165', '166', '167', '168', '169', '170', '171', '172', '173', '174', '175', '176', '177', '178', '179', '180');
		$heightHs = array('160', '161', '162', '163', '164', '165', '166', '167', '168', '169', '170', '171', '172', '173', '174', '175', '176', '177', '178', '179', '180', '181', '182', '183', '184', '185', '186', '187', '188', '189', '190', '191', '192', '193', '194', '195', '196', '197', '198', '199', '200', '201', '202', '203', '204', '205', '206', '207', '208', '209', '210', '211', '212', '213', '214', '215', '216', '217', '218', '219', '220');
		$day = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31');
		$myages = array('18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50');
		$myheights = array('140', '141', '142', '143', '144', '145', '146', '147', '148', '149', '150', '151', '152', '153', '154', '155', '156', '157', '158', '159', '160', '161', '162', '163', '164', '165', '166', '167', '168', '169', '170', '171', '172', '173', '174', '175', '176', '177', '178', '179', '180', '181', '182', '183', '184', '185', '186', '187', '188', '189', '190', '191', '192', '193', '194', '195', '196', '197', '198', '199', '200', '201', '202', '203', '204', '205', '206', '207', '208', '209', '210', '211', '212', '213', '214', '215', '216', '217', '218', '219', '220');
		if (!empty($_POST)) {
			$_POST['height'] = intval($_POST['height']);
			$_POST['uheightL'] = intval($_POST['uheightL']);
			$_POST['uheightH'] = intval($_POST['uheightH']);
			$_POST['uweight'] = intval($_POST['uweight']);
			$_POST['uage'] = intval($_POST['uage']);
			$_POST['gender'] = intval($_POST['gender']);
			if (empty($res['Descrip']) && !empty($_POST['Descrip'])) {
				$cfg = $this->module['config'];
				if (!empty($cfg['awardjifen']) && $cfg['awardjifen'] != '0') {
					$award = intval($cfg['awardjifen']);
					pdo_query('UPDATE ' . tablename('mc_members') . " SET credit1 = credit1 + '{$award}' WHERE uid = '{$_W['member']['uid']}' AND uniacid='{$weid}'");
				}
			}
			$RES = pdo_update("hnfans", $_POST, array('from_user' => $openid, 'weid' => $weid));
			if ($RES) {
				message("亲，恭喜您，保存成功", $this->createMobileUrl('alllist'), 'sucess');
			} else {
				message('你未做任何改变，保存失败！', '', 'referer');
			}
		}
		include $this->template('register');
	}

	public function doMobileuserinfo()
	{
		global $_W;
		$_GPC;
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		} else {
			$url = $this->createMobileUrl('Errorjoin');
			header("location:$url");
			exit;
		}
		$weid = $_W['weid'];
		$suijinum = rand();
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$openid = $_W['openid'];
		$res = $this->getres2($openid);
		if (!empty($res)) {
			include $this->template('userinfo');
		} else {
			die('您的资料被删除或是不存在，请从微信重新进入');
		}
	}

	private function tpl_form_field_image($name, $value = '', $default = '')
	{
		$url = $this->createMobileUrl('headupload', array('do' => 'upload'));
		$s = '';
		$sjnum = rand();
		if (!defined('INCLUDE_KINDEDITOR')) {
			$s = '';
		}
		if (strexists($name, '[')) {
			$id = str_replace(array('[', ']'), '_', $name);
		} else {
			$id = $name;
		}
		$s .= '
	<div style="" id="bianji">
	<input type="hidden" value="' . $value . '" name="' . $name . '" id="upload-image-url-' . $id . '" class="span3" autocomplete="off">
	<input type="hidden" value="" name="' . $name . '_old" id="upload-image-url-' . $id . '-old">
	
	</div>
	<div id="upload-image-preview-' . $id . '" style="margin-top:10px;">' . (!preg_match('/http:(.*)/', $value) ? '<img src="' . $GLOBALS['_W']['attachurl'] . $value . '" width="100" id="headimg"/>' : (empty($default) ? '<img src="' . $value . '" width="100" id="headimg"/>' : '')) . '</div>
	<script type="text/javascript">
	var editor = KindEditor.editor({
		allowFileManager : true,
		uploadJson : "' . $url . '",
		fileManagerJson : "./index.php?act=attachment&do=manager",
		afterUpload : function(url, data) {
			
		}
	});
	$("#upload-image-' . $id . '").click(function() {
		editor.loadPlugin("image", function() {
			editor.plugin.imageDialog({
				tabIndex : 1,
				imageUrl : $("#upload-image-url-' . $id . '").val(),
				clickFn : function(url) {
					editor.hideDialog();
					var filename = /images(.*)/.exec(url);
					$("#upload-image-url-' . $id . '-old").val($("#upload-image-url-' . $id . '").val());
					$("#upload-image-url-' . $id . '").val(filename[0]);
					$("#upload-image-preview-' . $id . '").html(\'<img src="\'+url+\'" width="100" />\');
				}
			});
		});
	});
	</script>';
		define('INCLUDE_KINDEDITOR', true);
		return $s;
	}

	public function doMobilemyphotos()
	{
		global $_W;
		$_GPC;
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		} else {
			$url = $this->createMobileUrl('Errorjoin');
			header("location:$url");
			exit;
		}
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$cfg = $this->module['config'];
		$appid = $cfg['appid'];
		$secret = $cfg['secret'];
		if (empty($openid)) {
			message('请重新从微信进入');
		}
		$photocfg = $this->module['config'];
		$photoss = $this->getphotos($openid);
		if (count($photoss) > 8) {
			$photos = array($photoss[0], $photoss[1], $photoss[2], $photoss[3], $photoss[4], $photoss[5], $photoss[6], $photoss[7]);
		} else {
			$photos = $photoss;
		}
		include $this->template('myphotos');
	}

	public function doMobiledeletephotos()
	{
		global $_W;
		$_GPC;
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		if (empty($openid) || empty($_GET['id'])) {
			message('请重新从微信进入');
		} else {
			$tablename = tablename("meepohongniangphotos");
			$sql = 'SELECT * FROM ' . $tablename . ' WHERE from_user=:from_user AND weid=:weid';
			$arr = array(":from_user" => $openid, ":weid" => $weid,);
			$res = pdo_fetch($sql, $arr);
			load()->func('file');
			file_delete($res['url']);
			pdo_delete('meepohongniangphotos', array('weid' => $weid, 'id' => $_GET['id']));
			message('删除成功', $this->createMobileUrl('myphotos'), 'sucess');
		}
	}

	public function doMobileUploadImage()
	{
		global $_W;
		$openid = $_W['openid'];
		$weid = $_W['weid'];
		$result = array();
		if (empty($_FILES['header_img_id']['name'])) {
			$result['message'] = '请选择要上传的文件！';
			$result['result'] = 0;
			exit(json_encode($result));
		}
		$back = $this->fileUpload2($_FILES['header_img_id'], $type = 'image');
		if ($back == '-1') {
			$result['message'] = '不支持此类文件！';
			$result['result'] = 0;
		} elseif ($back == '-2') {
			$result['message'] = '文件最大为2兆！';
			$result['result'] = 0;
		} elseif ($back == '-3') {
			$result['message'] = '网络超时，保存失败！';
			$result['result'] = 0;
		} else {
			$result['imgurl'] = $back['path'];
			$result['result'] = 1;
			load()->func('file');
			$headerimg = $this->getres2($openid);
			if (!strpos($headerimg['avatar'], 'qlogo')) {
				file_delete($headerimg['avatar']);
			}
			pdo_update('hnfans', array('avatar' => $back['path']), array('from_user' => $openid, 'weid' => $weid));
		}
		exit(json_encode($result));
	}

	public function doMobileUploadImage2()
	{
		global $_W;
		$_GPC;
		$openid = $_W['openid'];
		$weid = $_W['weid'];
		$photocfg = $this->module['config'];
		$result = array();
		if (empty($openid) || empty($_POST['id'])) {
			die('0');
		}
		load()->func('communication');
		load()->classs('weixin.account');
		$accObj = WeixinAccount::create($_W['account']['acid']);
		$access_token = $accObj->fetch_token();
		$token2 = $access_token;
		$url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $token2 . '&media_id=' . $_POST['id'];
		$pic_data = ihttp_request($url);
		$path = "images/meepoxiangqin/";
		load()->func('file');
		$picurl = $path . random(30) . ".jpg";
		file_write($picurl, $pic_data['content']);
		$data = array('from_user' => $openid, 'weid' => $weid, 'url' => $picurl, 'description' => '暂无描述', 'time' => time(),);
		if ($photocfg['isstatus'] == 0) {
			$data['status'] = 1;
		} else {
			$data['status'] = 0;
		}
		pdo_insert('meepohongniangphotos', $data);
		die($picurl);
	}

	public function fileUpload2($file, $type = 'image', $name = '')
	{
		if (empty($file)) {
			return '-1';
		}
		global $_W;
		if (empty($cfg['size'])) {
			$defsize = 2;
		}
		$deftype = array('jpg', 'png', 'jpeg');
		if (empty($_W['uploadsetting'])) {
			$_W['uploadsetting'] = array();
			$_W['uploadsetting'][$type]['folder'] = 'images';
			$_W['uploadsetting'][$type]['extentions'] = $deftype;
			$_W['uploadsetting'][$type]['limit'] = 1024 * $defsize;
		}
		$settings = $_W['uploadsetting'];
		if (!array_key_exists($type, $settings)) {
			return '-1';
		}
		$extention = pathinfo($file['name'], PATHINFO_EXTENSION);
		if (!in_array(strtolower($extention), $settings[$type]['extentions'])) {
			return '-1';
		}
		if (!empty($settings[$type]['limit']) && $settings[$type]['limit'] * 1024 < $file['size']) {
			return '-2';
		}
		$result = array();
		load()->func('file');
		if (empty($name) || $name == 'auto') {
			$result['path'] = "{$settings[$type]['folder']}/" . date('Y/m/');
			mkdirs(ATTACHMENT_ROOT . '/' . $result['path']);
			do {
				$filename = random(30) . ".{$extention}";
			} while (file_exists(ATTACHMENT_ROOT . '/' . $result['path'] . $filename));
			$result['path'] .= $filename;
		} else {
			$result['path'] = $name . '.' . $extention;
		}
		if (!file_move($file['tmp_name'], ATTACHMENT_ROOT . '/' . $result['path'])) {
			return '-3';
		}
		return $result;
	}

	public function doMobilehitmail()
	{
		global $_W;
		$_GPC;
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		} else {
			$url = $this->createMobileUrl('Errorjoin');
			header("location:$url");
			exit;
		}
		$weid = $_W['weid'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$openid = $_W['openid'];
		if (empty($openid)) {
			$openid = $_GET['openid'];
		}
		$to = $_GET['toname'];
		$toopenid = $_GET['toopenid'];
		if (empty($openid)) {
			message("登录身份无效，请重新从微信进入！");
		}
		if (empty($to)) {
			message("参数错误，请重新从微信进入！");
		} else {
			if ($openid == $toopenid) {
				message("自己不能和自己聊天哦！", $this->createMobileUrl('alllist'), 'error');
			}
			$sql = "SELECT * FROM " . tablename('hnblacklist') . " WHERE wantblack = :wantblack AND blackwho = :blackwho AND weid=:weid";
			$paras = array(':wantblack' => $openid, ':blackwho' => $toopenid, ':weid' => $weid);
			$result = pdo_fetch($sql, $paras);
			$sql2 = "SELECT * FROM " . tablename('hnblacklist') . " WHERE wantblack = :wantblack AND blackwho = :blackwho AND weid=:weid";
			$paras2 = array(':wantblack' => $toopenid, ':blackwho' => $openid, ':weid' => $weid);
			$uresult = pdo_fetch($sql2, $paras2);
			$res2 = $this->getres2($openid);
			if (preg_match('/http:(.*)/', $res2['avatar'])) {
				$avatar = $res2['avatar'];
			} elseif (preg_match('/images(.*)/', $res2['avatar'])) {
				$avatar = $_W['attachurl'] . $res2['avatar'];
			} else {
				$avatar = MEEPORES . "/static/friend/images/cdhn80.jpg";
			}
			$member['nickname'] = $res2['nickname'];
			$geter = $toopenid;
			$sender = $openid;
			$ten = pdo_fetchall("SELECT * FROM ( SELECT * FROM " . tablename('hnmessage') . " WHERE (sender='{$sender}' or sender='{$geter}') AND (geter='{$geter}' or geter='{$sender}') AND mloop = 1 ORDER BY stime DESC LIMIT 0,10) AS a ORDER BY stime ASC");
			include $this->template('chat2');
		}
	}

	public function doMobileblacklist()
	{
		global $_W;
		$_GPC;
		$weid = $_W['weid'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$openid = $_W['openid'];
		$to = $_GET['toname'];
		$toopenid = $_GET['toopenid'];
		if (empty($openid)) {
			message("登录身份无效，请重新从微信进入！");
		}
		$sql = "SELECT * FROM " . tablename('hnblacklist') . " WHERE wantblack = :wantblack  AND weid=:weid";
		$paras = array(':wantblack' => $openid, ':weid' => $weid);
		$result = pdo_fetchall($sql, $paras);
		if (!empty($result) && is_array($result)) {
			foreach ($result as $row) {
				$sql2 = "SELECT * FROM " . tablename('hnfans') . " WHERE from_user=:from_user   AND weid=:weid";
				$itsblack[] = pdo_fetch($sql2, array(':from_user' => $row['blackwho'], ':weid' => $weid));
			}
		}
		unset($result);
		include $this->template('blacklist');
	}

	public function doMobiledropblack()
	{
		global $_W;
		$_GPC;
		$weid = $_W['weid'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$openid = $_W['openid'];
		$to = $_GET['toname'];
		$toopenid = $_GET['toopenid'];
		if (empty($openid)) {
			message("登录身份无效，请重新从微信进入！");
		}
		if (empty($to)) {
			message("参数错误，请重新从微信进入！");
		} else {
			if ($openid == $toopenid) {
				message("自己不能拉黑自己哦！", $this->createMobileUrl('alllist'), error);
			}
			$sql = "SELECT * FROM " . tablename('hnblacklist') . " WHERE wantblack = :wantblack AND blackwho = :blackwho AND weid=:weid";
			$paras = array(':wantblack' => $openid, ':blackwho' => $toopenid, ':weid' => $weid);
			$result = pdo_fetch($sql, $paras);
			if (empty($result)) {
				$data = array('wantblack' => $openid, 'blackwho' => $toopenid, 'time' => time(), 'weid' => $weid);
				pdo_insert('hnblacklist', $data);
				message('你已经成功将' . $to . '拉入黑名单！', $this->createMobileUrl('mynews'), 'sucess');
			} else {
				pdo_delete('hnblacklist', array('wantblack' => $openid, 'blackwho' => $toopenid, 'weid' => $weid));
				message('你已经取消将' . $to . '拉入黑名单！', $this->createMobileUrl('mynews'), 'sucess');
			}
		}
	}

	public function doMobilegetfatherback10()
	{
		global $_W, $_GPC;
		$weid = $_W['uniacid'];
		$back = array();
		$sender = $_W['fans']['from_user'];
		if (empty($_GPC['sender']) || empty($_GPC['geter']) || empty($_GPC['page'])) {
			die('');
		} else {
			$geter = $_GPC['geter'];
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hnmessage') . " WHERE (sender='{$sender}' or sender='{$geter}') AND (geter='{$geter}' or geter='{$sender}') AND weid='{$weid}' AND mloop = 1");
			if ($_GPC['page'] == 1) {
				$back = pdo_fetchall(" SELECT * FROM " . tablename('hnmessage') . " WHERE (sender='{$sender}' or sender='{$geter}') AND (geter='{$geter}' or geter='{$sender}') AND weid='{$weid}' AND mloop = 1 ORDER BY stime DESC LIMIT 0,9");
			} elseif ($_GPC['page'] > 1) {
				$pindex = max(1, intval($_GPC['page']));
				$psize = 9;
				$back = pdo_fetchall("SELECT * FROM " . tablename('hnmessage') . " WHERE (sender='{$sender}' or sender='{$geter}') AND (geter='{$geter}' or geter='{$sender}')  AND weid='{$weid}' AND mloop = 1  ORDER BY stime DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
				if (empty($back)) {
					die('');
				}
			}
			if (is_array($back)) {
				foreach ($back as &$row) {
					$row['stime'] = date('Y-m-d H:i:s', $row['stime']);
				}
			}
			die(json_encode($back));
		}
	}

	public function doMobilegetmes()
	{
		global $_W, $_GPC;
		$weid = $_W['uniacid'];
		if (empty($_POST['sender'])) {
			exit();
		}
		$sender = $_POST['sender'];
		$geter = $_POST['geter'];
		$sql = "SELECT * FROM " . tablename('hnmessage') . " WHERE sender=:sender AND geter=:geter AND mloop=:mloop AND weid=:weid ORDER BY stime ASC";
		$all = pdo_fetchall($sql, array(':sender' => $geter, ':geter' => $sender, ':mloop' => 0, ':weid' => $weid));
		$mNums = count($all);
		if ($mNums < 1) {
			echo "nomessage";
			exit();
		} else {
			echo json_encode($all);
		}
		unset($all);
		if ($mNums > 0) {
			pdo_update('hnmessage', array('mloop' => 1), array('sender' => $geter, 'geter' => $sender, 'mloop' => 0, 'weid' => $weid));
		}
	}

	public function doMobilechatfatherajax()
	{
		global $_W, $_GPC;
		$weid = $_W['uniacid'];
		$back = array();
		$data = array();
		if (!empty($_GPC['content'])) {
			if (empty($_GPC['sender']) || empty($_GPC['geter'])) {
				$back['succ'] = 0;
			} else {
				$senderuid = pdo_fetch("SELECT * FROM " . tablename('hnfans') . " WHERE from_user = '{$_GPC['sender']}' AND weid = '{$weid}'");
				$senderavatar = $senderuid['avatar'];
				$sendernickname = $senderuid['nickname'];
				$data['sender'] = $_GPC['sender'];
				$data['geter'] = $_GPC['geter'];
				$data['content'] = $_GPC['content'];
				$data['msgtype'] = $_GPC['msgtype'];
				$data['stime'] = time();
				$data['weid'] = $weid;
				if (preg_match('/http:(.*)/', $senderavatar)) {
					$data['senderavatar'] = $senderavatar;
				} elseif (preg_match('/images(.*)/', $senderavatar)) {
					$data['senderavatar'] = $_W['attachurl'] . $senderavatar;
				} else {
					$data['senderavatar'] = MEEPORES . "/static/friend/images/cdhn80.jpg";
				}
				$data['sendernickname'] = $sendernickname;
				pdo_insert('hnmessage', $data);
				pdo_update('hnfans', array('mails' => $senderuid['mails'] + 1), array("from_user" => $_GPC['geter'], "weid" => $weid));
				$btime = date('Y-m-d' . '00:00:00', time());
				$btimestr = strtotime($btime);
				$sql = "SELECT * FROM " . tablename('hnmessage') . " WHERE sender=:sender AND geter=:geter AND mloop=:mloop AND weid=:weid AND stime>:stime";
				$checkit = pdo_fetchall($sql, array(':sender' => $_GPC['geter'], ':geter' => $_GPC['sender'], ':mloop' => 1, ':weid' => $weid, ':stime' => $btimestr));
				$max = count($checkit);
				$cfgs = $this->module['config'];
				$cfgnum = intval($cfgs['maxnum']);
				if ($cfgnum > 0) {
					if ($max < $cfgnum) {
						$this->sendmessage($_GPC['content'], $_GPC['geter']);
					}
				} else {
					$this->sendmessage($_GPC['content'], $_GPC['geter']);
				}
				$back['succ'] = 1;
			}
		} else {
			$back['succ'] = 0;
		}
		die(json_encode($back));
	}

	public function doMobileUploadImage3()
	{
		global $_W, $_GPC;
		$openid = $_W['openid'];
		$weid = $_W['weid'];
		$result = array();
		if (empty($openid) || empty($_POST['id']) || empty($_GPC['geter'])) {
			die('0');
		}
		load()->func('communication');
		load()->classs('weixin.account');
		$accObj = WeixinAccount::create($_W['account']['acid']);
		$access_token = $accObj->fetch_token();
		$token2 = $access_token;
		$url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $token2 . '&media_id=' . $_POST['id'];
		$pic_data = ihttp_request($url);
		$path = "images/meepoxiangqin/";
		$path2 = "images/meepoxiangqinthumb/";
		load()->func('file');
		$picurl = $path . random(30) . ".jpg";
		$thumbimg = $path2 . random(30) . ".jpg";
		file_write($picurl, $pic_data['content']);
		$thumb = file_image_thumb(IA_ROOT . '/attachment/' . $picurl, IA_ROOT . '/attachment/' . $thumbimg, $width = 70);
		if (!is_array($thumb)) {
			$thumburl = $thumb;
		} else {
			$thumburl = $picurl;
		}
		$sender = $_W['fans']['from_user'];
		$senderuid = pdo_fetch("SELECT * FROM " . tablename('hnfans') . " WHERE from_user = '{$sender}' AND weid = '{$weid}'");
		$senderavatar = $senderuid['avatar'];
		$sendernickname = $senderuid['nickname'];
		$data = array('sender' => $sender, 'geter' => $_GPC['geter'], 'content' => $picurl, 'msgtype' => 'images', 'thumburl' => $thumburl, 'stime' => time(), 'weid' => $weid, 'sendernickname' => $sendernickname,);
		if (preg_match('/http:(.*)/', $senderavatar)) {
			$data['senderavatar'] = $senderavatar;
		} elseif (preg_match('/images(.*)/', $senderavatar)) {
			$data['senderavatar'] = $_W['attachurl'] . $senderavatar;
		} else {
			$data['senderavatar'] = MEEPORES . "/static/friend/images/cdhn80.jpg";
		}
		$res = pdo_insert('hnmessage', $data);
		pdo_update('hnfans', array('mails' => $senderuid['mails'] + 1), array("from_user" => $_GPC['geter'], "weid" => $weid));
		if ($res) {
			$btime = date('Y-m-d' . '00:00:00', time());
			$btimestr = strtotime($btime);
			$sql = "SELECT * FROM " . tablename('hnmessage') . " WHERE sender=:sender AND geter=:geter AND mloop=:mloop AND weid=:weid AND stime>:stime";
			$checkit = pdo_fetchall($sql, array(':sender' => $_GPC['geter'], ':geter' => $sender, ':mloop' => 1, ':weid' => $weid, ':stime' => $btimestr));
			$cfgs = $this->module['config'];
			$cfgnum = intval($cfgs['maxnum']);
			if ($cfgnum) {
				if (count($checkit) < $cfgnum) {
					$this->sendmessage($_GPC['content'], $_GPC['geter']);
				}
			} else {
				$this->sendmessage($_GPC['content'], $_GPC['geter']);
			}
			$back['picurl'] = $picurl;
			$back['thumburl'] = $thumburl;
			die(json_encode($back));
		} else {
			die('0');
		}
	}

	public function doMobilemynews()
	{
		global $_W, $_GPC;
		$weid = $_W['uniacid'];
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		} else {
			$url = $this->createMobileUrl('Errorjoin');
			header("location:$url");
			exit;
		}
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$all = pdo_fetchall('SELECT sender FROM ' . tablename('hnmessage') . ' WHERE geter=:geter AND weid=:weid', array(':geter' => $_W['fans']['from_user'], ':weid' => $weid));
		if (!empty($all) && is_array($all)) {
			$names = arrayChange($all);
			$name = array_unique($names);
			foreach ($name as $row) {
				$itsrow[$row] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('hnmessage') . ' WHERE geter=:geter AND sender=:sender AND mloop=:mloop   AND weid=:weid', array(':geter' => $_W['fans']['from_user'], ':sender' => $row, ':mloop' => 0, ':weid' => $weid));
				$sql = "SELECT senderavatar,sendernickname FROM " . tablename('hnmessage') . " WHERE sender=:sender   AND weid=:weid";
				$itsuserinfo[$row] = pdo_fetch($sql, array(':sender' => $row, ':weid' => $weid));
			}
		}
		unset($all);
		unset($name);
		include $this->template('list');
	}

	public function doMobileothers()
	{
		global $_W, $_GPC;
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		} else {
			$url = $this->createMobileUrl('Errorjoin');
			header("location:$url");
			exit;
		}
		$weid = $_W['weid'];
		$suijinum = rand();
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$openid2 = $_W['openid'];
		$res2 = $this->getres2($openid2);
		$openid = $_GET['openid'];
		$exchangetitle = $this->getexchangetitle($openid2, $openid);
		if (!empty($exchangetitle)) {
			foreach ($exchangetitle as $exres) {
				$ex[$exres['twhichone']] = $exres['twhichone'];
			}
		}
		$photoss = $this->getphotos($openid);
		if (count($photoss) > 8) {
			$photos = array($photoss[0], $photoss[1], $photoss[2], $photoss[3], $photoss[4], $photoss[5], $photoss[6], $photoss[7]);
		} else {
			$photos = $photoss;
		}
		if (empty($openid)) {
			message("参数错误，请重新从微信进入！");
		} else {
			$res = $this->getres2($openid);
			$settings['share_title'] = $res['nickname'] . '个人中心';
			if ($res['yingcang'] == '2') {
				message("对不起，对方已将自己的信息隐藏！", $this->createMobileUrl('alllist'), 'error');
			}
			if (!empty($res2['lat']) && !empty($res2['lng']) && !empty($res['lat']) && !empty($res['lng'])) {
				$juli = '相距: ' . $this->getDistance($res2['lat'], $res2['lng'], $res['lat'], $res['lng']) . "km";
			} else {
				$juli = '';
			}
		}
		include $this->template('others');
	}

	public function doMobilemeepoweekwomenbangdan()
	{
		global $_W, $_GPC;
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		} else {
			$url = $this->createMobileUrl('Errorjoin');
			header("location:$url");
			exit;
		}
		$weid = $_W['weid'];
		$cfg = $this->module['config'];
		$appid = $cfg['appid'];
		$secret = $cfg['secret'];
		$suijinum = rand();
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$openid2 = $_W['openid'];
		$julires = $this->getres2($openid);
		$tablename = tablename("hnfans");
		$psize = 20;
		$pindex = 1;
		$isshow = 1;
		$gender = 2;
		$beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
		$gender = 2;
		$list = pdo_fetchall("SELECT *  FROM " . $tablename . " WHERE   yingcang=1 AND weid='{$weid}' AND nickname!='' AND isshow='{$isshow}' AND gender='{$gender}' ORDER BY rand() LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		if (!empty($list) && is_array($list)) {
			foreach ($list as $rand) {
				$list2[] = pdo_fetch("SELECT *  FROM " . $tablename . " WHERE yingcang=1 AND weid='{$weid}' AND from_user='{$rand['from_user']}'");
			}
			if (!empty($list2)) {
				foreach ($list2 as $row) {
					if (!empty($row['lat']) && !empty($row['lng'])) {
						if (!empty($julires['lat']) && !empty($julires['lng'])) {
							$juli[$row['id']] = "相距: " . $this->getDistance($julires['lat'], $julires['lng'], $row['lat'], $row['lng']) . "km";
						} else {
							$juli[$row['id']] = "";
						}
					} else {
						$juli[$row['id']] = "";
					}
					$photoss = $this->getphotos($row['from_user']);
					if (count($photoss) > 3) {
						$photos[$row['id']] = array($photoss[0], $photoss[1], $photoss[2]);
					} else {
						$photos[$row['id']] = $photoss;
					}
				}
			}
		}
		include $this->template('bangdan');
	}

	public function doMobilebangdanajax()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		$suijinum = rand();
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$openid2 = $_W['openid'];
		$julires = $this->getres2($openid);
		$tablename = tablename("hnfans");
		$psize = 20;
		$pindex = 1;
		$isshow = 1;
		$beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$endToday = mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')) - 1;
		if ($_POST['time'] == "week" && $_POST['type'] == "men") {
			$gender = 1;
			$list = pdo_fetchall("SELECT *  FROM " . $tablename . " WHERE yingcang=1 AND weid='{$weid}' AND nickname!='' AND isshow='{$isshow}' AND gender='{$gender}' ORDER BY rand() LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			if (!empty($list) && is_array($list)) {
				foreach ($list as $rand) {
					$list2[] = pdo_fetch("SELECT *  FROM " . $tablename . " WHERE yingcang=1 AND weid='{$weid}' AND from_user='{$rand['from_user']}' AND gender='{$gender}'");
				}
			}
		} elseif ($_POST['time'] == "week" && $_POST['type'] == "women") {
			$gender = 2;
			$list = pdo_fetchall("SELECT *  FROM " . $tablename . " WHERE yingcang=1 AND weid='{$weid}' AND nickname!='' AND isshow='{$isshow}' AND gender='{$gender}' ORDER BY rand() LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			if (!empty($list) && is_array($list)) {
				foreach ($list as $rand) {
					$list2[] = pdo_fetch("SELECT *  FROM " . $tablename . " WHERE yingcang=1 AND weid='{$weid}' AND from_user='{$rand['from_user']}'");
				}
			}
		} elseif ($_POST['time'] == "all" && $_POST['type'] == "women") {
			$gender = 2;
			$list2 = pdo_fetchall("SELECT *  FROM " . $tablename . " WHERE yingcang=1 AND weid='{$weid}' AND nickname!='' AND isshow='{$isshow}' AND gender='{$gender}' ORDER BY love DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		} else {
			$gender = 1;
			$list2 = pdo_fetchall("SELECT *  FROM " . $tablename . " WHERE yingcang=1 AND weid='{$weid}' AND nickname!='' AND isshow='{$isshow}' AND gender='{$gender}' ORDER BY love DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		}
		if (!empty($list2) && is_array($list2)) {
			foreach ($list2 as $row) {
				if (!empty($row['lat']) && !empty($row['lng'])) {
					if (!empty($julires['lat']) && !empty($julires['lng'])) {
						$juli[$row['id']] = "相距: " . $this->getDistance($julires['lat'], $julires['lng'], $row['lat'], $row['lng']) . "km";
					} else {
						$juli[$row['id']] = "";
					}
				} else {
					$juli[$row['id']] = "";
				}
				$photoss = $this->getphotos($row['from_user']);
				if (count($photoss) > 3) {
					$photos[$row['id']] = array($photoss[0], $photoss[1], $photoss[2]);
				} else {
					$photos[$row['id']] = $photoss;
				}
			}
			$result_str = '';
			foreach ($list2 as $row) {
				$result_str .= '<div class="search_list"><article>
										<div class="list_info"><p>';
				if (preg_match('/http:(.*)/', $row['avatar'])) {
					$result_str .= '<img src="' . $row['avatar'] . '" alt="用户头像" height="30" width="30">';
				} elseif (preg_match('/images(.*)/', $row['avatar'])) {
					$result_str .= '<img src="' . $_W['attachurl'] . $row['avatar'] . '" alt="用户头像" height="30" width="30">';
				} else {
					$result_str .= '<img src="./addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg" alt="用户头像" height="30" width="30">';
				}
				$result_str .= '</p><dl><dt><a href="' . $this->createMobileUrl('others', array('openid' => $row['from_user'])) . '">' . $row['realname'] . '</a><span>' . $row['age'] . ' | ' . $row['resideprovincecity'] . '</span>
										</dt>
										</dl>
										<input type="hidden" class="toopenid' . $row['id'] . '" value="' . $row['from_user'] . '"/>
										<a class="search_hi" id="hitlike" date="' . $row['id'] . '" title="' . $row['openid'] . '">赞&nbsp;&nbsp;' . $row['love'] . '</a>
										</div>
										<ul>';
				foreach ($photos[$row['id']] as $ph) {
					$result_str .= '<li><img src="' . $_W['attachurl'] . $ph['url'] . '" height="120" width="90" date="' . $row['from_user'] . '" id="btn"></li>';
				}
				$result_str .= '</ul>
										</article></div>';
			}
			if ($result_str == '') {
				echo json_encode(0);
			} else {
				echo json_encode($result_str);
			}
		} else {
			echo json_encode(0);
		}
	}

	public function doMobilegerenshow()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		} else {
			$url = $this->createMobileUrl('Errorjoin');
			header("location:$url");
			exit;
		}
		$suijinum = rand();
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$openid2 = $_W['openid'];
		$cfg = $this->module['config'];
		$openid = $_GET['openid'];
		$res2 = $this->getres2($openid);
		if (empty($res2['telephone'])) {
			message("该会员还未完善个人资料！", referer());
		}
		$photoss = $this->getphotos($openid);
		if (count($photoss) > 8) {
			$photos = array($photoss[0], $photoss[1], $photoss[2], $photoss[3], $photoss[4], $photoss[5], $photoss[6], $photoss[7], $photoss[8]);
		} else {
			$photos = $photoss;
		}
		if (empty($openid)) {
			message("参数错误，请重新从微信进入！");
		}
		include $this->template('gerenshow');
	}

	public function doMobilemymails()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$openid = $_W['openid'];
		$res = $this->getallmails($openid);
		$result = array();
		if (!empty($res)) {
			foreach ($res as $v) {
				$result[$v['id']] = $this->getres2($v['openid']);
			}
		}
		include $this->template('mymail');
	}

	public function doMobilehomecenter()
	{
		global $_W, $_GPC;
		$weid = $_W['uniacid'];
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		} else {
			$url = $this->createMobileUrl('Errorjoin');
			header("location:$url");
			exit;
		}
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$openid = $_W['openid'];
		load()->model('mc');
		$cfg = $this->module['config'];
		if (!empty($_W['member']['uid'])) {
			$member = mc_fetch($_W['member']['uid']);
		} else {
			die('请重新从微信进入！');
		}
		$res = $this->getres2($openid);
		include $this->template('gerenhome');
	}

	public function doMobileshow()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		if (!empty($openid) && $_POST['choose']) {
			$yingcang = intval($_POST['choose']);
			$res = pdo_update('hnfans', array('yingcang' => $yingcang), array('weid' => $weid, 'from_user' => $openid));
			if ($res) {
				die('1');
			} else {
				die('0');
			}
		} else {
			die('0');
		}
	}

	public function doMobilelikeajax()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		if (empty($openid) || empty($_POST['uid']) || empty($_POST['toopenid'])) {
			die('error');
		} else {
			$res = $this->getres2($openid);
			if (empty($res['constellation'])) {
				die('nfull');
			} else {
				if ($openid != $_POST['toopenid']) {
					$like_id = $this->getlikeon2($_POST['uid'], $openid, $_POST['toopenid']);
					$this->getlikecount($_POST['uid'], $openid, $_POST['toopenid']);
					if (!empty($like_id)) {
						echo "已喜欢";
						exit;
					} else {
						$res2 = $this->getres2($_POST['toopenid']);
						pdo_update('hnfans', array('love' => $res2['love'] + 1), array("from_user" => $_POST['toopenid'], "weid" => $weid));
						$data = array('uid' => $_POST['uid'], 'openid' => $openid, 'toopenid' => $_POST['toopenid'], 'status' => 1, 'createtime' => TIMESTAMP, 'weid' => $weid);
						pdo_insert('meepo_hongnianglikes', $data);
						$num = pdo_fetch("SELECT * FROM " . tablename("hnfans") . " WHERE from_user=:from_user AND weid=:weid", array(":from_user" => $_POST['toopenid'], ':weid' => $weid));
						echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $num['love'];
						exit;
					}
				} else {
					echo 'no way';
					exit;
				}
			}
		}
	}

	public function doMobilelikeajax2()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		if (empty($openid) || empty($_POST['uid']) || empty($_POST['toopenid'])) {
			exit;
		} else {
			$res = $this->getres2($openid);
			if (empty($res)) {
				exit;
			} else {
				if ($openid != $_POST['toopenid']) {
					$like_id = $this->getlikeon2($_POST['uid'], $openid, $_POST['toopenid']);
					$this->getlikecount($_POST['uid'], $openid, $_POST['toopenid']);
					if (!empty($like_id)) {
						echo "已赞";
						exit;
					} else {
						$res2 = $this->getres2($_POST['toopenid']);
						pdo_update('hnfans', array('love' => $res2['love'] + 1), array("from_user" => $_POST['toopenid'], "weid" => $weid));
						$data = array('uid' => $_POST['uid'], 'openid' => $openid, 'toopenid' => $_POST['toopenid'], 'status' => 1, 'createtime' => TIMESTAMP, 'weid' => $weid);
						pdo_insert('meepo_hongnianglikes', $data);
						$num = pdo_fetch("SELECT * FROM " . tablename("hnfans") . " WHERE from_user=:from_user AND weid=:weid", array(":from_user" => $_POST['toopenid'], ':weid' => $weid));
						echo '赞&nbsp;&nbsp;' . $num['love'];
						exit;
					}
				} else {
					echo 'no way';
					exit;
				}
			}
		}
	}

	private function getlikeon2($uid, $openid, $toopenid)
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$tablename = tablename("meepo_hongnianglikes");
		$sql = 'SELECT id FROM ' . $tablename . ' WHERE uid=:uid AND openid=:openid AND toopenid=:toopenid AND weid=:weid';
		$arr = array(":uid" => $uid, ":openid" => $openid, ":toopenid" => $toopenid, ":weid" => $weid);
		$res = pdo_fetchcolumn($sql, $arr);
		return $res;
	}

	public function doMobilemylike()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$from = $_W['openid'];
		$res = $this->getallmylike($from);
		if (!empty($res)) {
			foreach ($res as $row) {
				$result[] = $this->getres2($row['toopenid']);
			}
		}
		include $this->template('mylike');
	}

	public function doMobilelikeme()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$from = $_W['openid'];
		$res = $this->getalllikeme($from);
		if (!empty($res)) {
			foreach ($res as $row) {
				$result[] = $this->getres2($row['openid']);
			}
		}
		include $this->template('likeme');
	}

	public function doMobileErrorjoin()
	{
		global $_W, $_GPC;
		include $this->template('error');
	}

	private function getexchange($openid, $toopenid, $which)
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$tablename = tablename("hongniangexchangelog");
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE openid=:openid AND weid=:weid AND twhichone=:twhichone AND toopenid=:toopenid';
		$arr = array(":openid" => $openid, ":weid" => $_W['weid'], ":twhichone" => $which, ":toopenid" => $toopenid,);
		$res = pdo_fetch($sql, $arr);
		return $res;
	}

	private function getexchangetitle($openid, $toopenid)
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$tablename = tablename("hongniangexchangelog");
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE openid=:openid AND weid=:weid AND toopenid=:toopenid';
		$arr = array(":openid" => $openid, ":weid" => $_W['weid'], ":toopenid" => $toopenid);
		$res = pdo_fetchall($sql, $arr);
		return $res;
	}

	private function getres2($openid)
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$tablename = tablename("hnfans");
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE from_user=:from_user AND weid=:weid';
		$arr = array(":from_user" => $openid, ":weid" => $_W['weid']);
		$res = pdo_fetch($sql, $arr);
		return $res;
	}

	public function doMobilegetsomephotosajax()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$status = 1;
		$openid = $_POST['toopenid'];
		if (!empty($openid)) {
			$tablename = tablename("meepohongniangphotos");
			$sql = 'SELECT url FROM ' . $tablename . ' WHERE from_user=:from_user AND weid=:weid  AND status=:status ORDER BY time DESC';
			$arr = array(":from_user" => $openid, ":weid" => $_W['weid'], ":status" => $status);
			$res = pdo_fetchall($sql, $arr);
			if (!empty($res)) {
				foreach ($res as $row) {
					$newres[] = $_W['attachurl'] . $row['url'];
				}
				echo json_encode($newres);
			} else {
				echo '0';
			}
		} else {
			echo '0';
		}
	}

	private function getphotos($openid)
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$status = 1;
		$tablename = tablename("meepohongniangphotos");
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE from_user=:from_user AND weid=:weid  AND status=:status ORDER BY time DESC';
		$arr = array(":from_user" => $openid, ":weid" => $_W['weid'], ":status" => $status);
		$res = pdo_fetchall($sql, $arr);
		return $res;
	}

	private function getarea($openid)
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$tablename = tablename("meepo_hongniangarea");
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE openid=:openid AND weid=:weid';
		$arr = array(":openid" => $openid, ":weid" => $_W['weid']);
		$res = pdo_fetch($sql, $arr);
		return $res;
	}

	private function getallmails($openid)
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$tablename = tablename("meepo_hongniangmails");
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE toopenid=:toopenid AND weid=:weid ORDER BY time DESC';
		$arr = array(":toopenid" => $openid, ":weid" => $weid);
		$res = pdo_fetchall($sql, $arr);
		return $res;
	}

	private function getlikeon($uid, $openid, $toopenid)
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$tablename = tablename("meepo_hongnianglikes");
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE uid=:uid AND openid=:openid AND toopenid=:toopenid AND weid=:weid';
		$arr = array(":uid" => $uid, ":openid" => $openid, ":toopenid" => $toopenid, ":weid" => $weid);
		$res = pdo_fetch($sql, $arr);
		return $res;
	}

	private function getlikecount($uid, $openid, $toopenid)
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$tablename = tablename("meepo_hongnianglikes");
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE uid=:uid AND openid=:openid AND toopenid=:toopenid AND weid=:weid';
		$arr = array(":uid" => $uid, ":openid" => $openid, ":toopenid" => $toopenid, ":weid" => $weid);
		$res = pdo_fetchall($sql, $arr);
		if (count($res) > 1) {
			pdo_delete("meepo_hongnianglikes", array("id" => $res[0]['id'], "weid" => $weid));
		}
	}

	private function getallmylike($openid)
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$tablename = tablename("meepo_hongnianglikes");
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE openid=:openid AND weid=:weid  ORDER BY createtime DESC';
		$arr = array(":openid" => $openid, ":weid" => $weid);
		$res = pdo_fetchall($sql, $arr);
		return $res;
	}

	private function getalllikeme($toopenid)
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$tablename = tablename("meepo_hongnianglikes");
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE toopenid=:toopenid AND weid=:weid ORDER BY createtime DESC';
		$arr = array(":toopenid" => $toopenid, ":weid" => $weid);
		$res = pdo_fetchall($sql, $arr);
		return $res;
	}

	private function getonoff($weid)
	{
		$tablename = tablename('meepo_hongniangonoff');
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE weid=:weid';
		$arr = array(':weid' => $weid);
		$res = pdo_fetch($sql, $arr);
		return $res;
	}

	public function doWebList()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		checklogin();
		if (checksubmit('verify') && !empty($_GPC['select'])) {
			pdo_update('hnfans', array('isshow' => 1), " id  IN  ('" . implode("','", $_GPC['select']) . "')");
			message('审核成功！', $this->createWebUrl('list', array('page' => $_GPC['page'])), 'sucess');
		}
		if (checksubmit('delete') && !empty($_GPC['select'])) {
			pdo_delete('hnfans', " id  IN  ('" . implode("','", $_GPC['select']) . "')");
			message('删除成功！', $this->createWebUrl('list', array('page' => $_GPC['page'])), 'sucess');
		}
		load()->func('tpl');
		$isshow = isset($_GPC['isshow']) ? intval($_GPC['isshow']) : 0;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = '';
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND nickname LIKE '%{$_GPC['keyword']}%'";
		}
		if (!empty($_GPC['yingcang'])) {
			$yingcang = intval($_GPC['yingcang']);
			$condition .= " AND yingcang = {$yingcang}";
		}
		if (!empty($_GPC['telephone'])) {
			$telephone = trim($_GPC['telephone']);
			$condition .= " AND telephone LIKE '%{$_GPC['telephone']}%'";
		}
		if (!empty($_GPC['datelimit'])) {
			if ($_GPC['datelimit']['start'] != '1970-01-01' && $_GPC['datelimit']['end'] != '1970-01-01') {
				$starttime = strtotime($_GPC['datelimit']['start']);
				$endtime = strtotime($_GPC['datelimit']['end']);
				$_GPC['starttime'] = $starttime;
				$_GPC['endtime'] = $endtime;
				$condition .= " AND time >= {$starttime} AND time <= {$endtime}";
			}
		}
		$list = pdo_fetchall("SELECT * FROM " . tablename('hnfans') . " WHERE nickname!='' and isshow={$isshow}  and weid={$weid} {$condition} ORDER BY CASE  WHEN telephone !='' THEN  null  ELSE 1  END,time DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
		if (!empty($list)) {
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hnfans') . " WHERE nickname!='' AND  isshow={$isshow} AND weid={$weid}");
			$pager = pagination($total, $pindex, $psize);
		}
		include $this->template('list');
	}

	public function doWebPhotolist()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		checklogin();
		if (checksubmit('verify') && !empty($_GPC['select'])) {
			pdo_update('meepohongniangphotos', array('status' => 1), " id  IN  ('" . implode("','", $_GPC['select']) . "')");
			message('审核成功！', $this->createWebUrl('Photolist', array('page' => $_GPC['page'])), 'sucess');
		}
		if (checksubmit('delete') && !empty($_GPC['select'])) {
			pdo_delete('meepohongniangphotos', " id  IN  ('" . implode("','", $_GPC['select']) . "')");
			if (is_array($_GPC['select'])) {
				foreach ($_GPC['select'] as $row) {
					$imgurl = pdo_fetch("SELECT * FROM " . tablename('meepohongniangphotos') . "WHERE weid={$weid} AND id=:id", array(":id" => $row));
					load()->func('file');
					file_delete($imgurl['url']);
				}
			}
			message('删除成功！', $this->createWebUrl('Photolist', array('page' => $_GPC['page'])), 'sucess');
		}
		$status = isset($_GPC['status']) ? intval($_GPC['status']) : 0;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 5;
		$list = pdo_fetchall("SELECT * FROM " . tablename('meepohongniangphotos') . " WHERE  status={$status}  and weid={$weid} ORDER BY time DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
		if (!empty($list)) {
			if (!empty($list)) {
				foreach ($list as $arr) {
					$userinfo[$arr['from_user']] = pdo_fetch("SELECT * FROM " . tablename('hnfans') . "WHERE weid={$weid} AND from_user=:from_user", array(":from_user" => $arr['from_user']));
				}
			}
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('meepohongniangphotos') . " WHERE   status={$status} AND weid={$weid}");
			$pager = pagination($total, $pindex, $psize);
		}
		include $this->template('photolist');
	}

	public function doWebchatcontent()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		checklogin();
		if (checksubmit('delete') && !empty($_GPC['select'])) {
			pdo_delete('hnmessage', " id  IN  ('" . implode("','", $_GPC['select']) . "')");
			message('删除成功！', $this->createWebUrl('chatcontent', array('page' => $_GPC['page'])), 'sucess');
		}
		$status = isset($_GPC['status']) ? intval($_GPC['status']) : 0;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$list = pdo_fetchall("SELECT * FROM " . tablename('hnmessage') . " WHERE  weid={$weid} ORDER BY stime DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
		if (!empty($list)) {
			if (!empty($list)) {
				foreach ($list as $arr) {
					$userinfo[$arr['geter']] = pdo_fetch("SELECT * FROM " . tablename('hnfans') . "WHERE weid={$weid} AND from_user=:from_user", array(":from_user" => $arr['geter']));
				}
			}
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hnmessage') . " WHERE   weid={$weid}");
			$pager = pagination($total, $pindex, $psize);
		}
		include $this->template('chatmessage');
	}

	public function doWebonoff()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$tablename = tablename('meepo_hongniangonoff');
		$sql = 'SELECT * FROM ' . $tablename . ' WHERE weid=:weid';
		$arr = array(':weid' => $weid);
		$res = pdo_fetch($sql, $arr);
		if (!empty($_POST)) {
			$data = array("status" => intval($_POST['status']), "weid" => $weid);
			if (empty($res)) {
				pdo_insert("meepo_hongniangonoff", $data);
				if ($_POST['status'] == '1') {
					message("设置成功,您已经开启了审核", $this->createWebUrl('onoff'), 'success');
				} else {
					message('设置成功,您已经关闭了审核', $this->createWebUrl('onoff'), 'success');
				}
			} else {
				pdo_update('meepo_hongniangonoff', array('status' => $data['status']), array('id' => $res['id'], 'weid' => $weid));
				if ($_POST['status'] == '1') {
					message("设置成功,您已经开启了审核", $this->createWebUrl('onoff'), 'success');
				} else {
					message('设置成功,您已经关闭了审核', $this->createWebUrl('onoff'), 'success');
				}
			}
		}
		if (empty($res['id'])) {
			$res['status'] = 1;
		}
		include $this->template("onoff");
	}

	public function doWebpay_set()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid='{$weid}'");
		if (!empty($_POST)) {
			$data = array('pay_telephone' => $_GPC['pay_telephone'], 'pay_height' => $_GPC['pay_height'], 'pay_weight' => $_GPC['pay_weight'], 'pay_carhouse' => $_GPC['pay_carhouse'], 'pay_uheight' => $_GPC['pay_uheight'], 'pay_uage' => $_GPC['pay_uage'], 'pay_Descrip' => $_GPC['pay_Descrip'], 'pay_uitsOthers' => $_GPC['pay_uitsOthers'], 'pay_occupation' => $_GPC['pay_occupation'], 'pay_revenue' => $_GPC['pay_revenue'], 'pay_affectivestatus' => $_GPC['pay_affectivestatus'], 'pay_lxxingzuo' => $_GPC['pay_lxxingzuo'], 'share_jifen' => $_GPC['share_jifen'], 'pay_all' => $_GPC['pay_all'],);
			if (empty($settings)) {
				pdo_insert("meepo_hongniangset", $data);
				message('保存成功', referer());
			} else {
				pdo_update('meepo_hongniangset', $data, array('id' => intval($_GPC['id'])));
				message('更新成功', referer());
			}
		}
		include $this->template('set2');
	}

	public function doWebSet()
	{
		global $_GPC, $_W;
		$weid = $_W['weid'];
		//$check = $this->docheckurl();
		//if ($check) {
		//	die('No');
		//}
		$tablename = tablename('meepo_hongniangset');
		$id = $_GPC['id'];
		$sql = "SELECT * FROM " . $tablename . "WHERE weid='{$_W['weid']}'";
		$settings = pdo_fetch($sql);
		load()->func('tpl');
		if (!empty($_POST)) {
			$data = array('share_title' => $_GPC['share_title'], 'share_logo' => $_GPC['share_logo'], 'share_link' => $_GPC['share_link'], 'share_content' => $_GPC['share_content'], 'title' => $_GPC['title'], 'headtitle' => $_GPC['headtitle'], 'header_ads' => $_GPC['header_ads'], 'header_adsurl' => $_GPC['header_adsurl'], 'logo' => $_GPC['logo'], 'url' => $_GPC['url'], 'hnages' => trim($_GPC['hnages']), 'weid' => $_W['weid'],);
			if (empty($settings)) {
				pdo_insert("meepo_hongniangset", $data);
				message('保存成功', $this->createWebUrl('set'), 'sucess');
			} else {
				pdo_update('meepo_hongniangset', $data, array('id' => intval($_GPC['id'])));
				message('更新成功', $this->createWebUrl('set'), 'sucess');
			}
		}
		if (empty($settings)) {
			$settings['title'] = '相亲、交友';
			$settings['headtitle'] = '亲们，为了更好的找到属于自己的TA,一定要先去完善自己的资料';
			$settings['share_title'] = '';
			$settings['share_content'] = '';
			$settings['share_link'] = str_replace('./', '', $_W['siteroot'] . 'app/' . $this->createMobileUrl('Alllist'));
			$settings['url'] = "";
			$settings['hnages'] = "21,22,23,24,25,26";
			$settings['header_adsurl'] = "";
		}
		include $this->template("set");
	}

	public function getset()
	{
		global $_GPC, $_W;
		$tablename = tablename("meepo_hongniangset");
		$sql = "SELECT * FROM " . $tablename . "WHERE weid='{$_W['weid']}'";
		$settings = pdo_fetch($sql);
		return $settings;
	}

	public function doMobileshareajax()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$cfg = $this->module['config'];
		$share_num = isset($cfg['share_num']) ? $cfg['share_num'] : 3;
		$openid = $_W['openid'];
		if (!empty($settings['share_jifen']) && $settings['share_jifen'] != '0') {
			$todaytimestamp = strtotime(date('Y-m-d'));
			$all = pdo_fetchall("SELECT * FROM " . tablename('hongniangsharelogs') . " WHERE weid=" . $weid . "  AND openid='" . $openid . "' AND sharetime >= '" . $todaytimestamp . "'");
			$sharenum = count($all);
			if ($sharenum < $share_num) {
				$touid = $_W['member']['uid'];
				$share_jifen = intval($settings['share_jifen']);
				$result = pdo_query("UPDATE " . tablename('mc_members') . " SET credit1 = credit1 + '{$share_jifen}' WHERE uid = '{$touid}' AND uniacid='{$weid}' ");
				if ($result) {
					$data = array('openid' => $openid, 'weid' => $weid, 'openid' => $openid, 'jljifen' => $settings['share_jifen'], 'sharetime' => time());
					pdo_insert('hongniangsharelogs', $data);
					$all2 = pdo_fetchall("SELECT * FROM " . tablename('hongniangsharelogs') . " WHERE weid=" . $weid . "  AND openid='" . $openid . "' AND sharetime >= '" . $todaytimestamp . "'");
					$sharenum2 = count($all2);
					$othernum = $share_num - $sharenum2;
					echo $othernum;
				} else {
					echo 'no';
				}
			} else {
				echo 'over';
			}
		}
	}

	public function doMobilepay()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		checkAuth();
		$openid = $_W['openid'];
		load()->model('mc');
		if (!empty($_W['member']['uid'])) {
			$member = mc_fetch($_W['member']['uid']);
		} else {
			die('false');
		}
		$to = trim($_GPC['to']);
		if (!empty($to)) {
			$tsql = "SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE openid = :openid AND uniacid = :uniacid";
			$tparas = array(':uniacid' => $weid, ':openid' => $to);
			$touids = pdo_fetch($tsql, $tparas);
			$touid = $touids['uid'];
		} else {
			die('false');
		}
		if ($to == $openid) {
			die("no");
		}
		$payment = !empty($_GPC['payment']) ? intval($_GPC['payment']) : '0';
		$type = $_GPC['type'];
		$option = $_GPC['option'];
		$whichone = $_GPC['whichone'];
		if ($whichone == "carhouse") {
			$whichone = 'carstatus';
		}
		if ($whichone == "uheight") {
			$whichone = 'uheightL';
		}
		$userinfo = $this->getres2($to);
		if (empty($userinfo[$whichone]) || $userinfo[$whichone] == '0') {
			die("sorry");
		}
		if (empty($option)) {
			$option = '1';
		}
		if (empty($type)) {
			$type = '1';
		}
		$exchangeres2 = $this->getexchange($openid, $to, $whichone);
		if (!empty($exchangeres2)) {
			die("over");
		}
		if ($option == '1') {
			if ($type == '1') {
				$credit1 = $member['credit1'];
				if ($credit1 < $payment) {
					die("<div id='emptymoney'>积分余额不足，账户余额为" . $credit1 . "分!</div>");
				}
				if (pdo_query('UPDATE ' . tablename('mc_members') . " SET credit1 = credit1 - '{$payment}' WHERE uid = '{$_W['member']['uid']}' AND uniacid='{$weid}'")) {
					if (pdo_query("UPDATE " . tablename('mc_members') . " SET credit1 = credit1 + '{$payment}' WHERE uid = '{$touid}' AND uniacid='{$weid}' ")) {
						$exchangeres = $this->getexchange($openid, $to, $whichone);
						if (empty($exchangeres)) {
							$data = array('openid' => $openid, 'toopenid' => $to, 'twhichone' => $whichone, 'credit' => intval($payment), 'weid' => intval($weid), 'createtime' => time(),);
							pdo_insert('hongniangexchangelog', $data);
						}
						if ($whichone == "carstatus") {
							$userinfo[$whichone] = $userinfo['carstatus'] . '、' . $userinfo['housestatus'];
							die($userinfo[$whichone]);
						} elseif ($whichone == "height") {
							die($userinfo[$whichone] . "cm");
						} elseif ($whichone == "weight") {
							if ($userinfo[$whichone] == '401') {
								die('40kg以下');
							} elseif ($userinfo[$whichone] == '701') {
								die('70kg以上');
							} else {
								die($userinfo[$whichone] . "kg");
							}
						} elseif ($whichone == "uheight") {
							die($userinfo['uheightL'] . "cm~~" . $userinfo['uheightH']);
						} elseif ($whichone == "uage") {
							if (strlen($userinfo[$whichone]) == 2) {
								die($userinfo[$whichone] . "岁");
							} else {
								if ($userinfo[$whichone] == '1825') {
									die('18-25岁');
								} elseif ($userinfo[$whichone] == '2635') {
									die('26-35岁');
								} elseif ($userinfo[$whichone] == '3645') {
									die('36-45岁');
								} elseif ($userinfo[$whichone] == '4655') {
									die('46-55岁');
								} else {
									die('55岁以上');
								}
							}
						} else {
							die($userinfo[$whichone]);
						}
					} else {
						die('false');
					}
				} else {
					die('false');
				}
			} elseif ($type == '2') {
				$credit2 = intval($fans['credit2']);
				if ($credit2 < $payment) {
					die('余额不足，账户余额为' . $credit1 . '元!');
				}
				if (pdo_query('UPDATE ' . tablename('hnfans') . " SET credit2 = credit2 - '{$payment}' WHERE from_user = '{$openid}'")) {
					if (pdo_query("UPDATE " . tablename('hnfans') . " SET credit2 = credit2 + '{$payment}' WHERE from_user = '{$to}'")) {
						die('success');
					} else {
						die('false');
					}
				} else {
					die('false');
				}
			}
		} elseif ($option == '2') {
			if ($type == '1') {
				if (pdo_query("UPDATE " . tablename('hnfans') . " SET credit1 = credit1 + '{$payment}' WHERE from_user = '{$openid}'")) {
					die('success');
				} else {
					die('false');
				}
			} elseif ($type == '2') {
				if (pdo_query("UPDATE " . tablename('hnfans') . " SET credit2 = credit2 + '{$payment}' WHERE from_user = '{$openid}'")) {
					die('success');
				} else {
					die('false');
				}
			}
		}
	}

	private function checkAuth()
	{
		global $_W;
		checkauth();
	}

	public function doMobilePayjifen()
	{
		global $_W, $_GPC;
		if (empty($_W['member']['uid'])) {
			checkauth();
		}
		$weid = $_W['weid'];
		$openid = $_W['openid'];
		if (empty($openid)) {
			die('请重新从微信进入！');
		}
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$cfg = $this->module['config'];
		$username = $this->getres2($openid);
		include $this->template('recharge');
	}

	public function doMobilePayjifen2()
	{
		global $_W, $_GPC;
		$this->checkAuth();
		$openid = $_W['openid'];
		$weid = $_W['uniacid'];
		$cfg = $this->module['config'];
		$money = intval($_GPC['money']);
		$num = $money / $cfg['bilv'];
		if ($money >= $cfg['bilv'] && is_int($num)) {
			$params['tid'] = date('YmdHi') . random(10, 1);
			$params['user'] = $_W['fans']['from_user'];
			$params['fee'] = $money / $cfg['bilv'];
			$params['title'] = $_W['account']['name'];
			$params['ordersn'] = time();
			$params['virtual'] = true;
			include $this->template('pay');
		} else {
			message('你输入的积分数量有误，请核实！', url('entry', array('m' => 'meepo_weixiangqin', 'do' => 'Payjifen')), 'error');
		}
	}

	public function payResult($params)
	{
		global $_W, $_GPC;
		$weid = $_W['uniacid'];
		$uid = $_W['member']['uid'];
		if (empty($uid)) {
			$uid = intval($params['user']);
		}
		$openid = $_W['openid'];
		if (empty($openid)) {
			$tsql = "SELECT openid FROM " . tablename('mc_mapping_fans') . " WHERE uid = :uid AND uniacid = :uniacid";
			$tparas = array(':uniacid' => $weid, ':uid' => $uid);
			$topenid = pdo_fetch($tsql, $tparas);
			$openid = $topenid['openid'];
		}
		$cfg = $this->module['config'];
		if (empty($openid)) {
			die('身份失效，请重新进入！');
		} else {
			$res = $this->getres2($openid);
		}
		$fee = intval($params['fee']);
		$data = array('status' => $params['result'] == 'success' ? 1 : 0);
		$paytype = array('credit' => '1', 'wechat' => '2', 'alipay' => '2', 'delivery' => '3');
		$data['paytype'] = $paytype[$params['type']];
		if ($params['type'] == 'wechat') {
			$data['transid'] = $params['tag']['transaction_id'];
		}
		if ($params['type'] == 'delivery') {
			$data['status'] = 1;
		}
		$data['fee'] = $fee;
		$data['openid'] = $openid;
		$data['time'] = time();
		$data['avatar'] = $res['avatar'];
		$data['weid'] = $weid;
		$data['tid'] = $params['tid'];
		$addcredit = $fee * $cfg['bilv'];
		if ($params['from'] == 'return') {
			$endjl = pdo_insert('hnpayjifen', $data);
			load()->model('mc');
			$endresult = mc_credit_update($uid, 'credit1', $addcredit, $log = array());
			$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
			$credit = $setting['creditbehaviors']['currency'];
			if ($params['type'] == $credit) {
				message('支付成功！', $this->createMobileUrl('homecenter'), 'success');
			} else {
				message('支付成功！', '../../app/' . $this->createMobileUrl('homecenter'), 'success');
			}
		}
	}

	public function doMobilePayres()
	{
		global $_W, $_GPC;
		$this->checkAuth();
		$openid = $_W['openid'];
		$weid = $_W['weid'];
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		$cfg = $this->module['config'];
		$sql = "SELECT * FROM " . tablename('hnpayjifen') . " WHERE weid=:weid AND openid=:openid ORDER BY time DESC";
		$paras = array(":openid" => $openid, ":weid" => $weid);
		$res = pdo_fetchall($sql, $paras);
		include $this->template('payres');
	}

	public function getHomeTiles()
	{
		global $_W;
		$urls = array();
		$list = pdo_fetchall("SELECT title, reid FROM " . tablename('hnresearch') . " WHERE weid = '{$_W['uniacid']}'");
		if (!empty($list)) {
			foreach ($list as $row) {
				$urls[] = array('title' => $row['title'], 'url' => $_W['siteroot'] . "app/" . $this->createMobileUrl('research', array('id' => $row['reid'])));
			}
		}
		return $urls;
	}

	public function doWebQuery()
	{
		global $_W, $_GPC;
		$kwd = $_GPC['keyword'];
		$sql = 'SELECT * FROM ' . tablename('hnresearch') . ' WHERE `weid`=:weid AND `title` LIKE :title ORDER BY reid DESC LIMIT 0,8';
		$params = array();
		$params[':weid'] = $_W['uniacid'];
		$params[':title'] = "%{$kwd}%";
		$ds = pdo_fetchall($sql, $params);
		foreach ($ds as &$row) {
			$r = array();
			$r['title'] = $row['title'];
			$r['description'] = cutstr(strip_tags($row['description']), 50);
			$r['thumb'] = $row['thumb'];
			$r['reid'] = $row['reid'];
			$row['entry'] = $r;
		}
		include $this->template('hnquery');
	}

	public function doWebDetail()
	{
		global $_W, $_GPC;
		$rerid = intval($_GPC['id']);
		$sql = 'SELECT * FROM ' . tablename('hnresearch_rows') . " WHERE `rerid`=:rerid";
		$params = array();
		$params[':rerid'] = $rerid;
		$row = pdo_fetch($sql, $params);
		if (empty($row)) {
			message('访问非法.');
		}
		$sql = 'SELECT * FROM ' . tablename('hnresearch') . ' WHERE `weid`=:weid AND `reid`=:reid';
		$params = array();
		$params[':weid'] = $_W['uniacid'];
		$params[':reid'] = $row['reid'];
		$activity = pdo_fetch($sql, $params);
		if (empty($activity)) {
			message('非法访问.');
		}
		$sql = 'SELECT * FROM ' . tablename('hnresearch_fields') . ' WHERE `reid`=:reid ORDER BY `refid`';
		$params = array();
		$params[':reid'] = $row['reid'];
		$fields = pdo_fetchall($sql, $params);
		if (empty($fields)) {
			message('非法访问.');
		}
		$ds = $fids = array();
		foreach ($fields as $f) {
			$ds[$f['refid']]['fid'] = $f['title'];
			$ds[$f['refid']]['type'] = $f['type'];
			$ds[$f['refid']]['refid'] = $f['refid'];
			$fids[] = $f['refid'];
		}
		$fids = implode(',', $fids);
		$row['fields'] = array();
		$sql = 'SELECT * FROM ' . tablename('hnresearch_data') . " WHERE `reid`=:reid AND `rerid`='{$row['rerid']}' AND `refid` IN ({$fids})";
		$fdatas = pdo_fetchall($sql, $params);
		foreach ($fdatas as $fd) {
			$row['fields'][$fd['refid']] = $fd['data'];
		}
		foreach ($ds as $value) {
			if ($value['type'] == 'reside') {
				$row['fields'][$value['refid']] = '';
				foreach ($fdatas as $fdata) {
					if ($fdata['refid'] == $value['refid']) {
						$row['fields'][$value['refid']] .= $fdata['data'];
					}
				}
				break;
			}
		}
		include $this->template('hndetail');
	}

	public function doWebManage()
	{
		global $_W, $_GPC;
		$reid = intval($_GPC['id']);
		//$check = $this->docheckurl();
		//if ($check) {
		//	die('NO');
		//}
		$sql = 'SELECT * FROM ' . tablename('hnresearch') . ' WHERE `weid`=:weid AND `reid`=:reid';
		$params = array();
		$params[':weid'] = $_W['uniacid'];
		$params[':reid'] = $reid;
		$activity = pdo_fetch($sql, $params);
		if (empty($activity)) {
			message('非法访问.');
		}
		$sql = 'SELECT * FROM ' . tablename('hnresearch_fields') . ' WHERE `reid`=:reid ORDER BY `refid`';
		$params = array();
		$params[':reid'] = $reid;
		$fields = pdo_fetchall($sql, $params);
		if (empty($fields)) {
			message('非法访问.');
		}
		$ds = array();
		foreach ($fields as $f) {
			$ds[$f['refid']] = $f['title'];
		}
		$starttime = empty($_GPC['daterange']['start']) ? strtotime('-1 month') : strtotime($_GPC['daterange']['start']);
		$endtime = empty($_GPC['daterange']['end']) ? TIMESTAMP : strtotime($_GPC['daterange']['end']) + 86399;
		$select = array();
		if (!empty($_GPC['select'])) {
			foreach ($_GPC['select'] as $field) {
				if (isset($ds[$field])) {
					$select[] = $field;
				}
			}
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$sql = 'SELECT * FROM ' . tablename('hnresearch_rows') . " WHERE `reid`=:reid AND `createtime` > {$starttime} AND `createtime` < {$endtime} ORDER BY `createtime` DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		$params = array();
		$params[':reid'] = $reid;
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('hnresearch_rows') . " WHERE `reid`=:reid AND `createtime` > {$starttime} AND `createtime` < {$endtime}", $params);
		$pager = pagination($total, $pindex, $psize);
		$list = pdo_fetchall($sql, $params);
		$sql2 = 'SELECT title FROM ' . tablename('hnresearch') . " WHERE `reid`=:reid";
		$Thuodong = pdo_fetch($sql2, $params);
		if (is_array($list) && !empty($list)) {
			foreach ($list as &$row) {
				$user = pdo_fetch("SELECT nickname,avatar FROM" . tablename('hnfans') . " WHERE from_user=:from_user AND     weid=:weid", array(':from_user' => $row['openid'], ':weid' => $_W['uniacid']));
				$row['nickname'] = $user['nickname'];
				$row['avatar'] = $user['avatar'];
			}
		}
		if ($select) {
			$fids = implode(',', $select);
			foreach ($list as &$r) {
				$r['fields'] = array();
				$sql = 'SELECT data, refid FROM ' . tablename('hnresearch_data') . " WHERE `reid`=:reid AND `rerid`='{$r['rerid']}' AND `refid` IN ({$fids})";
				$fdatas = pdo_fetchall($sql, $params);
				foreach ($fdatas as $fd) {
					if (false == array_key_exists($fd['refid'], $r['fields'])) {
						$r['fields'][$fd['refid']] = $fd['data'];
					} else {
						$r['fields'][$fd['refid']] .= '--' . $fd['data'];
					}
				}
			}
		}
		foreach ($list as $key => &$value) {
			if (is_array($value['fields'])) {
				foreach ($value['fields'] as &$v) {
					$img = '<div align="center"><img src="';
					if (substr($v, 0, 6) == 'images') {
						$v = $img . $_W['attachurl'] . $v . '" style="width:50px;height:50px;"/></div>';
					}
				}
				unset($v);
			}
		}
		if (checksubmit('export', 1)) {
			$sql = 'SELECT title FROM ' . tablename('hnresearch_fields') . " AS f JOIN " . tablename('hnresearch_rows') . " AS r ON f.reid='{$params[':reid']}' GROUP BY title ORDER BY refid DESC";
			$tableheader = pdo_fetchall($sql, $params);
			$tablelength = count($tableheader);
			$tableheader[] = array('title' => '报名时间');
			$tableheader[] = array('title' => '粉丝微信标识');
			$tableheader[] = array('title' => '粉丝微信昵称');
			$tableheader[] = array('title' => '本次活动名称');
			$sql = 'SELECT * FROM ' . tablename('hnresearch_rows') . " WHERE `reid`=:reid AND `createtime` > {$starttime} AND `createtime` < {$endtime} ORDER BY `createtime` DESC";
			$params = array();
			$params[':reid'] = $reid;
			$list = pdo_fetchall($sql, $params);
			$sql2 = 'SELECT title FROM ' . tablename('hnresearch') . " WHERE `reid`=:reid";
			$huodongtitle = pdo_fetch($sql2, $params);
			if (empty($list)) {
				message('暂时没有数据');
			}
			if (is_array($list) && !empty($list)) {
				foreach ($list as &$row) {
					$user = pdo_fetch("SELECT nickname FROM" . tablename('hnfans') . " WHERE from_user=:from_user AND weid=:weid", array(':from_user' => $row['openid'], ':weid' => $_W['uniacid']));
					$row['nickname'] = $user['nickname'];
				}
			}
			foreach ($list as &$r) {
				$r['fields'] = array();
				$sql = 'SELECT data, refid FROM ' . tablename('hnresearch_data') . " WHERE `reid`=:reid AND `rerid`='{$r['rerid']}'";
				$fdatas = pdo_fetchall($sql, $params);
				foreach ($fdatas as $fd) {
					$r['fields'][$fd['refid']] .= $fd['data'];
				}
			}
			$data = array();
			foreach ($list as $key => $value) {
				if (!empty($value['fields'])) {
					foreach ($value['fields'] as $field) {
						$data[$key][] = str_replace(array("\n", "\r", "\t"), '', $field);
					}
				}
				$data[$key]['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
				$data[$key]['openid'] = $value['openid'];
				$data[$key]['nickname'] = $value['nickname'];
			}
			$html = "\xEF\xBB\xBF";
			$num = count($tableheader) - 1;
			for ($j = $num; $j >= 0; $j--) {
				$html .= $tableheader[$j]['title'] . "\t ,";
			}
			$html .= "\n";
			foreach ($data as $value) {
				$html .= $huodongtitle['title'] . "\t ,";
				$html .= $value['nickname'] . "\t ,";
				$html .= $value['openid'] . "\t ,";
				$html .= $value['createtime'] . "\t ,";
				for ($i = 0; $i < $tablelength; $i++) {
					$html .= $value[$i] . "\t ,";
				}
				$html .= "\n";
			}
			header('Content-type:text/csv');
			header('Content-Disposition:attachment; filename=' . $huodongtitle['title'] . "活动全部数据.csv");
			echo $html;
			exit();
		}
		include $this->template('hnmanage');
	}

	public function doWebDisplay()
	{
		global $_W, $_GPC;
		if ($_W['ispost']) {
			$reid = intval($_GPC['reid']);
			$switch = intval($_GPC['switch']);
			$sql = 'UPDATE ' . tablename('hnresearch') . ' SET `status`=:status WHERE `reid`=:reid';
			$params = array();
			$params[':status'] = $switch;
			$params[':reid'] = $reid;
			pdo_query($sql, $params);
			exit();
		}
		$sql = 'SELECT * FROM ' . tablename('hnresearch') . ' WHERE `weid`=:weid';
		$status = $_GPC['status'];
		if ($status != '') {
			$sql .= " and status=" . intval($status);
		}
		$ds = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
		foreach ($ds as &$item) {
			$item['isstart'] = $item['starttime'] > 0;
			$item['switch'] = $item['status'];
			$item['link'] = $_W['siteroot'] . "app/" . $this->createMobileUrl('research', array('id' => $item['reid']));
			$item['link'] = str_replace('./', '', $item['link']);
		}
		include $this->template('hndisplay');
	}

	public function doMobilehuodongindex()
	{
		global $_W, $_GPC;
		$cfg = $this->module['config'];
		$sql = 'SELECT * FROM ' . tablename('hnresearch') . ' WHERE `weid`=:weid ORDER BY reid desc';
		$ds = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		foreach ($ds as &$item) {
			$item['isstart'] = $item['starttime'] > 0;
			$item['switch'] = $item['status'];
			$item['link'] = $_W['siteroot'] . "app/" . $this->createMobileUrl('huodongcontent', array('id' => $item['reid']));
		}
		include $this->template('huodongindex');
	}

	public function doMobilehuodongcontent()
	{
		global $_W, $_GPC;
		$cfg = $this->module['config'];
		$id = $_GPC['id'];
		$sql = 'SELECT * FROM ' . tablename('hnresearch') . ' WHERE weid=:weid AND reid=:reid';
		$row = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':reid' => $id));
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		include $this->template('huodongcontent');
	}

	public function doWebDelete()
	{
		global $_W, $_GPC;
		$reid = intval($_GPC['id']);
		if ($reid > 0) {
			$params = array();
			$params[':reid'] = $reid;
			$sql = 'DELETE FROM ' . tablename('hnresearch') . ' WHERE `reid`=:reid';
			pdo_query($sql, $params);
			$sql = 'DELETE FROM ' . tablename('hnresearch_rows') . ' WHERE `reid`=:reid';
			pdo_query($sql, $params);
			$sql = 'DELETE FROM ' . tablename('hnresearch_fields') . ' WHERE `reid`=:reid';
			pdo_query($sql, $params);
			$sql = 'DELETE FROM ' . tablename('hnresearch_data') . ' WHERE `reid`=:reid';
			pdo_query($sql, $params);
			message('操作成功.', referer());
		}
		message('非法访问.');
	}

	public function doWebResearchDelete()
	{
		global $_W, $_GPC;
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			pdo_delete('hnresearch_rows', array('rerid' => $id));
		}
		message('操作成功.', referer());
	}

	public function doWebPost()
	{
		global $_W, $_GPC;
		$reid = intval($_GPC['id']);
		$hasData = false;
		if ($reid) {
			$sql = 'SELECT COUNT(*) FROM ' . tablename('hnresearch_rows') . ' WHERE `reid`=' . $reid;
			if (pdo_fetchcolumn($sql) > 0) {
				$hasData = true;
			}
		}
		if (checksubmit()) {
			$record = array();
			$record['title'] = trim($_GPC['activity']);
			$record['weid'] = $_W['uniacid'];
			$record['description'] = trim($_GPC['description']);
			$record['content'] = trim($_GPC['content']);
			$record['information'] = trim($_GPC['information']);
			if (!empty($_GPC['thumb'])) {
				$record['thumb'] = $_GPC['thumb'];
				load()->func('file');
				file_delete($_GPC['thumb-old']);
			}
			$record['status'] = intval($_GPC['status']);
			$record['inhome'] = intval($_GPC['inhome']);
			$record['pretotal'] = intval($_GPC['pretotal']);
			$record['starttime'] = strtotime($_GPC['starttime']);
			$record['endtime'] = strtotime($_GPC['endtime']);
			$record['noticeemail'] = trim($_GPC['noticeemail']);
			if (empty($reid)) {
				$record['status'] = 1;
				$record['createtime'] = TIMESTAMP;
				pdo_insert('hnresearch', $record);
				$reid = pdo_insertid();
				if (!$reid) {
					message('保存失败, 请稍后重试.');
				}
			} else {
				if (pdo_update('hnresearch', $record, array('reid' => $reid)) === false) {
					message('保存失败, 请稍后重试.');
				}
			}
			if (!$hasData) {
				$sql = 'DELETE FROM ' . tablename('hnresearch_fields') . ' WHERE `reid`=:reid';
				$params = array();
				$params[':reid'] = $reid;
				pdo_query($sql, $params);
				foreach ($_GPC['title'] as $k => $v) {
					$field = array();
					$field['reid'] = $reid;
					$field['title'] = trim($v);
					$field['displayorder'] = range_limit($_GPC['displayorder'][$k], 0, 254);
					$field['type'] = $_GPC['type'][$k];
					$field['essential'] = $_GPC['essentialvalue'][$k] == 'true' ? 1 : 0;
					$field['bind'] = $_GPC['bind'][$k];
					$field['value'] = $_GPC['value'][$k];
					$field['value'] = urldecode($field['value']);
					$field['description'] = $_GPC['desc'][$k];
					pdo_insert('hnresearch_fields', $field);
				}
			}
			if (!empty($record['noticeemail'])) {
				load()->func('communication');
				ihttp_email($record['noticeemail'], $record['title'] . '的报名提醒', $record['description']);
			}
			message('保存成功.', 'refresh');
		}
		$types = array();
		$types['number'] = '数字(number)';
		$types['text'] = '字串(text)';
		$types['textarea'] = '文本(textarea)';
		$types['radio'] = '单选(radio)';
		$types['checkbox'] = '多选(checkbox)';
		$types['select'] = '选择(select)';
		$types['calendar'] = '日历(calendar)';
		$types['email'] = '电子邮件(email)';
		$types['image'] = '上传图片(image)';
		$types['range'] = '日期范围(range)';
		$types['reside'] = '居住地(reside)';
		$fields = fans_fields();
		if ($reid) {
			$sql = 'SELECT * FROM ' . tablename('hnresearch') . ' WHERE `weid`=:weid AND `reid`=:reid';
			$params = array();
			$params[':weid'] = $_W['uniacid'];
			$params[':reid'] = $reid;
			$activity = pdo_fetch($sql, $params);
			$activity['starttime'] && $activity['starttime'] = date('Y-m-d H:i:s', $activity['starttime']);
			$activity['endtime'] && $activity['endtime'] = date('Y-m-d H:i:s', $activity['endtime']);
			if ($activity) {
				$sql = 'SELECT * FROM ' . tablename('hnresearch_fields') . ' WHERE `reid`=:reid ORDER BY `refid`';
				$params = array();
				$params[':reid'] = $reid;
				$ds = pdo_fetchall($sql, $params);
			}
		}
		if (empty($activity['endtime'])) {
			$activity['endtime'] = date('Y-m-d', strtotime('+1 day'));
		}
		include $this->template('hnpost');
	}

	public function doMobileResearch()
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		$reid = intval($_GPC['id']);
		$sql = 'SELECT * FROM ' . tablename('hnresearch') . ' WHERE `weid`=:weid AND `reid`=:reid';
		$params = array();
		$params[':weid'] = $_W['uniacid'];
		$params[':reid'] = $reid;
		$activity = pdo_fetch($sql, $params);
		$title = $activity['title'];
		if ($activity['status'] != '1') {
			message('当前活动已经停止.');
		}
		if (!$activity) {
			message('非法访问.');
		}
		if ($activity['starttime'] > TIMESTAMP) {
			message('当前活动还未开始！');
		}
		if ($activity['endtime'] < TIMESTAMP) {
			message('当前活动已经结束！');
		}
		$sql = 'SELECT * FROM ' . tablename('hnresearch_fields') . ' WHERE `reid` = :reid';
		$params = array();
		$params[':reid'] = $reid;
		$ds = pdo_fetchall($sql, $params);
		if (!$ds) {
			message('非法访问.');
		}
		$initRange = $initCalendar = false;
		$binds = array();
		foreach ($ds as &$r) {
			if ($r['type'] == 'range') {
				$initRange = true;
			}
			if ($r['type'] == 'calendar') {
				$initCalendar = true;
			}
			if ($r['value']) {
				$r['options'] = explode(',', $r['value']);
			}
			if ($r['bind']) {
				$binds[$r['type']] = $r['bind'];
			}
			if ($r['type'] == 'reside') {
				$reside = $r;
			}
		}
		if (checksubmit('submit')) {
			$pretotal = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('hnresearch_rows') . " WHERE reid = :reid AND openid = :openid", array(':reid' => $reid, ':openid' => $_W['fans']['from_user']));
			if ($pretotal >= $activity['pretotal']) {
				message('抱歉,每人只能报名' . $activity['pretotal'] . "次！", $this->createMobileUrl('huodongindex'), 'error');
			}
			if (empty($_W['fans']['from_user'])) {
				message('非法进去，请从公众号进入！');
			} else {
				$checksql = "SELECT * FROM " . tablename('hnfans') . " WHERE from_user=:from_user AND weid=:weid";
				$checkit = pdo_fetch($checksql, array(':from_user' => $_W['fans']['from_user'], ':weid' => $weid));
				if (empty($checkit)) {
					message('对不起，本活动只准许交友系统会员报名！');
				}
			}
			$row = array();
			$row['reid'] = $reid;
			$row['openid'] = $_W['fans']['from_user'];
			$row['createtime'] = TIMESTAMP;
			$datas = $fields = $update = array();
			foreach ($ds as $value) {
				$fields[$value['refid']] = $value;
			}
			foreach ($_GPC as $key => $value) {
				if (strexists($key, 'field_')) {
					$bindFiled = substr(strrchr($key, '_'), 1);
					if (!empty($bindFiled)) {
						$update[$bindFiled] = $value;
					}
					$refid = intval(str_replace('field_', '', $key));
					$field = $fields[$refid];
					if ($refid && $field) {
						$entry = array();
						$entry['reid'] = $reid;
						$entry['rerid'] = 0;
						$entry['refid'] = $refid;
						if (in_array($field['type'], array('number', 'text', 'calendar', 'email', 'textarea', 'radio', 'range', 'select'))) {
							$entry['data'] = strval($value);
						}
						if (in_array($field['type'], array('checkbox'))) {
							if (!is_array($value)) continue;
							$entry['data'] = implode(';', $value);
						}
						$datas[] = $entry;
					}
				}
			}
			if ($_FILES) {
				load()->func('file');
				foreach ($_FILES as $key => $file) {
					if (strexists($key, 'field_')) {
						$refid = intval(str_replace('field_', '', $key));
						$field = $fields[$refid];
						if ($refid && $field && $file['name'] && $field['type'] == 'image') {
							$entry = array();
							$entry['reid'] = $reid;
							$entry['rerid'] = 0;
							$entry['refid'] = $refid;
							$ret = file_upload($file);
							if (!$ret['success']) {
								message('上传图片失败, 请稍后重试.');
							}
							$entry['data'] = trim($ret['path']);
							$datas[] = $entry;
						}
					}
				}
			}
			if (!empty($_GPC['reside'])) {
				if (in_array('reside', $binds)) {
					$update['resideprovince'] = $_GPC['reside']['province'];
					$update['residecity'] = $_GPC['reside']['city'];
					$update['residedist'] = $_GPC['reside']['district'];
				}
				foreach ($_GPC['reside'] as $key => $value) {
					$resideData = array('reid' => $reside['reid']);
					$resideData['rerid'] = 0;
					$resideData['refid'] = $reside['refid'];
					$resideData['data'] = $value;
					$datas[] = $resideData;
				}
			}
			if (!empty($update)) {
				load()->model('mc');
				mc_update($_W['member']['uid'], $update);
			}
			if (empty($datas)) {
				message('非法访问.', '', 'error');
			}
			if (pdo_insert('hnresearch_rows', $row) != 1) {
				message('保存失败.');
			}
			$rerid = pdo_insertid();
			if (empty($rerid)) {
				message('保存失败.');
			}
			foreach ($datas as &$r) {
				$r['rerid'] = $rerid;
				pdo_insert('hnresearch_data', $r);
			}
			if (empty($activity['starttime'])) {
				$record = array();
				$record['starttime'] = TIMESTAMP;
				pdo_update('hnresearch', $record, array('reid' => $reid));
			}
			if (!empty($datas) && !empty($activity['noticeemail'])) {
				foreach ($datas as $row) {
					$img = "<img src='";
					if (substr($row['data'], 0, 6) == 'images') {
						$body = $fields[$row['refid']]['title'] . ':' . $img . tomedia($row['data']) . ' />';
					}
					$body .= $fields[$row['refid']]['title'] . ':' . $row['data'];
				}
				load()->func('communication');
				ihttp_email($activity['noticeemail'], $activity['title'] . '的报名提醒', $body);
			}
			message($activity['information'], $this->createMobileUrl('huodongindex'), 'sucess');
		}
		foreach ($binds as $key => $value) {
			if ($value == 'reside') {
				unset($binds[$key]);
				$binds[] = 'resideprovince';
				$binds[] = 'residecity';
				$binds[] = 'residedist';
				break;
			}
		}
		if (!empty($_W['fans']['from_user']) && !empty($binds)) {
			$profile = fans_search($_W['fans']['from_user'], $binds);
			if ($profile['gender']) {
				if ($profile['gender'] == '0') $profile['gender'] = '保密';
				if ($profile['gender'] == '1') $profile['gender'] = '男';
				if ($profile['gender'] == '2') $profile['gender'] = '女';
			}
			foreach ($ds as &$r) {
				if ($profile[$r['bind']]) {
					$r['default'] = $profile[$r['bind']];
				}
			}
		}
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		load()->func('tpl');
		include $this->template('hnsubmit');
	}

	public function doMobileMyResearch()
	{
		global $_W, $_GPC;
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$rows = pdo_fetchall("SELECT * FROM " . tablename('hnresearch_rows') . " WHERE openid = :openid", array(':openid' => $_W['fans']['from_user']));
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$reids[$row['reid']] = $row['reid'];
				}
				$research = pdo_fetchall("SELECT * FROM " . tablename('hnresearch') . " WHERE reid IN (" . implode(',', $reids) . ")", array(), 'reid');
			}
		} elseif ($operation == 'detail') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT * FROM " . tablename('hnresearch_rows') . " WHERE openid = :openid AND rerid = :rerid", array(':openid' => $_W['fans']['from_user'], ':rerid' => $id));
			if (empty($row)) {
				message('我的预约不存在或是已经被删除！');
			}
			$research = pdo_fetch("SELECT * FROM " . tablename('hnresearch') . " WHERE reid = :reid", array(':reid' => $row['reid']));
			$research['fields'] = pdo_fetchall("SELECT a.title, a.type, b.data FROM " . tablename('hnresearch_fields') . " AS a LEFT JOIN " . tablename('hnresearch_data') . " AS b ON a.refid = b.refid WHERE a.reid = :reid AND b.rerid = :rerid", array(':reid' => $row['reid'], ':rerid' => $id));
		}
		$settings = pdo_fetch("SELECT * FROM " . tablename('meepo_hongniangset') . " WHERE weid=:weid", array(':weid' => $_W['weid']));
		include $this->template('hnresearch');
	}

	public function doWebslide()
	{
		global $_W, $_GPC;
		//$check = $this->docheckurl();
		//if ($check) {
		//	die('NO');
		//}
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$list = pdo_fetchall("SELECT * FROM " . tablename('meepoweixiangqin_slide') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
		} elseif ($operation == 'post') {
			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				$data = array('weid' => $_W['uniacid'], 'title' => $_GPC['title'], 'url' => $_GPC['url'], 'status' => intval($_GPC['status']), 'displayorder' => intval($_GPC['displayorder']), 'attachment' => $_GPC['attachment']);
				if (!empty($id)) {
					pdo_update('meepoweixiangqin_slide', $data, array('id' => $id));
				} else {
					pdo_insert('meepoweixiangqin_slide', $data);
					$id = pdo_insertid();
				}
				message('更新幻灯片成功！', $this->createWebUrl('slide', array('op' => 'display')), 'success');
			}
			$adv = pdo_fetch("select * from " . tablename('meepoweixiangqin_slide') . " where id=:id and weid=:weid limit 1", array(":id" => $id, ":weid" => $_W['uniacid']));
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$adv = pdo_fetch("SELECT id  FROM " . tablename('meepoweixiangqin_slide') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
			if (empty($adv)) {
				message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('slide', array('op' => 'display')), 'error');
			}
			pdo_delete('meepoweixiangqin_slide', array('id' => $id));
			message('幻灯片删除成功！', $this->createWebUrl('slide', array('op' => 'display')), 'success');
		} else {
			message('请求方式不存在');
		}
		include $this->template('adv', TEMPLATE_INCLUDEPATH, true);
	}

	function getDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2)
	{
		$radLat1 = $lat1 * M_PI / 180;
		$radLat2 = $lat2 * M_PI / 180;
		$a = $lat1 * M_PI / 180 - $lat2 * M_PI / 180;
		$b = $lng1 * M_PI / 180 - $lng2 * M_PI / 180;
		$s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
		$s = $s * EARTH_RADIUS;
		$s = round($s * 1000);
		if ($len_type > 1) {
			$s /= 1000;
		}
		$s /= 1000;
		return round($s, $decimal);
	}

	function rad($d)
	{
		return $d * 3.1415926535898 / 180.0;
	}

	function GetDistance2($lat1, $lng1, $lat2, $lng2)
	{
		$EARTH_RADIUS = 6378.137;
		$radLat1 = $this->rad($lat1);
		$radLat2 = $this->rad($lat2);
		$a = $radLat1 - $radLat2;
		$b = $this->rad($lng1) - $this->rad($lng2);
		$s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
		$s = $s * $EARTH_RADIUS;
		$s = round($s * 10000) / 10000;
		return $s;
	}

	public function squarePoint($lng, $lat, $distance = 0.5)
	{
		$dlng = 2 * asin(sin($distance / (2 * EARTH_RADIUS)) / cos(deg2rad($lat)));
		$dlng = rad2deg($dlng);
		$dlat = $distance / EARTH_RADIUS;
		$dlat = rad2deg($dlat);
		return array('left-top' => array('lat' => $lat + $dlat, 'lng' => $lng - $dlng), 'right-top' => array('lat' => $lat + $dlat, 'lng' => $lng + $dlng), 'left-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng - $dlng), 'right-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng + $dlng));
	}

	private function sendmessage($content, $openid)
	{
		global $_W, $_GPC;
		$weid = $_W['weid'];
		$cfg = $this->module['config'];
		$appid = $cfg['appid'];
		$secret = $cfg['secret'];
		$img = $_W['attachurl'] . $cfg['kefuimg'];
		$id = $_W['openid'];
		$res = $this->getres2($id);
		load()->classs('weixin.account');
		$accObj = WeixinAccount::create($_W['account']['acid']);
		$access_token = $accObj->fetch_token();
		$token2 = $access_token;
		$title = $res['nickname'] . '给你发来新消息了！';
		$fans = pdo_fetch('SELECT salt,acid,openid FROM ' . tablename('mc_mapping_fans') . ' WHERE uniacid = :uniacid AND openid = :openid', array(':uniacid' => $weid, ':openid' => $openid));
		$pass['time'] = TIMESTAMP;
		$pass['acid'] = $fans['acid'];
		$pass['openid'] = $fans['openid'];
		$pass['hash'] = md5("{$fans['openid']}{$pass['time']}{$fans['salt']}{$_W['config']['setting']['authkey']}");
		$auth = base64_encode(json_encode($pass));
		$vars = array();
		$vars['__auth'] = $auth;
		$vars['forward'] = base64_encode($this->createMobileUrl('hitmail', array('toname' => $res['nickname'], 'toopenid' => $id)));
		$url2 = $_W['siteroot'] . 'app/' . murl('auth/forward', $vars);
		$data = '{
										"touser":"' . $openid . '",
										"msgtype":"news",
										"news":{
											"articles": [
											 {
												 "title":"' . $title . '",
												 "description":"' . $title . '",
												 "url":"' . $url2 . '",
												 "picurl":"' . $img . '",
											 }
											 ]
										}
									}';
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $token2;
		load()->func('communication');
		$it = ihttp_post($url, $data);
	}
}