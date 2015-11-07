<?php




if(!pdo_fieldexists('brand_reply', 'new_pic')) {
	pdo_query("ALTER TABLE ".tablename('brand_reply')." ADD `new_pic` VARCHAR(200) NOT NULL;");
}
if(!pdo_fieldexists('brand_reply', 'news_content')) {
	pdo_query("ALTER TABLE ".tablename('brand_reply')." ADD `news_content` VARCHAR(500) NOT NULL;");
}




if(!pdo_fieldexists('brand', 'btnName')) {
    pdo_query("ALTER TABLE ".tablename('brand')." ADD btnName VARCHAR(20) DEFAULT NUL ;");
}

if(!pdo_fieldexists('brand', 'btnUrl')) {
    pdo_query("ALTER TABLE ".tablename('brand')." ADD btnUrl VARCHAR(100) DEFAULT NULL ;");
}

if(!pdo_fieldexists('brand', 'showMsg')) {
    pdo_query("ALTER TABLE ".tablename('brand')." ADD showMsg INT(1) DEFAULT 0 ;");
}



if(!pdo_fieldexists('brand', 'btnName1')) {
    pdo_query("ALTER TABLE ".tablename('brand')." ADD btnName1 VARCHAR(20) DEFAULT NULL ;");
}

if(!pdo_fieldexists('brand', 'btnUrl1')) {
    pdo_query("ALTER TABLE ".tablename('brand')." ADD btnUrl1 VARCHAR(100) DEFAULT NULL ;");
}



if(!pdo_fieldexists('brand', 'btnName2')) {
    pdo_query("ALTER TABLE ".tablename('brand')." ADD btnName2 VARCHAR(20) DEFAULT NULL ;");
}

if(!pdo_fieldexists('brand', 'btnUrl2')) {
    pdo_query("ALTER TABLE ".tablename('brand')." ADD btnUrl2 VARCHAR(100) DEFAULT NULL ;");
}


if(!pdo_fieldexists('brand', 'intro2')) {
    pdo_query("ALTER TABLE ".tablename('brand')." ADD intro2 VARCHAR(500) NOT NULL ;");
}



if(!pdo_fieldexists('brand_message', 'address')) {
    pdo_query("ALTER TABLE ".tablename('brand_message')." ADD address VARCHAR(200) NOT NULL ;");
}








