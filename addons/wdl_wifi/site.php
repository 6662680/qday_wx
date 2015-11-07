<?php
defined('IN_IA') or exit('Access Denied');
class wdl_wifiModuleSite extends WeModuleSite {
	public $routertable='wdl_wifi_info';
	public $replytable='wdl_wifi_reply';
	public $mobantable='wdl_wifi_moban';
	public $table_authlist = 'wdl_wifi_authentication';
	public $create_node = 'https://api.authcat.org/node_api/create_node';
	public $update_node = 'https://api.authcat.org/node_api/update_node';
	public $retrieve_node = 'https://api.authcat.org/node_api/retrieve_node';
	public $delete_node = 'https://api.authcat.org/node_api/delete_node';
	public $retrieve_node_list = 'https://api.authcat.org/node_api/retrieve_node_list';
	public $unauth_user = 'http://wx.rippletek.com/Portal/Wx/unauth_user';
	public function getProfileTiles() {

	}
	public function getHomeTiles() {
	}
	public function doWebRouteradd() {
		global $_GPC, $_W;
		$node=intval($_GPC['node']);
		$uniacid=intval($_W['uniacid']);
		if(!empty($node))
		{
			$nodethings = array(
				'api_id' => $this->module['config']['nodeid'],
				'api_key' => $this->module['config']['nodekey'],
				'node' => $node,
				);
                                                      load()->func('communication');
			$item = ihttp_post($this->retrieve_node,json_encode($nodethings));
			$item = json_decode($item['content'],true);
			if(empty($item))
			{
				message('抱歉,您编辑的路由器信息不存在或已删除');
			}
		}
		if (checksubmit('submit')) {
			if (empty($_GPC['rname'])) {
				message('请输入路由器名称！');
			}
			if ($_GPC['auth2nd'] == '0') {
				$_GPC['auth2nd'] =0;
			}elseif ($_GPC['auth2nd'] == '1') {
				$_GPC['auth2nd'] =1;
			}else{
				$_GPC['auth2nd'] =2;
			}
			$data = array(
				'api_id' => $this->module['config']['nodeid'],
				'api_key' => $this->module['config']['nodekey'],
				'name' => $_GPC['nodename'],
				'description' => $_GPC['rname'],
				'login_url' => $_GPC['login_url'],
				'success_url' => $_GPC['success_url'],
				'probation_url' => null,
				'login_timeout' => intval($_GPC['login_timeout']),
				'probation_timeout' => 0,
				'is_portal' => $_GPC['is_portal'] == '1' ? true:false,
				'qq_login' => $_GPC['qq_login'] == '1' ? true:false,
				'weibo_login' => $_GPC['weibo_login'] == '1' ? true:false,
				'weixin_login' => $_GPC['weixin_login'] == '1' ? true:false,
				'wx_id' => $_GPC['wx_id'],
				'wx_name' => $_GPC['wx_name'],
				'wx_phone_only' => $_GPC['wx_phone_only'] == '1' ? true:false,
				'wx_unauth_timeout' =>$_GPC['wx_unauth_timeout'],
				'wx_reject_timeou' => 3,
				'white_list' => $_GPC['white_list'],
				'hide_cp' => $_GPC['hide_cp'] == '1' ? true:false,
				'auth2nd' => $_GPC['auth2nd'],
				);
                                                     load()->func('communication');
			if (empty($node))
			{
                                                                      
				$result = ihttp_post($this->create_node,json_encode($data));
				$result = json_decode($result['content'],true);
				if ($result['status'] == '0') {
					message('路由器节点添加成功！', $this->createWebUrl('routerlist'), 'success');
				}else{
					message('路由器节点添加失败！错误信息：'.$result['err_msg']);
				}
			}
			else
			{
				$data['node'] = $node;
				$result = ihttp_post($this->update_node,json_encode($data));
				$result = json_decode($result['content'],true);
				if ($result['status'] == '0') {
					message('路由器节点更新成功！', $this->createWebUrl('routerlist'), 'success');
				}else{
					message('路由器节点更新失败！错误信息：'.$result['err_msg']);
				}
			}
		}
		include $this->template('routeradd');
	}
	
	public function doWebRouterlist() {
		global $_W,$_GPC;
		$uniacid=$_W['uniacid'];
		$data = array(
			'api_id' => $this->module['config']['nodeid'],
			'api_key' => $this->module['config']['nodekey'],
			);
                                     load()->func('communication');
		$list = ihttp_post($this->retrieve_node_list,json_encode($data));
		$list = json_decode($list['content'],true);
		if ($list['status'] == '0') {
			$list = $list['node_list'];
		}else{
			message('路由列表请求错误，错误详情'.$list['err_msg']);
		}
		include $this->template('routerlist');
	}
	
