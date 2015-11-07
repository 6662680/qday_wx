<?php
/**
 * 朋友圈语音模块处理程序
 */
defined('IN_IA') or exit('Access Denied');

require "jssdk.class.php";

class Lee_tlvoiceModuleSite extends WeModuleSite {
	public function doMobileindex() {
		global $_W,$_GPC;
		$weixin = new jssdk();
		$wx = $weixin->getSign();
		$qr = $_W['attachurl'].'/qrcode_'.$account['acid'].'.jpg';
		if ($_W['isajax']) {
			$keyid = $this->randstr();
			$data = array(
				'uniacid'=>$_W['uniacid'],
				'serverid' => $_GPC['serverid'],
				'timelength' => $_GPC['timelength'],
				'keyid' => $keyid
			);			
			pdo_insert('lee_tlvoice_record',$data);			
            $data = array(
                    'ret' => 0,
                    'keyid' => $keyid
                );			
			die(json_encode($data));
		}		
		include $this->template('index');
	}

	private function randstr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
		  $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	  }
	
	public function doMobileplay() {
		global $_W,$_GPC;
		$weixin = new jssdk();
		$wx = $weixin->getSign();
		$serverid = $_GPC['serverId'];
		$date = $_GPC['date'];
		$recordtime = $_GPC['recordtime'];
		$qr = $_W['attachurl'].'/qrcode_'.$account['acid'].'.jpg';
		include $this->template('play');	
	}	

	public function doMobileerror() {
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$url = $_W['siteroot'].'app/' . substr($this->createMobileUrl('index'), 2);
	include $this->template('error');	
	}
		
	public function doWebdata() {
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT * FROM " . tablename('lee_tlvoice_record') . " WHERE uniacid = '{$uniacid}' ORDER BY id ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('lee_tlvoice_record')." WHERE uniacid = '{$_W['uniacid']}'");
		$pager = pagination($total, $pindex, $psize);
		include $this->template('data');
	}
	public function doWebdatadelete() {
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		pdo_delete("lee_tlvoice_record",array('id' => $id ));
		message("删除成功",referer(),'success');
	}	
}