<?php
 if(!pdo_fieldexists('amouse_weicard_sysset', 'isoauth')) {
	pdo_query("ALTER TABLE ".tablename('amouse_weicard_sysset')." ADD `isoauth` int(2) unsigned NOT NULL DEFAULT '1';");
}

 if(!pdo_fieldexists('amouse_weicard_card', 'templateFile')) {
	pdo_query("ALTER TABLE ".tablename('amouse_weicard_card')." ADD   `templateFile` varchar(300) DEFAULT 'qianx_index';");
}
 if(!pdo_fieldexists('amouse_weicard_industry', 'weid')) {
	pdo_query("ALTER TABLE ".tablename('amouse_weicard_industry')." ADD  `weid` int(10) NOT NULL;");
}

