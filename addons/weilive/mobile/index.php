<?php
	//商家一级分类
	$category = pdo_fetchall("SELECT * FROM ".tablename('weilive_category')." WHERE weid = :weid AND parentid = 0 ORDER BY displayorder DESC", array(':weid' => $weid));
	//幻灯片
	$slides = pdo_fetchall("select * from ".tablename('weilive_slide')." where weid = ".$weid." and isshow = 1 order by listorder desc");
	$pindex = max(1, intval($_GPC['page']));
	$psize = empty($setting) ? 10 : intval($setting['pagesize']);
	if($op=='display'){
		$style = 1;
		$hotstores = pdo_fetchall("SELECT * FROM " . tablename('weilive_stores') . " WHERE weid = :weid AND status<>0 AND isfirst=1 ORDER BY displayorder DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid));
		$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('weilive_stores') . " WHERE weid = :weid AND status<>0 AND isfirst=1", array(':weid' => $weid));
		$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where start_time < ".time()." and end_time > ".time()." and isopen = 1 and flag = 0 and ischeck = 1 and weid = ".$weid);
	} else {
		$stores = pdo_fetchall("SELECT id, title, level FROM ".tablename('weilive_stores')." WHERE weid = :weid AND status<>0 AND isfirst = 1 and checked=1", array(':weid' => $weid));
		$store = array();
		foreach($stores as $s){
			$store[$s['id']]['title'] = $s['title'];
			$store[$s['id']]['level'] = $s['level'];
			$store[$s['id']]['id'] = $s['id'];
		}
		$storeid = array();
		foreach($stores as $key=>$s){
			$storeid[$key] = $s['id'];
		}
		$sid = implode(",", $storeid);
		if(empty($stores)){
			$sid = 0;
		}
		if($op=='hot'){
			$style = 2;
			$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where storeid in (".$sid.") and start_time < ".time()." and end_time > ".time()." and flag = 0 and isopen = 1 and ischeck = 1 and weid = ".$weid." order by catch desc, used desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn("select count(id) from ".tablename('weilive_activity')." where storeid in (".$sid.") and start_time < ".time()." and end_time > ".time()." and flag = 0 and isopen = 1 and ischeck = 1 and weid = ".$weid);
		}
		if($op=='new'){
			$style = 3;
			$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where storeid in (".$sid.") and start_time < ".time()." and end_time > ".time()." and flag = 0 and isopen = 1 and ischeck = 1 and weid = ".$weid." order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn("select count(id) from ".tablename('weilive_activity')." where storeid in (".$sid.") and start_time < ".time()." and end_time > ".time()." and flag = 0 and isopen = 1 and ischeck = 1 and weid = ".$weid);
		}
	}
	
	$location_c = '中国';
	$pager = pagination1($total, $pindex, $psize);
	
	// $ip = getip();
	// $ip = "115.239.210.27";
	// $res = ihttp_get("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=".$ip);
	// $res = $res['content'];
	// $res = str_replace("var remote_ip_info = ", "", $res);
	// $res = str_replace(";", "", $res);
	// $res = json_decode($res);
	// $city = $res->city;

	include $this->template('index');
?>