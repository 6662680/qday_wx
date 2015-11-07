<?php
	if($op=='display'){
		$location_ps = pdo_fetchall("select distinct location_p from ".tablename('weilive_stores')." where checked = 1 and status = 1 and weid = ".$weid);
		$cities = pdo_fetchall("select distinct location_p,location_c from ".tablename('weilive_stores')." where checked = 1 and status = 1 and weid = ".$weid);
	}
	
	if($op=='positionsort'){
		$op = 'sort';
		$location_c = $_GPC['location_c'];
		$hotstores = pdo_fetchall("SELECT * FROM ".tablename('weilive_stores')." WHERE weid = :weid AND location_c = '".$location_c."' AND status<>0 AND isfirst=1 ORDER BY displayorder DESC ", array(':weid' => $weid));
		$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where start_time < ".time()." and end_time > ".time()." and isopen = 1 and flag = 0 and weid = ".$weid);
		include $this->template('index');
		exit;
	}
	
	if($op=='sort'){
		$op = 'sort';
		$condition = trim($_GPC['condition']);
		$activities = pdo_fetchall("select * from ".tablename('weilive_activity')." where title like '%".$condition."%' AND start_time < ".time()." and end_time > ".time()." and isopen = 1 and flag = 0 and weid = ".$weid);
		if(!empty($activities)){
			$flag = 0;
			$hotstores = pdo_fetchall("SELECT * FROM ".tablename('weilive_stores')." WHERE weid = :weid AND status<>0 ORDER BY displayorder DESC ", array(':weid' => $weid));
			
			$lng = $_GPC['lng'];
			$lat = $_GPC['lat'];
			foreach($hotstores as $key=>$s){
				$hotstores[$key]['dis'] = getDistance($lng, $lat, $s['lng'], $s['lat']);
			}
			if(!empty($hotstores)){
				foreach ($hotstores as $key => $row) {
					$level[$key]  = $row['level'];
					$dis[$key] = $row['dis'];
				}
				array_multisort($dis, SORT_ASC, $level, SORT_DESC, $hotstores);
			}
			
			foreach($hotstores as $key=>$h){
				foreach($activities as $a){
					if($a['storeid']==$h['id']){
						$flag = 1;
						break;
					}
				}
				if($flag == 0){
					unset($hotstores[$key]);
				}
				$flag = 0;
			}
		}
		$location_c = '返回';
		include $this->template('index');
		exit;
	}
	include $this->template('position');
?>