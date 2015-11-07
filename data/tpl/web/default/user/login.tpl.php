<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>
<style>
	@media screen and (max-width:767px){.login .panel.panel-default{width:90%; min-width:300px;}}
	@media screen and (min-width:768px){.login .panel.panel-default{width:70%;}}
	@media screen and (min-width:1200px){.login .panel.panel-default{width:50%;}}
</style>
<div class="login">
	<div class="logo">
		<a href="./?refresh" <?php  if(!empty($_W['setting']['copyright']['flogo'])) { ?>style="background:url('<?php  echo tomedia($_W['setting']['copyright']['flogo']);?>') no-repeat;"<?php  } ?>></a>
	</div>
	<div class="clearfix" style="margin-bottom:5em;">
		<div class="panel panel-default container">
			<div class="panel-body">
				<form action="" method="post" role="form" onsubmit="return formcheck();">
					<div class="form-group input-group">
						<div class="input-group-addon"><i class="fa fa-user"></i></div>
						<input name="username" type="text" class="form-control input-lg" placeholder="请输入用户名登录">
					</div>
					<div class="form-group input-group">
						<div class="input-group-addon"><i class="fa fa-unlock-alt"></i></div>
						<input name="password" type="password" class="form-control input-lg" placeholder="请输入登录密码">
					</div>
					<div class="form-group">
						<label class="checkbox-inline input-lg">
							<input type="checkbox" value="true" name="rember"> 记住用户名 
						</label>
						<div class="pull-right">
							<?php  if(!$_W['siteclose']) { ?><a href="<?php  echo url('user/register');?>" class="btn btn-link btn-lg">注册</a><?php  } ?>
							<input type="submit" name="submit" value="登录" class="btn btn-primary btn-lg" />
							<input name="token" value="<?php  echo $_W['token'];?>" type="hidden" />
						</div>
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
<script>
function formcheck() {
	if($('#remember:checked').length == 1) {
		cookie.set('remember-username', $(':text[name="username"]').val());
	} else {
		cookie.del('remember-username');
	}
	return true;
}
require(['jquery'],function($){
	var h = document.documentElement.clientHeight;
	$(".login").css('min-height',h);
});
</script>
</body>
</html>
