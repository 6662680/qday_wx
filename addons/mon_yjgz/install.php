<?php
/**
 *
 */

/**
 * 一键关注定义
 */




$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('mon_yjgz') . " (
 	id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	weid INT(11) UNSIGNED DEFAULT NULL,
	title VARCHAR(100) NOT NULL,
	banner_pic VARCHAR(300) NOT NULL,
	banner_desc VARCHAR(1000) NOT NULL,
	 `createtime` int(10) unsigned NOT NULL COMMENT '日期',
	PRIMARY KEY(id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_query($sql);


/**
 * 关注列表
 */
$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('mon_yjgz_item') . " (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `yid` int(10) unsigned NOT NULL DEFAULT '0',
   `title` VARCHAR(100) NOT NULL,

   icon VARCHAR(100) NOT NULL,

	  i_desc VARCHAR(500) NOT NULL,
	  i_url VARCHAR(300) NOT NULL,
	  `sort` int(3) default 0,
	  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
pdo_query($sql);





