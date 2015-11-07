<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<link type="text/css" rel="stylesheet" href="../addons/jufeng_wcy/images/common.css">
<style>
.table{font-size:12px;}
.table td{padding:5px 0;}
.table .title{border-top:0; font-weight:600; color:#51a351;}
.table .price{}
.table .price .total{font-size:14px; font-weight:bold; display:inline-block; width:100px;}
.table .price .btn{margin-top:5px;}
.table .price .payover{font-size:14px; font-weight:bold;}
</style>

<div class="order-main">
	<div class="order-hd">
    <span>我的订单</span>
	</div>
			<?php  if(is_array($list)) { foreach($list as $item) { ?>
            	<div class="order-detail">

            		<div class="order-detail-list">
			<table class="table">
            <tr>
					<td class="title suoshudianjia" colspan="3">
						<span class="pull-left">所属店家：<i><?php  echo $item['pcate3'][0]['name'];?></i></span>
					</td>
				</tr>
                 <tr>
					<td class="title dd" colspan="3">
						<span class="pull-left">联系电话：</span>
                        <span class="pull-right"><b><?php  echo $item['mobile'];?></b></span>
					</td>
				</tr>
                 <tr>
					<td class="title dd" colspan="3">
						<span class="pull-left">送餐时间：</span>
                        <span class="pull-right"><b><?php  echo $item['time'];?></b></span>
					</td>
				</tr>
                 <tr>
					<td class="title dd" colspan="3">
						<span class="pull-left">送餐地址：</span>
                        <span class="pull-right"><b><?php  echo $item['address'];?></b></span>
					</td>
				</tr>
                <tr>
					<td class="title dd" colspan="3">
						<span class="pull-left">支付方式：</span>
                        <span class="pull-right"><b><?php  if($item['paytype'] == 1) { ?>在线支付<?php  } else { ?>餐到付款<?php  } ?></b></span>
					</td>
				</tr>
                <?php  if($item['other']) { ?>
                 <tr>
					<td class="title dd" colspan="3">
						<span class="pull-left">订单备注：</span>
                        <span class="pull-right"><b><?php  echo $item['other'];?></b></span>
					</td>
				</tr>
                <?php  } ?>
				<tr>
					<td class="title" colspan="3">
						<span class="pull-right"><?php  echo date('Y-m-d H:i', $item['createtime'])?></span>
						<span class="pull-left">★订单号：<?php  echo $item['ordersn'];?></span>
					</td>
				</tr>
				<?php  if(is_array($item['foods'])) { foreach($item['foods'] as $foods) { ?>
				<tr>
					<td style="width:40%;"><?php  echo $foods['title'];?></td>
					<td style="width:60%; text-align:right;">数量：<?php  echo $item['total'][$foods['id']]['total'];?>，单价：<?php  if($foods['preprice']) { ?><?php  echo $foods['preprice'];?><?php  } else { ?><?php  echo $foods['oriprice'];?><?php  } ?>元 / <?php  echo $foods['unit'];?></td>
				</tr>
				<?php  } } ?>
				<tr>
					<td class="price" colspan="3">
						<div class="pull-right">
							<span class="total">总计：<?php  echo $item['price'];?>元</span>
								<?php  if($item['status'] == 1 && $item['paytype'] == 1) { ?>
                                 <span class="text-success payover btn btn-warning"><a href="<?php  echo $this->createMobileUrl('pay', array('orderid' => $item['id']))?>"">立即付款</a></span>
                                  <span class="text-success payover btn btn-danger"><a href="tel:<?php  echo $item['pcate3'][0]['shouji'];?>" mce_href="tel:<?php  echo $item['pcate3'][0]['shouji'];?>">店家电话</a></span>
								<?php  } else if($item['status'] == '2') { ?>
								<span class="text-success payover">已下单</span>
                                <span class="text-success payover btn btn-danger"><a href="tel:<?php  echo $item['pcate3'][0]['shouji'];?>" mce_href="tel:<?php  echo $item['pcate3'][0]['shouji'];?>">店家电话</a></span>
                                <?php  } else if($item['status'] == '3') { ?>
                                <span class="text-success payover">已确认</span>
                                  <span class="text-success payover btn btn-danger"><a href="tel:<?php  echo $item['pcate3'][0]['shouji'];?>" mce_href="tel:<?php  echo $item['pcate3'][0]['shouji'];?>">店家电话</a></span>
                                <?php  } else if($item['status'] == '0') { ?>
                                <span class="text-success payover">已完成</span>
                                <span class="text-success payover btn btn-danger">
                                 <a href="<?php  echo $this->createMobileUrl('MyOrder', array('id' => $item['id'], 'op' => 'shanchu','pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate']))?>" onclick="return confirm('确认删除？');return false;">删除</a>
                                 </span>
                                
							<?php  } else if($item['status'] == -1) { ?>
								<span class="text-success payover">已取消</span>
                                 <span class="text-success payover btn btn-danger">
                                 <a href="<?php  echo $this->createMobileUrl('MyOrder', array('id' => $item['id'], 'op' => 'shanchu','pcate'=>$_GPC['pcate'],'ccate'=>$_GPC['ccate']))?>" onclick="return confirm('确认删除？');return false;">删除</a>
                                 </span>
                                  <span class="text-success payover btn btn-danger"><a href="tel:<?php  echo $item['pcate3'][0]['shouji'];?>" mce_href="tel:<?php  echo $item['pcate3'][0]['shouji'];?>">店家电话</a></span>
                           <?php  } ?>
						</div>
					</td>
				</tr>
			</table>
            </div>
            
            	</div>

			<?php  } } ?>
		
</div>
<div class="navbar1 navbar2 btn-group btn-group-justified">
      <div class="btn-group btn-group-lg">
      <a href="<?php  echo $this->createMobileUrl('dianjia')?>" class="btn btn-default" role="button"><i class="glyphicon glyphicon-chevron-left"></i>&nbsp;选择店铺</a>
      </div>
      <div class="btn-group btn-group-lg">
      <a href="<?php  echo url('mc');?>" class="btn btn-default" role="button"><i class="glyphicon glyphicon-user"></i>&nbsp;会员中心</a>
      </div>
    </div>
<script>
$(function() {
	$('.order-detail-list li:last').css("border-bottom", 0);
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>