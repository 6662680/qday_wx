<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-bardown nav-justified" style="z-index:10;">
	<?php  if($cardstatus['status'] == 1) { ?>
		<li><a href="<?php  echo url('mc/bond/card');?>"><i class="fa fa-credit-card"></i> <span>会员卡</span></a></li>
	<?php  } ?>
	<li><a href="<?php  echo url('activity/coupon/');?>" class="active"><i class="fa fa-money"></i> <span>兑换</span></a></li>
	<li><a href="<?php  echo url('mc');?>"><i class="fa fa-user"></i> <span>我的</span></a></li>
</ul>
