<?php
$sql = "
CREATE TABLE IF NOT EXISTS `ims_thinkidea_rencai_apply_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL COMMENT '公众号',
  `person_id` int(11) NOT NULL COMMENT '求职者个人id',
  `company_id` int(11) NOT NULL COMMENT '公司id',
  `job_id` int(11) NOT NULL COMMENT '职位id',
  `dateline` int(11) NOT NULL COMMENT '申请时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='求职者申请职位表';

-- 数据导出被取消选择。


-- 导出  表 we7.ims_thinkidea_rencai_category 结构
CREATE TABLE IF NOT EXISTS `ims_thinkidea_rencai_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `isshow` smallint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `display` smallint(1) NOT NULL DEFAULT '0' COMMENT '排序',
  `ishot` smallint(6) NOT NULL DEFAULT '0' COMMENT '是否热门',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='职位分类表';

-- 数据导出被取消选择。


-- 导出  表 we7.ims_thinkidea_rencai_company 结构
CREATE TABLE IF NOT EXISTS `ims_thinkidea_rencai_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL DEFAULT '0',
  `from_user` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(100) DEFAULT NULL COMMENT '公司名称',
  `industry` smallint(1) DEFAULT NULL COMMENT '公司所属行业类别',
  `address` varchar(250) DEFAULT NULL COMMENT '公司地址',
  `contact` varchar(20) DEFAULT NULL COMMENT '联系人',
  `mobile` char(11) DEFAULT NULL COMMENT '手机',
  `scale` smallint(1) NOT NULL DEFAULT '0' COMMENT '规模',
  `type` tinyint(1) DEFAULT '0' COMMENT '企业类型',
  `description` text COMMENT '公司简介',
  `license` varchar(250) DEFAULT NULL COMMENT '公司营业执照',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否通过审核',
  `isauth` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否认证',
  `view_resume_nums` smallint(6) NOT NULL DEFAULT '0' COMMENT '已经查看简历数',
  `view_resume_total` smallint(6) NOT NULL DEFAULT '0' COMMENT '查看简历数上限',
  `dateline` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid_from_user` (`weid`,`from_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 we7.ims_thinkidea_rencai_industry 结构
CREATE TABLE IF NOT EXISTS `ims_thinkidea_rencai_industry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '行业名称',
  `parent_id` int(11) NOT NULL COMMENT '父id',
  `display` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `ishot` smallint(1) DEFAULT '0' COMMENT '是否热门',
  `isshow` smallint(1) DEFAULT '1' COMMENT '是否显示',
  `dateline` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='行业分类表';

-- 数据导出被取消选择。


-- 导出  表 we7.ims_thinkidea_rencai_job 结构
CREATE TABLE IF NOT EXISTS `ims_thinkidea_rencai_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL,
  `mid` int(11) NOT NULL COMMENT '企业id',
  `title` varchar(100) NOT NULL COMMENT '职位名称',
  `cid` int(11) NOT NULL COMMENT '职位类别id',
  `payroll` smallint(6) NOT NULL COMMENT '薪资',
  `educational` tinyint(4) NOT NULL COMMENT '学历',
  `workexperience` tinyint(4) NOT NULL COMMENT '工作经验',
  `welfare` varchar(50) NOT NULL COMMENT '福利保障',
  `positiontype` tinyint(4) NOT NULL COMMENT '职位类型',
  `nums` int(11) NOT NULL COMMENT '招聘人数',
  `workaddress` varchar(50) NOT NULL COMMENT '工作地点',
  `description` varchar(255) NOT NULL COMMENT '职位信息描述',
  `views` int(11) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `istop` smallint(1) NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `expiration` int(11) NOT NULL DEFAULT '0' COMMENT '置顶过期时间',
  `dateline` int(11) NOT NULL COMMENT '发布时间',
  PRIMARY KEY (`id`),
  KEY `weid_from_dateline` (`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='公司职位信息';

-- 数据导出被取消选择。


-- 导出  表 we7.ims_thinkidea_rencai_member 结构
CREATE TABLE IF NOT EXISTS `ims_thinkidea_rencai_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT NULL,
  `from_user` varchar(50) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT '是企业还是个人',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='注册用户表含企业、个人。作快速查询使用';

-- 数据导出被取消选择。


-- 导出  表 we7.ims_thinkidea_rencai_person 结构
CREATE TABLE IF NOT EXISTS `ims_thinkidea_rencai_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL,
  `from_user` varchar(100) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `headimgurl` varchar(250) DEFAULT NULL,
  `sex` smallint(1) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `age` smallint(6) DEFAULT NULL,
  `educational` tinyint(1) DEFAULT NULL COMMENT '我的学历',
  `professional` varchar(50) DEFAULT NULL COMMENT '我的专业',
  `workexperience` smallint(6) DEFAULT NULL COMMENT '工作经验',
  `assessment` varchar(255) DEFAULT NULL COMMENT '自我评价',
  `istop` smallint(1) NOT NULL DEFAULT '0' COMMENT '是否置顶该简历',
  `expiration` int(11) NOT NULL DEFAULT '0',
  `dateline` int(11) NOT NULL,
  `views` int(11) NOT NULL COMMENT '被浏览数',
  `updatetime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `weid_from_user` (`weid`,`from_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='求职个人表';

-- 数据导出被取消选择。


-- 导出  表 we7.ims_thinkidea_rencai_person_collect 结构
CREATE TABLE IF NOT EXISTS `ims_thinkidea_rencai_person_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL COMMENT '职位id',
  `dateline` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收藏职位表';

-- 数据导出被取消选择。


-- 导出  表 we7.ims_thinkidea_rencai_person_resume 结构
CREATE TABLE IF NOT EXISTS `ims_thinkidea_rencai_person_resume` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) DEFAULT NULL,
  `weid` int(11) DEFAULT NULL,
  `company_name` varchar(50) DEFAULT NULL COMMENT '公司名称',
  `start_time` char(11) DEFAULT NULL COMMENT '开始时间',
  `end_time` char(11) DEFAULT NULL COMMENT '结束时间',
  `wage` int(11) DEFAULT NULL COMMENT '税前工资',
  `work_description` text COMMENT '工作描述',
  `dateline` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='个人简历';

-- 数据导出被取消选择。


-- 导出  表 we7.ims_thinkidea_rencai_reply 结构
CREATE TABLE IF NOT EXISTS `ims_thinkidea_rencai_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acid` int(11) DEFAULT NULL,
  `rid` int(11) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `avatar` varchar(250) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `dateline` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 we7.ims_thinkidea_rencai_share 结构
CREATE TABLE IF NOT EXISTS `ims_thinkidea_rencai_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `default_title` varchar(250) NOT NULL DEFAULT '0',
  `default_desc` varchar(250) NOT NULL DEFAULT '0',
  `default_pic` varchar(250) NOT NULL DEFAULT '0',
  `index_title` varchar(250) NOT NULL COMMENT '首页title',
  `index_desc` varchar(250) NOT NULL COMMENT '首页描述',
  `index_pic` varchar(250) NOT NULL,
  `zhao_title` varchar(250) NOT NULL COMMENT '招聘列表页title',
  `zhao_desc` varchar(250) NOT NULL COMMENT '招聘列表页描述',
  `zhao_pic` varchar(250) NOT NULL,
  `qiu_title` varchar(250) NOT NULL COMMENT '求职列表页title',
  `qiu_desc` varchar(250) NOT NULL COMMENT '求职列表页描述',
  `qiu_pic` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='设置分享';
";
$sql = preg_replace('/ims_/', $_W['config']['db']['tablepre'], $sql);
pdo_query($sql);
?>