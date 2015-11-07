<?php


$sql = "
";

pdo_run($sql);

if(pdo_fieldexists('hotel2_order', 'oprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_order')." CHANGE `oprice` `oprice` decimal(10,2) DEFAULT '0.00' COMMENT '原价';");
}
if(pdo_fieldexists('hotel2_order', 'cprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_order')." CHANGE `cprice`  `cprice` decimal(10,2) DEFAULT '0.00' COMMENT '现价';");
}
if(pdo_fieldexists('hotel2_order', 'mprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_order')." CHANGE `mprice`   `mprice` decimal(10,2) DEFAULT '0.00' COMMENT '会员价';");
}
if(pdo_fieldexists('hotel2_order', 'sum_price')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_order')." CHANGE `sum_price`   `sum_price` decimal(10,2) DEFAULT '0.00' COMMENT '总价';");
}
if(pdo_fieldexists('hotel2_room', 'oprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room')." CHANGE `oprice` `oprice` decimal(10,2) DEFAULT '0.00' COMMENT '原价';");
}
if(pdo_fieldexists('hotel2_room', 'cprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room')." CHANGE `cprice`  `cprice` decimal(10,2) DEFAULT '0.00' COMMENT '现价';");
}
if(pdo_fieldexists('hotel2_room', 'mprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room')." CHANGE `mprice`   `mprice` decimal(10,2) DEFAULT '0.00' COMMENT '会员价';");
}

if(pdo_fieldexists('hotel2_room_price', 'oprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room_price')." CHANGE `oprice` `oprice` decimal(10,2) DEFAULT '0.00' COMMENT '原价';");
}
if(pdo_fieldexists('hotel2_room_price', 'cprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room_price')." CHANGE `cprice`  `cprice` decimal(10,2) DEFAULT '0.00' COMMENT '现价';");
}
if(pdo_fieldexists('hotel2_room_price', 'mprice')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_room_price')." CHANGE `mprice`   `mprice` decimal(10,2) DEFAULT '0.00' COMMENT '会员价';");
}

if(!pdo_fieldexists('hotel2_set', 'email')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_set')." ADD `email` varchar(255) NOT NULL DEFAULT '' COMMENT '提醒接受邮箱';");
}
if(!pdo_fieldexists('hotel2_set', 'mobile')) {
	pdo_query("ALTER TABLE  ".tablename('hotel2_set')." ADD `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '提醒接受手机';");
}
