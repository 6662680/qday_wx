<?php defined('IN_IA') or exit('Access Denied');?><div class="btn-group-top-box">
	<div class="btn-group btn-group-top">
		<a href="<?php  echo url('activity/coupon/display')?>" class="btn btn-default <?php  if($action == 'coupon') { ?>active<?php  } ?>">折扣券</a>
		<a href="<?php  echo url('activity/token/display')?>" class="btn btn-default <?php  if($action == 'token') { ?>active<?php  } ?>">代金券</a>
		<a href="<?php  echo url('activity/goods/display')?>" class="btn btn-default <?php  if($action == 'goods') { ?>active<?php  } ?>">实体物品</a>
		<a href="<?php  echo url('activity/partimes/display')?>" class="btn btn-default <?php  if($action == 'partimes') { ?>active<?php  } ?>">活动参与次数</a>
	</div>
</div>