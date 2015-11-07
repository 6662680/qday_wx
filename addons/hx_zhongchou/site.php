<?php
/**
 * 众筹模块微站定义
 *
 * @author 情天科技
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class Hx_zhongchouModuleSite extends WeModuleSite {

	public function doMobileList() {
		global $_W,$_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 4;
		$condition = '';
		if (!empty($_GPC['ccate'])) {
			$cid = intval($_GPC['ccate']);
			$condition .= " AND ccate = '{$cid}'";
			$_GPC['pcate'] = pdo_fetchcolumn("SELECT parentid FROM " . tablename('hx_zhongchou_category') . " WHERE id = :id", array(':id' => intval($_GPC['ccate'])));
		} elseif (!empty($_GPC['pcate'])) {
			$cid = intval($_GPC['pcate']);
			$condition .= " AND pcate = '{$cid}'";
		}
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
		}
		$children = array();
		$category = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_category') . " WHERE weid = '{$_W['uniacid']}' and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
		foreach ($category as $index => $row) {
			if (!empty($row['parentid'])) {
				$children[$row['parentid']][$row['id']] = $row;
				unset($category[$index]);
			}
		}
		//幻灯片
		$advs = pdo_fetchall("select * from " . tablename('hx_zhongchou_adv') . " where enabled=1 and weid= '{$_W['uniacid']}'");
		foreach ($advs as &$adv) {
			if (substr($adv['link'], 0, 5) != 'http:') {
				$adv['link'] = "http://" . $adv['link'];
			}
		}
		unset($adv);
		$rpindex = max(1, intval($_GPC['rpage']));
		$rpsize = 6;
		$condition = ' and isrecommand=1';
		$rlist = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_project') . " WHERE weid = '{$_W['uniacid']}' AND status = '1' $condition ORDER BY displayorder DESC, finish_price DESC LIMIT " . ($rpindex - 1) * $rpsize . ',' . $rpsize);
		$carttotal = $this->getCartTotal();
		include $this->template('list');
	}
	public function doMobilelist2() {
		global $_GPC, $_W;
		$pindex = max(1, intval($_GPC["page"]));
		$psize = 10;
		$condition = '';
		if (!empty($_GPC['ccate'])) {
			$cid = intval($_GPC['ccate']);
			$condition .= " AND ccate = '{$cid}'";
			$_GPC['pcate'] = pdo_fetchcolumn("SELECT parentid FROM " . tablename('hx_zhongchou_category') . " WHERE id = :id", array(':id' => intval($_GPC['ccate'])));
		} elseif (!empty($_GPC['pcate'])) {
			$cid = intval($_GPC['pcate']);
			$condition .= " AND pcate = '{$cid}'";
		}
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
		}
		$sort = empty($_GPC['sort']) ? 0 : $_GPC['sort'];
		$sortfield = "displayorder asc";
		$sortb0 = empty($_GPC['sortb0']) ? "desc" : $_GPC['sortb0'];
		$sortb1 = empty($_GPC['sortb1']) ? "desc" : $_GPC['sortb1'];
		$sortb2 = empty($_GPC['sortb2']) ? "desc" : $_GPC['sortb2'];
		$sortb3 = empty($_GPC['sortb3']) ? "asc" : $_GPC['sortb3'];
		if ($sort == 0) {
			$sortb00 = $sortb0 == "desc" ? "asc" : "desc";
			$sortfield = "createtime " . $sortb0;
			$sortb11 = "desc";
			$sortb22 = "desc";
			$sortb33 = "asc";
		} else if ($sort == 1) {
			$sortb11 = $sortb1 == "desc" ? "asc" : "desc";
			$sortfield = "donenum " . $sortb1;
			$sortb00 = "desc";
			$sortb22 = "desc";
			$sortb33 = "asc";
		} else if ($sort == 2) {
			$sortb22 = $sortb2 == "desc" ? "asc" : "desc";
			$sortfield = "donenum " . $sortb2;
			$sortb00 = "desc";
			$sortb11 = "desc";
			$sortb33 = "asc";
		}
		$sorturl = $this->createMobileUrl('list2', array("keyword" => $_GPC['keyword'], "pcate" => $_GPC['pcate'], "ccate" => $_GPC['ccate']), true);
		if (!empty($_GPC['isnew'])) {
			$condition .= " AND isnew = 1";
			$sorturl.="&isnew=1";
		}
		if (!empty($_GPC['ishot'])) {
			$condition .= " AND ishot = 1";
			$sorturl.="&ishot=1";
		}
		if (!empty($_GPC['isdiscount'])) {
			$condition .= " AND isdiscount = 1";
			$sorturl.="&isdiscount=1";
		}
		if (!empty($_GPC['istime'])) {
			$condition .= " AND istime = 1 and " . time() . ">=timestart and " . time() . "<=timeend";
			$sorturl.="&istime=1";
		}
		$children = array();
		$category = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_category') . " WHERE weid = '{$_W['uniacid']}' and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
		foreach ($category as $index => $row) {
			if (!empty($row['parentid'])) {
				$children[$row['parentid']][$row['id']] = $row;
				unset($category[$index]);
			}
		}
		$list = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_project') . " WHERE weid = '{$_W['uniacid']}'   AND status = '1' $condition ORDER BY $sortfield LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hx_zhongchou_project') . " WHERE weid = '{$_W['uniacid']}'    AND status = '1' $condition");
		$pager = pagination($total, $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
		$carttotal = $this->getCartTotal();
		include $this->template('list2');
	}
	public function doMobileDetail(){
		global $_GPC,$_W;
		$id = intval($_GPC['id']);
		$item = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project') . " WHERE id = :id", array(':id' => $id));
		if (empty($item)) {
			message("抱歉，订单不存在!", referer(), "error");
		}
		$favournum = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hx_zhongchou_cart') . " WHERE weid = '{$_W['uniacid']}' AND projectid = '{$id}'");
		$isfavour = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_cart') . " WHERE projectid = '{$id}' AND from_user = '{$_W['fans']['from_user']}'");
		$items = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_project_item') . " WHERE weid = '{$_W['uniacid']}'   AND pid = '{$id}' ");
		$carttotal = $this->getCartTotal();
		include $this->template('detail');
	}
	public function getproject($id) {
		$item = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project') . " WHERE id = :id", array(':id' => $id));
		return $item;
	}
	public function getitem($id) {
		$item = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project_item') . " WHERE id = :id", array(':id' => $id));
		return $item;
	}
	public function doMobileDetail_more(){
		global $_GPC,$_W;
		$id = intval($_GPC['id']);
		$detail = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project') . " WHERE id = :id", array(':id' => $id));
		if (empty($detail)) {
			message("抱歉，项目不存在!", referer(), "error");
		}
		include $this->template('detail_more');
	}
	public function doMobileConfirm() {
		global $_W, $_GPC;
		checkauth();
		$id = intval($_GPC['id']);
		$project = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project') . " WHERE id = :id", array(':id' => $id));
		if (empty($project)) {
			message("抱歉，该项目不存在!", referer(), "error");
		}
		$item_id = intval($_GPC['item_id']);
		$item = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project_item') . " WHERE id = :id", array(':id' => $item_id));
		if (empty($item)) {
			message("抱歉，该回报不存在!", referer(), "error");
		}
		if ($item['limit_num'] != 0 && $item['limit_num'] <= $item['donenum']) {
			message('该回报以筹集完毕，请选择其他回报');
		}
		$returnurl = $this->createMobileUrl("confirm", array("id" => $id, "item_id" => $item_id));
		$dispatch = pdo_fetchall("select id,dispatchname,dispatchtype,firstprice,firstweight,secondprice,secondweight from " . tablename("hx_zhongchou_dispatch") . " WHERE weid = {$_W['uniacid']} order by displayorder desc");
		foreach ($dispatch as &$d) {
			$weight = 0;
			$weight = $item['weight'];
			$price = 0;
			if ($weight <= $d['firstweight']) {
				$price = $d['firstprice'];
			} else {
				$price = $d['firstprice'];
				$secondweight = $weight - $d['firstweight'];
				if ($secondweight % $d['secondweight'] == 0) {
					$price+= (int) ( $secondweight / $d['secondweight'] ) * $d['secondprice'];
				} else {
					$price+= (int) ( $secondweight / $d['secondweight'] + 1 ) * $d['secondprice'];
				}
			}
			$d['price'] = $price;
		}
		unset($d);
		if (checksubmit('submit')) {
			$address = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_address') . " WHERE id = :id", array(':id' => intval($_GPC['address'])));
			if (empty($address)) {
				message('抱歉，请您填写收货地址！');
			}
			//项目回报价格
			$item_price = $item['price'];
			//运费
			$dispatchid = intval($_GPC['dispatch']);
			$dispatchprice = 0;
			foreach ($dispatch as $d) {
				if ($d['id'] == $dispatchid) {
					$dispatchprice = $d['price'];
					$sendtype = $d['dispatchtype'];
				}
			}
			$data = array(
				'weid' => $_W['uniacid'],
				'from_user' => $_W['fans']['from_user'],
				'ordersn' => date('md') . random(4, 1),
				'price' => $item_price + $dispatchprice,
				'dispatchprice' => $dispatchprice,
				'item_price' => $item_price,
				'status' => 0,
				'sendtype' =>intval($sendtype),
				'dispatch' => $dispatchid,
				'return_type' => intval($item['return_type']),
				'remark' => $_GPC['remark'],
				'addressid' => $address['id'],
				'pid' => $id,
				'item_id' => $item_id,
				'createtime' => TIMESTAMP,
			);
			pdo_insert('hx_zhongchou_order', $data);
			$orderid = pdo_insertid();
			message('提交订单成功,现在跳转到付款页面...',$this->createMobileUrl('pay', array('orderid' => $orderid)),'success');
		}
		$profile = fans_search($_W['fans']['from_user'], array('resideprovince', 'residecity', 'residedist', 'address', 'realname', 'mobile'));
		$row = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_address') . " WHERE isdefault = 1 and deleted=0 and openid = :openid limit 1", array(':openid' => $_W['fans']['from_user']));
		$carttotal = $this->getCartTotal();
		include $this->template('confirm');
	}
	public function doMobileMyCart() {
		global $_W, $_GPC;
		$this->checkAuth();
		$op = $_GPC['op'];
		if ($op == 'add') {
			$pid = intval($_GPC['pid']);
			$project = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project') . " WHERE id = :id", array(':id' => $pid));
			if (empty($project)) {
				$result['message'] = '抱歉，该项目不存在或是已经被删除！';
				message($result, '', 'ajax');
			}
			$row = pdo_fetch("SELECT id FROM " . tablename('hx_zhongchou_cart') . " WHERE from_user = :from_user AND weid = '{$_W['uniacid']}' AND projectid = :pid", array(':from_user' => $_W['fans']['from_user'], ':pid' => $pid));
			if ($row == false) {
				//不存在
				$data = array(
					'weid' => $_W['uniacid'],
					'projectid' => $pid,
					'from_user' => $_W['fans']['from_user'],
				);
				pdo_insert('hx_zhongchou_cart', $data);
				$type = 'add';
			} else {
				pdo_delete('hx_zhongchou_cart', array('id' => $row['id']));
				$type = 'del';
			}
			//返回数据
			$carttotal = $this->getCartTotal();
			$ptotal = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hx_zhongchou_cart') . " WHERE projectid = '{$pid}'");
			$result = array(
				'result' => 1,
				'total' => $carttotal,
				'ptotal' => $ptotal,
				't' => $type
			);
			die(json_encode($result));
		} else if ($op == 'clear') {
			pdo_delete('hx_zhongchou_cart', array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid']));
			die(json_encode(array("result" => 1)));
		} else if ($op == 'remove') {
			$id = intval($_GPC['id']);
			pdo_delete('hx_zhongchou_cart', array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['uniacid'], 'id' => $id));
			die(json_encode(array("result" => 1, "cartid" => $id)));
		} else {
			$list = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_cart') . " WHERE  weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'");
			include $this->template('cart');
		}
	}
	public function doMobilePay() {
		global $_W, $_GPC;
		$this->checkAuth();
		$orderid = intval($_GPC['orderid']);
		$order = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_order') . " WHERE id = :id", array(':id' => $orderid));
		if ($order['status'] != '0') {
			message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', $this->createMobileUrl('myorder'), 'error');
		}
		$params['tid'] = $orderid;
		$params['user'] = $_W['fans']['from_user'];
		$params['fee'] = $order['price'];
		$params['title'] = $_W['account']['name'];
		$params['ordersn'] = $order['ordersn'];
		$params['virtual'] = $order['return_type'] == 2 ? true : false;
		include $this->template('pay');
	}
	public function payResult($params) {
		$fee = intval($params['fee']);
		$data = array('status' => $params['result'] == 'success' ? 1 : 0);
		$paytype = array('credit' => '1', 'wechat' => '2', 'alipay' => '2', 'delivery' => '3');
		$data['paytype'] = $paytype[$params['type']];
		if ($params['type'] == 'wechat') {
			$data['transid'] = $params['tag']['transaction_id'];
		}
		if ($params['type'] == 'delivery') {
			$data['status'] = 1;
		}
		$order = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_order') . " WHERE id = '{$params['tid']}'");
		if ($order['status'] != 1) {
			pdo_update('hx_zhongchou_order', $data, array('id' => $params['tid']));
			//$order = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_order') . " WHERE id = '{$params['tid']}'");
			$pid = $order['pid'];
			$item_id = $order['item_id'];
			$project = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project') . " WHERE id = '{$pid}'");
			$item = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project_item') . " WHERE id = '{$item_id}'");
			pdo_update('hx_zhongchou_project',array('finish_price'=>$project['finish_price'] + $order['item_price'],'donenum'=>$project['donenum']+1),array('id'=>$pid));
			pdo_update('hx_zhongchou_project_item',array('donenum'=>$item['donenum']+1),array('id'=>$item_id));
		}
		
		if ($params['from'] == 'return') {
			//积分变更
			//$this->setOrderCredit($params['tid']);
			//邮件提醒

			if (!empty($this->module['config']['noticeemail'])) {
				
				$address = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_address') . " WHERE id = :id", array(':id' => $order['addressid']));
				$body = "<h3>购买众筹项目详情</h3> <br />";
				$body .= "名称：{$project['title']} <br />";
				$body .= "<br />支持金额：{$item['price']}元 （已付款）<br />";
				$body .= "<h3>购买用户详情</h3> <br />";
				$body .= "真实姓名：{$address['realname']} <br />";
				$body .= "地区：{$address['province']} - {$address['city']} - {$address['area']}<br />";
				$body .= "详细地址：{$address['address']} <br />";
				$body .= "手机：{$address['mobile']} <br />";
                load()->func('communication');
				ihttp_email($this->module['config']['noticeemail'], '众筹订单提醒', $body);
			}

			$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
			$credit = $setting['creditbehaviors']['currency'];
			if ($params['type'] == $credit) {
				message('支付成功！', $this->createMobileUrl('myorder'), 'success');
			} else {
				message('支付成功！', '../../app/' . $this->createMobileUrl('myorder'), 'success');
			}
		}
	}
	public function doMobileAddress() {
		global $_W, $_GPC;
		$from = $_GPC['from'];
		$returnurl = urldecode($_GPC['returnurl']);
		$this->checkAuth();
		$carttotal = $this->getCartTotal();
		$operation = $_GPC['op'];
		if ($operation == 'post') {
			$id = intval($_GPC['id']);
			$data = array(
				'weid' => $_W['uniacid'],
				'openid' => $_W['fans']['from_user'],
				'realname' => $_GPC['realname'],
				'mobile' => $_GPC['mobile'],
				'province' => $_GPC['province'],
				'city' => $_GPC['city'],
				'area' => $_GPC['area'],
				'address' => $_GPC['address'],
			);
			if (empty($_GPC['realname']) || empty($_GPC['mobile']) || empty($_GPC['address'])) {
				message('请输完善您的资料！');
			}
			if (!empty($id)) {
				unset($data['weid']);
				unset($data['openid']);
				pdo_update('hx_zhongchou_address', $data, array('id' => $id));
				message($id, '', 'ajax');
			} else {
				pdo_update('hx_zhongchou_address', array('isdefault' => 0), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
				$data['isdefault'] = 1;
				pdo_insert('hx_zhongchou_address', $data);
				$id = pdo_insertid();
				if (!empty($id)) {
					message($id, '', 'ajax');
				} else {
					message(0, '', 'ajax');
				}
			}
		} elseif ($operation == 'default') {
			$id = intval($_GPC['id']);
			$address = pdo_fetch("select isdefault from " . tablename('hx_zhongchou_address') . " where id='{$id}' and weid='{$_W['uniacid']}' and openid='{$_W['fans']['from_user']}' limit 1 ");
			if(!empty($address) && empty($address['isdefault'])){
				pdo_update('hx_zhongchou_address', array('isdefault' => 0), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
				pdo_update('hx_zhongchou_address', array('isdefault' => 1), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user'], 'id' => $id));
			}
			message(1, '', 'ajax');
		} elseif ($operation == 'detail') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id, realname, mobile, province, city, area, address FROM " . tablename('hx_zhongchou_address') . " WHERE id = :id", array(':id' => $id));
			message($row, '', 'ajax');
		} elseif ($operation == 'remove') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$address = pdo_fetch("select isdefault from " . tablename('hx_zhongchou_address') . " where id='{$id}' and weid='{$_W['uniacid']}' and openid='{$_W['fans']['from_user']}' limit 1 ");
				if (!empty($address)) {
					//pdo_delete("hx_zhongchou_address",  array('id'=>$id, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
					//修改成不直接删除，而设置deleted=1
					pdo_update("hx_zhongchou_address", array("deleted" => 1, "isdefault" => 0), array('id' => $id, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
					if ($address['isdefault'] == 1) {
						//如果删除的是默认地址，则设置是新的为默认地址
						$maxid = pdo_fetchcolumn("select max(id) as maxid from " . tablename('hx_zhongchou_address') . " where weid='{$_W['uniacid']}' and openid='{$_W['fans']['from_user']}' limit 1 ");
						if (!empty($maxid)) {
							pdo_update('hx_zhongchou_address', array('isdefault' => 1), array('id' => $maxid, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
							die(json_encode(array("result" => 1, "maxid" => $maxid)));
						}
					}
				}
			}
			die(json_encode(array("result" => 1, "maxid" => 0)));
		} else {
			$profile = fans_search($_W['fans']['from_user'], array('resideprovince', 'residecity', 'residedist', 'address', 'realname', 'mobile'));
			$address = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_address') . " WHERE deleted=0 and openid = :openid", array(':openid' => $_W['fans']['from_user']));
			$carttotal = $this->getCartTotal();
			include $this->template('address');
		}
	}
	private function checkAuth() {
		global $_W;
		checkauth();
	}
	public function getCartTotal() {
		global $_W;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hx_zhongchou_cart') . " WHERE weid = '{$_W['uniacid']}'  AND from_user = '{$_W['fans']['from_user']}'");
		return empty($total) ? 0 : $total;
	}
	public function doWebOrder() {
		global $_W, $_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$status = $_GPC['status'];
			$sendtype = !isset($_GPC['sendtype']) ? 0 : $_GPC['sendtype'];
			$condition = " o.weid = :weid";
			$paras = array(':weid' => $_W['uniacid']);
			if (empty($starttime) || empty($endtime)) {
				$starttime =  strtotime('-1 month');
				$endtime = time();
			}
			if (!empty($_GPC['time'])) {
				$starttime = strtotime($_GPC['time']['start']);
				$endtime = strtotime($_GPC['time']['end']) + 86399;
				$condition .= " AND o.createtime >= :starttime AND o.createtime <= :endtime ";
				$paras[':starttime'] = $starttime;
				$paras[':endtime'] = $endtime;
			}
			if (!empty($_GPC['paytype'])) {
				$condition .= " AND o.paytype = '{$_GPC['paytype']}'";
			} elseif ($_GPC['paytype'] === '0') {
				$condition .= " AND o.paytype = '{$_GPC['paytype']}'";
			}
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND o.ordersn LIKE '%{$_GPC['keyword']}%'";
			}
			if (!empty($_GPC['member'])) {
				$condition .= " AND (a.realname LIKE '%{$_GPC['member']}%' or a.mobile LIKE '%{$_GPC['member']}%')";
			}
			if ($status != '') {
				$condition .= " AND o.status = '" . intval($status) . "'";
			}
			if (!empty($sendtype)) {
				$condition .= " AND o.sendtype = '" . intval($sendtype) . "' AND status != '3'";
			}
			if ($_GPC['out_put'] == 'output') {
				$sql = "select o.* , a.realname,a.mobile from ".tablename('hx_zhongchou_order')." o"
					." left join ".tablename('hx_zhongchou_address')." a on o.addressid = a.id "
					. " where $condition ORDER BY o.status DESC, o.createtime DESC ";
				$list = pdo_fetchall($sql,$paras);
				$paytype = array (
					'0' => array('css' => 'default', 'name' => '未支付'),
					'1' => array('css' => 'danger','name' => '余额支付'),
					'2' => array('css' => 'info', 'name' => '在线支付'),
					'3' => array('css' => 'warning', 'name' => '货到付款')
				);
				$orderstatus = array (
					'-1' => array('css' => 'default', 'name' => '已取消'),
					'0' => array('css' => 'danger', 'name' => '待付款'),
					'1' => array('css' => 'info', 'name' => '待发货'),
					'2' => array('css' => 'warning', 'name' => '待收货'),
					'3' => array('css' => 'success', 'name' => '已完成')
				);
				foreach ($list as &$value) {
					$s = $value['status'];
					$value['statuscss'] = $orderstatus[$value['status']]['css'];
					$value['status'] = $orderstatus[$value['status']]['name'];
					if ($s < 1) {
						$value['css'] = $paytype[$s]['css'];
						$value['paytype'] = $paytype[$s]['name'];
						continue;
					}
					$value['css'] = $paytype[$value['paytype']]['css'];
					if ($value['paytype'] == 2) {
						if (empty($value['transid'])) {
							$value['paytype'] = '支付宝支付';
						} else {
							$value['paytype'] = '微信支付';
						}
					} else {
						$value['paytype'] = $paytype[$value['paytype']]['name'];
					}
				}
				if (!empty($list)) {
					foreach ($list as &$row) {
						// !empty($row['addressid']) && $addressids[$row['addressid']] = $row['addressid'];
						$row['dispatch'] = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_dispatch') . " WHERE id = :id", array(':id' => $row['dispatch']));
					}
					unset($row);
				}
				if (!empty($list)) {
					foreach ($list as &$row) {
						// !empty($row['addressid']) && $addressids[$row['addressid']] = $row['addressid'];
						$row['address'] = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_address') . " WHERE id = :id", array(':id' => $row['addressid']));
					}
					unset($row);
				}
				$i = 0;
				foreach ($list as $key => $value) {
					$arr[$i]['ordersn'] = $value['ordersn'];
					$arr[$i]['status'] = $value['status'];
					$arr[$i]['realname'] = $value['realname'];
					$arr[$i]['mobile'] = "'".$value['mobile'];
					$arr[$i]['address'] = $value['address']['province'].'-'.$value['address']['city'].'-'.$value['address']['area'].'-'.$value['address']['address'];
					$arr[$i]['createtime'] = "'".date('Y-m-d H:i:s',$value['createtime']);
					$arr[$i]['dispatchname'] = $value['dispatch']['dispatchname'];
					$i ++;
				}
				//print_r($list);
				$this->exportexcel($arr,array('订单号','状态','真实姓名','电话号码','地址','时间','邮寄方式'),time());
				exit();
			}
			$sql = "select o.* , a.realname,a.mobile from ".tablename('hx_zhongchou_order')." o"
					." left join ".tablename('hx_zhongchou_address')." a on o.addressid = a.id "
					. " where $condition ORDER BY o.status DESC, o.createtime DESC "
					. "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			$list = pdo_fetchall($sql,$paras);
			$paytype = array (
					'0' => array('css' => 'default', 'name' => '未支付'),
					'1' => array('css' => 'danger','name' => '余额支付'),
					'2' => array('css' => 'info', 'name' => '在线支付'),
					'3' => array('css' => 'warning', 'name' => '货到付款')
			);
			$orderstatus = array (
					'-1' => array('css' => 'default', 'name' => '已取消'),
					'0' => array('css' => 'danger', 'name' => '待付款'),
					'1' => array('css' => 'info', 'name' => '待发货'),
					'2' => array('css' => 'warning', 'name' => '待收货'),
					'3' => array('css' => 'success', 'name' => '已完成')
			);
			foreach ($list as &$value) {
				$s = $value['status'];
				$value['statuscss'] = $orderstatus[$value['status']]['css'];
				$value['status'] = $orderstatus[$value['status']]['name'];
				if ($s < 1) {
					$value['css'] = $paytype[$s]['css'];
					$value['paytype'] = $paytype[$s]['name'];
					continue;
				}
				$value['css'] = $paytype[$value['paytype']]['css'];
				if ($value['paytype'] == 2) {
					if (empty($value['transid'])) {
						$value['paytype'] = '支付宝支付';
					} else {
						$value['paytype'] = '微信支付';
					}
				} else {
					$value['paytype'] = $paytype[$value['paytype']]['name'];
				}
			}
			$total = pdo_fetchcolumn(
						'SELECT COUNT(*) FROM ' . tablename('hx_zhongchou_order') . " o "
						." left join ".tablename('hx_zhongchou_address')." a on o.addressid = a.id "
						." WHERE $condition", $paras);
			$pager = pagination($total, $pindex, $psize);
			if (!empty($list)) {
				foreach ($list as &$row) {
					// !empty($row['addressid']) && $addressids[$row['addressid']] = $row['addressid'];
					$row['dispatch'] = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_dispatch') . " WHERE id = :id", array(':id' => $row['dispatch']));
				}
				unset($row);
			}
//			if (!empty($addressids)) {
//				$address = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_address') . " WHERE id IN ('" . implode("','", $addressids) . "')", array(), 'id');
//			}
		} elseif ($operation == 'detail') {
			$id = intval($_GPC['id']);
			$item = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_order') . " WHERE id = :id", array(':id' => $id));
			if (empty($item)) {
				message("抱歉，订单不存在!", referer(), "error");
			}
			if (checksubmit('confirmsend')) {
				if (!empty($_GPC['isexpress']) && empty($_GPC['expresssn'])) {
					message('请输入快递单号！');
				}
				$item = pdo_fetch("SELECT transid FROM " . tablename('hx_zhongchou_order') . " WHERE id = :id", array(':id' => $id));
				if (!empty($item['transid'])) {
					$this->changeWechatSend($id, 1);
				}
				pdo_update(
					'hx_zhongchou_order',
					array(
						'status' => 2,
						'remark' => $_GPC['remark'],
						'express' => $_GPC['express'],
						'expresscom' => $_GPC['expresscom'],
						'expresssn' => $_GPC['expresssn'],
					),
					array('id' => $id)
				);
				message('发货操作成功！', referer(), 'success');
			}
			if (checksubmit('cancelsend')) {
				$item = pdo_fetch("SELECT transid FROM " . tablename('hx_zhongchou_order') . " WHERE id = :id", array(':id' => $id));
				if (!empty($item['transid'])) {
					$this->changeWechatSend($id, 0, $_GPC['cancelreson']);
				}
				pdo_update(
					'hx_zhongchou_order',
					array(
						'status' => 1,
						'remark' => $_GPC['remark'],
					),
					array('id' => $id)
				);
				message('取消发货操作成功！', referer(), 'success');
			}
			if (checksubmit('finish')) {
				pdo_update('hx_zhongchou_order', array('status' => 3, 'remark' => $_GPC['remark']), array('id' => $id));
				message('订单操作成功！', referer(), 'success');
			}
			if (checksubmit('cancel')) {
				pdo_update('hx_zhongchou_order', array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
				message('取消完成订单操作成功！', referer(), 'success');
			}
			if (checksubmit('cancelpay')) {
				pdo_update('hx_zhongchou_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
				message('取消订单付款操作成功！', referer(), 'success');
			}
			if (checksubmit('confrimpay')) {
				pdo_update('hx_zhongchou_order', array('status' => 1, 'paytype' => 2, 'remark' => $_GPC['remark']), array('id' => $id));


				$order = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_order') . " WHERE id = '{$id}'");
				$pid = $order['pid'];
				$item_id = $order['item_id'];
				$project = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project') . " WHERE id = '{$pid}'");
				$item = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project_item') . " WHERE id = '{$item_id}'");
				pdo_update('hx_zhongchou_project',array('finish_price'=>$project['finish_price'] + $order['item_price'],'donenum'=>$project['donenum']+1),array('id'=>$pid));
				pdo_update('hx_zhongchou_project_item',array('donenum'=>$item['donenum']+1),array('id'=>$item_id));
				message('确认订单付款操作成功！', referer(), 'success');
			}
			if (checksubmit('close')) {
				$item = pdo_fetch("SELECT transid FROM " . tablename('hx_zhongchou_order') . " WHERE id = :id", array(':id' => $id));
				if (!empty($item['transid'])) {
					$this->changeWechatSend($id, 0, $_GPC['reson']);
				}
				pdo_update('hx_zhongchou_order', array('status' => -1, 'remark' => $_GPC['remark']), array('id' => $id));
				message('订单关闭操作成功！', referer(), 'success');
			}
			if (checksubmit('open')) {
				pdo_update('hx_zhongchou_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
				message('开启订单操作成功！', referer(), 'success');
			}
			$dispatch = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_dispatch') . " WHERE id = :id", array(':id' => $item['dispatch']));
			if (!empty($dispatch) && !empty($dispatch['express'])) {
				$express = pdo_fetch("select * from " . tablename('hx_zhongchou_express') . " WHERE id=:id limit 1", array(":id" => $dispatch['express']));
			}
			$item['user'] = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_address') . " WHERE id = {$item['addressid']}");
		} elseif ($operation == 'delete') {
			/*订单删除*/
			$orderid = intval($_GPC['id']);
			if (pdo_delete('hx_zhongchou_order', array('id' => $orderid))) {
				message('订单删除成功', $this->createWebUrl('order', array('op' => 'display')), 'success');
			} else {
				message('订单不存在或已被删除', $this->createWebUrl('order', array('op' => 'display')), 'error');
			}
		}
		include $this->template('order');
	}
	public function doWebProject() {
		global $_GPC, $_W;
		load()->func('tpl');
		$category = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC", array(), 'id');
		$dispatch = pdo_fetchall("select id,dispatchname,dispatchtype,firstprice,firstweight,secondprice,secondweight from " . tablename("hx_zhongchou_dispatch") . " WHERE weid = {$_W['uniacid']} order by displayorder desc");
		if (!empty($category)) {
			$children = '';
			foreach ($category as $cid => $cate) {
				if (!empty($cate['parentid'])) {
					$children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
				}
			}
		}
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'post') {
			$id = intval($_GPC['id']);
			$item_id = intval($_GPC['item_id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project') . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，项目不存在或是已经删除！', '', 'error');
				}
			}
			if (empty($category)) {
				message('抱歉，请您先添加项目分类！', $this->createWebUrl('category', array('op' => 'post')), 'error');
			}
			$step = intval($_GPC['step']) ? intval($_GPC['step']) : 1;
			if ($step == 1) {
					
			}elseif($step == 2) {
				$items = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_project_item') . " WHERE weid = '{$_W['uniacid']}' AND pid = '{$id}' ORDER BY id ASC");
				//print_r($items);
				if ($item_id) {
					$item_info = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_project_item') . " WHERE id = :id", array(':id' => $item_id));
				}
				if (checksubmit('submit')) {
					if (empty($_GPC['title'])) {
						message('项目名称必填，请返回修改');
					}
					$data = array(
						'weid' => $_W['uniacid'],
						'title' => $_GPC['title'],
						'limit_price' => intval($_GPC['limit_price']),
						'deal_days' => intval($_GPC['deal_days']),
						'isrecommand' => intval($_GPC['isrecommand']),
						'pcate' => intval($_GPC['pcate']),
						'ccate' => intval($_GPC['ccate']),
						'thumb' => $_GPC['thumb'],
						'brief' => htmlspecialchars_decode($_GPC['brief']),
						'content' => htmlspecialchars_decode($_GPC['content']),
						'url' => $_GPC['url'],
						'nosubuser' => intval($_GPC['nosubuser']),
						'createtime' => TIMESTAMP,
						);
					if (empty($id)) {
						pdo_insert('hx_zhongchou_project', $data);
						$id = pdo_insertid();
					} else {
						unset($data['createtime']);
						pdo_update('hx_zhongchou_project', $data, array('id' => $id));
					}
					message('保存成功,即将进入下一步',$this->createWebUrl('project',array('id'=>$id,'op'=>'post','step'=>'2')),'success');
				}
			}elseif($step == 3) {
				if (checksubmit('submit')) {
					$insert = array(
						'weid' => $_W['uniacid'],
						'pid' => intval($_GPC['id']),
						'price' => $_GPC['price'],
						'description' => htmlspecialchars_decode($_GPC['description']),
						'thumb' => $_GPC['thumb'],
						'limit_num' => intval($_GPC['limit_num']),
						'repaid_day' => intval($_GPC['repaid_day']),
						'return_type' => intval($_GPC['return_type']),
						'dispatch' => $_GPC['dispatch'],
						'createtime' => TIMESTAMP,
						);
					if (empty($item_id)) {
						pdo_insert('hx_zhongchou_project_item', $insert);
					} else {
						unset($insert['createtime']);
						pdo_update('hx_zhongchou_project_item', $insert, array('id' => $item_id));
					}
					message('保存成功,继续添加',$this->createWebUrl('project',array('id'=>$id,'op'=>'post','step'=>'2')),'success');
				}
				$items = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_project_item') . " WHERE weid = '{$_W['uniacid']}' AND pid = '{$id}' ORDER BY id ASC");
				if (empty($items)) {
					message('您尚未添加项目回报，请返回添加',$this->createWebUrl('project',array('id'=>$id,'op'=>'post','step'=>'2')),'error');
				}
			}elseif ($step == 4) {
				if (checksubmit('finish')) {
					pdo_update('hx_zhongchou_project', array('status' => 1), array('id' => $id));
					message('恭喜您，活动已经成功开始！',$this->createWebUrl('project',array('op'=>'display')),'success');
				}else{
					message('活动保存成功！',$this->createWebUrl('project',array('op'=>'display')),'success');
				}
			}
		} elseif ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$condition = '';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
			}
			if (!empty($_GPC['cate_2'])) {
				$cid = intval($_GPC['cate_2']);
				$condition .= " AND ccate = '{$cid}'";
			} elseif (!empty($_GPC['cate_1'])) {
				$cid = intval($_GPC['cate_1']);
				$condition .= " AND pcate = '{$cid}'";
			}
			if (isset($_GPC['status'])) {
				$condition .= " AND status = '" . intval($_GPC['status']) . "'";
			}
			$list = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_project') . " WHERE weid = '{$_W['uniacid']}'  $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hx_zhongchou_project') . " WHERE weid = '{$_W['uniacid']}'  $condition");
			$pager = pagination($total, $pindex, $psize);
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id, thumb FROM " . tablename('hx_zhongchou_project') . " WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，项目不存在或是已经被删除！');
			}
			pdo_delete('hx_zhongchou_project', array('id' => $id));
			pdo_delete('hx_zhongchou_project_item', array('pid' => $id));
			message('删除成功！', referer(), 'success');
		} elseif ($operation == 'itemdelete') {
			$id = intval($_GPC['id']);
			$item_id = intval($_GPC['item_id']);
			$row = pdo_fetch("SELECT id, thumb FROM " . tablename('hx_zhongchou_project_item') . " WHERE id = :id", array(':id' => $item_id));
			if (empty($row)) {
				message('抱歉，项目不存在或是已经被删除！');
			}
			pdo_delete('hx_zhongchou_project_item', array('id' => $item_id));
			message('删除成功！', $this->createWebUrl('project',array('id'=>$id,'op'=>'post','step'=>'2')), 'success');
		}
		include $this->template('project');
	}
	public function doWebSetProjectProperty() {
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$type = $_GPC['type'];
		$data = intval($_GPC['data']);
		if (in_array($type, array('hot', 'recommand'))) {
			$data = ($data==1?'0':'1');
			pdo_update("hx_zhongchou_project", array("is" . $type => $data), array("id" => $id, "weid" => $_W['uniacid']));
			die(json_encode(array("result" => 1, "data" => $data)));
		}
		if (in_array($type, array('status'))) {
			  $data = ($data==1?'0':'1');
		   pdo_update("hx_zhongchou_project", array($type => $data), array("id" => $id, "weid" => $_W['uniacid']));
		   die(json_encode(array("result" => 1, "data" => $data)));
		}
		die(json_encode(array("result" => 0)));
	}
	public function doMobileContactUs() {
		global $_W;
		$cfg = $this->module['config'];
		include $this->template('contactus');
	}
	public function doWebCategory() {
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			if (!empty($_GPC['displayorder'])) {
				foreach ($_GPC['displayorder'] as $id => $displayorder) {
					pdo_update('hx_zhongchou_category', array('displayorder' => $displayorder), array('id' => $id));
				}
				message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
			}
			$children = array();
			$category = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
			foreach ($category as $index => $row) {
				if (!empty($row['parentid'])) {
					$children[$row['parentid']][] = $row;
					unset($category[$index]);
				}
			}
			include $this->template('category');
		} elseif ($operation == 'post') {
			$parentid = intval($_GPC['parentid']);
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$category = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_category') . " WHERE id = '$id'");
			} else {
				$category = array(
					'displayorder' => 0,
				);
			}
			if (!empty($parentid)) {
				$parent = pdo_fetch("SELECT id, name FROM " . tablename('hx_zhongchou_category') . " WHERE id = '$parentid'");
				if (empty($parent)) {
					message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
				}
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['catename'])) {
					message('抱歉，请输入分类名称！');
				}
				$data = array(
					'weid' => $_W['uniacid'],
					'name' => $_GPC['catename'],
					'thumb' => $_GPC['thumb'],
					'enabled' => intval($_GPC['enabled']),
					'displayorder' => intval($_GPC['displayorder']),
					'isrecommand' => intval($_GPC['isrecommand']),
					'description' => $_GPC['description'],
					'parentid' => intval($parentid),
				);
				if (!empty($id)) {
					unset($data['parentid']);
					pdo_update('hx_zhongchou_category', $data, array('id' => $id));
				} else {
					pdo_insert('hx_zhongchou_category', $data);
					$id = pdo_insertid();
				}
				message('更新分类成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
			}
			include $this->template('category');
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$category = pdo_fetch("SELECT id, parentid FROM " . tablename('hx_zhongchou_category') . " WHERE id = '$id'");
			if (empty($category)) {
				message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display')), 'error');
			}
			pdo_delete('hx_zhongchou_category', array('id' => $id, 'parentid' => $id), 'OR');
			message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
		}
	}
	public function doWebDispatch() {
		global $_W, $_GPC;
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$list = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_dispatch') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
		} elseif ($operation == 'post') {
			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				$data = array(
					'weid' => $_W['uniacid'],
					'displayorder' => intval($_GPC['dispatch_name']),
					'dispatchtype' => intval($_GPC['dispatchtype']),
					'dispatchname' => $_GPC['dispatchname'],
					'express' => $_GPC['express'],
					'firstprice' => $_GPC['firstprice'],
					'firstweight' => $_GPC['firstweight'],
					'secondprice' => $_GPC['secondprice'],
					'secondweight' => $_GPC['secondweight'],
					'description' => $_GPC['description'],
                    'enabled'=> intval($_GPC['enabled'])
				);
				if (!empty($id)) {
					pdo_update('hx_zhongchou_dispatch', $data, array('id' => $id));
				} else {
					pdo_insert('hx_zhongchou_dispatch', $data);
					$id = pdo_insertid();
				}
				message('更新配送方式成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
			}
			//修改
			$dispatch = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_dispatch') . " WHERE id = '$id' and weid = '{$_W['uniacid']}'");
			$express = pdo_fetchall("select * from " . tablename('hx_zhongchou_express') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$dispatch = pdo_fetch("SELECT id  FROM " . tablename('hx_zhongchou_dispatch') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
			if (empty($dispatch)) {
				message('抱歉，配送方式不存在或是已经被删除！', $this->createWebUrl('dispatch', array('op' => 'display')), 'error');
			}
			pdo_delete('hx_zhongchou_dispatch', array('id' => $id));
			message('配送方式删除成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
		} else {
			message('请求方式不存在');
		}
		include $this->template('dispatch', TEMPLATE_INCLUDEPATH, true);
	}
	public function doWebExpress() {
		global $_W, $_GPC;
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$list = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_express') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
		} elseif ($operation == 'post') {
			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				if (empty($_GPC['express_name'])) {
					message('抱歉，请输入物流名称！');
				}
				$data = array(
					'weid' => $_W['uniacid'],
					'displayorder' => intval($_GPC['displayorder']),
					'express_name' => $_GPC['express_name'],
					'express_url' => $_GPC['express_url'],
					'express_area' => $_GPC['express_area'],
				);
				if (!empty($id)) {
					unset($data['parentid']);
					pdo_update('hx_zhongchou_express', $data, array('id' => $id));
				} else {
					pdo_insert('hx_zhongchou_express', $data);
					$id = pdo_insertid();
				}
				message('更新物流成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
			}
			//修改
			$express = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_express') . " WHERE id = '$id' and weid = '{$_W['uniacid']}'");
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$express = pdo_fetch("SELECT id  FROM " . tablename('hx_zhongchou_express') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
			if (empty($express)) {
				message('抱歉，物流方式不存在或是已经被删除！', $this->createWebUrl('express', array('op' => 'display')), 'error');
			}
			pdo_delete('hx_zhongchou_express', array('id' => $id));
			message('物流方式删除成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
		} else {
			message('请求方式不存在');
		}
		include $this->template('express', TEMPLATE_INCLUDEPATH, true);
	}
	public function doWebAdv() {
		global $_W, $_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$list = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_adv') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
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
					pdo_update('hx_zhongchou_adv', $data, array('id' => $id));
				} else {
					pdo_insert('hx_zhongchou_adv', $data);
					$id = pdo_insertid();
				}
				message('更新幻灯片成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
			}
			$adv = pdo_fetch("select * from " . tablename('hx_zhongchou_adv') . " where id=:id and weid=:weid limit 1", array(":id" => $id, ":weid" => $_W['uniacid']));
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$adv = pdo_fetch("SELECT id  FROM " . tablename('hx_zhongchou_adv') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
			if (empty($adv)) {
				message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('adv', array('op' => 'display')), 'error');
			}
			pdo_delete('hx_zhongchou_adv', array('id' => $id));
			message('幻灯片删除成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
		} else {
			message('请求方式不存在');
		}
		include $this->template('adv', TEMPLATE_INCLUDEPATH, true);
	}

	public function doMobileMyOrder() {
		global $_W, $_GPC;
		$this->checkAuth();
		$carttotal = $this->getCartTotal();
		$op = $_GPC['op'];
		if ($op == 'confirm') {
			$orderid = intval($_GPC['orderid']);
			$order = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_order') . " WHERE id = :id AND from_user = :from_user", array(':id' => $orderid, ':from_user' => $_W['fans']['from_user']));
			if (empty($order)) {
				message('抱歉，您的订单不存或是已经被取消！', $this->createMobileUrl('myorder'), 'error');
			}
			pdo_update('hx_zhongchou_order', array('status' => 3), array('id' => $orderid, 'from_user' => $_W['fans']['from_user']));
			message('确认收货完成！', $this->createMobileUrl('myorder'), 'success');
		} else if ($op == 'detail') {
			$orderid = intval($_GPC['orderid']);
			$item = pdo_fetch("SELECT * FROM " . tablename('hx_zhongchou_order') . " WHERE weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}' and id='{$orderid}' limit 1");
			if (empty($item)) {
				message('抱歉，您的订单不存或是已经被取消！', $this->createMobileUrl('myorder'), 'error');
			}
			$address = pdo_fetch("select * from " . tablename('hx_zhongchou_address') . " where id=:id limit 1", array(":id" => $item['addressid']));
			//print_r($address);
			$dispatch = pdo_fetch("select id,dispatchname from " . tablename('hx_zhongchou_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
			include $this->template('order_detail');
		} else {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$status = intval($_GPC['status']);
			$where = " weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'";
			if ($status == 2) {
				$where.=" and ( status=1 or status=2 )";
			} else {
				$where.=" and status=$status";
			}
			$list = pdo_fetchall("SELECT * FROM " . tablename('hx_zhongchou_order') . " WHERE $where ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hx_zhongchou_order') . " WHERE weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'");
			$pager = pagination($total, $pindex, $psize);
			include $this->template('order');
		}
	}

	protected function exportexcel($data=array(),$title=array(),$filename='report'){
    	header("Content-type:application/octet-stream");
    	header("Accept-Ranges:bytes");
    	header("Content-type:application/vnd.ms-excel");  
    	header("Content-Disposition:attachment;filename=".$filename.".xls");
    	header("Pragma: no-cache");
    	header("Expires: 0");
    	//导出xls 开始
    	if (!empty($title)){
    	    foreach ($title as $k => $v) {
    	        $title[$k]=iconv("UTF-8", "GB2312",$v);
    	    }
    	    $title= implode("\t", $title);
    	    echo "$title\n";
    	}
    	if (!empty($data)){
    	    foreach($data as $key=>$val){
    	        foreach ($val as $ck => $cv) {
    	            $data[$key][$ck]=iconv("UTF-8", "GB2312", $cv);
    	        }
    	        $data[$key]=implode("\t", $data[$key]);
    	        
    	    }
    	    echo implode("\n",$data);
    	}
 	}

}