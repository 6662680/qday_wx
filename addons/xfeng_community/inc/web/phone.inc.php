<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区电话信息
 */
defined('IN_IA') or exit('Access Denied');
	global $_GPC,$_W;
	$id = $_GPC['id'];
	$op = !empty($_GPC['op']) ? $_GPC['op'] : '';
	if ($op == '') {
		//常用号码显示
		$pindex = max(1, intval($_GPC['page']));
		$psize  = 10;
		$sql    = "select * from ".tablename("xcommunity_phone")."where weid = '{$_W['weid']}' LIMIT ".($pindex - 1) * $psize.','.$psize;
		$phones = pdo_fetchall($sql);
		$total  = pdo_fetchcolumn('select count(*) from'.tablename("xcommunity_phone")."where  weid = '{$_W['weid']}' ");
		$pager  = pagination($total, $pindex, $psize);
		if(checksubmit('submit')){
			//常用电话添加和修改
			for ($i=0; $i <count($_GPC['titles']) ; $i++) { 
					$ids       = $_GPC['ids'];
					$insert    = array(
						'title'    =>  $_GPC['titles'][$i] ,
						'weid'     =>  $_W['weid'],
						'phone'    =>  $_GPC['phones'][$i],
					);
				if($ids[$i] !=NULL){
					pdo_update("xcommunity_phone",$insert,array('id'=>$ids[$i]));
				}else{
					pdo_insert("xcommunity_phone",$insert);
				}
			}
			message('更新信息成功',referer(), 'success');
		}
	}elseif ($op == 'delete') {
		//常用号码删除
		pdo_delete("xcommunity_phone",array('id'=>$id));
	}
	include $this->template('phone');