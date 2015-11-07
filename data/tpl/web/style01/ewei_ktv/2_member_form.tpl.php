<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<div class="main">
	<ul class="nav nav-tabs">
		<li><a href="<?php  echo $this->createWebUrl('member');?>">用户列表</a></li>
		<li<?php  if($op=='edit' && empty($id)) { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('member',array('op'=>'edit'));?>">添加用户</a></li>
		<?php  if($op=='edit' && !empty($id)) { ?><li class="active"><a href="<?php  echo $this->createWebUrl('member',array('op'=>'edit','id'=>$id));?>">编辑用户</a></li><?php  } ?>
	</ul>
	<form action="" class="form-horizontal form" method="post" onsubmit="return formcheck()">
		<input type="hidden" name="id" value="<?php  echo $item['id'];?>">
		<div class="panel panel-default">
			<div class="panel-heading">
				用户基本信息
			</div>
			<div class="panel-body">
				<?php  if(!empty($item)) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">微信号</label>
					<div class="col-sm-9 col-xs-12">
						<span class='form-control-static'><?php  echo $item['from_user'];?></span>
					</div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">姓名</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="realname" id="realname" value="<?php  echo $item['realname'];?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户名</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="username" id="username" value="<?php  echo $item['username'];?>" class="form-control" <?php  if(!empty($item)) { ?>readonly<?php  } ?>>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">手机</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="mobile" id="mobile" value="<?php  echo $item['mobile'];?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><?php  if(!empty($id)) { ?>新<?php  } ?>密码</label>
					<div class="col-sm-9 col-xs-12">
						<input type="password" name="password" id="password" value="" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">确认<?php  if(!empty($id)) { ?>新<?php  } ?>密码</label>
					<div class="col-sm-9 col-xs-12">
						<input type="password" name="password2" id="password2" value="" class="form-control">
					</div>
				</div>
				<?php  if(!empty($item)) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">订房积分</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="score" id="score" value="<?php  echo $item['score'];?>" class="form-control">
					</div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否绑定微信号</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" name="isauto" class="isauto_radio" value="1" <?php  if($item['isauto'] == 1) { ?>checked<?php  } ?>/>微信用户
						</label>
						<label class="radio-inline">
							<input type="radio" name="isauto" class="isauto_radio" value="0" <?php  if($item['isauto'] == 0) { ?>checked<?php  } ?>/>会员用户
						</label>
						<span class='help-block'>会员用户显示房型的会员价，微信用户显示房型的优惠价</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户类型</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" name="userbind" value="1" <?php  if($item['userbind'] == 1) { ?>checked<?php  } ?>/>绑定
						</label>
						<label class="radio-inline">
							<input type="radio" name="userbind" value="0" <?php  if($item['userbind'] == 0) { ?>checked<?php  } ?>/>不绑定
						</label>
						<span class='help-block'>绑定之后，此账户与微信用户绑定，帐号不能从其他微信登录</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" name="status" value="1" <?php  if($item['status'] == 1) { ?>checked<?php  } ?>/>启用
						</label>
						<label class="radio-inline">
							<input type="radio" name="status" value="0" <?php  if($item['status'] == 0) { ?>checked<?php  } ?>/>禁用
						</label>
						<span class='help-block'>禁用以后用户无法登录</span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
	<script type="text/javascript">
		//kindeditor($('.richtext-clone'));
		function formcheck() {
			var isauto = $('input[name="isauto"]:checked').val();
			if (isauto == 0) {
				if ($("#realname").isEmpty()) {
					Tip.select("realname", "请填写姓名!", "right");
					return false;
				}
				if ($("#username").isEmpty()) {
					Tip.select("username", "请填写用户名!", "right");
					return false;
				}
				if (!$("#mobile").isMobile()) {
					Tip.select("mobile", "请填写正确的手机!", "right");
					return false;
				}
			}
			var check_pass = 1;
			<?php  if(!empty($id)) { ?>
				if ($("#password").isEmpty() && $("#password2").isEmpty()) {
					check_pass = 0;
				}
				<?php  } ?>
					if (check_pass) {
						if ($("#password").isEmpty()) {
							Tip.select("password", "请填写<?php  if(!empty($id)) { ?>新<?php  } ?>密码!", "right");
							return false;
						}
						if ($("#password2").isEmpty()) {
							Tip.select("password2", "请填写确认<?php  if(!empty($id)) { ?>新<?php  } ?>密码!", "right");
							return false;
						}
						if ($.trim($("#password").val()) != $.trim($("#password2").val())  ) {
							Tip.select("password2", "两次密码不一致，请确认!", "right");
							return false;
						}
					}
					return true;
				}
	</script>

