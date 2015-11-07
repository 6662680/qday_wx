<?php
/**
 * 刮刮乐模块微站定义
 *
 * @author 华轩科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Hx_cardsModuleSite extends WeModuleSite {
	public $table_reply = 'hx_cards_reply';
	public $table_award = 'hx_cards_award';
	public $table_fans = 'hx_cards_fans';
	public $table_share = 'hx_cards_share';

	public function doWebList() {
		global $_GPC, $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT * FROM ".tablename($this->table_reply)." WHERE uniacid = '{$_W['uniacid']}' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize .',' .$psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_reply) . " WHERE uniacid = '{$_W['uniacid']}'");
		$pager = pagination($total, $pindex, $psize);
		include $this->template('list');
	}

	public function doWebAwardlist(){
		global $_GPC, $_W;
		$reply_id = intval($_GPC['reply_id']);
		if (empty($reply_id)) {
			message('非法访问');
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT * FROM ".tablename($this->table_award)." WHERE reply_id = '{$reply_id}' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize .',' .$psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_award) . " WHERE reply_id = '{$reply_id}'");
		$pager = pagination($total, $pindex, $psize);
		include $this->template('awardlist');
	}
	public function doMobileMyaward() {
		global $_GPC, $_W;
		$reply_id = intval($_GPC['reply_id']);
		$uid = $_W['member']['uid'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT * FROM ".tablename($this->table_award)." WHERE uid = '{$uid}' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize .',' .$psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_award) . " WHERE uid = '{$uid}'");
		$pager = pagination($total, $pindex, $psize);
		//load()->func('tpl');
		include $this->template('list');
	}

	public function doMobileDetail() {
		global $_W, $_GPC;
		$uniacid=$_W['uniacid'];
		load()->model('mc');
		//这个操作被定义用来呈现 微站首页导航图标
		$id = intval($_GPC['id']);
		if (empty($id)) {
			message('非法访问');
		}
		$reply = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE id = :id", array(':id' => $id));
		//print_r($reply);
		$share_from = base64_decode(urldecode($_GPC['share_from']));
		$share_from = isset($_GPC['share_from']) ? $share_from : $_W['openid'];
		//$reply['link'] = isset($reply['share_url']) ? $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=entry&id='.$id.'&do=detail&m=hx_cards' : $reply['share_url'];
		if (!empty($reply)) {	
			if (empty($_W['fans']['from_user'])) {
				$errorCode = 10999;
				$errorMsg = '亲，抽奖需要您先关注我们的平台哦～';
			}else{
				$from_user = $_W['fans']['from_user'];
				$fans = pdo_fetch("SELECT fanid,uid FROM ". tablename('mc_mapping_fans') ." WHERE `openid`='$from_user' LIMIT 1");
				$uid = '0';
				if ($fans['uid'] != '0') {
					$uid = $fans['uid'];
				}else{
					$uid = mc_update($uid, array('email' => md5($from_user).'@qdaygroup.com'));
					if (!empty($fans['fanid']) && !empty($uid)) {
						pdo_update('mc_mapping_fans', array('uid' => $uid), array('fanid' => $fans['fanid']));
					}
				}
				$profile = mc_fetch($uid);
				$sharenum = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_share) . " WHERE reply_id = '{$id}' AND share_from = '{$_W['openid']}'");
				$addplaytime = floor($sharenum/(empty($reply['zfcs'])?1:$reply['zfcs'])) * $reply['zjcs'];
				$awardnum = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_award) . " WHERE reply_id = '{$id}' AND uid = '{$uid}'");
				$awardfans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE reply_id = '{$id}' AND uid = '{$uid}'");
				$t = mktime(0, 0, 0, date("m",time()), date("d",time()), date("y",time()));
				if (empty($awardfans)) {
					$data1 = array(
						'reply_id' => $id,
						'from_user' => $from_user,
						'uid' => $uid,
						'todaynum' => '0',
						'totalnum' => '0',
						'awardnum' => '0',
						'last_time' => time(),
						'createtime' => time(),
						);
					pdo_insert($this->table_fans,$data1);
				}elseif ($awardfans['last_time'] < $t) {
					$data2 = array(
						'todaynum' => '0',
						'last_time' => time(),
						);
					pdo_update($this->table_fans,$data2,array('id' => $awardfans['id']));
				}
				if (time() <= $reply['starttime'] || time() >= $reply['endtime'] || $reply['status'] == 0) {
					$errorCode = 1;
					$errorMsg = '亲，本次抽奖活动已结束，请关注我们的下一次活动，谢谢～';
				}elseif ($reply['status'] == 2) {
					$errorCode = 1;
					$errorMsg = '亲，本次抽奖活动暂停中，请随时关注我们平台的通知信息，谢谢～';
				}elseif ($reply['groupid'] != 0 && $reply['groupid'] != $profile['groupid']) {
					$errorCode = 1;
					$errorMsg = '亲，本次抽奖活动仅允许特定的会员组参加，请随时关注我们平台的其他活动哦，谢谢～';
				}elseif ($reply['need_num'] > $profile[$reply['need_type']]) {
					$errorCode = 1;
					$errorMsg = '亲，本次抽奖活动需要'.$this->getcreditname($reply['need_type']).$reply['need_num'].'个，您的'.$this->getcreditname($reply['need_type']).'仅剩'.$profile[$reply['need_type']].'个';
				}elseif ($awardnum >= $reply['awardnum']) {
					$errorCode = 1;
					$errorMsg = '亲，本次抽奖活动最多允许中奖'.$reply['awardnum'].'次，您已经中奖'.$awardnum.'次';
				}elseif ($addplaytime + $awardfans['totalnum'] >= $reply['playnum']) {
					$errorCode = 1;
					$errorMsg = '亲，本次抽奖活动最多允许参加'.$reply['playnum'].'次，您已经参加'.$awardfans['totalnum'].'次';
				}elseif ($awardfans['todaynum'] >= $reply['dayplaynum']) {
					$errorCode = 1;
					$errorMsg = '亲，本次抽奖活动每天最多允许参加'.$reply['dayplaynum'].'次，您今天已经参加'.$awardfans['todaynum'].'次';
				}else{
					$errorCode = 0;
					$errorMsg = '123';
				}
			}
			if (empty($reply['noprize'])) {
				$failedInfo = '哎呀，肯定姿势不对';
			}else{
				$arr = explode("\n", $reply['noprize']);
				$k = rand(0,count($arr)-1);
				$failedInfo = $arr[$k];
			}
			$jsondata = array(
				'id' => $reply['id'],
				'rid' => $reply['rid'],
				'uid' => $uid,
				'title' => urlencode($reply['title']),
				'alias' => base64_encode($_W['fans']['from_user']),
				'created_time' => date('Y-m-d H:i:s',$reply['createtime']),
				'start_time' => date('Y-m-d H:i:s',$reply['starttime']),
				'end_time' => date('Y-m-d H:i:s',$reply['endtime']),
				'notice' => '',
				'costPoint' => empty($reply['need_num']) ? 0 : $reply['need_num'],
				'costName' => $this->getcreditname($reply['need_type']),
				'givePoint' => $reply['give_num'],
				'giveName' => $this->getcreditname($reply['give_type']),
				'successInfo' => '',
				'failedInfo' => $failedInfo,
				'hasPoint' => false,
				'lotteryAgain' => '1',
				'subscribe' => $reply['share_url'],
				'logout' => 'index.php?i='.$_W['uniacid'].'&c=entry&do=checkprize&m=hx_cards',
				'errorCode' => $errorCode,
				'errorMsg' => urlencode($errorMsg),
				);
			$json = urldecode(json_encode($jsondata));
			include $this->template('detail');
		}else{
			exit('参数错误');
		}
	}

	public function doMobileShareData() {
		global $_W,$_GPC;
		if(empty($_SERVER["HTTP_X_REQUESTED_WITH"]) || strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])!="xmlhttprequest"){
			exit('非法访问');
		}
		$id = intval($_GPC['id']);
		$data = array(
			'uinacid' => $_W['uniacid'],
			'reply_id' => $id,
			'share_from' => $_GPC['from'],
			'share_time' => time(),
			);
		pdo_insert('hx_cards_share',$data);
		echo json_encode($data);
	}

	public function doMobileCheckprize() {
		global $_W, $_GPC;
		if(empty($_SERVER["HTTP_X_REQUESTED_WITH"]) || strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])!="xmlhttprequest"){
			exit('非法访问');
		}
		load()->model('activity');
		$id = $_GPC['id'];
		$reply = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE id = :id", array(':id' => $id));
		//print_r($reply);
		if (!empty($reply)) {	
			if (empty($_W['fans']['from_user'])) {//防止错误提交的数据
				$errorCode = 10999;
			}else{
				$from_user = $_W['fans']['from_user'];
				$fans = pdo_fetch("SELECT fanid,uid FROM ". tablename('mc_mapping_fans') ." WHERE `openid`='$from_user' LIMIT 1");
				$uid = '0';
				if ($fans['uid'] != '0') {
					$uid = $fans['uid'];
				}else{
					$uid = mc_update($uid, array('email' => md5($from_user).'@qdaygroup.com'));
					if (!empty($fans['fanid']) && !empty($uid)) {
						pdo_update('mc_mapping_fans', array('uid' => $uid), array('fanid' => $fans['fanid']));
					}
				}
				$profile = mc_fetch($uid);
				$awardnum = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_award) . " WHERE reply_id = '{$id}' AND uid = '{$uid}'");
				$awardfans = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE reply_id = '{$id}' AND uid = '{$uid}'");
				$t = mktime(0, 0, 0, date("m",time()), date("d",time()), date("y",time()));
				if (empty($awardfans)) {
					$data1 = array(
						'reply_id' => $id,
						'from_user' => $from_user,
						'uid' => $uid,
						'todaynum' => '0',
						'totalnum' => '0',
						'awardnum' => '0',
						'last_time' => time(),
						'createtime' => time(),
						);
					pdo_insert($this->table_fans,$data1);
				}elseif ($awardfans['last_time'] < $t) {
					$data2 = array(
						'todaynum' => '0',
						'last_time' => time(),
						);
					pdo_update($this->table_fans,$data2,array('id' => $awardfans['id']));
				}
				if (time() <= $reply['starttime'] || time() >= $reply['endtime'] || $reply['status'] == 0) {
					$errorCode = 1;
				}elseif ($reply['status'] == 2) {
					$errorCode = 1;
				}elseif ($reply['groupid'] != 0 && $reply['groupid'] != $profile['groupid']) {
					$errorCode = 1;
				}elseif ($reply['need_num'] > $profile[$reply['need_type']]) {
					$errorCode = 1;
				}elseif ($awardnum >= $reply['awardnum']) {
					$errorCode = 1;
				}elseif ($awardfans['totalnum'] >= $reply['playnum']) {
					$errorCode = 1;
				}elseif ($awardfans['todaynum'] >= $reply['dayplaynum']) {
					$errorCode = 1;
				}else{
					mc_credit_update($uid,$reply['need_type'],'-'.$reply['need_num'],array('1','刮刮乐 消耗 '.$this->getcreditname($reply['need_type']).'：'.$reply['need_num']));
					$data3 = array(
						'todaynum' => $awardfans['todaynum'] + 1,
						'totalnum' => $awardfans['totalnum'] + 1,
						'last_time' => time(),
						);
					pdo_update($this->table_fans,$data3,array('id' => $awardfans['id']));
					/*中奖部分代码开始*/
					$rate = $reply['rate'];
					$prizes = iunserializer($reply['prizes']);
					$p_num = $prizes['p1_num'] + $prizes['p2_num'] + $prizes['p3_num'] + $prizes['p4_num'];
					$arr['p1'] = round(100 * $rate * $prizes['p1_num']/$p_num);
					$arr['p2'] = round(100 * $rate * $prizes['p2_num']/$p_num);
					$arr['p3'] = round(100 * $rate * $prizes['p3_num']/$p_num);
					$arr['p4'] = round(100 * $rate * $prizes['p4_num']/$p_num);
					$arr['p5'] = 10000 - $arr['p1'] - $arr['p2'] - $arr['p3'] - $arr['p4'];
					if ($awardnum >= $p_num) {
						$result = 'p5';
					}else{
						$result = $this->get_rand($arr);//返回结果为键值p1,p2,p3,p4
					}
					/*中奖部分代码结束*/
					//echo $result;
					if ($result == 'p5') {
						if ($reply['give_num'] != 0) {
							$errorCode = 1;
							mc_credit_update($uid,$reply['give_type'],$reply['give_num'],array('1','刮刮乐 未中奖赠送 '.$this->getcreditname($reply['give_type']).'：'.$reply['give_num']));	
						}
					}else{
						if ($reply['give_num'] != 0 && $reply['onlynone'] != 1) {
							$give_point = $reply['give_num'];
							$give_name = $this->getcreditname($reply['give_type']);
							mc_credit_update($uid,$reply['give_type'],$reply['give_num'],array('1','刮刮乐 未中奖赠送 '.$this->getcreditname($reply['give_type']).'：'.$reply['give_num']));
						}
						$errorCode = 0;
						if ($result == 'p1') {
							if ($prizes['p1_type'] != 2 && $prizes['p1_type'] != 3 && $prizes['p1_type'] != 4) {//积分类奖品
								$point = $prizes['p1_score'];
								$point_name = $this->getcreditname($prizes['p1_type']);
								mc_credit_update($uid,$prizes['p1_type'],$point,array('1','刮刮乐 中奖获得 '.$this->getcreditname($prizes['p1_type']).'：'.$point));
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=mc&a=bond&do=credits&credittype='.$prizes['p1_type'];
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => $this->getcreditname($prizes['p1_type']).':'.$point,
									'prizetype' => $prizes['p1_type'],
									'level' => '1',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
							}elseif($prizes['p1_type'] == 2){//非积分类奖品:折扣券
								$point = 0;
								$couponid = $prizes['p1_score'];
								$coupon = activity_coupon_info($couponid, $_W['uniacid']);
								$ret = activity_coupon_grant($uid, $couponid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($coupon['credittype']).':'.$coupon['credit'];
									mc_credit_update($uid,$coupon['credittype'],$coupon['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($coupon['credittype']).'：'.$coupon['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=coupon&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$coupon['title'],
									'prizetype' => 'coupon',
									'level' => '1',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '折扣券';
								$type = '折扣券';
								$value = 1;
							}elseif($prizes['p1_type'] == 3){//非积分类奖品:代金券
								$point = 0;
								$tokenid = $prizes['p1_score'];
								$token = activity_token_info($tokenid, $_W['uniacid']);
								$ret = activity_token_grant($uid, $tokenid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($token['credittype']).':'.$token['credit'];
									mc_credit_update($uid,$token['credittype'],$token['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($token['credittype']).'：'.$token['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=token&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$token['title'],
									'prizetype' => 'token',
									'level' => '1',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '代金券';
								$type = '代金券';
								$value = 1;
							}elseif($prizes['p1_type'] == 4){//非积分类奖品:真实物品
								$point = 0;
								$goodsid = $prizes['p1_score'];
								$goods = activity_exchange_info($goodsid, $_W['uniacid']);
								$ret = activity_goods_grant($uid, $goodsid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($goods['credittype']).':'.$goods['credit'];
									mc_credit_update($uid,$goods['credittype'],$goods['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($goods['credittype']).'：'.$goods['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=goods&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$goods['title'],
									'prizetype' => 'goods',
									'level' => '1',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '真实物品券';
								$type = '真实物品券';
								$value = 1;
							}
							$award = array(
								'title' => $title,
								'value' => $value,
								'type' => $type,
								'remsg' => $remsg,
								'point' => $point,
								'point_name' => $point_name,
								'give_point' => $give_point,
								'give_name' => $give_name,
								'level' => '1',
								'detail_url' => $detail_url
								);
						}elseif ($result == 'p2') {
							if ($prizes['p2_type'] != 2 && $prizes['p2_type'] != 3 && $prizes['p2_type'] != 4) {//积分类奖品
								$point = $prizes['p2_score'];
								$point_name = $this->getcreditname($prizes['p2_type']);
								mc_credit_update($uid,$prizes['p2_type'],$point,array('1','刮刮乐 中奖获得 '.$this->getcreditname($prizes['p2_type']).'：'.$point));
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=mc&a=bond&do=credits&credittype='.$prizes['p2_type'];
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => $this->getcreditname($prizes['p2_type']).':'.$point,
									'prizetype' => $prizes['p2_type'],
									'level' => '2',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
							}elseif($prizes['p2_type'] == 2){//非积分类奖品:折扣券
								$point = 0;
								$couponid = $prizes['p2_score'];
								$coupon = activity_coupon_info($couponid, $_W['uniacid']);
								$ret = activity_coupon_grant($uid, $couponid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($coupon['credittype']).':'.$coupon['credit'];
									mc_credit_update($uid,$coupon['credittype'],$coupon['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($coupon['credittype']).'：'.$coupon['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=coupon&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$coupon['title'],
									'prizetype' => 'coupon',
									'level' => '2',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '折扣券';
								$type = '折扣券';
								$value = 1;
							}elseif($prizes['p2_type'] == 3){//非积分类奖品:代金券
								$point = 0;
								$tokenid = $prizes['p2_score'];
								$token = activity_token_info($tokenid, $_W['uniacid']);
								$ret = activity_token_grant($uid, $tokenid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($token['credittype']).':'.$token['credit'];
									mc_credit_update($uid,$token['credittype'],$token['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($token['credittype']).'：'.$token['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=token&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$token['title'],
									'prizetype' => 'token',
									'level' => '2',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '代金券';
								$type = '代金券';
								$value = 1;
							}elseif($prizes['p2_type'] == 4){//非积分类奖品:真实物品
								$point = 0;
								$goodsid = $prizes['p2_score'];
								$goods = activity_exchange_info($goodsid, $_W['uniacid']);
								$ret = activity_goods_grant($uid, $goodsid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($goods['credittype']).':'.$goods['credit'];
									mc_credit_update($uid,$goods['credittype'],$goods['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($goods['credittype']).'：'.$goods['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=goods&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$goods['title'],
									'prizetype' => 'goods',
									'level' => '2',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '真实物品券';
								$type = '真实物品券';
								$value = 1;
							}
							$award = array(
								'title' => $title,
								'value' => $value,
								'type' => $type,
								'remsg' => $remsg,
								'point' => $point,
								'point_name' => $point_name,
								'give_point' => $give_point,
								'give_name' => $give_name,
								'level' => '2',
								'detail_url' => $detail_url
								);

						}elseif ($result == 'p3') {
							if ($prizes['p3_type'] != 2 && $prizes['p3_type'] != 3 && $prizes['p3_type'] != 4) {//积分类奖品
								$point = $prizes['p3_score'];
								$point_name = $this->getcreditname($prizes['p3_type']);
								mc_credit_update($uid,$prizes['p3_type'],$point,array('1','刮刮乐 中奖获得 '.$this->getcreditname($prizes['p3_type']).'：'.$point));
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=mc&a=bond&do=credits&credittype='.$prizes['p3_type'];
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => $this->getcreditname($prizes['p3_type']).':'.$point,
									'prizetype' => $prizes['p3_type'],
									'level' => '3',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
							}elseif($prizes['p3_type'] == 2){//非积分类奖品:折扣券
								$point = 0;
								$couponid = $prizes['p3_score'];
								$coupon = activity_coupon_info($couponid, $_W['uniacid']);
								$ret = activity_coupon_grant($uid, $couponid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($coupon['credittype']).':'.$coupon['credit'];
									mc_credit_update($uid,$coupon['credittype'],$coupon['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($coupon['credittype']).'：'.$coupon['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=coupon&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$coupon['title'],
									'prizetype' => 'coupon',
									'level' => '3',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '折扣券';
								$type = '折扣券';
								$value = 1;
							}elseif($prizes['p3_type'] == 3){//非积分类奖品:代金券
								$point = 0;
								$tokenid = $prizes['p3_score'];
								$token = activity_token_info($tokenid, $_W['uniacid']);
								$ret = activity_token_grant($uid, $tokenid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($token['credittype']).':'.$token['credit'];
									mc_credit_update($uid,$token['credittype'],$token['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($token['credittype']).'：'.$token['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=token&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$token['title'],
									'prizetype' => 'token',
									'level' => '3',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '代金券';
								$type = '代金券';
								$value = 1;
							}elseif($prizes['p3_type'] == 4){//非积分类奖品:真实物品
								$point = 0;
								$goodsid = $prizes['p3_score'];
								$goods = activity_exchange_info($goodsid, $_W['uniacid']);
								$ret = activity_goods_grant($uid, $goodsid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($goods['credittype']).':'.$goods['credit'];
									mc_credit_update($uid,$goods['credittype'],$goods['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($goods['credittype']).'：'.$goods['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=goods&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$goods['title'],
									'prizetype' => 'goods',
									'level' => '3',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '真实物品券';
								$type = '真实物品券';
								$value = 1;
							}
							$award = array(
								'title' => $title,
								'value' => $value,
								'type' => $type,
								'remsg' => $remsg,
								'point' => $point,
								'point_name' => $point_name,
								'give_point' => $give_point,
								'give_name' => $give_name,
								'level' => '3',
								'detail_url' => $detail_url,
								);

						}elseif ($result == 'p4') {
							if ($prizes['p4_type'] != 2 && $prizes['p4_type'] != 3 && $prizes['p4_type'] != 4) {//积分类奖品
								$point = $prizes['p4_score'];
								$point_name = $this->getcreditname($prizes['p4_type']);
								mc_credit_update($uid,$prizes['p4_type'],$point,array('1','刮刮乐 中奖获得 '.$this->getcreditname($prizes['p4_type']).'：'.$point));
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=mc&a=bond&do=credits&credittype='.$prizes['p4_type'];
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => $this->getcreditname($prizes['p4_type']).':'.$point,
									'prizetype' => $prizes['p4_type'],
									'level' => '4',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
							}elseif($prizes['p4_type'] == 2){//非积分类奖品:折扣券
								$point = 0;
								$couponid = $prizes['p4_score'];
								$coupon = activity_coupon_info($couponid, $_W['uniacid']);
								$ret = activity_coupon_grant($uid, $couponid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($coupon['credittype']).':'.$coupon['credit'];
									mc_credit_update($uid,$coupon['credittype'],$coupon['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($coupon['credittype']).'：'.$coupon['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=coupon&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$coupon['title'],
									'prizetype' => 'coupon',
									'level' => '4',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '折扣券';
								$type = '折扣券';
								$value = 1;
							}elseif($prizes['p4_type'] == 3){//非积分类奖品:代金券
								$point = 0;
								$tokenid = $prizes['p4_score'];
								$token = activity_token_info($tokenid, $_W['uniacid']);
								$ret = activity_token_grant($uid, $tokenid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($token['credittype']).':'.$token['credit'];
									mc_credit_update($uid,$token['credittype'],$token['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($token['credittype']).'：'.$token['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=token&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$token['title'],
									'prizetype' => 'token',
									'level' => '4',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '代金券';
								$type = '代金券';
								$value = 1;
							}elseif($prizes['p4_type'] == 4){//非积分类奖品:真实物品
								$point = 0;
								$goodsid = $prizes['p4_score'];
								$goods = activity_exchange_info($goodsid, $_W['uniacid']);
								$ret = activity_goods_grant($uid, $goodsid, 'hx_cards', '刮刮乐中奖获得');
								if(is_error($ret)) {//领取错误做等值处理
									$remsg = ' 由于'.$ret['message'].' 奖品转换为等值'.$this->getcreditname($goods['credittype']).':'.$goods['credit'];
									mc_credit_update($uid,$goods['credittype'],$goods['credit'],array('1','刮刮乐 等价 '.$this->getcreditname($goods['credittype']).'：'.$goods['credit'].'原因：'.$ret['message']));
								}
								$detail_url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=activity&a=goods&do=mine';
								$awarddata = array(
									'reply_id' => $id,
									'uid' => $uid,
									'name' => '折扣券:'.$goods['title'],
									'prizetype' => 'goods',
									'level' => '4',
									'createtime' => time(),
									'consumetime' => time(),
									'status' => 1,//1已发放
									);
								$title = '真实物品券';
								$type = '真实物品券';
								$value = 1;
							}
							$award = array(
								'title' => $title,
								'value' => $value,
								'type' => $type,
								'remsg' => !empty($remsg) ? $remsg : ' ',
								'point' => $point,
								'point_name' => $point_name,
								'give_point' => $give_point,
								'give_name' => $give_name,
								'level' => '4',
								'detail_url' => $detail_url
								);

						}
						pdo_insert($this->table_award,$awarddata);
						pdo_update($this->table_fans,array('awardnum' => $awardfans['awardnum']+1 ),array('id'=>$awardfans['id']));
					}
				}
			}
		}
		$data['code'] = $errorCode;
		$data['msg'] = $errorMsg;
		$data['data'] = $award;
		echo json_encode($data);
	}

	protected function getcreditname($key) {
		$creditnames = uni_setting($_W['uniacid'], array('creditnames'));
		if($creditnames) {
			foreach($creditnames['creditnames'] as $index=>$creditname) {
				if($creditname['enabled'] == 0) {
					unset($creditnames['creditnames'][$index]);
				}
			}
			$select_credit = implode(', ', array_keys($creditnames['creditnames']));
		} else {
			$select_credit = '';
		}
		return $creditnames['creditnames'][$key]['title'];
	}

	protected function get_rand($proArr) { 
		$result = ''; 
		//概率数组的总概率精度 
		$proSum = array_sum($proArr);
		//概率数组循环
		foreach ($proArr as $key => $proCur) { 
			$randNum = mt_rand(1, $proSum); 
			if ($randNum <= $proCur) { 
				$result = $key; 
				break;
				} else { 
					$proSum -= $proCur; 
				} 
			} 
		unset ($proArr);
		return $result; 
	}

	protected function getfansnum($reply_id){
		$num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_fans) . " WHERE reply_id = '{$reply_id}'");
		return $num;
	}
	protected function getawardnum($reply_id){
		$num = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_award) . " WHERE reply_id = '{$reply_id}'");
		return $num;
	}

	protected function getreplyname($reply_id){
		$num = pdo_fetch('SELECT title FROM ' . tablename($this->table_reply) . " WHERE id = '{$reply_id}'");
		return $num['title'];
	}
}