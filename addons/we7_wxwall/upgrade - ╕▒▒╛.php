<?php
if(!pdo_fieldexists('wxwall_reply', 'logo')) {
	pdo_query("ALTER TABLE ".tablename('wxwall_reply')." ADD `logo` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `isshow`;");
	pdo_query("ALTER TABLE ".tablename('wxwall_reply')." ADD `background` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `logo`;");
	pdo_query("ALTER TABLE ".tablename('wxwall_reply')." ADD `acid` INT NOT NULL AFTER `id`;");
}

if(!pdo_fieldexists('wxwall_members', 'avatar')) {
	pdo_query("ALTER TABLE ".tablename('wxwall_members')." ADD  `avatar` varchar(255) NOT NULL COMMENT '粉丝头像';");
}