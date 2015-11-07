<?php
/**
 * 微信墙模块
 *
 * [WeEngine System] Copyright (c) 2013 qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

/**
 * 微信墙内容
 */
global $_GPC, $_W;
$id = intval($_GPC['id']);
$wall = $this->getWall($id);
$wall['onlinemember'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('wxwall_members')." WHERE rid = :rid ", array(':rid'=>$wall['rid']));
$list = pdo_fetchall("SELECT id, content, from_user, type, createtime FROM ".tablename('wxwall_message')." WHERE rid = :rid AND isshow = '2' AND from_user <> '' ORDER BY createtime DESC", array(':rid'=>$wall['rid']));
$this->formatMsg($list);
include $this->template('detail');
