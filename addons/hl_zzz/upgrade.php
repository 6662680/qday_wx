<?php
if(!pdo_fieldexists('zzz_reply', 'bgurl')) {
	pdo_query("ALTER TABLE ".tablename('zzz_reply')." ADD `bgurl` int(10) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('zzz_reply', 'bigunit')) {
	pdo_query("ALTER TABLE ".tablename('zzz_reply')." ADD `bigunit` varchar(50) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('zzz_reply', 'smallunit')) {
	pdo_query("ALTER TABLE ".tablename('zzz_reply')." ADD `start_time` int(10) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('zzz_reply', 'start_time')) {
	pdo_query("ALTER TABLE ".tablename('zzz_reply')." ADD `start_time` int(10) NOT NULL DEFAULT '0';");
}
 if(!pdo_fieldexists('zzz_reply', 'end_time')) {
	pdo_query("ALTER TABLE ".tablename('zzz_reply')." ADD `end_time` int(10) NOT NULL DEFAULT '1600000000';");
}

