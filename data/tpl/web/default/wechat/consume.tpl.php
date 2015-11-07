<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($do == 'account') { ?>class="active"<?php  } ?>><a href="<?php  echo url('wechat/account');?>" style="color:#d9534f"><i class="fa fa fa-cog fa-spin"></i> 公众号列表</a></li>
	<?php  if($do == 'record') { ?><li class="active"><a href="javascript:;">卡券核销</a></li><?php  } ?>
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
<style>
	.text-danger{color:red;}
</style>
<?php  if($do == 'account') { ?>
<div class="clearfix">
	<div class="alert alert-info">
		<i class="fa fa-info-circle"> 微信卡券功能需要您的公众号为认证订阅号或认证服务号</i>
	</div>
	<div class="panel panel-default">
		<div class="panel-body table-responsive">
			<table class="table table-hover" style="width:100%;" cellspacing="0" cellpadding="0">
				<thead class="navbar-inner">
				<tr>
					<th width="20%">公众号名称</th>
					<th width="20%">等级</th>
					<th width="20%">操作</th>
				</tr>
				</thead>
				<tbody>
				<?php  if(is_array($accounts)) { foreach($accounts as $li) { ?>
				<tr>
					<td><?php  echo $li['name'];?></td>
					<td>
						<?php  if($li['level'] == 3) { ?>
						<span class="label label-danger">认证订阅号</span>
						<?php  } else if($li['level'] == 4) { ?>
						<span class="label label-success">认证服务号</span>
						<?php  } ?>
					</td>
					<td>
						<a href="<?php  echo url('wechat/consume/record', array('__acid' => $li['acid']));?>" class="btn btn-default" style="color:#d9534f" target="_balnk" data-toggle="tooltip" data-placement="bottom" data-original-title="管理"><i class="fa fa-cog fa-spin"></i></a>
					</td>
				</tr>
				<?php  } } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php  } else if($do == 'record') { ?>
<div class="clearfix">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
				<input type="hidden" name="c" value="wechat">
				<input type="hidden" name="a" value="card">
				<input type="hidden" name="do" value="record"/>
				<input type="hidden" name="op" value="list"/>
				<input type="hidden" name="card_id" value="<?php  echo $card_id;?>"/>
				<input type="hidden" name="status" value="<?php  echo $status;?>"/>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
					<div class="col-sm-7 col-lg-8 col-md-8 col-xs-12">
						<a class="btn btn-default <?php  if($_GPC['status'] == '') { ?>btn-primary<?php  } ?>" href="<?php  echo url('wechat/consume/record', array('op' => 'list', 'status' => '', 'card_id' => $card_id))?>">不限</a>
						<a class="btn btn-default <?php  if($_GPC['status'] == '1') { ?>btn-primary<?php  } ?>" href="<?php  echo url('wechat/consume/record', array('op' => 'list', 'status' => '1', 'card_id' => $card_id))?>">未使用</a>
						<a class="btn btn-default <?php  if($_GPC['status'] == '2') { ?>btn-primary<?php  } ?>" href="<?php  echo url('wechat/consume/record', array('op' => 'list', 'status' => '2', 'card_id' => $card_id))?>">已失效</a>
						<a class="btn btn-default <?php  if($_GPC['status'] == '3') { ?>btn-primary<?php  } ?>" href="<?php  echo url('wechat/consume/record', array('op' => 'list', 'status' => '3', 'card_id' => $card_id))?>">已核销</a>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">code码</label>
					<div class="col-sm-7 col-lg-8 col-md-8 col-xs-12">
						<input class="form-control" name="code" placeholder="code码" type="text" value="<?php  echo $_GPC['code'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝昵称</label>
					<div class="col-sm-7 col-lg-8 col-md-8 col-xs-12">
						<input class="form-control" name="nickname" placeholder="粉丝昵称" type="text" value="<?php  echo $_GPC['nickname'];?>">
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
						<th width="150">卡券名称</th>
						<th width="80">场景名称/id</th>
						<th width="90">领取方式</th>
						<th width="90">领取人</th>
						<th width="90">转赠人</th>
						<th width="120">code码</th>
						<th width="60">状态</th>
						<th width="120">领取时间</th>
						<th width="120">使用时间</th>
						<th width="80">核销员</th>
						<th style="width:150px; text-align:right;">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($data)) { foreach($data as $dca) { ?>
					<tr>
						<td><?php  echo $cards[$dca['card_id']]['title'];?></td>
						<td>
							<?php  if($outers[$dca['outer_id']]['name']) { ?>
							<?php  echo $outers[$dca['outer_id']]['name'];?>/<?php  echo $dca['outer_id'];?>
							<?php  } else { ?>
							<?php  echo $dca['outer_id'];?>
							<?php  } ?>
						</td>
						<td>
							<?php  if($dca['givebyfriend'] == 1) { ?>
							<span class="label label-danger">朋友赠送</span>
							<?php  } else { ?>
							<span class="label label-success">自己领取</span>
							<?php  } ?>
						</td>
						<td>
							<?php  if($nicknames[$dca['openid']]['nickname']) { ?>
							<?php  echo $nicknames[$dca['openid']]['nickname'];?>
							<?php  } else { ?>
							<?php  echo cutstr($dca['openid'], 8);?>
							<?php  } ?>
						</td>
						<td>
							<?php  if($nicknames[$dca['friend_openid']]['nickname']) { ?>
							<?php  echo $nicknames[$dca['friend_openid']]['nickname'];?>
							<?php  } else { ?>
							<?php  echo cutstr($dca['friend_openid'], 8);?>
							<?php  } ?>
						</td>
						<td><?php  echo $dca['code'];?></td>
						<td>
							<?php  if($dca['status'] == 1) { ?>
							<span class="label label-success">未使用</span>
							<?php  } else if($dca['status'] == 2) { ?>
							<span class="label label-warning">已失效</span>
							<?php  } else if($dca['status'] == 3) { ?>
							<span class="label label-danger">已核销</span>
							<?php  } ?>
						</td>
						<td>
							<?php  echo date('Y-m-d H:i:s', $dca['addtime']);?>
						</td>
						<td>
							<?php  if($dca['usetime']) { ?>
								<?php  echo date('Y-m-d H:i:s', $dca['usetime']);?>
							<?php  } ?>
						</td>
						<td><?php  echo $dca['clerk_name'];?></td>
						<td style="text-align:right;">
							<?php  if($dca['status'] == 1) { ?>
							<a href="<?php  echo url('wechat/consume/record', array('op' => 'unavailable', 'id' => $dca['id']))?>" class="btn btn-default btn-sm" title="设置为失效" data-toggle="tooltip" data-placement="top" onclick="if(!confirm('设置为失效后将不可恢复，确定设置失效吗?')) return false;"><i class="fa fa-power-off"> </i></a>
							<a href="javascript:;" class="btn btn-default btn-sm consume" title="核销卡券" data-id="<?php  echo $dca['id'];?>" data-toggle="tooltip" data-placement="top"><i class="fa fa-key"> </i></a>
							<?php  } ?>
							<a href="<?php  echo url('wechat/consume/record', array('op' => 'unavailable', 'del' => 1, 'id' => $dca['id']))?>" class="btn btn-default btn-sm" title="删除" data-toggle="tooltip" data-placement="top" onclick="if(!confirm('删除后不可恢复，确定删除吗?')) return false;"><i class="fa fa-times"> </i></a>
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

<div id="content" class="hide">
	<form action="<?php  echo url('wechat/consume/record', array('op' => 'consume'))?>" method="post">
		<input type="hidden" name="id" value=""/>
		<div class="form-group">
			<label class="control-label">店员密码:</label>
			<input type="text" class="form-control" id="pdw" name="pdw">
			<div class="help-block">请输入您的店员密码</div>
		</div>
		<div class="form-group" style="text-align:right">
			<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			<input type="submit" class="btn btn-primary" value="确定">
		</div>
	</form>
</div>

<script>
	require(['jquery', 'util'], function($, u){
		$('.consume').click(function(){
			var id = $(this).attr('data-id');
			$('#content input[name="id"]').val(id);
			var obj = u.dialog('', $('#content').html());
			obj.modal('show');

		});
	});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>