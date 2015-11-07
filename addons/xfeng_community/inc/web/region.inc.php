<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台小区信息
 */
defined('IN_IA') or exit('Access Denied');
	global $_GPC,$_W;
	$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	//显示小区信息
	if($op == "display"){
		$regions = $this->regions();
	}
	//删除小区信息
	if($op == 'delete'){
		$id = intval($_GPC['id']);
		pdo_delete("xcommunity_region", array('id' => $id));
		message('删除成功',referer(), 'success');
	}
	//添加和更新小区信息
	if(checksubmit('submit')){
		for ($i=0; $i <count($_GPC['titles']) ; $i++) { 
			$ids = $_GPC['ids'];
			$id  = trim(implode(',', $ids),',');
			$insert = array(
								'title'   =>  $_GPC['titles'][$i] ,
								'linkmen' =>  $_GPC['linkmen'][$i],
								'linkway' =>  $_GPC['linkways'][$i],
								'weid'    =>  $_W['weid'],
			 			);
			if($ids[$i] !=NULL){
				pdo_update("xcommunity_region",$insert,array('id'=>$ids[$i]));
			}else{
				pdo_insert("xcommunity_region",$insert);
			}
		}
		message('更新信息成功',referer(), 'success');
	}

	include $this->template('region');