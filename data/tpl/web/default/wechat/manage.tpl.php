<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('wechat/nav', TEMPLATE_INCLUDEPATH)) : (include template('wechat/nav', TEMPLATE_INCLUDEPATH));?>
<script>
	require(['bootstrap'],function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});
</script>
<style>
	.text-danger{color:red;}
</style>
<?php  if($do == 'logo') { ?>
	<div class="alert alert-info">
		<i class="fa fa-info-circle"> 微信卡券功能需要您的公众号为认证订阅号或认证服务号，并且在微信公众平台开通了“卡券功能插件”</i>
	</div>
	<div class="clearfix">
		<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
			<div class="panel panel-default" id="step1">
				<div class="panel-heading">
					商户LOGO
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 上传商户LOGO</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_wechat_image('logo', $coupon_setting['logourl'], '', array('acid' => $acid, 'mode' => 'file_upload'));?>
							<div class="help-block">商户LOGO大小不超过1M。像素为：300*300。仅支持JPG格式</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group col-sm-12">
				<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</form>
	</div>
</div>
<?php  } else if($do == 'location_post') { ?>
		<div class="clearfix">
		<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
			<div class="panel panel-default" id="step1">
				<div class="panel-heading">
					门店信息
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 门店名</label>
						<div class="col-sm-8 col-xs-12">
							<input type="text" class="form-control" name="business_name" value="<?php  echo $item['business_name'];?>"/>
							<span class="help-block">门店名不得含有区域地址信息（如，北京市XXX公司）</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 分店名(选填)</label>
						<div class="col-sm-8 col-xs-12">
							<input type="text" class="form-control" name="branch_name" value="<?php  echo $item['branch_name'];?>"/>
							<span class="help-block">分店名不得含有区域地址信息（如，“北京国贸店”中的“北京”）</span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 类目</label>
						<div class="col-sm-8 col-xs-12">
							<?php  echo tpl_form_field_location_category("class");?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> </label>
						<div class="col-sm-8 col-xs-12">
							<span class="help-block">请选择门店类目。门店类目必须合法有效。</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 地址</label>
						<div class="col-sm-8 col-xs-12">
							<?php  echo tpl_fans_form('reside',array('province' => $item['province'],'city' => $item['city'],'district' => $item['district']));?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 详细地址</label>
						<div class="col-sm-8 col-xs-12">
							<input type="text" name="address" id="addresss" class="form-control" placeholder="输入详细地址，请勿重复填写省市区信息" value="<?php  echo $item['address'];?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 定位</label>
						<div class="col-sm-8 col-xs-12" id="map">
							<?php  echo tpl_form_field_coordinate('baidumap', $item['baidumap'])?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 电话</label>
						<div class="col-sm-8 col-xs-12">
							<input type="text" class="form-control" name="telephone" value="<?php  echo $item['telephone'];?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 图片列表</label>
						<div class="col-sm-8 col-xs-12">
							<?php  echo tpl_form_field_wechat_multi_image('photo_list', '', '', array('mode' => 'file_upload', 'acid' => $acid));?>
							<span class="help-block">图片只支持jpg格式,大小不超过为1M</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> 人均价格</label>
						<div class="col-sm-8 col-xs-12">
							<input type="text" name="avg_price" class="form-control"/>
							<span class="help-block">人均价格，大于0的整数,单位为人民币（元）</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 营业时间</label>
						<div class="col-sm-9 col-xs-4 col-md-3">
							<div class="input-group">
								<input type="text" class="form-control" placeholder="8:00" name="open_time_start" value="8:00">
								<span class="input-group-addon" id="basic-addon2">-</span>
								<input type="text" class="form-control" placeholder="24:00" name="open_time_end" value="24:00">
							</div>
							<span class="help-block">营业时间，24小时制表示，如 8:00-20:00</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> 推荐</label>
						<div class="col-sm-8 col-xs-12">
							<textarea name="recommend" class="form-control" cols="30" rows="3"></textarea>
							<span class="help-block">推荐品，餐厅可为推荐菜；酒店为推荐套房；景点为 推荐游玩景点等，针对自己行业的推荐内容</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 特色服务</label>
						<div class="col-sm-8 col-xs-12">
							<textarea name="special" class="form-control" cols="30" rows="3"></textarea>
							<span class="help-block">特色服务，如免费wifi，免费停车，送货上门等商户 能提供的特色功能或服务</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> 简介</label>
						<div class="col-sm-8 col-xs-12">
							<textarea name="introduction" class="form-control" cols="30" rows="3"></textarea>
							<span class="help-block">商户简介，主要介绍商户信息等 </span>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group col-sm-12">
				<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</form>
	</div>
<?php  } ?>
<?php  if($do == 'location_edit') { ?>
<div class="clearfix">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
		<div class="panel panel-default" id="step1">
			<div class="panel-heading">
				基本信息 <small class="text-danger">不可修改</small>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 门店名</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" name="business_name" value="<?php  echo $location['business_name'];?>" disabled/>
						<span class="help-block">门店名不得含有区域地址信息（如，北京市XXX公司）</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 分店名(选填)</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" name="branch_name" value="<?php  echo $location['branch_name'];?>" disabled/>
						<span class="help-block">分店名不得含有区域地址信息（如，“北京国贸店”中的“北京”）</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 类目</label>
					<div class="col-sm-8 col-xs-12">
						<?php  echo tpl_form_field_location_category("class", $location['category']);?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 地址</label>
					<div class="col-sm-8 col-xs-12">
						<?php  echo tpl_fans_form('reside',array('province' => $location['province'],'city' => $location['city'],'district' => $location['district']));?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 详细地址</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" name="address" id="addresss" class="form-control" disabled placeholder="输入详细地址，请勿重复填写省市区信息" value="<?php  echo $location['address'];?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 定位</label>
					<div class="col-sm-8 col-xs-12" id="map">
						<?php  echo tpl_form_field_coordinate('baidumap', $location['baidumap'])?>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default" id="step1">
			<div class="panel-heading">
				服务信息 <small class="text-danger">可修改</small>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 电话</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" name="telephone" value="<?php  echo $location['telephone'];?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 图片列表</label>
					<div class="col-sm-8 col-xs-12">
						<?php  echo tpl_form_field_wechat_multi_image('photo_list', $location['photo_list'], '', array('mode' => 'file_upload', 'acid' => $acid));?>
						<span class="help-block">图片只支持jpg格式,大小不超过为1M</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 营业时间</label>
					<div class="col-sm-9 col-xs-4 col-md-3">
						<div class="input-group">
							<input type="text" class="form-control" placeholder="8:00" name="open_time_start" value="<?php  echo $location['open_time_start'];?>">
							<span class="input-group-addon" id="basic-addon2">-</span>
							<input type="text" class="form-control" placeholder="24:00" name="open_time_end" value="<?php  echo $location['open_time_end'];?>">
						</div>
						<span class="help-block">营业时间，24小时制表示，如 8:00-20:00</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> 人均价格</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" name="avg_price" class="form-control" value="<?php  echo $location['avg_price'];?>"/>
						<span class="help-block">人均价格，大于0的整数,单位为人民币（元）</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> 推荐</label>
					<div class="col-sm-8 col-xs-12">
						<textarea name="recommend" class="form-control" cols="30" rows="3"><?php  echo $location['recommend'];?></textarea>
						<span class="help-block">推荐品，餐厅可为推荐菜；酒店为推荐套房；景点为 推荐游玩景点等，针对自己行业的推荐内容</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 特色服务</label>
					<div class="col-sm-8 col-xs-12">
						<textarea name="special" class="form-control" cols="30" rows="3"><?php  echo $location['special'];?></textarea>
						<span class="help-block">特色服务，如免费wifi，免费停车，送货上门等商户 能提供的特色功能或服务</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> 简介</label>
					<div class="col-sm-8 col-xs-12">
						<textarea name="introduction" class="form-control" cols="30" rows="3"><?php  echo $location['introduction'];?></textarea>
						<span class="help-block">商户简介，主要介绍商户信息等 </span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<script>
	$(function(){
		$('select[name^="class_0"]').attr('disabled', 'disabled');
		$('select[name^="reside"]').attr('disabled', 'disabled');
		$('input[name^="baidumap"]').attr('disabled', 'disabled');
	});
</script>
<?php  } ?>

