<?php
/**

 */
defined('IN_IA') or exit('Access Denied');

class Lxy_buscardModuleSite extends WeModuleSite {
	public $cardtable='lxy_bussiness_card';
	public $coptable='lxy_bussiness_card_cop';
	public $classtable='lxy_bussiness_card_class';
	public $replytable='lxy_bussiness_card_reply';
	public $tplname=array('default','card_yellow','card_bull','card_fas','card_bull_s','card_deful','card_mount');
	
	public function getProfileTiles() {

	}

	public function getHomeTiles() {
	}
	
	
	public function doWebCardlist() {
		global $_W,$_GPC;
		$uniacid=$_W['uniacid'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = '';
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND username LIKE '%{$_GPC['keyword']}%'";
		}
		$list = pdo_fetchall("SELECT * FROM ".tablename($this->cardtable)." WHERE uniacid = '{$uniacid}' $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->cardtable) . " WHERE uniacid = '{$uniacid}' $condition");
		$pager = pagination($total, $pindex, $psize);
		include $this->template('cardlist');
	}
	
	public function doWebCardadd() {
		global $_GPC, $_W;
		$uniacid=$_W['uniacid'];
		$id = intval($_GPC['id']);
		if (!empty($id)) 
		{
			$item = pdo_fetch("SELECT * FROM ".tablename($this->cardtable)." WHERE id = :id", array(':id' => $id));
			if (empty($item))
			{
				message('抱歉，名片不存在或是已经删除！', '', 'error');
			}
		}	
	
		if (checksubmit('submit')) {
			if (empty($_GPC['username'])) {
				message('请输入姓名！');
			}
			$data = array(
					'username'=>$_GPC['username'],
					'uniacid' => $_W['uniacid'],
                            'thumb'=>$_GPC['thumb'],
					'degree' => $_GPC['degree'],
					'mobile' => $_GPC['mobile'],
					'company' => $_GPC['company'],
					'tel' => $_GPC['tel'],
					'qq' => $_GPC['qq'],
					'email' => $_GPC['email'],
					'websiteswitch' => $_GPC['websiteswitch'],
					'website' =>  $_GPC['website'],
					'addrswitch' => $_GPC['addrswitch'],
					'addr' => $_GPC['addr'],
					'jw_addr' => $_GPC['jw_addr'],
					'lng' => $_GPC['baidumap']['lng'],
					'lat' => $_GPC['baidumap']['lat'],					
					'province' =>$_GPC['district']['province'],
					'city' => $_GPC['district']['city'],
					'dist' => $_GPC['district']['dist'],				
					'createtime' => TIMESTAMP,
			);
			 
			if (empty($id))
			{
				pdo_insert($this->cardtable, $data);
			} else {
				unset($data['createtime']);
				pdo_update($this->cardtable, $data, array('id' => $id));
			}
			message('名片信息更新成功！', $this->createWebUrl('cardlist', array()), 'success');
	
		}
		include $this->template('cardadd');
	}
	
	public function doWebCopadd() {
		global $_GPC, $_W;
		$uniacid=$_W['uniacid'];
		$item = pdo_fetch("SELECT * FROM ".tablename($this->coptable)." WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));
		if (checksubmit('submit')) {
			if (empty($_GPC['copname'])) {
				message('请设置公司名称！');
			}
			$data = array(
					'copname'=>$_GPC['copname'],
					'copintro'=>$_GPC['copintro'],
					'uniacid' => $_W['uniacid'],
                            'thumb'=>$_GPC['thumb'],
					'website' =>  $_GPC['website'],
					'bankuser'=>$_GPC['bankuser'],
					'bankname'=>$_GPC['bankname'],
					'bankaccount'=>$_GPC['bankaccount'],
					'addr' => $_GPC['addr'],
					'jw_addr' => $_GPC['addr'],
					'lng' => $_GPC['baidumap']['lng'],
					'lat' => $_GPC['baidumap']['lat'],
					'province' => $_GPC['district']['province'],
					'city' => $_GPC['district']['city'],
					'dist' => $_GPC['district']['dist'],
					'createtime' => TIMESTAMP,
			);
			 
			if (empty($item))
			{
				pdo_insert($this->coptable, $data);
			}
			 else
		  {
		  	unset($data['createtime']);
		  	pdo_update($this->coptable, $data, array('uniacid' => $uniacid));			  	
			}
			message('通用信息更新成功！', $this->createWebUrl('copadd', array()), 'success');	
		}
		include $this->template('copadd');
	}
	
	public function doWebClasslist() {
		global $_W,$_GPC;
		$uniacid=$_W['uniacid'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = '';
		if (!empty($_GPC['keyword'])) {
			$condition .= " AND cname LIKE '%{$_GPC['keyword']}%'";
		}
		$list = pdo_fetchall("SELECT * FROM ".tablename($this->classtable)." WHERE uniacid = '{$uniacid}' $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->classtable) . " WHERE uniacid = '{$uniacid}' $condition");
		$pager = pagination($total, $pindex, $psize);
		include $this->template('classlist');
	}
	
	
	public function doWebClassadd() {
		global $_GPC, $_W;
		$id=$_GPC['id'];
		$uniacid=$_W['uniacid'];
		if(!empty($id))
		{
			$item = pdo_fetch("SELECT * FROM ".tablename($this->classtable)." WHERE uniacid = :uniacid and id=:id", array(':uniacid' => $uniacid,':id'=>$id));
			if(empty($item))
			{
				message('抱歉,您编辑的分类不存在');
			}
		}
		if (checksubmit('submit')) {
			if (empty($_GPC['cname'])) {
				message('请输入分类名称！');
			}
			$data = array(
					'cname'=>$_GPC['cname'],
					'isshow'=>$_GPC['isshow'],
					'uniacid' => $_W['uniacid'],		
					'outurl'=> $_GPC['outurl'],	
                                                                                        'thumb'=>$_GPC['thumb'],
			);
				
			if (empty($id))
			{
				pdo_insert($this->classtable, $data);
			}
			else
			{
				pdo_update($this->classtable, $data, array('id' => $id));
			}
			message('产品分类更新成功！', $this->createWebUrl('classlist', array()), 'success');
		}
		include $this->template('classadd');
	}
	
	public function doWebTplsetindex(){
		global $_GPC, $_W;
		$id = $_GPC['id'];
		$uniacid=$_W['uniacid'];
		if(empty($id))
		{
			message('抱歉，您查看的名片不存在或已经删除！','','error');
		}
		$list=$this->tplname;
		$style = pdo_fetchcolumn("SELECT style FROM ".tablename($this->cardtable)." WHERE id = :id and uniacid=:uniacid", array(':id' => $id,':uniacid'=>$uniacid));
		if(empty($style))
		{
			$style='default';
		}
		include $this->template('tplsetindex');
	}
	
	public function doWebAjaxChangetpl()
	{
		global $_GPC, $_W;
		$id=$_GPC['id'];
		$uniacid=$_W['uniacid'];
		$tplname = $_GPC['tpl'];
		$tplname=in_array($tplname, $this->tplname)?$tplname:'';
		$ishave= pdo_fetchcolumn("SELECT count(1) FROM ".tablename($this->cardtable)." WHERE id = :id and uniacid=:uniacid", array(':id' => $id,':uniacid'=>$uniacid));
		//正常情况
		if($tplname!=''&&$ishave)
		{
			$data=array(
					'style'=>$tplname,
			);
			
			$ret=pdo_update($this->cardtable, $data, array('id' => $id));
			if ($ret)
			{
				header('Content-type: '.'text/html');
				echo '1';
				die();
			}
			
		}
		echo '0';
	}
	
	public function doWebstatus( $rid = 0) {
		global $_GPC;
		$rid = $_GPC['rid'];
		$data = array(
				'status' => $_GPC['status'],
		);	
		if(pdo_update($this->replytable,$data,array('rid' => $rid)))
		{
			message('模块操作成功！', '', 'ajax');
		}
		else 
		{
			message('请保存规则后再进行设置！', '', 'ajax');
		}
	}
	
	public function doWebDeletecard() {
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch("SELECT * FROM ".tablename($this->cardtable)." WHERE id = :id and uniacid=:uniacid" , array(':id' => $id,':uniacid'=>$_W['uniacid']));
		if (empty($item)) {
			message('抱歉，名片不存在或是已经删除！', '', 'error');
		}
                
		if (!empty($item['thumb'])) {
			load()->func('file'); file_delete($item['thumb']);
		}
		pdo_delete($this->cardtable, array('id' => $item['id']));
		message('删除成功！', referer(), 'success');
	}
	
	public function doWebDeleteclass() {
		global $_GPC,$_W;
		$uniacid=$_W['uniacid'];
		$id = intval($_GPC['id']);
		$item = pdo_fetch("SELECT * FROM ".tablename($this->classtable)." WHERE id = :id and uniacid=:uniacid" , array(':id' => $id,':uniacid'=>$_W['uniacid']));
		if (empty($item)) {
			message('抱歉，分类不存在或是已经删除！', '', 'error');
		}
		if (!empty($item['thumb'])) {
			load()->func('file'); file_delete($item['thumb']);
		}
		pdo_delete($this->classtable, array('id' => $item['id']));
		message('删除成功！', referer(), 'success');
	}
	
	public function doMobileViewcard() {
		global $_GPC, $_W;
		$id = $_GPC['id'];
		$uniacid=$_W['uniacid'];
		$weaccount=$_W['account'];
		$item = pdo_fetch("SELECT * FROM ".tablename($this->cardtable)." WHERE id = :id", array(':id' => $id));		
		$copinfo=pdo_fetch("SELECT * FROM ".tablename($this->coptable)." WHERE uniacid = :uniacid", array(':uniacid' => $uniacid));		
		if(empty($item))
		{
			message('您指定的名片不存在或已经被删除','','error');			
		}
		if(empty($copinfo))
		{
			message('请先完善公司设置中相关信息，谢谢！','','error');
		}
		$item['copname']=$copinfo['copname'];
                                
		$item['coplogo']=$copinfo['thumb'];
		$item['bankuser']=$copinfo['bankuser'];
		$item['bankname']=$copinfo['bankname'];
		$item['bankaccount']=$copinfo['bankaccount'];
		if($item['addrswitch']==1)
		{
			$item['addr']=$copinfo['addr'];
			$item['jw_addr']=$copinfo['jw_addr'];
			$item['lng']=$copinfo['lng'];
			$item['lat']=$copinfo['lat'];			
		}
		if($item['websiteswitch']==1)
		{
			$item['website']=$copinfo['website'];
		}
	
		$classes = pdo_fetchall("SELECT * FROM ".tablename($this->classtable)." WHERE uniacid = :uniacid and isshow=1", array(':uniacid' => $uniacid));
		include $this->template('tpl'.$item['style']);
	}
}
