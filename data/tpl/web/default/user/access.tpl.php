<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
	<ol class="breadcrumb">
		<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
		<li><a href="<?php  echo url('system/welcome');?>">系统</a></li>
		<li class="active">注册选项</li>
	</ol>
	<ul class="nav nav-tabs">
		<li class="active"><a href="<?php  echo url('user/registerset');?>">注册选项</a></li>
		<li><a href="<?php  echo url('user/fields');?>">字段管理</a></li>
	</ul>
	<div class="clearfix">
		<h5 class="page-header">注册设置</h5>
		<form action="" method="post" class="form-horizontal" role="form">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">是否开启用户注册</label>
				<div class="col-sm-9 col-xs-12">
					<label for="isshow1" class="radio-inline"><input type="radio" name="open" value="1" id="isshow1" <?php  if(!empty($settings['open'])) { ?> checked<?php  } ?>> 是</label>&nbsp;&nbsp;&nbsp;
					<label for="isshow2" class="radio-inline"><input type="radio" name="open" value="0" id="isshow2" <?php  if(empty($settings['open'])) { ?> checked<?php  } ?>> 否</label>
					<div class="help-block"></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">是否审核新用户</label>
				<div class="col-sm-9 col-xs-12">
					<label for="isshow3" class="radio-inline"><input type="radio" name="verify" value="1" id="isshow3"<?php  if(!empty($settings['verify'])) { ?> checked<?php  } ?>> 是</label>&nbsp;&nbsp;&nbsp;
					<label for="isshow4" class="radio-inline"><input type="radio" name="verify" value="0" id="isshow4"<?php  if(empty($settings['verify'])) { ?> checked<?php  } ?>> 否</label>
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">启用注册验证码</label>
				<div class="col-sm-9 col-xs-12">
					<label for="isshow5" class="radio-inline"><input type="radio" name="code" value="1" id="isshow5"<?php  if(!empty($settings['code'])) { ?> checked<?php  } ?>> 是</label>&nbsp;&nbsp;&nbsp;
					<label for="isshow6" class="radio-inline"><input type="radio" name="code" value="0" id="isshow6"<?php  if(empty($settings['code'])) { ?> checked<?php  } ?>> 否</label>
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">默认所属用户组</label>
				<div class="col-sm-9 col-xs-12">
					<select name="groupid" class="form-control">
						<option value="0">请选择所属用户组</option>
						<?php  if(is_array($groups)) { foreach($groups as $row) { ?>
						<option value="<?php  echo $row['id'];?>" <?php  if($settings['groupid'] == $row['id']) { ?>selected<?php  } ?>><?php  echo $row['name'];?></option>
						<?php  } } ?>
					</select>
					<span class="help-block">当开启用户注册后，新注册用户将会分配到该用户组里，并直接拥有该组的模块操作权限。</span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-md-offset-2 col-lg-offset-2 col-xs-12 col-sm-9 col-md-10 col-lg-10">
					<input name="submit" type="submit" value="提交" class="btn btn-primary span3" />
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				</div>
			</div>
		</form>
	</div>
	
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>
