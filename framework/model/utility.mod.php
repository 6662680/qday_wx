<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function code_verify($uniacid, $receiver, $code) {
	$data = pdo_fetch('SELECT * FROM ' . tablename('uni_verifycode') . ' WHERE uniacid = :uniacid AND receiver = :receiver AND verifycode = :verifycode AND createtime > :createtime', array(':uniacid' => $uniacid, ':receiver' => $receiver, ':verifycode' => $code, ':createtime' => time() - 1800));
	if(empty($data)) {
		return false;
	}
	return true;
}
