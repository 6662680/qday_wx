<?php

function rad($d)  
{  
       return $d * 3.1415926535898 / 180.0;  
}  
function GetDistance($lat1, $lng1, $lat2, $lng2)  
{  
    $EARTH_RADIUS = 6378.137;  
    $radLat1 = rad($lat1);  
   $radLat2 = rad($lat2);  
   $a = $radLat1 - $radLat2;  
   $b = rad($lng1) - rad($lng2);  
   $s = 2 * asin(sqrt(pow(sin($a/2),2) +  
    cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));  
   $s = $s *$EARTH_RADIUS;  
   $s = round($s * 10000) / 10000;  
   return $s;  
} //距离计算函数

		global $_GPC, $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$fansloc = pdo_fetch("SELECT * FROM ".tablename('jufeng_wcy_loc')." WHERE weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'");
		if($_GPC['op'] == "locate"){
			if($fansloc['loc_x']){
				pdo_update('jufeng_wcy_loc', array('loc_x' => $_GPC['loc_x'], 'loc_y' => $_GPC['loc_y'], 'createtime' =>TIMESTAMP), array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid']));
				$difference = GetDistance($fansloc["loc_x"],$fansloc["loc_y"],$_GPC['loc_x'],$_GPC['loc_y']);
                if($difference > 0.3) $result = "refresh";
				}
			else{pdo_insert('jufeng_wcy_loc', array('loc_x' => $_GPC['loc_x'], 'loc_y' => $_GPC['loc_y'], 'createtime' =>TIMESTAMP,'from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid']));
			$result = "refresh";
			}
			message($result, '', 'ajax');
			exit;
			}//op为locate
			
$shoptype = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_shoptype')." WHERE weid = '{$_W['uniacid']}' ");
$typeid = intval($_GPC['typeid']);		
			switch($_GPC['order']){
				default: $orderStr = 'displayorder DESC';break;
				case '1': $orderStr = 'total DESC';break;
				case '2': $orderStr = 'sendprice ASC';break;
				case '3': $orderStr = 'enabled DESC';break;
				}
				if(!empty($typeid)){$typeidStr = " AND typeid = '{$typeid}'";}else{$typeidStr = '';}
$shop = pdo_fetchall("SELECT * FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND parentid = '0'".$typeidStr." ORDER BY ".$orderStr." LIMIT ".($pindex - 1) * $psize.','.$psize);

foreach($shop as &$row){
	$dist = GetDistance($fansloc["loc_x"],$fansloc["loc_y"],$row['loc_x'],$row['loc_y']);
	if($dist >= 10){$dist = round($dist/10,1);$dist .= "千米";}
	else{$dist = round($dist*100,-1);$dist .= "米";}
	$row['dist'] = $dist;
	}
	
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('jufeng_wcy_category')." WHERE weid = '{$_W['uniacid']}' AND parentid = '0'".$typeidStr." ORDER BY displayorder DESC");
			
		$pager = pagination($total, $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));		
		include $this->template('dianjia');
					?>