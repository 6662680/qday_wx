<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title><?php  if(!empty($_W['page']['sitename'])) { ?><?php  echo $_W['page']['sitename'];?><?php  } ?><?php  if(!empty($_W['account']['name'])) { ?><?php  echo $_W['account']['name'];?><?php  } ?></title>
	<meta name="format-detection" content="telephone=no, address=no">
	<meta name="apple-mobile-web-app-capable" content="yes" /> <!-- apple devices fullscreenkkkk -->
	<meta name="apple-touch-fullscreen" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<meta name="keywords" content="<?php  if(empty($_W['page']['keywords'])) { ?>微信,微信公众平台<?php  } else { ?><?php  echo $_W['page']['keywords'];?><?php  } ?>" />
	<meta name="description" content="<?php  if(empty($_W['page']['description'])) { ?>移动网站及移动互联网技术解决方案。<?php  } else { ?><?php  echo $_W['page']['description'];?><?php  } ?>" />
	<link href="./resource/css/bootstrap.min.css" rel="stylesheet">
	<link href="./resource/css/font-awesome.min.css" rel="stylesheet">
	<link href="./resource/css/animate.css" rel="stylesheet">
	<link href="./resource/css/common.css" rel="stylesheet">
	<link href="<?php  echo url('utility/style');?>" rel="stylesheet">
	<script src="./resource/js/require.js"></script>
	<script src="./resource/js/app/config.js"></script>
	<script type="text/javascript" src="./resource/js/lib/jquery-1.11.1.min.js"></script>
	<script type="text/javascript">
		window.sharedata = {
			'appId': '', // 服务号可以填写appId
			'imgUrl' : '', // 缩略图
			'link': '', // 内容链接
			'title': '', // 内容标题
			'desc': '' // 内容简介
		};
		window.onshared = ''; 
	</script>
	
</head>
<body>
<div class="container container-fill">
	
