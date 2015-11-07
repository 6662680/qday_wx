<?php
if(!pdo_fieldexists('fighting_setting', 'followurl')) {
    pdo_query("ALTER TABLE ".tablename('fighting_setting')." ADD  `followurl` varchar(1000) DEFAULT '';");
}
if(!pdo_fieldexists('fighting_setting', 'thumb')) {
    pdo_query("ALTER TABLE ".tablename('fighting_setting')." ADD    `thumb` varchar(100) NOT NULL COMMENT '广告';");
}

if(!pdo_fieldexists('fighting_setting', 'thumb_url')) {
    pdo_query("ALTER TABLE ".tablename('fighting_setting')." ADD    `thumb_url` varchar(100) NOT NULL COMMENT '广告URL';");
}

if(!pdo_fieldexists('fighting_question_bank', 'sid')) {
    pdo_query("ALTER TABLE ".tablename('fighting_question_bank')." ADD   `sid` int(10) unsigned NOT NULL COMMENT '广告URL';");
}
if(pdo_fieldexists('fighting_question_bank', 'figure')) {
    pdo_query("ALTER TABLE ".tablename('fighting_question_bank')." CHANGE `figure` `figure` int(30) NOT NULL;");
}
