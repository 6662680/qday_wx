<?php
/**
 * 分享达人
 */
defined('IN_IA') or exit('Access Denied');

class Eso_shareModuleSite extends WeModuleSite {

	public $table_reply = 'eso_share_reply';
	public $table_list   = 'eso_share_list';
	public $table_data   = 'eso_share_data';

	public function getProfileTiles() {

	}

	public function getHomeTiles($keyword = '') {
		$urls = array();
		$list = pdo_fetchall("SELECT name, id FROM ".tablename('rule')." WHERE module = 'eso_share'".(!empty($keyword) ? " AND name LIKE '%{$keyword}%'" : ''));
		if (!empty($list)) {
			foreach ($list as $row) {
				$urls[] = array('title'=>$row['name'], 'url'=> $this->createMobileUrl('eso_share', array('id' => $row['id'])));
			}
		}
		return $urls;
	}

	public function doMobileeso_share() {
		//分享达人分享页面显示。
		global $_GPC,$_W;

		if (empty($_GPC['rid'])) {
			$rid = $_GPC['id'];
		}else{
			$rid = $_GPC['rid'];
		}
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		}

		$fromuser = authcode(base64_decode($_GPC['from_user']), 'DECODE');
		$eso_shareip = getip();
		$now = time();
		//取得分享达人数据
		if(!empty($fromuser)) {
			$list = pdo_fetch("SELECT * FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and rid = '".$rid."' limit 1" );

			if(!empty($list)){
				$eso_shareid = $list['id'];
				//取得分享详细数据，判断浏览者是否是同一人24小时内同一IP访问
				$eso_share_data = pdo_fetch("SELECT * FROM ".tablename($this->table_data)." WHERE from_user = '".$fromuser."' and rid = '".$rid."' and eso_shareip= '".$eso_shareip."' limit 1" );
				if(!empty($eso_share_data)){
					$sid		=	$eso_share_data['id'];
					$eso_sharetime	=	$eso_share_data['eso_sharetime'];
					$updatetime	=	$now-$eso_sharetime;
					//访问如果是在24小时后，更新分享数据，更新分享数
					if($updatetime > 24*3600){
						$updatedata = array(
							'userid'	=> $_W['member']['uid'],
							'eso_sharetime' => $now
						);
						$updatelist = array(
							'eso_sharenum' => $list['eso_sharenum']+1,
							'userid'	=> $_W['member']['uid'],
							'eso_sharetime' => $now
						);
						pdo_update($this->table_data, $updatedata, array('id' => $sid));
						pdo_update($this->table_list, $updatelist, array('id' => $eso_shareid));
					}
				}else{
					$insertdata = array(
						'rid' => $rid,
						'from_user' => $fromuser,
						'eso_shareip'	=> $eso_shareip,
						'userid'	=> $_W['member']['uid'],
						'eso_sharetime' => $now
					);
					$updatelist = array(
						'eso_sharenum' => $list['eso_sharenum']+1,
						'userid'	=> $_W['member']['uid'],
						'eso_sharetime' => $now
					);
					pdo_insert($this->table_data, $insertdata);
					pdo_update($this->table_list, $updatelist, array('id' => $eso_shareid));
				}
			}else{
				$insertdata = array(
					'rid' => $rid,
					'from_user' => $fromuser,
					'eso_shareip'	=> $eso_shareip,
					'userid'	=> $_W['member']['uid'],
					'eso_sharetime' => $now
				);
				$insertlist = array(
					'rid' => $rid,
					'from_user' => $fromuser,
					'userid'	=> $_W['member']['uid'],
					'eso_sharenum' => '1',
					'eso_sharetime'=>$now
				);
				pdo_insert($this->table_data, $insertdata);
				pdo_insert($this->table_list, $insertlist);

			}
		}
		//整理数据进行页面显示
		$imgurl=$_W['attachurl'] . $reply['picture'];
		$title = $reply['title'];
		$loclurl=$_W['siteroot'].'app/'.$this->createMobileUrl('eso_share', array('rid' => $rid, 'from_user' => $_GPC['from_user']));

