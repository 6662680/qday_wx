<?php
//字段长度
if(pdo_fieldexists('business', 'content')) {
     pdo_query("ALTER TABLE  ".tablename('business')." CHANGE `content` `content` text NOT NULL;");
}