<?php
/**
 * [WeiZan System] Copyright (c) 2014 WeiZan.Com
 * WeiZan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('app');
$title = $_W['page']['title'];
$navs = app_navs('home', $multiid);
$share_tmp = pdo_fetch('SELECT title,description,thumb FROM ' . tablename('cover_reply') . ' WHERE uniacid = :aid AND multiid = :id AND module = :m', array(':aid' => $_W['uniacid'], ':id' => $multiid, ':m' => 'site'));
$_share['imgUrl'] = tomedia($share_tmp['thumb']);
$_share['desc'] = $share_tmp['description'];
$_share['title'] = $share_tmp['title'];

template('home/home');