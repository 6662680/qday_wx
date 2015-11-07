<?php
/**
 * 拍卖模块微站定义
 *
 * @author 封遗
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Feng_auctionModuleSite extends WeModuleSite {
//会员信息提取
	public function __construct(){
		global $_W;
		load()->func('communication');
		$openid = $_W['openid'];
		if (!empty($openid)) {
			$account = account_fetch($_W['acid']);//获取公众号信息
			$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$account['access_token']['token']."&openid=".$openid."&lang=zh_CN";
			$re = ihttp_get($url);//ihttp_get()封装的 http GET 请求方法
			if ($re['code'] == 200) {
				$content = json_decode($re['content'],true);
				if ($content['subscribe'] == 1) { //此人已关注
					$data = array(
						'uniacid' => $_W['uniacid'],
						'from_user' => $openid,
						'nickname' => $content['nickname'],
						'avatar' => $content['headimgurl'],
					);
					$profile = pdo_fetch("SELECT id FROM " . tablename('auction_member') . " WHERE uniacid ='{$_W['uniacid']}' and from_user = '{$openid}'");
					if (empty($profile)) {
						pdo_insert('auction_member', $data);
					}else{
						pdo_update('auction_member', $data, array('id' => $profile['id']));
					}
				}else{
					$userinfo = mc_oauth_userinfo();
					$data = array(
						'uniacid' => $_W['uniacid'],
						'from_user' => $userinfo['openid'],
						'nickname' => $userinfo['nickname'],
						'avatar' => $userinfo['avatar'],
					);
					$profile = pdo_fetch("SELECT id FROM " . tablename('auction_member') . " WHERE uniacid ='{$_W['uniacid']}' and from_user = '{$userinfo['openid']}'");
					if (empty($profile)) {
						pdo_insert('auction_member', $data);
					}else{
						pdo_update('auction_member', $data, array('id' => $profile['id']));
					}
				}
			}
		}
	}

/*＝＝＝＝＝＝＝＝＝＝＝＝＝＝以下为微信端页面管理＝＝＝＝＝＝＝＝＝＝＝＝＝＝*/
	//微信端首页
	public function doMobileIndex() {
		$this->__mobile(__FUNCTION__);
	}
	//已完成的拍卖
	public function doMobilearea() {
		$this->__mobile(__FUNCTION__);
	}
	//微信端拍品详情
	public function doMobiledetails() {
		$this->__mobile(__FUNCTION__);
	}
	//微信端个人中心
	public function doMobileProfile() {
		$this->__mobile(__FUNCTION__);
	}
	//微信端个人资料
	public function doMobileprodata() {
		global $_W,$_GPC;
		/*checkauth();*/
		$weid = $_W['uniacid'];
		if (empty($_W['fans']['from_user'])) {
			message('openid为空');
		}
		$people = pdo_fetch("SELECT * FROM " . tablename('auction_member') . " WHERE uniacid= '{$weid}' AND from_user= '{$_W['fans']['from_user']}'");
		include $this->template('prodata');
	}
	public function doMobileAjaxypsubmit() {
		global $_W,$_GPC;
		/*checkauth();*/
		$weid = $_W['uniacid'];
		$result="";
		$data=array(
			'uniacid'=>$weid,
			'from_user'=>$_W['fans']['from_user'],
			'realname'=>$_GPC['acceptname'],
			'nickname'=>$_GPC['nickname'],
			'mobile'=>$_GPC['phone'],
			'address'=>$_GPC['addr'],
			'bankcard'=>$_GPC['bankcard'],
			'bankname'=>$_GPC['bankname'],
			'alipay'=>$_GPC['alipay'],
			'aliname'=>$_GPC['aliname'],
		);

		if (empty($_GPC['id'])) {
			if(pdo_insert('auction_member',$data))
			{
				$result="您的资料修改成功！";
			}
			else
			{
				$result="您的资料修改失败！";
			}
		}else{
			if(pdo_update('auction_member', $data, array('id' => $_GPC['id'])))
			{
				$result="您的资料修改成功！";
			}
			else
			{
				$result="您的资料修改失败！";
			}
		}
		echo $result;
	}
	//出价
	public function doMobileexchange() {
		$this->__mobile(__FUNCTION__);
	}
	public function doMobilepostorder(){
		$this->__mobile(__FUNCTION__);
	}
	//出价记录
	public function doMobilerecord(){
		$this->__mobile(__FUNCTION__);
	}
	public function doMobilerecording(){
		$this->__mobile(__FUNCTION__);
	}
	public function doMobilerecordf(){
		$this->__mobile(__FUNCTION__);
	}
	public function doMobileredetails(){
		$this->__mobile(__FUNCTION__);
	}
	//充值订单提交
	public function doMobilerecharge() {
		$this->__mobile(__FUNCTION__);
	}
	public function doMobilereorder() {
		$this->__mobile(__FUNCTION__);
	}
	public function doMobilepay() {
		$this->__mobile(__FUNCTION__);
	}
	public function payResult($params){
		global $_W, $_GPC;

		$uniacid=$_W['uniacid'];
		$fee = intval($params['fee']);
		$data = array('status' => $params['result'] == 'success' ? 1 : 0);
		$paytype = array('credit' => '1', 'wechat' => '3', 'alipay' => '2');
		$data['paytype'] = $paytype[$params['type']];
		if ($params['type'] == 'wechat') {
			$data['transid'] = $params['tag']['transaction_id'];
		}
		if ($params['from'] == 'return') {
			$member = pdo_fetch("SELECT * FROM " . tablename('auction_member') . " WHERE from_user ='{$_W['fans']['from_user']}'");
			$balance['balance'] = $fee + $member['balance'];
			pdo_update('auction_member', $balance, array('id' => $member['id']));
			pdo_update('auction_recharge', $data, array('ordersn' => $params['tid']));
			
			if ($params['type'] == $credit) {
				message('支付成功！', $this->createMobileUrl('profile'), 'success');
			} else {
				message('支付成功！', '../../app/' . $this->createMobileUrl('profile'), 'success');
			}
		}
	}
	//账户明细
	public function doMobileaccount() {
		$this->__mobile(__FUNCTION__);
	}
	//获得的拍品
	public function doMobilemyauction() {
		$this->__mobile(__FUNCTION__);
	}
	//物流信息
	public function doMobilelogistics() {
		$this->__mobile(__FUNCTION__);
	}
	//付余款
	public function doMobilepayment() {
		$this->__mobile(__FUNCTION__);
	}
	//提现管理
	public function doMobilewithdrawals() {
		$this->__Mobile(__FUNCTION__);
	}
	public function doMobileajaxwith() {
		$this->__Mobile(__FUNCTION__);
	}
	//拍卖介绍
	public function doMobileintroduction() {
		global $_W,$_GPC;
		$share_data = $this->module['config'];
		$content = $share_data['content'];
		include $this->template('introduction');
	}
