<?php
/**
 */
$sql =<<<EOF
     CREATE TABLE IF NOT EXISTS `ims_fineness_article_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID,0为第一级',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `thumb` varchar(1024) NOT NULL DEFAULT '' COMMENT '分类图片',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '分类描述',
  `template` VARCHAR(300) NOT NULL DEFAULT '' COMMENT '分类模板目录',
  `templatefile` VARCHAR(300) NOT NULL DEFAULT '' COMMENT '分类模板名称',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_wx_tuijian` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '公众号名称',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '公众号名称',
  `guanzhuUrl` varchar(255) NOT NULL DEFAULT '' COMMENT '引导关注',
  `type` varchar(1)  NOT NULL DEFAULT '1',
  `clickNum` int(10) unsigned NOT NULL  DEFAULT '0',
  `ipclient` varchar(50) NOT NULL DEFAULT '' COMMENT 'ip',
  `thumb` varchar(500) NOT NULL DEFAULT '' COMMENT '缩略图',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE if not exists `ims_fineness_adv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) default 0,
  `link` varchar(255) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),KEY `indx_weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8  COMMENT='幻灯片';

EOF;
pdo_run($sql);

if(!pdo_fieldexists('fineness_article', 'credit')) {
   pdo_query("ALTER TABLE ".tablename('fineness_article')." ADD `credit` varchar(255) DEFAULT '0' ;");
}
if(!pdo_fieldexists('fineness_article', 'pcate')) {
  pdo_query("ALTER TABLE ".tablename('fineness_article')." ADD `pcate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '一级分类' ;");
}
if(!pdo_fieldexists('fineness_article', 'ccate')) {
 pdo_query("ALTER TABLE ".tablename('fineness_article')." ADD  `ccate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '二级分类' ;");
}
if(!pdo_fieldexists('fineness_article', 'template')) {
 pdo_query("ALTER TABLE ".tablename('fineness_article')." ADD `template` VARCHAR(300) NOT NULL DEFAULT '' COMMENT '内容模板目录' ;");
}
if(!pdo_fieldexists('fineness_article', 'templatefile')) {
    pdo_query("ALTER TABLE ".tablename('fineness_article')." ADD `templatefile` VARCHAR(300) NOT NULL DEFAULT '' COMMENT '内容模板目录' ;");
}
if(!pdo_fieldexists('fineness_article_category', 'templatefile')) {
    pdo_query("ALTER TABLE ".tablename('fineness_article_category')." ADD `templatefile` VARCHAR(300) NOT NULL DEFAULT '' COMMENT '内容模板目录' ;");
}
if(!pdo_fieldexists('fineness_article', 'description')) {
  pdo_query("ALTER TABLE ".tablename('fineness_article')." ADD `description` varchar(500) NOT NULL DEFAULT '' COMMENT '简介';");
}
if(!pdo_fieldexists('fineness_article', 'author')) {
    pdo_query("ALTER TABLE ".tablename('fineness_article')." ADD `author` varchar(100)   DEFAULT '' COMMENT '简介';");
}

if(!pdo_fieldexists('fineness_sysset', 'logo')) {
 pdo_query("ALTER TABLE ".tablename('fineness_sysset')." ADD `logo` varchar(255)   DEFAULT '' COMMENT 'logo';");
}

if(!pdo_fieldexists('fineness_sysset', 'tjgzh')) {
    pdo_query("ALTER TABLE ".tablename('fineness_sysset')." ADD `tjgzh` varchar(255) DEFAULT '1' comment '推荐公众号图片';");
}
if(!pdo_fieldexists('fineness_sysset', 'tjgzhUrl')) {
    pdo_query("ALTER TABLE ".tablename('fineness_sysset')." ADD `tjgzhUrl` varchar(255) DEFAULT '1' comment '推荐公众号引导关注';");
}

pdo_query("ALTER TABLE ".tablename('fineness_sysset')." MODIFY column `cnzz` varchar(800) ;");

if(!pdo_fieldexists('fineness_article', 'displayorder')) {
 pdo_query("ALTER TABLE ".tablename('fineness_article')." ADD  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序' ");
}
if(!pdo_fieldexists('fineness_article', 'outLink')) {
    pdo_query("ALTER TABLE ".tablename('fineness_article')." ADD `outLink` varchar(500) DEFAULT '' COMMENT '外链' ");
}
if(!pdo_fieldexists('wx_tuijian', 'hot')) {
    pdo_query("ALTER TABLE ".tablename('wx_tuijian')." ADD  `hot` int(1) NOT NULL COMMENT '是否热门 0默认 1热门' ");
}