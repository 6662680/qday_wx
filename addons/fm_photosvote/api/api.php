<?php
/**
 * [Fmoons System] Copyright (c) 2014 qdaygroup.com
 * Fmoons is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
define('IN_API', true);
require_once '../framework/bootstrap.inc.php';
load()->model('reply');
load()->app('common');
load()->classs('wesession');
$api = $_GPC['api'];
if ($api == 'api') {
	$ourl = '../api/api.php?&weburl=';
	$apiurl = base64_encode($ourl);
	$fmdata = array(
		"config" => 1,
		"apiurl" => $apiurl,
	);
	echo json_encode($fmdata);
	exit();	
}
