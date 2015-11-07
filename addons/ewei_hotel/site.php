<?php
/**
 * 微酒店
 *
 */
defined('IN_IA') or exit('Access Denied');

include "model.php";

class Ewei_hotelModuleSite extends WeModuleSite {

	public $_img_url = '../addons/ewei_hotel/template/style/img/';

	public $_css_url = '../addons/ewei_hotel/template/style/css/';

	public $_script_url = '../addons/ewei_hotel/template/style/js/';

	public $_search_key = '__hotel2_search';

	public $_from_user = '';

	public $_weid = '';

	public $_version = 0;

	public $_hotel_level_config = array(5 => '五星级酒店', 4 => '四星级酒店', 3 => '三星级酒店', 2 => '两星级以下', 15 => '豪华酒店', 14 => '高档酒店', 13 => '舒适酒店', 12 => '经济型酒店', );

	public $_set_info = array();

	public $_user_info = array();



	function __construct()
	{
		global $_W;
		$this->_from_user = $_W['fans']['from_user'];
		$this->_weid = $_W['uniacid'];
		$this->_set_info = get_hotel_set();
		$this->_version = $this->_set_info['version'];
	}

	public function getItemTiles() {
		global $_W;
		$urls = array(
			array('title' => "酒店首页", 'url' => $this->createMobileUrl('index')),
			array('title' => "我的订单", 'url' => $this->createMobileUrl('orderlist')),
		);
		return $urls;
	}

	function getSearchArray(){

		$search_array = get_cookie($this->_search_key);
		if (empty($search_array)) {
			//默认搜索参数
			$search_array['order_type'] = 1;
			$search_array['order_name'] = 2;
			$search_array['location_p'] = $this->_set_info['location_p'];
			$search_array['location_c'] = $this->_set_info['location_c'];
			if (strpos($search_array['location_p'], '市') > -1) {
				//直辖市
				$search_array['municipality'] = 1;
				$search_array['city_name'] = $search_array['location_p'];
			} else {
				$search_array['municipality'] = 0;
				$search_array['city_name'] = $search_array['location_c'];
			}
			$search_array['business_id'] = 0;
			$search_array['business_title'] = '';
			$search_array['brand_id'] = 0;
			$search_array['brand_title'] = '';

			$weekarray = array("日", "一", "二", "三", "四", "五", "六");

			$date = date('Y-m-d');
			$time = strtotime($date);
			$search_array['btime'] = $time;
			$search_array['etime'] = $time + 86400;
			$search_array['bdate'] = $date;
			$search_array['edate'] = date('Y-m-d', $search_array['etime']);
			$search_array['bweek'] = '星期' . $weekarray[date("w", $time)];
			$search_array['eweek'] = '星期' . $weekarray[date("w", $search_array['etime'])];
			$search_array['day'] = 1;
			insert_cookie($this->_search_key, $search_array);
		}
		//print_r($search_array);exit;
		return $search_array;
	}

	//入口文件
	public function doMobileIndex()
	{
		global $_GPC, $_W;
		$weid = $this->_weid;
		$from_user = $this->_from_user;
		$set = $this->_set_info;
		$hid = $_GPC['hid'];
		$user_info = pdo_fetch("SELECT * FROM " . tablename('hotel2_member') . " WHERE from_user = :from_user AND weid = :weid limit 1", array(':from_user' => $from_user, ':weid' => $weid));
		//独立用户
		if ($set['user'] == 2) {
			if (empty($user_info['id'])) {
				//用户不存在
				if ($set['reg'] == 1) {
					//开启注册
					$url = $this->createMobileUrl('register');
				} else {
					//禁止注册
					$url = $this->createMobileUrl('login');
				}
			} else {
				//用户已经存在，判断用户是否登录
				$check = check_hotel_user_login($this->_set_info);
				if ($check) {
					if ($user_info['status'] == 1) {
						$url = $this->createMobileUrl('search');
					} else {
						$url = $this->createMobileUrl('login');
					}
				} else {
					$url = $this->createMobileUrl('login');
				}
			}
		} else {
			//微信用户
			if (empty($user_info['id'])) {
				//用户不存在，自动添加一个用户
				$member = array();
				$member['weid'] = $weid;
				$member['from_user'] = $from_user;
				$member['createtime'] = time();
				$member['isauto'] = 1;
				$member['status'] = 1;
				pdo_insert('hotel2_member', $member);
				$member['id'] = pdo_insertid();
				$member['user_set'] = $set['user'];
				//自动添加成功，将用户信息放入cookie
				hotel_set_userinfo(0, $member);
			} else {
				if ($user_info['status'] == 1) {
					$user_info['user_set'] = $set['user'];
					//用户已经存在，将用户信息放入cookie
					hotel_set_userinfo(1, $user_info);
				} else {
					//用户帐号被禁用
					$msg = "抱歉，你的帐号被禁用，请联系酒店解决。";
					if ($this->_set_info['is_unify'] == 1) {
						$msg .= "酒店电话：" . $this->_set_info['tel'] . "。";
					}
					$url = $this->createMobileUrl('error',array('msg' => $msg));
					header("Location: $url");
					exit;
				}
			}
			//微信粉丝，可以直接使用
			$url = $this->createMobileUrl('search', array('hid' => $hid));
		}
		header("Location: $url");
		exit;
	}

	//检查酒店版本
	public function check_version()
	{
		global $_GPC, $_W;
		$weid = $this->_weid;
		$hid = $_GPC['hid'];
		//单酒店版
		if ($this->_version == 0) {
			$params = array(':weid' => $weid, ':status' => '1');
			if (empty($hid)) {
				$where = ' ORDER BY displayorder DESC';
			} else {
				$where = ' AND `id` = :id';
				$params[':id'] = $hid;
			}
			$sql = "SELECT id FROM " . tablename('hotel2') . " WHERE weid = :weid AND status = :status" . $where;
			$data = pdo_fetch($sql, $params);
			if (empty($data['id'])) {
				echo "酒店信息获取失败";exit;
			}
			$hid = intval($data['id']);
			$url = $this->createMobileUrl('detail', array('hid' => $hid));
			header("Location: $url");
		}
	}

	//查询条件页
	public function doMobilesearch()
	{
		global $_GPC, $_W;
		$this->check_login();
		$search_array = $this->getSearchArray();
		$this->check_version();
		$key_word = '';
		if (!empty($search_array['keyword'])) {
			$key_word .= $search_array['keyword'];
		}
		if (!empty($search_array['business_id'])) {
			if (!empty($key_word)) {
				$key_word .= "/";
			}
			$key_word .= $search_array['business_title'];
		}
		if (!empty($search_array['brand_id'])) {
			if (!empty($key_word)) {
				$key_word .= "/";
			}
			$key_word .= $search_array['brand_title'];
		}
		if (empty($key_word)) {
			$key_word = '酒店名/商圈/品牌';
		}
		include $this->template('search');
	}

	//日期选择页
	public function doMobiledate()
	{
		global $_GPC, $_W;
		$this->check_login();
		$search_array = get_cookie($this->_search_key);
		$hid = $_GPC['hid'];
		if ($search_array && !empty($search_array['bdate']) && !empty($search_array['day'])) {
			$bdate = $search_array['bdate'];
			$day = $search_array['day'];
		} else {
			$bdate = date('Y-m-d');
			$day = 1;
		}
		load()->func('tpl');
		include $this->template('date');
	}

	//登录页
	public function doMobilelogin()
	{
		global $_GPC, $_W;;
		$set = $this->_set_info;

		if (checksubmit()) {
			$member = array();
			$username = trim($_GPC['username']);

			if (empty($username)) {
				die(json_encode(array("result" => 2, "error" => "请输入要登录的用户名")));
			}
			$member['username'] = $username;
			$member['password'] = $_GPC['password'];
			//$member['status'] = 1;

			if (empty($member['password'])) {
				die(json_encode(array("result" => 3, "error" => "请输入登录密码")));
			}

			$weid = $this->_weid;
			$from_user = $this->_from_user;
			$set = $this->_set_info;

			$member['weid'] = $weid;
			$record = hotel_member_single($member);

			if (!empty($record)) {
				if ( ($set['bind'] == 3 && ($record['userbind'] == 1) || $set['bind'] == 2)) {
					if (!empty($record['from_user'])) {
						if ($record['from_user'] != $this->_from_user) {
							die(json_encode(array("result" => 0, "error" => "登录失败，您的帐号与绑定的微信帐号不符！")));
						}
					}
				}

				if (empty($record['status'])) {
					die(json_encode(array("result" => 0, "error" => "登录失败，您的帐号被禁止登录，请联系酒店解决！")));
				}

				$record['user_set'] = $set['user'];

				//登录成功
				hotel_set_userinfo(0, $record);

				$url = $this->createMobileUrl('search');
				die(json_encode(array("result" => 1, "url" => $url)));
			} else {
				die(json_encode(array("result" => 0, "error" => "登录失败，请检查您输入的用户名和密码！")));
			}
		} else {
			include $this->template('login');
		}
	}

	//ajax数据处理,包含 城市选择 时间选择 价格选择
	public function doMobileajaxData()
	{
		global $_GPC, $_W;
		$hid = $_GPC['hid'];
		$data = $this->getSearchArray();
		$key = $this->_search_key;
		switch ($_GPC['ac'])
		{
			//选择日期
			case 'time':
				$bdate = $_GPC['bdate'];
				$day = $_GPC['day'];
				if (!empty($bdate) && !empty($day)) {
					$btime = strtotime($bdate);
					$etime = $btime + $day * 86400;
					$weekarray = array("日", "一", "二", "三", "四", "五", "六");
					$data['btime'] = $btime;
					$data['etime'] = $etime;
					$data['bdate'] = $bdate;
					$data['edate'] = date('Y-m-d', $etime);
					$data['bweek'] = '星期' . $weekarray[date("w", $btime)];
					$data['eweek'] = '星期' . $weekarray[date("w", $etime)];
					$data['day'] = $day;
					insert_cookie($this->_search_key, $data);
					$url = $this->createMobileUrl('detail', array('hid' => $hid));
					die(json_encode(array("result" => 1, "url" => $url)));
				}
				break;

			//选择价格和星级
			case 'price':
				$price_type = $_GPC['price_type'];
				$price_value = $_GPC['price_value'];

				if (empty($price_value)) {
					$data['price_type'] = 0;
				} else {
					$data['price_type'] = $price_type;
				}
				$data['price_value'] = $price_value;
				insert_cookie($key, $data);
				die(json_encode(array("result" => 1)));
				break;

			//选择城市
			case 'city':
				$location_p = $_GPC['location_p'];
				$location_c = $_GPC['location_c'];

				if (!empty($location_p) && !empty($location_c)) {

					if (strpos($location_p, '市') > -1) {
						//直辖市
						$data['municipality'] = 1;
						$data['city_name'] = $location_p;
					} else {
						$data['municipality'] = 0;
						$data['city_name'] = $location_c;
					}

					$data['location_p'] = $location_p;
					$data['location_c'] = $location_c;

					insert_cookie($key, $data);
				}
				$url = $this->createMobileUrl('search');
				die(json_encode(array("result" => 1, "url" => $url)));
				break;

			//价格排序
			case 'orderby':
				$order_name = $_GPC['order_name'];
				$order_type = $_GPC['order_type'];

				$data['order_name'] = $order_name;
				$data['order_type'] = $order_type;

				insert_cookie($key, $data);
				$url = $this->createMobileUrl('list');
				die(json_encode(array("result" => 1, "order_type"=>$order_type,"order_name"=>$order_name, "url" => $url)));
				break;

			//选择品牌商圈
			case 'brand':
				$business_id = $_GPC['business_id'];
				$business_title = $_GPC['business_title'];
				$brand_id = $_GPC['brand_id'];
				$brand_title = $_GPC['brand_title'];
				$keyword = $_GPC['keyword'];

				$data['business_id'] = $business_id;
				$data['brand_id'] = $brand_id;
				if (!empty($business_title)) {
					$data['business_title'] = $business_title;
				}
				if (!empty($brand_title)) {
					$data['brand_title'] = $brand_title;
				}
				$data['keyword'] = $keyword;

				insert_cookie($key, $data);
				$url = $this->createMobileUrl('search');
				die(json_encode(array("result" => 1, "url" => $url)));
				break;

			//清除品牌商圈信息
			case 'clear_brand':
				$data['business_id'] = 0;
				$data['brand_id'] = 0;
				$data['business_title'] = '';
				$data['brand_title'] = '';
				$data['keyword'] = '';

				insert_cookie($key, $data);
				$url = $this->createMobileUrl('search');
				die(json_encode(array("result" => 1, "url" => $url)));
				break;
		}
	}

	//城市选择页
	public function doMobilecity()
	{
		global $_GPC, $_W;

		$this->check_login();
		$search_array = get_cookie($this->_search_key);
		include $this->template('city');
	}

