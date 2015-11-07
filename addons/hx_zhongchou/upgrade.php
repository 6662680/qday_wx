<?php
if(!pdo_fieldexists('hx_zhongchou_dispatch', 'enabled')) {
	pdo_query("ALTER TABLE ".tablename('hx_zhongchou_dispatch')." ADD `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启';");
}