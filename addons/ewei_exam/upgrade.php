<?php

if(!pdo_fieldexists('ewei_exam_member_info', 'mid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_exam_member_info')." ADD `mid` INT( 11 ) default 0;");
}
if(!pdo_fieldexists('ewei_exam_paper', 'papertype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_exam_paper')." ADD `papertype` INT( 11 ) default 0;");
}
