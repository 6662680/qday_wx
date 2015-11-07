<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<script type="text/javascript">
	require(['bootstrap.switch'], function($){
		$(function(){
			$(':checkbox').bootstrapSwitch();
			$(':checkbox').on('switchChange.bootstrapSwitch', function(e, state){
				var rids = [];
				$(':checkbox:checked').each(function(){
					rids.push($(this).val());
				});
				$.post(location.href, {'rids': rids.toString()}, function(data){
					console.dir(data)
				});
			});
		});
	});
</script>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo url('platform/service/switch');?>">常用服务接入</a></li>
</ul>
<div class="panel panel-default">
	<div class="table-responsive panel-body">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:100px;">服务名称</th>
					<th style="width:200px;">功能说明</th>
					<th style="width:120px;">状态</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($ds)) { foreach($ds as $row) { ?>
				<tr>
					<td><?php  echo $row['title'];?></td>
					<td><?php  echo $row['description'];?></td>
					<td>
						<input type="checkbox" value="<?php  echo $row['rid'];?>" <?php  echo $row['switch'];?>/>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
