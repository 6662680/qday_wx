<?php
$sql = "
	CREATE TABLE IF NOT EXISTS `ims_fm_photosvote_iplist` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `rid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '规则id',
	  `weid` int(10) unsigned NOT NULL COMMENT '公众号ID',
	  `uniacid` int(10) unsigned NOT NULL COMMENT '公众号ID',
	  `iparr` varchar(2000) NOT NULL DEFAULT '' COMMENT 'IP区域',
	  `ipadd` varchar(200) NOT NULL DEFAULT '' COMMENT 'IP区域',
	  `createtime` int unsigned NOT NULL COMMENT '时间',
	  PRIMARY KEY (`id`),KEY `indx_uniacid` (`uniacid`),KEY `indx_createtime` (`createtime`)
	) ENGINE = MYISAM DEFAULT CHARSET = utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_fm_photosvote_iplistlog` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `rid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '规则id',
	  `uniacid` int(10) unsigned NOT NULL COMMENT '公众号ID',
	  `avatar` varchar(200) NOT NULL DEFAULT '' COMMENT '微信头像', 
	  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '微信昵称',
	  `from_user` varchar(255) NOT NULL DEFAULT '' COMMENT 'openid',
	  `ip` varchar(255) NOT NULL DEFAULT '' COMMENT 'IP',
	  `hitym` varchar(255) NOT NULL DEFAULT '' COMMENT '点击页面',
	  `createtime` int(11) NOT NULL COMMENT '初始时间',
	  PRIMARY KEY (`id`),KEY `indx_uniacid` (`uniacid`),KEY `indx_createtime` (`createtime`)
	) ENGINE=MYISAM DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_fm_photosvote_announce` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `rid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '规则id',
	  `weid` int(10) unsigned NOT NULL COMMENT '公众号ID',
	  `uniacid` int(10) unsigned NOT NULL COMMENT '公众号ID',
	  `content` varchar(2000) NOT NULL DEFAULT '' COMMENT '公告',
	  `nickname` varchar(200) NOT NULL DEFAULT '' COMMENT '公告',
	  `createtime` int unsigned NOT NULL COMMENT '时间',
	  PRIMARY KEY (`id`),KEY `indx_uniacid` (`uniacid`),KEY `indx_createtime` (`createtime`)
	) ENGINE = MYISAM DEFAULT CHARSET = utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_fm_photosvote_provevote_voice` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `rid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '规则id',
	  `uniacid` int(10) unsigned NOT NULL COMMENT '公众号ID',
	  `from_user` varchar(200) NOT NULL DEFAULT '' COMMENT 'openid',
	  `mediaid` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐id',
	  `timelength` varchar(200) NOT NULL DEFAULT '' COMMENT '时间轴',
	  `voice` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `fmmid` varchar(200) NOT NULL DEFAULT '' COMMENT '识别',
	  `ip` varchar(255) NOT NULL DEFAULT '' COMMENT 'IP',
	  `createtime` int(11) NOT NULL COMMENT '初始时间',
	  PRIMARY KEY (`id`),KEY `indx_uniacid` (`uniacid`),KEY `indx_createtime` (`createtime`)
	) ENGINE=MYISAM DEFAULT CHARSET=utf8;
	
	CREATE TABLE IF NOT EXISTS `ims_fm_photosvote_provevote_name` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `rid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '规则id',
	  `uniacid` int(10) unsigned NOT NULL,
	  `from_user` varchar(255) NOT NULL DEFAULT '' COMMENT '用户openid',
	  `musicname` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `photoname` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `picarr_1_name` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `picarr_2_name` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `picarr_3_name` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `picarr_4_name` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `picarr_5_name` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `picarr_6_name` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `picarr_7_name` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `picarr_8_name` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `musicnamefop` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `voicename` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `voicenamefop` varchar(200) NOT NULL DEFAULT '' COMMENT '音乐',
	  `vedioname` varchar(200) NOT NULL DEFAULT '' COMMENT '视频',
	  `vedionamefop` varchar(200) NOT NULL DEFAULT '' COMMENT '视频',
	  PRIMARY KEY (`id`),KEY `indx_uniacid` (`uniacid`),KEY `indx_rid` (`rid`)
	) ENGINE = MYISAM DEFAULT CHARSET = utf8;
";

