<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<link type="text/css" rel="stylesheet" href="../addons/jufeng_wcy/images/common.css">
<link type="text/css" rel="stylesheet" href="http://cdn.staticfile.org/fancybox/2.1.5/jquery.fancybox.min.css">
<script type="text/javascript" src="http://cdn.staticfile.org/fancybox/2.1.5/jquery.fancybox.min.js"></script>
<script type="text/javascript">
//jssdkconfig.debug = true;
require(['jquery'], function($){
               wx.ready(function(){
wx.getLocation({
    success: function (res) {
        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
        var speed = res.speed; // 速度，以米/每秒计
        var accuracy = res.accuracy; // 位置精度
		$.getJSON("<?php  echo $this->createMobileUrl('dianjia',array('op'=>'locate'))?>", {'loc_x' : res.latitude, 'loc_y' : res.longitude}, function(s){
			if (s.message == 'refresh') {
				location.href = "<?php  echo $this->createMobileUrl('dianjia')?>";
			} 
		});//getJSON
    }
});
             });
});
	$(document).ready(function() {
		$('.fancybox').fancybox();
		$('.fancybox-skin').css("padding","0");
	});
</script>

	<div class="menu-list">
    <div class="panel panel-default" style="margin-top:10px;margin-bottom:0;padding-bottom:10px;">
    <a class="btn <?php  if($_GPC['typeid'] == 0) { ?>btn-success<?php  } else { ?>btn-default<?php  } ?>" style="margin: 5px 0 3px 5px;" href="<?php  echo $this->createMobileUrl('dianjia', array('typeid' =>0,'order' =>$_GPC['order']))?>" role="button">全部分类</a>
    <?php  if(is_array($shoptype)) { foreach($shoptype as $item) { ?>
    <a class="btn <?php  if($item['id'] == $_GPC['typeid']) { ?>btn-success<?php  } else { ?>btn-default<?php  } ?>" style="margin: 5px 0 3px 5px;" href="<?php  echo $this->createMobileUrl('dianjia', array('order' =>$_GPC['order'],'typeid' =>$item['id']))?>" role="button"><?php  echo $item['name'];?></a>
    <?php  } } ?>
	</div>
		<ul class="list-unstyled">
			<?php  if(is_array($shop)) { foreach($shop as $item) { ?>
			<li class="shopli">
             <div class="pull-right">  
             <?php  if($item['enabled'] == "0") { ?>      
                     <div><span class="label label-default">休息中</span></div>
                      <?php  } ?>
                       <div>&nbsp;&nbsp;</div>
                    <div><button type="button" class="btn btn-sm btn-default" onClick="javascript:document.location.href='http://apis.map.qq.com/uri/v1/geocoder?coord=<?php  echo $item['loc_x'];?>,<?php  echo $item['loc_y'];?>';"><i class="glyphicon glyphicon-map-marker"></i>&nbsp;<?php  echo $item['dist'];?></button></div>
                    
			</div>
				 <a href="<?php  echo $this->createMobileUrl('list',array('pcate'=>$item['id']))?>">
				<div class="pull-left menu-pic">
					<?php  if($item['thumb']) { ?><img src="<?php  echo $_W['attachurl'];?><?php  echo $item['thumb'];?>" class="img-rounded">
                    <?php  } else { ?><img src="<?php  echo $_W['attachurl'];?>/headimg_<?php  echo $_W['uniacid'];?>.jpg" class="img-rounded">
                    <?php  } ?>
				</div>
				<div class="pull-left menu-detail">
					<span class="title"><?php  echo $item['name'];?></span>
                    <span class="click">热度：<?php  echo $item['total'];?></span>
                     <span class="click"><?php  echo $item['sendprice'];?>元起送</span>
				</div>
                </a>
			</li>
             <div class="shopfoot"></div>
			<?php  } } ?>
		</ul>
		<?php  echo $pager;?>
	</div>
<div class="navbar1 navbar2 btn-group btn-group-justified">
     <div class="btn-group btn-group-lg dropup">
        <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php  if($_GPC['order'] == 0) { ?>默认排序<?php  } else if($_GPC['order'] == 1) { ?>按热度<?php  } else if($_GPC['order'] == 2) { ?>按起送价<?php  } else if($_GPC['order'] == 3) { ?>营业优先<?php  } ?><span class="caret"></span>
        </a>
        <ul class="dropdown-menu" role="menu">
          <li <?php  if($_GPC['order'] == 0) { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createMobileUrl('dianjia', array('typeid' => $_GPC['typeid'],'order' =>0))?>">默认排序</a></li>
           <li class="divider"></li>
          <li <?php  if($_GPC['order'] == 1) { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createMobileUrl('dianjia', array('typeid' => $_GPC['typeid'],'order' =>1))?>">按热度</a></li>
           <li class="divider"></li>
          <li <?php  if($_GPC['order'] == 2) { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createMobileUrl('dianjia', array('typeid' => $_GPC['typeid'],'order' =>2))?>">按起送价</a></li>
          <li class="divider"></li>
          <li <?php  if($_GPC['order'] == 3) { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createMobileUrl('dianjia', array('typeid' => $_GPC['typeid'],'order' =>3))?>">营业优先</a></li>
        </ul>
      </div>
      <div class="btn-group btn-group-lg">
      <a href="<?php  echo $this->createMobileUrl('myorder',array('pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate']))?>" class="btn btn-default" role="button"><i class="glyphicon glyphicon-th-list"></i>&nbsp;我的订单</a>
      </div>
      <div class="btn-group btn-group-lg">
      <a href="<?php  echo url('mc');?>" class="btn btn-default" role="button"><i class="glyphicon glyphicon-user"></i>&nbsp;会员中心</a>
      </div>
    </div>
<?php  $footer_off = 0;?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>