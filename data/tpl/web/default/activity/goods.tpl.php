<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($do == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo url('activity/goods/display', array());?>">管理实物兑换</a></li>
	<li <?php  if($do == 'post' && !$id) { ?>class="active"<?php  } ?>><a href="<?php  echo url('activity/goods/post', array());?>">添加实物兑换</a></li>
	<?php  if($do == 'post' && $id) { ?><li class="active"><a href="<?php  echo url('activity/goods/post', array('id' => $id));?>">编辑实物兑换</a></li><?php  } ?>
	<li <?php  if($do == 'record') { ?>class="active"<?php  } ?>><a href="<?php  echo url('activity/goods/record');?>">实物兑换记录</a></li>
	<li <?php  if($do == 'deliver') { ?>class="active"<?php  } ?>><a href="<?php  echo url('activity/goods/deliver');?>">发货记录</a></li>
	<?php  if($do == 'receiver' && $id) { ?><li class="active"><a href="<?php  echo url('activity/goods/receiver', array('id' => $id));?>">编辑收货人信息</a></li><?php  } ?>
</ul>
<script>
	require(['bootstrap'],function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});
</script>
<?php  if($do == 'post') { ?>
<style>
	.text-danger{color:red;}
</style>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
		<div class="panel panel-default">
			<div class="panel-heading">
				兑换真实物品
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 兑换名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="title" class="form-control" value="<?php  echo $item['title'];?>" />
						<span class="help-block">此设置项为当前礼品兑换设置一个名称。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 兑换内容</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="extra[title]" class="form-control" value="<?php  echo $item['extra']['title'];?>" />
						<span class="help-block">此设置项设置当前礼品兑换的礼品名称。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 积分类型</label>
					<div class="col-sm-9 col-xs-12">
						<select name="credittype" class="form-control">
							<?php  if(is_array($creditnames)) { foreach($creditnames as $key => $credit) { ?>
							<option value="<?php  echo $key;?>" <?php  if($key == $item['credittype']) { ?>selected<?php  } ?>><?php  echo $credit;?></option>
							<?php  } } ?>
						</select>
						<span class="help-block">此设置项设置当前礼品兑换需要消耗的积分类型,如:金币、积分、贡献等。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">积分数量</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="credit" class="form-control" value="<?php  echo $item['credit'];?>" />
						<span class="help-block">此设置项设置当前礼品兑换需要消耗的积分数量。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">使用期限</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_daterange('datelimit', array('start' => date('Y-m-d', $item['starttime']),'end' => date('Y-m-d', $item['endtime'])), '')?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 每人最大兑换次数</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="pretotal" class="form-control" value="<?php  echo $item['pretotal'];?>" />
						<span class="help-block">此设置项设置每个用户最大兑换次数。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 兑换总数</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="total" class="form-control" value="<?php  echo $item['total'];?>" />
						<span class="help-block">此设置项设置兑换总量。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 封面</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_image('thumb', $item['thumb'])?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 说明</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_ueditor('description', $item['description'])?>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input name="id" type="hidden" value="<?php  echo $item['id'];?>">
			<input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<?php  } else if($do == 'display') { ?>
<div class="main">
	<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form">
		<input type="hidden" name="c" value="activity" />
		<input type="hidden" name="a" value="goods" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">兑换名称</label>
				<div class="col-sm-7 col-lg-9 col-xs-12">
					<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
				</div>
				<div class="pull-right col-xs-12 col-sm-3 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="panel panel-default">
	<div class="table-responsive panel-body">
		<table class="table table-hover">
			<thead>
				<tr>
					<th style="width:50px">图标</th>
					<th style="width:100px;">标题</th>
					<th style="width:80px;">领取条件</th>
					<th style="width:90px;">可兑换次数</th>
					<th style="width:80px;">已兑换</th>
					<th style="width:80px;">总量</th>
					<th style="width:150px;">有效时间</th>
					<th style="text-align:right; width:120px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td><img width="40" src="<?php  echo $item['thumb'];?>"></td>
					<td><?php  echo $item['title'];?></td>
					<td><?php  echo $item['credit'];?> <?php  echo $creditnames[$item['credittype']];?></td>
					<td><?php  echo $item['pretotal'];?> 次</td>
					<td><?php  echo $item['num'];?> 个</td>
					<td><?php  echo $item['total'];?> 个</td>
					<td><?php  echo date('Y-m-d', $item['starttime'])?> - <?php  echo date('Y-m-d', $item['endtime'])?></td>
					<td style="text-align:right;">
						<a href="<?php  echo url('activity/goods/post', array('id' => $item['id']))?>" data-toggle="tooltip" data-placement="top" title="编辑" class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a>
						<a href="<?php  echo url('activity/goods/del', array('id' => $item['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" data-toggle="tooltip" data-placement="top" title="删除" class="btn btn-default btn-sm"><i class="fa fa-times"></i></a>
						<a href="<?php  echo url('activity/goods/record', array('exid' => $item['id']))?>" data-toggle="tooltip" data-placement="top" title="兑换记录" class="btn btn-default btn-sm"><i class="fa fa-clock-o"></i></a>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
		</div>
	</div>
	<?php  echo $pager;?>
<script>
	require(['bootstrap'],function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});
