<?php 
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */

defined('IN_IA') or exit('Access Denied');

define('REGULAR_EMAIL', '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/i');
define('REGULAR_MOBILE', '/1\d{10}/');
define('REGULAR_USERNAME', '/^[\x{4e00}-\x{9fa5}a-z\d_\.]{3,15}$/iu');


define('TEMPLATE_DISPLAY', 0);


define('TEMPLATE_FETCH', 1);


define('TEMPLATE_INCLUDEPATH', 2);
