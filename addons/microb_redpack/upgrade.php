<?php
if(!pdo_fieldexists('mbrp_activities', 'banner')) {
    pdo_run("ALTER TABLE `ims_mbrp_activities` ADD `banner` VARCHAR(500) NOT NULL DEFAULT '';");
}
