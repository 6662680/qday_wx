<?php



$sql = "
	DROP TABLE IF EXISTS `ims_eso_sale_address`;
	DROP TABLE IF EXISTS `ims_eso_sale_cart`;
	DROP TABLE IF EXISTS `ims_eso_sale_category`;
	DROP TABLE IF EXISTS `ims_eso_sale_feedback`;
	DROP TABLE IF EXISTS `ims_eso_sale_goods`;
	DROP TABLE IF EXISTS `ims_eso_sale_order`;
	DROP TABLE IF EXISTS `ims_eso_sale_order_goods`;
	DROP TABLE IF EXISTS `ims_eso_sale_product`;
	DROP TABLE IF EXISTS `ims_eso_sale_spec`;
	DROP TABLE IF EXISTS `ims_eso_sale_dispatch`;
	DROP TABLE IF EXISTS `ims_eso_sale_express`;
	DROP TABLE IF EXISTS `ims_eso_sale_goods_option`;
	DROP TABLE IF EXISTS `ims_eso_sale_goods_param`;
	DROP TABLE IF EXISTS `ims_eso_sale_adv`;
	DROP TABLE IF EXISTS `ims_eso_sale_spec_item`;
	DROP TABLE IF EXISTS `ims_eso_sale_member`;
	DROP TABLE IF EXISTS `ims_eso_sale_commission`;
	DROP TABLE IF EXISTS `ims_eso_sale_rules`;
		DROP TABLE IF EXISTS `ims_eso_sale_share_history`;
	DROP TABLE IF EXISTS `ims_eso_sale_credit_request`;
	DROP TABLE IF EXISTS `ims_eso_sale_credit_award`;
	DROP TABLE IF EXISTS `ims_eso_sale_rule`;
";

pdo_run($sql);