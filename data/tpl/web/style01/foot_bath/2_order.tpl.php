<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<script language='javascript' src='<?php  echo $this->_script_url?>jquery.gcjs.js'></script>
<script language='javascript' src='<?php  echo $this->_script_url?>jquery.form.js'></script>
<script language='javascript' src='<?php  echo $this->_script_url?>tooltipbox.js'></script>
<div class="main">
	<ul class="nav nav-tabs">
		<li class="active"><a href="<?php  echo $this->createWebUrl('order',array('op'=>'list'));?>">订单管理</a></li>
	</ul>
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
				<input type="hidden" name="c" value="site" />
				<input type="hidden" name="a" value="entry" />
				<input type="hidden" name="m" value="ewei_hotel" />
				<input type="hidden" name="do" value="order" />
				<input type="hidden" name="hotelid" value="<?php  echo $hotel['id'];?>" />
				<input type="hidden" name="roomid" value="<?php  echo $room['id'];?>" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">套餐</label>
					<div class="col-sm-4 col-xs-12">
						<input class="form-control" name="hoteltitle"  type="text" value="<?php  if(!empty($hotel)) { ?><?php  echo $hotel['title'];?><?php  } else { ?><?php  echo $_GPC['hoteltitle'];?><?php  } ?>" placeholder="套餐名称">
					</div>
					<div class="col-sm-4 col-xs-12">
						<input class="form-control" name="roomtitle"  type="text" value="<?php  if(!empty($room)) { ?><?php  echo $room['title'];?><?php  } else { ?><?php  echo $_GPC['roomtitle'];?><?php  } ?>" placeholder="房型名称">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户</label>
					<div class="col-sm-4 col-xs-12">
						<input class="form-control" name="realname" id="" type="text" value="<?php  echo $_GPC['realname'];?>" placeholder="姓名">
					</div>
					<div class="col-sm-4 col-xs-12">
						<input class="form-control" name="mobile" id="" type="text" value="<?php  echo $_GPC['mobile'];?>" placeholder="手机号">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">订单</label>
					<div class="col-sm-3 col-xs-12">
						<input class="form-control" name="ordersn" id="" type="text" value="<?php  echo $_GPC['ordersn'];?>" placeholder="订单编号">
					</div>
					<div class="col-sm-3 col-xs-12">
						<select name="status" class="form-control">
							<option value="" <?php  if($_GPC['status']=='') { ?> selected="selected"<?php  } ?>>订单状态</option>
							<option value="0" <?php  if($_GPC['status']=='0') { ?> selected="selected"<?php  } ?>>等待确认</option>
							<option value="-1" <?php  if($_GPC['status'] == -1 ) { ?> selected="selected"<?php  } ?>>订单取消/退款</option>
							<option value="1" <?php  if($_GPC['status'] == 1 ) { ?> selected="selected"<?php  } ?>>订单确认</option>
							<option value="2" <?php  if($_GPC['status'] == 2 ) { ?> selected="selected"<?php  } ?>>订单拒绝/退款</option>
							<option value="3" <?php  if($_GPC['status'] == 3 ) { ?> selected="selected"<?php  } ?>>订单完成</option>
						</select>
					</div>
					<div class="col-sm-3 col-xs-12">
						<select name="paystatus" class="form-control">
							<option value="" <?php  if($_GPC['paystatus'] == '') { ?> selected="selected"<?php  } ?>>支付状态</option>
							<option value="0" <?php  if($_GPC['paystatus'] == '0') { ?> selected="selected"<?php  } ?>>未支付</option>
							<option value="1" <?php  if($_GPC['paystatus'] == '1') { ?> selected="selected"<?php  } ?>>已支付</option>
						</select>
					</div>
					<div class=" col-xs-12 col-sm-2 col-lg-2">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
				<div class="form-group">
				</div>
			</form>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
			<tr>
				<th class='with-checkbox' style='width:30px;'>
					<input type="checkbox" class="check_all" />
				</th>
				<th style="width:120px;">订单编号</th>
				<th style="width:120px;">套餐</th>
				<th style="width:120px;">技师</th>
				<th style="width:120px;">预订人名字</th>
				<th style="width:120px;">预定人/手机</th>
				<th style="width:120px;">预定时间</th>
				<th style="width:100px;">总价</th>
				<th style="width:120px;">支付方式<i></i></th>
				<th style="width:120px;">下单时间<i></i></th>
				<th style="width:120px;">订单状态<i></i></th>
				<th style="width:100px;">操作</th>
			</tr>
			</thead>
			<tbody>
			<?php  if(is_array($list)) { foreach($list as $row) { ?>
			<tr>
				<td class="with-checkbox">
					<input type="checkbox" name="check" value="<?php  echo $row['id'];?>"></td>
				<td><?php  echo $row['ordersn'];?></td>
				<td><?php  echo $row['p_name'];?></td>
				<td><?php  echo $row['t_name'];?></td>
				<td><?php  echo $row['contact_name'];?></td>
                <td><?php  echo $row['mobile'];?></td>
				<td><?php  echo date('Y-m-d',$row['btime'])?></td>
				<td><?php  echo $row['price'];?></td>
				<td ><?php  if($row['paytype']==1) { ?>
					余额支付
					<?php  } else if($row['paytype']==21) { ?>
					微支付
					<?php  } else if($row['paytype']==22) { ?>
					支付宝
					<?php  } else { ?>
					到店付款
					<?php  } ?>
				</td>
				<td ><?php  echo date("Y-m-d H:i:s",$row['time']); ?></td>
				<td>
					<?php  if($row['paystatus']==0) { ?>
					<?php  if($row['status'] == 0) { ?><span class="label label-info"><?php  if($row['paytype']==1 || $row['paytype']==2) { ?>待付款<?php  } else { ?>等待确认<?php  } ?></span><?php  } ?>
					<?php  if($row['status'] == -1) { ?><span class="label label-default">已取消</span><?php  } ?>
					<?php  if($row['status'] == 1) { ?><span class="label label-success">已接受</span><?php  } ?>
					<?php  if($row['status'] == 2) { ?><span class="label label-default">已拒绝</span><?php  } ?>
					<?php  if($row['status'] == 3) { ?><span class="label label-success">订单完成</span><?php  } ?>
					<?php  } else { ?>
					<?php  if($row['status'] == 0) { ?><span class="label label-info">已支付,等待确认</span><?php  } ?>
					<?php  if($row['status'] == -1) { ?><span class="label label-default">已支付,取消并退款</span><?php  } ?>
					<?php  if($row['status'] == 1) { ?><span class="label label-success">已支付,已确认</span><?php  } ?>
					<?php  if($row['status'] == 2) { ?><span class="label label-default">已支付, 已退款</span><?php  } ?>
					<?php  if($row['status'] == 3) { ?><span class="label label-success">订单完成</span><?php  } ?>
					<?php  } ?>
				</td>
				<td>
					<a href="<?php  echo $this->createWebUrl('order', array('op'=>'edit', 'id' => $row['id'])); ?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="编辑"><i class="fa fa-edit"></i></a>&nbsp;
                    <a onclick="return confirm('此操作不可恢复，确认吗？');return false;" href="<?php  echo $this->createWebUrl('order', array('op'=>'delete', 'id' => $row['id'],'hotelid'=>$hotelid,'roomidid'=>$roomidid))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="删除"><i class="fa fa-times"></i></a>
				</td>
			</tr>
			<?php  } } ?>
			<tr>
				<td colspan="12">
					<input type="button" class="btn btn-primary" name="deleteall" value="删除选择的" />
				</td>
			</tr>
			</tbody>
			<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
		</table>
	</div>
	</div>
	<?php  echo $pager;?>
</div>
<script>
	require(['bootstrap'],function($){
		$('.btn').tooltip();
	});
</script>
<script>
	$(function(){

		$(".check_all").click(function(){
			var checked = $(this).get(0).checked;
			$(':checkbox').each(function(){this.checked = checked});
		});
		$("input[name=deleteall]").click(function(){

			var check = $("input:checked");
			if(check.length<1){
				alert('请选择要删除的记录!');
				return false;
			}
			if( confirm("确认要删除选择的记录?")){
				var id = new Array();
				check.each(function(i){
					id[i] = $(this).val();
				});

				$.post("<?php  echo $this->createWebUrl('order',array('op'=>'deleteall'))?>", {idArr:id},function(data){
					if (data.errno ==0)
					{
						location.reload();
					} else {
						alert(data.error);
					}
				},'json');
			}

		});
	});
</script>
<script>
	function drop_confirm(msg, url){
		if(confirm(msg)){
			window.location = url;
		}
	}
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
