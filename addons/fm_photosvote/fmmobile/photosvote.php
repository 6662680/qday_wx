<?php
/**
 * 女神来了模块定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
if ($reply['ipannounce'] == 1) {
			$announce = pdo_fetchall("SELECT * FROM " . tablename($this->table_announce) . " WHERE uniacid= '{$uniacid}' AND rid= '{$rid}' ORDER BY id DESC");
			
		}
		//幻灯片
        $banners = pdo_fetchall("SELECT * FROM " . tablename($this->table_banners) . " WHERE enabled=1 AND uniacid= '{$uniacid}' AND rid= '{$rid}' ORDER BY displayorder ASC");
        foreach ($banners as &$banner) {
            if (substr($banner['link'], 0, 5) != 'http:') {
                $banner['link'] = "http://" . $banner['link'];
            }
        }
        unset($banner);
		//赞助商
		if ($reply['isindex'] == 1) {
			$advs = pdo_fetchall("SELECT * FROM " . tablename($this->table_advs) . " WHERE enabled=1 AND uniacid= '{$uniacid}'  AND rid= '{$rid}' ORDER BY displayorder ASC");
			foreach ($advs as &$adv) {
				if (substr($adv['link'], 0, 5) != 'http:') {
					$adv['link'] = "http://" . $adv['link'];
				}
			}
			unset($adv);
		}
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = empty($reply['indextpxz']) ? 10 : $reply['indextpxz'];
		//取得用户列表
		$where = '';
		if (!empty($_GPC['keyword'])) {
				$keyword = $_GPC['keyword'];
				if (is_numeric($keyword)) 
					$where .= " AND id = '".$keyword."'";
				else 				
					$where .= " AND nickname LIKE '%{$keyword}%'";
			
		}
		$where .= " AND status = '1'";
		$userlist = pdo_fetchall('SELECT * FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid and rid = :rid '.$where.' order by `id` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $uniacid,':rid' => $rid) );
		
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_users).' WHERE uniacid= :uniacid and rid = :rid '.$where.'', array(':uniacid' => $uniacid,':rid' => $rid));
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
		//查询是否参与活动
		//if(!empty($from_user)) {
		//    $usergift = pdo_fetch("SELECT * FROM ".tablename($this->table_users)." WHERE uniacid = :uniacid and from_user = :from_user and rid = :rid", array(':uniacid' => $uniacid,':from_user' => $from_user,':rid' => $rid));
		   
			//$follow = pdo_fetch("SELECT follow FROM ".tablename('mc_mapping_fans')." WHERE uniacid = :uniacid and openid = :from_user", array(':uniacid' => $uniacid,':from_user' => $from_user));
			
		//}
			
		//统计
		$csrs = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_users)." WHERE rid= ".$rid." AND uniacid= ".$uniacid."");//参赛人数
		$ljtp = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->table_log)." WHERE rid= ".$rid."") + pdo_fetchcolumn("SELECT sum(xnphotosnum) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."");//累计投票
		$cyrs = $csrs + $reply['hits'] + pdo_fetchcolumn("SELECT sum(hits) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."") + pdo_fetchcolumn("SELECT sum(xnhits) FROM ".tablename($this->table_users)." WHERE rid= ".$rid."") + $reply['xuninum'];//参与人数
		
		
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
		$lingjiangurl = $_W['siteroot'] .'app/'.$this->createMobileUrl('lingjiang', array('rid' => $rid));//领奖URL
		$mygifturl = $_W['siteroot'] .'app/'.$this->createMobileUrl('photosvoteview', array('rid' => $rid));//我的页面
		$shouquan = base64_encode($_SERVER ['HTTP_HOST'].'anquan_ma_photosvote');
		$title = $reply['title'] . ' ';
		
		
		$_share['link'] = $_W['siteroot'] .'app/'.$this->createMobileUrl('shareuserview', array('rid' => $rid,'fromuser' => $from_user));//分享URL
		 $_share['title'] = $reply['sharetitle'];
		$_share['content'] =  $reply['sharecontent'];
		$_share['imgUrl'] = toimage($reply['sharephoto']);
		
		$toye = $this->_stopllq('photosvote');
		include $this->template($toye);
		
