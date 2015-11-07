<?php
/**
 */
$sql = "
CREATE TABLE IF NOT EXISTS `ims_amouse_house_slide` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `weid` int(10) unsigned NOT NULL,
    `url` varchar(200) NOT NULL DEFAULT '',
    `slide` varchar(200) NOT NULL DEFAULT '',
    `listorder` int(10) unsigned NOT NULL DEFAULT '0',
    `isshow` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
    `createtime` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";

pdo_run($sql);

if(!pdo_fieldexists('amouse_house', 'thumb3')) {
   pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD `thumb3` varchar(1000) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('amouse_house', 'thumb4')) {
  pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD  `thumb4` varchar(1000) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('amouse_house', 'thumb1')) {
   pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD `thumb1` varchar(1000) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('amouse_house', 'thumb2')) {
  pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD  `thumb2` varchar(1000)  DEFAULT '';");
}
if(!pdo_fieldexists('amouse_house', 'lat')) {
   pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD `lat` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '经度';");
}
if(!pdo_fieldexists('amouse_house', 'lng')) {
  pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD  `lng` decimal(18,10) NOT NULL DEFAULT '0.0000000000' COMMENT '纬度';");
}
if(!pdo_fieldexists('amouse_house', 'location_p')) {
   pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD `location_p` varchar(100) NOT NULL DEFAULT '' COMMENT '省';");
}
if(!pdo_fieldexists('amouse_house', 'place')) {
  pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD   `place` varchar(200) NOT NULL DEFAULT '';");
}
if(!pdo_fieldexists('amouse_house', 'location_c')) {
   pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD   `location_c` varchar(100) NOT NULL DEFAULT '' COMMENT '市';");
}
if(!pdo_fieldexists('amouse_house', 'location_a')) {
  pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD    `location_a` varchar(100) NOT NULL DEFAULT '' COMMENT '区';");
}

if(!pdo_fieldexists('amouse_house', 'jjrmobile')) {
   pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD  `jjrmobile` varchar(13) DEFAULT '0';");
}
if(!pdo_fieldexists('amouse_house', 'broker')) {
  pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD    `broker`  varchar(200) DEFAULT '';");
}
if(!pdo_fieldexists('amouse_house', 'isshow')) {
   pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD   `isshow` int(10) DEFAULT '1' comment '是否只显示经纪人信息';");
}
if(!pdo_fieldexists('amouse_house', 'defcity')) {
  pdo_query("ALTER TABLE ".tablename('amouse_house')." ADD     `defcity`  varchar(1000) DEFAULT '中国';");
}
