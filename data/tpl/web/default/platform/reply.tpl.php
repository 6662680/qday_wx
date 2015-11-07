<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php  if(!in_array($m, $sysmods)) { ?>
<ol class="breadcrumb" style="padding:5px 0;">
	<li><a href="./?refresh"><i class="fa fa-cogs"></i> &nbsp; 扩展功能</a></li>
	<li><a href="<?php  echo url('home/welcome/ext', array('m' => $module['name']));?>"><?php  echo $types[$module['type']]['title'];?>模块 - <?php  echo $module['title'];?></a></li>
	<li class="active">管理 <?php  echo $module['title'];?></li>
</ol>
<?php  } ?>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo url('platform/reply', array('m' => $m));?>">管理<?php  echo $module['title'];?></a></li>
	<li><a href="<?php  echo url('platform/reply/post', array('m' => $m));?>"><i class="fa fa-plus"></i> 添加<?php  echo $module['title'];?></a></li>
	<?php  if(!empty($site_urls)) { ?>
		<?php  if(is_array($site_urls)) { foreach($site_urls as $site_url) { ?>
			<li><a href="<?php  echo $site_url['url'];?>"> <?php  echo $site_url['title'];?></a></li>
		<?php  } } ?>
	<?php  } ?>
</ul>
<div class="clearfix">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="platform">
			<input type="hidden" name="a" value="reply">
			<input type="hidden" name="m" value="<?php  echo $_GPC['m'];?>" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
					<div class="col-sm-8 col-xs-12">
						<select name="status" class="form-control">
							<option value="-1" <?php  if($_GPC['status'] == '-1') { ?> selected<?php  } ?>>所有</option>
							<option value="1" <?php  if($_GPC['status'] == '1') { ?> selected<?php  } ?>>启用</option>
							<option value="0" <?php  if($_GPC['status'] == '0') { ?> selected<?php  } ?>>禁用</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
					<div class="col-sm-8 col-xs-12">
							<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
					</div>
					<div class="col-xs-12 col-sm-2 col-lg-1 text-right">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<form action="<?php  echo url('platform/reply/delete');?>" method="post" class="form-horizontal" role="form" id="form1">
	<input type="hidden" name="m" value="<?php  echo $m;?>"/>
	<?php  if(!empty($replies)) { ?>
	<div>
		<?php  if(is_array($replies)) { foreach($replies as $row) { ?>
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<label class="checkbox-inline" style="padding-top:0"><input type="checkbox" name="rid[]" value="<?php  echo $row['id'];?>"/> <?php  echo $row['name'];?></label>
				<span class="pull-right">
					<?php  if($row['displayorder'] > 0) { ?>
						<?php  if($row['displayorder'] == '255') { ?>
							<span class="label label-primary">置顶</span>
						<?php  } else { ?>
							<span class="label label-info">优先级 <?php  echo $row['displayorder'];?></span>
						<?php  } ?>
					<?php  } ?>
					<?php  if($row['status'] == '0') { ?><span class="label label-danger">已禁用</span><?php  } ?>
				</span>
			</div>
			<div class="panel-body">
				<?php  if(is_array($row['keywords'])) { foreach($row['keywords'] as $kw) { ?>
				<span class="label label-default" data-toggle="tooltip" data-placement="top" title="<?php  if($kw['type']==1) { ?>等于<?php  } else if($kw['type']==2) { ?>包含<?php  } else if($kw['type']==3) { ?>正则<?php  } ?>"><?php  echo $kw['content'];?></span>&nbsp;
				<?php  if($kw['type'] == 4) { ?><span class="label label-info" data-toggle="tooltip" data-placement="top" title="托管">优先级在<?php  echo $row['displayorder'];?>之下直接生效</span><?php  } ?>
				<?php  } } ?>
			</div>
			<div class="panel-footer clearfix">
				<div class="btn-group pull-right">
					<a class="btn btn-default btn-sm" href="<?php  echo url('platform/reply/post', array('m' => $m, 'rid' => $row['id']))?>"><i class="fa fa-edit"></i> 编辑</a>
					<a class="btn btn-default btn-sm" href="<?php  echo url('platform/reply/delete', array('m' => $m, 'rid' => $row['id']))?>" onclick="return confirm('删除规则将同时删除关键字与回复，确认吗？');return false;"><i class="fa fa-times"></i> 删除</a>
					<a class="btn btn-default btn-sm" href="<?php  echo url('platform/stat/trend', array('id' => $row['id'], 'm' => $row['module']))?>"><i class="fa fa-bar-chart-o"></i> 使用率走势</a>
					<?php  if($row['options']) { ?>
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
							功能选项
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu" style="min-width:0px;">
							<?php  if(is_array($row['options'])) { foreach($row['options'] as $opt) { ?>
								<li><a href="<?php  echo $opt['url'];?>"><?php  echo $opt['title'];?></a></li>
							<?php  } } ?>
						</ul>
					</div>
					<?php  } ?>
				</div>
			</div>
		</div>
		<?php  } } ?>
	</div>
	<div>
		<label class="checkbox-inline" style="margin-top:-30px;margin-left:17px"><input type="checkbox" name="select_all" id="select_all" value="1"/></label>
		<input type="submit" class="btn btn-danger" value="删除" onclick="if(!confirm('确定删除选中的规则吗？')) return false;"/>
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>"/>
	</div>
	<?php  } ?>
	</form>
	<?php  echo $pager;?>
</div>
<script>
require(['bootstrap'], function($){
	$(function () {
		$('[data-toggle="tooltip"]').tooltip();
		$('#select_all').click(function(){
			$('#form1 :checkbox').prop('checked', $(this).prop('checked'));
		});
		$('#form1 :checkbox').click(function(){
			if(!$(this).prop('checked')) {
				$('#select_all').prop('checked', false);
			} else {
				var flag = 0;
				$('#form1 :checkbox[name="rid[]"]').each(function(){
					if(!$(this).prop('checked') && !flag) {
						flag = 1;
					}
				});
				if(flag) {
					$('#select_all').prop('checked', false);
				} else {
					$('#select_all').prop('checked', true);
				}
			}
		});
	})
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
