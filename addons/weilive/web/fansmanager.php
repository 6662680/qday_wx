<?php
	if($op=='display') {
		if (!empty($_GPC['credit1']) || !empty($_GPC['credit2'])) {
			$credit1 = $_GPC['credit1'];
			$credit2 = $_GPC['credit2'];
			foreach ($credit1 as $uid => $c1) {
				pdo_update('mc_members', array('credit1' => $c1, 'credit2' => $credit2[$id]), array('uid' => $uid));
			}
			message('更新成功！', $this->createWebUrl('fansmanager', array('op' => 'display')), 'success');
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$fans = pdo_fetchall("SELECT * FROM " .tablename('mc_members')." WHERE uniacid = ".$weid." ORDER BY createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("SELECT count(uid) FROM " . tablename('mc_members') . " WHERE uniacid = ".$weid);
		$pager = pagination1($total, $pindex, $psize);
	}

	if($op=='save'){
		$uid = $_GPC['id'];
		$temp = pdo_update('mc_members', array('credit1' => $_GPC['credit1'], 'credit2' => $_GPC['credit2']), array('uid' => $uid));
		echo $uid;
		exit;
	}
	
	if($op=='sort'){
		$sort = array(
			'realname'=>trim($_GPC['realname']),
			'nickname'=>trim($_GPC['nickname']),
			'mobile'=>trim($_GPC['mobile'])
		);
		if($_GPC['ischeck']==1){
			$ischeck = 1;
			$realnames = explode(',', $sort['realname']);
			$rs = '';
			foreach($realnames as $r){
				$rs = $rs."'".$r."',";
			}
			$rs = '('.trim($rs,',').')';
			$fans = pdo_fetchall("SELECT * FROM ".tablename('mc_members')." WHERE uniacid = ".$weid." and realname in ".$rs);
		} elseif ($_GPC['ischeck']==2){
			$ischeck = 2;
			$nickname = explode(',', $sort['nickname']);
			$rs = '';
			foreach($nickname as $r){
				$rs = $rs."'".$r."',";
			}
			$rs = '('.trim($rs,',').')';
			$fans = pdo_fetchall("SELECT * FROM ".tablename('mc_members')." WHERE uniacid = ".$weid." and nickname in ".$rs);
		} elseif ($_GPC['ischeck']==3){
			$ischeck = 3;
			$mobile = explode(',', $sort['mobile']);
			$rs = '';
			foreach($mobile as $r){
				$rs = $rs."'".$r."',";
			}
			$rs = '('.trim($rs,',').')';
			$fans = pdo_fetchall("SELECT * FROM ".tablename('mc_members')." WHERE uniacid = ".$weid." and mobile in ".$rs);
		} else {
			$fans = pdo_fetchall("SELECT * FROM ".tablename('mc_members')." WHERE uniacid = ".$weid." and realname like '%".$sort['realname']."%' and nickname like '%".$sort['nickname']."%' and mobile like '%".$sort['mobile']."%' ORDER BY createtime DESC");
		}
	}
	
	include $this->template('web/fansmanager');
?> 
