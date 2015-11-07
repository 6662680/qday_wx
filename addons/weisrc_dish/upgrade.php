<?php
$sql = "
CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_area` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
    `name` varchar(50) NOT NULL COMMENT '区域名称',
    `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID,0为第一级',
    `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
    `dateline` int(10) unsigned NOT NULL DEFAULT '0',
    `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_print_setting` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `weid` int(10) unsigned NOT NULL,
    `storeid` int(10) unsigned NOT NULL,
    `print_status` tinyint(1) NOT NULL,
    `print_type` tinyint(1) NOT NULL,
    `print_usr` varchar(50) DEFAULT '',
    `print_nums` tinyint(3) DEFAULT '1',
    `print_top` varchar(40) DEFAULT '',
    `print_bottom` varchar(40) DEFAULT '',
    `dateline` int(10) DEFAULT '0',
    PRIMARY KEY (`id`)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_print_order` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `weid` int(10) unsigned NOT NULL,
    `orderid` int(10) unsigned NOT NULL,
    `print_usr` varchar(50) DEFAULT '',
    `print_status` tinyint(1) DEFAULT '-1',
    `dateline` int(10) DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_weisrc_dish_sms_checkcode` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `weid` int(10) unsigned NOT NULL,
        `from_user` varchar(100) DEFAULT '' COMMENT '用户ID',
        `tel` varchar(30) NOT NULL DEFAULT '' COMMENT '手机',
        `checkcode` varchar(100) DEFAULT '' COMMENT '验证码',
        `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0未使用1已使用',
        `dateline` int(10) DEFAULT '0' COMMENT '创建时间',
        PRIMARY KEY (`id`)
    )  ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
pdo_run($sql);

if(!pdo_fieldexists('weisrc_dish_stores', 'areaid')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD `areaid` int(10) NOT NULL DEFAULT '0' COMMENT '区域id';");
}

if(!pdo_fieldexists('weisrc_dish_order', 'print_sta')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `print_sta` tinyint(1) DEFAULT '-1' COMMENT '打印状态';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'tables')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `tables` varchar(10) NOT NULL DEFAULT '' COMMENT '桌号';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'dining_mode')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `dining_mode` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用餐类型 1:到店 2:外卖';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'address')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `address` varchar(250) NOT NULL DEFAULT '' COMMENT '地址';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'sign')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `sign` tinyint(1) NOT NULL DEFAULT '0' COMMENT '-1拒绝，0未处理，1已处理';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'reply')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD `reply` varchar(1000) NOT NULL DEFAULT '' COMMENT '回复';");
}
//ims_weisrc_dish_setting
if(!pdo_fieldexists('weisrc_dish_setting', 'storeid')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD `storeid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '默认门店';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'dining_mode')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD `dining_mode` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用餐类型 1:到店 2:外卖';");
}
if(!pdo_fieldexists('weisrc_dish_print_setting', 'title')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD `title` varchar(200) DEFAULT '';");
}
//ims_weisrc_dish_email_setting
if(!pdo_fieldexists('weisrc_dish_email_setting', 'email_host')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_email_setting')." ADD `email_host` varchar(50) DEFAULT '' COMMENT '邮箱服务器';");
}
if(!pdo_fieldexists('weisrc_dish_email_setting', 'email_send')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_email_setting')." ADD `email_send` varchar(20) DEFAULT '' COMMENT '商户发送邮件邮箱';");
}
if(!pdo_fieldexists('weisrc_dish_email_setting', 'email_pwd')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_email_setting')." ADD `email_pwd` varchar(20) DEFAULT '' COMMENT '邮箱密码';");
}
if(!pdo_fieldexists('weisrc_dish_email_setting', 'email_user')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_email_setting')." ADD `email_user` varchar(100) DEFAULT '' COMMENT '发信人名称';");
}

if(!pdo_fieldexists('weisrc_dish_stores', 'is_meal')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD `is_meal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否店内点餐';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'is_delivery')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD `is_delivery` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否外卖订餐';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'sendingprice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD `sendingprice` varchar(10) NOT NULL DEFAULT '' COMMENT '起送价格';");
}

if(!pdo_fieldexists('weisrc_dish_mealtime', 'storeid')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_mealtime')." ADD `storeid` int(10) unsigned NOT NULL;");
}
if(!pdo_fieldexists('weisrc_dish_mealtime', 'status')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_mealtime')." ADD `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启';");
}
if(!pdo_fieldexists('weisrc_dish_mealtime', 'dateline')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_mealtime')." ADD  `dateline` int(10) DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'transid')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD  `transid` varchar(30) NOT NULL DEFAULT '0' COMMENT '微信支付单号';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'goodsprice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD   `goodsprice` decimal(10,2) DEFAULT '0.00';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'dispatchprice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD  `dispatchprice` decimal(10,2) DEFAULT '0.00';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'isemail')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD  `isemail` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'issms')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD    `issms` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_order', 'istpl')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_order')." ADD    `istpl` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_print_setting', 'qrcode_status')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD  `qrcode_status` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('weisrc_dish_print_setting', 'rcode_url')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_print_setting')." ADD  `qrcode_url` varchar(200) DEFAULT '';");
}

if(!pdo_fieldexists('weisrc_dish_setting', 'istplnotice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD     `istplnotice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否模版通知';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'tplneworder')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD    `tplneworder` varchar(200) DEFAULT '' COMMENT '模板id';");
}
if(!pdo_fieldexists('weisrc_dish_setting', 'tpluser')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_setting')." ADD   `tpluser` text COMMENT '通知用户';");
}
if(!pdo_fieldexists('weisrc_dish_stores', 'dispatchprice')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_dish_stores')." ADD    `dispatchprice` decimal(10,2) DEFAULT '0.00';");
}