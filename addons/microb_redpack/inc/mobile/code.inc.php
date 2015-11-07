<?php
global $_W, $_GPC;
session_start();

$api = array();
$api['uid'] = '';
$api['password'] = '';
$phone = trim($_GPC['phone']);
if(!preg_match('/^1\d{10}$/', $phone)) {
    exit('error phone number');
}
$code = random(6, true);
$_SESSION['code'] = $code;
$message = '不错';

$url = "手机短信接口请联系大树桩";

load()->func('communication');
$resp = ihttp_get($url);
if(!is_error($resp)) {
    $ret = $resp['content'];
    exit($ret);
}
exit('failed');
