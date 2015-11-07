<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
<ol class="breadcrumb">
	<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
	<li><a href="<?php  echo url('system/welcome');?>">系统</a></li>
	<?php  if($do == 'display') { ?><li class="active">服务套餐列表</li><?php  } ?>
	<?php  if($do == 'post') { ?><li class="active"><?php  if($id) { ?>编辑<?php  } else { ?>添加<?php  } ?>服务套餐</li><?php  } ?>
	
</ol>
<ul class="nav nav-tabs">
	<li <?php  if($do == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo url('account/groups/display');?>">服务套餐列表</a></li>
	<li <?php  if($do == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo url('account/groups/post');?>"><?php  if($id) { ?>编辑<?php  } else { ?>添加<?php  } ?>服务套餐</a></li>
</ul>
<?php  if($do == 'post') { ?>
<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
<div class="clearfix">
	<input type="hidden" name="id" value="<?php  echo $id;?>" />
	<input type="hidden" name="templateid" value="<?php  echo $template['id'];?>">
	<h5 class="page-header">服务套餐管理</h5>
	<div class="form-group">
		<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">服务套餐名称</label>
		<div class="col-sm-10 col-xs-12">
			<input type="text" class="form-control" name="name" id="name" value="<?php  echo $item['name'];?>" />
		</div>
	</div>
	<h5 class="page-header">设置当前用户允许访问的模块</h5>
	<div class="panel panel-default">
		<div class="panel-body table-responsive">
			<table class="table">
				<thead>
				<tr>
					<th class="row-first">选择</th>
					<th>模块名称</th>
					<th>模块标识</th>
					<th>功能简介</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php  if(is_array($modules)) { foreach($modules as $module) { ?>
				<tr>
					<td class="row-first"><?php  if(!$module['issystem']) { ?><input class="modules" type="checkbox" value="<?php  echo $module['name'];?>" name="modules[]" <?php  if(!empty($item['modules']) && in_array($module['name'], $item['modules'])) { ?>checked<?php  } ?> /><?php  } else { ?><input class="modules" type="checkbox" value="<?php  echo $module['name'];?>" name="modules[]" disabled checked /><?php  } ?></td>
					<td><?php  echo $module['title'];?></td>
					<td><?php  echo $module['name'];?></td>
					<td><?php  echo $module['ability'];?></td>
					<td><?php  if($module['issystem']) { ?><span class="label label-success">系统模块</span><?php  } ?></td>
				</tr>
				<?php  } } ?>
				</tbody>
			</table>
		</div>
	</div>
	<h5 class="page-header">设置当前用户允许访问的模板</h5>
	<div class="panel panel-default">
		<div class="panel-body table-responsive">
			<table class="table">
				<thead>
				<tr>
					<th>选择</th>
					<th>模板名称</th>
					<th>模板标识</th>
					<th>功能简介</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php  if(is_array($templates)) { foreach($templates as $temp) { ?>
				<tr>
					<td class="row-first"><?php  if($temp['name'] != 'default') { ?><input class="modules" type="checkbox" value="<?php  echo $temp['id'];?>" name="templates[]" <?php  if(!empty($item['templates']) && in_array($temp['id'], $item['templates'])) { ?>checked<?php  } ?> /><?php  } else { ?><input class="modules" type="checkbox" value="<?php  echo $temp['id'];?>" name="templates[]" disabled checked /><?php  } ?></td>
					<td><?php  echo $temp['title'];?></td>
					<td><?php  echo $temp['name'];?></td>
					<td><?php  echo $temp['description'];?></td>
					<td></td>
				</tr>
				<?php  } } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-12">
			<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
			<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" />
		</div>
	</div>
</div>
</form>
<?php  } else if($do == 'display') { ?>
<style>
	ul.ul-float{padding:0;marign:0}
	ul.ul-float li{float:left;width:115px;height:30px;line-height:30px;overflow:hidden;}
</style>


<form action="" method="post">
<div class="panel panel-default">
<div class="clearfix table-responsive panel-body">
	<table class="table">
		<thead>
			<tr>
				<th style="width:30px;">删？</th>
				<th style="width:150px;">名称</th>
				<th>可用模块</th>
				<th>可用模板</th>
				<th style="min-width:60px;">操作</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><input type="checkbox" disabled value="<?php  echo $item['id'];?>" /></td>
				<td>基础服务<span class="label label-info">系统</span></td>
				<td>
					<ul class="ul-float">
						<li>基本文字回复</li>
						<li>基本混合图文回复</li>
						<li>基本语音回复</li>
						<li>自定义接口回复</li>
					</ul>
				</td>
				<td>
					<ul class="ul-float">
						<li>微站默认模板</li>
					</ul>
				</td>
				<td><span></span></td>
			</tr>
			<tr>
				<td><input type="checkbox" disabled value="<?php  echo $item['id'];?>" /></td>
				<td>所有服务<span class="label label-info">系统</span></td>
				<td>
					<span class="label label-danger">系统所有模块</span></li>
				</td>
				<td>
					<ul class="ul-float">
						<span class="label label-danger">系统所有模板</span></li>
					</ul>
				</td>
				<td><span></span></td>
			</tr>
			<?php  if(is_array($list)) { foreach($list as $item) { ?>
			<tr>
				<td><input type="checkbox" name="delete[]" value="<?php  echo $item['id'];?>" /></td>
				<td><?php  echo $item['name'];?></td>
				<td>
					<ul class="ul-float">
					<?php  if(is_array($item['modules'])) { foreach($item['modules'] as $module) { ?>
						<li><?php  echo $module['title'];?></li>
					<?php  } } ?>
					</ul>
				</td>
				<td>
					<ul class="ul-float">
						<?php  if(is_array($item['templates'])) { foreach($item['templates'] as $template) { ?>
							<li><?php  echo $template['title'];?></li>
						<?php  } } ?>
					</ul>
				</td>
				<td><span><a href="<?php  echo url('account/groups/post', array('id' => $item['id']))?>">编辑</a></span></td>
			</tr>
			<?php  } } ?>
		</tbody>
		<tr>
			<th></th>
			<td>
				<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
				<input type="submit" class="btn btn-primary" name="submit" value="提交" />
			</td>
		</tr>
	</table>
</div>
</div>
</form>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>