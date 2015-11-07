<?php
	
	$cid = intval($_GPC['cid']);
	$condition_store = '';
	if (empty($cid)) {
		//全部类别
		$datas['关闭'] = 0;
		$categorys = pdo_fetchall("SELECT * FROM ". tablename('weilive_category'). " WHERE parentid=0 ORDER BY displayorder DESC");
	} else {
		//按类别查询
		$category = pdo_fetch("SELECT * FROM ". tablename('weilive_category'). " WHERE id={$cid}" );
		//属于父级
		if (empty($category['parentid'])) {
			$categorys = pdo_fetchall("SELECT * FROM ". tablename('weilive_category'). " WHERE (parentid={$category['id']}) ORDER BY parentid,displayorder DESC", array(), 'id');
			$datas = array();
			$datas['关闭'] = 0;
			foreach($categorys as $c){
				$datas[$c['name']] = $this->createMobileUrl('list', array('ccate'=>$c['id'], 'cname'=>$c['name'], 'cid'=>$cid, 'keyword'=>'keyword', 'op'=>'datas'));
			}
			$datas = json_encode($datas);
			$categoryids = implode("','", array_keys($categorys));
			$condition_store .= " AND pcate={$cid} ";
		} else {
			//子级
			$categorys = pdo_fetchall("SELECT * FROM ". tablename('weilive_category'). " WHERE (parentid={$category['parentid']}) ORDER BY parentid,displayorder DESC", array(), 'id');
			$datas = array();
			$datas['关闭'] = 0;
			foreach($categorys as $c){
				$datas[$c['name']] = $p['id'];
			}
			$datas = json_encode($datas);
			$condition_store .= " AND pcate={$cid} ";
			//$condition_store .= " AND ccate = {$category['id']} ";
		}
	}

	$setting = pdo_fetch("SELECT * FROM " . tablename('weilive_setting') . " WHERE weid = :weid ", array(':weid' => $weid));

	$pindex = max(1, intval($_GPC['page']));
	$psize = empty($setting) ? 10 : intval($setting['pagesize']);

	//商家列表 //搜索处理
	$keyword = trim($_GPC['keyword']);

	$order_condition = " ORDER BY displayorder DESC,status DESC,id DESC ";
	if (!empty($keyword)) {
		$gps = true;
		if($op=='datas'){
			$styles = 4;
			$condition_store .= " AND ccate={$_GPC['ccate']} ";
			$cname = $_GPC['cname'];
		} elseif($op=='level') {
			$styles = 1;
		} else {
			$styles = 3;
			$condition_store .= " AND location_a='{$_GPC['location_a']}' ";
			$location_a = $_GPC['location_a'];
		}
		
		if($op=='level'){
			$stores = pdo_fetchall("SELECT id, title, level FROM " . tablename('weilive_stores') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store}", array(':weid' => $weid));
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
			$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where storeid in (".$sid.") and start_time < ".time()." and end_time > ".time()." and flag = 0 and isopen = 1 and weid = ".$weid." order by used desc, catch desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			
			$total = pdo_fetchcolumn("select count(id) from ".tablename('weilive_activity')." where storeid in (".$sid.") and start_time < ".time()." and end_time > ".time()." and flag = 0 and isopen = 1 and weid = ".$weid);
		} else {
			//$condition_store = " AND (title like '%{$keyword}%' OR address like '%{$keyword}%' ) ";
			$stores = pdo_fetchall("SELECT id, title, level FROM " . tablename('weilive_stores') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} {$order_condition} LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid));
			$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where start_time < ".time()." and end_time > ".time()." and flag = 0 and isopen = 1 and weid = ".$weid);
			$total = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename('weilive_stores') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} ", array(':weid' => $weid));
		}
		
	} else {
		$styles = 2;
		if(empty($_GPC['lng'])){
			$gps = false;
		} else {
			$gps = true;
			$lng = $_GPC['lng'];
			$lat = $_GPC['lat'];
			$stores = pdo_fetchall("SELECT * FROM " . tablename('weilive_stores') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} {$order_condition} LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid));
			$distance = empty($setting) ? 5 : $setting['distance'];
			$total1 = 0;
			foreach($stores as $key=>$s){
				$stores[$key]['dis'] = getDistance($lng, $lat, $s['lng'], $s['lat']);
				if($stores[$key]['dis'] > $distance){
					$total1++;
					unset($stores[$key]);
				}
			}
			if(!empty($stores)){			
				foreach ($stores as $key => $row) {
					$level[$key]  = $row['level'];
					$dis[$key] = $row['dis'];
				}
				array_multisort($dis, SORT_ASC, $level, SORT_DESC, $stores);
			}
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('weilive_stores') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} ", array(':weid' => $weid));
			$total = $total - $total1;
			$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where start_time < ".time()." and end_time > ".time()." and flag = 0 and isopen = 1 and weid = ".$weid);
			if ($total <= $psize) {
				$no_more_data = 1;
			}
		}
		
	}
	$pager = pagination1($total, $pindex, $psize);
	
	if($op=='zone'){
		$location_as = pdo_fetchall("select distinct location_a from ".tablename('weilive_stores')." where weid = ".$weid." and location_c = '".$_GPC['text']."'");
		$data = array();
		$data['关闭'] = 0;
		foreach($location_as as $a){
			$data[$a['location_a']] = $this->createMobileUrl('list', array('cid'=>$cid, 'op'=>'data', 'location_a'=>$a['location_a'], 'keyword'=>'keyword'));
		}
		echo json_encode($data);
		exit;
	}
	
	$location_cs = pdo_fetchall("select distinct location_c from ".tablename('weilive_stores')." where weid = ".$weid);
	$data = array();
	$data['关闭'] = 0;
	foreach($location_cs as $key=>$c){
		$data[$c['location_c']] = $key;
	}
	$data = json_encode($data);
	
	include $this->template('category');
?>