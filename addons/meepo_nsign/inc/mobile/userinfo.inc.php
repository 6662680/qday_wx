<?php
global $_GPC, $_W;
checkauth();		
$rid = intval($_GPC['rid']);
		
$uid = $_W['member']['uid'];
$$fields = array('realname','mobile');		
$profile = mc_fetch($uid, $fields);
		
include $this->template('userinfo');