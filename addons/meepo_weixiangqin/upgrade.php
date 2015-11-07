<?php
 if(!pdo_fieldexists('hnfans', 'yingcang')) {
    pdo_query("ALTER TABLE ".tablename('hnfans')." ADD `yingcang` int(2) NOT NULL DEFAULT '1' COMMENT '隐藏显示';");
}
pdo_query("CREATE TABLE IF NOT EXISTS `ims_hnresearch` (
  `reid` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(1000) NOT NULL,
  `content` text NOT NULL,
  `information` varchar(500) NOT NULL DEFAULT '',
  `thumb` varchar(200) NOT NULL DEFAULT '',
  `inhome` tinyint(4) NOT NULL DEFAULT '0',
  `createtime` int(10) NOT NULL DEFAULT '0',
  `starttime` int(10) NOT NULL DEFAULT '0',
  `endtime` int(10) unsigned NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `pretotal` int(10) unsigned NOT NULL DEFAULT '1',
  `noticeemail` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`reid`),
  KEY `weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

pdo_query("CREATE TABLE IF NOT EXISTS `ims_hnresearch_data` (
  `redid` bigint(20) NOT NULL AUTO_INCREMENT,
  `reid` int(11) NOT NULL,
  `rerid` int(11) NOT NULL,
  `refid` int(11) NOT NULL,
  `data` varchar(800) NOT NULL,
  PRIMARY KEY (`redid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

pdo_query("CREATE TABLE IF NOT EXISTS `ims_hnresearch_fields` (  
  `refid` int(11) NOT NULL AUTO_INCREMENT,
  `reid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `essential` tinyint(4) NOT NULL DEFAULT '0',
  `bind` varchar(30) NOT NULL DEFAULT '',
  `value` varchar(300) NOT NULL DEFAULT '',
  `description` varchar(500) NOT NULL DEFAULT '',
  `displayorder` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`refid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

pdo_query("CREATE TABLE IF NOT EXISTS `ims_hnresearch_rows` (
  `rerid` int(11) NOT NULL AUTO_INCREMENT,
  `reid` int(11) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `createtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rerid`),
  KEY `reid` (`reid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
 if(!pdo_fieldexists('meepo_hongniangset', 'header_adsurl')) {
    pdo_query("ALTER TABLE ".tablename('meepo_hongniangset')." ADD `header_adsurl` varchar(200) NOT NULL  COMMENT '首页图片链接';");
}

//0803
 if(!pdo_fieldexists('hnmessage', 'msgtype')) {
    pdo_query("ALTER TABLE ".tablename('hnmessage')." ADD   `msgtype` varchar(20) NOT NULL DEFAULT 'text' COMMENT 'leixing';");
}
 if(!pdo_fieldexists('hnmessage', 'thumburl')) {
    pdo_query("ALTER TABLE ".tablename('hnmessage')." ADD   `thumburl` varchar(100) NOT NULL DEFAULT '0' COMMENT 'thumb';");
}


//增加幻灯片
pdo_query("CREATE TABLE IF NOT EXISTS `ims_meepoweixiangqin_slide` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `weid` int(10) unsigned NOT NULL,
            `title` varchar(100) NOT NULL DEFAULT '',
            `url` varchar(200) NOT NULL DEFAULT '',
            `attachment` varchar(100) NOT NULL DEFAULT '',
            `displayorder` int(10) unsigned NOT NULL DEFAULT '0',
            `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
            PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
?>