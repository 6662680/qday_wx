<?php defined('IN_IA') or exit('Access Denied');?><div class="mobile-img">
	<?php  if(!empty($foods['thumb'])) { ?><img src="<?php  echo $_W['attachurl'];?><?php  echo $foods['thumb'];?>" width="100%"/> <?php  } else { ?><img src="<?php  echo $_W['attachurl'];?>/headimg_<?php  echo $_W['uniacid'];?>.jpg" /"><?php  } ?>
</div>
<div class="mobile-div img-rounded" style="margin:10px 0;text-align:center;">
	<div class="mobile-hd" style="text-align:center;margin-bottom:5px;">
    <?php  echo $foods['title'];?>
    <span class="menu-list-button reduce" onclick="order.reduce(<?php  echo $foods['id'];?>);"><img src="../addons/jufeng_wcy/images/reduce.png" height="30px" width="30px" /></span>
	<span class="menu-list-num foodsnum_<?php  echo $foods['id'];?>"><?php  if($foodscart['total']) { ?><?php  echo $foodscart['total'];?><?php  } else { ?>0<?php  } ?></span>
    <span class="menu-list-button add" onclick="order.add(<?php  echo $foods['id'];?>)"><img src="../addons/jufeng_wcy/images/add.png" height="30px" width="30px" /></span>

    </div>
    <div class="mobile-hd" style="margin:0 auto;">
    单价：¥<?php  echo $foods['preprice'];?>/<?php  echo $foods['unit'];?><?php  if($foods['preprice'] < $foods['oriprice']) { ?> <span style="text-decoration:line-through;">¥<?php  echo $foods['oriprice'];?>/<?php  echo $foods['unit'];?></span><?php  } ?>
    </div>
    <div class="mobile-hd" style="margin:0 auto;">
    热度：<?php  echo $foods['hits'];?>人点过
    </div>
    <div class="mobile-hd" style="margin:0 auto;">
    余量：充足
    </div>
</div>