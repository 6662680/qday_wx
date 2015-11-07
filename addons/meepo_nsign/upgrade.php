<?php

if(!pdo_fieldexists('nsign_add', 'type')) {
	pdo_query("ALTER TABLE ".tablename('nsign_add')." ADD `type` TEXT NOT NULL ;");
}