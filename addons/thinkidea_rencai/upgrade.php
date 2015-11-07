<?php

if (!pdo_fieldexists('thinkidea_rencai_person', 'workaddress')) {
    pdo_query("ALTER TABLE " . tablename('thinkidea_rencai_person') . " ADD  `workaddress` varchar(200) NOT NULL;");
}
if (!pdo_fieldexists('thinkidea_rencai_job', 'workaddress')) {
    pdo_query("ALTER TABLE " . tablename('thinkidea_rencai_person') . " ADD  `workaddress` varchar(200) NOT NULL;");
}
if (!pdo_fieldexists('thinkidea_rencai_job', 'ishot')) {
    pdo_query("ALTER TABLE " . tablename('thinkidea_rencai_job') . " ADD  `ishot` SMALLINT(1) NOT NULL DEFAULT '0';");
}
if (!pdo_fieldexists('thinkidea_rencai_adslider', 'exprtime')) {
    pdo_query("ALTER TABLE " . tablename('thinkidea_rencai_adslider') . " ADD   `exprtime` varchar(50) NOT NULL DEFAULT '0' COMMENT '过期时间';");
}
if (!pdo_fieldexists('thinkidea_rencai_company', 'coordinate')) {
    pdo_query("ALTER TABLE " . tablename('thinkidea_rencai_company') . " ADD    `coordinate` varchar(255) NOT NULL DEFAULT '';");
}
if (!pdo_fieldexists('thinkidea_rencai_company', 'logo')) {
    pdo_query("ALTER TABLE " . tablename('thinkidea_rencai_company') . " ADD   `logo` varchar(255) NOT NULL DEFAULT '';");
}
if (!pdo_fieldexists('thinkidea_rencai_company', 'avatar')) {
    pdo_query("ALTER TABLE " . tablename('thinkidea_rencai_company') . " ADD    `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '企业封面';");
}
if (!pdo_fieldexists('thinkidea_rencai_company', 'position')) {
    pdo_query("ALTER TABLE " . tablename('thinkidea_rencai_company') . " ADD    `position` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐位';");
}
if (!pdo_fieldexists('thinkidea_rencai_member', 'status')) {
    pdo_query("ALTER TABLE " . tablename('thinkidea_rencai_member') . " ADD    `status` smallint(1) NOT NULL DEFAULT '0' COMMENT '状态。是否可用';");
}
if (!pdo_fieldexists('thinkidea_rencai_share', 'uniacid')) {
    pdo_query("ALTER TABLE " . tablename('thinkidea_rencai_share') . " ADD  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT '统一公众号';");
}
if (!pdo_fieldexists('thinkidea_rencai_share', 'mobile_title')) {
    pdo_query("ALTER TABLE " . tablename('thinkidea_rencai_share') . " ADD  `mobile_title` varchar(255) DEFAULT NULL COMMENT '手机端title';");
}
$sql = 
"CREATE TABLE if not exists `ims_thinkidea_rencai_jobs_comments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `weid` SMALLINT(6) NULL DEFAULT NULL,
  `mid` INT(11) NULL DEFAULT NULL COMMENT '用户id',
  `jobid` INT(11) NULL DEFAULT NULL COMMENT '职位id',
  `content` VARCHAR(250) NULL DEFAULT NULL,
  `status` TINYINT(1) NULL DEFAULT '0',
  `dateline` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) COMMENT='评论表' COLLATE='utf8_general_ci' ENGINE=MyISAM;";
pdo_query($sql);

   