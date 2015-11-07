<?php defined('IN_IA') or exit('Access Denied');?><?php  $newUI = true;?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li<?php  if($do == 'display') { ?> class="active"<?php  } ?>><a href="<?php  echo url('mc/broadcast')?>"><i class="icon-group"></i> 发送通知消息</a></li>
</ul>
<?php  if(!$_W['ispost'] || empty($count)) { ?>
<div class="main">
	<form action="<?php  echo url('mc/broadcast');?>" method="post" class="form-horizontal form">
		<div class="panel panel-default">
			<div class="panel-heading">
				批量发送通知
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">通知方式</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" name="type" value="email"<?php  if($_GPC['type'] == 'email' || empty($_GPC['type'])) { ?> checked="checked"<?php  } ?>/>
							邮件
						</label>
						<label class="radio-inline">
							<input type="radio" name="type" value="sms"<?php  if($_GPC['type'] == 'sms') { ?> checked="checked"<?php  } ?>/>
							短信
						</label>
						<label class="radio-inline">
							<input type="radio" name="type" value="wechat"<?php  if($_GPC['type'] == 'wechat') { ?> checked="checked"<?php  } ?> onclick="location.href='<?php  echo url("mc/mass")?>'"/>
							微信
						</label>
						<label class="radio-inline">
							<input type="radio" name="type" value="yixin"<?php  if($_GPC['type'] == 'yixin') { ?> checked="checked"<?php  } ?>/>
							易信
						</label>
						<label class="radio-inline">
							<input type="radio" name="type" value="app"<?php  if($_GPC['type'] == 'app') { ?> checked="checked"<?php  } ?>/>
							APP
						</label>
						<span class="help-block">请指定你要发送通知的方式, 不同的方式能到达的用户也不同</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户组</label>
					<div class="col-sm-9 col-xs-12">
						<select name="group" class="form-control">
							<option value="">不限</option>
							<?php  if(is_array($groups)) { foreach($groups as $group) { ?>
							<option value="<?php  echo $group['groupid'];?>"<?php  if($_GPC['group'] == $group['groupid']) { ?> selected="selected"<?php  } ?>><?php  echo $group['title'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" class="form-control" name="username" value="<?php  echo $_GPC['username'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
						<input type="submit" class="btn btn-primary" value="筛选用户" />
						<?php  if(isset($count) && empty($count)) { ?>
						<span class="help-block">没有查找到符合条件的用户, 请更换条件</span>
						<?php  } ?>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php  } else { ?>
<div class="main" ng-controller="doNotifySend">
	<form action="<?php  echo url('mc/broadcast');?>" method="post" class="form-horizontal form">
		<div class="panel panel-default">
			<div class="panel-heading">通知目标</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-10 col-xs-12">
						已经搜索到 <?php  echo $count;?> 位用户 &nbsp; <a href="javascript:;" onclick="history.go(-1);">重新搜索</a>
					</div>
				</div>
				<?php  if($_GPC['type'] == 'email') { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">邮件标题</label>
					<div class="col-sm-10 col-xs-12">
						<input type="text" ng-model="params.title" class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">邮件内容</label>
					<div class="col-sm-10 col-xs-12">
						<?php  load()->func('tpl')?>
						<?php  echo tpl_ueditor('editor')?>
					</div>
				</div>
				<?php  } ?>
				<?php  if($_GPC['type'] == 'sms') { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">短信内容</label>
					<div class="col-sm-10 col-xs-12">
						<textarea class="form-control content"></textarea>
						<span class="help-block">请确认你的短信格式已经符合审核模板, 否则将会发送失败(已购买的数量不会退还). 为避免损失, 请联系管理员确认, 并先使用少量人员群发测试</span>
					</div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-10 col-xs-12">
						<input type="button" class="btn btn-primary" value="群发通知" ng-disabled="isRunning" ng-click="send(1);" />
						<span class="help-block label-result"></span>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script>
	var editor = UE.getEditor('editor');
	var running = false;
	var success = failed = 0;
	window.onbeforeunload = function(e) {
		if(running) {
			return (e || window.event).returnValue = '正在进行群发操作, 离开页面将会中止执行.';
		}
	}
	require(['angular'], function(angular){
		angular.module('app', []).controller('doNotifySend', function($scope, $http) {
			$scope.params = {
				type : '<?php  echo $_GPC['type'];?>',
				group : '<?php  echo $_GPC['group'];?>',
				username : '<?php  echo $_GPC['username'];?>'
			};
			$scope.isRunning = running = false;
			
			$scope.send = function(pindex) {
				var params = {};
				var params = angular.copy($scope.params);
				<?php  if($_GPC['type'] == 'email') { ?>
				params.title = $scope.params.title ? $scope.params.title : '';
				params.content = editor.getContent();
				if(params.title == '' || params.content == '') {
					util.message('请输入完整的通知内容.', '', 'error');
					return;
				}
				<?php  } ?>
				<?php  if($_GPC['type'] == 'sms') { ?>
				params.content = angular.element('.content').val();
				if(params.content == '') {
					util.message('请输入通知内容.', '', 'error');
					return;
				}
				if(!confirm('请确认你的短信格式已经符合审核模板, 否则将会发送失败(已购买的数量不会退还). 为避免损失, 请联系管理员确认, 并先使用少量人员群发测试. 确定要继续吗?')) {
					return;
				}
				<?php  } ?>
				<?php  if($_GPC['type'] == 'sms') { ?>
				<?php  } ?>
				<?php  if($_GPC['type'] == 'sms') { ?>
				<?php  } ?>
				<?php  if($_GPC['type'] == 'sms') { ?>
				<?php  } ?>
				<?php  if($_GPC['type'] == 'sms') { ?>
				<?php  } ?>
				$scope.isRunning = running = true;
				params.method = 'send';
				params.next = pindex;
				if(pindex == 1) {
					success = failed = 0;
					var label = '正在发送中, 总共 <?php  echo $count;?>';
					angular.element('.label-result').html(label);
				}
				
				$http.post(location.href, params).success(function(dat, status){
					if(!angular.isObject(dat)) {
						util.message('执行错误, 请稍后重试', location.href);
						return;
					}
					
					success += dat.success;
					failed += dat.failed;
					var label = '正在发送中, 总共 ' + dat.total + ', 发送成功 ' + success + ', 发送失败 ' + failed;
					angular.element('.label-result').html(label);
					if(dat.total <= (success + failed) || dat.next == -1) {
						$scope.isRunning = running = false;
						if(dat.total <= failed) {
							util.message('没有发送成功任何通知消息, 请检查配置项', '', 'error');
						}
					} else {
						$scope.send(dat.next);
					}
				});
			};
		});
		angular.bootstrap(document, ['app']);
	});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
