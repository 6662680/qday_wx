<?php
/**
 * 微ktv
 *
 * @author SxxPro & ewei
 * @url
 */
defined('IN_IA') or exit('Access Denied');


/**
 * 计算用户密码hash
 * @param int $flag 0注册，1登录
 * @param array $member 用户数据
 * @return string
 */
if(!function_exists('ktv_set_userinfo')) {
	function ktv_set_userinfo($flag = 0, $member)
	{
		global $_GPC, $_W;
		unset($member['password']);
		unset($member['salt']);
		insert_cookie('__ktv_member', $member);
	}
}



if(!function_exists('ktv_get_userinfo')) {
	function ktv_get_userinfo()
	{
		global $_W;
		$key = '__ktv_member';
		return get_cookie($key);
	}
}



if(!function_exists('get_cookie')) {
	function get_cookie($key)
	{
		global $_W;
		$key = $_W['config']['cookie']['pre'] . $key;
		return json_decode(base64_decode($_COOKIE[$key]), true);
	}
}



if(!function_exists('insert_cookie')) {
	function insert_cookie($key, $data)
	{
		global $_W, $_GPC;
		$session = base64_encode(json_encode($data));
		isetcookie($key, $session, !empty($_GPC['rember']) ? 7 * 86400 : 0);
	}
	}

//检查用户是否登录
if(!function_exists('check_ktv_user_login')) {
	function check_ktv_user_login($set)
	{
		global $_W;
		$weid = $_W['uniacid'];
		$from_user = $_W['fans']['from_user'];
		$user_info = ktv_get_userinfo();
		if (empty($user_info['id'])) {
			return 0;
		} else {
		  if ( ($from_user == $user_info['from_user']) && ($weid == $user_info['weid']) ) {
			  if ($set['user'] == 2 && $user_info['user_set'] != 2) {
				  return 0;
			  } else {
				  return 1;
			  }
			} else {
				return 0;
			}
		}
	}
}

/**
 * 计算用户密码hash
 * @param string $input 输入字符串
 * @param string $salt 附加字符串
 * @return string
 */
if(!function_exists('ktv_member_hash')) {
	function ktv_member_hash($input, $salt)
	{
		global $_W;
		$input = "{$input}-{$salt}-{$_W['config']['setting']['authkey']}";
		return sha1($input);
	}
}

/**
 * 用户注册
 * PS:密码字段不要加密
 * @param array $member 用户注册信息，需要的字段必须包括 username, password, remark
 * @return int 成功返回新增的用户编号，失败返回 0
 */
if(!function_exists('ktv_member_check')) {
	function ktv_member_check($member)
	{
		$sql = 'SELECT `password`,`salt` FROM ' . tablename('ktv2_member') . " WHERE 1";
		$params = array();
		if (!empty($member['uid'])) {
			$sql .= ' AND `uid`=:uid';
			$params[':uid'] = intval($member['uid']);
		}
		if (!empty($member['weid'])) {
			$sql .= ' AND `weid`=:weid';
			$params[':weid'] = intval($member['weid']);
		}
		if (!empty($member['username'])) {
			$sql .= ' AND `username`=:username';
			$params[':username'] = $member['username'];
		}
		if (!empty($member['from_user'])) {
			$sql .= ' AND `from_user`=:from_user';
			$params[':from_user'] = $member['from_user'];
		}
		if (!empty($member['status'])) {
			$sql .= " AND `status`=:status";
			$params[':status'] = intval($member['status']);
		}
		if (!empty($member['id'])) {
			$sql .= " AND `id`!=:id";
			$params[':id'] = intval($member['id']);
		}
		$sql .= " LIMIT 1";
		$record = pdo_fetch($sql, $params);
		if (!$record || empty($record['password']) || empty($record['salt'])) {
			return false;
		}
		if (!empty($member['password'])) {
			$password = ktv_member_hash($member['password'], $record['salt']);
			return $password == $record['password'];
		}
		return true;
	}
}

/**
 * 获取单条用户信息，如果查询参数多于一个字段，则查询满足所有字段的用户
 * PS:密码字段不要加密
 * @param array $member 要查询的用户字段，可以包括  uid, username, password, status
 * @param bool 是否要同时获取状态信息
 * @return array 完整的用户信息
 */
if(!function_exists('ktv_member_single')) {
	function ktv_member_single($member)
	{
		$sql = 'SELECT * FROM ' . tablename('ktv2_member') . " WHERE 1";
		$params = array();
		if (!empty($member['weid'])) {
			$sql .= ' AND `weid`=:weid';
			$params[':weid'] = $member['weid'];
		}
		if (!empty($member['from_user'])) {
			$sql .= ' AND `from_user`=:from_user';
			$params[':from_user'] = $member['from_user'];
		}
		if (!empty($member['username'])) {
			$sql .= ' AND `username`=:username';
			$params[':username'] = $member['username'];
		}
		if (!empty($member['status'])) {
			$sql .= " AND `status`=:status";
			$params[':status'] = intval($member['status']);
		}
		$sql .= " LIMIT 1";
		$record = pdo_fetch($sql, $params);
		if (!$record) {
			return false;
		}
		if (!empty($member['password'])) {
			$password = ktv_member_hash($member['password'], $record['salt']);
			if ($password != $record['password']) {
				return false;
			}
		}
		return $record;
	}
}