</script>
</div>
<?php  } else if($do == 'record') { ?>
<div class="main">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="activity">
			<input type="hidden" name="a" value="goods">
			<input type="hidden" name="do" value="record">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">兑换标题</label>
					<div class="col-sm-6 col-lg-8 col-xs-12">
						<select class="form-control" name="exid">
							<?php  if(is_array($exchanges)) { foreach($exchanges as $exchange) { ?>
								<option value="<?php  echo $exchange['id'];?>" <?php  if($_GPC['exid'] == $exchange['id']) { ?>selected<?php  } ?>><?php  echo $exchange['title'];?></option>
							<?php  } } ?>
						</select>	
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户UID</label>
					<div class="col-sm-6 col-lg-8 col-xs-12">
						<input class="form-control" name="uid" id="" type="text" value="<?php  echo $_GPC['uid'];?>">	
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">兑奖日期</label>
					<div class="col-sm-6 col-lg-8 col-xs-12">
						<?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
					</div>
					<div class="pull-right col-xs-12 col-sm-3 col-lg-2">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="panel panel-default">
	<div class="table-responsive panel-body">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:80px; text-align:center;">用户ID</th>
					<th style="width:80px; text-align:center;">标题</th>
					<th style="width:150px; text-align:center;">图标</th>
					<th style="width:150px; text-align:center;">兑换物品</th>
					<th style="width:150px; text-align:center;">兑换时间</th>
					<th style="width:120px; text-align:center;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td class="text-center"><?php  echo $item['uid'];?></td>
					<td class="text-center"><?php  echo $item['title'];?></td>
					<td class="text-center"><img src="<?php  echo $item['thumb'];?>" style="max-width:50px; max-height: 30px;"></td>
					<td class="text-center"><?php  echo $item['extra']['title'];?></td>
					<td class="text-center"><?php  echo date('Y-m-d H:i', $item['createtime'])?></td>
					<td class="text-center">
						<a onclick="if(!confirm('删除后不可恢复,您确定删除吗?')) return false;"  href="<?php  echo url('activity/goods/record-del', array('id' => $item['tid']))?>" class="btn btn-default btn-sm" title="删除兑换记录"><i class="fa fa-times"></i></a>
						<a href="<?php  echo url('activity/goods/receiver', array('id' => $item['tid']))?>" class="btn btn-default btn-sm" title="收货人信息"><i class="fa fa-truck"></i></a>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
	</div>
	<?php  echo $pager;?>
</div>
<?php  } else if($do == 'deliver') { ?>
<div class="main">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="activity">
			<input type="hidden" name="a" value="goods">
			<input type="hidden" name="do" value="deliver">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">兑换标题</label>
					<div class="col-sm-6 col-lg-8 col-xs-12">
						<select class="form-control" name="exid">
							<?php  if(is_array($exchanges)) { foreach($exchanges as $exchange) { ?>
								<option value="<?php  echo $exchange['id'];?>" <?php  if($_GPC['exid'] == $exchange['id']) { ?>selected<?php  } ?>><?php  echo $exchange['title'];?></option>
							<?php  } } ?>
						</select>	
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户UID</label>
					<div class="col-sm-6 col-lg-8 col-xs-12">
						<input class="form-control" name="uid" id="" type="text" value="<?php  echo $_GPC['uid'];?>">		
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">兑奖日期</label>
					<div class="col-sm-6 col-lg-8 col-xs-12">
						<?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
					</div>
					<div class="pull-right col-xs-12 col-sm-3 col-lg-2">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="panel panel-default">
	<div class="table-responsive panel-body">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:60px;">用户ID</th>
					<th style="width:80px;">标题</th>
					<th style="width:100px;">兑换物品</th>
					<th style="width:100px;">收件人</th>
					<th style="width:100px;">收件人电话</th>
					<th style="width:100px;">收件人邮编</th>
					<th style="width:150px;">收件地址</th>
					<th style="width:80px;">状态</th>
					<th style="text-align:center;width:80px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td><?php  echo $item['uid'];?></td>
					<td><?php  echo $item['title'];?></td>
					<td><?php  echo $item['extra']['title'];?></td>
					<td><?php  echo $item['name'];?></td>
					<td><?php  echo $item['mobile'];?></td>
					<td><?php  echo $item['zipcode'];?></td>
					<td><?php  echo $item['province'];?> <?php  echo $item['city'];?> <?php  echo $item['district'];?> <?php  echo $item['address'];?></td>
					<td>
						<?php  if($item['status'] == 0) { ?>
							<span class="label label-danger">待发货</span>
						<?php  } else if($item['status'] == 1) { ?>
							<span class="label label-warning">已发货</span>
						<?php  } else if($item['status'] == 2) { ?>
							<span class="label label-success">已收货</span>	
						<?php  } else if($item['status'] == -1) { ?>
							<span class="label label-default">已关闭</span>	
						<?php  } ?>
					</td>
					<td style="text-align:center;">
						<a href="<?php  echo url('activity/goods/receiver',array('id'=>$item['tid']));?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-edit"></i></a>
						<!--  <a onclick="return confirm('确定要删除当前物品吗？');" href="<?php  echo url('activity/exchange/shipping',array('op'=>'delete','id'=>$item['id']));?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="删除"><i class="fa fa-times"></i></a>-->
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
	</div>
	<?php  echo $pager;?>
</div>
<?php  } else if($do == 'receiver') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form">
		<div class="panel panel-default">
			<div class="panel-heading">
				收货人信息
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">收货人姓名</label>
					<div class="col-sm-9">
						<input type="text" name="realname" class="form-control" value="<?php  echo $shipping['name'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">收货人电话</label>
					<div class="col-sm-9">
						<input type="text" name="mobile" class="form-control" value="<?php  echo $shipping['mobile'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">邮寄地址</label>
					<div class="col-sm-9">
						<?php  echo tpl_fans_form('reside', array('province' => $shipping['province'], 'city' => $shipping['city'], 'district' => $shipping['district']));?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">收货人邮编</label>
					<div class="col-sm-9">
						<input type="text" name="zipcode" class="form-control" value="<?php  echo $shipping['zipcode'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">收件地址</label>
					<div class="col-sm-9">
						<input type="text" name="address" class="form-control" value="<?php  echo $shipping['address'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
					<div class="col-sm-9">
						<label class="radio-inline"><input type="radio" value="0" <?php  if($shipping['status'] == 0) { ?>checked<?php  } ?> name="status">待发货</label>
						<label class="radio-inline"><input type="radio" value="1" <?php  if($shipping['status'] == 1) { ?>checked<?php  } ?> name="status">已发货</label>
						<label class="radio-inline"><input type="radio" value="2" <?php  if($shipping['status'] == 2) { ?>checked<?php  } ?> name="status">已收货</label>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<input name="id" type="hidden" value="<?php  echo $id;?>">
				<input name="submit" type="submit" value="保存" class="btn btn-primary">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>