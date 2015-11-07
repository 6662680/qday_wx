<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('wechat/nav', TEMPLATE_INCLUDEPATH)) : (include template('wechat/nav', TEMPLATE_INCLUDEPATH));?>
<div class="clearfix">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
				<input type="hidden" name="c" value="wechat">
				<input type="hidden" name="a" value="card">
				<input type="hidden" name="do" value="display"/>
				<input type="hidden" name="type" value="<?php  echo $_GPC['type'];?>"/>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">类型</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<a class="btn btn-default <?php  if($_GPC['type'] == '') { ?>btn-primary<?php  } ?>" href="<?php  echo url('wechat/card/display', array('type' => ''))?>">不限</a>
						<a class="btn btn-default <?php  if($_GPC['type'] == 'discount') { ?>btn-primary<?php  } ?>" href="<?php  echo url('wechat/card/display', array('type' => 'discount'))?>">折扣券</a>
						<a class="btn btn-default <?php  if($_GPC['type'] == 'cash') { ?>btn-primary<?php  } ?>" href="<?php  echo url('wechat/card/display', array('type' => 'cash'))?>">代金券</a>
						<a class="btn btn-default <?php  if($_GPC['type'] == 'gift') { ?>btn-primary<?php  } ?>" href="<?php  echo url('wechat/card/display', array('type' => 'gift'))?>">礼品券</a>
						<a class="btn btn-default <?php  if($_GPC['type'] == 'groupon') { ?>btn-primary<?php  } ?>" href="<?php  echo url('wechat/card/display', array('type' => 'groupon'))?>">团购券</a>
						<a class="btn btn-default <?php  if($_GPC['type'] == 'general_coupon') { ?>btn-primary<?php  } ?>" href="<?php  echo url('wechat/card/display', array('type' => 'general_coupon'))?>">优惠券</a>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">审核状态</label>
					<div class="col-sm-9 col-xs-9 col-md-9">
						<label class="radio-inline"><input type="radio" name="status" value="" <?php  if($_GPC['status'] == '') { ?>checked<?php  } ?>/> 不限</label>
						<label class="radio-inline"><input type="radio" name="status" value="1" <?php  if($_GPC['status'] == '1') { ?>checked<?php  } ?>/> 审核中</label>
						<label class="radio-inline"><input type="radio" name="status" value="2" <?php  if($_GPC['status'] == '2') { ?>checked<?php  } ?>/> 未通过</label>
						<label class="radio-inline"><input type="radio" name="status" value="3" <?php  if($_GPC['status'] == '3') { ?>checked<?php  } ?>/> 已通过</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">券标题</label>
					<div class="col-sm-7 col-lg-8 col-md-8 col-xs-12">
						<input class="form-control" name="title" placeholder="券标题" type="text" value="<?php  echo $_GPC['title'];?>">
					</div>
					<div class="col-xs-12 col-sm-3 col-md-2 col-lg-1">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<form class="form-horizontal" action="" method="post" onkeydown="if(event.keyCode==13){return false;}">
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th width="100">卡券类型</th>
						<th width="150">卡券名称</th>
						<th width="200">卡券有效期</th>
						<th width="90">状态</th>
						<th width="90">库存</th>
						<th width="100">每人领取限制</th>
						<th width="100">上架状态</th>
						<th style="width:240px; text-align:right;">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($data)) { foreach($data as $dca) { ?>
					<tr>
						<td><?php  echo $types[$dca['type']];?></td>
						<td><?php  echo $dca['title'];?></td>
						<td>
							<?php  if($dca['date_info']['time_type'] == 1) { ?>
								<?php  echo $dca['date_info']['time_limit_start'];?>~<?php  echo $dca['date_info']['time_limit_end'];?>
							<?php  } else { ?>
								领取后<?php  echo $dca['date_info']['deadline'];?>天后生效,<?php  echo $dca['date_info']['limit'];?>天有效期
							<?php  } ?>
						</td>
						<td>
							<?php  if($dca['status'] == '1') { ?>
							<span class="label label-info">审核中</span>
							<?php  } else if($dca['status'] == '2') { ?>
							<span class="label label-danger">未通过</span>
							<?php  } else if($dca['status'] == '3') { ?>
							<span class="label label-success">已通过</span>
							<?php  } ?>
						</td>
						<td><input type="text" value="<?php  echo $dca['quantity'];?>" class="form-control modifystock" data-id="<?php  echo $dca['id'];?>" data-old="<?php  echo $dca['quantity'];?>" style="width:80px"/></td>
						<td><?php  echo $dca['get_limit'];?></td>
						<td>
							<?php  if($dca['is_display'] == 1) { ?>
								<span class="label label-success">上架中</span>
							<?php  } else { ?>
								<span class="label label-danger">已下架</span>
							<?php  } ?>
						</td>
						<td style="text-align:right;">
<!--
							<a href="<?php  echo url('wechat/card/', array('do' => $dca['type'], 'op' => 'post', 'id' => $dca['id']))?>" class="btn btn-default btn-sm" title="编辑" data-toggle="tooltip" data-placement="top"><i class="fa fa-edit"></i></a>
-->
							<a href="javascript:;" data-cid="<?php  echo $dca['id'];?>" class="btn btn-default btn-sm toggle-display" title="上架/下架" data-toggle="tooltip" data-placement="top"><?php  if($dca['is_display'] == 1) { ?><i class="fa fa-stop"></i><?php  } else { ?><i class="fa fa-play"></i><?php  } ?></a>
							<a href="<?php  echo url('wechat/card/qr', array('cid' => $dca['id'], 'op' => 'list'))?>" class="btn btn-default btn-sm" title="生成投放二维码" data-toggle="tooltip" data-placement="top"><i class="fa fa-qrcode"></i></a>
							<a href="<?php  echo url('wechat/card/record', array('card_id' => $dca['card_id'], 'op' => 'list'))?>" class="btn btn-default btn-sm" title="领取记录" data-toggle="tooltip" data-placement="top"><i class="fa fa-bar-chart"></i></a>
							<a href="<?php  echo url('wechat/card/', array('do' => $dca['type'], 'f' => 'post', 'id' => $dca['id']))?>" class="btn btn-success btn-sm" title="查看详情" data-toggle="tooltip" data-placement="top">查看详情</a>
							<a href="<?php  echo url('wechat/card/del', array('id' => $dca['id']))?>" class="btn btn-default btn-sm" title="删除" data-toggle="tooltip" data-placement="top" onclick="if(!confirm('删除后将不可恢复，确定删除吗?')) return false;"><i class="fa fa-times"> </i></a>
						</td>
					</tr>
					<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php  echo $pager;?>
	</form>
</div>



<script>
	require(['bootstrap', 'util'],function($, u){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
		$('.modifystock').blur(function(){
			var old_val = parseInt($(this).attr('data-old'));
			var new_val = parseInt($(this).val());
			if(old_val == new_val) return false;
			var id = parseInt($(this).attr('data-id'));
			if(id) {
				$.post("<?php  echo url('wechat/card/modifystock')?>", {'id' : id, 'num' : new_val}, function(dat){
					var data = $.parseJSON(dat);
					if(!data.erron) {
						location.reload();
						return false;
					} else {
						u.message(data.error, '', 'error');
					}
				});
			}
		});
		$('.modifystock').keyup(function(event){
			if(event.keyCode == 13) {
				$(this).blur();return false;
			}
		});

		$('.toggle-display').click(function(){
			var id = parseInt($(this).attr('data-cid'));
			if(id <= 0) return false;
			$.post("<?php  echo url('wechat/card/toggle', array('op' => 'is_display'))?>", {'id':id}, function(data){
				if(data == 'success') {
					location.reload();
				} else {
					u.message(data, '', 'error');
				}
				return false;
			});
			return false;
		});
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>