if(!function_exists('get_ktv_set')) {
	function get_ktv_set()
	{
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$set = pdo_fetch("select * from " . tablename('ktv2_set') . " where weid=:weid limit 1", array(":weid" => $weid));
		if (!$set) {
			$set = array(
				"user" => 1,
				"bind" => 1,
				"reg" => 1,
				"ordertype" => 1,
				"regcontent" => "",
				"paytype1" => 0,
				"paytype2" => 0,
				"paytype3" => 0,
				"is_unify" => 0,
				"version" => 0,
				"tel" => "",
			);
		}
		return $set;
	}
	}

//获取登录用户
if(!function_exists('get_login_user')) {
	function get_login_user()
	{
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		if (isset($_SESSION['ktv2_member'])) {
			return json_decode($_SESSION['ktv2_member']);
		}
		$member = pdo_fetch("select * from " . tablename('ktv2_member') . " where weid=:weid and from_user=:from_user and islogin=1 limit 1", array(":weid" => $weid, ":from_user" => $_W['fans']['from_user']));
		session_start();
		$_SESSION['ktv2_member'] = json_encode($member);
		
		return $member;
	}
}




if(!function_exists('check_orderinfo')) {
	function check_orderinfo($member)
	{
		global $_GPC, $_W;
		$sql = "SELECT ID FROM " . tablename('ktv2_order') . " WHERE 1 = 1";
		if (!empty($member['ktvid'])) {
			$sql .= ' AND `ktvid`=:ktvid';
			$params[':ktvid'] = $member['ktvid'];
		}
		if (!empty($member['openid'])) {
			$sql .= ' AND `openid`=:openid';
			$params[':openid'] = $member['openid'];
		}
		if (!empty($member['roomid'])) {
			$sql .= ' AND `roomid`=:roomid';
			$params[':roomid'] = $member['roomid'];
		}
		if (!empty($member['memberid'])) {
			$sql .= ' AND `memberid`=:memberid';
			$params[':memberid'] = $member['memberid'];
		}
		if (!empty($member['name'])) {
			$sql .= ' AND `name`=:name';
			$params[':name'] = $member['name'];
		}
		if (!empty($member['contact_name'])) {
			$sql .= ' AND `contact_name`=:contact_name';
			$params[':contact_name'] = $member['contact_name'];
		}
		if (!empty($member['mobile'])) {
			$sql .= ' AND `mobile`=:mobile';
			$params[':mobile'] = $member['mobile'];
		}
		if (!empty($member['btime'])) {
			$sql .= ' AND `btime`=:btime';
			$params[':btime'] = $member['btime'];
		}
		if (!empty($member['etime'])) {
			$sql .= ' AND `etime`=:etime';
			$params[':etime'] = $member['etime'];
		}
		if (!empty($member['nums'])) {
			$sql .= ' AND `nums`=:nums';
			$params[':nums'] = $member['nums'];
		}
		if (!empty($member['sum_price'])) {
			$sql .= ' AND `sum_price`=:sum_price';
			$params[':sum_price'] = $member['sum_price'];
		}
		$sql .= " LIMIT 1";
		$record = pdo_fetch($sql, $params);
		if ($record) {
			return 1;
		} else {
			return 0;
		}
	}
}

/**
 * 生成分页数据
 * @param int $currentPage 当前页码
 * @param int $totalCount 总记录数
 * @param string $url 要生成的 url 格式，页码占位符请使用 *，如果未写占位符，系统将自动生成
 * @param int $pageSize 分页大小
 * @return string 分页HTML
 */
if(!function_exists('get_page_array')) {
	function get_page_array($tcount, $pindex, $psize = 15)
	{
		global $_W;
		$pdata = array(
			'tcount' => 0,
			'tpage' => 0,
			'cindex' => 0,
			'findex' => 0,
			'pindex' => 0,
			'nindex' => 0,
			'lindex' => 0,
			'options' => ''
		);
		$pdata['tcount'] = $tcount;
		$pdata['tpage'] = ceil($tcount / $psize);
		if ($pdata['tpage'] <= 1) {
			$pdata['isshow'] = 0;
			return $pdata;
		}
		$cindex = $pindex;
		$cindex = min($cindex, $pdata['tpage']);
		$cindex = max($cindex, 1);
		$pdata['cindex'] = $cindex;
		$pdata['findex'] = 1;
		$pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
		$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
		$pdata['lindex'] = $pdata['tpage'];
		if ($pdata['cindex'] == $pdata['lindex']) {
			$pdata['isshow'] = 0;
			$pdata['islast'] = 1;
		} else {
			$pdata['isshow'] = 1;
			$pdata['islast'] = 0;
		}
		return $pdata;
	}
	}

//0升序 1降序
if(!function_exists('array_sort')) {
	function array_sort($arr, $keys, $type = 0)
	{
		$keysvalue = $new_array = array();
		foreach ($arr as $k => $v) {
			$keysvalue[$k] = $v[$keys];
		}
		if ($type == 0) {
			asort($keysvalue);
		} else {
			arsort($keysvalue);
		}
		reset($keysvalue);
		foreach ($keysvalue as $k => $v) {
			$new_array[$k] = $arr[$k];
		}
		return $new_array;
	}
}



if(!function_exists('img_url')) {
	function img_url($img = '') {
		global $_W;
		if (empty($img)) {
			return "";
		}
		if (substr($img, 0, 6) == 'avatar') {
			return $_W['siteroot'] . "resource/image/avatar/" . $img;
		}
		if (substr($img, 0, 8) == './themes') {
			return $_W['siteroot'] . $img;
		}
		if (substr($img, 0, 1) == '.') {
			return $_W['siteroot'] . substr($img, 2);
		}
		if (substr($img, 0, 5) == 'http:') {
			return $img;
		}
		return $_W['attachurl'] . $img;
	}
}