<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<div class="main">
<ul class="nav nav-tabs">
	<li><a href="<?php  echo $this->createWebUrl('order',array('op'=>'list','hotelid'=>$hotelid,'roomidid'=>$roomidid));?>">订单管理</a></li>
	<li class="active"><a href="<?php  echo $this->createWebUrl('order',array('op'=>'edit','id'=>$id));?>">订单处理</a></li>
</ul>
<form action="<?php  echo $this->createWebUrl('order',array('op'=>'edit','id'=>$id));?>" class="form-horizontal form" method="post" enctype="multipart/form-data">
	<input type="hidden" name="id" value="<?php  echo $orderList['id'];?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			订单处理
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">订单编号</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo $orderList['ordersn'];?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">套餐名</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo $orderList['p_name'];?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">技师</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					 <?php  echo $orderList['t_name'];?>
				</div>
			</div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">预订人名字</label>
                <div class="col-sm-9 col-xs-12 form-control-static">
                    <?php  echo $orderList['contact_name'];?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">预订人手机号</label>
                <div class="col-sm-9 col-xs-12 form-control-static">
                    <?php  echo $orderList['mobile'];?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">预订时间</label>
                <div class="col-sm-9 col-xs-12 form-control-static">
                    <?php  echo $btime?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">价格</label>
                <div class="col-sm-9 col-xs-12 form-control-static">
                    <?php  echo $orderList['price'];?>元
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付方式</label>
                <div class="col-sm-9 col-xs-12 form-control-static">
                    <?php  if($orderList['paytype']==1) { ?>
                    余额支付
                    <?php  } else if($orderList['paytype']==21) { ?>
                    微支付
                    <?php  } else if($orderList['paytype']==22) { ?>
                    支付宝
                    <?php  } else { ?>
                    到店付款
                    <?php  } ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">下单时间</label>
                <div class="col-sm-9 col-xs-12 form-control-static">
                    <?php  echo $time;?>
                </div>
            </div>

			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户类型</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
                <?php  if($member_info['isauto'] == 1) { ?>
                微信用户
                <?php  } else { ?>
                会员用户
                <?php  } ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">操作</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<select name="status" class="form-control" id="status">
						<option value="-1" <?php  if($orderList['status'] == -1 ) { ?> selected="selected"<?php  } ?>>订单取消</option>
						<option value="1" <?php  if($orderList['status'] == 1 ) { ?> selected="selected"<?php  } ?>>订单确认</option>
						<option value="2" <?php  if($orderList['status'] == 2 ) { ?> selected="selected"<?php  } ?>>
						<?php  if($orderList['paytype']==0) { ?>订单拒绝<?php  } else { ?>订单退款<?php  } ?></option>
						<option value="3" <?php  if($orderList['status'] == 3 ) { ?> selected="selected"<?php  } ?>>订单完成</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">详情</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<textarea style="height:100px;" class="form-control richtext-clone" name="msg" cols="70" id="reply-add-text" disabled><?php  echo $orderList['detail'];?></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group col-sm-12">
		<input type="hidden" name="old_status" value="<?php  echo $item['status'];?>" />
		<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
	</div>
</form>

<script language="JavaScript">
	function formcheck(){
		var ok = true;
		return ok;
	}
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
