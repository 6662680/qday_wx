<?php
/**
 * 一元夺宝模块微站定义
 *
 * @author 封遗
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');
require_once "jssdk.class.php";

class Feng_duobaoModuleSite extends WeModuleSite {
//会员信息提取
	public function __construct(){
		global $_W;
		load()->func('communication');
		$openid = $_W['openid'];
		$account = account_fetch($_W['acid']);//获取公众号信息
		$profile = pdo_fetch("SELECT id FROM " . tablename('feng_member') . " WHERE uniacid ='{$_W['uniacid']}' and from_user = '{$_W['openid']}'");
		if (empty($profile)) {
			if ($_W['account']['level']==2) {
			$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$account['access_token']['token']."&openid=".$openid."&lang=zh_CN";
			$re = ihttp_get($url);//ihttp_get()封装的 http GET 请求方法
			if ($re['code'] == 200) {
				$content = json_decode($re['content'],true);
				if ($content['subscribe'] == 1) { //此人已关注
					$data = array(
						'uniacid' => $_W['uniacid'],
						'from_user' => $_W['openid'],
						'nickname' => $content['nickname'],
						'avatar' => $content['headimgurl'],
					);
					if (empty($profile)) {
						pdo_insert('feng_member', $data);
					}else{
						pdo_update('feng_member', $data, array('id' => $profile['id']));
					}
					/*pdo_update('mc_mapping_fans',array('follow' => 1),array('acid'=>$_W['acid'],'openid'=>$openid));*/
				}
			}
			}else{
				
			}
		}
	}
/*＝＝＝＝＝＝＝＝＝＝＝＝＝＝以下为微信端页面管理＝＝＝＝＝＝＝＝＝＝＝＝＝＝*/
	public function doMobileIndex() {
		$this->__mobile(__FUNCTION__);
	}
//商品详情
	public function doMobiledetails() {
		$this->__mobile(__FUNCTION__);
	}
//购买
	public function doMobileexchange() {
		$this->__mobile(__FUNCTION__);
	}
//提交订单
	public function doMobilepostorder() {
		$this->__mobile(__FUNCTION__);
	}
//兑换记录
	public function doMobilemyorder() {
		$this->__mobile(__FUNCTION__);
	}
//我的兑换码
	public function doMobilemycodes() {
		$this->__mobile(__FUNCTION__);
	}
//兑换码加载	
	public function doMobileshowrecord() {
		$this->__mobile(__FUNCTION__);
	}
//个人中心
	public function doMobileprofile() {
		$this->__mobile(__FUNCTION__);
	}
//个人资料
	public function doMobileprodata() {
		$this->__mobile(__FUNCTION__);
	}
	public function doMobileAjaxypsubmit() {
		$this->__mobile(__FUNCTION__);
	}
//往期开奖
	public function doMobileperiod() {
		$this->__mobile(__FUNCTION__);
	}
//获得的商品
	public function doMobileprize(){
		$this->__mobile(__FUNCTION__);
	}
//玩儿法介绍
	public function doMobileintroduction() {
		global $_W, $_GPC;
		checkauth();
		include $this->template('introduction');
	}
//付款
	public function doMobilePay() {
		$this->__mobile(__FUNCTION__);
	}
//关注引导
	public function doMobileattention() {
		$share_data = $this->module['config'];
		include $this->template('attention');
	}