		if ($reply['status']) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			if (strpos($user_agent, 'MicroMessenger') === false) {
				echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
			} else {
				$unlist = uni_accounts($_W['uniacid']);
				$acna = "";
				$acid = "";
				$qrcode = "";
				$subscribeurl = "";
				foreach($unlist AS $item) {
					if ($item['subscribeurl']) {
						$acna = $item['name'];
						$acid = $item['acid'];
						$qrcode = $_W['attachurl'].'qrcode_'.$item['acid'].'.jpg';
						$subscribeurl = $item['subscribeurl'];
						$original = $item['original'];
						if (strpos(strtolower($user_agent),'ipad') === false && strpos(strtolower($user_agent),'iphone') === false) {
							$subscribeurl = "weixin://profile/".$original;
						}
						break;
					}
				}
				$zz = $_COOKIE["z".$this->table_reply.$rid];
				pdo_update($this->table_reply,array('r'=>$reply['r']+1), array('rid' => $rid));
				include $this->template('default');
			}
		} else {
			echo '<h1>分享达人活动已结束!</h1>';
			exit;
		}
	}
	public function doMobileeso_sharez() {
		//点赞
		global $_GPC;

		if (empty($_GPC['rid'])) {
			$rid = $_GPC['id'];
		}else{
			$rid = $_GPC['rid'];
		}
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			if ($reply) {
				$zz = $_COOKIE["z".$this->table_reply.$rid];
				if ($zz != "y") {
					setcookie("z".$this->table_reply.$rid, 'y', time()+3600*24);
					$reply['z']++;
					pdo_update($this->table_reply,array('z'=>$reply['z']), array('rid' => $rid));
				}else{
					setcookie("z".$this->table_reply.$rid, 'n', time()+3600*24);
					$reply['z']--;
					pdo_update($this->table_reply,array('z'=>$reply['z']), array('rid' => $rid));
				}
			}
			echo $reply['z'];
			exit();
		}
		echo "0";
		exit();

	}
	public function doWebeso_sharelist($rid, $state) {
		global $_GPC, $_W;
		checklogin();
		$weid = $_W['account']['weid'];//当前公众号ID
		$id = intval($_GPC['id']);
		if (isset($_GPC['idArr'])) {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				if ($id > 0) {
					pdo_delete($this->table_list, " id = ".$id);
				}
			}
			message('删除成功！', create_url('index/entry', array('do' => 'eso_sharelist', 'm' => 'eso_share', 'id' => $id, 'page' => $_GPC['page'])));
		}
		//
		$rules = pdo_fetchall('SELECT `id`,`name` FROM '.tablename('rule').' WHERE `module`=\'eso_share\'');
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		//取得分享达人列表
		$list_eso_share = pdo_fetchall('SELECT * FROM '.tablename($this->table_list).' WHERE rid= :rid order by `eso_sharenum` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $id) );
		$listtotal = pdo_fetchall('SELECT * FROM '.tablename($this->table_list).' WHERE rid= :rid order by `id` desc ', array(':rid' => $id) );
		$total = count($listtotal);
		$pager = pagination($total, $pindex, $psize);
		include $this->template('eso_sharelist');

	}
	public function doWebstatus( $rid = 0) {
		global $_GPC;
		$rid = $_GPC['rid'];
		echo $rid;
		$insert = array(
			'status' => $_GPC['status']
		);

		pdo_update($this->table_reply,$insert,array('rid' => $rid));
		message('模块操作成功！', referer(), 'success');
	}
	public function doWebdos( $id = 0) {
		global $_GPC;
		$rid = $_GPC['rid'];
		$id = $_GPC['id'];
		echo $id;
		$insert = array(
			'status' => $_GPC['status']
		);

		pdo_update($this->table_list,$insert,array('id' => $id,'rid' => $rid));
		message('屏蔽操作成功！', create_url('site/entry/eso_sharelist', array('m' => 'eso_share', 'id' => $rid, 'page' => $_GPC['page'])));
	}
	public function doWebeso_sharedata($rid, $state) {
		global $_GPC, $_W;
		checklogin();
		$weid = $_W['account']['weid'];//当前公众号ID
		$id = intval($_GPC['id']);
		if (checksubmit('delete')) {
			pdo_delete($this->table_data, " id IN ('".implode("','", $_GPC['select'])."')");
			message('删除成功！', create_url('site/entry/eso_sharedata', array('m' => 'eso_share', 'id' => $id, 'page' => $_GPC['page'])));
		}
		$rules = pdo_fetchall('SELECT `id`,`name` FROM '.tablename('rule').' WHERE `module`=\'eso_share\'');
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		//取得分享点击详细数据
		$list_eso_sharedata = pdo_fetchall('SELECT * FROM '.tablename($this->table_data).' WHERE rid= :rid order by `eso_sharetime` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':rid' => $id) );
		$listtotal = pdo_fetchall('SELECT * FROM '.tablename($this->table_data).' WHERE rid= :rid order by `eso_sharetime` desc ', array(':rid' => $id) );
		$total = count($listtotal);
		$pager = pagination($total, $pindex, $psize);
		include $this->template('eso_sharedata');

	}
	public function doWebdeldata( $id = 0) {
		global $_GPC;
		$rid = $_GPC['rid'];
		$id = $_GPC['id'];
		if ($_GPC['idArr']) {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				if ($id > 0) {
					pdo_delete($this->table_data, " id = ".$id);
				}
			}
			message('删除成功！', create_url('site/entry/eso_sharedata', array('m' => 'eso_share', 'id' => $rid, 'page' => $_GPC['page'])));
		}else{
			if (!empty($id)) {
				pdo_delete($this->table_data, " id = ".$id);
				message('删除成功！', create_url('site/entry/eso_sharedata', array('m' => 'eso_share', 'id' => $rid, 'page' => $_GPC['page'])));
			}
		}
	}
}