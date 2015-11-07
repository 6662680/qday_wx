<?php

$sql = "
	
	DROP TABLE IF EXISTS `ims_fm_photosvote_reply`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_provevote`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_provevote_name`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_provevote_voice`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_votelog`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_bbsreply`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_gift`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_banners`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_advs`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_iplist`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_awarding`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_awardingtype`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_giftmika`;
	DROP TABLE IF EXISTS `ims_fm_photosvote_data`;
";

pdo_run($sql);