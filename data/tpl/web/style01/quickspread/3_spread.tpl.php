<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($op == 'leaflet') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('spread', array('op' => 'leaflet'));?>">传单库</a></li>
  <li <?php  if($op == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('spread', array('op' => 'post'));?>">编辑传单</a></li>
	<li <?php  if($op == 'black') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('spread', array('op' => 'black'));?>">黑名单</a></li>
	<li <?php  if($op == 'log') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('spread', array('op' => 'log'));?>">推广记录</a></li>
	<li <?php  if($op == 'qualitylog') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('spread', array('op' => 'qualitylog'));?>">优质推广</a></li>
  <?php  if($op == 'user') { ?> <li class="active"><a href="#">个人推广详情</a></li><?php  } ?>
</ul>


<?php  if($op == 'post') { ?>
  <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('post', TEMPLATE_INCLUDEPATH)) : (include template('post', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>
<?php  if($op == 'black') { ?>
  <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('black', TEMPLATE_INCLUDEPATH)) : (include template('black', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>
<?php  if($op == 'leaflet') { ?>
  <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('leaflet', TEMPLATE_INCLUDEPATH)) : (include template('leaflet', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>
<?php  if($op == 'log' or $op == 'qualitylog') { ?>
  <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('log', TEMPLATE_INCLUDEPATH)) : (include template('log', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>
<?php  if($op == 'user') { ?>
  <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('user', TEMPLATE_INCLUDEPATH)) : (include template('user', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>



<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
