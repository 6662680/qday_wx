<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($_GPC['a']) && $_GPC['a'] != 'appstore') { ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
			<ol class="breadcrumb">
				<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
				<li class=""><a href="<?php  echo url('system/welcome');?>">系统</a></li>
				<li class="active"><?php  echo $title;?></li>
			</ol>
			<div class="clearfix">
				<iframe src="<?php  echo $iframe;?>" marginheight="0" marginwidth="0" frameborder="0" width="100%" style="<?php  if($do == 'profile') { ?>height:900px;<?php  } ?>" scrolling="no" allowTransparency="true"></iframe>
			</div>
		</div>
	</div>
</div>
<style>
	.container-fluid:{padding-bottom:30px;}
</style>
<?php  } else { ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>
<iframe src="<?php  echo $iframe;?>" marginheight="0" marginwidth="0" frameborder="0" width="100%" scrolling="auto" allowTransparency="true" id="iframe" name="iframe"></iframe>
<style>
	body{overflow:hidden; background:#FFF; display:flex;}
	.gw-container{width:100%;}
</style>
<script>
	require(['jquery'], function($){
		$("#iframe, body").height($(window).height());
	});
</script>
<?php  } ?>
</body>
</html>