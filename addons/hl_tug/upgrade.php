<?php
 if(!pdo_fieldexists('hl_tug_reply', 'timelimit')) {
pdo_query("ALTER TABLE ".tablename('hl_tug_reply')." ADD `timelimit` int(11) NOT NULL DEFAULT '0';");
}