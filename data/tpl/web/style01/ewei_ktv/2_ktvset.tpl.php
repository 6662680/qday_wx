<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php  load()->func('tpl')?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo $this->createWebUrl('ktvset')?>">基本设置</a></li>
</ul>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" onsubmit='return check()'>
		<input type="hidden" name="id" value="<?php  echo $set['id'];?>" />
		<div class="panel panel-default">
			<div class="panel-heading">
				基本设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">版本设置</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline">
							<input type="radio" value="0" name="version" class="version" <?php  if($set['version']==0 || empty($set['version'])) { ?>checked<?php  } ?>/> 独立ktv版本
						</label>
						<label class="radio-inline">
							<input type="radio" value="1" name="version" class="version" <?php  if($set['version']==1) { ?>checked<?php  } ?>/> 多个ktv版本
						</label>
						<span class="help-block">请根据您ktv的实际情况选择，不要随意修改</span>
					</div>
				</div>
				<div  id="de_city" class="form-group" <?php  if($set['version'] != 1) { ?>style="display:none"<?php  } ?>>
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">默认城市</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_form_field_district('district',array('province'=>$set['location_p'],'city'=>$set['location_c'],'district'=>$set['location_a']))?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户设置</label>
				<div class="col-sm-9 col-xs-12">
					<label class="radio-inline">
						<input type="radio" value="1" name="user" class="user" <?php  if($set['user']==1 || empty($set['user'])) { ?>checked<?php  } ?>/> 微信粉丝
					</label>
					<label class="radio-inline">
						<input type="radio" value="2" name="user" class="user" <?php  if($set['user']==2) { ?>checked<?php  } ?>/> 独立用户
					</label>
					<span class="help-block">用户是微信粉丝还是独立的用户, 独立用户需要注册</span>
				</div>
			</div>
			<div class="form-group" id='trbind' <?php  if($set['user']!=2) { ?>style="display:none"<?php  } ?>>
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">绑定设置</label>
				<div class="col-sm-9 col-xs-12">
					<label class="radio-inline">
						<input type="radio" value="1" name="bind" class="bind" <?php  if($set['bind']==1 || empty($set['bind'])) { ?>checked<?php  } ?>/> 不绑定
					</label>
					<label class="radio-inline">
						<input type="radio" value="2" name="bind" class="bind" <?php  if($set['bind']==2) { ?>checked<?php  } ?>/> 绑定
					</label>
					<label class="radio-inline">
						<input type="radio" value="3" name="bind" class="bind" <?php  if($set['bind']==3) { ?>checked<?php  } ?>/> 针对用户设置
					</label>
					<span class="help-block">如果选择绑定，用户第一次登录后自动与微信号绑定，其他微信用户无法使用此绑定帐号登录</span>
				</div>
			</div>
			<div class="form-group" id='trreg' <?php  if($set['user']!=2) { ?>style="display:none"<?php  } ?>>
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">注册设置</label>
				<div class="col-sm-9 col-xs-12">
					<label class="radio-inline">
						<input type="radio" value="1" name="reg" class='reg' <?php  if($set['reg']==1 || empty($set['reg'])) { ?>checked<?php  } ?>/> 开启注册
					</label>
					<label class="radio-inline">
						<input type="radio" value="2" name="reg" class='reg' <?php  if($set['reg']==2) { ?>checked<?php  } ?>/> 禁止注册
					</label>
				</div>
			</div>
			<div class="form-group" id='trregcontent' <?php  if($set['user']!=2) { ?>style="display:none"<?php  } ?>>
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">注册说明</label>
				<div class="col-sm-9 col-xs-12">
					<textarea name="regcontent" class="form-control" cols="70"><?php  echo $set['regcontent'];?></textarea>
					<span class="help-block">在禁止注册的时候对用户注册的说明</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">预定类型</label>
				<div class="col-sm-9 col-xs-12">
					<label class='radio-inline'><input type="radio" name="ordertype" value="0" <?php  if($set['ordertype'] == 0) { ?> checked="true" <?php  } ?>>电话预定</label>
					<label class='radio-inline'><input type="radio" name="ordertype" value="1"  <?php  if($set['ordertype'] == 1) { ?> checked="true" <?php  } ?>> 电话预定和网络预定</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">预定电话</label>
				<div class="col-sm-9 col-xs-12">
					<label class='radio-inline'><input type="radio" name="is_unify" class="is_unify" value="0" <?php  if($set['is_unify'] == 0) { ?> checked="true" <?php  } ?>>使用各ktv电话</label>
					<label class='radio-inline'><input type="radio" name="is_unify" class="is_unify" value="1"  <?php  if($set['is_unify'] == 1) { ?> checked="true" <?php  } ?>> 使用统一电话</label>
				</div>
			</div>
			<div class="form-group" <?php  if($set['is_unify'] == 0) { ?>style="display:none"<?php  } ?> id='trtel'>
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">统一电话号码</label>
				<div class="col-sm-9 col-xs-12">
					<input type="text" name="tel" id="tel" value="<?php  echo $set['tel'];?>" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">余额支付</label>
				<div class="col-sm-9 col-xs-12">
					<label class='radio-inline'><input type="radio" name="paytype1" value="1"  <?php  if($set['paytype1'] == 1) { ?> checked="true" <?php  } ?>> 开启</label>
					<label class='radio-inline'><input type="radio" name="paytype1" value="0" <?php  if($set['paytype1'] == 0) { ?> checked="true" <?php  } ?>>关闭</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">在线支付</label>
				<div class="col-sm-9 col-xs-12">
					<label class='radio-inline'><input type="radio" name="paytype2" value="0" <?php  if($set['paytype2'] == 0) { ?> checked="true" <?php  } ?>>关闭</label>
					<label class='radio-inline'><input type="radio" name="paytype2" value="21" <?php  if($set['paytype2'] == 21) { ?> checked="true" <?php  } ?>> 微支付</label>
					<label class='radio-inline'><input type="radio" name="paytype2" value="22" <?php  if($set['paytype2'] == 22) { ?> checked="true" <?php  } ?>> 支付宝</label>
					<label class='radio-inline'><input type="radio" name="paytype2" value="23" <?php  if($set['paytype2'] == 23) { ?> checked="true" <?php  } ?>> 微支付+支付宝</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">到店付款</label>
				<div class="col-sm-9 col-xs-12">
					<label class='radio-inline'><input type="radio" name="paytype3" value="1"  <?php  if($set['paytype3'] == 1) { ?> checked="true" <?php  } ?>> 开启</label>
					<label class='radio-inline'><input type="radio" name="paytype3" value="0" <?php  if($set['paytype3'] == 0) { ?> checked="true" <?php  } ?>>关闭</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">提醒接收邮箱</label>
				<div class="col-sm-9 col-xs-12">
					<input type="text" name="email" class="form-control" value="<?php  echo $set['email'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">提醒接收手机</label>
				<div class="col-sm-9 col-xs-12">
					<input type="text" name="mobile" class="form-control" value="<?php  echo $set['mobile'];?>" />
				</div>
			</div>
		</div>
	</div>
	<div class="form-group col-sm-12">
		<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
	</div>
</form>
</div>
<script language='javascript'>
	function check(){
	}
	$(function(){

		$(".version").click(function(){
			var obj = $(this);
			if(obj.val()=='1'){
				$("#de_city").show();
			}
			else{
				$("#de_city").hide();
			}
		});

		$(".user").click(function(){
			var obj = $(this);
			if(obj.val()=='2'){
				$("#trreg").show();$("#trbind").show();
			}
			else{
				$("#trreg").hide();$("#trbind").hide();
			}
		});

		$(".reg").click(function(){
			var obj = $(this);
			if(obj.val()=='1'){
				$("#trregcontent").hide();
			}
			else{
				$("#trregcontent").show();
			}
		});
		$(".is_unify").click(function(){
			var obj = $(this);
			if(obj.val()=='1'){
				$("#trtel").show();;
			}
			else{
				$("#trtel").hide();;
			}
		});
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>