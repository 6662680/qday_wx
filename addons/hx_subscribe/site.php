<?php
/**
 * 推荐关注模块微站定义
 *
 * @author 华轩科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Hx_subscribeModuleSite extends WeModuleSite {

	public function __construct(){
		global $_W;
		load()->func('communication');
		$openid = $_W['openid'];
		$account = account_fetch($_W['acid']);
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$account['access_token']['token']."&openid=".$openid."&lang=zh_CN";
		$re = ihttp_get($url);
		if ($re['code'] == 200) {
			$content = json_decode($re['content'],true);
			if ($content['subscribe'] == 1) { //此人已关注
				$data = array(
					'nickname' => $content['nickname'],
					'gender' => $content['sex'],
					'avatar' => $content['headimgurl'],
					'resideprovince' => $content['province'],
					'residecity' => $content['city'],
					'nationality' => $content['country'],
				);
				pdo_update('mc_members', $data, array('uid' => $_W['member']['uid']));
				pdo_update('mc_mapping_fans',array('follow' => 1),array('acid'=>$_W['acid'],'openid'=>$openid));
			}
		}
	}

	public function get_user_info($uid,$openid) {
		global $_W;
		load()->func('communication');
		if ($uid == 0) {
			$f_log = pdo_fetch("SELECT * FROM ".tablename('mc_mapping_fans') . " WHERE `uniacid` = '{$_W['uniacid']}' AND `openid` = '{$openid}'");
			if (!empty($f_log['uid'])) {
				pdo_update('hx_subscribe_data', array('uid'=>$f_log['uid']), array('openid' => $openid));
				$uid = $f_log['uid'];
			}else{
				$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
				$data = array(
					'uniacid' => $_W['uniacid'],
					'email' => md5($openid).'@qdaygroup.com',
					'salt' => random(8),
					'groupid' => $default_groupid,
					'createtime' => TIMESTAMP,
				);
				$data['password'] = md5($message['from'] . $data['salt'] . $_W['config']['setting']['authkey']);
				pdo_insert('mc_members', $data);
				$uid = pdo_insertid();
				pdo_update('mc_mapping_fans', array('uid'=>$uid),array('openid'=>$openid));
				pdo_update('hx_subscribe_data', array('uid'=>$uid), array('openid' => $openid));
			}
		}
		$info = mc_fetch($uid);
		if (empty($info['nickname'])) {
			$account = account_fetch($_W['acid']);
			$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$account['access_token']['token']."&openid=".$openid."&lang=zh_CN";
			$re = ihttp_get($url);
			if ($re['code'] == 200) {
				$content = json_decode($re['content'],true);
				if ($content['subscribe'] == 1) { //此人已关注
					$data = array(
						'nickname' => $content['nickname'],
						'gender' => $content['sex'],
						'avatar' => $content['headimgurl'],
						'resideprovince' => $content['province'],
						'residecity' => $content['city'],
						'nationality' => $content['country'],
					);
					pdo_update('mc_members', $data, array('uid' => $_W['member']['uid']));
					pdo_update('mc_mapping_fans',array('follow' => 1),array('acid'=>$_W['acid'],'openid'=>$openid));
				}
			}
			$info = mc_fetch($uid);
			return $info;
		}else{
			return $info;
		}
	}

	public function doMobileMain() {
		global $_W,$_GPC;
		$profile = mc_fetch($_W['member']['uid']);
		$openid = $_W['openid'];
		$credit = mc_fetch($_W['openid']);
		$apply = pdo_fetch("SELECT *  from ".tablename('hx_subscribe_apply')." ORDER BY id DESC LIMIT 1");
		$start_num = isset($this->module['config']['start_num']) ? $this->module['config']['start_num'] : '20000';
		$credit_type = isset($this->module['config']['credit_type']) ? $this->module['config']['credit_type'] : 'credit1';
		$log = pdo_fetch("SELECT * FROM ".tablename('hx_subscribe_data') . " WHERE `uniacid` = '{$_W['uniacid']}' AND `openid` = '{$openid}'");
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hx_subscribe_data') . " WHERE uniacid = '{$_W['uniacid']}'");
		include $this->template('main');
	}

	public function doWebArticle() {
		global $_W,$_GPC;
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($op == 'post') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM ".tablename('hx_subscribe_article')." WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，文章不存在或是已经删除！', '', 'error');
				}
    		}
    		if (checksubmit('submit')) {
    			if (empty($_GPC['title'])) {
					message('标题不能为空，请输入标题！');
				}
				$data = array(
					'uniacid' => $_W['uniacid'],
					'title' => $_GPC['title'],
					'description' => $_GPC['description'],
					'content' => htmlspecialchars_decode($_GPC['content']),
					'source' => $_GPC['source'],
					'author' => $_GPC['author'],
					'linkurl' => $_GPC['linkurl'],
					'displayorder' => intval($_GPC['displayorder']),
					'createtime' => TIMESTAMP,
				);
				if (!empty($_GPC['thumb'])) {
					$data['thumb'] = $_GPC['thumb'];
					if (!empty($_GPC['incontent'][0])) {
						$thumb = '<p><img src="' . tomedia($data['thumb']) . '" title="' . tomedia($data['title']) . '" /></p>';
						$data['content'] = $thumb . $data['content'];
					}
				} elseif (!empty($_GPC['autolitpic'])) {
					$match = array();
					preg_match('/attachment\/(.*?)(\.gif|\.jpg|\.png|\.bmp)/', $_GPC['content'], $match);
					if (!empty($match[1])) {
						$data['thumb'] = $match[1].$match[2];
					}
				}
				if (empty($id)) {
					pdo_insert('hx_subscribe_article', $data);
				}else{
					unset($data['createtime']);
					pdo_update('hx_subscribe_article', $data, array('id' => $id));
				}
				message('文章更新成功！', $this->createWebUrl('article'), 'success');
    		}
		}elseif ($op == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
			}
			$list = pdo_fetchall("SELECT * FROM ".tablename('hx_subscribe_article')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY displayorder DESC, id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hx_subscribe_article') . " WHERE uniacid = '{$_W['uniacid']}'");
			$pager = pagination($total, $pindex, $psize);
		}elseif ($op == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM ".tablename('hx_subscribe_article')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，文章不存在或是已经被删除！');
			}
			pdo_delete('hx_subscribe_article', array('id' => $id));
			message('删除成功！', referer(), 'success');
		}
		load()->func('tpl');
		include $this->template('article');
	}
	public function doMobileArticleList() {
		global $_W,$_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT * FROM ".tablename('hx_subscribe_article')." WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC, id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hx_subscribe_article') . " WHERE uniacid = '{$_W['uniacid']}'");
		$pager = pagination($total, $pindex, $psize);
		include $this->template('article_list');
	}
	public function doMobileArticleDetail() {
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		$detail = pdo_fetch("SELECT * FROM ".tablename('hx_subscribe_article')." WHERE id = :id" , array(':id' => $id));
		//print_r($detail);
		if (empty($detail)) {
			message('抱歉，文章不存在或是已经删除！', '', 'error');
		}
		$from_uid = intval($_GPC['from_uid']);
		if (!empty($from_uid)) {
			$f_log = pdo_fetch("SELECT * FROM " . tablename('hx_subscribe_data') . " WHERE `uniacid`='{$_W['uniacid']}' AND `uid`='{$from_uid}'");//查询是否有账户
			$fans_info =  pdo_fetch("SELECT openid,follow FROM " . tablename('mc_mapping_fans') . " WHERE `uniacid`='{$_W['uniacid']}' AND `uid`='{$from_uid}'");
			if (empty($f_log) && $fans_info['follow'] == 1) {
				$insert = array(
					'uniacid' => $_W['uniacid'],
					'openid' => $fans_info['openid'],
					'uid' => $from_uid,
					'from_uid' => '0',
					'sn' => time(),
					'follow' => '1',
					'article_id' => '0',
					'createtime' => TIMESTAMP,
					);
				pdo_insert('hx_subscribe_data',$insert);
			}
			$f_log = pdo_fetch("SELECT * FROM " . tablename('hx_subscribe_data') . " WHERE `uniacid`='{$_W['uniacid']}' AND `uid`='{$from_uid}'");
		}
		$openid = $_W['openid'];
		$flag = '1';
		$follow = pdo_fetch("SELECT uid,follow FROM " . tablename('mc_mapping_fans') . " WHERE `uniacid`='{$_W['uniacid']}' AND `openid`='{$openid}'");
		//print_r($follow);
		$credit_type = isset($this->module['config']['credit_type']) ? $this->module['config']['credit_type'] : 'credit1';
		$credit_subscribe = isset($this->module['config']['credit_subscribe']) ? $this->module['config']['credit_subscribe'] : 5;
		if (empty($follow) || empty($follow['follow'])) {//未关注用户
			$flag = '0';
			$u_log = pdo_fetch("SELECT * FROM " . tablename('hx_subscribe_data') . " WHERE `uniacid`='{$_W['uniacid']}' AND `openid`='{$openid}'");
			$data = array(
				'uniacid' => $_W['uniacid'],
				'openid' => $openid,
				'uid' => $_W['member']['uid'],
				'from_uid' => $from_uid,
				'sn' => time(),
				'follow' => '0',
				'article_id' => $id,
				'shouyi' => $credit_subscribe,
				'createtime' => TIMESTAMP,
			);
			if (empty($u_log)) {
				pdo_insert('hx_subscribe_data',$data);
			}else{
				unset($data['sn']);
				unset($data['createtime']);
				$credit = mc_fetch($_W['openid']);
				mc_credit_update($_W['member']['uid'],$credit_type,'-'.$credit[$credit_type],array('1','取消关注清除'));
				pdo_update('hx_subscribe_data',$data,array('id'=>$u_log['id']));
			}
			$f_uid = $from_uid;
			header("Location:".$detail['linkurl']);
			exit();
		}else{
			$u_log = pdo_fetch("SELECT * FROM " . tablename('hx_subscribe_data') . " WHERE `uniacid`='{$_W['uniacid']}' AND `openid`='{$openid}'");
			$credit = mc_fetch($_W['openid']);
			$f_uid = $follow['uid'];
		}
		$ff_log = pdo_fetch("SELECT * FROM " . tablename('hx_subscribe_data') . " WHERE `uniacid`='{$_W['uniacid']}' AND `uid`='{$f_uid}'");
		include $this->template('article_detail');
	}
	public function doWebList() {
		global $_W,$_GPC;
		load()->model('mc');
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = " WHERE `uniacid` = '{$_W['uniacid']}' AND `follow` = '1'";
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('hx_subscribe_data') . $where);
		$list = pdo_fetchall("SELECT * FROM " . tablename('hx_subscribe_data') . $where . " ORDER BY shouyi ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $paras);
		$pager = pagination($total, $pindex, $psize);
		include $this->template('list');
	}
	public function doMobileList() {//积分排行
		global $_W,$_GPC;
		load()->model('mc');
		$pindex = max(1, intval($_GPC['page']));
		if (!empty($_W['openid'])) {
			$myinfo = pdo_fetch("SELECT * FROM " . tablename('hx_subscribe_data') . "  WHERE `uniacid` = '{$_W['uniacid']}' AND `follow` = '1' AND `openid` = '{$_W['openid']}'");
			$mymc = pdo_fetchcolumn("SELECT COUNT(*)+1 FROM " . tablename('hx_subscribe_data') . "WHERE `uniacid` = '{$_W['uniacid']}' AND `follow` = '1' AND `shouyi` > '{$myinfo['shouyi']}'");
			$eqnum = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('hx_subscribe_data') . "WHERE `uniacid` = '{$_W['uniacid']}' AND `follow` = '1' AND `shouyi` = '{$myinfo['shouyi']}'  AND `id` < '{$myinfo['id']}'");
			$mymc = $mymc + $eqnum;
		}
		$psize = 10;
		$where = " WHERE `uniacid` = '{$_W['uniacid']}' AND `follow` = '1'";
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('hx_subscribe_data') . $where);
		$list = pdo_fetchall("SELECT * FROM " . tablename('hx_subscribe_data') . $where . " ORDER BY shouyi DESC, id asc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $paras);
		$pager = pagination($total, $pindex, $psize);
		load()->func('tpl');
		include $this->template('list');
	}
	public function doMobileMycredit() {//我的积分
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		$uniacid=$_W["uniacid"];
		load()->model('mc');
		$openid = $_W['fans']['from_user'];
		$fans = pdo_fetch("SELECT fanid,uid FROM ". tablename('mc_mapping_fans') ." WHERE `openid`='$openid' LIMIT 1");
		$uid = '0';
		if ($fans['uid'] != '0') {
			$uid = $fans['uid'];
		}else{
			$uid = mc_update($uid, array('email' => md5($_W['openid']).'@qdaygroup.com'));
			if (!empty($fans['fanid']) && !empty($uid)) {
				pdo_update('mc_mapping_fans', array('uid' => $uid), array('fanid' => $fans['fanid']));
			}
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT *  from ".tablename('hx_subscribe_apply')." where uniacid='{$uniacid}' AND uid = '{$uid}' order by id asc LIMIT ". ($pindex -1) * $psize . ',' .$psize );
		$total = pdo_fetchcolumn("SELECT COUNT(*)  from ".tablename('hx_subscribe_apply')." where uniacid='{$uniacid}' AND uid = '{$uid}' order by id asc");
		$pager = pagination($total, $pindex, $psize);
		include $this->template('myapply');
	}

	public function doMobileApply() {
		//这个操作被定义用来呈现 微站个人中心导航
		global $_W, $_GPC;
		load()->model('mc');
		$openid = $_W['fans']['from_user'];
		$fans = pdo_fetch("SELECT fanid,uid FROM ". tablename('mc_mapping_fans') ." WHERE `openid`='$openid' LIMIT 1");
		$uid = '0';
		if ($fans['uid'] != '0') {
			$uid = $fans['uid'];
		}else{
			$uid = mc_update($uid, array('email' => md5($_W['openid']).'@qdaygroup.com'));
			if (!empty($fans['fanid']) && !empty($uid)) {
				pdo_update('mc_mapping_fans', array('uid' => $uid), array('fanid' => $fans['fanid']));
			}
		}
		$minnum = isset($this->module['config']['out_limit']) ? $this->module['config']['out_limit'] : '100.00';
		$credit_type = isset($this->module['config']['credit_type']) ? $this->module['config']['credit_type'] : 'credit1';
		$yue = mc_credit_fetch($uid);
		$ff_log = pdo_fetch("SELECT * FROM " . tablename('hx_subscribe_data') . " WHERE `uniacid`='{$_W['uniacid']}' AND `uid`='{$uid}'");
		$profile = mc_fetch($uid);
		if (checksubmit('submit')) {
			if ($_GPC['type'] == '1' && empty($_GPC['alipay'])) {
				message('参数错误，请返回修改');
			}
			if ($_GPC['type'] == '2' && empty($_GPC['cardid'])) {
				message('参数错误，请返回修改');
			}
			$remark['1']['user'] = $_GPC['realname'];
			$remark['1']['time'] = time();
			$remark['1']['reason'] = '';
			$data = array(
				'uniacid' => $_W['uniacid'],
				'uid' => $uid,
				'realname' => $_GPC['realname'],
				'qq' => $_GPC['qq'],
				'type' => intval($_GPC['type']),
				'alipay' => $_GPC['alipay'],
				'cardid' => $_GPC['cardid'],
				'cardfrom' => $_GPC['cardfrom'],
				'cardname' => $_GPC['cardname'],
				'credit2' => $_GPC['credit2'],
				'mobile' => $_GPC['mobile'],
				'createtime' => time(),
				'status' => '1',//1.申请提现 2.已审核 -2 审核失败 3.审核通过待支付 4.已支付
				'remark' => iserializer($remark),
				);
			pdo_insert('hx_subscribe_apply',$data);
			mc_credit_update($uid,$credit_type,'-'.$_GPC['mobile'],array('1','申请提现'));
			message('提现成功',$this->createMobileUrl('myapply'),'success');
		}
		include $this->template('apply');
	}

	public function doWebCredit() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W, $_GPC;
		$uniacid=$_W["uniacid"];
		$where="";
		if ($_GPC['s'] != 0) {
			$where .= "AND status = {$_GPC['s']}";
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT *  from ".tablename('hx_subscribe_apply')." where uniacid='{$uniacid}' {$where} order by id asc LIMIT ". ($pindex -1) * $psize . ',' .$psize );
		$total = pdo_fetchcolumn("SELECT COUNT(*)  from ".tablename('hx_subscribe_apply')." where uniacid='{$uniacid}' {$where} order by id asc");
		$pager = pagination($total, $pindex, $psize);
		load()->func('tpl');
		include $this->template('clist');
	}
	public function doWebDelete(){
		global $_GPC, $_W;
		if(!empty($id)){
			$set = pdo_delete('hx_subscribe_apply', array('id' => $_GPC['id']));	
			message('成功删除本条记录！', referer(), 'success');	
		}
	}
	public function doWebManager(){
		global $_W, $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch("SELECT *  from ".tablename('hx_subscribe_apply')." where id='{$id}'");
		load()->func('tpl');
		$reason = iunserializer($item['remark']);
		$r = $reason[$item['status']];
		if (checksubmit('submit')) {
			if ($_GPC['status'] == '-2' && empty($_GPC['reason'])) {
				message('请输入审核失败原因');
			}
			$data['user'] = $_GPC['user'];
			$data['time'] = time();
			$data['reason'] = $_GPC['reason'];
			$reason[$_GPC['status']] = $data;
			pdo_update('hx_subscribe_apply',array('status'=>$_GPC['status'],'remark'=>iserializer($reason)),array('id'=>$id));
			message('操作成功',$this->createWebUrl('list'),'success');
		}
		include $this->template('manager');
	}

}