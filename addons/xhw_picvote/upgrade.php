<?php
 if(!pdo_fieldexists('xhw_picvote', 'rule')) {
	pdo_query("ALTER TABLE ".tablename('xhw_picvote')." ADD `rule` text NOT NULL;");
}
 if(!pdo_fieldexists('xhw_picvote', 'bgcolor')) {
	pdo_query("ALTER TABLE ".tablename('xhw_picvote')." ADD `bgcolor` varchar(20) NOT NULL;");
}
 if(!pdo_fieldexists('xhw_picvote', 'viewnum')) {
	pdo_query("ALTER TABLE ".tablename('xhw_picvote')." ADD `viewnum` int(10) NOT NULL;");
}