/*＝＝＝＝＝＝＝＝＝＝＝＝＝＝以下为后台管理＝＝＝＝＝＝＝＝＝＝＝＝＝＝*/
	//幻灯片管理
	public function doWebAdv() {
		global $_W, $_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$list = pdo_fetchall("SELECT * FROM " . tablename('auction_adv') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
		} elseif ($operation == 'post') {
			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				$data = array(
					'weid' => $_W['uniacid'],
					'advname' => $_GPC['advname'],
					'link' => $_GPC['link'],
					'enabled' => intval($_GPC['enabled']),
					'displayorder' => intval($_GPC['displayorder']),
					'thumb'=>$_GPC['thumb']
				);

				if (!empty($id)) {
					pdo_update('auction_adv', $data, array('id' => $id));
				} else {
					pdo_insert('auction_adv', $data);
					$id = pdo_insertid();
				}
				message('更新幻灯片成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
			}
			$adv = pdo_fetch("select * from " . tablename('auction_adv') . " where id=:id and weid=:weid limit 1", array(":id" => $id, ":weid" => $_W['uniacid']));
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$adv = pdo_fetch("SELECT id FROM " . tablename('auction_adv') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
			if (empty($adv)) {
				message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('adv', array('op' => 'display')), 'error');
			}
			pdo_delete('auction_adv', array('id' => $id));
			message('幻灯片删除成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
		} else {
			message('请求方式不存在');
		}
		include $this->template('adv', TEMPLATE_INCLUDEPATH, true);
	}
	//后台拍品管理
	public function doWebGoods() {
		$this->__web(__FUNCTION__);
	}
	//后台交易记录
	public function doWebRecord() {
		$this->__web(__FUNCTION__);
	}

	public function doWebdelrecharge(){
		global $_W,$_GPC;
		$orderid = intval($_GPC['id']);
		if (pdo_delete('auction_recharge', array('id' => $orderid))) {
			message('充值记录删除成功', $this->createWebUrl('record'), 'success');
		} else {
			message('订单不存在或已被删除', $this->createWebUrl('record'), 'error');
		}
	}
	//拍品发货管理
	public function doWebSendout() {
		$this->__web(__FUNCTION__);
	}
	public function doWebSendprize() {
		$this->__web(__FUNCTION__);
	}
	//会员管理
	public function doWebMember() {
		$this->__web(__FUNCTION__);
	}
	//提现管理
	public function doWebwithdrawals() {
		$this->__web(__FUNCTION__);
	}

	public function __web($f_name){
		global $_W,$_GPC;
		checklogin();
		$weid = $_W['uniacid'];
		load()->func('tpl');
		include_once  'web/'.strtolower(substr($f_name,5)).'.php';
	}
	
	public function __mobile($f_name){
		global $_W,$_GPC;
		/*checkauth();*/
		$weid = $_W['uniacid'];
		$share_data = $this->module['config'];
		$to_url = "http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
		$pro_realname = pdo_fetch("SELECT nickname FROM ".tablename('auction_member')." WHERE uniacid = '{$weid}' and from_user ='{$_W['openid']}' ");
		if (empty($pro_realname['nickname'])) {
			message('请先填写您的资料！', $this->createMobileUrl('prodata'), 'warning');
		}
		include_once  'mobile/'.strtolower(substr($f_name,8)).'.php';
	}

	public function Judge(){
		global $_W,$_GPC;
		$weid = $_W['uniacid'];
		$nowtime = TIMESTAMP;
		$goods = pdo_fetchall("SELECT * FROM ".tablename('auction_goodslist')." WHERE uniacid = '{$weid}' and end_time < '{$nowtime}' ");
		if (!empty($goods)) {
			foreach ($goods as $key => $value) {
				if (empty($value['q_uid'])) {
					$redata = pdo_fetch("SELECT * FROM " . tablename('auction_record') . " WHERE uniacid = '{$weid}' and sid ='{$value['id']}' ORDER BY createtime DESC limit 1");
					$data['q_uid']=$redata['nickname'];
					$data['q_user']=$redata['from_user'];
					pdo_update('auction_goodslist', $data, array('id' => $value['id']));

					$all_res = pdo_fetchall("SELECT * FROM ".tablename('auction_record')." WHERE uniacid = '{$weid}' and sid = '{$value['id']}' and bond > 0");
					foreach ($all_res as $re_key => $re_value) {
						if ($re_value['from_user'] !=$data['q_user'] ) {
							$rech_data['uniacid'] = $weid;
							$rech_data['from_user'] = $re_value['from_user'];
							$rech_data['nickname'] = $re_value['nickname'];
							$rech_data['uid'] = $re_value['uid'];
							$rech_data['ordersn'] = $re_value['ordersn'];
							$rech_data['status'] = 1;
							$rech_data['paytype'] = 4;
							$rech_data['price'] = $re_value['bond'];
							$rech_data['createtime'] = $nowtime;
							pdo_insert('auction_recharge', $rech_data);
							$m_data = pdo_fetch("SELECT * FROM ".tablename('auction_member')." WHERE uniacid = '{$weid}' and id = '{$re_value['uid']}' ");
							$mem_data['balance'] = $re_value['bond'] + $m_data['balance'];
							pdo_update('auction_member', $mem_data, array('id' => $m_data['id']));
 						}
					}
				}
			}
		}
	}
}