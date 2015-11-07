<?php defined('IN_IA') or exit('Access Denied');?><ol class="breadcrumb">
	<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
	<li><a href="<?php  echo url('system/welcome');?>">系统</a></li>
	<li class="active"><?php  if($do == 'display') { ?>管理服务<?php  } else if($do == 'post' && empty($rid)) { ?> 添加常用服务<?php  } else if($do == 'post' && !empty($rid)) { ?>编辑常用服务<?php  } ?></li>
</ol>
<ul class="nav nav-tabs">
	<li<?php  if($do == 'display') { ?> class="active"<?php  } ?>><a href="<?php  echo url('extension/service/display')?>">管理服务</a></li>
	<li<?php  if($do == 'post' && empty($rid)) { ?> class="active"<?php  } ?>><a href="<?php  echo url('extension/service/post')?>"><i class="icon-plus"></i> 添加常用服务</a></li>
	<?php  if($do == 'post' && !empty($rid)) { ?><li class="active"><a href="<?php  echo url('extension/service/post', array('id' => $rid))?>"><i class="icon-plus"></i> 编辑常用服务</a></li><?php  } ?>
</ul>
