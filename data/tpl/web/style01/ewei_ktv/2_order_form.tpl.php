<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<div class="main">
<ul class="nav nav-tabs">
	<li><a href="<?php  echo $this->createWebUrl('order',array('op'=>'list','ktvid'=>$ktvid,'roomidid'=>$roomidid));?>">订单管理</a></li>
	<li class="active"><a href="<?php  echo $this->createWebUrl('order',array('op'=>'edit','id'=>$id));?>">订单处理</a></li>
</ul>
<form action="" class="form-horizontal form" method="post" enctype="multipart/form-data" onsubmit="return formcheck();">
	<input type="hidden" name="id" value="<?php  echo $item['id'];?>">
	<input type="hidden" name="oldstatus" id="oldstatus" value="<?php  echo $item['status'];?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			订单处理
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">ktv名称</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo $ktv['title'];?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">房间名称</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo $room['title'];?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">房间名称</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					 <?php  echo $room['title'];?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">房量/房态</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<select multiple="true" name="room_list" id="room_list" style="height:200px" class='form-control'>
						<?php  if(is_array($date_array)) { foreach($date_array as $row) { ?>
						<option value="<?php  echo $row['month'];?>-<?php  echo $row['day'];?>|<?php  echo $list[$row['time']]['status'];?>|<?php  echo $list[$row['time']]['num'];?>">
							<?php  echo $row['month'];?>-<?php  echo $row['day'];?> --
							<?php  if($list[$row['time']]['status'] == 1 && ($list[$row['time']]['num'] > 0 || $list[$row['time']]['num'] == '不限')) { ?>
							有房
							--
							<?php  echo $list[$row['time']]['num'];?>
							<?php  } else { ?>
							无房
							<?php  } ?>
						</option>
						<?php  } } ?>
					</select>
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
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">预定人</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo $item['name'];?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">联系人</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo $item['contact_name'];?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">手机</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo $item['mobile'];?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">到店时间</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo date("Y-m-d",$item['btime']); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">离店时间</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo date("Y-m-d",$item['etime']); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">住几晚</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo $item['day'];?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">预定数量</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo $item['nums'];?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">单价</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
                <?php  if($member_info['isauto'] == 1) { ?>
                <?php  echo $item['cprice'];?>(优惠价)
                <?php  } else { ?>
                <?php  echo $item['mprice'];?>(会员价)
                <?php  } ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">支付方式</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
                <?php  if($item['paytype']==1) { ?>
                余额支付
                <?php  } else if($item['paytype']==21) { ?>
                微支付
                <?php  } else if($item['paytype']==22) { ?>
                支付宝
                <?php  } else { ?>
                到店付款
                <?php  } ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">订单时间</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<?php  echo date("Y-m-d H:i:s",$item['time']); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">操作</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<select name="status" class="form-control" id="status">
						<option value="-1" <?php  if($item['status'] == -1 ) { ?> selected="selected"<?php  } ?>>订单取消</option>
						<option value="1" <?php  if($item['status'] == 1 ) { ?> selected="selected"<?php  } ?>>订单确认</option>
						<option value="2" <?php  if($item['status'] == 2 ) { ?> selected="selected"<?php  } ?>>
						<?php  if($item['paytype']==0) { ?>订单拒绝<?php  } else { ?>订单退款<?php  } ?></option>
						<option value="3" <?php  if($item['status'] == 3 ) { ?> selected="selected"<?php  } ?>>订单完成</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">备注</label>
				<div class="col-sm-9 col-xs-12 form-control-static">
					<textarea style="height:100px;" class="form-control richtext-clone" name="msg" cols="70" id="reply-add-text"><?php  echo $item['msg'];?></textarea>
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
		if($("#status").val()==1 && $("#oldstatus").val()!=1){
			$("#room_list option").each(function(){
				var val = $(this).val().split("|");
				if(val[1] == 0){
					alert(val[0] + "无房");
					ok =false;
					return false;
				} else {
					if (val[2] != '不限') {
						if(parseInt(val[2]) == 0) {
							alert(val[0] + "没有空房间");
							ok =false;
							return false;
						}
						if(parseInt(val[2]) > 0 && parseInt(val[2]) < <?php  echo $item['nums'];?>) {
							alert(val[0] + "房间数量不够");
							ok =false;
							return false;
						}
					}
				}
			});
		}
		return ok;
	}
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
