<?php
global $_GPC,$_W;

		$rid = $_GPC['rid'];
		
		$weid = $_W['weid'];
		checkauth();
		$fromuser = $_W['fans']['from_user'];
		
		$current_date = date('Y-m-d');
		
		$times = isset($this->module['config']['times']) ? $this->module['config']['times'] : 1;
		
		$bd = $_GPC['bd'];
		
		$ed = $_GPC['ed'];

		if (!empty($bd) && !empty($ed) ){
		
			$current_month = $this -> getThisMonth($bd);
			
			$current_last_month = $this -> getLastMonth($bd);
			
			$current_next_month = $this -> getNextMonth($bd);

		}
		else{
		
			$current_month = $this -> getThisMonth($current_date);
			
			$current_last_month = $this -> getLastMonth($current_date);
			
			$current_next_month = $this -> getNextMonth($current_date);
		
		}
		
		$this_month_b = $current_month['0'];
		
		$this_month_e = $current_month['1'];
		
		$this_year = substr($this_month_b,0,4);
		
		$this_month = substr($this_month_b,5,2);
		
		
		$last_month_b = $current_last_month['0'];
		
		$last_month_e = $current_last_month['1']; 
		
		$last_month = substr(str_replace('-','',$last_month_b),0,6);
		
		
		$next_month_b = $current_next_month['0'];
		
		$next_month_e = $current_next_month['1'];
		
		$next_month = substr(str_replace('-','',$next_month_b),0,6);
		
		$month_usersigned_info = pdo_fetchall("SELECT * FROM " . tablename('nsign_record') . " WHERE `uid` = :uid AND `sign_time` >= :this_month_b AND `sign_time` <= :this_month_e", array(':uid' => $_W['member']['uid'], ':this_month_b' => strtotime($this_month_b), ':this_month_e' => strtotime($this_month_e) ));
		
		$value = array(); 

		foreach( $month_usersigned_info as $value )
		{

			$user_signed_days .= date('d',$value['sign_time']).',';//粉丝当月签到日期

		}
		
		$user_signed_days = '['.$user_signed_days.']';
		
		$user_lastsign_info = pdo_fetch("SELECT * FROM " . tablename('nsign_record') . " WHERE `uid` = :uid ORDER BY sign_time DESC LIMIT 1 ", array(':uid' => $_W['member']['uid'] ));

		$user_maxallsign_num = $user_lastsign_info['maxtotal_sign_num'];
		
		$today_usersigned_info = pdo_fetchall("SELECT * FROM " . tablename('nsign_record') . " WHERE `uid` = :uid AND sign_time >= :current_date ", array(':uid' => $_W['member']['uid'], ':current_date' => strtotime($current_date) ));
		
		$today_usersigned_num = count($today_usersigned_info);
		
		if(empty($user_maxallsign_num)){
		
			$user_maxallsign_num = 0;
		
		}
		
		$profile = fans_search($fromuser);
		
		if (!empty($rid)) {
		
			$reply = pdo_fetch("SELECT * FROM ".tablename('nsign_reply')." WHERE rid = :rid ", array(':rid' => $rid));			
 		
		}
		
		$Picurl = $_W['attachurl'].$reply['picture'];
		
		include $this->template('index');