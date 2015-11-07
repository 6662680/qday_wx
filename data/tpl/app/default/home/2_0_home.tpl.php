<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
body{
	font:<?php  echo $_W['styles']['fontsize'];?> <?php  echo $_W['styles']['fontfamily'];?>;
	color:<?php  if(empty($_W['styles']['fontcolor'])) { ?>#555<?php  } else { ?><?php  echo $_W['styles']['fontcolor'];?><?php  } ?>;
	padding:0;
	margin:0;
	background-image:url('<?php  if(empty($_W['styles']['indexbgimg'])) { ?>./themes/default/images/bg_index.jpg<?php  } else { ?><?php  echo $_W['styles']['indexbgimg'];?><?php  } ?>');
	background-size:cover;
	background-color:<?php  if(empty($_W['styles']['indexbgcolor'])) { ?>#fbf5df<?php  } else { ?><?php  echo $_W['styles']['indexbgcolor'];?><?php  } ?>;
	<?php  echo $_W['styles']['indexbgextra'];?>
}
a{color:<?php  echo $_W['styles']['linkcolor'];?>; text-decoration:none;}
<?php  echo $_W['styles']['css'];?>
.home-container{width:58%;overflow:hidden;margin:.6em .3em;}
.home-container .box-item{float:left;display:block;text-decoration:none;outline:none;width:5em;height:6em;margin:.1em;background:rgba(0, 0, 0, 0.3);text-align:center;color:#ccc;}
.home-container i{display:block;height:45px; margin: 5px auto; font-size:35px; padding-top:10px; width:45px;}
.home-container span{color:<?php  echo $_W['styles']['fontnavcolor'];?>;display:block; width:90%; margin:0 5%;  overflow:hidden; height:20px; line-height:20px;}
.footer{color:#dddddd;}
</style>
<div class="home-container clearfix">
	<?php  $site_navs = modulefunc('site', 'site_navs', array (
  'func' => 'site_navs',
  'item' => 'row',
  'limit' => 10,
  'index' => 'iteration',
  'multiid' => 0,
  'uniacid' => 2,
  'acid' => 0,
)); if(is_array($site_navs)) { $i=0; foreach($site_navs as $i => $row) { $i++; $row['iteration'] = $i; ?>
	<?php  echo $row['html'];?>
	<?php  } } ?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>