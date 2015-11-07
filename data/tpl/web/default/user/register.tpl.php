<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>
<script>
require(['jquery', 'util'], function($, u){
	$('#form1').submit(function(){
		if($.trim($(':text[name="username"]').val()) == '') {
			u.message('没有输入用户名.', '', 'error');
			return false;
		}
		if($('#password').val() == '') {
			u.message('没有输入密码.', '', 'error');
			return false;
		}
		if($('#password').val() != $('#repassword').val()) {
			u.message('两次输入的密码不一致.', '', 'error');
			return false;
		}
/* 		<?php  if(is_array($extendfields)) { foreach($extendfields as $item) { ?>
		<?php  if($item['required']) { ?>
			if (!$.trim($('[name="<?php  echo $item['field'];?>"]').val())) {
				u.message('<?php  echo $item['title'];?>为必填项，请返回修改！', '', 'error');
				return false;
			}
		<?php  } ?>
		<?php  } } ?>
 */		<?php  if($setting['register']['code']) { ?>
		if($.trim($(':text[name="code"]').val()) == '') {
			u.message('没有输入验证码.', '', 'error');
			return false;
		}
		<?php  } ?>
	});
});
require(['jquery'],function($){
	var h = document.documentElement.clientHeight;
	$(".login").css('min-height',h);
});
</script>
<style>
	@media screen and (max-width:767px){.register .panel.panel-default{width:90%; min-width:300px;}}
	@media screen and (min-width:768px){.register .panel.panel-default{width:70%;}}
	@media screen and (min-width:1200px){.register .panel.panel-default{width:50%;}}
</style>
<div class="register">
	<div class="logo"><a href="./?refresh" <?php  if(!empty($_W['setting']['copyright']['flogo'])) { ?>style="background:url('<?php  echo tomedia($_W['setting']['copyright']['flogo']);?>') no-repeat;"<?php  } ?>></a></div>
	<div class="clearfix" style="margin-bottom:5em;">
		<div class="panel panel-default container">
			<div class="panel-body">
				<form action="" method="post" role="form" id="form1">
					<div class="form-group">
						<label>用户名:<span style="color:red">*</span></label>
						<input name="username" type="text" class="form-control" placeholder="请输入用户名">
					</div>
					<div class="form-group">
						<label>密码:<span style="color:red">*</span></label>
						<input name="password" type="password" id="password" class="form-control" placeholder="请输入密码">
					</div>
					<div class="form-group">
						<label>确认密码:<span style="color:red">*</span></label>
						<input name="password" type="password" id="repassword" class="form-control" placeholder="请再次输入密码">
					</div>
					<?php  if($extendfields) { ?>
						<?php  if(is_array($extendfields)) { foreach($extendfields as $item) { ?>
							<div class="form-group">
								<label><?php  echo $item['title'];?>：<?php  if($item['required']) { ?><span style="color:red">*</span><?php  } ?></label>
								<?php  echo tpl_fans_form($item['field'])?>
							</div>
						<?php  } } ?>
					<?php  } ?>
					<?php  if($setting['register']['code']) { ?>
						<div class="form-group">
							<label style="display:block;">验证码:<span style="color:red;">*</span></label>
							<input name="code" type="text" class="form-control" placeholder="请输入验证码" style="width:65%;display:inline;margin-right:17px">
							<img src="<?php  echo url('utility/code');?>" class="img-rounded" style="cursor:pointer;" onclick="this.src='<?php  echo url('utility/code');?>' + Math.random();" />
						</div>
					<?php  } ?>
					<!--div class="form-group">
						<label>邀请码:<span style="color:red">*</span></label>
						<input name="invitation" type="text" class="form-control" placeholder="请输入邀请码">
					</div-->
					<div class="pull-right">
						<a href="<?php  echo url('user/login');?>" class="btn btn-link">登录</a>
						<input type="submit" name="submit" value="注册" class="btn btn-default" />
						<input name="token" value="<?php  echo $_W['token'];?>" type="hidden" />
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="center-block footer" role="footer">
		<div class="text-center">
			<?php  if(empty($_W['setting']['copyright']['footerright'])) { ?><a href="http://www.qdaygroup.com">关于情天</a>&nbsp;&nbsp;<a href="http://www.qdaygroup.com">情天帮助</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerright'];?><?php  } ?> &nbsp; &nbsp; <?php  if(!empty($_W['setting']['copyright']['statcode'])) { ?><?php  echo $_W['setting']['copyright']['statcode'];?><?php  } ?>
		</div>
		<div class="text-center">
			<?php  if(empty($_W['setting']['copyright']['footerleft'])) { ?>Powered by <a href="http://www.qdaygroup.com"><b>情天</b></a> v<?php echo IMS_VERSION;?> &copy; 2014 <a href="http://www.qdaygroup.com">www.qdaygroup.com</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerleft'];?><?php  } ?>
		</div>
	</div>
</div>
</body>
</html>