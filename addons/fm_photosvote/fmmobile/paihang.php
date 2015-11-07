<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

		if ($istop['ipannounce'] == 1) {
			$announce = pdo_fetchall("SELECT * FROM " . tablename($this->table_announce) . " WHERE uniacid= '{$uniacid}' AND rid= '{$rid}' ORDER BY id DESC");
			
		}
		//赞助商
		if ($reply['ispaihang'] == 1) {
			$advs = pdo_fetchall("SELECT * FROM " . tablename($this->table_advs) . " WHERE enabled=1  AND ismiaoxian = 0 AND uniacid= '{$uniacid}'  AND rid= '{$rid}' ORDER BY displayorder ASC");
			foreach ($advs as &$adv) {
				if (substr($adv['link'], 0, 5) != 'http:') {
					$adv['link'] = "http://" . $adv['link'];
				}
			}
			unset($adv);
		}
		
		//统计
		$csrs = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_users)." WHERE rid= ".$rid." AND status = 1");//参赛人数
		$ljtp = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_log)." WHERE rid= ".$rid."") + pdo_fetchcolumn("SELECT sum(xnphotosnum) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."");//累计投票
		$cyrs = $csrs + $reply['hits'] + pdo_fetchcolumn("SELECT sum(hits) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."") + pdo_fetchcolumn("SELECT sum(xnhits) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."") + $reply['xuninum'];//参与人数
		
		if(!empty($from_user)) {
		    $mygift = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
			//此处更新一下分享量和邀请量
			
		}
		
		
		
		if ($_GPC['votelog'] == 1) {//投票人
			$tuser = pdo_fetch("SELECT avatar,nickname FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $tfrom_user,':rid' => $rid));
			
			$pindex = max(1, intval($_GPC['page']));
			$psize = empty($reply['phbtpxz']) ? 10 : $reply['phbtpxz'];
			$m = ($pindex-1) * $psize+1;
			//取得用户列表
			$where = '';			
			
			if (!empty($tfrom_user)) {				
				$where .= " AND tfrom_user = '".$tfrom_user."'";				
			}
			
			$userlist = pdo_fetchall('SELECT * FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid and rid = :rid '.$where.' ORDER BY `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $uniacid,':rid' => $rid));
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_log).' WHERE uniacid= :uniacid and rid = :rid '.$where.'', array(':uniacid' => $uniacid,':rid' => $rid));
			$total_pages = ceil($total/$psize);	
			$pager = paginationm($total, $pindex, $psize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
			
			$title = $tuser['nickname'] . ' 的投票用户 - ' . $reply['title']; 
			$sharetitle = $tuser['nickname'] . '正在参加'. $reply['title'] .'，快来为'.$tuser['nickname'].'投一票吧！';
			$sharecontent = $tuser['nickname'] . '正在参加'. $reply['title'] .'，快来为'.$tuser['nickname'].'投一票吧！';
			$sharephoto = !empty($mygift['photo']) ? toimage($mygift['photo']) : toimage($tuser['avatar']);
			
			 $_share['title'] = $tuser['nickname'] . '正在参加'. $reply['title'] .'，快来为'.$tuser['nickname'].'投一票吧！';
			$_share['content'] = $tuser['nickname'] . '正在参加'. $reply['title'] .'，快来为'.$tuser['nickname'].'投一票吧！';
			$_share['imgUrl'] =  !empty($mygift['photo']) ? toimage($mygift['photo']) : toimage($tuser['avatar']);
		}else {//排行榜用户
			$pindex = max(1, intval($_GPC['page']));
			$psize =  empty($reply['phbtpxz']) ? 10 : $reply['phbtpxz'];
			$m = ($pindex-1) * $psize+1;
			//取得用户列表
			$where = '';			
			if (!empty($tfrom_user)) {				
				$where .= " AND tfrom_user = '".$tfrom_user."'";				
			}
			$where .= " AND status = '1'";
			
			if ($reply['indexpx'] == '0') {
				$where .= " ORDER BY `photosnum` + `xnphotosnum` DESC";
			}elseif ($reply['indexpx'] == '1') {
				$where .= " ORDER BY `createtime` DESC";
				
			}elseif ($reply['indexpx'] == '2') {
				$where .= " ORDER BY `hits` + `xnhits` DESC";
			}else{
				$where .= " ORDER BY `photosnum` + `xnphotosnum` DESC";
			}
			$userlist = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid and rid = :rid '.$where.' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $uniacid,':rid' => $rid));
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid and rid = :rid '.$where.'', array(':uniacid' => $uniacid,':rid' => $rid));
			$total_pages = ceil($total/$psize);	
			$pager = paginationm($total, $pindex, $psize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
			
			
			
			$title = $reply['title'] . ' 排行榜 - ' . $_W['account']['name']; 
						
			$_share['title'] = $reply['title'] . ' 排行榜 - ' . $_W['account']['name']; 
			$_share['content'] = $reply['title'] . ' 排行榜 - ' . $_W['account']['name']; 
			$_share['imgUrl'] = toimage($reply['sharephoto']);	
			
		}

				
		//整理数据进行页面显示		
		$reply['sharetitle']= $this->get_share($uniacid,$rid,$from_user,$reply['sharetitle']);
		$reply['sharecontent']= $this->get_share($uniacid,$rid,$from_user,$reply['sharecontent']);
		
		$myavatar = $avatar;
		$mynickname = $nickname;
		$shareurl = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'duli' => 3,'fromuser' => $from_user));//分享URL
		$regurl = $_W['siteroot'] .'app/'.$this->createMobileUrl('reg', array('rid' => $rid));//关注或借用直接注册页
		$guanzhu = $reply['shareurl'];//没有关注用户跳转引导页
		$mygifturl = $_W['siteroot'] .'app/'.$this->createMobileUrl('photosvoteview', array('rid' => $rid));//我的页面
		
		$_share['link'] = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'duli' => 3,'fromuser' => $from_user));//分享URL		
		
				
		
		$toye = $this->_stopllq('paihang');
		include $this->template($toye);
