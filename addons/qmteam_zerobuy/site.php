<?php
/**
 * 零元购模块微站定义
 *
 * @author 青盟
 * @url http://www.54lm.com/
 */
defined('IN_IA') or exit('Access Denied');

class Qmteam_zerobuyModuleSite extends WeModuleSite {
	/*
	 * WEB 列表
	 */
	public function doWebList() {
		global $_W, $_GPC;
		load()->func('tpl');
		$act = isset($_GPC['act']) ? $_GPC['act'] : 'add';
		$info = array();
		$list = pdo_fetchall("SELECT * FROM " . tablename('zerobuy_list') ." where weid = ".$_W['weid']);
		if($act == 'edit'){
			$info = pdo_fetch("SELECT * FROM " . tablename('zerobuy_list') . " WHERE id = ".$_GPC['id']);
		}
		if($act == 'delete'){
			pdo_delete('zerobuy_list', array('id'=>$_GPC['id']));
			message('删除商品成功！', $this->createWebUrl('list', array('op' => 'display')), 'success');
		}
		if($_GPC['submit']){
			$data = array();
			$data['title'] = $_GPC['title'];
			$data['weid'] = $_W['weid'];
			$data['price'] = $_GPC['price'];
			$data['inventory'] = $_GPC['inventory'];
			$data['info'] = $_GPC['info'];
			$data['status'] = $_GPC['status'];
			$data['thumb'] = $_GPC['thumb'];
			if($act == 'add'){
				pdo_insert('zerobuy_list', $data);
				message('添加商品成功！', $this->createWebUrl('list', array('op' => 'display')), 'success');
			}else{
				pdo_update('zerobuy_list', $data, array('id'=>$_GPC['id']));
				message('更新商品成功！', $this->createWebUrl('list', array('op' => 'display')), 'success');
			}
		}
		include $this->template('list');
	}
	/*
	 * WEB 活动详情
	 */
	public function doWebDetail() {
		global $_W, $_GPC;
		load()->func('tpl');
		//更新活动状态
		$this->update_status();
		$act = isset($_GPC['act']) ? $_GPC['act'] : 'add';
		$info = array();
		$list = pdo_fetchall("SELECT d.*,l.title as goods_title FROM " . tablename('zerobuy_detail') . " d left join " . tablename('zerobuy_list') . " l on d.lid=l.id WHERE d.weid=".$_W['weid']);
		$product_list = pdo_fetchall("SELECT * FROM " . tablename('zerobuy_list')." WHERE status=1 AND weid = ".$_W['weid']);
		$rule_list = pdo_fetchall("SELECT * FROM " . tablename('zerobuy_rule')." where weid = ".$_W['weid']);
		if($act == 'edit'){
			$info = pdo_fetch("SELECT * FROM " . tablename('zerobuy_detail') . " WHERE id = ".$_GPC['id']);
		}
		if($act == 'clear'){
			$draw_info = pdo_fetch("SELECT * FROM " . tablename('zerobuy_detail') . " WHERE id = ".$_GPC['id']);
		}
		if($act == 'delete'){
			pdo_delete('zerobuy_detail', array('id'=>$_GPC['id']));
			message('删除活动成功！', $this->createWebUrl('detail', array('op' => 'display')), 'success');
		}
		if($_GPC['submit']){
			$data = array();
			$data['title'] = $_GPC['title'];
			$data['lid'] = $_GPC['lid'];
			$data['rid'] = $_GPC['rid'];
			$data['weid'] = $_W['weid'];
			$data['starttime'] = strtotime($_GPC['starttime']);
			$data['endtime'] = strtotime($_GPC['endtime']);
			$data['zerobuy_price'] = $_GPC['zerobuy_price'];
			$data['exchange'] = $_GPC['exchange'];
			if($act == 'add'){
				pdo_insert('zerobuy_detail',$data);
				message('添加活动成功！', $this->createWebUrl('detail', array('op' => 'display')), 'success');
			}else{
				pdo_update('zerobuy_detail',$data, array('id'=>$_GPC['id']));
				message('更新活动成功！', $this->createWebUrl('detail', array('op' => 'display')), 'success');
			}
		}
		include $this->template('detail');
	}
	/*
	 * WEB 规则
	 */
	public function doWebActivity_rule() {
		global $_W, $_GPC;
		$act = isset($_GPC['act']) ? $_GPC['act'] : 'add';
		$info = array();
		$list = pdo_fetchall("SELECT * FROM " . tablename('zerobuy_rule')." where weid = ".$_W['weid']);
		if($act == 'edit'){
			$info = pdo_fetch("SELECT * FROM " . tablename('zerobuy_rule') . " WHERE id = ".$_GPC['id']);
		}
		if($act == 'delete'){
			pdo_delete('zerobuy_rule', array('id'=>$_GPC['id']));
			message('删除活动规则成功！', $this->createWebUrl('activity_rule', array('op' => 'display')), 'success');
		}
		if($_GPC['submit']){
			$data = array();
			$data['title'] = $_GPC['title'];
			$data['weid'] = $_W['weid'];
			$data['rule'] = $_GPC['rule'];
			$data['rule_draw'] = $_GPC['rule_draw'];
			if($act == 'add'){
				pdo_insert('zerobuy_rule', $data);
				message('添加活动规则成功！', $this->createWebUrl('activity_rule', array('op' => 'display')), 'success');
			}else{
				pdo_update('zerobuy_rule', $data, array('id'=>$_GPC['id']));
				message('更新活动规则成功！', $this->createWebUrl('activity_rule', array('op' => 'display')), 'success');
			}
		}
		include $this->template('activity_rule');
	}
	/*
	 * MOBILE 活动列表
	 */
	public function doMobileList() {
		global $_W, $_GPC;
		checkauth();
		//更新活动状态
		$this->update_status();
		$now = time();
		$act = isset($_GPC['act']) ? $_GPC['act'] : 'now';
		if($act == 'now'){
			$list = pdo_fetchall("SELECT l.*,d.* FROM " . tablename('zerobuy_detail') . " d left join " . tablename('zerobuy_list') . " l on d.lid=l.id WHERE d.weid = ".$_W['weid']." AND d.status=2 order by endtime");
		}elseif ($act == 'pass'){
			$list = pdo_fetchall("SELECT l.*,d.* FROM " . tablename('zerobuy_detail') . " d left join " . tablename('zerobuy_list') . " l on d.lid=l.id WHERE d.weid = ".$_W['weid']." AND d.status in (3,4) order by endtime desc");
		}else{
			$list = pdo_fetchall("SELECT l.*,d.* FROM " . tablename('zerobuy_detail') . " d left join " . tablename('zerobuy_list') . " l on d.lid=l.id WHERE d.weid = ".$_W['weid']." AND d.status=1 order by endtime desc");
		}
		include $this->template('list');
	}
	/*
	 * MOBILE 详情页
	 */
	public function doMobileDetail() {
		global $_W, $_GPC;
		$modules = uni_modules();
		$info = pdo_fetch("SELECT l.title as goods_title,l.info,l.price,l.thumb,d.*,r.title as rule_title,r.rule FROM " . tablename('zerobuy_detail') . " d left join " . tablename('zerobuy_list') . " l on d.lid=l.id left join ".tablename('zerobuy_rule')." r on d.rid=r.id WHERE d.id=".$_GPC['id']);
		if($info['status'] == 4){
			$btn_info = '已开奖';
		}elseif ($info['status'] == 1){
			$btn_info = '即将开始';
		}elseif ($info['status'] == 2){
			$btn_info = '立即参与';
			//免费参加次数是否已经使用
			if($modules['qmteam_zerobuy']['config']['dayjoin'] == 1){
				$where = " WHERE uid='".$_W['member']['uid']."' AND did=".$info['id']." AND jointime>".strtotime(date('Y-m-d'));
			}else{
				$where = " WHERE uid='".$_W['member']['uid']."' AND did=".$info['id'];
			}
			$free = pdo_fetch("SELECT * FROM " . tablename('zerobuy_code') . $where);
			if(!empty($free)){
				$btn_info = '已参与';
			}
		}else{
			$btn_info = '等待开奖';
		}

		if($info['status'] == 4){
			$member_info = pdo_fetch("SELECT * FROM " . tablename('mc_members') . " WHERE uid=".$info['winner_uid']);
		}

		include $this->template('detail');
	}
	/*
	 * 积分兑换抽奖次数
	 */
	public function doMobileCredit() {
		global $_W, $_GPC;
		if(!$this->check_mc_follow()){
			if(empty($_W['account']['subscribeurl'])){
				$this->msg('error','请关注本公众平台后再参与活动!');
			}
			$this->msg('redirect',$_W['account']['subscribeurl']);
		}
		checkauth();
		if(empty($_W['member']['mobile'])){
				//如果未填写手机号码，跳转到会员中心
				$this->msg('uc', '<P>请先完善您的个人资料！</p><P><font color="red">注意</font>:姓名和手机号码必填。</p><P>个人资料是将来兑奖的依据。</p>');
		}
		//用户信息
		$member_info = pdo_fetch("SELECT * FROM " . tablename('mc_members') . " WHERE uid=".$_W['member']['uid']);
		//活动信息
		$info = pdo_fetch("SELECT l.title as goods_title,l.info,l.price,l.thumb,d.*,r.title as rule_title,r.rule FROM " . tablename('zerobuy_detail') . " d left join " . tablename('zerobuy_list') . " l on d.lid=l.id left join ".tablename('zerobuy_rule')." r on d.rid=r.id WHERE d.id=".$_GPC['id']);
		if($info['status'] == 1){
			$this->msg('error', '活动还未开始，请耐心等待！');
		}
		if($info['status'] == 3 || $info['status'] ==4){
			$this->msg('error', '活动已经结束！');
		}
		$num = floor($member_info['credit1']/$info['exchange']);
		if($_GPC['act'] == 'exchange'){
			if($num < 1){
				$this->msg('error', '积分不足');
			}
			$msg = $this->make_code($_GPC['id']);
			//更行用户表
			load()->model('mc');
			mc_credit_update($_W['member']['uid'], 'credit1',-$info['exchange'],array($_W['member']['uid'],"参与零元购活动:".$info['title']));
			$this->msg('success', $msg);
		}
	}
	/*
	 * 用户抽奖码
	 */
	public function doMobileUsercode(){
		global $_W,$_GPC;
		checkauth();
		$now = time();
		$code_info = pdo_fetchall("SELECT c.*,d.title,d.status,d.endtime,d.winner_uid,d.win_code,l.title as goods_title FROM " . tablename('zerobuy_detail')." d right join ".tablename('zerobuy_code')." c on c.did=d.id left join ".tablename('zerobuy_list')." l on d.lid=l.id WHERE c.uid=".$_W['member']['uid']." group by c.id DESC");
	
		include $this->template('usercode');
	}
	/*
	 * 参加活动
	 */
	public function doMobileJoin(){
		global $_W, $_GPC;
		if(!$this->check_mc_follow()){
			if(empty($_W['account']['subscribeurl'])){
				$this->msg('error','请关注本公众平台后再参与活动!');
			}
			$this->msg('redirect',$_W['account']['subscribeurl']);
		}
		checkauth();
		$info = pdo_fetch("SELECT l.title as goods_title,l.info,l.price,l.thumb,d.*,r.title as rule_title,r.rule FROM " . tablename('zerobuy_detail') . " d left join " . tablename('zerobuy_list') . " l on d.lid=l.id left join ".tablename('zerobuy_rule')." r on d.rid=r.id WHERE d.id=".$_GPC['id']);
		//参与活动
		if($_GPC['act'] == 'join'){
			if(empty($_W['member']['mobile'])){
				//如果未填写手机号码，跳转到会员中心
				$this->msg('uc', '<P>请先完善您的个人资料！</p><P><font color="red">注意</font>:姓名和手机号码必填。</p><P>个人资料是将来兑奖的依据。</p>');
			}
		if(empty($_W['member']['uid'])){
			$this->msg('error', '非法访问!');
		}
			$msg = $this->make_code($_GPC['id']);
			$this->msg('success', $msg);
		}
	}
	/*
	 * 关注引导
	 */
	public function doMobileShade(){
		include $this->template('shade');
	}
	
