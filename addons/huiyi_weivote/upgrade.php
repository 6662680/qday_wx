<?php
/**
 * 微信投票
 * 模块定义
 * @url
 */

if(!pdo_fieldexists('huiyi_weivote_option', 'vid')) {
    pdo_query("ALTER TABLE ".tablename('huiyi_weivote_option')." ADD `vid` int(10) unsigned NOT NULL COMMENT '投票id';");
}

if(!pdo_fieldexists('huiyi_weivote_vote', 'uniac_type')) {
    pdo_query("ALTER TABLE ".tablename('huiyi_weivote_vote')." ADD `uniac_type` SMALLINT( 10 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '活动用公众号类型';");
}

if(!pdo_fieldexists('huiyi_weivote_vote', 'follow_vote')) {
    pdo_query("ALTER TABLE ".tablename('huiyi_weivote_vote')." ADD `follow_vote` SMALLINT( 10 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注投票/非关注投票';");
}

//if(!pdo_fieldexists('weivote_option', 'description')) {
//    pdo_query("ALTER TABLE ".tablename('weivote_option')." ADD `description` text DEFAULT '' COMMENT '选项描述信息';");
//} else {
//    pdo_query("ALTER TABLE ".tablename('weivote_option')." CHANGE `description` `description` text DEFAULT '' COMMENT '选项描述信息';");
//}
//if(!pdo_fieldexists('weivote_log', 'wexinno')) {} else {
//    pdo_query("ALTER TABLE ".tablename('weivote_log')." DROP `wexinno`;");
//}
//if(!pdo_indexexists('weivote_log', 'rid')) {
//    pdo_query("ALTER TABLE ".tablename('weivote_log')." ADD INDEX (`rid`)");
//}
