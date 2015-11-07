<?php






if(!pdo_fieldexists('weishare', 'count')) {
	pdo_query("ALTER TABLE ".tablename('weishare')." ADD `count` int(11) NOT NULL  COMMENT '领卡数量限制';");
}

if(!pdo_fieldexists('weishare', 'showu')) {
	pdo_query("ALTER TABLE ".tablename('weishare')." ADD `showu` varchar(1) NOT NULL DEFAULT 0;");
}

if(!pdo_fieldexists('weishare', 'sortcount')) {
	pdo_query("ALTER TABLE ".tablename('weishare')." ADD `sortcount` varchar(100) NOT NULL DEFAULT 10 ;");
}






if(!pdo_fieldexists('weishare', 'shareIcon')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD `shareIcon` varchar(200) NOT NULL COMMENT '分享图标' ;");
}


if(!pdo_fieldexists('weishare', 'shareTitle')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD `shareTitle` varchar(200) NOT NULL ;");
}

if(!pdo_fieldexists('weishare', 'shareContent')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD `shareContent` varchar(200) NOT NULL ;");
}



if(!pdo_fieldexists('weishare', 'totallimit')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD `totallimit` int(11) NOT NULL  COMMENT '总得助力次数' ;");
}



if(!pdo_fieldexists('weishare', 'background')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD `background` varchar(100) NOT NULL COMMENT '背景颜色';");
}

if(!pdo_fieldexists('weishare', 'tip')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD `tip` varchar(100) NOT NULL COMMENT '提示语';");
}

if(!pdo_fieldexists('weishare', 'copyright')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD `copyright` varchar(100) NOT NULL COMMENT '版权' ;");
}



if(!pdo_fieldexists('weishare', 'cardname')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD `cardname` varchar(100) NOT NULL COMMENT '卡片名称' ;");
}


if(!pdo_fieldexists('weishare', 'unit')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD `unit` varchar(100) NOT NULL COMMENT '单位' ;");
}

if(!pdo_fieldexists('weishare', 'helplimit')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD `helplimit` int(11) NOT NULL  COMMENT '每天助力限制次数';");
}

if(!pdo_fieldexists('weishare', 'limittype')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD `limittype` int(1) NOT NULL  COMMENT '限制类型' ;");
}

if(!pdo_fieldexists('weishare_firend', 'sid')) {
    pdo_query("ALTER TABLE ".tablename('weishare_firend')." ADD `sid` int(10) NOT NULL DEFAULT '0' ;");
    
}


if(!pdo_fieldexists('weishare', 'endtime')) {
    pdo_query("ALTER TABLE ".tablename('weishare')." ADD endtime	int(11) unsigned NOT NULL COMMENT '日期' ;");

}


if(!pdo_fieldexists('weishare_setting', 'weid')) {
    pdo_query("ALTER TABLE ".tablename('weishare_setting')." ADD weid INT(11) UNSIGNED DEFAULT NULL ;");

}













