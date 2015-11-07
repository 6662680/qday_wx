<?php
 if(!pdo_fieldexists('bigwheel_reply', 'c_rate_one')) {
	pdo_query("ALTER TABLE ".tablename('bigwheel_reply')." ADD `c_rate_one`  double DEFAULT '0';");
}
if(!pdo_fieldexists('bigwheel_reply', 'c_rate_two')) {
	pdo_query("ALTER TABLE ".tablename('bigwheel_reply')." ADD `c_rate_two`  double DEFAULT '0';");
}
if(!pdo_fieldexists('bigwheel_reply', 'c_rate_three')) {
	pdo_query("ALTER TABLE ".tablename('bigwheel_reply')." ADD `c_rate_three`  double DEFAULT '0';");
}
if(!pdo_fieldexists('bigwheel_reply', 'c_rate_four')) {
	pdo_query("ALTER TABLE ".tablename('bigwheel_reply')." ADD `c_rate_four`  double DEFAULT '0';");
}
if(!pdo_fieldexists('bigwheel_reply', 'c_rate_five')) {
	pdo_query("ALTER TABLE ".tablename('bigwheel_reply')." ADD `c_rate_five`  double DEFAULT '0';");
}
if(!pdo_fieldexists('bigwheel_reply', 'c_rate_six')) {
	pdo_query("ALTER TABLE ".tablename('bigwheel_reply')." ADD `c_rate_six`  double DEFAULT '0';");
}
if(pdo_fieldexists('bigwheel_reply', 'share_txt')) {
     pdo_query("ALTER TABLE  ".tablename('bigwheel_reply')." CHANGE `share_txt` `share_txt` text NOT NULL;");
}