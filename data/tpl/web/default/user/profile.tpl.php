<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
<ol class="breadcrumb">
	<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
	<li><a href="<?php  echo url('system/welcome');?>">系统</a></li>
	<?php  if($do == 'profile') { ?><li class="active">账号信息</li><?php  } ?>
	<?php  if($do == 'base') { ?><li class="active">基本信息</li><?php  } ?>
</ol>
<ul class="nav nav-tabs">
	<li <?php  if($do == 'profile') { ?>class="active"<?php  } ?>><a href="<?php  echo url('user/profile/profile');?>">账号信息</a></li>
	<li <?php  if($do == 'base') { ?>class="active"<?php  } ?>><a href="<?php  echo url('user/profile/base');?>">基本信息</a></li>
</ul>
<?php  if($do == 'profile') { ?>
	<div class="clearfix">
		<form action="" method="post" class="form-horizontal form" onsubmit="return formcheck(this)">
			<h5 class="page-header">管理员信息修改</h5>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">管理员帐号</label>
				<div class="col-sm-9 col-xs-12">
						<input type="text" name="name" class="form-control" value="<?php  echo $_W['username'];?>" readonly />
						<div class="help-block">只能用'0-9'、'a-z'、'A-Z'、'.'、'@'、'_'、'-'、'!'以内范围的字符</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">管理员密码</label>
				<div class="col-sm-9 col-xs-12">
						<input type="password" name="pw" class="form-control" value="" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label" style="color:red">新密码</label>
				<div class="col-sm-9 col-xs-12">
						<input type="password" name="pw2" class="form-control" value="" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label" style="color:red">确认密码</label>
				<div class="col-sm-9 col-xs-12">
						<input type="password" name="pw3" class="form-control" value="" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
				<div class="col-sm-9 col-xs-12">
						<input name="submit" type="submit" value="提交" class="btn btn-primary" />
						<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				</div>
			</div>
		</form>
	</div>
	<script type="text/javascript">
	function formcheck(form) {
		if (!form['name'].value) {
			alert('请填写管理员帐号！');
			form['name'].focus();
			return false;
		}
		if (!form['pw'].value) {
			alert('请填写管理员密码！');
			form['pw'].focus();
			return false;
		}
		if (!form['pw2'].value) {
			alert('请填写新密码！');
			form['pw2'].focus();
			return false;
		}
		if (form['pw'].value == form['pw2'].value) {
			alert('新密码与原密码一致，请检查！');
			form['pw'].focus();
			return false;
		}
		if (form['pw2'].value.length < 6 ) {
			alert('管理员密码不得小于6个字符！');
			form['pw2'].focus();
			return false;
		}
		if (form['pw2'].value != form['pw3'].value) {
			alert('两次输入的新密码不一致，请重新输入！');
			form['pw2'].focus();
			return false;
		}
	}
	</script>
<?php  } else { ?>
<div class="clearfix">
	<?php  if($extendfields) { ?>
	<form action="" class="form-horizontal form" method="post" enctype="multipart/form-data">
		<h5 class="page-header">基本资料</h5>
<?php  if(is_array($extendfields)) { foreach($extendfields as $item) { ?>
	<?php  if($item['field']=='birthyear') { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><?php  echo $item['title'];?>：<?php  if($item['required']) { ?><span style="color:red">*</span><?php  } ?></label>
			<div class="col-sm-10 col-xs-12">
				<?php  echo tpl_fans_form($item['field'],$profile['birth']);?>
			</div>
		</div>
	<?php  } else if($item['field']=='resideprovince') { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><?php  echo $item['title'];?>：<?php  if($item['required']) { ?><span style="color:red">*</span><?php  } ?></label>
			<div class="col-sm-10 col-xs-12">
				<?php  echo tpl_fans_form($item['field'],$profile['reside']);?>
			</div>
		</div>
	<?php  } else { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"><?php  echo $item['title'];?>：<?php  if($item['required']) { ?><span style="color:red">*</span><?php  } ?></label>
			<div class="col-sm-10 col-xs-12">
				<?php  echo tpl_fans_form($item['field'], $profile[$item['field']]);?>
			</div>
		</div>
	<?php  } ?>
<?php  } } ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
			<div class="col-sm-9 col-xs-12">
				<button type="submit" class="btn btn-primary span3" name="submit" value="提交">提交</button>
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
	<?php  } ?>
</div>

<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>