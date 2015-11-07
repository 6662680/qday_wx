<?php
/**
 * 微小店辅助工具
 */
defined('IN_IA') or exit('Access Denied');

require 'MicrobCore.class.php';
class ShoppingHook {
	private $core = null;

	public function __construct() {
		global $_W;
		if(!empty($_W['account']) && !empty($_W['fans'])) {
			$this->core = new MicrobCore($_W['account']);
		}
	}

	public function submitNotify($tid) {
		if(empty($this->core)) {
			return;
		}
		global $_W;
		$tid = intval($tid);
		$order = pdo_fetch("SELECT price, from_user FROM " . tablename('shopping_order') . " WHERE id = '{$tid}'");
		$ordergoods = pdo_fetchall("SELECT goodsid, total FROM " . tablename('shopping_order_goods') . " WHERE orderid = '{$tid}'", array(), 'goodsid');
		$goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit, total FROM " . tablename('shopping_goods') . " WHERE id IN ('" . implode("','", array_keys($ordergoods)) . "')");
		if(count($goods) > 1) {
			$title = $goods[0]['title'] . '等多件商品';
		} else {
			$title = $goods[0]['title'];
		}

		$openid = $order['from_user'];
		$t = array();
        $t['buyer'] = $_W['fans']['nickname'];
		$t['details'] = array();
		$t['details'][] = array(
			'title'		=> $title,
			'price'		=> $order['price'] 
		);
		$ret = $this->core->submitNotify($openid, $t);
	}
}
