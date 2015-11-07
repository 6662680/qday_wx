<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 后台分类管理
 */
defined('IN_IA') or exit('Access Denied');

	global $_GPC,$_W;
		$op       = !empty($_GPC['op']) ? $_GPC['op']:'display'; 
		$parentid = intval($_GPC['parentid']);
		$id       = intval($_GPC['id']);
		if ($op == 'post') {
			//编辑分类信息
			if (!empty($id)) {
				$category = pdo_fetch("SELECT * FROM".tablename('xcommunity_servicecategory')."WHERE id=:id",array(':id' => $id));
			}
			//添加分类主ID
			if (!empty($parentid)) {
				$parent = pdo_fetch("SELECT * FROM".tablename('xcommunity_servicecategory')."WHERE id=:parentid",array(':parentid' => $parentid));
			}
			//提交
			if (checksubmit('submit')) {
				$data = array(
					'name'         => $_GPC['catename'],
					'parentid'     => 0,
					'displayorder' => $_GPC['displayorder'],
					'description'  => $_GPC['description'],
					'enabled'      => 1,
					'weid'         => $_W['weid'],
					);
				if (empty($parentid)) {
					if (empty($id)) {
						//添加主类
						pdo_insert("xcommunity_servicecategory",$data);
					}else{
						//更新
						$data['displayorder'] = $_GPC['displayorder'];
						$data['name']         = $_GPC['catename'];
						$data['description']  = $_GPC['description'];
						pdo_update("xcommunity_servicecategory",$data,array('id'=>$id));
					}					
				}else{
					//添加子类
					if(empty($id)){
							$data['parentid'] = $parentid;
							pdo_insert("xcommunity_servicecategory",$data);
					}else{
						//更新子类
						$data['parentid'] = $parentid;
						$data['displayorder'] = $_GPC['displayorder'];
						$data['name']         = $_GPC['catename'];
						$data['description']  = $_GPC['description'];
						pdo_update("xcommunity_servicecategory",$data,array('id'=>$id));
					}
				
				}
				message('更新成功',referer(),'success');
			}
		}elseif($op == 'display'){
			//显示全部分类信息
			$sql      = "select * from".tablename("xcommunity_servicecategory")."where parentid= 0 ";
			$category = pdo_fetchall($sql);
			$children = array();
			foreach ($category as $key => $value) {
				$sql  = "select *from".tablename("xcommunity_servicecategory")."where weid='{$_W['weid']}' and  parentid=".$value['id'];
				$list = pdo_fetchall($sql);
				$children[$value['id']] = $list;
			}
		}elseif ($op == 'delete') {
			//删除分类信息
			pdo_delete("xcommunity_servicecategory",array('id'=>$id));
			message('删除成功',referer(),'success');
		}
		include $this->template('servicecategory');