pdo_run($sql);



 if(!pdo_fieldexists('fm_photosvote_reply', 'autolitpic')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `autolitpic` int(10) unsigned NOT NULL DEFAULT '50' COMMENT '裁剪大小';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'autozl')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `autozl` int(10) unsigned NOT NULL DEFAULT '50' COMMENT '裁剪质量';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'zbgcolor')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD  `zbgcolor` varchar(50) NOT NULL COMMENT '背景色';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'zbg')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `zbg` varchar(255) NOT NULL COMMENT '背景图';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'zbgtj')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `zbgtj` varchar(255) NOT NULL COMMENT '背景图';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'lapiao')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `lapiao` varchar(5) NOT NULL COMMENT '拉票';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'sharename')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `sharename` varchar(2) NOT NULL COMMENT '分享';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'ishuodong')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `ishuodong` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'huodongname')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `huodongname` varchar(100) NOT NULL COMMENT '活动名称';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'huodongurl')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `huodongurl` varchar(255) NOT NULL COMMENT '活动链接网址';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'ishuodong')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `ishuodong` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'isindex')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `isindex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否首页显示0为不需要1为需要';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'isvotexq')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `isvotexq` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否详情页显示0为不需要1为需要';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'ispaihang')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `ispaihang` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否排行页显示0为不需要1为需要';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'isreg')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `isreg` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否报名页显示0为不需要1为需要';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'isdes')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `isdes` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否描述页显示0为不需要1为需要';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'addpv')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `addpv` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启添加投稿';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'addpvapp')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `addpvapp` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '前端是否允许用户报名';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'messagetemplate')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `messagetemplate` varchar(255) NOT NULL COMMENT '投票模板ID';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'regmessagetemplate')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `regmessagetemplate` varchar(255) NOT NULL COMMENT '报名模板ID';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'shmessagetemplate')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `shmessagetemplate` varchar(255) NOT NULL COMMENT '报名模板ID';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'iscode')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `iscode` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启投票验证码';");
}
 if(!pdo_fieldexists('fm_photosvote_reply', 'isedes')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `isedes` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启首页显示说明';");
}

//字段长度
if(pdo_fieldexists('fm_photosvote_provevote', 'picarr')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_provevote')." CHANGE `picarr` `picarr` varchar(5000) DEFAULT '';");
}
//颜色及背景配置
if(!pdo_fieldexists('fm_photosvote_reply', 'bgarr')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `bgarr` varchar(5000) NOT NULL DEFAULT '';");
}

if(!pdo_fieldexists('fm_photosvote_reply', 'tstart_time')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `tstart_time` int(10) unsigned NOT NULL COMMENT '投票开始时间';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'tend_time')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `tend_time` int(10) unsigned NOT NULL COMMENT '投票结束时间';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'bstart_time')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `bstart_time` int(10) unsigned NOT NULL COMMENT '报名开始时间';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'bend_time')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `bend_time` int(10) unsigned NOT NULL COMMENT '报名结束时间';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'ttipstart')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `ttipstart` varchar(255) NOT NULL COMMENT '投票开始时间';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'ttipend')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `ttipend` varchar(255) NOT NULL COMMENT '投票结束时间';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'btipstart')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `btipstart` varchar(255) NOT NULL COMMENT '报名开始时间';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'btipend')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `btipend` varchar(255) NOT NULL COMMENT '报名结束时间';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'tmreply')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `tmreply` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '弹幕评论是否同步到数据库';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'tmyushe')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `tmyushe` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '弹幕评论是否同步到数据库';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'isipv')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `isipv` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启IP作弊限制';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'ipturl')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `ipturl` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '存在作弊ip后是否继续允许查看，投票，评论等';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'ipstopvote')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `ipstopvote` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '存在作弊ip后是否继续允许查看，投票，评论等';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'ipannounce')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `ipannounce` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启公告';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'tmoshi')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `tmoshi` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '首页显示模式';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'mediatype')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `mediatype` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '上传模式';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'music')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `music` varchar(512) NOT NULL DEFAULT '' COMMENT '音乐';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'mediaid')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `mediaid` varchar(512) NOT NULL DEFAULT '' COMMENT 'voiceid';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'vedio')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `vedio` varchar(512) NOT NULL DEFAULT '' COMMENT '视频';");
}
if(!pdo_fieldexists('fm_photosvote_data', 'tfrom_user')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_data')." ADD `tfrom_user` varchar(150) NOT NULL DEFAULT '' COMMENT '被分享用户openid';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'tpname')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `tpname` varchar(255) NOT NULL COMMENT '投票名字';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'rqname')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `rqname` varchar(255) NOT NULL COMMENT '人气名字';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'tpsname')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `tpsname` varchar(255) NOT NULL COMMENT '投票数名字';");
}
if(!pdo_fieldexists('fm_photosvote_announce', 'url')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_announce')." ADD `url` varchar(500) NOT NULL DEFAULT '' COMMENT '公告链接';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'votesuccess')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `votesuccess` varchar(555) NOT NULL COMMENT '投票成功提示语';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'subscribedes')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `subscribedes` varchar(555) NOT NULL COMMENT '分享提示语';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'csrs')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `csrs` varchar(555) NOT NULL COMMENT '参赛作品';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'ljtp')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `ljtp` varchar(555) NOT NULL COMMENT '累计投票';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'cyrs')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `cyrs` varchar(555) NOT NULL COMMENT '参与人数';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'voicebg')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `voicebg` varchar(555) NOT NULL COMMENT '背景';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'voice')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `voice` varchar(512) NOT NULL DEFAULT '' COMMENT '视频';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'timelength')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `timelength` varchar(512) NOT NULL DEFAULT '' COMMENT '时间轴';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'fmmid')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `fmmid` varchar(512) NOT NULL DEFAULT '' COMMENT '文件名';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'voicemoshi')) {
	pdo_query("ALTER TABLE  ".tablename('fm_photosvote_reply')." ADD `voicemoshi` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '语音室模式';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'qiniu')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `qiniu` varchar(5000) NOT NULL DEFAULT '' COMMENT 'qiniu';");
}	  
	 
if(!pdo_fieldexists('fm_photosvote_reply', 'votetime')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `votetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户投票时间';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'ttipvote')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `ttipvote` varchar(100) NOT NULL COMMENT '用户投票时间结束提示语';");
}

