<?php
	
	$setting = pdo_fetch("SELECT * FROM " . tablename('weilive_setting') . " WHERE weid = :weid ", array(':weid' => $weid));

	$pindex = max(1, intval($_GPC['page']));
	$psize = empty($setting) ? 10 : intval($setting['pagesize']);
	$follow = pdo_fetch("select uid from ".tablename('mc_mapping_fans')." where uniacid = ".$weid." and openid = '".$_W['openid']."'");
	if($op=='display'){
		$profile = mc_fetch($follow['uid'], array('credit1'));
		$award_list = pdo_fetchall("SELECT * FROM ".tablename('weilive_prize')." WHERE weid = ".$weid." and starttime <= ".time()." and endtime > ".time()." and number > 0 order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("SELECT count(id) FROM ".tablename('weilive_prize')." WHERE weid = ".$weid." and starttime <= ".time()." and endtime > ".time()." and number > 0");
		$awards = array();
		foreach($award_list as $a){
			if($a['inkind'] == 1){
				$awards[1][$a['id']] = $a;
			} elseif($a['inkind'] == 2){
				$awards[2][$a['id']] = $a;
			} else {
				$awards[3][$a['id']] = $a;
			}
		}
		$pager = pagination1($total, $pindex, $psize);
		include $this->template('award_list');
		exit;
	}
	
	if($op=='detail'){
		$id = intval($_GPC['id']);
  		$fromuser = $_W['openid'];
		$profile = fans_search($_W['fans']['from_user'], array('credit1'));
   		$prize = pdo_fetch("SELECT * FROM ".tablename('weilive_prize')." WHERE id='{$id}'");
		include $this->template('award_detail');
		exit;
	}
	
	if($op=='exchange'){
		$id = intval($_GPC['id']);
  		$fromuser = $_W['fans']['from_user'];
   		$prize = pdo_fetch("SELECT * FROM ".tablename('weilive_prize')." WHERE id='{$id}'");
  		$profile = mc_fetch($follow['uid']);
		$data = array(
			'weid'       => $weid,
			'title'      => $prize['title'],
			'thumb'      => $prize['thumb'],
			'integral'      => $prize['integral'],
			'realname'   => $profile['realname'],
			'createtime' => TIMESTAMP,
			'openid'     => $_W['fans']['from_user'],
			'inkind'     => $prize['inkind'],
		);
		
   		if($profile['credit1']>=$prize['integral']&&$prize['number']>0){
			if($prize['inkind'] == 1){
				$data['status'] = 1;
				//卡密
				$activation_code = iunserializer($prize['activation_code']);
				$code = array_pop($activation_code);
				//print_r($code);exit;
				$data['code'] = $code;
				$number = $prize['number']-1;
				//更新奖品卡密
				pdo_update('weilive_prize', array('number'=>$number, 'activation_code'=>iserializer($activation_code)), array('id'=>$id));
				//pdo_query("UPDATE ".tablename('weilive_prize')."SET number = ".$number." and activation_code= '".iserializer($activation_code)."' WHERE id = '$id' AND weid = '{$weid}'");
			} else {
				$data['status'] = 0;
				$number = $prize['number']-1;
				//更新实物数量
				pdo_query("UPDATE ".tablename('weilive_prize')."SET number= ".$number." WHERE id = '$id' AND weid = '{$weid}'");
			}
			$data['url'] = $prize['activation_url'];
			pdo_insert('weilive_prizecode',$data);
			$data = array(
				'credit1' => $profile['credit1']-$prize['integral']
			);
			fans_update($fromuser, $data);
   			
			if ($prize['inkind']==1) {
				$result = '兑换码：' . $code . (empty($prize['activation_url']) ? '' : '<br>兑换方式：' . $prize['activation_url'].'<br>请妥善保存好兑换码和兑换地址！');
				message($result,'','success'); 
			}else{
				message('兑换成功,等待我们联系您！',$this->createMobileUrl('home'),'success');
			}
   		} else {
   			 message('兑换失败，积分不足！',$this->createMobileUrl('home'),'error');
   		}
	}
	
	if($op=='myaward'){
		$award_list = pdo_fetchall("select * from ".tablename('weilive_prizecode')." where weid = ".$weid." and openid = '".$_W['openid']."' order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("select count(id) from ".tablename('weilive_prizecode')." where weid = ".$weid." and openid = '".$_W['openid']."'");
		$awards = array();
		foreach($award_list as $a){
			if($a['inkind'] == 1){
				$awards[1][$a['id']] = $a;
			} elseif($a['inkind'] == 2){
				$awards[2][$a['id']] = $a;
			} else {
				$awards[3][$a['id']] = $a;
			}
		}
		$pager = pagination1($total, $pindex, $psize);
		include $this->template('myaward_list');
		exit;
	}
?>