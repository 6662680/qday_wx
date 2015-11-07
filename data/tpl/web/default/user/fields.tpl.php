<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
<ol class="breadcrumb">
	<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
	<li><a href="<?php  echo url('system/welcome');?>">系统</a></li>
	<li class="active">字段管理</li>
</ol>
<ul class="nav nav-tabs">
	<li><a href="<?php  echo url('user/registerset');?>">注册选项</a></li>
	<li <?php  if(empty($id)) { ?>class="active"<?php  } ?>><a href="<?php  echo url('user/fields');?>">字段管理</a></li>
	<?php  if(!empty($id)) { ?><li class="active"><a href="<?php  echo url('user/fields');?>">编辑字段</a></li><?php  } ?>
</ul>
<?php  if($do == 'display') { ?>
<form action="" method="post">
<div class="table-responsive clearfix">
	<h5 class="page-header">字段管理</h5>
	<div class="panel panel-default">
	<div class="panel-body table-responsive">
	<table class="table table-hover">
		<thead class="navbar-inner">
			<tr>
				<th style="width:80px;">排序</th>
				<th style="width:100px;">字段名</th>
				<th style="width:150px;">标题</th>
				<th style="width:80px;">是否启用</th>
				<th style="width:100px;">注册页显示</th>
				<th style="width:80px;">是否必填</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			<?php  if(is_array($fields)) { foreach($fields as $field) { ?>
			<tr>
				<td><input type="text" class="form-control"  style="width:50px;" placeholder="" name="displayorder[<?php  echo $field['id'];?>]" value="<?php  echo $field['displayorder'];?>"></td>
				<td style="vertical-align:middle;"><div><?php  echo $field['field'];?></div></td>
				<td style="vertical-align:middle;"><?php  echo $field['title'];?></td>
				<td style="vertical-align:middle;"><input type="checkbox" name="available[<?php  echo $field['id'];?>]" value="1" <?php  if($field['available']) { ?>checked<?php  } ?>/></td>
				<td style="vertical-align:middle;"><input type="checkbox" name="showinregister[<?php  echo $field['id'];?>]" value="1" <?php  if($field['showinregister']) { ?>checked<?php  } ?>/></td>
				<td style="vertical-align:middle;"><input type="checkbox" name="required[<?php  echo $field['id'];?>]" value="1" <?php  if($field['required']) { ?>checked<?php  } ?>/></td>
				<td style="vertical-align:middle;"><a href="<?php  echo url('user/fields/post', array('id' => $field['id']))?>" title="编辑">编辑</a></td>
			</tr>
			<?php  } } ?>
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
	<h5 class="page-header">编辑字段</h5>
	<form class="form-horizontal form" action="" method="post">
		<input type="hidden" name="id" value="<?php  echo $item['id'];?>">
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">字段</label>
			<div class="col-sm-10 col-xs-12">
				<input type="text" class="form-control" value="<?php  echo $item['field'];?>" disabled>		
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">排序</label>
			<div class="col-sm-10 col-xs-12">
					<input type="text" class="form-control" placeholder="" name="displayorder" value="<?php  echo $item['displayorder'];?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">名称</label>
			<div class="col-sm-10 col-xs-12">
					<input type="text" class="form-control" placeholder="" name="title" value="<?php  echo $item['title'];?>">
					<input type="hidden" name="name_old" value="<?php  echo $item['Field'];?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">描述</label>
			<div class="col-sm-10 col-xs-12">
					<textarea style="height:100px;" class="form-control" name="description" cols="50"><?php  echo $item['description'];?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否启用</label>
			<div class="col-sm-10 col-xs-12">
					<label for="radio_1" class="radio-inline"><input type="radio" name="available" id="radio_1" value="1" <?php  if(empty($item) || $item['available'] == 1) { ?> checked<?php  } ?> /> 是</label>
					<label for="radio_0" class="radio-inline"><input type="radio" name="available" id="radio_0" value="0" <?php  if(!empty($item) && $item['available'] == 0) { ?> checked<?php  } ?> /> 否</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否必填</label>
			<div class="col-sm-10 col-xs-12">
					<label for="radio_6" class="radio-inline"><input type="radio" name="required" id="radio_6" value="1" <?php  if(empty($item) || $item['required'] == 1) { ?> checked<?php  } ?> /> 是</label>
					<label for="radio_7" class="radio-inline"><input type="radio" name="required" id="radio_7" value="0" <?php  if(!empty($item) && $item['required'] == 0) { ?> checked<?php  } ?> /> 否</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提交后不可修改</label>
			<div class="col-sm-10 col-xs-12">
					<label for="radio_3" class="radio-inline"><input type="radio" name="unchangeable" id="radio_3" value="1" <?php  if(empty($item) || $item['unchangeable'] == 1) { ?> checked<?php  } ?> /> 是</label>
					<label for="radio_2" class="radio-inline"><input type="radio" name="unchangeable" id="radio_2" value="0" <?php  if(!empty($item) && $item['unchangeable'] == 0) { ?> checked<?php  } ?> /> 否</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">在注册页面显示</label>
			<div class="col-sm-10 col-xs-12">
					<label for="radio_4" class="radio-inline"><input type="radio" name="showinregister" id="radio_4" value="1" <?php  if(empty($item) || $item['showinregister'] == 1) { ?> checked<?php  } ?> /> 是</label>
					<label for="radio_5" class="radio-inline"><input type="radio" name="showinregister" id="radio_5" value="0" <?php  if(!empty($item) && $item['showinregister'] == 0) { ?> checked<?php  } ?> /> 否</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
			<div class="col-sm-10 col-xs-12">
					<button type="submit" class="btn btn-primary" name="submit" value="提交">提交</button>
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>