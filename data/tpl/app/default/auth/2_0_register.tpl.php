<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<script>
	require(['angular', 'bootstrap', 'util'], function(angular, $, u){
		angular.module('app', []).controller('registerPanel', function($scope, $http){
			$scope.label = {
				error: false
			};
			$scope.ret = {
				username : '',
				password : '',
				repassword : ''
			};
			$scope.register = function(){
				var ret = $scope.ret;
				if(ret.username == '' || ret.password == '' || ret.repassword == '') {
					$scope.label.error = true;
					return;
				}
				if(ret.username.length <3) {
					u.message('用户名不能小于三位数');
					return;
				}
				if(ret.password.length < 6) {
					u.message('密码不能小于六位数');
					return;
				}
				if(ret.password != ret.repassword) {
					u.message('两次输入密码不一致');
					return;
				}
				
				$scope.label.error = false;
 				var params = angular.copy(ret);
 				$http.post(location.href, params).success(function(dat){
					if(dat != 'success') {
						u.message(dat);
					} else {
						location.reload();
					}
				});
 			};
		});
		angular.bootstrap(document, ['app']);
	});
</script>
<style>
	body{background:#d2e6e9;}
	.panel{margin:.5em; border:none;}
	.panel-info>.panel-heading {background: -webkit-gradient(linear, 0 0, 100% 0, from(#ebebeb), to(#f3f9fa), color-stop(30%, #f5f9f9)); color:#666666; border:none;}
	a{color:#666666;}a:hover{color: #3ebacc;}
	.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus{color: #3ebacc;}
	.actions{margin:.8em auto;}
	.nav.nav-tabs{margin-bottom:.8em;}
	.btn.btn-primary{background: #56c6d6; color: #FFF; border: 0;}
</style>
<div class="ng-cloak panel panel-info ng-container" ng-controller="registerPanel">
	<div class="panel-heading">
		<?php  if($reregister) { ?>
		<h4>完善注册</h4>
		<span class="help-block">进入“个人中心”时需要您完善注册信息，如果您已经注册过用户请直接 <a style="font-weight:600; text-decoration:underline" href="<?php  echo url('auth/login', array('forward' => $_GPC['forward']));?>#wechat_redirect">登录</a> 绑定。</span>
		<?php  } else { ?>
		<h4>注册用户</h4>
		<?php  } ?>
	</div>
	<div class="panel-body">
	<form name="theform" method="post" role="form" ng-submit="register();">
		<ul class="nav nav-tabs" role="tablist">
			<li class="active"><a role="tab" data-toggle="tab"><?php  if($reregister) { ?>完善注册<?php  } else { ?>注册用户<?php  } ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane animated active">
				<div class="form-group has-feedback" ng-class="{'has-error': label.error}">
					<label class="control-label sr-only"></label>
					<input ng-model="ret.username" type="text" class="form-control" placeholder="<?php  if($item == 'random') { ?>手机号/邮箱<?php  } else if($item == 'email') { ?>请填写邮箱<?php  } else { ?>请填写手机号<?php  } ?>">
					<span class="glyphicon glyphicon-remove form-control-feedback" ng-show="label.error"></span>
				</div>
				<div class="form-group has-feedback" ng-class="{'has-error': label.error}">
					<label class="control-label sr-only"></label>
					<input ng-model="ret.password" type="password" class="form-control" placeholder="密码">
					<span class="glyphicon glyphicon-remove form-control-feedback" ng-show="label.error"></span>
				</div>
				<div class="form-group has-feedback" ng-class="{'has-error': label.error}">
					<label class="control-label sr-only"></label>
					<input ng-model="ret.repassword" type="password" class="form-control" placeholder="确认密码">
					<span class="glyphicon glyphicon-remove form-control-feedback" ng-show="label.error"></span>
				</div>
			</div>
		</div>
		<button type="submit" class="btn btn-primary btn-block">立即注册</button>
	</form>
	<div class="clearfix actions">
		<?php  if($uc_setting['status'] == 1) { ?>
			<a class="pull-left" href="<?php  echo url('auth/login/uc', array('forward' => $_GPC['forward']));?>#wechat_redirect">使用<?php  echo $uc_setting['title'];?>账号登录</a>
			<a class="pull-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
		<?php  } ?>
		<a class="pull-left" href="<?php  echo url('auth/login', array('forward' => $_GPC['forward']));?>#wechat_redirect">用户登录</a>
		<a class="pull-right" href="<?php  echo url('auth/forget', array('forward' => $_GPC['forward']));?>#wechat_redirect">找回密码</a>
	</div>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