if(!pdo_fieldexists('fm_photosvote_reply', 'mediatypem')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `mediatypem` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '上传模式';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'mediatypev')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `mediatypev` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '上传模式';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'isdaojishi')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `isdaojishi` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '倒计时';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'picarr_1')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `picarr_1` varchar(500) NOT NULL DEFAULT '' COMMENT '照片组';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'picarr_2')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `picarr_2` varchar(500) NOT NULL DEFAULT '' COMMENT '照片组';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'picarr_3')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `picarr_3` varchar(500) NOT NULL DEFAULT '' COMMENT '照片组';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'picarr_4')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `picarr_4` varchar(500) NOT NULL DEFAULT '' COMMENT '照片组';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'picarr_5')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `picarr_5` varchar(500) NOT NULL DEFAULT '' COMMENT '照片组';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'picarr_6')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `picarr_6` varchar(500) NOT NULL DEFAULT '' COMMENT '照片组';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'picarr_7')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `picarr_7` varchar(500) NOT NULL DEFAULT '' COMMENT '照片组';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'picarr_8')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `picarr_8` varchar(500) NOT NULL DEFAULT '' COMMENT '照片组';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'isjob')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD  `isjob` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否需要输入职业0为不需要1为需要';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'isxingqu')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD  `isxingqu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否需要输入兴趣0为不需要1为需要';");
}

