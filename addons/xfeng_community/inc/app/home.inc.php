<?php
/**
 * 微小区模块
 *
 * [晓锋] Copyright (c) 2013 qfinfo.cn
 */
/**
 * 微信端首页
 */
defined('IN_IA') or exit('Access Denied');
	global $_GPC,$_W;		
	$title  = $_W['account']['name'];
	$member = $this->changemember();
	$nav1 = pdo_fetchAll("SELECT * FROM".tablename('xcommunity_navExtension')."WHERE weid='{$_W['weid']}' AND cate=1");
	$nav2 = pdo_fetchAll("SELECT * FROM".tablename('xcommunity_navExtension')."WHERE weid='{$_W['weid']}' AND cate=2");
	$nav3 = pdo_fetchAll("SELECT * FROM".tablename('xcommunity_navExtension')."WHERE weid='{$_W['weid']}' AND cate=3");
	include $this->template('home');