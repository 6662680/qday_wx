<?php

if (true) { // always try, pitty
  $create_table_sql = "CREATE TABLE IF NOT EXISTS " . tablename('quickspread_blacklist') . " (
    `from_user` varchar(50) not null default '',
    `weid`  int(10) unsigned NOT NULL ,
    `access_time`  int(10) unsigned NOT NULL ,
    `hit` int(10) NOT NULL DEFAULT 0,
    PRIMARY KEY(from_user, weid)
  ) ENGINE = MYISAM DEFAULT CHARSET = utf8;";
  pdo_query($create_table_sql);
}

if(!pdo_fieldexists('quickspread_spread', 'max_credit')) {
	pdo_query("ALTER TABLE ".tablename('quickspread_spread')." ADD `max_credit` int(10) unsigned NOT NULL DEFAULT 0;");
}

if(!pdo_fieldexists('quickspread_user', 'address')) {
  pdo_query("ALTER TABLE ".tablename('quickspread_user')." ADD `address` varchar(512) NOT NULL DEFAULT '';");
}

if(!pdo_fieldexists('quickspread_user', 'memo')) {
  pdo_query("ALTER TABLE ".tablename('quickspread_user')." ADD `memo` varchar(512) NOT NULL DEFAULT '';");
}

if(!pdo_fieldexists('quickspread_channel', 'createtime')) {
	pdo_query("ALTER TABLE ".tablename('quickspread_channel')." ADD `createtime` int(10) unsigned NOT NULL DEFAULT 0;");
}

if(!pdo_fieldexists('quickspread_channel', 'bgparam')) {
	pdo_query("ALTER TABLE ".tablename('quickspread_channel')." ADD `bgparam` varchar(10240) NOT NULL DEFAULT '';");
}
