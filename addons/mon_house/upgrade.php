<?php


if (!pdo_fieldexists('mon_house_order', 'tel')) {
    pdo_query("ALTER TABLE " . tablename('mon_house_order') . "ADD  `tel` varchar(20) NOT NULL;");

}
















