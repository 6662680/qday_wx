<?php
//
if(!pdo_fieldexists('eso_share_reply', 'u')) {
	pdo_query("ALTER TABLE ".tablename('eso_share_reply')." ADD COLUMN `u`  varchar(255) NULL;");
}
if(!pdo_fieldexists('eso_share_reply', 'share_title')) {
	pdo_query("ALTER TABLE ".tablename('eso_share_reply')." ADD COLUMN `share_title`  text NULL;");
}
if(!pdo_fieldexists('eso_share_reply', 'share_url')) {
	pdo_query("ALTER TABLE ".tablename('eso_share_reply')." ADD COLUMN `share_url`  text NULL;");
}
if(!pdo_fieldexists('eso_share_reply', 'share_txt')) {
	pdo_query("ALTER TABLE ".tablename('eso_share_reply')." ADD COLUMN `share_txt`  text NULL;");
}
if(!pdo_fieldexists('eso_share_reply', 'share_desc')) {
	pdo_query("ALTER TABLE ".tablename('eso_share_reply')." ADD COLUMN `share_desc`  text NULL;");
}
