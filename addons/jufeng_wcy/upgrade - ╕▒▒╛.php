<?php

if(!pdo_fieldexists('jufeng_wcy_category', 'count1')) {
	pdo_query("ALTER TABLE ".tablename('jufeng_wcy_category')." ADD  `count1` varchar(20) NOT NULL;");
}
if(!pdo_fieldexists('jufeng_wcy_category', 'count2')) {
	pdo_query("ALTER TABLE ".tablename('jufeng_wcy_category')." ADD  `count2` varchar(20) NOT NULL;");
}
if(!pdo_fieldexists('jufeng_wcy_category', 'count3')) {
	pdo_query("ALTER TABLE ".tablename('jufeng_wcy_category')." ADD  `count3` varchar(20) NOT NULL;");
}
?>