	//预定页，预定信息提交页
	public function doMobileOrder()
	{
		global $_GPC, $_W;
		$this->check_login();

		$paysetting = uni_setting($_W['uniacid'], array('payment', 'creditbehaviors'));
		$_W['account'] = array_merge($_W['account'], $paysetting);
		$isauto = $this->_user_info['isauto'];
		$hid = intval($_GPC['hid']);
		$id = intval($_GPC['id']);
		$weid = $this->_weid;
		$price = $_GPC['price'];
		if(empty($hid) || empty($id)){
			message("参数错误1！");
		}

		$search_array = $this->getSearchArray();
		if (!$search_array || empty($search_array['btime']) || empty($search_array['day'])) {
			$url = $this->createMobileUrl('index');
			header("Location: $url");
		}

		$is_submit = checksubmit();
		$sql = 'SELECT `title`, `mail`, `phone`, `thumb`, `description` FROM ' . tablename('hotel2') . ' WHERE `id` = :id';
		$reply = pdo_fetch($sql, array(':id' => $hid));
		if (empty($reply)) {
			if ($is_submit) {
				die(json_encode(array("result" => 0, "error" => "酒店未找到!")));
			} else {
				message('酒店未找到, 请联系管理员!');
			}
		}

		// 设置分享信息
		$shareTitle = $reply['title'];
		$shareDesc = $reply['description'];
		$shareThumb = tomedia($reply['thumb']);

		if ($this->_set_info['is_unify'] == 1) {
			$tel = $this->_set_info['tel'];
		} else {
			$tel = $reply['phone'];
		}

		$pricefield = $this->_user_info['isauto'] == 1 ? 'cprice' : 'mprice';
		$sql = "SELECT *, $pricefield AS roomprice FROM " . tablename('hotel2_room') . " WHERE `id` = :id AND `hotelid` = :hotelid ";
		$room = pdo_fetch($sql, array(':id' => $id, ':hotelid' => $hid));
		if (empty($room)) {
			if ($is_submit) {
				die(json_encode(array("result" => 0, "error" => "房型未找到!")));
			} else {
				message("房型未找到, 请联系管理员!");
			}
		}

		// 入住
		$btime = $search_array['btime'];
		$bdate = $search_array['bdate'];
		// 住几天
		$days =intval($search_array['day']);
		// 离店
		$etime = $search_array['etime'];
		$edate = $search_array['edate'] ;
		$date_array = array();
		$date_array[0]['date'] = $bdate;
		$date_array[0]['day'] = date('j', $btime);
		$date_array[0]['time'] = $btime;
		$date_array[0]['month'] = date('m',$btime);

		if ($days > 1) {
			for($i = 1; $i < $days; $i++) {
				$date_array[$i]['time'] = $date_array[$i-1]['time'] + 86400;
				$date_array[$i]['date'] = date('Y-m-d', $date_array[$i]['time']);
				$date_array[$i]['day'] = date('j', $date_array[$i]['time']);
				$date_array[$i]['month'] = date('m', $date_array[$i]['time']);
			}
		}

		$sql = 'SELECT `id`, `roomdate`, `num`, `status` FROM ' . tablename('hotel2_room_price') . ' WHERE `roomid` = :roomid
                AND `roomdate` >= :btime AND `roomdate` < :etime AND `status` = :status';

		$params = array(':roomid' => $id, ':btime' => $btime, ':etime' => $etime, ':status' => '1');
		$room_date_list = pdo_fetchall($sql, $params);
		$flag = intval($room_date_list);
		$list = array();
		$max_room = 8;
		$is_order = 1;

		if ($flag == 1) {
			for($i = 0; $i < $days; $i++) {
				$k = $date_array[$i]['time'];
				foreach ($room_date_list as $p_key => $p_value) {
					// 判断价格表中是否有当天的数据
					if($p_value['roomdate'] == $k) {
						$room_num = $p_value['num'];
						if (empty($room_num)) {
							$is_order = 0;
							$max_room = 0;
							$list['num'] = 0;
							$list['date'] =  $date_array[$i]['date'];
						} else if ($room_num > 0 && $room_num < $max_room) {
							$max_room = $room_num;
							$list['num'] =  $room_num;
							$list['date'] =  $date_array[$i]['date'];
						}
						break;
					}
				}
			}
		}

		if ($max_room == 0) {
			$msg = $list['date'] . '当天没有空房间了,请选择其他房型。';
			$url = $this->createMobileUrl('error', array('msg' => $msg));
			header("Location: $url");
			exit;
		}

		$user_info = hotel_get_userinfo();
		$memberid = intval($user_info['id']);

		// 显示会员价还是普通价
		$pricefield = $isauto == 1 ? 'cprice' : 'mprice';
		$r_sql = 'SELECT `roomdate`, `num`, `status`, ' . $pricefield . ' AS `m_price` FROM ' . tablename('hotel2_room_price') .
			' WHERE `roomid` = :roomid AND `weid` = :weid AND `hotelid` = :hotelid AND `roomdate` >= :btime AND ' .
			' `roomdate` < :etime';
		$params = array(':roomid' => $id, ':weid' => $weid, ':hotelid' => $hid, ':btime' => $btime, ':etime' => $etime);
		$price_list = pdo_fetchall($r_sql, $params);
		$this_price = $old_price = $room['roomprice'];
		$totalprice =  $old_price * $days;


		if ($price_list) {
			//价格表中存在
			$check_date = array();
			foreach($price_list as $k => $v) {
				$new_price = $v['m_price'];
				$roomdate = $v['roomdate'];
				if ($v['status'] == 0 || $v['num'] == 0 ) {
					$has = 0;
				} else {
					if ($new_price && $roomdate) {
						if (!in_array($roomdate, $check_date)) {
							$check_date[] = $roomdate;
							if ($old_price != $new_price) {
								$totalprice = $totalprice - $old_price + $new_price;
							}
						}
					}
				}
			}
			$this_price = round($totalprice / $days);
		}


		if ($is_submit) {
			$from_user = $this->_from_user;
			$name = $_GPC['uname'];
			$contact_name = $_GPC['contact_name'];
			$mobile = $_GPC['mobile'];

			if (empty($name)) {
				die(json_encode(array("result" => 0, "error" => "入住人不能为空!")));
			}

			if (empty($contact_name)) {
				die(json_encode(array("result" => 0, "error" => "联系人不能为空!")));
			}

			if (empty($mobile)) {
				die(json_encode(array("result" => 0, "error" => "手机号不能为空!")));
			}

			if ($_GPC['nums'] > $max_room) {
				die(json_encode(array("result" => 0, "error" => "您的预定数量超过最大限制!")));
			}

			$insert = array(
				'weid' => $weid,
				'ordersn' => date('md') . sprintf("%04d", $_W['fans']['id']) . random(4, 1),
				'hotelid' => $hid,
				'openid' => $from_user,
				'roomid' => $id,
				'memberid' => $memberid,
				'name' => $name,
				'contact_name' => $contact_name,
				'mobile' => $mobile,
				'btime' => $search_array['btime'],
				'etime' => $search_array['etime'],
				'day' => $search_array['day'],
				'style' => $room['title'],
				'nums' => intval($_GPC['nums']),
				'oprice' => $room['oprice'],
				'cprice' => $room['cprice'],
				'mprice' => $room['mprice'],
				'time' => TIMESTAMP,
				'paytype' => $_GPC['paytype']
			);

			$insert[$pricefield] = $this_price;
			$insert['sum_price'] = $totalprice * $insert['nums'];

			pdo_insert('hotel2_order', $insert);
			$order_id = pdo_insertid();

			//如果有接受订单的邮件,
			if (!empty($reply['mail'])) {
				$subject = "微信公共帐号 [" . $_W['account']['name'] . "] 微酒店订单提醒.";
				$body = "您后台有一个预定订单: <br/><br/>";
				$body .= "预定酒店: " . $reply['title'] . "<br/>";
				$body .= "预定房型: " . $room['title'] . "<br/>";
				$body .= "预定数量: " . $insert['nums'] . "<br/>";
				$body .= "预定价格: " . $insert['sum_price'] . "<br/>";
				$body .= "预定人: " . $insert['name'] . "<br/>";
				$body .= "预定电话: " . $insert['mobile'] . "<br/>";
				$body .= "到店时间: " . $bdate . "<br/>";
				$body .= "离店时间: " . $edate . "<br/><br/>";
				$body .= "请您到管理后台仔细查看. <a href='" .$_W['siteroot'] .create_url('member/login') . "' target='_blank'>立即登录后台</a>";
				$result = ihttp_email($reply['mail'], $subject, $body);
			}

			$url = $this->createMobileUrl('orderdetail', array('id' => $order_id));
			die(json_encode(array("result" => 1, "url" => $url)));
		} else {
			$price = $totalprice;
			$member = array();
			$member['from_user'] = $this->_from_user;
			$record = hotel_member_single($member);

			if ($record) {
				$realname = $record['realname'];
				$mobile = $record['mobile'];
			} else {
				$fans = pdo_fetch("SELECT id, realname, mobile FROM " . tablename('fans') . " WHERE from_user = :from_user limit 1", array(':from_user' => $this->_from_user));
				if (!empty($fans)) {
					$realname = $fans['realname'];
					$mobile = $fans['mobile'];
				}
			}
           // var_dump($_W['account']['payment']['credit']['switch']);die();
			include $this->template('order');
		}
	}

	// 酒店详情页，显示房间列表
	public function doMobilekeyword()
	{
		global $_GPC, $_W;

		$this->check_login();
		$referer = referer();
		$search_array = $this->getSearchArray();

		if (!$search_array || empty($search_array['location_p']) || empty($search_array['location_c'])) {
			$url = $this->createMobileUrl('index');
			header("Location: $url");
		}

		$search_array['business_id'] =  intval($search_array['business_id']);
		$search_array['brand_id'] =  intval($search_array['brand_id']);

		$business_sql = "SELECT id, title FROM " . tablename('hotel2_business') . " WHERE weid = '{$this->_weid}'";
		$business_sql .= " AND location_p ='" . $search_array['location_p'] . "'";
		$business_sql .= " AND location_c ='" . $search_array['location_c'] . "'";
		$business_sql .= " AND status = 1 ORDER BY displayorder DESC";
		$business_list = pdo_fetchall($business_sql);
		//print_r($business_list);exit;

		$brand_sql = "SELECT id, title FROM " . tablename('hotel2_brand') . " WHERE weid = '{$this->_weid}' AND status = 1 ORDER BY displayorder DESC";
		$brand_list = pdo_fetchall($brand_sql);

		include $this->template('keyword');

	}

