<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo url('wechat/account');?>" style="color:#d9534f"><i class="fa fa fa-cog fa-spin"></i> 公众号列表</a></li>
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
								<a href="<?php  echo url('wechat/manage', array('__acid' => $li['acid']));?>" class="btn btn-default" style="color:#d9534f" target="_balnk" data-toggle="tooltip" data-placement="bottom" data-original-title="管理"><i class="fa fa-cog fa-spin"></i></a>
							</td>
						</tr>
					<?php  } } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>