//付款结果返回
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
			$order = pdo_fetch("SELECT * FROM " . tablename('feng_record') . " WHERE id ='{$params['tid']}'");//获取商品ID
			if ($order['status'] != 1) {
				if ($params['result'] == 'success') {
					$data['status'] = 1;
					$codes = pdo_fetch("SELECT * FROM " . tablename('feng_goodscodes') . " WHERE s_id ='{$order['sid']}'");//获取商品code
					$sidm = pdo_fetch("SELECT * FROM " . tablename('feng_goodslist') . " WHERE id ='{$order['sid']}'");//获取商品详情
					$s_codes=unserialize($codes['s_codes']);//转换商品code
					$c_number=intval($codes['s_len']);;
					if ($fee<$c_number) {
						//计算购买的夺宝码
						$data['s_codes']=array_slice($s_codes,0,$fee);
						$data['s_codes']=serialize($data['s_codes']);
						$r_codes['s_len']=$c_number-$fee;
						$r_codes['s_codes']=array_slice($s_codes,$fee,$r_codes['s_len']);
						$r_codes['s_codes']=serialize($r_codes['s_codes']);
						$sid_mess['canyurenshu']=$sidm['canyurenshu']+$fee;
						$sid_mess['shengyurenshu']=$sidm['shengyurenshu']-$fee;
						$sid_mess['scale']=round(($sid_mess['canyurenshu'] / $sidm['zongrenshu'])*100);

						//执行数据库更新
						pdo_update('feng_goodscodes', $r_codes, array('id' => $codes['id']));
						pdo_update('feng_goodslist', $sid_mess, array('id' => $sidm['id']));
					}elseif ($fee==$c_number) {
						$data['s_codes']=$codes['s_codes'];
						/*$data['s_codes']=serialize($data['s_codes']);*/
						$r_codes['s_len']=0;
						$r_codes['s_codes']=NULL;

						//计算获奖的code和获奖人
						$s_record = pdo_fetchall("SELECT * FROM " . tablename('feng_record') . " WHERE uniacid = '{$_W['uniacid']}' and sid ='{$order['sid']}'");//获取商品所有交易记录
						if (empty($sidm['q_user_code'])) {
							$wincode=mt_rand(1,$sidm['zongrenshu']);
							$wincode=$wincode+1000000;
						}else{
							$wincode=$sidm['q_user_code'];
						}
						//计算获奖人
						foreach ($s_record as $value) {
							$ss_codes=unserialize($value['s_codes']);//转换商品code
							for ($i=0; $i < count($ss_codes) ; $i++) { 
								if ($ss_codes[$i]==$wincode) {
									$sid_mess['q_user']=$value['from_user'];
									break;
								}
							}
						}
						if(empty($sid_mess['q_user'])){
							$ss_codes=unserialize($data['s_codes']);//转换商品code
							for ($i=0; $i < count($ss_codes) ; $i++) { 
								if ($ss_codes[$i]==$wincode) {
									$sid_mess['q_user']=$_W['fans']['from_user'];
									break;
								}
							}
						}
						$sid_mess['canyurenshu']=$sidm['zongrenshu'];
						$sid_mess['shengyurenshu']=0;
						$sid_mess['q_user_code']=$wincode;
						$pro_m = pdo_fetch("SELECT * FROM " . tablename('feng_member') . " WHERE uniacid = '{$_W['uniacid']}' and from_user ='{$sid_mess['q_user']}'");//用户信息
						$sid_mess['q_uid']=$pro_m['nickname'];
						$sid_mess['status']=1;
						$sid_mess['q_end_time']=TIMESTAMP;
						$sid_mess['scale']=100;

						//模板消息推送
						$wechats = pdo_fetch("SELECT * FROM " . tablename('feng_wechat') . " WHERE uniacid ='{$_W['uniacid']}'");
						if ($_W['account']['level']==2) {
						$json_data = array(
						  'touser'=>$sid_mess['q_user'],
						  'template_id'=>$wechats['win_mess'],
						  'url'=>'',
						  'topcolor'=>'#FF0000',
						  "data"=>array("title"=>array('value' =>'尊敬的客户' ,'color' =>'#173177' ),
						  				"headinfo"=>array('value' =>'恭喜您，中奖啦！' ,'color' =>'#FF0000' ),
						  				"program"=>array('value' =>'一元夺宝','color'=>'#FF0000' ),
						  				"result"=>array('value' =>'获得了我们的大奖' ,'color'=>'#FF0000' ),
						  				"remark"=>array('value' =>'请进入个人中心查看中奖详情，祝你生活愉快！' ,'color' =>'#173177' )
						  				)
							);
						$template_mess=json_encode($json_data);
						$this->send_template_message($template_mess);
						}

						//生成新一期商品
						if ($sidm['periods']<=$sidm['maxperiods']) {
							$new_sid=array(
								'uniacid'=>$_W['uniacid'],
								'sid'=>$sidm['sid'],
								'title'=>$sidm['title'],
								'price'=>$sidm['price'],
								'zongrenshu'=>$sidm['zongrenshu'],
								'canyurenshu'=>0,
								'shengyurenshu'=>$sidm['zongrenshu'],
								'periods'=>$sidm['periods']+1,
								'maxperiods'=>$sidm['maxperiods'],
								'picarr'=>$sidm['picarr'],
								'content'=>$sidm['content'],
								'createtime'=>TIMESTAMP,
								'pos'=>$sidm['pos'],
								'status'=>$sidm['status'],
							);
							pdo_insert(feng_goodslist,$new_sid);
							$id = pdo_insertid();

							$CountNum=intval($sidm['price']);
							$new_codes=array();
							for($i=1;$i<=$CountNum;$i++){
								$new_codes[$i]=1000000+$i;
							}shuffle($new_codes);$new_codes=serialize($new_codes);

							$data1['uniacid'] = $_W['uniacid'];
							$data1['s_id'] = $id;
							$data1['s_len'] = $CountNum;
							$data1['s_codes'] = $new_codes;
							$data1['s_codes_tmp'] = $new_codes;

							$ret = pdo_insert(feng_goodscodes, $data1);
							unset($new_codes);
						
						}

						//执行数据库操作
						pdo_update('feng_goodscodes', $r_codes, array('id' => $codes['id']));
						pdo_update('feng_goodslist', $sid_mess, array('id' => $sidm['id']));
					}else{
						message('错误！');
					}
				}

				pdo_update('feng_record', $data, array('id' => $params['tid']));
			}
			
			if ($params['type'] == $credit) {
				message('支付成功！', $this->createMobileUrl('myorder'), 'success');
			} else {
				message('支付成功！', '../../app/' . $this->createMobileUrl('myorder'), 'success');
			}
		}
	}