	public function doWebDelrouter() {
		global $_GPC,$_W;
		$node = intval($_GPC['node']);
		$data = array(
			'api_id' => $this->module['config']['nodeid'],
			'api_key' => $this->module['config']['nodekey'],
			'node' => $node,
			);
                 load()->func('communication');
		$result = ihttp_post($this->delete_node,json_encode($data));
		$result = json_decode($result['content'],true);
		if ($result['status'] == '0') {
			message('删除成功！', referer(), 'success');
		}else{
			message('删除失败，错误详情为：'.$result['err_msg']);
		}
	}

	public function getnode_info($node){
		$node = intval($node);
		$data = array(
			'api_id' => $this->module['config']['nodeid'],
			'api_key' => $this->module['config']['nodekey'],
			'node' => $node,
			);
                 load()->func('communication');
		$item = ihttp_post($this->retrieve_node,json_encode($data));
		$item = json_decode($item['content'],true);
		return $item;
	}
	
	public function doWebAuthlist() {
		global $_W,$_GPC;
		$uniacid=$_W['uniacid'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT * FROM ".tablename($this->table_authlist)." WHERE uniacid = '{$uniacid}' ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename($this->table_authlist)." WHERE uniacid = '{$uniacid}'");
		$pager = pagination($total, $pindex, $psize);
		include $this->template('authlist');
	}

	public function doWebUnauth_user(){
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		$node = intval($_GPC['node']);
		$openid = $_GPC['openid'];
		$data = array(
			'api_id' => $this->module['config']['authid'],
			'api_key' => $this->module['config']['authkey'],
			'node' => $node,
			'openid' => $openid,
            ); load()->func('communication');
		$result = ihttp_post($this->unauth_user,json_encode($data));
		$result = json_encode($result['content'],true);
		//print_r($result);
		if ($result['status'] == 0) {
			pdo_update('wdl_wifi_authentication',array('result'=>'0'),array('id'=>$id));
			message('强制下线成功',referer(), 'success');
		}else{
			message('强制下线失败,错误详情为'.$result['err_msg']);
		}
	}

	public function doWebMoban(){
		global $_W,$_GPC;
		if (empty($_GPC['type'])) {
			$_GPC['type'] = 'auth';
		}
		if (checksubmit('submit')) {

			$bgimg = array(
				'0' => $_GPC['b0'],
				'1' => $_GPC['b1'],
				'2' => $_GPC['b2'],
				);
			$data = array(
				'uniacid' => $_W['uniacid'],
				'type' => $_GPC['type'],
				'style' => $_GPC['style'],
				'title' => $_GPC['title'],
				'shopname' => $_GPC['shopname'],
				'shopaddress' => $_GPC['shopaddress'],
				'shoptel' => $_GPC['shoptel'],
				'logo' => $_GPC['logo'],
				'qrcode' => $_GPC['qrcode'],
				'copyright' => $_GPC['copyright'],
				'bgimg' => iserializer($bgimg),
				'createtime' => time(),
				);
			if (!empty($_GPC['id'])) {
				if (pdo_update($this->mobantable,$data,array('id' => $_GPC['id']))) {
					message('模板更新成功',referer(), 'success');
				}else{
					message('模板更新失败');
				}
			}else{
				if (pdo_insert($this->mobantable,$data)) {
					message('模板添加成功',referer(), 'success');
				}else{
					message('模板添加失败');
				}
			}
		}
		if ($_GPC['type'] == 'auth') {
			$item = pdo_fetch("SELECT * FROM ".tablename($this->mobantable)." WHERE uniacid = '{$_W['uniacid']}' AND type = 'auth' ORDER BY createtime DESC LIMIT 1");
			include $this->template('t_auth');
		}else{
			$item = pdo_fetch("SELECT * FROM ".tablename($this->mobantable)." WHERE uniacid = '{$_W['uniacid']}' AND type = 'portal' ORDER BY createtime DESC LIMIT 1");
			$bgimg = iunserializer($item['bgimg']);
			$item['b0'] = $bgimg['0'];
			$item['b1'] = $bgimg['1'];
			$item['b2'] = $bgimg['2'];
			include $this->template('t_portal');
		}	
	}
	
	public function doMobileAuth(){
		global $_W;
		$item = pdo_fetch("SELECT * FROM ".tablename($this->mobantable)." WHERE uniacid = '{$_W['uniacid']}' AND type = 'auth' ORDER BY createtime DESC LIMIT 1");
		include $this->template('auth/'.$item['style']);
	}

	public function doMobileIndex(){
		global $_W;

		$item = pdo_fetch("SELECT * FROM ".tablename($this->mobantable)." WHERE uniacid = '{$_W['uniacid']}' AND type = 'portal' ORDER BY createtime DESC LIMIT 1");
		$bgimg = iunserializer($item['bgimg']);
		include $this->template('portal/'.$item['style']);
	}
}
