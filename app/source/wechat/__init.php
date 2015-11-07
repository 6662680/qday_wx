<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan isNOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
checkauth();
load()->model('coupon');
if(empty($_W['acid'])) {
	message('acid不存在', referer(), 'error');
}



