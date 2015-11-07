<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */
$_W['page']['title'] = '更新缓存 - 系统管理';
load()->model('cache');
load()->model('setting');
if (checksubmit('submit')) {
	cache_build_template();
	cache_build_modules();
	cache_build_users_struct();
	cache_build_setting();
	message('缓存更新成功！', url('system/updatecache'));
} else {
	template('system/updatecache');
}

















