<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

		$item_per_page = $_GPC['pagesnum'];  
		$page_number = $_GPC['page'];    
		if(!is_numeric($page_number)){  
   		 header('HTTP/1.1 500 Invalid page number!');  
    		exit();  
		}
      	
		$reply = pdo_fetch("SELECT moshi,tpname,tpsname, iscode, indexorder,tmoshi FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		
		$position = ($page_number * $item_per_page);  
		$where = '';
		if (!empty($_GPC['keyword'])) {
				$keyword = $_GPC['keyword'];
				if (is_numeric($keyword)) 
					$where .= " AND id = '".$keyword."'";
				else 				
					$where .= " AND (nickname LIKE '%{$keyword}%' OR realname LIKE '%{$keyword}%' )";
			
		}
		
		$where .= " AND status = '1'";
		
		if ($reply['indexorder'] == '1') {
			$where .= " ORDER BY `createtime` DESC";
		}elseif ($reply['indexorder'] == '11') {
			$where .= " ORDER BY `createtime` ASC";
		}elseif ($reply['indexorder'] == '2') {
			$where .= " ORDER BY `id` DESC";
		}elseif ($reply['indexorder'] == '22') {
			$where .= " ORDER BY `id` ASC";
		}elseif ($reply['indexorder'] == '3') {
			$where .= " ORDER BY `photosnum` + `xnphotosnum` DESC";
		}elseif ($reply['indexorder'] == '33') {
			$where .= " ORDER BY `photosnum` + `xnphotosnum` ASC";
		}elseif ($reply['indexorder'] == '4') {
			$where .= " ORDER BY `hits` + `xnhits` DESC";
		}elseif ($reply['indexorder'] == '44') {
			$where .= " ORDER BY `hits` + `xnhits` ASC";
		}elseif ($reply['indexorder'] == '5') {
			$where .= " ORDER BY `vedio` DESC, `music` DESC, `id` DESC";
		}else {
			$where .= " ORDER BY `id` DESC";
		}
		$userlist = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid and rid = :rid '.$where.'  LIMIT ' . $position . ',' . $item_per_page, array(':uniacid' => $uniacid,':rid' => $rid) );
		
		if (!empty($userlist)){
		//output results from database 
			if ($reply['tmoshi'] == 1 || $reply['tmoshi'] == 2 || $reply['tmoshi'] == 3) {
				foreach ($userlist as $mid => $row) {					
					if ($row['realname']){
						$usernames = cutstr($row['realname'], '4');
					}else{
						$usernames = cutstr($row['nickname'], '4');
					}
					$result = $result.'<input type="hidden" name="ucreatetime" id="ucreatetime" value="'.$row['createtime'].'" />';
					if ($reply['tmoshi'] == 1) {
						$result = $result.'<li style="cursor: pointer;"><div class="li_box">';
					}elseif ($reply['tmoshi'] == 2) {
						if (($mid+1)%2 == 1) {
							$result = $result.'<li style="cursor: pointer;"><div class="li_box">';
						}else{
							$result = $result.'<li style="cursor: pointer;  margin: 0px 0% 8px 2%;"><div class="li_box">';
						}
					}else{
						$result = $result.'<li style="cursor: pointer;"><div class="li_box">';
					}
					
					
					
					if ($reply['moshi'] == 2) {
						$result = $result.'<a href="'.$this->createMobileUrl('tuser', array('rid' => $rid, 'tfrom_user'=> $row['from_user'])).'">';
					}else {
						$result = $result.'<a href="'.$this->createMobileUrl('tuserphotos', array('rid' => $rid, 'tfrom_user'=> $row['from_user'])).'">';
					}	
					
					$result = $result.'<div class="xs_pic">';
					if ($row['photo']) {
						$result = $result.'<img src="'.toimage($row['photo']).'">';
					}else  {
						$result = $result.'<img src="'.toimage($row['avatar']).'">';
					}
					$result = $result.'<span style="  left: 6px;  top: 6px;  position: absolute;  color: #fff;  background: rgba(0, 0, 0, 0.51);  padding: 1px 6px;  border-radius: 5px;">ID: '.$row['id'].'</span>';
					$result = $result.'<div class="biaozhu_s" style="font-size:12px;"><img src="'.toimage($row['avatar']).'" width="35" style="border-radius: 35px;margin-right:10px;width:15px;">';
					$result = $result.$usernames;
					$result = $result.'</div></div></a>';
					$result = $result.'<div class="toupiao" id="'.$row['id'].'" style=" padding: 0px 10px 0px;  height: 70px;">';
					$piaoshu = ($row['photosnum'] + $row['xnphotosnum']);
					$result = $result.'<span class="piaoshu">'.$reply['tpsname'].' '.$piaoshu.'</span>';
					$result = $result.'<dd style="text-align:center;  text-decoration: none;">';
					
					if ($reply['tpname']) {			
						$result = $result.'<a href="javascript:void(0)" id="'.$row['id'].'" class="btn  btn-danger"  data-toggle="tooltip" data-placement="top" ';										
						$result = $result.'onclick="tvotep(\''.$row['from_user'].'\', \''.$usernames.'\')"';					
						$result = $result.'style="color:#fff;  background-color: #e2216f;  font-size: 12px;">'.$reply['tpname'].'</a>';		
						
					}
					$result = $result.'</dd></div></div></li>';	
				}
				print_r($result);
			}else {
				//$result = $result.'<ul class="fl columns">';
				foreach ($userlist as $mid => $row) {
					if (($mid+1)%2 == 1) {
						continue;
					}
			
					if ($row['realname']){
						$usernames = cutstr($row['realname'], '4');
					}else{
						$usernames = cutstr($row['nickname'], '4');
					}
					$result = $result.'<input type="hidden" name="ucreatetime" id="ucreatetime'.$row['from_user'].'" value="'.$row['createtime'].'" />';
					if ($page_number%2 == 0) {
						$result = $result.'<li style="cursor: pointer;"><div class="li_box">';
					}else {
						$result = $result.'<li style="cursor: pointer;margin: 0px 0% 8px 2%;"><div class="li_box">';	
					}
					
					
					
					
					if ($reply['moshi'] == 2) {
						$result = $result.'<a href="'.$this->createMobileUrl('tuser', array('rid' => $rid, 'tfrom_user'=> $row['from_user'])).'">';
					}else {
						$result = $result.'<a href="'.$this->createMobileUrl('tuserphotos', array('rid' => $rid, 'tfrom_user'=> $row['from_user'])).'">';
					}	
					$result = $result.'<div class="xs_pic">';
					if ($row['photo']) {
						$result = $result.'<img src="'.toimage($row['photo']).'">';
					}else  {
						$result = $result.'<img src="'.toimage($row['avatar']).'">';
					}
					$result = $result.'<span style="  left: 6px;  top: 6px;  position: absolute;  color: #fff;  background: rgba(0, 0, 0, 0.51);  padding: 1px 6px;  border-radius: 5px;">ID: '.$row['id'].'</span>';
					$result = $result.'<div class="biaozhu_s" style="font-size:12px;"><img src="'.toimage($row['avatar']).'" width="35" style="border-radius: 35px;margin-right:10px;width:15px;">';
					$result = $result.$usernames;
					$result = $result.'</div></div></a>';
					$result = $result.'<div class="toupiao" id="'.$row['id'].'" style=" padding: 0px 10px 0px;  height: 70px;">';
					$piaoshu = ($row['photosnum'] + $row['xnphotosnum']);
					$result = $result.'<span class="piaoshu">'.$reply['tpsname'].' '.$piaoshu.'</span>';
					$result = $result.'<dd style="text-align:center;  text-decoration: none;">';
					
					if ($reply['tpname']) {			
						$result = $result.'<a href="javascript:void(0)" id="'.$row['id'].'" class="btn  btn-danger"  data-toggle="tooltip" data-placement="top" ';										
						$result = $result.'onclick="tvotep(\''.$row['from_user'].'\', \''.$usernames.'\')"';					
						$result = $result.'style="color:#fff;  background-color: #e2216f;  font-size: 12px;">'.$reply['tpname'].'</a>';		
						
					}
					$result = $result.'</dd></div></div></li>';	
				}
				//$result = $result.'</ul>';
				print_r($result);
				//$result = $result.'<ul class="fr columns">';
				if ($_GPC['pagedatas'] == 'fr') {
					foreach ($userlist as $mid => $row) {
						if (($mid+1)%2 == 0) {
							continue;
						}
				
						if ($row['realname']){
							$usernames = cutstr($row['realname'], '4');
						}else{
							$usernames = cutstr($row['nickname'], '4');
						}
						$resultr = $resultr.'<input type="hidden" name="ucreatetime" id="ucreatetime'.$row['from_user'].'" value="'.$row['createtime'].'" />';
						
						if (($page_number + 1)%2 == 0) {
							$resultr = $resultr.'<li style="cursor: pointer;"><div class="li_box">';
						}else {
							$resultr = $resultr.'<li style="cursor: pointer;margin: 0px 0% 8px 2%;"><div class="li_box">';	
						}
								
						if ($reply['moshi'] == 2) {
							$resultr = $resultr.'<a href="'.$this->createMobileUrl('tuser', array('rid' => $rid, 'tfrom_user'=> $row['from_user'])).'">';
						}else {
							$resultr = $resultr.'<a href="'.$this->createMobileUrl('tuserphotos', array('rid' => $rid, 'tfrom_user'=> $row['from_user'])).'">';
						}	
						$resultr = $resultr.'<div class="xs_pic">';
						if ($row['photo']) {
							$resultr = $resultr.'<img src="'.toimage($row['photo']).'">';
						}else  {
							$resultr = $resultr.'<img src="'.toimage($row['avatar']).'">';
						}
						$resultr = $resultr.'<span style="  left: 6px;  top: 6px;  position: absolute;  color: #fff;  background: rgba(0, 0, 0, 0.51);  padding: 1px 6px;  border-radius: 5px;">ID: '.$row['id'].'</span>';
						$resultr = $resultr.'<div class="biaozhu_s" style="font-size:12px;"><img src="'.toimage($row['avatar']).'" width="35" style="border-radius: 35px;margin-right:10px;width:15px;">';
						$resultr = $resultr.$usernames;
						$resultr = $resultr.'</div></div></a>';
						$resultr = $resultr.'<div class="toupiao" id="'.$row['id'].'" style=" padding: 0px 10px 0px;  height: 70px;">';
						$piaoshu = ($row['photosnum'] + $row['xnphotosnum']);
						$resultr = $resultr.'<span class="piaoshu">'.$reply['tpsname'].' '.$piaoshu.'</span>';
						$resultr = $resultr.'<dd style="text-align:center;  text-decoration: none;">';
						
						if ($reply['tpname']) {			
							$resultr = $resultr.'<a href="javascript:void(0)" id="'.$row['id'].'" class="btn  btn-danger"  data-toggle="tooltip" data-placement="top" ';										
							$resultr = $resultr.'onclick="tvotep(\''.$row['from_user'].'\', \''.$usernames.'\')"';					
							$resultr = $resultr.'style="color:#fff;  background-color: #e2216f;  font-size: 12px;">'.$reply['tpname'].'</a>';		
							
						}
						$resultr = $resultr.'</dd></div></div></li>';
					}
					//$resultr = $resultr.'</ul>';
					print_r($resultr);
				}
				
			}		
		}
