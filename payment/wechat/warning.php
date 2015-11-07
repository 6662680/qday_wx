<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
require '../../source/bootstrap.inc.php';
$input = file_get_contents('php://input');

$obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
if($obj instanceof SimpleXMLElement && !empty($obj->FeedBackId)) {
	$data = array(
		'appid' => trim($obj->AppId),
		'timestamp' => trim($obj->TimeStamp),
		'errortype' => trim($obj->ErrorType),
		'description' => trim($obj->Description),
		'alarmcontent' => trim($obj->AlarmContent),
		'appsignature' => trim($obj->AppSignature),
		'signmethod' => trim($obj->SignMethod),
	);
	require '../../framework/bootstrap.inc.php';
	WeUtility::logging('pay-warning', $input);
}
exit('success');