	/*
	 * 生成抽奖码
	 */
	public function make_code($did){
		global $_W, $_GPC;
		$info = pdo_fetch("SELECT l.title as goods_title,l.info,l.price,l.thumb,d.*,r.title as rule_title,r.rule FROM " . tablename('zerobuy_detail') . " d left join " . tablename('zerobuy_list') . " l on d.lid=l.id left join ".tablename('zerobuy_rule')." r on d.rid=r.id WHERE d.id=".$did);
		for($i=0;$i<10;$i++){
			$code = mt_rand(0, 99999);
			if($code<10){
				$code = '0000'.$code;
			}elseif ($code>=10 && $code<100){
				$code = '000'.$code;
			}elseif ($code>=100 && $code<1000){
				$code = '00'.$code;
			}elseif ($code>=1000 && $code<10000){
				$code = '0'.$code;
			}else{
			}
			$code_info = pdo_fetch("SELECT * FROM " . tablename('zerobuy_code') . " WHERE code = ".$code." AND did=".$did);
			if(empty($code_info)){
				break;
			}
		}
		pdo_insert('zerobuy_code', array('weid'=>$_W['weid'],'did'=>$did,'uid'=>$_W['member']['uid'],'jointime'=>time(),'code'=>$code));
		pdo_update('zerobuy_detail', 'join_num = join_num +1', array('id'=>$did));
		$message = '<p>商品名：'.$info['goods_title'].'</p>';
		$message .= '<p>抽奖码：'.$code.'</p>';
		$message .= '<p>开奖时间：'.date("Y-m-d H:i:s",$info['endtime']).'</p>';
		return $message;
	}
	/*
	 * 活动状态更新
	 */
	public function update_status(){
        $now = time();
		//更新即将进行的状态
		$future_info = pdo_fetchall("SELECT id,status FROM " . tablename('zerobuy_detail') . " WHERE  starttime>".$now." AND status = 0");
		foreach ($future_info as $v){
			pdo_update('zerobuy_detail', array('status'=>1), array('id'=>$v['id']));
		}
		//更新正在进行的状态	
		$now_info = pdo_fetchall("SELECT id,status FROM " . tablename('zerobuy_detail') . " WHERE starttime < ".$now." AND endtime>".$now." AND status in (0,1)");
		foreach ($now_info as $v){
			pdo_update('zerobuy_detail', array('status'=>2), array('id'=>$v['id']));
		}
		//更新已结束的状态
		$pass_info = pdo_fetchall("SELECT id,status FROM " . tablename('zerobuy_detail') . " WHERE endtime < ".$now." AND status in (0,1,2)");
		foreach ($pass_info as $v){
			pdo_update('zerobuy_detail', array('status'=>3), array('id'=>$v['id']));
		}
	}
	/*
	 * ajax提示信息
	 * @param string $info:'success'-成功信息  'error'-错误信息 'uc'-跳转到个人中心信息
	 * @param string 信息内容
	 */
	public function msg($info,$msg){
			$data = array();
			$data['info'] = $info;
			$data['msg'] = $msg;
			exit(json_encode($data));
	}
	/*
	 * 开奖
	 */
	public function doWebClear(){
		global $_W, $_GPC;
		$detail_info = pdo_fetch("SELECT d.*,r.rule_draw FROM " . tablename('zerobuy_detail') . " d left join " .tablename('zerobuy_rule')." r on d.rid=r.id WHERE d.id=".$_GPC['id']);
		if($detail_info['endtime'] > time() || $detail_info['starttime'] > time()){
			exit('<script>alert("活动还没有结束或还没有开始");history.go(-1);</script>');
		}
		if($_GPC['sub_draw']){
			$did = $_GPC['id'];
			$draw_code = $_GPC['draw_code'];
			$data = array();
			if($detail_info['rule_draw'] == 0){
				$winner_info = pdo_fetch("SELECT `id`,`code`,`uid`,MIN(Abs(`code`-".$draw_code.")) as jdz FROM ".tablename('zerobuy_code')." WHERE did=".$did." group by code order by jdz asc limit 1");	
			}else{
				$winner_info = pdo_fetch("SELECT `id`,`code`,`uid` FROM ".tablename('zerobuy_code')." WHERE did=".$did." AND code = ".$draw_code." limit 1");
			}
			if(empty($winner_info)){
				$data['status'] = 5;
				$data['win_code'] = 0;
				$data['winner_uid'] = 0;
			}else{
				$data['status'] = 4;
				$data['win_code'] = $winner_info['code'];
				$data['winner_uid'] = $winner_info['uid'];
			}
			$data['draw_code'] = $draw_code;
			//更新detail数据
			pdo_update('zerobuy_detail', $data, array('id'=>$did));
			//获取获奖用户openid
			if(empty($winner_info['uid'])){
				message('本期无人中奖！', $this->createWebUrl('detail', array('op' => 'display')), 'success');
			}else{
				$winner_user = pdo_fetch("SELECT uid,openid FROM " . tablename('mc_mapping_fans') . " WHERE uid=".$winner_info['uid']); 
				$content = "恭喜你在零元购活动 ".$detail_info['title']." 中奖";
				$this->push_draw($winner_user['openid'], $content);
			}
		}
		message('开奖成功！', $this->createWebUrl('detail', array('op' => 'display')), 'success');
	}
	/*
	 * 发送中奖信息
	 */
	public function push_draw($touser,$content){
		global $_W, $_GPC;
		load()->func('communication');
		//获取平台信息
		$wechat = pdo_fetch("SELECT * FROM " . tablename('account_wechats') . " WHERE uniacid=".$_W['uniacid']);
		$token = unserialize($wechat['access_token']);
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$token['token'];
		
	    $data=array();
	    $data['touser'] = $touser;
		$data['msgtype'] = 'text';
		$data['text']['content'] = urlencode($content);
		$dat = json_encode($data);
		$dat = urldecode($dat);
		$result = ihttp_post($url,$dat);
	    $final = json_decode($result);
	    return $final;
	}
	/*
	 * post数据
	 */
	function https_post($url,$data)
	{
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url); 
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	    $result = curl_exec($curl);
	    if (curl_errno($curl)) {
	       return 'Errno'.curl_error($curl);
	    }
	    curl_close($curl);
	    return $result;
	}
	/*
	 * 判断粉丝是否关注公众号
	 */
	function check_mc_follow(){
		global $_W, $_GPC;
		if(empty($_W['openid'])){
			return false;
		}
		$mc_info = pdo_fetch("SELECT * FROM ".tablename('mc_mapping_fans')." WHERE uid= ".$_W['member']['uid']);
		if(empty($mc_info) || $mc_info['follow'] == 0){
			return false;
		}
		return true;
	}

}