	//酒店详情页，显示房间列表
	public function doMobiledetail()
	{
		global $_GPC, $_W;
		$this->check_login();
		$hid = $_GPC['hid'];
		$weid = $this->_weid;
		$referer = referer();
		$search_array = $this->getSearchArray();
		if ($this->_version == 1) {
			//多酒店才有查询条件
			if (!$search_array) {
				$url = $this->createMobileUrl('index');
				header("Location: $url");
			}
		}

		$reply = pdo_fetch("SELECT * FROM " . tablename('hotel2') . " WHERE id = :id ", array(':id' => $hid));

		// 设置分享信息
		$shareTitle = $reply['title'];
		$shareDesc = $reply['description'];
		$shareThumb = tomedia($reply['thumb']);

		if(empty($reply)){
			message("酒店未找到, 请联系管理员!");
		}
		$thumbs = unserialize($reply['thumbs']);
		$thumbcount = count($thumbs) + 1;
		if ($this->_set_info['is_unify'] == 1) {
			$tel = $this->_set_info['tel'];
		} else {
			$tel = $reply['phone'];
		}
		$ac = $_GPC['ac'];
		if ($ac == "getDate") {
			$isauto = $this->_user_info['isauto'];
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;

			//显示会员价还是普通价
			$pricefield = $isauto==1?"cprice":"mprice";

			//入住
			$bdate = $search_array['bdate'];
			$btime = $search_array['btime'];

			//住几天
			$day =intval($search_array['day']);

			//离店
			$edate = $search_array['edate'];
			$etime = $search_array['etime'];

			$params = array(
				":weid"=>$weid,
				":hotelid"=>$hid
			);

			$sql = "SELECT id, hotelid, id as roomid, title, breakfast, thumb, thumbs, oprice, " . $pricefield . " as m_price";
			$sql .= " FROM " .tablename('hotel2_room');
			$sql .= " WHERE 1 = 1";
			$sql .= " AND hotelid = :hotelid";
			$sql .= " AND weid = :weid";
			$sql .= " AND status = 1";
			$sql .= " ORDER BY displayorder, sortid DESC";

			$room_list = pdo_fetchall($sql, $params);


			//循环房间列表
			foreach($room_list as $key => $value) {
				$room_list[$key]['thumbs'] = unserialize($value['thumbs']);

				$r_sql = "SELECT roomdate, num, status, oprice, " . $pricefield . " as m_price FROM " . tablename('hotel2_room_price');
				$r_sql .= " WHERE 1 = 1";
				$r_sql .= " AND roomid = " . $value['roomid'];
				$r_sql .= " AND weid = :weid";
				$r_sql .= " AND hotelid = :hotelid";
				$r_sql .= " AND roomdate >=" . $btime ." AND roomdate <" .$etime;

				$price_list = pdo_fetchall($r_sql, $params);

				if ($price_list) {
					//价格表中存在
					$has = 1;
					$avg = 0;
					$old_price = $value['m_price'];
					$totalprice =  $old_price * $day;
					$check_date = array();
					foreach($price_list as $k => $v) {
						$new_price = $v['m_price'];
						$roomdate = $v['roomdate'];
						if ($new_price && $roomdate) {
							if (!in_array($roomdate, $check_date)) {
								$check_date[] = $roomdate;
								if ($old_price != $new_price) {
									$avg = 1;
									$totalprice = $totalprice - $old_price + $new_price;
								}
							}
						}
						if ($v['status'] == 0 || $v['num'] == 0 ) {
							$has = 0;
						} else {

						}
					}
					$room_list[$key]['has'] = $has;
					$room_list[$key]['price'] = round( $totalprice / $day);
					$room_list[$key]['total_price'] = $totalprice;
					if($day == 1) {
						$avg = 0;
					}
					$room_list[$key]['avg'] = $avg;
				} else {
					//价格表中不存在
					$room_list[$key]['has'] = 1;
					$room_list[$key]['price'] = $value['m_price'];
					$room_list[$key]['total_price'] = $value['m_price'] * $day;
					$room_list[$key]['avg'] = 0;
				}
			}


			if ($search_array['price_type'] == 1) {
				$price_value = $search_array['price_value'];
				if (!empty($price_value)) {

					foreach($room_list as $key => $value) {
						$new_price = $value['price'];
						$price_flag = 1;

						if (strstr($price_value, '-') !== false) {
							$price_array = explode("-", $price_value);
							if ($new_price >= intval($price_array[0]) && $new_price <= intval($price_array[1])) {
								$price_flag = 1;
							} else {
								$price_flag = 0;
							}
						} else {
							if ($price_value == 150) {
								if ($new_price <= 150) {
									$price_flag = 1;
								} else {
									$price_flag = 0;
								}
							}else if ($price_value == 1000) {
								if ($new_price >= 1000) {
									$price_flag = 1;
								} else {
									$price_flag = 0;
								}
							}
						}

						if ($price_flag == 0) {
							unset($room_list[$key]);
						}
					}
				}
			}


			$total = count($room_list);


			if ($total <= $psize) {
				$list = $room_list;
			} else {
				// 需要分页
				if($pindex > 0) {
					$list_array = array_chunk($room_list, $psize, true);
					$list = $list_array[($pindex-1)];
				} else {
					$list = $room_list;
				}
			}

			$data = array();
			$data['result'] = 1;
			$page_array = get_page_array($total, $pindex, $psize);


			ob_start();
			include $this->template('room_crumb');
			$data['code'] = ob_get_contents();
			ob_clean();

			$data['total'] = $total;
			$data['isshow'] = $page_array['isshow'];
			if ($page_array['isshow'] == 1) {
				$data['nindex'] = $page_array['nindex'];
			}

			die(json_encode($data));



			exit;


			$where.=" GROUP BY r.id";

			if ($search_array['price_type'] == 1) {
				$price_value = $search_array['price_value'];
				if (!empty($price_value)) {
					$where .= " HAVING";
					if (strstr($price_value, '-') !== false) {
						$price_array = explode("-", $price_value);
						$where .= " price BETWEEN " . intval($price_array[0]) . " AND " . intval($price_array[1]);
					} else {
						if ($price_value == 150) {
							$where .= " price <= 150";
						}else if ($price_value == 1000) {
							$where .= " price >= 1000";
						}
					}
				}
			}

			$sql .= $where;
			$count_sql = "select count(1) as num from (" . $sql . ") count_test";

			$sql .= " ORDER BY displayorder, sortid DESC";

//            $sql = "SELECT * FROM " . tablename('hotel2_room');
//            $where = " WHERE 1 = 1";
//            $where .= " AND hotelid = $hid";
//            $where .= " AND status = 1";
//
//            $sql .= $where;
//            $count_sql = "SELECT (id) FROM " . tablename('hotel2_room') . $where;
//
//            $sql .= " ORDER BY displayorder, sortid DESC";

			if($pindex > 0) {
				// 需要分页
				$start = ($pindex - 1) * $psize;
				$sql .= " LIMIT {$start},{$psize}";
			}

			$rooms = pdo_fetchall($sql);

			foreach($rooms as &$r){
				$pricedays = pdo_fetchall("select $pricefield as price,roomdate from ew_hotel2_room_price where roomid={$r['id']} and roomdate>=$btime and roomdate<=$etime");

				//找出$day天的价格记录
				$totalprice =  0 ;
				$prices = array();
				for($d=0;$d<$day;$d++){
					$t = $btime+ 86400 * $d;
					$p = $r['roomprice'];
					foreach($pricedays as $pd){
						if($pd['roomdate']==$t){
							$p = $pd['price'];
						}
					}
					$prices[] = $p;
					$totalprice+=$p;
				}

				//价格表的价格是否都相同
				$prices1 = array_unique($prices);
				$r['avg'] = count($prices1)!=1;
				$r['price'] = round( $totalprice/$day );

			}
			unset($r);
			$total = pdo_fetchcolumn($count_sql);
			//$total = pdo_fetchcolumn("select count(*) from ew_hotel2_room r left join ew_hotel2_room_price p on r.id = p.roomid ".$where);
			$page_array = get_page_array($total, $pindex, $psize);
			$data = array();
			$data['result'] = 1;

			ob_start();
			include $this->template('room_crumb');
			$data['code'] = ob_get_contents();
			ob_clean();

			$data['total'] = $total;
			$data['isshow'] = $page_array['isshow'];
			if ($page_array['isshow'] == 1) {
				$data['nindex'] = $page_array['nindex'];
			}
			die(json_encode($data));
		} else {
			$device = '';
			$reply['device'] = unserialize($reply['device']);
			if ($reply['device']) {
				foreach ($reply['device'] as $key => $value) {
					if ($value['isshow'] == 1) {
						$device .= $value['value'] . ' ';
					}
				}
			}

			include $this->template('detail');
		}
	}

	//获取房型信息
	public function doMobileroomdevice()
	{
		global $_GPC, $_W;
		$this->check_login();

		$id = $_GPC['id'];
		$hid = $_GPC['hid'];
		$has = $_GPC['has'];
		$price = $_GPC['price'];
		$total_price = $_GPC['total_price'];

		$search_array = $this->getSearchArray();

		$pricefield = $this->_user_info['isauto']==1?"cprice":"mprice";

		$data = array();
		if(empty($id) || empty($hid)) {
			$data['result'] = 0;
		} else {
			$sql = "SELECT *,$pricefield as roomprice ";
			$sql .= " FROM " .tablename('hotel2_room');
			$sql .= " WHERE id = :id AND hotelid = :hotelid AND status = 1";
			$sql .= " LIMIT 1";

			$params = array();
			$params[':hotelid'] = $hid;
			$params[':id'] = $id;
			$item = pdo_fetch($sql, $params);

			//计算价格
			//   //显示会员价还是普通价

			//入住
//            $bdate = $search_array['bdate'];
//            $btime = strtotime($bdate);
//
//            //住几天
//            $day =intval($search_array['day']);
//
//            //离店
//            $etime = $btime+86400;
//            $edate = date('Y-m-d',$etime) ;
//            $pricedays = pdo_fetchall("select $pricefield as price,roomdate from ew_hotel2_room_price where roomid={$item['id']} and roomdate>=$btime and roomdate<=$etime");
//
//
//            //找出$day天的价格记录
//            $totalprice =  0 ;
//            $prices = array();
//             $ts = array();
//             for($d=0;$d<$day;$d++){
//                    $t = $btime+ 86400 * $d;
//                    $p = $item['roomprice'];
//                    foreach($pricedays as $pd){
//                        if($pd['roomdate']==$t){
//                            $p = $pd['price'];
//                        }
//                    }
//                    $prices[] = $p;
//                    $totalprice+=$p;
//                }
//                //价格表的价格是否都相同
//                $prices1 = array_unique($prices);
//                $item['avg'] = count($prices1)!=1;
//                $item['price'] = round( $totalprice/$day );


			// 获取酒店电话
			if ($this->_set_info['is_unify'] == 1) {
				$tel = $this->_set_info['tel'];
			} else {
				$sql = 'SELECT `phone` FROM ' . tablename('hotel2') . ' WHERE id = :id ';
				$tel = pdo_fetchcolumn($sql, array(':id' => $hid));

			}
			$data['result'] = 1;

			ob_start();
			include $this->template('room_device');
			$data['code'] = ob_get_contents();
			ob_clean();
		}
		die(json_encode($data));
	}

	//获取酒店列表
	public function doMobilelist()
	{
		global $_GPC, $_W;
		$this->check_login();
		$search_array =$this->getSearchArray();
		if (!$search_array || empty($search_array['city_name'])) {
			$url = $this->createMobileUrl('index');
			header("Location: $url");
		}

		//0 默认推荐 1 价格排序
		$order_name = intval($search_array['order_name']);
		$order_type =intval( $search_array['order_type'] );
		$ac = $_GPC['ac'];
		if ($ac == "getDate") {
			$weid = $this->_weid;
			$price_type = $search_array['price_type'];
			$price_value = $search_array['price_value'];

			$data = array();
			$data['result'] = 1;
			$data['title'] = $search_array['city_name'];

			//入住
			$bdate = $search_array['bdate'];
			$btime = $search_array['btime'];

			//住几天
			$day =intval($search_array['day']);

			//离店
			$edate = $search_array['edate'];
			$etime = $search_array['etime'];

			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;

			$params = array(
				":weid"=>$weid
			);
			$pricefield = $this->_user_info['isauto']==1?"cprice":"mprice";

			$sql = "SELECT h.id, r.id as roomid, h.title, h.thumb, h.level, h.displayorder, r.title as style, " . $pricefield . " as m_price";
			$sql .= " FROM " .tablename('hotel2') ." AS h";
			$sql .= " right JOIN " .tablename('hotel2_room') ." AS r ON h.id = r.hotelid";
			$sql .= " WHERE 1 = 1";
			$sql .= " AND r.weid = :weid";
			$sql .= " AND h.status = 1 AND r.status = 1";

			//商圈
			if (!empty($search_array['business_id'])) {
				$sql .= " AND h.businessid =:businessid";
				$params[':businessid'] = $search_array['business_id'];
			}
			//品牌
			if (!empty($search_array['brand_id'])) {
				$sql .= " AND h.brandid = :brandid";
				$params[':brandid'] = $search_array['brand_id'];
			}
			//名称
			if (!empty($search_array['keyword'])) {
				$sql .= " AND h.title LIKE :keyword";
				$params[':keyword'] = "%{$search_array['keyword']}%";
			}
			//城市
			if (!empty($search_array['city_name'])) {
				if ($search_array['municipality'] == 1) {
					$sql .= " AND h.location_p =:city";
				} else {
					$sql .= " AND h.location_c =:city";
				}
				$params[':city'] = $search_array['city_name'];
			}
			//星级
			if ($price_type == 2) {
				if (!empty($price_value)) {
					$sql .= " AND h.level in( $price_value,". ($price_value + 10) .")";
				}
			}
			$room_list = pdo_fetchall($sql, $params);

			if (!$room_list) {
				$data['total'] = 0;
				$data['isshow'] = 0;
				die(json_encode($data));
			}

			$day = intval($search_array['day']);

			//循环房间列表
			foreach($room_list as $key => $value) {
				$r_sql = "SELECT count(id) as num, min(" . $pricefield . ") as m_price FROM " . tablename('hotel2_room_price') . " as p";
				$r_sql .= " WHERE 1 = 1";
				$r_sql .= " AND roomid = " . $value['roomid'];
				$r_sql .= " AND status = 1";
				$r_sql .= " AND roomdate >=" . $btime ." AND roomdate <" .$etime;
				$r_sql .= " AND num != 0";
				$r_price = pdo_fetch($r_sql);
				$min_price = intval($r_price['m_price']);
				$r_num = intval($r_price['num']);

				//如果价格表中设置了价格
				if ($r_num && !empty($min_price)) {
					if ($r_num == $day) {
						//如果选择的天数都设置了价格
						$room_list[$key]['m_price'] = $min_price;
					} else {
						//如果价格表存在更低的价格
						if ($min_price < $value['m_price']) {
							$room_list[$key]['m_price'] = $min_price;
						}
					}
				}
			}

			$hotel_list = array();
			foreach($room_list as $key => $value) {
				$hotelid = $value['id'];
				$roomid = $value['roomid'];
				$new_price = $value['m_price'];
				$price_flag = 1;

				//用户选择了价格区间
				if ($price_type == 1) {
					if (!empty($price_value)) {
						if (strstr($price_value, '-') !== false) {
							$price_array = explode("-", $price_value);
							if ($new_price >= intval($price_array[0]) && $new_price <= intval($price_array[1])) {
								$price_flag = 1;
							} else {
								$price_flag = 0;
							}
						} else {
							if ($price_value == 150) {
								if ($new_price <= 150) {
									$price_flag = 1;
								} else {
									$price_flag = 0;
								}
							}else if ($price_value == 1000) {
								if ($new_price >= 1000) {
									$price_flag = 1;
								} else {
									$price_flag = 0;
								}
							}
						}
					}
				}

				if ($price_flag == 0) {
					continue;
				}

				//取出酒店最低价放入数组中
				if (array_key_exists($hotelid, $hotel_list)) {
					$old_price = $hotel_list[$hotelid]['m_price'];
					if ($new_price < $old_price) {
						$hotel_list[$hotelid] = $value;
					}
				} else {
					$hotel_list[$hotelid] = $value;
				}
			}

			//排序
			switch ($order_name)
			{
				case 0:
					//优先级
					$hotel_list = array_sort($hotel_list, 'displayorder', 1);
					break;
				case 1:
					if ($order_type == 1) {
						//价格降序
						$hotel_list = array_sort($hotel_list, 'm_price', 1);
					} else {
						//价格升序
						$hotel_list = array_sort($hotel_list, 'm_price', 0);
					}
					break;
			}

			$total = count($hotel_list);

			if ($total <= $psize) {
				$list = $hotel_list;
			} else {
				// 需要分页
				if($pindex > 0) {
					$list_array = array_chunk($hotel_list, $psize);
					$list = $list_array[($pindex-1)];
					//print_r($list);exit;
				} else {
					$list = $hotel_list;
				}
			}

			$page_array = get_page_array($total, $pindex, $psize);

			ob_start();
			include $this->template('hotel_crumb');
			$data['code'] = ob_get_contents();
			ob_clean();

			$data['total'] = $total;
			$data['title'] .=  "(" . $total . ")";
			$data['isshow'] = $page_array['isshow'];
			if ($page_array['isshow'] == 1) {
				$data['nindex'] = $page_array['nindex'];
			}

			die(json_encode($data));
		} else {

			include $this->template('list');
		}
	}

