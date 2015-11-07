<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($do == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo url('activity/token/display', array());?>">管理代金券</a></li>
	<li <?php  if($do == 'post' && !$couponid) { ?>class="active"<?php  } ?>><a href="<?php  echo url('activity/token/post', array());?>">添加代金券</a></li>
	<?php  if($do == 'post' &&  $couponid) { ?><li class="active"><a href="<?php  echo url('activity/token/post', array('id' => $couponid));?>">编辑代金券</a></li><?php  } ?>
	<li <?php  if($do == 'record') { ?>class="active"<?php  } ?>><a href="<?php  echo url('activity/token/record', array('id' => $couponid));?>">代金券兑换记录</a></li>
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
	.text-danger{color:red}
</style>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
		<div class="panel panel-default" id="step1">
			<div class="panel-heading">
				代金券
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger"></span> 可用模块</label>
					<div class="col-sm-9 col-xs-12">
						<a href="javascript:;" id="add-module" class="btn btn-default">选择模块</a>
						<input type="hidden" name="module-select" value="<?php  echo $item['module'];?>"/>
						<table class="table" id="module-contain" style="margin-top:10px">
							<tr>
								<?php  if(is_array($coupon_modules)) { foreach($coupon_modules as $modu) { ?>
								<td><?php  echo $module[$modu['module']]['title'];?></td>
								<?php  } } ?>
							</tr>
						</table>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 代金券名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="title" class="form-control" value="<?php  echo $item['title'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 使用条件</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" name="condition" class="form-control"  value="<?php  echo $item['condition'];?>" />
							<span class="input-group-addon">元</span>
						</div>
						<span class="help-block">订单满多少钱可用。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 代金券面额</label>
					<div class="col-sm-9 col-xs-12">
						<div class="input-group">
							<input type="text" name="discount" class="form-control" value="<?php  echo $item['discount'];?>" />
							<span class="input-group-addon">元</span>
						</div>
						<span class="help-block">代金券面额必须少于使用条件的金额。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 可使用的会员组</label>
					<div class="col-sm-9 col-xs-12">
						<select class="form-control" multiple="multiple" name="group[]">
							<?php  if($group) { ?>
							<?php  if(is_array($group)) { foreach($group as $li) { ?>
							<option value="<?php  echo $li['groupid'];?>" <?php  if($li['groupid_select'] == '1') { ?>selected<?php  } ?>><?php  echo $li['title'];?></option>
							<?php  } } ?>
							<?php  } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 缩略图</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_image('thumb', $item['thumb'])?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 代金券说明</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_ueditor('description', $item['description'])?>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default" id="step2" style="display:none">
			<div class="panel-heading">
				代金券
			</div>
			<div class="panel-body">
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
						<?php  echo tpl_form_field_daterange('datelimit', array('starttime' => date('Y-m-d', $item['starttime']),'endtime' => date('Y-m-d', $item['endtime'])), '')?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 每人可领取数量</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="limit" class="form-control" value="<?php  echo $item['limit'];?>" />
						<span class="help-block">此设置项设置每个用户可领取此代金券数量。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span class="text-danger">*</span> 代金券总数量</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="amount" class="form-control" value="<?php  echo $item['amount'];?>" />
						<span class="help-block">此设置项设置代金券的总发行数量。</span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<a href="javascript:;" id="step-control" class="btn btn-primary col-lg-1"  style="margin-right:20px;">下一步</a>
				<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary col-lg-1" style="display:none">
				<input name="id" type="hidden" value="<?php  echo $item['couponid'];?>">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
	<div id="footer-location"  class="hide">
		<span name="submit" id="submit" class="pull-right btn btn-primary">保存</span>
	</div>
</div>
<script type="text/javascript">
	//选择模块
	$('#add-module').click(function(){
		var obj = util.dialog('选择适用模块', ["<?php  echo url('activity/module')?>"], $('#footer-location').html());
		obj.modal('show');

		obj.find('.btn.btn-primary').click(function(){
			var chks = $('.module-list :checkbox:checked');
			var modules = [];
			var modules_detail = [];
			var tmp = [];

			if(chks.length>0){
				chks.each(function(){
					modules.push(this.value);
					tmp['id'] = this.value;
					tmp['title'] = $('#module-' + this.value + ' .title').html();
					modules_detail.push(tmp);
					tmp=[];
				});
				var s = modules.join('@');
				$('#form1 input[name="module-select"]').val(s);

				if(modules_detail) {
					var str = '';
					var len = modules_detail.length;
					var yu = len % 7;
					if(yu > 0) {
						for(var j = 0; j < 7-yu;j++) {
							tmp['id'] = '';
							modules_detail.push(tmp);
						}
					}
					for(var i = 0;i<modules_detail.length;) {
						if(i % 7 == 0) {
							str += '<tr>';
						}
						if(modules_detail[i]['id']) {
							str += '<td>'+modules_detail[i]['title']+'</td>';
						} else {
							str += '<td></td>';
						}
						i++;
						if(i % 7 == 0) {
							str += '</tr>';
						}
					}
					$('#module-contain').html(str)
					$('#module-contain').show();
				}
			} else {
				$('#form1 input[name="module-select"]').val('');
				$('#module-contain').html('');
			}
			obj.modal('hide');
		});
	});

	$('#step-control').click(function(){
		if(this.innerText == '下一步') {
			if($.trim($(':text[name="title"]').val()) == "") {
				util.message("请填写代金券名称",'','error');
				return false;
			}
			var condition = parseInt($.trim($(':text[name="condition"]').val()));
			if(isNaN(condition)) {
				util.message("请填写订单满多少钱可用，必须为数字",'','error');
				return false;
			}
			var discount = parseInt($.trim($(':text[name="discount"]').val()));
			if(isNaN(discount)) {
				util.message("请填写可抵消的金额",'','error');
				return false;
			}
			if($.trim($('select[name="group[]"]').val()) == "") {
				util.message("请选择可使用的会员组",'','error');
				return false;
			}
			if($.trim($('input[name="thumb"]').val()) == "") {
				util.message("上传代金券缩略图",'','error');
				return false;
			}
			/*
			if($.trim($('textarea[name="description"]').val()) == "") {
				util.message("请填写代金券说明",'','error');
				return false;
			}
			*/
			$('#step1').hide();
			$('#step2').show();
			$('#submit').show();
			this.innerText = '上一步';
		} else {
			$('#step1').show();
			$('#step2').hide();
			$('#submit').hide();
			this.innerText = '下一步';
		}
		
	});
	$("#form1").submit(function(){
		if($.trim($('select[name="credittype"]').val()) == "") {
			util.message("请选择积分类型",'','error');
			return false;
		}
		var credit = parseInt($.trim($(':text[name="credit"]').val()));
		if(isNaN(credit)) {
			util.message("积分数量必须为数字",'','error');
			return false;
		}

		var limit = parseInt($.trim($(':text[name="limit"]').val()));
		if(isNaN(limit)) {
			util.message("每人限领数量必须为数字",'','error');
			return false;
		}
		var amount = parseInt($.trim($(':text[name="amount"]').val()));
		if(isNaN(amount)) {
			util.message("代金券总数量必须为数字",'','error');
			return false;
		}
		return true;
	});
</script>
<?php  } else if($do == 'display') { ?>
<div class="main">
	<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form">
		<input type="hidden" name="c" value="activity" />
		<input type="hidden" name="a" value="token" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
				<div class="col-sm-7 col-lg-9 col-xs-12">
					<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">序列号</label>
				<div class="col-sm-7 col-lg-9 col-xs-12">
					<input class="form-control" name="couponsn" id="" type="text" value="<?php  echo $_GPC['couponsn'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">会员组</label>
				<div class="col-sm-7 col-lg-9 col-xs-12">
					<select name="groupid" class="form-control">
						<option value="0">不限</option>
						<?php  if(is_array($groupall)) { foreach($groupall as $li) { ?>
							<option <?php  if($_GPC['groupid'] == $li['groupid']) { ?>selected<?php  } ?> value="<?php  echo $li['groupid'];?>"><?php  echo $li['title'];?></option>
						<?php  } } ?>
					</select>
				</div>
				<div class="pull-right col-xs-12 col-sm-3 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>
			<div class="form-group">
			</div>
		</form>
	</div>
</div>
<div class="alert alert-info">
	如果您希望在会员在线上消费时，可以使用优惠券来减免金额，请确保 <a href="<?php  echo url('profile/payment')?>" target="_blank">支付参数</a> 中的卡券开关为："使用系统卡券"
</div>
<div class="panel panel-default">
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
			<tr>
				<th style="width:60px;">缩略图</th>
				<th style="width:100px;">标题</th>
				<th style="width:130px;">序列号</th>
				<th style="width:80px;">使用条件</th>
				<th style="width:80px;">抵消金额</th>
				<th style="width:80px;">领取条件</th>
				<th style="width:80px;">可用次数</th>
				<th style="width:60px;">总量</th>
				<th style="width:70px;">已领取</th>
				<th style="width:150px;">有效时间</th>
				<th style="text-align:right; width:130px;">操作</th>
			</tr>
			</thead>
			<tbody>
			<?php  if(is_array($list)) { foreach($list as $item) { ?>
			<tr>
				<td><img src="<?php  echo $item['thumb'];?>" width="40"></td>
				<td><?php  echo $item['title'];?></td>
				<td><?php  echo $item['couponsn'];?></td>
				<td>满 <?php  echo $item['condition'];?></td>
				<td><?php  echo $item['discount'];?></td>
				<td><?php  echo $item['credit'];?> <?php  echo $creditnames[$item['credittype']];?></td>
				<td><?php  echo $item['limit'];?> 次</td>
				<td><?php  echo $item['amount'];?> 张</td>
				<td><?php  echo $item['dosage'];?> 张</td>
				<td><?php  echo date('Y-m-d', $item['starttime'])?> - <?php  echo date('Y-m-d', $item['endtime'])?></td>
				<td style="text-align:right;">
					<a href="<?php  echo url('activity/token/post', array('id' => $item['couponid'], 'op' => 'post'))?>" data-toggle="tooltip" data-placement="top" title="编辑" class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a>
					<a href="<?php  echo url('activity/token/del', array('id' => $item['couponid'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" data-toggle="tooltip" data-placement="top" title="删除" class="btn btn-default btn-sm"><i class="fa fa-times"></i></a>
					<a href="<?php  echo url('activity/token/record', array('couponid' => $item['couponid']))?>" data-toggle="tooltip" data-placement="top" title="兑换记录" class="btn btn-default btn-sm"><i class="fa fa-clock-o"></i></a>
				</td>
			</tr>
			<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<?php  echo $pager;?>
</div>
<?php  } else if($do == 'record') { ?>
<div class="main">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="activity">
			<input type="hidden" name="a" value="token">
			<input type="hidden" name="do" value="record">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">兑换标题</label>
					<div class="col-sm-6 col-lg-8 col-xs-12">
						<select class="form-control" name="couponid">
							<option value="0">不限</option>
							<?php  if(is_array($coupons)) { foreach($coupons as $coupon) { ?>
								<option value="<?php  echo $coupon['couponid'];?>" <?php  if($_GPC['couponid'] == $coupon['couponid']) { ?>selected<?php  } ?>><?php  echo $coupon['title'];?></option>
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
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">操作店员</label>
					<div class="col-sm-6 col-lg-8 col-xs-12">
						<input class="form-control" name="operator"  type="text" value="<?php  echo $_GPC['operator'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">代金券状态</label>
					<div class="col-sm-6 col-lg-8 col-xs-12">
						<select class="form-control" name="status">
							<option>不限</option>
							<?php  if(is_array($status)) { foreach($status as $k => $v) { ?>
								<option value="<?php  echo $k;?>" <?php  if($_GPC['status'] == $k) { ?> selected <?php  } ?>><?php  echo $v;?></option>
							<?php  } } ?>
						</select>	
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
					<th style="width:80px; text-align:center;">图标</th>
					<th style="width:100px; text-align:center;">面额</th>
					<th style="width:150px; text-align:center;">兑换时间</th>
					<th style="width:150px; text-align:center;">发放模块</th>
					<th style="width:150px; text-align:center;">代金券状态</th>
					<th style="width:100px;">操作者</th>
					<th style="width:150px; text-align:center;">使用时间</th>
					<th style="width:80px; text-align:center;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td class="text-center"><?php  echo $item['uid'];?></td>
					<td class="text-center"><?php  echo $item['title'];?></td>
					<td class="text-center"><img src="<?php  echo $item['thumb'];?>" style="max-width:50px; max-height: 30px;"></td>
					<td class="text-center"><?php  echo $item['discount'];?></td>
					<td class="text-center"><?php  echo date('Y-m-d H:i', $item['granttime'])?></td>
					<td class="text-center">
						<?php  if(!empty($item['grantmodule'])) { ?>
							<span class="label label-warning"><?php  echo $modules[$item['grantmodule']]['title'];?></span>
						<?php  } ?>
					</td>
					<td class="text-center">
						<?php  if($item['status'] == 1) { ?>
							<span class="label label-success">未使用</span>
						<?php  } else { ?>
							<span class="label label-danger">已使用</span>
						<?php  } ?>
					</td>
					<td>
						<?php  if($item['status'] == 1) { ?>
							<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#tabused" data-cid="<?php  echo $item['couponid'];?>" data-rid="<?php  echo $item['recid'];?>"
							data-uid="<?php  echo $item['uid'];?>" onclick="$('#couponid').val($(this).attr('data-cid'));$('#uid').val($(this).attr('data-uid'));$('#recid').val($(this).attr('data-rid'))">标记为使用</button>
						<?php  } else { ?>
							<?php  echo $item['operator'];?>
						<?php  } ?>
					</td>
					<td class="text-center">
						<?php  if($item['status'] == 1) { ?>
						<?php  } else { ?>
							<?php  echo date('Y-m-d H:i', $item['usetime'])?>
						<?php  } ?>
					</td>
					<td class="text-center">
						<a onclick="if(!confirm('删除后不可恢复,您确定删除吗?')) return false;"  href="<?php  echo url('activity/coupon/record-del', array('id' => $item['recid']))?>" class="btn btn-default btn-sm" title="删除兑换记录"><i class="fa fa-times"></i></a>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
	</div>
	<?php  echo $pager;?>
</div>

<!-- 代金券标记已使用模态层 -->
<div class="modal fade" id="tabused" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">将折扣券标记为使用</h4>
			</div>
			<form action="" method="post" class="form-horizontal" role="form">
			<div class="modal-body">
				请输入店员操作密码：
				<input type="text" name="password" class="form-control" />
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
				<button type="submit" name="submit" class="btn btn-danger" value="used" />标记为已使用</button>
				<input type="hidden" name="uid" id="uid" value="" />
				<input type="hidden" name="couponid" id="couponid" value="" />
				<input type="hidden" name="recid" id="recid" value="" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
			</form>
		</div>
	</div>
</div>

<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>