if(!pdo_fieldexists('fm_photosvote_reply', 'limitip')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD   `limitip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票ip每天限制数';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'webinfo')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD   `webinfo` text NOT NULL COMMENT '内容';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'bgarr')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD  `bgarr` varchar(1000) NOT NULL COMMENT '颜色及背景配置';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'votesuccess')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD  `votesuccess` varchar(200) NOT NULL COMMENT '投票成功提示语';");
}

if(!pdo_fieldexists('fm_photosvote_reply', 'subscribedes')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD   `subscribedes` varchar(200) NOT NULL COMMENT '分享提示语';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'voicebg')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD   `voicebg` varchar(200) NOT NULL COMMENT '录音室背景';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'qiniu')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD    `qiniu` varchar(600) NOT NULL COMMENT '七牛';");
}

if(!pdo_fieldexists('fm_photosvote_provevote', 'youkuurl')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD   `youkuurl` varchar(200) NOT NULL DEFAULT '' COMMENT '视频';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'job')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD    `job` varchar(20) NOT NULL DEFAULT '' COMMENT '职业';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'xingqu')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD     `xingqu` varchar(20) NOT NULL DEFAULT '' COMMENT '兴趣';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'iparr')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD   `iparr` varchar(200) NOT NULL DEFAULT '' COMMENT 'ip地区';");
}

if(!pdo_fieldexists('fm_photosvote_votelog', 'iparr')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_votelog')." ADD   `iparr` varchar(200) NOT NULL DEFAULT '' COMMENT 'ip地区';");
}

if(!pdo_fieldexists('fm_photosvote_bbsreply', 'iparr')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_bbsreply')." ADD `iparr` varchar(200) NOT NULL DEFAULT '' COMMENT 'ip地区';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'job')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `job` varchar(50) NOT NULL DEFAULT '' COMMENT '职业';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'xingqu')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `xingqu` varchar(50) NOT NULL DEFAULT '' COMMENT '兴趣';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'isjob')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `isjob` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否需要输入职业0为不需要1为需要';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'isxingqu')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `isxingqu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否需要输入兴趣0为不需要1为需要';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'indexorder')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `indexorder` int(3) unsigned NOT NULL DEFAULT '0' COMMENT '首页排序';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'istopheader')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `istopheader` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '最上方';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'isid')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `isid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'isid';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'zanzhums')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD `zanzhums` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '赞助商显示';");
}

if(!pdo_fieldexists('fm_photosvote_reply', 'codekey')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD  `codekey` varchar(64) NOT NULL COMMENT '验证码key';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'youkuurl')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `youkuurl` varchar(255) NOT NULL DEFAULT '' COMMENT 'youkuurl';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'ysid')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `ysid` int(10) unsigned NOT NULL COMMENT 'ysid';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'ewm')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `ewm` varchar(200) NOT NULL DEFAULT '' COMMENT '二维码地址';");
}

if(!pdo_fieldexists('fm_photosvote_advs', 'ismiaoxian')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_advs')." ADD `ismiaoxian` int(10) unsigned NOT NULL COMMENT 'ismiaoxian';");
}

if(!pdo_fieldexists('fm_photosvote_advs', 'issuiji')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_advs')." ADD `issuiji` int(10) unsigned NOT NULL COMMENT 'ismiaoxian';");
}

if(!pdo_fieldexists('fm_photosvote_advs', 'times')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_advs')." ADD `times` int(10) unsigned NOT NULL COMMENT 'ismiaoxian';");
}

if(!pdo_fieldexists('fm_photosvote_advs', 'nexttime')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_advs')." ADD `nexttime` int(10) unsigned NOT NULL COMMENT 'ismiaoxian';");
}

if(!pdo_fieldexists('fm_photosvote_provevote', 'tfrom_user')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD `tfrom_user` varchar(150) NOT NULL DEFAULT '' COMMENT '被分享用户openid';");
}
if(!pdo_fieldexists('fm_photosvote_advs', 'description')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_advs')." ADD `description` varchar(150) NOT NULL DEFAULT '' COMMENT '描述';");
}

if(!pdo_fieldexists('fm_photosvote_reply', 'hhhdpicture')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD  `hhhdpicture` varchar(150) NOT NULL COMMENT '会话活动图片';");
}
//0731
if(!pdo_fieldexists('fm_photosvote_reply', 'fansmostvote')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD   `fansmostvote` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户最高投票数';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'mtemplates')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD  `mtemplates` varchar(500) NOT NULL COMMENT '模板ID';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'huodong')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD    `huodong` varchar(500) NOT NULL COMMENT '活动';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'command')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD   `command` varchar(10) NOT NULL COMMENT '报名命令';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'istop')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD  `istop` varchar(300) NOT NULL COMMENT '顶部设置';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'isid')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD    `isid` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'isid';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'hhhdpicture')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD      `hhhdpicture` varchar(150) NOT NULL COMMENT '会话活动图片';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'iplocallimit')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD    `iplocallimit` varchar(100) NOT NULL COMMENT '地区限制';");
}
if(!pdo_fieldexists('fm_photosvote_reply', 'iplocaldes')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply')." ADD    `iplocaldes` varchar(100) NOT NULL COMMENT '地区限制';");
}
if(!pdo_fieldexists('fm_photosvote_provevote', 'ysid')) {
	pdo_query("ALTER TABLE ".tablename('fm_photosvote_provevote')." ADD   `ysid` int(10) unsigned NOT NULL COMMENT 'ysid';");
}