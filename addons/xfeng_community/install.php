<?php
$sql = "
CREATE TABLE IF NOT EXISTS `ims_xcommunity_admap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL,
  `regionid` int(11) NOT NULL,
  `adid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ims_xcommunity_navExtension` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL,
  `title` varchar(30) NOT NULL,
  `navurl` varchar(100) NOT NULL,
  `icon` varchar(20) NOT NULL,
  `content` text NOT NULL COMMENT '说明',
  `cate` int(1) NOT NULL,
  `bgcolor` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ims_xcommunity_slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL,
  `displayorder` int(10) NOT NULL,
  `title` varchar(30) NOT NULL,
  `thumb` varchar(200) NOT NULL,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `ims_xcommunity_property` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `topPicture` varchar(255) NOT NULL COMMENT '照片',
  `mcommunity` varchar(255) NOT NULL COMMENT '微社区URL',
  `content` varchar(2000) NOT NULL COMMENT '内容',
  `createtime` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='物业介绍' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `ims_xcommunity_announcement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL COMMENT '公众号ID',
  `regionid` int(10) unsigned NOT NULL COMMENT '小区编号',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `author` varchar(50) NOT NULL COMMENT '作者',
  `createtime` int(10) unsigned NOT NULL,
  `starttime` int(11) unsigned NOT NULL COMMENT '开始时间',
  `endtime` int(11) unsigned NOT NULL COMMENT '结束时间',
  `status` tinyint(1) NOT NULL COMMENT '状态' COMMENT '1禁用，2启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='发布公告';

CREATE TABLE IF NOT EXISTS `ims_xcommunity_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(11) unsigned NOT NULL,
  `regionid` int(10) unsigned NOT NULL COMMENT '小区编号',
  `openid` varchar(50) NOT NULL,
  `realname` varchar(50) NOT NULL COMMENT '真实姓名',
  `mobile` varchar(15) NOT NULL COMMENT '手机号',
  `regionname` varchar(50) NOT NULL COMMENT '小区名称',
  `address` varchar(100) NOT NULL COMMENT '楼栋门牌',
  `remark` varchar(1000) NOT NULL COMMENT '备注',
  `status` tinyint(1) unsigned NOT NULL ,
  `createtime` int(10) unsigned NOT NULL,
  `manage_status` tinyint(1) unsigned NOT NULL COMMENT '授权管理员',
  PRIMARY KEY (`id`),
  KEY `idx_openid` (`openid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='注册用户';

CREATE TABLE IF NOT EXISTS `ims_xcommunity_phone` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `regionid` int(10) unsigned NOT NULL COMMENT '小区编号',
  `weid` int(11) unsigned NOT NULL COMMENT '公众号',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `phone` varchar(50) NOT NULL COMMENT '号码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='常用电话';

CREATE TABLE IF NOT EXISTS `ims_xcommunity_region` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL COMMENT '公众号ID',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `linkmen` varchar(50) NOT NULL COMMENT '联系人',
  `linkway` varchar(50) NOT NULL COMMENT '联系电话',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='添加小区信息';