/*＝＝＝＝＝＝＝＝＝＝＝＝＝＝以下为后台管理＝＝＝＝＝＝＝＝＝＝＝＝＝＝*/
//商品管理
	private function getGoodsStatus($status){
		$status = intval($status);
		if ($status == 1) {
			return '下架';
		} elseif ($status == 2) {
			return '上架';
		} else {
			return '未知';
		}
	}
//商品管理
	public function doWebGoods() {
		$this->__web(__FUNCTION__);
	}
//往期商品
	public function doWebshowperiod() {
		$this->__web(__FUNCTION__);
	}
//交易记录
	public function doWebRecord() {
		$this->__web(__FUNCTION__);
	}
//中奖订单
	public function doWebOrder() {
		$this->__web(__FUNCTION__);
	}
//中奖订单发货
	public function doWebsendprize() {
		$this->__web(__FUNCTION__);
	}
//会员管理
	public function doWebMember() {
		$this->__web(__FUNCTION__);
	}
//兑换码加载	
	public function doWebshowrecords() {
		$this->__web(__FUNCTION__);
	}
//商品交易记录	
	public function doWebsrecords() {
		$this->__web(__FUNCTION__);
	}

	public function __web($f_name){
		global $_W,$_GPC;
		checklogin();
		$uniacid=$_W['uniacid'];
		load()->func('tpl');
		include_once  'web/'.strtolower(substr($f_name,5)).'.php';
	}
	
	public function __mobile($f_name){
		global $_W,$_GPC;
		checkauth();
		$uniacid=$_W['uniacid'];
/*		$share_data = pdo_fetch("SELECT * FROM ".tablename('feng_wechat')." WHERE uniacid = '{$uniacid}'");*/
		$share_data = $this->module['config'];
		//$this->
		$to_url = $_W['siteroot'].'app/'.$this->createMobileUrl('attention', array());
		include_once  'mobile/'.strtolower(substr($f_name,8)).'.php';
	}
	
/*＝＝＝＝＝＝＝＝＝＝＝＝＝＝以下为其他函数＝＝＝＝＝＝＝＝＝＝＝＝＝＝*/
	public function send_template_message($data){
		global $_W, $_GPC;

		$uniacid=$_W['uniacid'];
		$list = pdo_fetch("SELECT * FROM ".tablename('feng_wechat')." WHERE uniacid = '{$uniacid}'");
		if (empty($list['uniacid'])) {
			$list['uniacid']=$_W['uniacid'];
			$list['appid']=$_W['account']['key'];
			$list['appsecret']=$_W['account']['secret'];
			$list['lasttime']=1420477087;

			$ret = pdo_insert(feng_wechat, $list);
		}
		
		if (time()>($list['lasttime']+7200)) {
			$url_token="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$list['appid']."&secret=".$list['appsecret'];
			$res_token=$this->http_request($url_token);
			$result=json_decode($res_token, true);
			$list['access_token']=$result['access_token'];
			$list['appid']=$_W['account']['key'];
			$list['appsecret']=$_W['account']['secret'];
			$list['lasttime']=time();
			$ret = pdo_update(feng_wechat, $list, array('uniacid'=>$_W['uniacid']));
		}

		$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$list['access_token'];
		$res=$this->http_request($url, $data);
		return json_decode($res,true);
	}

	public function http_request($url, $data=NULL){
		$curl=curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output=curl_exec($curl);
		curl_close($curl);
		return $output;
	}

}