<?php  if($do == 'location_view') { ?>
<div class="clearfix">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
		<div class="panel panel-default">
			<div class="panel-heading">
				基本信息 <small class="text-danger"></small>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 门店名</label>
					<div class="col-sm-8 col-xs-12">
						<p class="form-control-static"><?php  echo $location['business_name'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 分店名</label>
					<div class="col-sm-8 col-xs-12">
						<p class="form-control-static"><?php  echo $location['branch_name'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 类目</label>
					<div class="col-sm-8 col-xs-12">
						<p class="form-control-static"><?php  echo $location['category'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 地址</label>
					<div class="col-sm-8 col-xs-12">
						<p class="form-control-static"><?php  echo $location['address'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 定位</label>
					<div class="col-sm-8 col-xs-12">
						<?php  echo tpl_form_field_coordinate('baidumap', $location['baidumap'])?>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				服务信息 <small class="text-danger"></small>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 门店图片</label>
					<div class="col-sm-8 col-xs-12 multi-img-details">
						<?php  if(is_array($location['photo_list'])) { foreach($location['photo_list'] as $photo) { ?>
						<div class="multi-item">
							<img src="<?php  echo url('utility/wxcode/image', array('attach' => $photo['photo_url']));?>" class="img-responsive img-thumbnail">
						</div>
						<?php  } } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 电话</label>
					<div class="col-sm-8 col-xs-12">
						<p class="form-control-static"><?php  echo $location['telephone'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 人均价格</label>
					<div class="col-sm-8 col-xs-12">
						<p class="form-control-static"><?php  echo $location['avg_price'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 营业时间</label>
					<div class="col-sm-8 col-xs-12">
						<p class="form-control-static"><?php  echo $location['open_time'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 推荐</label>
					<div class="col-sm-8 col-xs-12">
						<p class="form-control-static"><?php  echo $location['recommend'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 特色服务</label>
					<div class="col-sm-8 col-xs-12">
						<p class="form-control-static"><?php  echo $location['special'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"> 简介</label>
					<div class="col-sm-8 col-xs-12">
						<p class="form-control-static"><?php  echo $location['introduction'];?></p>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php  } ?>

<?php  if($do == 'location_list') { ?>
	<div class="clearfix">
		<div class="form-group" style="margin-bottom: 40px;margin-left:-15px">
			<div class="col-sm-12">
				<a href="<?php  echo url('wechat/manage/location_post')?>" class="btn btn-success col-lg-1" style="margin-right:20px;">添加门店</a>
				<a href="<?php  echo url('wechat/manage/export')?>" class="btn btn-danger col-lg-1">从微信导入</a>
			</div>
		</div>
		<div class="panel panel-info">
			<div class="panel-heading">筛选</div>
			<div class="panel-body">
				<form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
					<input type="hidden" name="c" value="wechat" />
					<input type="hidden" name="a" value="manage" />
					<input type="hidden" name="do" value="location_list" />
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">关键字</label>
						<div class="col-sm-9 col-md-8 col-lg-8 col-xs-12">
							<input type="text" class="form-control" placeholder="请输入店名或地址" name="keyword" value="<?php  echo $keyword;?>"/>
						</div>
						<div class="pull-right col-xs-12 col-sm-3 col-lg-2">
							<button class="btn btn-default" data-original-title="" title=""><i class="fa fa-search"></i> 搜索</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<form action="<?php  echo url('wechat/manage/location_list');?>" method="post" id="form2">
			<input type="hidden" name="acid" value="<?php  echo $acid;?>">
			<div class="panel panel-default">
				<div class="panel-body table-responsive">
					<table class="table table-hover" ng-controller="advAPI" style="width:100%;" cellspacing="0" cellpadding="0">
						<thead class="navbar-inner">
						<tr>
							<th width="150">门店名称</th>
							<th width="150">分店名</th>
							<th width="170">类型</th>
							<th width="90">营业时间</th>
							<th width="120">电话</th>
							<th>地址</th>
							<th width="100">审核状态</th>
							<th width="170">审核说明</th>
							<th width="100" style="text-align:right">操作</th>
						</tr>
						</thead>
						<tbody>
							<?php  if(is_array($list)) { foreach($list as $li) { ?>
								<tr>
									<td><?php  echo $li['business_name'];?></td>
									<td><?php  echo $li['branch_name'];?></td>
									<td><?php  echo $li['category_'];?></td>
									<td><?php  echo $li['open_time'];?></td>
									<td><?php  echo $li['telephone'];?></td>
									<td><?php  echo $li['province'];?> <?php  echo $li['city'];?> <?php  echo $li['district'];?> <?php  echo $li['address'];?></td>
									<td>
										<?php  if($li['status'] == 1) { ?>
										<span class="label label-success">审核通过</span>
										<?php  } else if($li['status'] == 2) { ?>
										<span class="label label-default">审核中</span>
										<?php  } else { ?>
										<span class="label label-danger">审核未通过</span>
										<?php  } ?>
									</td>
									<td>
										<?php  if(empty($li['message'])) { ?>
											暂无说明
										<?php  } else { ?>
											<a style="cursor:pointer" tabindex="0" role="button" data-toggle="popover" data-placement="bottom" data-content="<?php  echo $li['message'];?>"><?php  echo cutstr($li['message'], 15, '...');?></a>
										<?php  } ?>
									</td>
									<td style="text-align:right">
										<a class="btn btn-default btn-sm" href="<?php  echo url('wechat/manage/location_view', array('id' => $li['id']))?>" data-toggle="tooltip" data-placement="top" title="详情"><i class="fa fa-eye"></i></a>
										<a class="btn btn-default btn-sm" href="<?php  echo url('wechat/manage/location_del', array('id' => $li['id']))?>" onclick="if(!confirm('您确定要删除吗？')) return false;" data-toggle="tooltip" data-placement="top" title="删除"><i class="fa fa-times"></i></a>
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
<?php  } ?>
<?php  if($do == 'whitelist') { ?>
<div class="clearfix">
	<div class="alert alert-warning"><i class="fa fa-info-circle"> 由于卡券有审核要求，为方便公众号调试，可以设置一些测试帐号，这些帐号可领取未通 过审核的卡券，体验整个流程。最多可添加10个测试白名单</i></div>
	<form action="" method="post" class="form-horizontal form">
		<input type="hidden" name="acid" value="<?php  echo $acid;?>">
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<table class="table table-hover" style="width:100%;" cellspacing="0" cellpadding="0">
					<thead class="navbar-inner">
					<tr>
						<th width="20%">微信号</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
						<?php  if(is_array($whitelist)) { foreach($whitelist as $li) { ?>
							<tr>
								<td><input type="text" name="username[]" class="form-control" value="<?php  echo $li;?>" placeholder="填写微信号"/></td>
								<td>
									<a href="javascript:;" onclick="$(this).parent().parent().remove();return false;"><i class="fa fa-times-circle" title="删除"> </i></a>
								</td>
							</tr>
						<?php  } } ?>
					<tr>
						<td colspan="2">
							<a href="javascript:;" id="add"><i class="fa fa-plus-circle"> 添加测试白名单</i></a>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<?php  } ?>
<script>
	require(['bootstrap'], function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});

		$('[data-toggle="popover"]').popover();

		$('#add').click(function(){
			var html = '<tr>' +
							'<td><input type="text" name="username[]" value="" placeholder="填写微信号" class="form-control"/></td>' +
							'<td><a href="javascript:;" onclick="$(this).parent().parent().remove();return false;"><i class="fa fa-times-circle" title="删除"> </i></a></td>' +
						'</tr>';
			$(this).parent().parent().before(html);
		});
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>