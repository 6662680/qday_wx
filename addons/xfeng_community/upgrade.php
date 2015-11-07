<?php

if(!pdo_fieldexists('xcommunity_carpool', 'status')) {
  pdo_query("ALTER TABLE ".tablename('xcommunity_carpool')." ADD `status` int( 1 ) NOT NULL ;");
}