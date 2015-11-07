<?php defined('IN_IA') or exit('Access Denied');?><!--<script>window.location.href="/www/index.php"</script>-->
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo url('home/welcome/' . $do);?>">账号概况 - <?php  echo $title;?></a></li>
</ul>
<div class="clearfix welcome-container">
	<?php  if($do != 'ext' && $do != 'solution') { ?>
	<div class="page-header">
		<h4><i class="fa fa-plane"></i> 快捷操作</h4>
	</div>
	<div class="shortcut clearfix">
		<a href="<?php  echo url('platform/reply', array('m' => 'userapi'))?>">
			<i class="fa fa-sitemap"></i>
			<span>自定义接口</span>
		</a>
		<?php  if(is_array($shortcuts)) { foreach($shortcuts as $shortcut) { ?>
			<a href="<?php  echo $shortcut['link'];?>" title="<?php  echo $shortcut['title'];?>">
				<img src="<?php  echo $shortcut['image'];?>" alt="<?php  echo $shortcut['title'];?>" class="img-rounded" />
				<span><?php  echo $shortcut['title'];?></span>
			</a>
		<?php  } } ?>
	</div>
	<?php  } ?>
	<?php  if($do == 'platform') { ?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('home/welcome-platform', TEMPLATE_INCLUDEPATH)) : (include template('home/welcome-platform', TEMPLATE_INCLUDEPATH));?>
	<?php  } ?>
	<?php  if($do == 'site') { ?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('home/welcome-site', TEMPLATE_INCLUDEPATH)) : (include template('home/welcome-site', TEMPLATE_INCLUDEPATH));?>
	<?php  } ?>
	<?php  if($do == 'mc') { ?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('home/welcome-mc', TEMPLATE_INCLUDEPATH)) : (include template('home/welcome-mc', TEMPLATE_INCLUDEPATH));?>
	<?php  } ?>
	<?php  if($do == 'setting') { ?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('home/welcome-setting', TEMPLATE_INCLUDEPATH)) : (include template('home/welcome-setting', TEMPLATE_INCLUDEPATH));?>
	<?php  } ?>
	<?php  if($do == 'ext') { ?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('home/welcome-ext', TEMPLATE_INCLUDEPATH)) : (include template('home/welcome-ext', TEMPLATE_INCLUDEPATH));?>
	<?php  } ?>
	<?php  if($do == 'solution') { ?>
	<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('home/welcome-solution', TEMPLATE_INCLUDEPATH)) : (include template('home/welcome-solution', TEMPLATE_INCLUDEPATH));?>
	<?php  } ?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
