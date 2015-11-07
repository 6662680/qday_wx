<?php
/*
*/
if(!pdo_fieldexists('weilive_activity', 'credit')) {  
	pdo_query("ALTER TABLE ".tablename('weilive_activity')." ADD `credit` int(10) unsigned NOT NULL DEFAULT 0 comment '领取消费积分';");
}

?>