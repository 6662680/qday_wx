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
		//幻灯片
        $banners = pdo_fetchall("SELECT * FROM " . tablename($this->table_banners) . " WHERE enabled=1 AND uniacid= '{$uniacid}' AND rid= '{$rid}' ORDER BY displayorder ASC");
        foreach ($banners as &$banner) {
            if (substr($banner['link'], 0, 5) != 'http:' && $banner['link'] != '#' && $banner['link']!='javascript::;') {
                $banner['link'] = "http://" . $banner['link'];
            }
        }
        unset($banner);
		//赞助商
		if ($reply['isindex'] == 1) {
			$advs = pdo_fetchall("SELECT * FROM " . tablename($this->table_advs) . " WHERE enabled=1 AND ismiaoxian = 0 AND uniacid= '{$uniacid}'  AND rid= '{$rid}' ORDER BY displayorder ASC");
			foreach ($advs as &$adv) {
				if (substr($adv['link'], 0, 5) != 'http:' && $banner['link'] != '#' && $banner['link']!='javascript::;') {
					$adv['link'] = "http://" . $adv['link'];
				}
			}
			unset($adv);
		}
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = empty($reply['indextpxz']) ? 10 : $reply['indextpxz'];
		//取得用户列表
		$where = '';
		$keyword = $_GPC['keyword'];
		if (!empty($_GPC['keyword'])) {				
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
		$userlist = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid and rid = :rid '.$where.'  LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $uniacid,':rid' => $rid) );
		
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid and rid = :rid '.$where.'', array(':uniacid' => $uniacid,':rid' => $rid));
		$total_pages = ceil($total/$psize);	
		$pager = paginationm($total, $pindex, $psize, '', array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
		 
		// $userlist = pdo_fetchall("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid ", array(':uniacid' => $uniacid));
		
		
		//查询自己是否参与活动
		if(!empty($from_user)) {
		    $mygift = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
			//此处更新一下分享量和邀请量
			if(!empty($mygift)){
			    $yql = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_data)." WHERE uniacid = :uniacid and fromuser = :fromuser and rid = :rid and isin >= ".$reply['opensubscribe']."", array(':uniacid' => $uniacid,':fromuser' => $from_user,':rid' => $rid));
			    $fxl = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_data)." WHERE uniacid = :uniacid and fromuser = :fromuser and rid = :rid", array(':uniacid' => $uniacid,':fromuser' => $from_user,':rid' => $rid));
				//$hits = $mygift['hits'] + 1;
				pdo_update($this->table_users,array('sharenum' => $fxl,'yaoqingnum' => $yql),array('id' => $mygift['id']));
			}	
		}
			
		//统计
		$csrs = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_users)." WHERE rid= ".$rid." AND uniacid= ".$uniacid." AND status= 1 ");//参赛人数
		$ljtp = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_log)." WHERE rid= ".$rid."") + pdo_fetchcolumn("SELECT sum(xnphotosnum) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."");//累计投票
		$cyrs = $csrs + $reply['hits'] + pdo_fetchcolumn("SELECT sum(hits) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."") + pdo_fetchcolumn("SELECT sum(xnhits) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."") + $reply['xuninum'];//点击
		
		
		//每个奖品的位置
		//虚拟人数据配置
		$now = time();
		if($now-$reply['xuninum_time']>$reply['xuninumtime']){
		    pdo_update($this->table_reply, array('xuninum_time' => $now,'xuninum' => $reply['xuninum']+mt_rand($reply['xuninuminitial'],$reply['xuninumending'])), array('rid' => $rid));
		}
		//虚拟人数据配置
		//参与活动人数
		$total = $reply['xuninum'] + pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_users).' WHERE uniacid=:uniacid and rid=:rid', array(':uniacid' => $uniacid,':rid' => $rid));
		//参与活动人数
		//查询分享标题以及内容变量
		$reply['sharetitle']= $this->get_share($uniacid,$rid,$from_user,$reply['sharetitle']);
		$reply['sharecontent']= $this->get_share($uniacid,$rid,$from_user,$reply['sharecontent']);
		//整理数据进行页面显示
		$myavatar = $avatar;
		$mynickname = $nickname;
		$shareurl = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'fromuser' => $from_user));//分享URL
		$regurl = $_W['siteroot'] .'app/'.$this->createMobileUrl('reg', array('rid' => $rid));//关注或借用直接注册页
		//$mygifturl = $_W['siteroot'] .'app/'.$this->createMobileUrl('tuser', array('rid' => $rid, 'tfrom_user' => $from_user));//我的页面
		$title = $reply['title'] . ' ';
		
		
		$_share['link'] = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'fromuser' => $from_user));//分享URL
		 $_share['title'] = $reply['sharetitle'];
		$_share['content'] =  $reply['sharecontent'];
		$_share['imgUrl'] = toimage($reply['sharephoto']);
		
		$toye = $this->_stopllq('photosvote');
		include $this->template($toye);
		