CREATE TABLE IF NOT EXISTS `ims_xcommunity_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `openid` varchar(50) NOT NULL,
  `reportid` int(10) unsigned NOT NULL COMMENT '报告ID',
  `isreply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是回复',
  `content` varchar(5000) NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_xcommunity_report` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL COMMENT '用户身份',
  `weid` int(11) unsigned NOT NULL COMMENT '公众号ID',
  `regionid` int(10) unsigned NOT NULL COMMENT '小区编号',
  `type` tinyint(1) NOT NULL COMMENT '1为报修，2为投诉',
  `category` varchar(50) NOT NULL DEFAULT '' COMMENT '类目',
  `content` varchar(255) NOT NULL COMMENT '投诉内容',
  `requirement` varchar(1000) NOT NULL,
  `createtime` int(11) unsigned NOT NULL COMMENT '投诉日期',
  `status` tinyint(1) unsigned NOT NULL COMMENT '状态,1已解决,0未解决,2为用户取消',
  `newmsg` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否有新信息',
  `rank` tinyint(3) unsigned NOT NULL COMMENT '评级 1满意，2一般，3不满意',
  `comment` varchar(1000) NOT NULL,
  `resolve` varchar(1000) NOT NULL COMMENT '处理结果',
  `resolver` varchar(50) NOT NULL COMMENT '处理人',
  `resolvetime` int(10)  NOT NULL COMMENT '处理时间',
  `images` text,
  `print_sta` int(3) NOT NULL COMMENT '打印状态',
  PRIMARY KEY (`id`),
  KEY `idx_weid_regionid` (`weid`,`regionid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_xcommunity_service` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `openid` varchar(50) NOT NULL,
  `regionid` int(10) unsigned NOT NULL,
  `servicecategory` int(10) unsigned NOT NULL COMMENT '生活服务大分类 1家政服务，2租赁服务',
  `servicesmallcategory` varchar(50) NOT NULL COMMENT '生活服务小分类',
  `requirement` varchar(255) NOT NULL COMMENT '精准要求,如保洁需要填写 平米大小',
  `remark` varchar(500) NOT NULL COMMENT '备注',
  `contacttype` int(10) unsigned NOT NULL COMMENT '联系类型:1.随时联系;2.白天联系;3:晚上联系;4:自定义',
  `contactdesc` varchar(255) NOT NULL COMMENT '联系描述',
  `status` int(10) unsigned NOT NULL COMMENT '状态',
  `createtime` int(10) unsigned NOT NULL,
  `images` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ims_xcommunity_verifycode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `openid` varchar(50) NOT NULL,
  `verifycode` varchar(6) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `total` tinyint(3) unsigned NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_xcommunity_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `regionid` varchar(500) NOT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `enddate` varchar(30) NOT NULL,
  `picurl` varchar(100) NOT NULL,
  `number` int(11) NOT NULL DEFAULT '1',
  `content` varchar(2000) NOT NULL,
  `status` int(1) NOT NULL,
  `createtime` int(11) unsigned NOT NULL,
  `resnumber` int(11) unsigned NOT NULL COMMENT '报名人数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_xcommunity_res` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `openid` varchar(100) NOT NULL,
  `truename` varchar(30)  NOT NULL,
  `mobile` varchar(12)  NOT NULL,
  `num` int(2) unsigned NOT NULL,
  `rid` int(11) unsigned NOT NULL,
  `sex` varchar(6) NOT NULL,
  `createtime` int(11) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_xcommunity_search` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `sname` varchar(30) NOT NULL,
  `surl` varchar(100) NOT NULL,
  `status` int(1) NOT NULL,
  `icon` varchar(100) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_xcommunity_fled` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `openid` varchar(50) NOT NULL,
  `title` varchar(20) NOT NULL,
  `rolex` varchar(30) NOT NULL,
  `category` varchar(30)  NOT NULL,
  `yprice` int(10) NOT NULL,
  `zprice` int(10) NOT NULL,
  `realname` varchar(18) NOT NULL,
  `mobile` varchar(12) NOT NULL,
  `description` varchar(100) NOT NULL,
  `regionid` int(10) NOT NULL,
  `createtime` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `images` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_xcommunity_carpool` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `seat` int(2) unsigned NOT NULL ,
  `sprice` int(10) unsigned NOT NULL ,
  `month` int(2) unsigned NOT NULL ,
  `yday` int(2) unsigned NOT NULL ,
  `contact` varchar(50) NOT NULL,
  `mobile` varchar(13) NOT NULL,
  `openid` varchar(50) NOT NULL,
  `start_position` varchar(100) NOT NULL,
  `end_position` varchar(100) NOT NULL,
  `startMinute` int(10) unsigned NOT NULL,
  `startSeconds` int(10) unsigned NOT NULL,
  `license_number` varchar(100) NOT NULL,
  `car_model` varchar(100) NOT NULL,
  `car_brand` varchar(100) NOT NULL,
  `content` varchar(300) NOT NULL,
  `enable` int(1) NOT NULL COMMENT '1开启,0关闭',
  `createtime` int(10) unsigned NOT NULL,
  `regionid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ims_xcommunity_business` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `mobile` varchar(12) NOT NULL,
  `username` varchar(30)  NOT NULL,
  `password` varchar(100)  NOT NULL,
  `photo` varchar(100)  NOT NULL,
  `qq` int(11)  NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `status` int(1) unsigned NOT NULL COMMENT '0未审核，1审核',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_xcommunity_sjdp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `uid`   int(11) NOT NULL,
  `regionid`   int(11) NOT NULL,
  `sjname` varchar(30)  NOT NULL,
  `picurl` varchar(100)  NOT NULL,
  `contactname` varchar(30)  NOT NULL,
  `mobile` varchar(12)  NOT NULL,
  `phone` varchar(15)  NOT NULL,
  `qq`   int(11) NOT NULL,
  `province` varchar(50)  NOT NULL,
  `city` varchar(50)  NOT NULL,
  `dist` varchar(50)  NOT NULL,
  `address` varchar(150)  NOT NULL,
  `shopdesc` varchar(500)  NOT NULL,
  `businnesstime` varchar(20)  NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_xcommunity_propertyfree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `mobile`   varchar(13) NOT NULL,
  `username`   varchar(30) NOT NULL,
  `homenumber` int(4)  NOT NULL,
  `profree` int(4)  NOT NULL,
  `tcf` int(4)  NOT NULL,
  `gtsf` int(4)  NOT NULL,
  `gtdf` int(4)  NOT NULL,
  `protimeid`   int(10) NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_xcommunity_protime` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL,
  `protime`   varchar(30) NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_xcommunity_servicecategory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned  NULL ,
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `description` varchar(50) NOT NULL COMMENT '分类描述',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID,0为第一级',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
INSERT INTO `ims_xcommunity_servicecategory` (`id`, `name`, `description`, `parentid`, `displayorder`, `enabled`) VALUES
(1, '家政服务', '', 0, 0, 1),
(2, '租赁服务', '', 0, 0, 1),
(3, '报修类型', '', 0, 0, 1),
(4, '投诉类型', '', 0, 0, 1),
(5, '二手交易类型', '', 0, 0, 1);
CREATE TABLE `ims_xfcommunity_images` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `src` varchar(255) DEFAULT NULL,
    `file` longtext,
    `type` int(11) NOT NULL COMMENT '报修1，租赁2',
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


";
pdo_query($sql);