<?php
/**
 * 集阅读模块微站定义
 *
 * @author 别具一格
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class ju_readModuleSite extends WeModuleSite {
	public $table_reply = 'ju_read_reply';
	public function doWebList() {
		global $_GPC, $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT * FROM ".tablename($this->table_reply)." WHERE uniacid = '{$_W['uniacid']}' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize .',' .$psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_reply) . " WHERE uniacid = '{$_W['uniacid']}'");
		$pager = pagination($total, $pindex, $psize);
		include $this->template('list');
	}

	public function doWebLogs() {
		global $_GPC, $_W;
		load()->model('mc');
		$reply_id = intval($_GPC['reply_id']);
		$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE id = :reply_id ORDER BY `id` DESC", array(':reply_id' => $reply_id));
		$prizes = iunserializer($reply['prizes']);
		if ($_GPC['op'] == 'setstatus') {
			$log_id = intval($_GPC['log_id']);
			$item = pdo_fetch("SELECT * FROM ".tablename('ju_read_data')." WHERE id = :log_id ORDER BY `id` DESC", array(':log_id' => $log_id));
			if ($item['status'] == 2) {
				pdo_update('ju_read_data',array('status'=>'3'),array('id'=>$log_id));				
			}else{
				pdo_update('ju_read_data',array('status'=>'2'),array('id'=>$log_id));
			}
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT * FROM ".tablename('ju_read_data')." WHERE uniacid = '{$_W['uniacid']}' AND reply_id = '{$reply_id}' ".$condition." ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize .',' .$psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ju_read_data') . " WHERE uniacid = '{$_W['uniacid']}' AND reply_id = '{$reply_id}' ".$condition);
		$pager = pagination($total, $pindex, $psize);
		include $this->template('logs');
	}

	public function doWebPlayStatus() {
		global $_GPC, $_W;
		$reply_id = intval($_GPC['reply_id']);
		$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE id = :reply_id ORDER BY `id` DESC", array(':reply_id' => $reply_id));
		if (empty($reply)) {
			message('活动不存在');
		}else{
			if ($reply['status'] == 1) {
				pdo_update($this->table_reply,array('status'=>2),array('id'=>$reply_id));
			}elseif ($reply['status'] == 2) {
				pdo_update($this->table_reply,array('status'=>1),array('id'=>$reply_id));
			}else{

			}
			header("location:".$this->createWebUrl('list'));
		}
	}

	protected function getcynum($reply_id){
		return pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ju_read_data') . " WHERE reply_id = '{$reply_id}'");
	}

	protected function gethitsnum($reply_id){
		return pdo_fetchcolumn('SELECT SUM(hits) FROM ' . tablename('ju_read_data') . " WHERE reply_id = '{$reply_id}'");
	}

	public function doMobileCheck() {
		global $_W,$_GPC;
		$reply_id = intval($_GPC['reply_id']);
		$log_id = intval($_GPC['log_id']);
		$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE id = :reply_id ORDER BY `id` DESC", array(':reply_id' => $reply_id));
		if (empty($reply)) {
			message("参数错误");
		}
		$log = pdo_fetch("SELECT * FROM ".tablename('ju_read_data')." WHERE id = :log_id ORDER BY `id` DESC", array(':log_id' => $log_id));
		if (empty($log)) {
			message("参数错误");
		}elseif ($log['status'] == 1) {
			message("该用户没有中奖");
		}elseif ($log['status'] == 3) {
			message("该用户已经终结");
		}elseif (empty($log['sn'])) {
			message("兑换码不存在");
		}else{
			if(checksubmit('submit')) {
				load()->model('user');
				$password = $_GPC['password'];
				$sql = 'SELECT * FROM ' . tablename('activity_coupon_password') . " WHERE `uniacid` = :uniacid AND `password` = :password";
				$clerk = pdo_fetch($sql, array(':uniacid' => $_W['uniacid'], ':password' => $password));
				if(!empty($clerk)) {
					$update = array(
						'status' => '3',
					);
					pdo_update('ju_read_data',$update,array('id'=>$log['id']));
					message("兑换成功",$this->createMobileUrl('main',array('id'=>$reply_id)),'success');
				}
				message('密码错误！', referer(), 'error');
			}
			include $this->template('check');
		}
	}

	public function doMobileMain() {
		global $_W,$_GPC;
		$reply_id = intval($_GPC['id']);
		$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE id = :reply_id ORDER BY `id` DESC", array(':reply_id' => $reply_id));
		if (!empty($reply)) {
			if ($reply['starttime'] > time()) {//检测时间是否开始
				message("本次活动尚未开始,敬请期待！");
			}elseif ($reply['endtime'] < time() || $reply['status'] == 0) {//检测时间是否结束或者状态是否为结束
				message("本次活动已经结束，请关注我们后续的活动！");
			}elseif ($reply['status'] == 2) {//检测状态是否暂停
				message("本次活动暂停中");
			}else{//活动大状态正常 检测每天的时间是否正常
				if (empty($_W['openid'])) {
					message("请在微信中访问");
				}
				if (!empty($_GPC['parentopenid'])) {
					$read_log = pdo_fetch("SELECT * FROM ".tablename('ju_read_log')." WHERE reply_id = :reply_id and parentopenid = :parentopenid and readopenid = :readopenid", array(':reply_id' => $reply_id,':parentopenid'=> $_GPC['parentopenid'],':readopenid' => $_W['openid']));
					if (empty($read_log) && $_GPC['parentopenid'] != $_W['openid']) {
						$data = array(
							'uniacid' => $_W['uniacid'],
							'reply_id' => $reply_id,
							'parentopenid' => $_GPC['parentopenid'],
							'readopenid' => $_W['openid'],
							'ceratetime' => TIMESTAMP,
							);
						pdo_insert('ju_read_log',$data);
						pdo_query("update ".tablename('ju_read_data')." set hits=hits+1 where openid = '{$_GPC['parentopenid']}' and reply_id = '{$reply_id}'");
					}
				}
				$log = pdo_fetch("SELECT * FROM ".tablename('ju_read_data')." WHERE reply_id = :reply_id and openid = :openid", array(':reply_id' => $reply_id,':openid' => $_W['openid']));
				if (empty($log)) {
					$insert = array(
						'uniacid' => $_W['uniacid'],
						'reply_id' => $reply_id,
						'openid' => $_W['openid'],
						'status' => '1',
						'createtime' => TIMESTAMP,
						);
					pdo_insert('ju_read_data',$insert);
					$log = pdo_fetch("SELECT * FROM ".tablename('ju_read_data')." WHERE reply_id = :reply_id and openid = :openid", array(':reply_id' => $reply_id,':openid' => $_W['openid']));
				}
				$prizes = iunserializer($reply['prizes']);
				$sytime = $this->endtime($reply['endtime']);
				include $this->template('main');
			}
		}else{
			message("活动不存在");
		}
	}

	public function doMobileDuijiang() {
		global $_W,$_GPC;
		$reply_id = intval($_GPC['replyId']);
		$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE id = :reply_id ORDER BY `id` DESC", array(':reply_id' => $reply_id));
		$prizes = iunserializer($reply['prizes']);
		$itemId = intval($_GPC['itemId']);
		if (empty($_W['openid']) || empty($reply_id)) {
			$status = '0';//参数错误
		}else{
			$log = pdo_fetch("SELECT * FROM ".tablename('ju_read_data')." WHERE reply_id = :reply_id and openid = :openid", array(':reply_id' => $reply_id,':openid' => $_W['openid']));
			if (empty($log)) {
				$data['status'] = '0';//参数错误
				$data['msg'] = '参数错误,请刷新页面后重试';
			}elseif ($log['status'] != '1') {
				$data['status'] = '2';//已领取过奖励
				$data['msg'] = '抱歉，您已领取过奖励，不能重复领取';
			}elseif ($log['hits'] < $prizes[$itemId]['neednum']) {
				$data['status'] = '3';//阅读数不够
				$data['msg'] = '抱歉，您的阅读数不够，不能领取该奖励';
			}elseif ($prizes[$itemId]['prizesy'] < 1) {
				$data['status'] = '4';//奖品不够不够
				$data['msg'] = '抱歉，该奖品已经被领取完了';
			}else{
				$prizes[$itemId]['prizesy'] = $prizes[$itemId]['prizesy'] - 1;
				$update = array(
					'status' => '2',
					'sn' => date('md') . random(4, 1),
					'prizeid' => $itemId,
					);
				pdo_update('ju_read_data',$update,array('id'=>$log['id']));
				$data['status'] = '1';
				$data['Result'] = $update['sn'];
			}
		}
		exit(json_encode($data));
	}

	protected function endtime($e) {
		$time = $e - time();
		if ($time > 0) {
			$d = floor($time / 86400);
			$h = floor(($time - $d * 86400)/3600);
			$m = floor(($time - $d * 86400 - $h * 3600)/60);
			return $d . '天' . $h . '小时' . $m . '分钟';
		}else{
			return '已结束';
		}
		
	}

}