	//订单列表
	public function doMobileorderlist()
	{
		global $_GPC, $_W;
		$weid = $this->_weid;
        checkauth();
		$this->check_login();
		$memberid = $this->_user_info['id'];
		if (empty($memberid)) {
			$url = $this->createMobileUrl('index');
			header("Location: $url");
		}
		$ac = $_GPC['ac'];
		if ($ac == "getDate") {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$sql = "SELECT o.*, h.title ";
			$where = " FROM " .tablename('hotel2_order') ." AS o";
			$where .= " LEFT JOIN " .tablename('hotel2') ." AS h ON o.hotelid = h.id";
			$where .= " WHERE 1 = 1";
			$where .= " AND o.memberid = $memberid";
			$where .= " AND o.weid = $weid";

			$count_sql = "SELECT COUNT(o.id) " . $where;
			$sql .= $where;
			$sql .= " ORDER BY o.id DESC";
			if($pindex > 0) {
				// 需要分页
				$start = ($pindex - 1) * $psize;
				$sql .= " LIMIT {$start},{$psize}";
			}
			$list = pdo_fetchall($sql);
			$total = pdo_fetchcolumn($count_sql);
            //print_r($count_sql);die();
			$page_array = get_page_array($total, $pindex, $psize);
			$data = array();
			$data['result'] = 1;
			ob_start();
			include $this->template('order_crumb');
			$data['code'] = ob_get_contents();
			ob_clean();

			$data['total'] = $total;
			$data['isshow'] = $page_array['isshow'];
			if ($page_array['isshow'] == 1) {
				$data['nindex'] = $page_array['nindex'];
			}
			die(json_encode($data));
		} else {
			include $this->template('orderlist');
		}
	}

	//订单详情
	public function doMobileorderdetail()
	{
		global $_GPC, $_W;
		$weid = $this->_weid;
		$id = $_GPC['id'];
		$this->check_login();
		if (empty($id)) {
			$url = $this->createMobileUrl('orderlist');
			header("Location: $url");
		}
		$memberid = $this->_user_info['id'];
		if (empty($memberid)) {
			$url = $this->createMobileUrl('index');
			header("Location: $url");
		}
		$sql = "SELECT o.*, h.title, h.address, h.phone, h.thumb";
		$sql .= " FROM " .tablename('hotel2_order') ." AS o";
		$sql .= " LEFT JOIN " .tablename('hotel2') ." AS h ON o.hotelid = h.id";
		$sql .= " WHERE 1 = 1";
		$sql .= " AND o.id = :id";
		$sql .= " AND o.memberid = :memberid";
		$sql .= " AND o.weid = :weid";

		$params = array();
		$params[':memberid'] = $memberid;
		$params[':weid'] = $weid;
		$params[':id'] = $id;
		$sql .= " LIMIT 1";
		$item = pdo_fetch($sql, $params);
		if ($this->_set_info['is_unify'] == 1) {
			$tel = $this->_set_info['tel'];
		} else {
			$tel = $item['phone'];
		}
		if(!empty($_W['member']['uid'])) {
			$member = mc_fetch($_W['member']['uid'], array('credit1', 'credit2'));
		}
		$params['module'] = "ewei_hotel";
		$params['ordersn'] = $item['ordersn'];
		$params['tid'] = $item['id'];
		$params['user'] = $_W['fans']['from_user'];
		$params['fee'] = $item['sum_price'];
		$params['title'] = $_W['account']['name'] . "酒店订单{$item['ordersn']}";

		// 设置分享信息
		$shareDesc = $item['address'];
		$shareThumb = tomedia($item['thumb']);

		include $this->template('orderdetail');
	}

	//检查用户是否登录
	public function check_login()
	{
		$check = check_hotel_user_login($this->_set_info);
		if ($check == 0) {
			$url = $this->createMobileUrl('index');
			header("Location: $url");
		} else {
			if(empty($this->_user_info)) {
				$weid = $this->_weid;
				$from_user = $this->_from_user;
				$user_info = pdo_fetch("SELECT * FROM " . tablename('hotel2_member') . " WHERE from_user = :from_user AND weid = :weid limit 1", array(':from_user' => $from_user, ':weid' => $weid));
				$this->_user_info = $user_info;
			}
		}
	}

	public function doMobileorderinfo()
	{
		global $_GPC, $_W;
		include $this->template('orderinfo');
	}
	public function doMobileorderpay(){
		global $_W,$_GPC;
		//立即支付
		$orderid = intval($_GPC['id']);
		$order = pdo_fetch("SELECT * FROM " . tablename('hotel2_order') . " WHERE id = :id", array(':id' => $orderid));
		if ($order['paystatus'] != '0' && $order['paytype'] != '1' && $order['paytype'] != '2') {
			message('抱歉，您的订单已付款或是被关闭！', $this->createMobileUrl('orderdetail',array("id"=>$order['id'])), 'error');
		}

		$params['ordersn'] = $order['ordersn'];
		$params['tid'] = $orderid;
		$params['user'] = $_W['fans']['from_user'];
		$params['fee'] = $order['sum_price'];
		$params['title'] = $_W['account']['name'] . "酒店订单{$order['ordersn']}";
		$this->pay($params);

	}

	public function payResult($params) {
		global $_GPC, $_W;
		$weid = $this->_weid;
		$sql = 'SELECT * FROM ' . tablename('hotel2_order') . ' WHERE `id` = :id AND `weid` = :weid';
		$order = pdo_fetch($sql, array(':id' => $params['tid'], ':weid' => $weid));
		pdo_update('hotel2_order', array('paystatus' => 1), array('id' => $params['tid']));

		$sql = 'SELECT `email`, `mobile` FROM ' . tablename('hotel2_set') . ' WHERE `weid` = :weid';
		$setInfo = pdo_fetch($sql, array(':weid' => $_W['uniacid']));

		if (!empty($setInfo['email']) || !empty($setInfo['mobile'])) {
			$body = "<h3>酒店订单</h3> <br />";
			$body .= '订单编号：' . $order['ordersn'] . '<br />';
			$body .= '姓名：' . $order['name'] . '<br />';
			$body .= '手机：' . $order['mobile'] . '<br />';
			$body .= '房型：' . $order['style'] . '<br />';
			$body .= '订购数量' . $order['nums'] . '<br />';
			$body .= '原价：' . $order['oprice']  . '<br />';
			$body .= '会员价：' . $order['mprice']  . '<br />';
			$body .= '入住日期：' . $order['btime']  . '<br />';
			$body .= '退房日期：' . $order['etime']  . '<br />';
			$body .= '总价:' . $order['sum_price'];

			// 发送邮件提醒
			if (!empty($setInfo['email'])) {
				load()->func('communication');
				ihttp_email($this->module['config']['noticeemail'], '微商城订单提醒', $body);
			}

			// 发送短信提醒
			if (!empty($setInfo['mobile'])) {
				load()->model('cloud');
				$body = str_replace(array('<h3>', '</h3>', '<br />'), array('', '', "\n"), $body);
				cloud_sms_send($this->module['config']['mobile'], $body);
			}
		}

		if ($params['from'] == 'return') {
			$roomid = $order['roomid'];
			$room = pdo_fetch("SELECT score FROM " . tablename('hotel2_room') . " WHERE id = {$roomid} AND weid = {$weid} LIMIT 1");
			$score = intval($room['score']);
			if ($score) {
				$from_user = $this->_from_user;
				pdo_fetch("UPDATE " . tablename('hotel2_member') . " SET score = (score + " . $score . ") WHERE from_user = '" . $from_user . "' AND weid = " . $weid . "");
				if ($_W['member']['uid'] > 0) {
					pdo_query("UPDATE " . tablename('mc_members') . " SET credit1 = (credit1 + " . $score . ") WHERE uid = '" . $_W['member']['uid'] . "' AND uniacid = " . $_W['uniacid'] . "");
				}
			}
			message('支付成功！', '../../app/' . $this->createMobileUrl('orderdetail', array("id" => $order['id'])), 'success');
		}
	}


	//用户注册
	public function doMobileregister()
	{
		global $_GPC, $_W;

		if (checksubmit()) {
			$weid = $this->_weid;
			$from_user = $this->_from_user;
			$set = $this->_set_info;

			$member = array();
			$member['from_user'] = $from_user;
			$member['username'] = $_GPC['username'];
			$member['password'] = $_GPC['password'];
			//print_r($_GPC);exit;

			if (!preg_match(REGULAR_USERNAME, $member['username'])) {
				die(json_encode(array("result" => 0, "error" => "必须输入用户名，格式为 3-15 位字符，可以包括汉字、字母（不区分大小写）、数字、下划线和句点。")));
			}

			// if (!preg_match(REGULAR_USERNAME, $member['from_user'])) {
			//    die(json_encode(array("result" => 0, "error" => "微信号码获取失败。")));
			//}

			if (hotel_member_check(array('from_user' => $member['from_user'], 'weid' => $weid))) {
				die(json_encode(array("result" => 0, "error" => "非常抱歉，此用微信号已经被注册，你可以直接使用注册时的用户名登录，或者更换微信号注册！")));
			}

			if (hotel_member_check(array('username' => $member['username'], 'weid' => $weid))) {
				die(json_encode(array("result" => 0, "error" => "非常抱歉，此用户名已经被注册，你需要更换注册用户名！")));
			}

			if (istrlen($member['password']) < 6) {
				die(json_encode(array("result" => 0, "error" => "必须输入密码，且密码长度不得低于6位。")));
			}
			$member['salt'] = random(8);
			$member['password'] = hotel_member_hash($member['password'], $member['salt']);

			$member['weid'] = $weid;
			$member['mobile'] = $_GPC['mobile'];
			$member['realname'] = $_GPC['realname'];
			$member['createtime'] = time();
			$member['status'] = 1;
			$member['isauto'] = 0;

			pdo_insert('hotel2_member', $member);

			$member['id'] = pdo_insertid();
			$member['user_set'] = $set['user'];

			//注册成功
			hotel_set_userinfo(1, $member);

			$url = $this->createMobileUrl('search');
			die(json_encode(array("result" => 1, "url" => $url)));
		} else {
			//$css_url = $this->_css_url;
			include $this->template('register');
		}
	}

