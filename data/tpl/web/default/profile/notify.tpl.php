<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li<?php  if($do == 'mail') { ?> class="active"<?php  } ?>><a href="<?php  echo url('profile/notify/mail')?>">邮件通知</a></li>
	<li<?php  if($do == 'wechat') { ?> class="active"<?php  } ?>><a href="<?php  echo url('mc/mass')?>">微信通知</a></li>
</ul>
<script type="text/javascript">
	<?php  if($notify['mail']['smtp']['type'] == 'custom') { ?>
		$("#smtp").show();
	<?php  } ?>
</script>
<div class="main">
	<form id="payform" action="<?php  echo url('profile/notify')?>" method="post" class="form-horizontal form">
	<?php  if($do == 'mail') { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				邮件通知选项
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">STMP服务器</label>
					<div class="col-sm-9 col-xs-12">
						<label for="radio_3" class="radio-inline"><input type="radio" name="smtp[type]" id="radio_3" value="163" <?php  if($notify['mail']['smtp']['type'] == '163' || empty($notify['mail']['smtp']['type'])) { ?> checked<?php  } ?> onclick="$('#smtp').hide();"/> 网易邮箱服务器（建议使用）</label>
						<label for="radio_4" class="radio-inline"><input type="radio" name="smtp[type]" id="radio_4" value="custom" <?php  if($notify['mail']['smtp']['type'] == 'custom') { ?> checked<?php  } ?> onclick="$('#smtp').show();" /> 自定义</label>
						<div class="help-block">SMTP服务器为发送邮件的服务器，系统内置了网易的邮件服务器的信息，可直接使用。如果有特殊需要请自定义SMTP服务器</div>
					</div>
				</div>
				<div class="tb" id="smtp" style="display:none;">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">SMTP服务器地址</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="smtp[server]" class="form-control" value="<?php  echo $notify['mail']['smtp']['server'];?>" />
							<div class="help-block">指定SMTP服务器的地址</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">SMTP服务器端口</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="smtp[port]" class="form-control" value="<?php  echo $notify['mail']['smtp']['port'];?>" />
							<div class="help-block">指定SMTP服务器的端口</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">使用SSL加密</label>
						<div class="col-sm-9 col-xs-12">
							<label for="radio_5" class="radio-inline"><input type="radio" name="smtp[authmode]" id="radio_5" value="1" <?php  if(!empty($notify['mail']['smtp']['qq'])) { ?> checked<?php  } ?> /> 是</label>
							<label for="radio_6" class="radio-inline"><input type="radio" name="smtp[authmode]" id="radio_6" value="0" <?php  if(empty($notify['mail']['smtp']['authmode'])) { ?> checked<?php  } ?> /> 否</label>
							<div class="help-block">开启此项后，连接将用SSL的形式，此项需要SMTP服务器支持</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">发送帐号用户名</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="username" class="form-control" value="<?php  echo $notify['mail']['username'];?>" />
						<div class="help-block">指定发送邮件的用户名，例如：test@163.com</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">smtp客户端授权密码</label>
					<div class="col-sm-9 col-xs-12">
						<input type="password" name="password" class="form-control" value="<?php  echo $notify['mail']['password'];?>" />
						<div class="help-block">指定发送邮件的密码</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">发件人名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="sender" class="form-control" value="<?php  echo $notify['mail']['sender'];?>" />
						<div class="help-block">指定发送邮件发信人名称</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">邮件签名</label>
					<div class="col-sm-9 col-xs-12">
						<textarea id="signature" style="height:150px;" name="signature" class="form-control" cols="60"><?php  echo $notify['mail']['signature'];?></textarea>
						<div class="help-block">指定邮件末尾添加的签名信息</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">测试接收人</label>
					<div class="col-sm-9 col-xs-12">
						<label for="radio_7" class="checkbox-inline">
							<input type="checkbox" name="testsend" id="radio_7" value="1" checked onclick="$(':text[name=receiver]').toggle();" /> 保存后测试邮件
						</label>
						<input type="text" name="receiver" class="form-control" />
						<div class="help-block">你可以指定一个收件邮箱, 系统将在保存参数成功后尝试发送一条测试性的邮件, 来检测邮件通知是否正常工作</div>
					</div>
				</div>
				<input type="hidden" name="do" value="mail">
			</div>
		</div>
	<?php  } ?>

	<?php  if($do != 'sms') { ?>
		<div class="form-group col-sm-12">
			<button type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">提交</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	<?php  } ?>
	</form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
