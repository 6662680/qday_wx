<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<ul class="nav nav-tabs">
	<li <?php  if(empty($id)) { ?>class="active"<?php  } ?>><a href="<?php  echo url('mc/fields');?>">字段管理</a></li>
	<?php  if(!empty($id)) { ?><li class="active"><a href="<?php  echo url ('mc/fields/post', array('id' => $id));?>">编辑字段</a></li><?php  } ?>
</ul>

<?php  if($do == 'display') { ?>
<form action="" method="post">
<div class="table-responsive clearfix">
	<div class="page-header">
		<h4>字段管理</h4>
	</div>
	<div class="panel panel-default">
	<div class="panel-body table-responsive">
	<table class="table table-hover">
		<thead class="navbar-inner">
			<tr>
				<th style="width:10%;">排序</th>
				<th style="width:20%;">字段名</th>
				<th style="width:20%;">标题</th>
				<th style="width:20%;">是否启用</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			<?php  if(is_array($fields)) { foreach($fields as $field) { ?>
			<tr>
				<td>
					<input type="text" class="form-control" name="displayorder[<?php  echo $field['fid'];?>]" value="<?php  echo $field['displayorder'];?>" />
					<input type="hidden" name="id[<?php  echo $field['fid'];?>]" value="<?php  echo $field['id'];?>" />
					<input type="hidden" name="fieldid[<?php  echo $field['fid'];?>]" value="<?php  echo $field['fid'];?>" />
					<input type="hidden" name="title[<?php  echo $field['fid'];?>]" value="<?php  echo $field['title'];?>" />
				</td>
				<td><?php  echo $field['field'];?></td>
				<td><?php  echo $field['title'];?></td>
				<td>
					<input type="checkbox" name="available[<?php  echo $field['fid'];?>]" value="1" <?php  if($field['available']) { ?> checked <?php  } ?> />
				</td>
				<td>
					<?php  if(!empty($field['id'])) { ?>
					<a href="<?php  echo url('mc/fields/post', array('id' => $field['id']))?>" title="编辑" class="btn btn-primary btn-sm">编辑</a>
					<?php  } ?>
				</td>
			</tr>
			<?php  } } ?>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td>
					<input type="checkbox" id="selectAll" onclick="var ck = this.checked;$(':checkbox').each(function(){this.checked = ck});">
					<a class="btn btn-success btn-sm" style="margin-left: -25px;" onclick="$('#selectAll').click();">全选</a>
				</td>
				<td></td>
			</tr>
		</tbody>
	</table>
	</div>
	</div>
	<div>
		<button type="submit" class="btn btn-primary" name="submit" value="提交">提交</button>
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
	</div>
</div>
</form>

<?php  } else if($do == 'post') { ?>
<div class="clearfix">
	<div class="page-header">
		<h4>编辑字段</h4>
	</div>
	<form class="form-horizontal form" action="" method="post">
		<input type="hidden" name="id" value="<?php  echo $item['id'];?>">
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">排序</label>
			<div class="col-sm-10 col-xs-12">
				<input type="text" class="form-control" name="displayorder" value="<?php  echo $item['displayorder'];?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">名称</label>
			<div class="col-sm-10 col-xs-12">
				<input type="text" class="form-control" name="title" value="<?php  echo $item['title'];?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">启用</label>
			<div class="col-sm-10 col-xs-12">
				<label for="radio_1" class="radio-inline">
					<input type="radio" name="available" id="radio_1" value="1" <?php  if(empty($item) || $item['available'] == 1) { ?> checked<?php  } ?> /> 是
				</label>
				<label for="radio_0" class="radio-inline">
					<input type="radio" name="available" id="radio_0" value="0" <?php  if(!empty($item) && $item['available'] == 0) { ?> checked<?php  } ?> /> 否
				</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label"></label>
			<div class="col-sm-10 col-xs-12">
				<input type="submit" class="btn btn-primary" name="submit" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
</div>
<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>