	//错误信息提示页
	public function doMobileError()
	{
		global $_GPC, $_W;

		$msg = $_GPC['msg'];
		include $this->template('error');
	}

	public  function  doMobileAjaxdelete()
	{
		global $_GPC;
		$delurl = $_GPC['pic'];
		if(file_delete($delurl)) {
			echo 1;
		} else {
			echo 0;
		}
	}


	public function doWebHotel() {
		global $_GPC, $_W;

		$op = $_GPC['op'];
		$weid = $_W['uniacid'];
		$hotel_level_config = $this->_hotel_level_config;
		load()->func('tpl');

		if ($op == 'edit') {
			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				$insert = array(
					'weid' => $weid,
					'displayorder' => $_GPC['displayorder'],
					'title' => $_GPC['title'],
					'thumb'=>$_GPC['thumb'],
					'address' => $_GPC['address'],
					'location_p' => $_GPC['district']['province'],
					'location_c' => $_GPC['district']['city'],
					'location_a' => $_GPC['district']['district'],
					'lng' => $_GPC['baidumap']['lng'],
					'lat' => $_GPC['baidumap']['lat'],
					'phone' => $_GPC['phone'],
					'mail' => $_GPC['mail'],
					'description' => $_GPC['description'],
					'content' => $_GPC['content'],
					'traffic' => $_GPC['traffic'],
					'sales' => $_GPC['sales'],
					'level' => $_GPC['level'],
					'status' => $_GPC['status'],
					'brandid' => $_GPC['brandid'],
					'businessid' => $_GPC['businessid'],
				);

				if ($_GPC['device']) {
					$devices = array();
					foreach ($_GPC['device'] as $key => $device) {
						if ($device != '') {
							$devices[] = array('value' => $device, 'isshow' => intval($_GPC['show_device'][$key]));
						}
					}
					$insert['device'] = empty($devices) ? '' : iserializer($devices);
				}
				$insert['thumbs'] = empty($_GPC['thumbs']) ? '' : iserializer($_GPC['thumbs']);

				if (empty($id)) {
					pdo_insert('hotel2', $insert);
				} else {
					pdo_update('hotel2', $insert, array('id' => $id));
				}
				message("酒店信息保存成功!", $this->createWebUrl('hotel'), "success");
			}
			$sql = 'SELECT * FROM ' . tablename('hotel2') . ' WHERE `id` = :id';
			$item = pdo_fetch($sql, array(':id' => $id));
			if (empty($item['device'])) {
				$devices = array(
					array('isdel' => 0, 'value' => '有线上网'),
					array('isdel' => 0, 'isshow' => 0, 'value' => 'WIFI无线上网'),
					array('isdel' => 0, 'isshow' => 0, 'value' => '可提供早餐'),
					array('isdel' => 0, 'isshow' => 0, 'value' => '免费停车场'),
					array('isdel' => 0, 'isshow' => 0, 'value' => '会议室'),
					array('isdel' => 0, 'isshow' => 0, 'value' => '健身房'),
					array('isdel' => 0, 'isshow' => 0, 'value' => '游泳池')
				);
			} else {
				$devices = iunserializer($item['device']);
			}

			//品牌
			$sql = 'SELECT * FROM ' . tablename('hotel2_brand') . ' WHERE `weid` = :weid';
			$params = array(':weid' => $_W['uniacid']);
			$brands = pdo_fetchall($sql, $params);

			$sql = 'SELECT `title` FROM ' . tablename('hotel2_business') . ' WHERE `weid` = :weid AND `id` = :id';
			$params[':id'] = intval($item['businessid']);
			$item['hotelbusinesss'] = pdo_fetchcolumn($sql, $params);

			include $this->template('hotel_form');
		} else if ($op == 'delete') {

			$id = intval($_GPC['id']);

			if (!empty($id)) {
				$item = pdo_fetch("SELECT id FROM " . tablename('hotel2_order') . " WHERE hotelid = :hotelid LIMIT 1", array(':hotelid' => $id));
				if (!empty($item)) {
					message('抱歉，请先删除该酒店的订单,再删除该酒店！', '', 'error');
				}
			} else {
				message('抱歉，参数错误！', '', 'error');
			}

			pdo_delete("hotel2_order", array("hotelid" => $id));
			pdo_delete("hotel2_room", array("hotelid" => $id));
			pdo_delete("hotel2", array("id" => $id));

			message("酒店信息删除成功!", referer(), "success");
		} else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);

				if (!empty($id)) {
					$item = pdo_fetch("SELECT id FROM " . tablename('hotel2_order') . " WHERE hotelid = :hotelid LIMIT 1", array(':hotelid' => $id));
					if (!empty($item)) {
						message('抱歉，请先删除该酒店的订单,再删除该酒店！', '', 'error');
					}
				} else {
					message('抱歉，参数错误！', '', 'error');
				}

				pdo_delete("hotel2_order", array("hotelid" => $id));
				pdo_delete("hotel2_room", array("hotelid" => $id));
				pdo_delete("hotel2", array("id" => $id));
			}
			$this->web_message('酒店信息删除成功！', '', 0);
			exit();
		} else if ($op == 'showall') {
			if ($_GPC['show_name'] == 'showall') {
				$show_status = 1;
			} else {
				$show_status = 0;
			}

			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);

				if (!empty($id)) {
					pdo_update('hotel2', array('status' => $show_status), array('id' => $id));
				}
			}
			$this->web_message('操作成功！', '', 0);
			exit();
		} else if ($op == 'status') {

			$id = intval($_GPC['id']);
			if (empty($id)) {
				message('抱歉，传递的参数错误！', '', 'error');
			}
			$temp = pdo_update('hotel2', array('status' => $_GPC['status']), array('id' => $id));
			if ($temp == false) {
				message('抱歉，刚才操作数据失败！', '', 'error');
			} else {
				message('状态设置成功！', referer(), 'success');
			}
		} else if ($op == 'query') {
			$kwd = trim($_GPC['keyword']);
			$sql = 'SELECT id,title,description,thumb FROM ' . tablename('hotel2') . ' WHERE `weid`=:weid';
			$params = array();
			$params[':weid'] = $_W['uniacid'];
			if (!empty($kwd)) {
				$sql.=" AND `title` LIKE :title";
				$params[':title'] = "%{$kwd}%";
			}
			$ds = pdo_fetchall($sql, $params);
			foreach ($ds as &$value) {
				$value['thumb'] = tomedia($value['thumb']);
			}
			include $this->template('query');
		} else {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$where = ' WHERE `weid` = :weid';
			$params = array(':weid' => $_W['uniacid']);

			if (!empty($_GPC['title'])) {
				$where .= ' AND `title` LIKE :keywords';
				$params[':keywords'] = "%{$_GPC['title']}%";
			}
			if (!empty($_GPC['level'])) {
				$where .= ' AND level=:level';
				$params[':level'] = intval($_GPC['level']);
			}

			$sql = 'SELECT COUNT(*) FROM ' . tablename('hotel2') . $where;
			$total = pdo_fetchcolumn($sql, $params);

			if ($total > 0) {
				$pindex = max(1, intval($_GPC['page']));
				$psize = 10;

				$sql = 'SELECT * FROM ' . tablename('hotel2') . $where . ' ORDER BY `displayorder` DESC LIMIT ' .
						($pindex - 1) * $psize . ',' . $psize;
				$list = pdo_fetchall($sql, $params);
				foreach ($list as &$row) {
					$row['level'] = $this->_hotel_level_config[$row['level']];
				}

				$pager = pagination($total, $pindex, $psize);
			}

			if (!empty($_GPC['export'])) {
				/* 输入到CSV文件 */
				$html = "\xEF\xBB\xBF";

				/* 输出表头 */
				$filter = array(
					'title' => '酒店名称',
					'level' => '星级',
					'roomcount' => '房间数',
					'phone' => '电话',
					'status' => '状态',
				);

				foreach ($filter as $key => $value) {
					$html .= $value . "\t,";
				}
				$html .= "\n";

				if (!empty($list)) {
					$status = array('隐藏', '显示');
					foreach ($list as $key => $value) {
						foreach ($filter as $index => $title) {
							if ($index != 'status') {
								$html .= $value[$index] . "\t, ";
							} else {
								$html .= $status[$value[$index]] . "\t, ";
							}
						}
						$html .= "\n";
					}
				}

				/* 输出CSV文件 */
				header("Content-type:text/csv");
				header("Content-Disposition:attachment; filename=全部数据.csv");
				echo $html;
				exit();

			}

			include $this->template('hotel');
		}
	}



	public function doWebCopyroom() {
		global $_GPC, $_W;

		$hotelid = $_GPC['hotelid'];
		$roomid = $_GPC['roomid'];

		if (empty($hotelid) || empty($roomid)) {
			message('参数错误', 'refresh', 'error');
		}

		$item = pdo_fetch("SELECT * FROM " . tablename('hotel2_room') . " WHERE id = :id", array(':id' => $roomid));

		unset($item['id']);
		$item['status'] = 0;
		//$item['sortid'] = $roomid;

		pdo_insert('hotel2_room', $item);
		$id = pdo_insertid();
		$url = $this->createWebUrl('room', array('op' => 'edit', 'hotelid' => $hotelid, 'id' => $id));
		header("Location: $url");
		exit;
	}

	//批量修改房价
	public function doWebRoom_price() {
		global $_GPC, $_W;

		$hotelid = $_GPC['hotelid'];
		$weid = $_W['uniacid'];
		$ac = $_GPC['ac'];

		if ($ac == "getDate") {
			if (empty($_GPC['start']) || empty($_GPC['end'])) {
				die(json_encode(array("result" => 0, "error" => "请选择时间")));
			}
			$start = $_GPC['start'];
			$end = $_GPC['end'];
			$btime = strtotime($start);
			$etime = strtotime($end);
			//日期列
			$days = ceil(($etime - $btime) / 86400);
			$pagesize = 10;
			$totalpage = ceil($days / $pagesize);
			$page = intval($_GPC['page']);
			if ($page > $totalpage) {
				$page = $totalpage;
			} else if ($page <= 1) {
				$page = 1;
			}
			$currentindex = ($page - 1) * $pagesize;
			$start = date('Y-m-d', strtotime(date('Y-m-d') . "+$currentindex day"));
			$btime = strtotime($start);
			$etime = strtotime(date('Y-m-d', strtotime("$start +$pagesize day")));
			$date_array = array();
			$date_array[0]['date'] = $start;
			$date_array[0]['day'] = date('j', $btime);
			$date_array[0]['time'] = $btime;
			$date_array[0]['month'] = date('m', $btime);

			for ($i = 1; $i <= $pagesize; $i++) {
				$date_array[$i]['time'] = $date_array[$i - 1]['time'] + 86400;
				$date_array[$i]['date'] = date('Y-m-d', $date_array[$i]['time']);
				$date_array[$i]['day'] = date('j', $date_array[$i]['time']);
				$date_array[$i]['month'] = date('m', $date_array[$i]['time']);
			}
			$params = array();
			$sql = "SELECT r.* FROM " . tablename('hotel2_room') . "as r";
			$sql .= " WHERE 1 = 1";
			$sql .= " AND r.hotelid = $hotelid";
			$sql .= " AND r.weid = $weid";
			$list = pdo_fetchall($sql, $params);

			foreach ($list as $key => $value) {
				$sql = "SELECT * FROM " . tablename('hotel2_room_price');
				$sql .= " WHERE 1 = 1";
				$sql .= " AND roomid = " . $value['id'];
				$sql .= " AND roomdate >= " . $btime;
				$sql .= " AND roomdate < " . ($etime + 86400);
				$item = pdo_fetchall($sql);
				if ($item) {
					$flag = 1;
				} else {
					$flag = 0;
				}
				$list[$key]['price_list'] = array();

				if ($flag == 1) {
					for ($i = 0; $i <= $pagesize; $i++) {
						$k = $date_array[$i]['time'];
						foreach ($item as $p_key => $p_value) {
							//判断价格表中是否有当天的数据
							if ($p_value['roomdate'] == $k) {
								$list[$key]['price_list'][$k]['oprice'] = $p_value['oprice'];
								$list[$key]['price_list'][$k]['cprice'] = $p_value['cprice'];
								$list[$key]['price_list'][$k]['mprice'] = $p_value['mprice'];
								$list[$key]['price_list'][$k]['roomid'] = $value['id'];
								$list[$key]['price_list'][$k]['hotelid'] = $hotelid;
								$list[$key]['price_list'][$k]['has'] = 1;
								break;
							}
						}
						//价格表中没有当天数据
						if (empty($list[$key]['price_list'][$k]['oprice'])) {
							$list[$key]['price_list'][$k]['oprice'] = $value['oprice'];
							$list[$key]['price_list'][$k]['cprice'] = $value['cprice'];
							$list[$key]['price_list'][$k]['mprice'] = $value['mprice'];
							$list[$key]['price_list'][$k]['roomid'] = $value['id'];
							$list[$key]['price_list'][$k]['hotelid'] = $hotelid;
						}
					}
				} else {
					//价格表中没有数据
					for ($i = 0; $i <= $pagesize; $i++) {
						$k = $date_array[$i]['time'];
						$list[$key]['price_list'][$k]['oprice'] = $value['oprice'];
						$list[$key]['price_list'][$k]['cprice'] = $value['cprice'];
						$list[$key]['price_list'][$k]['mprice'] = $value['mprice'];
						$list[$key]['price_list'][$k]['roomid'] = $value['id'];
						$list[$key]['price_list'][$k]['hotelid'] = $hotelid;
					}
				}
			}
			$data = array();
			$data['result'] = 1;
			ob_start();
			include $this->template('room_price_list');
			$data['code'] = ob_get_contents();
			ob_clean();
			die(json_encode($data));
		} else if ($ac == 'submitPrice') {  //修改价格
			$hotelid = intval($_GPC['hotelid']);
			$roomid = intval($_GPC['roomid']);
			$price = $_GPC['price'];
			$pricetype = $_GPC['pricetype'];
			$date = $_GPC['date'];
			$roomprice = $this->getRoomPrice($hotelid, $roomid, $date);
			$roomprice[$pricetype] = $price;
			if (empty($roomprice['id'])) {
				pdo_insert("hotel2_room_price", $roomprice);
			} else {
				pdo_update("hotel2_room_price", $roomprice, array("id" => $roomprice['id']));
			}
			die(json_encode(array("result" => 1, "hotelid" => $hotelid, "roomid" => $roomid, "pricetype" => $pricetype, "price" => $price)));
		} else if ($ac == 'updatelot') {
			//批量修改房价
			$startime = time();
			$firstday = date('Y-m-01', time());
			//当月最后一天
			$endtime = strtotime(date('Y-m-d', strtotime("$firstday +1 month -1 day")));
			$rooms = pdo_fetchall("select * from " . tablename("hotel2_room") . " where hotelid=" . $hotelid);
			include $this->template('room_price_lot');
			exit();
		} else if ($ac == 'updatelot_create') {
			$rooms = $_GPC['rooms'];
			if (empty($rooms)) {
				die("");
			}
			$days = $_GPC['days'];
			$days_arr = implode(",", $days);
			$rooms_arr = implode(",", $rooms);
			$start = $_GPC['start'];
			$end = $_GPC['end'];
			$list = pdo_fetchall("select * from " . tablename("hotel2_room") . " where id in (" . implode(",", $rooms) . ")");
			ob_start();
			include $this->template('room_price_lot_list');
			$data['result'] = 1;
			$data['code'] = ob_get_contents();
			ob_clean();
			die(json_encode($data));
		} else if ($ac == 'updatelot_submit') {
			$rooms = $_GPC['rooms'];
			$rooms_arr = explode(",", $rooms);
			$days = $_GPC['days'];
			$days_arr = explode(",", $days);
			$oprices = $_GPC['oprice'];
			$cprices = $_GPC['cprice'];
			$mprices = $_GPC['mprice'];
			$start = strtotime($_GPC['start']);
			$end = strtotime($_GPC['end']);
			foreach ($rooms_arr as $v) {
				for ($time = $start; $time <= $end; $time+=86400) {
					$week = date('w', $time);
					if (in_array($week, $days_arr)) {
						$roomprice = $this->getRoomPrice($hotelid, $v, date('Y-m-d', $time));
						$roomprice['oprice'] = $oprices[$v];
						$roomprice['cprice'] = $cprices[$v];
						$roomprice['mprice'] = $mprices[$v];
						if (empty($roomprice['id'])) {
							pdo_insert("hotel2_room_price", $roomprice);
						} else {
							pdo_update("hotel2_room_price", $roomprice, array("id" => $roomprice['id']));
						}
					}
				}
			}
			message("批量修改房价成功!", $this->createWebUrl('room_price', array("hotelid" => $hotelid)), "success");
		}
		$startime = time();
		$firstday = date('Y-m-01', time());
		//当月最后一天
		$endtime = strtotime(date('Y-m-d', strtotime("$firstday +1 month -1 day")));
		include $this->template('room_price');
	}

	//批量修改房价
	public function doWebRoom_status() {
		global $_GPC, $_W;

		$hotelid = $_GPC['hotelid'];
		$weid = $_W['uniacid'];

		$ac = $_GPC['ac'];
		if ($ac == "getDate") {
			if (empty($_GPC['start']) || empty($_GPC['end'])) {
				die(json_encode(array("result" => 0, "error" => "请选择时间")));
			}

			$btime = strtotime($_GPC['start']);
			$etime = strtotime($_GPC['end']);
			// 日期列
			$days = ceil(($etime - $btime) / 86400);

			$pagesize = 10;
			$totalpage = ceil($days / $pagesize);
			$page = intval($_GPC['page']);
			if ($page > $totalpage) {
				$page = $totalpage;
			} else if ($page <= 1) {
				$page = 1;
			}
			$currentindex = ($page - 1) * $pagesize;
			$start = date('Y-m-d', strtotime(date('Y-m-d') . "+$currentindex day"));

			$btime = strtotime($start);
			$etime = strtotime(date('Y-m-d', strtotime("$start +$pagesize day")));
			$date_array = array();
			$date_array[0]['date'] = $start;
			$date_array[0]['day'] = date('j', $btime);
			$date_array[0]['time'] = $btime;
			$date_array[0]['month'] = date('m', $btime);

			for ($i = 1; $i <= $pagesize; $i++) {
				$date_array[$i]['time'] = $date_array[$i - 1]['time'] + 86400;
				$date_array[$i]['date'] = date('Y-m-d', $date_array[$i]['time']);
				$date_array[$i]['day'] = date('j', $date_array[$i]['time']);
				$date_array[$i]['month'] = date('m', $date_array[$i]['time']);
			}

			$params = array();
			$sql = "SELECT r.* FROM " . tablename('hotel2_room') . "as r";
			$sql .= " WHERE 1 = 1";
			$sql .= " AND r.hotelid = $hotelid";
			$sql .= " AND r.weid = $weid";

			$list = pdo_fetchall($sql, $params);

			foreach ($list as $key => $value) {
				$sql = "SELECT * FROM " . tablename('hotel2_room_price');
				$sql .= " WHERE 1 = 1";
				$sql .= " AND roomid = " . $value['id'];
				$sql .= " AND roomdate >= " . $btime;
				$sql .= " AND roomdate < " . ($etime + 86400);

				$item = pdo_fetchall($sql);

				if ($item) {
					$flag = 1;
				} else {
					$flag = 0;
				}
				$list[$key]['price_list'] = array();
				if ($flag == 1) {
					for ($i = 0; $i <= $pagesize; $i++) {
						$k = $date_array[$i]['time'];

						foreach ($item as $p_key => $p_value) {
							//判断价格表中是否有当天的数据
							if ($p_value['roomdate'] == $k) {

								$list[$key]['price_list'][$k]['status'] = $p_value['status'];
								if (empty($p_value['num'])) {
									$list[$key]['price_list'][$k]['num'] = "不限";
								} else if ($p_value['num'] == -1) {
									$list[$key]['price_list'][$k]['num'] = "不限";
								} else {
									$list[$key]['price_list'][$k]['num'] = $p_value['num'];
								}
								$list[$key]['price_list'][$k]['roomid'] = $value['id'];
								$list[$key]['price_list'][$k]['hotelid'] = $hotelid;
								$list[$key]['price_list'][$k]['has'] = 1;
								break;
							}
						}
						//价格表中没有当天数据
						if (empty($list[$key]['price_list'][$k])) {
							$list[$key]['price_list'][$k]['num'] = "不限";
							$list[$key]['price_list'][$k]['status'] = 1;
							$list[$key]['price_list'][$k]['roomid'] = $value['id'];
							$list[$key]['price_list'][$k]['hotelid'] = $hotelid;
						}
					}
				} else {
					//价格表中没有数据
					for ($i = 0; $i <= $pagesize; $i++) {
						$k = $date_array[$i]['time'];
						$list[$key]['price_list'][$k]['num'] = "不限";
						$list[$key]['price_list'][$k]['status'] = 1;
						$list[$key]['price_list'][$k]['roomid'] = $value['id'];
						$list[$key]['price_list'][$k]['hotelid'] = $hotelid;
					}
				}
			}

			$data = array();
			$data['result'] = 1;

			ob_start();
			include $this->template('room_status_list');
			$data['code'] = ob_get_contents();
			ob_clean();

			die(json_encode($data));
		} else if ($ac == 'submitPrice') {  //修改价格
			$hotelid = intval($_GPC['hotelid']);
			$roomid = intval($_GPC['roomid']);
			$price = $_GPC['price'];
			$pricetype = $_GPC['pricetype'];
			$date = $_GPC['date'];
			$roomprice = $this->getRoomPrice($hotelid, $roomid, $date);
			if ($pricetype == 'num') {
				$roomprice['num'] = $_GPC['price'];
			} else {
				$roomprice['status'] = $_GPC['status'];
			}

			if (empty($roomprice['id'])) {
				pdo_insert("hotel2_room_price", $roomprice);
			} else {
				pdo_update("hotel2_room_price", $roomprice, array("id" => $roomprice['id']));
			}
			die(json_encode(array("result" => 1, "hotelid" => $hotelid, "roomid" => $roomid, "pricetype" => $pricetype, "price" => $price)));
		} else if ($ac == 'updatelot') {
			//批量修改房价
			$startime = time();
			$firstday = date('Y-m-01', time());
			//当月最后一天
			$endtime = strtotime(date('Y-m-d', strtotime("$firstday +1 month -1 day")));
			$rooms = pdo_fetchall("select * from " . tablename("hotel2_room") . " where hotelid=" . $hotelid);
			include $this->template('room_status_lot');
			exit();
		} else if ($ac == 'updatelot_create') {
			$rooms = $_GPC['rooms'];
			if (empty($rooms)) {
				die("");
			}
			$days = $_GPC['days'];
			$days_arr = implode(",", $days);
			$rooms_arr = implode(",", $rooms);
			$start = $_GPC['start'];
			$end = $_GPC['end'];
			$list = pdo_fetchall("select * from " . tablename("hotel2_room") . " where id in (" . implode(",", $rooms) . ")");
			ob_start();
			include $this->template('room_status_lot_list');
			$data['result'] = 1;
			$data['code'] = ob_get_contents();
			ob_clean();
			die(json_encode($data));
		} else if ($ac == 'updatelot_submit') {
			$rooms = $_GPC['rooms'];
			$rooms_arr = explode(",", $rooms);
			$days = $_GPC['days'];
			$days_arr = explode(",", $days);
			$nums = $_GPC['num'];
			$statuses = $_GPC['status'];
			$start = strtotime($_GPC['start']);
			$end = strtotime($_GPC['end']);
			foreach ($rooms_arr as $v) {
				for ($time = $start; $time <= $end; $time+=86400) {
					$week = date('w', $time);
					if (in_array($week, $days_arr)) {
						$roomprice = $this->getRoomPrice($hotelid, $v, date('Y-m-d', $time));
						$roomprice['num'] = empty($nums[$v]) ? '-1' : intval($nums[$v]);
						$roomprice['status'] = $statuses[$v];
						if (empty($roomprice['id'])) {
							pdo_insert("hotel2_room_price", $roomprice);
						} else {
							pdo_update("hotel2_room_price", $roomprice, array("id" => $roomprice['id']));
						}
					}
				}
			}
			message("批量修改房量房态成功!", $this->createWebUrl('room_status', array("hotelid" => $hotelid)), "success");
		}

		$startime = time();
		$firstday = date('Y-m-01', time());
		//当月最后一天
		$endtime = strtotime(date('Y-m-d', strtotime("$firstday +1 month -1 day")));
		include $this->template('room_status');
	}

	//获取房型某天的记录
	private function getRoomPrice($hotelid, $roomid, $date) {
		global $_W;
		$btime = strtotime($date);
		$sql = "SELECT * FROM " . tablename('hotel2_room_price');
		$sql .= " WHERE 1 = 1";
		$sql .=" and weid=" . $_W['uniacid'];
		$sql .= " AND hotelid = " . $hotelid;
		$sql .= " AND roomid = " . $roomid;
		$sql .= " AND roomdate = " . $btime;
		$sql .=" limit 1";
		$roomprice = pdo_fetch($sql);

		if (empty($roomprice)) {
			$room = $this->getRoom($hotelid, $roomid);
			$roomprice = array(
				"weid" => $_W['uniacid'],
				"hotelid" => $hotelid,
				"roomid" => $roomid,
				"oprice" => $room['oprice'],
				"cprice" => $room['cprice'],
				"mprice" => $room['mprice'],
				"status" => $room['status'],
				"roomdate" => strtotime($date),
				"thisdate" => $date,
				"num" => "-1",
				"status" => 1,
			);
		}
		return $roomprice;
	}

	private function getRoom($hotelid, $roomid) {
		$sql = "SELECT * FROM " . tablename('hotel2_room');
		$sql .= " WHERE 1 = 1";
		$sql .= " AND hotelid = " . $hotelid;
		$sql .= " AND id = " . $roomid;
		$sql .=" limit 1";
		return pdo_fetch($sql);
	}

	public function doWebRoom() {
		global $_GPC, $_W;
		$op = $_GPC['op'];
		if ($op == 'edit') {
			$id = intval($_GPC['id']);
			$hotelid = intval($_GPC['hotelid']);
			$hotel = pdo_fetch("select id,title from " . tablename('hotel2') . "where id=:id limit 1", array(":id" => $hotelid));
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('hotel2_room') . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，房型不存在或是已经删除！', '', 'error');
				}
				$piclist = unserialize($item['thumbs']);
			}

			if (checksubmit('submit')) {
				if (empty($_GPC['title'])) {
					message('请输入房型！');
				}
				$data = array(
					'weid' => $_W['uniacid'],
					'hotelid' => $hotelid,
					'title' => $_GPC['title'],
					'thumb'=>$_GPC['thumb'],
					'breakfast' => $_GPC['breakfast'],
					'oprice' => $_GPC['oprice'],
					'cprice' => $_GPC['cprice'],
					'mprice' => $_GPC['mprice'],
					'area' => $_GPC['area'],
					'area_show' => $_GPC['area_show'],
					'bed' => $_GPC['bed'],
					'bed_show' => $_GPC['bed_show'],
					'bedadd' => $_GPC['bedadd'],
					'bedadd_show' => $_GPC['bedadd_show'],
					'persons' => $_GPC['persons'],
					'persons_show' => $_GPC['persons_show'],
					'sales' => $_GPC['sales'],
					'device' => $_GPC['device'],
					'floor' => $_GPC['floor'],
					'floor_show' => $_GPC['floor_show'],
					'smoke' => $_GPC['smoke'],
					'smoke_show' => $_GPC['smoke_show'],
					'score' => intval($_GPC['score']),
					'status' => $_GPC['status'],
				);
				if(is_array($_GPC['thumbs'])){
					$data['thumbs'] = serialize($_GPC['thumbs']);
				} else {
					$data['thumbs'] = serialize(array());
				}
				if (empty($id)) {
					pdo_insert('hotel2_room', $data);
				} else {
					pdo_update('hotel2_room', $data, array('id' => $id));
				}
				pdo_query("update " . tablename('hotel2') . " set roomcount=(select count(*) from " . tablename('hotel2_room') . " where hotelid=:hotelid) where id=:hotelid", array(":hotelid" => $hotelid));
				message('房型信息更新成功！', $this->createWebUrl('room'), 'success');
			}
			include $this->template('room_form');
		} else if ($op == 'delete') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT id FROM " . tablename('hotel2_order') . " WHERE roomid = :roomid LIMIT 1", array(':roomid' => $id));
				if (!empty($item)) {
					message('抱歉，请先删除该房间的订单,再删除该房间！', '', 'error');
				}
			} else {
				message('抱歉，参数错误！', '', 'error');
			}
			pdo_delete('hotel2_room', array('id' => $id));
			pdo_delete('hotel2_order', array('roomid' => $id));
			pdo_query("update " . tablename('hotel2') . " set roomcount=(select count(*) from " . tablename('hotel2_room') . " where hotelid=:hotelid) where id=:hotelid", array(":hotelid" => $id));
			message('删除成功！', referer(), 'success');
		} else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				if (!empty($id)) {
					$item = pdo_fetch("SELECT id FROM " . tablename('hotel2_order') . " WHERE roomid = :roomid LIMIT 1", array(':roomid' => $id));
					if (!empty($item)) {
						$this->web_message('抱歉，请先删除该房间的订单,再删除该房间！', '', 'error');
					}
				} else {
					$this->web_message('抱歉，参数错误！', '', 'error');
				}
				pdo_delete('hotel2_room', array('id' => $id));
				pdo_delete('hotel2_order', array('roomid' => $id));
				pdo_query("update " . tablename('hotel2') . " set roomcount=(select count(*) from " . tablename('hotel2_room') . " where hotelid=:hotelid) where id=:hotelid", array(":hotelid" => $id));
			}
			$this->web_message('删除成功！', '', 0);
			exit();
		} else if ($op == 'showall') {
			if ($_GPC['show_name'] == 'showall') {
				$show_status = 1;
			} else {
				$show_status = 0;
			}
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				if (!empty($id)) {
					pdo_update('hotel2_room', array('status' => $show_status), array('id' => $id));
				}
			}
			$this->web_message('操作成功！', '', 0);
			exit();
		} else if ($op == 'status') {
			$id = intval($_GPC['id']);
			if (empty($id)) {
				message('抱歉，传递的参数错误！', '', 'error');
			}
			$temp = pdo_update('hotel2_room', array('status' => $_GPC['status']), array('id' => $id));
			if ($temp == false) {
				message('抱歉，刚才操作数据失败！', '', 'error');
			} else {
				message('状态设置成功！', referer(), 'success');
			}
		} else {
			$hotelid = intval($_GPC['hotelid']);
			$hotel = pdo_fetch("select title from " . tablename('hotel2') . "where id=:id limit 1", array(":id" => $hotelid));
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$sql = "";
			$params = array();
			if (!empty($_GPC['title'])) {
				$sql .= ' AND `title` LIKE :keywords';
				$params[':keywords'] = "%{$_GPC['title']}%";
			}
			if (!empty($hotelid)) {
				$sql.=' and r.hotelid=:hotelid';
				$params[':hotelid'] = $hotelid;
			}
			if (!empty($_GPC['title'])) {
				$sql .= ' AND r.title LIKE :keywords';
				$params[':keywords'] = "%{$_GPC['title']}%";
			}
			if (!empty($_GPC['hoteltitle'])) {
				$sql .= ' AND h.title LIKE :keywords';
				$params[':keywords'] = "%{$_GPC['hoteltitle']}%";
			}
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("SELECT r.*,h.title as hoteltitle FROM " . tablename('hotel2_room') . " r left join " . tablename('hotel2') . " h on r.hotelid = h.id WHERE r.weid = '{$_W['uniacid']}' $sql ORDER BY h.id, r.displayorder, r.sortid DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hotel2_room') . " r left join " . tablename('hotel2') . " h on r.hotelid = h.id WHERE r.weid = '{$_W['uniacid']}' $sql", $params);
			$pager = pagination($total, $pindex, $psize);
			include $this->template('room');
		}
	}

	public function web_message($error, $url = '', $errno = -1) {
		$data = array();
		$data['errno'] = $errno;
		if (!empty($url)) {
			$data['url'] = $url;
		}
		$data['error'] = $error;
		echo json_encode($data);
		exit;
	}

	public function doWebOrder() {
		global $_GPC, $_W;
		checklogin();
		$hotelid = intval($_GPC['hotelid']);
		$hotel = pdo_fetch("select id,title from " . tablename('hotel2') . " where id=:id limit 1", array(":id" => $hotelid));
		$roomid = intval($_GPC['roomid']);
		$room = pdo_fetch("select id,title from " . tablename('hotel2_room') . " where id=:id limit 1", array(":id" => $roomid));

		$op = $_GPC['op'];
		if ($op == 'edit') {
			$id = $_GPC['id'];
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('hotel2_order') . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，订单不存在或是已经删除！', '', 'error');
				}
			}
			if (checksubmit('submit')) {
				$old_status = $_GPC['old_status'];

				$data = array(
					'status' => $_GPC['status'],
					'msg' => $_GPC['msg'],
					'mngtime' => time(),
				);

				$params = array();
				$sql = "SELECT id, roomdate, num FROM " . tablename('hotel2_room_price');
				$sql .= " WHERE 1 = 1";
				$sql .= " AND roomid = :roomid";
				$sql .= " AND roomdate >= :btime AND roomdate < :etime";
				$sql .= " AND status = 1";

				$params[':roomid'] = $item['roomid'];
				$params[':btime'] = $item['btime'];
				$params[':etime'] = $item['etime'];

				//订单确认
				if ($data['status'] == 1 && $old_status != 1) {
					$room_date_list = pdo_fetchall($sql, $params);
					if ($room_date_list) {
						//$change_data = array();

						foreach ($room_date_list as $key => $value) {
							$num = $value['num'];
							if ($num > 0) {
								if ($num > $item['nums']) {
									$now_num = $num - $item['nums'];
								} else {
									$now_num = 0;
								}
								pdo_update('hotel2_room_price', array('num' => $now_num), array('id' => $value['id']));
							}
						}
					}
				}

				//订单取消
				//print_r($old_status . '=>' . $data['status']); exit;
				if ($old_status == 1 && ($data['status'] == -1 || $data['status'] == 2)) {
					$room_date_list = pdo_fetchall($sql, $params);
					if ($room_date_list) {
						foreach ($room_date_list as $key => $value) {
							$num = $value['num'];
							if ($num >= 0) {
								$now_num = $num + $item['nums'];
								pdo_update('hotel2_room_price', array('num' => $now_num), array('id' => $value['id']));
							}
						}
					}
				}
				pdo_update('hotel2_order', $data, array('id' => $id));
				message('订单信息处理完成！', $this->createWebUrl('order', array('hotelid' => $hotelid, "roomid" => $roomid)), 'success');
			}

			$btime = $item['btime'];
			$etime = $item['etime'];

			$start = date('m-d', $btime);
			$end = date('m-d', $etime);

			//日期列
			$days = ceil(($etime - $btime) / 86400);

			//print_r($days);exit;

			$date_array = array();
			$date_array[0]['date'] = $start;
			$date_array[0]['day'] = date('j', $btime);
			$date_array[0]['time'] = $btime;
			$date_array[0]['month'] = date('m', $btime);

			if ($days > 1) {
				for ($i = 1; $i < $days; $i++) {
					$date_array[$i]['time'] = $date_array[$i - 1]['time'] + 86400;
					$date_array[$i]['date'] = date('Y-m-d', $date_array[$i]['time']);
					$date_array[$i]['day'] = date('j', $date_array[$i]['time']);
					$date_array[$i]['month'] = date('m', $date_array[$i]['time']);
				}
			}

			//print_r($date_array);exit;

			$sql = "SELECT id, roomdate, num, status FROM " . tablename('hotel2_room_price');
			$sql .= " WHERE 1 = 1";
			$sql .= " AND roomid = :roomid";
			$sql .= " AND roomdate >= :btime AND roomdate < :etime";
			$sql .= " AND status = 1";

			$params[':roomid'] = $item['roomid'];
			$params[':btime'] = $item['btime'];
			$params[':etime'] = $item['etime'];

			$room_date_list = pdo_fetchall($sql, $params);

			if ($room_date_list) {
				$flag = 1;
			} else {
				$flag = 0;
			}
			$list = array();

			if ($flag == 1) {
				for ($i = 0; $i < $days; $i++) {
					$k = $date_array[$i]['time'];

					foreach ($room_date_list as $p_key => $p_value) {
						//判断价格表中是否有当天的数据
						if ($p_value['roomdate'] == $k) {
							$list[$k]['status'] = $p_value['status'];
							if (empty($p_value['num'])) {
								$list[$k]['num'] = 0;
							} else if ($p_value['num'] == -1) {
								$list[$k]['num'] = "不限";
							} else {
								$list[$k]['num'] = $p_value['num'];
							}
							$list[$k]['has'] = 1;
							break;
						}
					}
					//价格表中没有当天数据
					if (empty($list[$k])) {
						$list[$k]['num'] = "不限";
						$list[$k]['status'] = 1;
					}
				}
			} else {
				//价格表中没有数据
				for ($i = 0; $i < $days; $i++) {
					$k = $date_array[$i]['time'];
					$list[$k]['num'] = "不限";
					$list[$k]['status'] = 1;
				}
			}

			$member_info = pdo_fetch("SELECT from_user,isauto FROM " . tablename('hotel2_member') . " WHERE id = :id LIMIT 1", array(':id' => $item['memberid']));

			include $this->template('order_form');
		} else if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$item = pdo_fetch("SELECT id FROM " . tablename('hotel2_order') . " WHERE id = :id LIMIT 1", array(':id' => $id));

			if (empty($item)) {
				message('抱歉，订单不存在或是已经删除！', '', 'error');
			}
			pdo_delete('hotel2_order', array('id' => $id));
			message('删除成功！', referer(), 'success');
		} elseif($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				pdo_delete('hotel2_order', array('id' => $id));
			}
			$this->web_message('删除成功！', '', 0);
			exit();
		}
		else {

			$weid = $_W['uniacid'];
			$realname = $_GPC['realname'];
			$mobile = $_GPC['mobile'];
			$ordersn = $_GPC['ordersn'];
			$roomtitle = $_GPC['roomtitle'];
			$hoteltitle = $_GPC['hoteltitle'];
			$condition = '';
			$params = array();
			if (!empty($hoteltitle)) {
				$condition .= ' AND h.title LIKE :hoteltitle';
				$params[':hoteltitle'] = "%{$hoteltitle}%";
			}
			if (!empty($roomtitle)) {
				$condition .= ' AND r.title LIKE :roomtitle';
				$params[':roomtitle'] = "%{$roomtitle}%";
			}

			if (!empty($realname)) {
				$condition .= ' AND o.name LIKE :realname';
				$params[':realname'] = "%{$realname}%";
			}
			if (!empty($mobile)) {
				$condition .= ' AND o.mobile LIKE :mobile';
				$params[':mobile'] = "%{$mobile}%";
			}
			if (!empty($ordersn)) {
				$condition .= ' AND o.ordersn LIKE :ordersn';
				$params[':ordersn'] = "%{$ordersn}%";
			}
			if (!empty($hotelid)) {
				$condition.=" and o.hotelid=" . $hotelid;
			}
			if (!empty($roomid)) {
				$condition.=" and o.roomid=" . $roomid;
			}
			$status = $_GPC['status'];
			if ($status != '') {
				$condition.=" and o.status=" . intval($status);
			}
			$paystatus = $_GPC['paystatus'];
			if ($paystatus != '') {
				$condition.=" and o.paystatus=" . intval($paystatus);
			}

			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("SELECT o.*,h.title as hoteltitle,r.title as roomtitle FROM " . tablename('hotel2_order') . " o left join " . tablename('hotel2') .
				"h on o.hotelid=h.id left join " . tablename("hotel2_room") . " r on r.id = o.roomid  WHERE o.weid = '{$_W['uniacid']}' $condition ORDER BY o.id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM  ' . tablename('hotel2_order') . " o left join " . tablename('hotel2') .
				"h on o.hotelid=h.id left join " . tablename("hotel2_room") . " r on r.id = o.roomid  WHERE o.weid = '{$_W['uniacid']}' $condition", $params);

			$pager = pagination($total, $pindex, $psize);
			include $this->template('order');
		}
	}

	public function doWebMember() {
		global $_GPC, $_W;
		$op = $_GPC['op'];
		if ($op == 'edit') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('hotel2_member') . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，用户不存在或是已经删除！', '', 'error');
				}
			}
			if (checksubmit('submit')) {
				$data = array(
					'weid' => $_W['uniacid'],
					'username' => $_GPC['username'],
					'realname' => $_GPC['realname'],
					'mobile' => $_GPC['mobile'],
					'score' => $_GPC['score'],
					'userbind' => $_GPC['userbind'],
					'isauto' => $_GPC['isauto'],
					'status' => $_GPC['status'],
				);
				if (!empty($_GPC['password'])) {
					$data['salt'] = random(8);
					$data['password'] = hotel_member_hash($_GPC['password'], $data['salt']);
					//$data['password'] = md5($_GPC['password']);
				}

				if (empty($id)) {
					$c = pdo_fetchcolumn("select count(*) from " . tablename('hotel2_member') . " where username=:username ", array(":username" => $data['username']));
					if ($c > 0) {
						message("用户名 " . $data['username'] . " 已经存在!", "", "error");
					}
					$data['createtime'] = time();
					pdo_insert('hotel2_member', $data);
				} else {
					pdo_update('hotel2_member', $data, array('id' => $id));
				}
				message('用户信息更新成功！', $this->createWebUrl('member'), 'success');
			}
			include $this->template('member_form');
		} else if ($op == 'delete') {
			$id = intval($_GPC['id']);
			pdo_delete('hotel2_member', array('id' => $id));
			pdo_delete('hotel2_order', array('memberid' => $id));
			message('删除成功！', referer(), 'success');
		} else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				pdo_delete('hotel2_member', array('id' => $id));
				pdo_delete('hotel2_order', array('memberid' => $id));
			}
			$this->web_message('规则操作成功！', '', 0);
			exit();
		} else if ($op == 'showall') {
			if ($_GPC['show_name'] == 'showall') {
				$show_status = 1;
			} else {
				$show_status = 0;
			}
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				if (!empty($id)) {
					pdo_update('hotel2_member', array('status' => $show_status), array('id' => $id));
				}
			}
			$this->web_message('操作成功！', '', 0);
			exit();
		} else if ($op == 'status') {
			$id = intval($_GPC['id']);
			if (empty($id)) {
				message('抱歉，传递的参数错误！', '', 'error');
			}
			$temp = pdo_update('hotel2_member', array('status' => $_GPC['status']), array('id' => $id));

			if ($temp == false) {
				message('抱歉，刚才操作数据失败！', '', 'error');
			} else {
				message('状态设置成功！', referer(), 'success');
			}
		} else {
			$sql = "";
			$params = array();
			if (!empty($_GPC['realname'])) {
				$sql .= ' AND `realname` LIKE :realname';
				$params[':realname'] = "%{$_GPC['realname']}%";
			}
			if (!empty($_GPC['mobile'])) {
				$sql .= ' AND `mobile` LIKE :mobile';
				$params[':mobile'] = "%{$_GPC['mobile']}%";
			}
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("SELECT * FROM " . tablename('hotel2_member') . " WHERE weid = '{$_W['uniacid']}' $sql ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hotel2_member') . " WHERE weid = '{$_W['uniacid']}' $sql", $params);
			$pager = pagination($total, $pindex, $psize);
			include $this->template('member');
		}
	}

	public function doWebhotelset() {
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		if (checksubmit('submit')) {
			$data = array(
				'weid' => $_W['uniacid'],
				'location_p' => $_GPC['district']['province'],
				'location_c' => $_GPC['district']['city'],
				'location_a' => $_GPC['district']['district'],
				'version' => $_GPC['version'],
				'user' => $_GPC['user'],
				'reg' => $_GPC['reg'],
				'regcontent' => $_GPC['regcontent'],
				'bind' => $_GPC['bind'],
				'ordertype' => $_GPC['ordertype'],
				'paytype1' => $_GPC['paytype1'],
				'paytype2' => $_GPC['paytype2'],
				'paytype3' => $_GPC['paytype3'],
				'is_unify' => $_GPC['is_unify'],
				'tel' => $_GPC['tel'],
				'email' => $_GPC['email'],
				'mobile' => $_GPC['mobile'],
			);
			if (!empty($id)) {
				pdo_update("hotel2_set", $data, array("id" => $id));
			} else {
				pdo_insert("hotel2_set", $data);
			}
			message("保存设置成功!", referer(), "success");
		}

		$sql = 'SELECT * FROM ' . tablename('hotel2_set') . ' WHERE `weid` = :weid';
		$set = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
		if (empty($set)) {
			$set = array('user' => 1, 'reg' => 1, 'bind' => 1);
		}
		include $this->template("hotelset");
	}

	public function doWebBrand() {
		global $_GPC, $_W;
		$op = $_GPC['op'];
		if ($op == 'edit') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('hotel2_brand') . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，品牌不存在或是已经删除！', '', 'error');
				}
			}

			if (checksubmit('submit')) {
				$data = array(
					'weid' => $_W['uniacid'],
					'title' => $_GPC['title'],
					'status' => $_GPC['status'],
				);

				if (empty($id)) {
					pdo_insert('hotel2_brand', $data);
				} else {
					pdo_update('hotel2_brand', $data, array('id' => $id));
				}
				message('品牌信息更新成功！', $this->createWebUrl('brand'), 'success');
			}
			include $this->template('brand_form');
		} else if ($op == 'delete') {
			$id = intval($_GPC['id']);
			pdo_delete('hotel2_brand', array('id' => $id));
			message('删除成功！', referer(), 'success');
		} else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {

				$id = intval($id);
				pdo_delete('hotel2_brand', array('id' => $id));
			}
			$this->web_message('规则操作成功！', '', 0);
			exit();
		} else if ($op == 'showall') {
			if ($_GPC['show_name'] == 'showall') {
				$show_status = 1;
			} else {
				$show_status = 0;
			}

			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);

				if (!empty($id)) {
					pdo_update('hotel2_brand', array('status' => $show_status), array('id' => $id));
				}
			}
			$this->web_message('操作成功！', '', 0);
			exit();
		} else if ($op == 'status') {

			$id = intval($_GPC['id']);
			if (empty($id)) {
				message('抱歉，传递的参数错误！', '', 'error');
			}
			$temp = pdo_update('hotel2_brand', array('status' => $_GPC['status']), array('id' => $id));

			if ($temp == false) {
				message('抱歉，刚才操作数据失败！', '', 'error');
			} else {
				message('状态设置成功！', referer(), 'success');
			}
		} else {
			$sql = "";
			$params = array();
			if (!empty($_GPC['title'])) {
				$sql .= ' AND `title` LIKE :title';
				$params[':title'] = "%{$_GPC['title']}%";
			}
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("SELECT * FROM " . tablename('hotel2_brand') . " WHERE weid = '{$_W['uniacid']}' $sql ORDER BY displayorder DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hotel2_brand') . " WHERE weid = '{$_W['uniacid']}' $sql", $params);
			$pager = pagination($total, $pindex, $psize);
			include $this->template('brand');
		}
	}

	public function doWebGetBusiness() {
		global $_W, $_GPC;
		$kwd = trim($_GPC['keyword']);
		$sql = 'SELECT * FROM ' . tablename('hotel2_business') . ' WHERE `weid`=:weid';
		$params = array();
		$params[':weid'] = $_W['uniacid'];
		if (!empty($kwd)) {
			$sql.=" AND `title` LIKE :title";
			$params[':title'] = "%{$kwd}%";
		}
		$ds = pdo_fetchall($sql, $params);
		include $this->template('business_query');
		exit();
	}

	public function doWebBusiness() {
		global $_GPC, $_W;
		$op = $_GPC['op'];
		if ($op == 'edit') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('hotel2_business') . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，商圈不存在或是已经删除！', '', 'error');
				}
			}

			if (checksubmit('submit')) {
				$data = array(
					'weid' => $_W['uniacid'],
					'title' => $_GPC['title'],
					'location_p' => $_GPC['district']['province'],
					'location_c' => $_GPC['district']['city'],
					'location_a' => $_GPC['district']['district'],
					'displayorder' => $_GPC['displayorder'],
					'status' => $_GPC['status'],
				);

				if (empty($id)) {
					pdo_insert('hotel2_business', $data);
				} else {
					pdo_update('hotel2_business', $data, array('id' => $id));
				}
				message('商圈信息更新成功！', $this->createWebUrl('business'), 'success');
			}
			include $this->template('business_form');
		} else if ($op == 'delete') {
			$id = intval($_GPC['id']);
			pdo_delete('hotel2_business', array('id' => $id));
			message('删除成功！', referer(), 'success');
		} else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {

				$id = intval($id);
				pdo_delete('hotel2_business', array('id' => $id));
			}
			$this->web_message('规则操作成功！', '', 0);
			exit();
		} else if ($op == 'showall') {
			if ($_GPC['show_name'] == 'showall') {
				$show_status = 1;
			} else {
				$show_status = 0;
			}

			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);

				if (!empty($id)) {
					pdo_update('hotel2_business', array('status' => $show_status), array('id' => $id));
				}
			}
			$this->web_message('操作成功！', '', 0);
			exit();
		} else if ($op == 'status') {

			$id = intval($_GPC['id']);
			if (empty($id)) {
				message('抱歉，传递的参数错误！', '', 'error');
			}
			$temp = pdo_update('hotel2_business', array('status' => $_GPC['status']), array('id' => $id));

			if ($temp == false) {
				message('抱歉，刚才操作数据失败！', '', 'error');
			} else {
				message('状态设置成功！', referer(), 'success');
			}
		} else {
			$sql = "";
			$params = array();
			if (!empty($_GPC['title'])) {
				$sql .= ' AND `title` LIKE :title';
				$params[':title'] = "%{$_GPC['title']}%";
			}
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("SELECT * FROM " . tablename('hotel2_business') . " WHERE weid = '{$_W['uniacid']}' $sql ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hotel2_business') . " WHERE weid = '{$_W['uniacid']}' $sql", $params);
			$pager = pagination($total, $pindex, $psize);
			include $this->template('business');
		}
	}
}
