<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
<ol class="breadcrumb">
	<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
	<li><a href="<?php  echo url('account/display');?>">公众号列表</a></li>
	<li class="active">编辑主公众号</li>
</ol>
<style>
	.nav-width{border-bottom:0;}
	.nav-width li.active{width:20%;text-align:center;overflow:hidden;height:40px;}
	.nav-width .normal{background:#EEEEEE;width:20%;text-align:center;overflow:hidden;height:40px;}
	.guide em{font-style:normal; color:#d9534f;}
	.guide .list-group .list-group-item a{color:#07d;}
	.guide .list-group .list-group-item{padding-top:20px;}
	.guide .img{margin-bottom:15px; display:inline-block; border:1px solid #cccccc;padding:3px;}
	.guide .con{padding: 10px 30px;}
</style>

<ul class="nav nav-tabs nav-width">
	<li <?php  if($_GPC['step']==1 || $_GPC['step']=='') { ?>class="active"<?php  } ?> class="normal"><a href="javascript:;">1. 账号基本信息</a></li>
	<li <?php  if($_GPC['step']==2) { ?>class="active"<?php  } ?> class="normal"><a href="javascript:;">2. 一键获取公众号信息</a></li>
	<li <?php  if($_GPC['step']==3) { ?>class="active"<?php  } ?> class="normal"><a href="javascript:;">3. 设置公众号信息</a></li>
	<li <?php  if($_GPC['step']==4) { ?>class="active"<?php  } ?> class="normal"><a href="javascript:;">4. 高级设置</a></li>
	<li <?php  if($_GPC['step']==5) { ?>class="active"<?php  } ?> class="normal"><a href="javascript:;">5. 引导页面</a></li>
</ul>

<div class="clearfix">
	<?php  if($_GPC['step'] == 1 || $_GPC['step'] == '') { ?>
	<form action="" method="post"  class="form-horizontal" role="form" enctype="multipart/form-data" id="form1">
		<input type="hidden" name="step" value="2">
		<div class="panel panel-default">
			<div class="panel-heading">
				基础信息
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="name" value="<?php  echo $account['name'];?>" class="form-control" autocomplete="off">
						<span class="help-block">名称为了方便标识此公众号的作用及身份。可以为商户、组织或是公司的名称。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">描述</label>
					<div class="col-sm-9 col-xs-12">
						<textarea style="height: 80px;" class="form-control" name="description"><?php  echo $account['description'];?></textarea>
						<span class="help-block">用于说明此公众号的功能及用途。</span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<input name="submit" type="submit" value="下一步" class="btn btn-primary col-lg-1" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
	<?php  } else if($_GPC['step']== 2) { ?>
	<form action="" method="post"  class="form-horizontal" role="form" id="form2">
		<input type="hidden" name="step" value="3">
		<input type="hidden" name="uniacid" value="<?php  echo $uniacid;?>">
		<input type="hidden" name="name" value="<?php  echo $name;?>">
		<input type="hidden" name="description" value="<?php  echo $description;?>">
	</form>
	<form action="" method="post"  class="form-horizontal" role="form" enctype="multipart/form-data" id="form1">
		<input type="hidden" name="step" value="3">
		<input type="hidden" name="uniacid" value="<?php  echo $uniacid;?>">
		<input type="hidden" name="name" value="<?php  echo $name;?>">
		<input type="hidden" name="description" value="<?php  echo $description;?>">
		<div class="panel panel-default">
			<div class="panel-heading">
				一键获取公众号信息
			</div>
			<div class="panel-body">
			<div class="alert alert-danger">
		<i class="fa fa-exclamation-triangle"></i> 如果无法一键获取到公众号信息，请选择下面的‘跳过’。改为手动输入公众号休息
					</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">类型</label>
					<div class="col-sm-9 col-xs-12">
						<label for="b_radio_3" class="radio-inline"><input type="radio" name="type" id="b_radio_3" value="1" <?php  if(empty($account['type']) || $account['type'] == 1) { ?>checked<?php  } ?> /> 微信</label>
						<label for="b_radio_4" class="radio-inline"><input type="radio" name="type" id="b_radio_4" value="2" <?php  if($account['type'] == 2) { ?>checked<?php  } ?>/> 易信</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">公众登录用户</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="wxusername" id="username" class="form-control" value="<?php  echo $account['username'];?>" autocomplete="off"/>
						<span class="help-block">请输入你的公众平台用户名</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">公众登录密码</label>
					<div class="col-sm-9 col-xs-12">
						<input type="password" name="wxpassword" class="form-control" value="" autocomplete="off"  />
						<span class="help-block">请输入你的公众平台密码</span>
					</div>
				</div>
				<div class="form-group" style="display:none;">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">登录验证码</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="verify" class="txt grid-1 alpha pin form-control" value="" autocomplete="off" />
						<span class="help-inline"><img src="" id="imgverify"> <a href="javascript:;" id="toggle">换一张</a></span>
					</div>
				</div>
				<?php  if(!empty($account['username'])) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
						<div class="col-sm-9 col-xs-12">
							<input name="sync" type="submit" value="同步微信公众平台帐号信息" class="btn span4" />
							<div class="help-block">填写公众号帐号密码后，如果发现信息没有同步成功，请点击此选项进行手动同步。</div>
						</div>
					</div>
				<?php  } ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<a href="javascript:;" onclick="$('#form2').submit();return;" class="btn btn-default" />跳过</a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input name="submit" type="submit" value="下一步" class="btn btn-primary" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
	<?php  } else if($_GPC['step']==3) { ?>
	<form action="" method="post"  class="form-horizontal" role="form" id="form4">
		<input type="hidden" name="step" value="2">
		<input type="hidden" name="uniacid" value="<?php  echo $uniacid;?>">
		<input type="hidden" name="name" value="<?php  echo $name;?>">
		<input type="hidden" name="description" value="<?php  echo $description;?>">
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
		<input type="hidden" name="back" value="<?php  echo $_W['token'];?>">
	</form>

	<form action="" method="post"  class="form-horizontal" role="form" enctype="multipart/form-data" id="form1">
		<input type="hidden" name="step" value="4">
		<input type="hidden" name="flag" value="1">
		<input type="hidden" name="uniacid" value="<?php  echo $uniacid;?>">
		<input type="hidden" name="acid" value="<?php  echo $acid;?>">
		<input type="hidden" name="name" value="<?php  echo $name;?>">
		<input type="hidden" name="description" value="<?php  echo $description;?>">
		<div class="panel panel-default">
			<div class="panel-heading">
				设置公众号信息
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">类型</label>
					<div class="col-sm-9 col-xs-12">
						<label for="b_radio_3" class="radio-inline"><input type="radio" name="type" id="b_radio_3" value="1" <?php  if(empty($account['type']) || $account['type'] == 1) { ?>checked<?php  } ?> /> 微信</label>
						<label for="b_radio_4" class="radio-inline"><input type="radio" name="type" id="b_radio_4" value="2" <?php  if($account['type'] == 2) { ?>checked<?php  } ?>/> 易信</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span> 公众号名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="cname" class="form-control" value="<?php  echo $account['name'];?>" autocomplete="off" />
						<span class="help-block">填写公众号的帐号名称</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">公众号帐号</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="account" class="form-control" value="<?php  echo $account['account'];?>" autocomplete="off" />
						<span class="help-block">填写公众号的帐号，一般为英文帐号</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">原始ID</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="original" class="form-control" value="<?php  echo $account['original'];?>" autocomplete="off" />
						<span class="help-block">在给粉丝发送客服消息时,原始ID不能为空。建议您完善该选项</span>
					</div>
				</div>
				<div id="panel_1">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">级别</label>
						<div class="col-sm-9 col-xs-12">
							<label for="status_1" class="radio-inline"><input autocomplete="off" type="radio" name="level" id="status_1" value="1" <?php  if(empty($account['level']) || $account['level'] == 1) { ?> checked<?php  } ?> > 普通订阅号</label>
							<label for="status_2" class="radio-inline"><input autocomplete="off" type="radio" name="level" id="status_2" value="2" <?php  if($account['level'] == 2) { ?> checked<?php  } ?> /> 普通服务号</label>
							<label for="status_3" class="radio-inline"><input autocomplete="off" type="radio" name="level" id="status_3" value="3" <?php  if($account['level'] == 3) { ?> checked<?php  } ?> /> 认证订阅号</label>
							<label for="status_4" class="radio-inline"><input autocomplete="off" type="radio" name="level" id="status_4" value="4" <?php  if($account['level'] == 4) { ?> checked<?php  } ?> /> 认证服务号</label>
							<span class="help-block">注意：即使公众平台显示为“未认证”, 但只要【公众号设置】/【账号详情】下【认证情况】显示资质审核通过, 即可认定为认证号.</span>
						</div>
					</div>
					<div id="adv">
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">AppId</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" name="key" class="form-control" value="<?php  echo $account['key'];?>" autocomplete="off"/>
								<div class="help-block">请填写微信公众平台后台的AppId</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">AppSecret</label>
							<div class="col-sm-9 col-xs-12">
								<input type="text" name="secret" class="form-control" value="<?php  echo $account['secret'];?>" autocomplete="off"/>
								<div class="help-block">请填写微信公众平台后台的AppSecret, 只有填写这两项才能管理自定义菜单</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label">Oauth 2.0</label>
							<div class="col-sm-9 col-xs-12">
								<p class="form-control-static">在微信公众号请求用户网页授权之前，开发者需要先到公众平台网站的【开发者中心】<b>网页服务</b>中配置授权回调域名。<a href="http://www.qdaygroup.com/wiki/" target="_black">查看详情</a></p>
							</div>
						</div>
					</div>
				</div>
				<div id="panel_2">
				</div>
				<?php  if(!empty($acid)) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">接口地址</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" value="<?php  echo $_W['siteroot'];?>api.php?id=<?php  echo $account['acid'];?>" readonly autocomplete="off"/>
							<div class="help-block">设置“公众平台接口”配置信息中的接口地址</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label" style="color:red">Token</label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group">
								<input type="text" name="wetoken" class="form-control" value="<?php  echo $account['token'];?>" readonly />
								<span class="input-group-addon" style="cursor:pointer" onclick="tokenGen();">生成新的</span>
							</div>
							<div class="help-block">与公众平台接入设置值一致，必须为英文或者数字，长度为3到32个字符. 请妥善保管, Token 泄露将可能被窃取或篡改平台的操作数据.</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label" style="color:red">EncodingAESKey</label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group">
								<input type="text" name="encodingaeskey" class="form-control" value="<?php  echo $account['encodingaeskey'];?>" />
								<span class="input-group-addon" style="cursor:pointer" onclick="EncodingAESKeyGen();">生成新的</span>
							</div>
							<div class="help-block">与公众平台接入设置值一致，必须为英文或者数字，长度为43个字符. 请妥善保管, EncodingAESKey 泄露将可能被窃取或篡改平台的操作数据.</div>
						</div>
					</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">二维码</label>
					<div class="col-sm-9 col-xs-12">
						<input type="file" name="qrcode" value="<?php  echo $item['qrcode'];?>">
						<span class="help-block">只支持JPG图片</span>
					</div>
				</div>
				<?php  if(file_exists(IA_ROOT . '/attachment/qrcode_'.$account['acid'].'.jpg')) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group">
								<img src="<?php  echo $_W['attachurl'];?>/qrcode_<?php  echo $account['acid'];?>.jpg?time=<?php  echo time()?>" class="img-responsive img-thumbnail" width="150" />
							</div>
						</div>
					</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">头像</label>
					<div class="col-sm-9 col-xs-12">
						<input type="file" name="headimg" value="<?php  echo $item['headimg'];?>">
						<span class="help-block">只支持JPG图片</span>
					</div>
				</div>
				<?php  if(file_exists(IA_ROOT . '/attachment/headimg_'.$account['acid'].'.jpg')) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
						<div class="col-sm-9 col-xs-12">
							<div class="input-group">
								<img src="<?php  echo $_W['attachurl'];?>/headimg_<?php  echo $account['acid'];?>.jpg?time=<?php  echo time()?>" class="img-responsive img-thumbnail" width="150" />
							</div>
						</div>
					</div>
				<?php  } ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<?php  if(!empty($account)) { ?>
				<?php  } else { ?>
					<a href="javascript:;" onclick="$('#form4').submit();return false;" class="btn btn-default" />返回上一步</a>
				<?php  } ?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input name="submit" type="submit" value="下一步" class="btn btn-primary" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
	<?php  } else if($_GPC['step']==4) { ?>
	<form action="" method="post"  class="form-horizontal" role="form" enctype="multipart/form-data" id="form1">
		<input type="hidden" name="step" value="4">
		<input type="hidden" name="flag" value="2">
		<input type="hidden" name="uniacid" value="<?php  echo $uniacid;?>">
		<input type="hidden" name="acid" value="<?php  echo $acid;?>">
		<div class="panel panel-default">
			<div class="panel-heading">
				高级设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">当前服务套餐</label>
					<div class="col-sm-9 col-xs-12">
						<select name="groupid" class="form-control">
							<option value="0" <?php  if($account['groupid'] == 0) { ?> selected<?php  } ?> >基础服务</option>
							<?php  if(is_array($group['package'])) { foreach($group['package'] as $item) { ?>
							<option <?php  if($account['groupid'] == $item['id']) { ?> selected<?php  } ?> value="<?php  echo $item['id'];?>"><?php  echo $item['name'];?></option>
							<?php  } } ?>
						</select>
						<span class="help-block">指定公众号可使用的功能及权限。基础服务包含系统模块使用权限。所有服务则表示拥有系统中全部权限。</span>
					</div>
				</div>
				<?php  if($_W['isfounder']) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">服务过期时间</label>
					<div class="col-sm-9 col-xs-12">
						<label for="p_radio_1" class="radio-inline"><input type="radio" name="isexpire" id="p_radio_1" value="1"  onclick="$('#expirepanel').show();"/> 是</label>
						<label for="p_radio_2" class="radio-inline"><input type="radio" name="isexpire" id="p_radio_2" value="0" checked  onclick="$('#expirepanel').hide();"/> 否</label>
						<span class="help-block"></span>
					</div>
				</div>
				<div id="expirepanel" style="display:none">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">过期时间</label>
						<div class="col-sm-9 col-xs-12">
							<?php  echo tpl_form_field_date('endtime', date('Y-m-d H:i', time()), true)?>
							<span class="help-block">用户的使用时间过期时,只能使用'基础服务'服务套餐的功能。</span>
						</div>
					</div>
				</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">短信剩余条数</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="balance" id="balance" value="<?php  echo $notify['sms']['balance'];?>" class="form-control" autocomplete="off">
							<span class="help-block">请填写短信剩余条数,必须为整数。</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">短信签名</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="signature" value="<?php  echo $notify['sms']['signature'];?>" class="form-control" autocomplete="off">		
							<span class="help-block">请填写短信签名。</span>
						</div>
					</div>
				<?php  } ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<input name="submit" type="submit" value="下一步" class="btn btn-primary" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
	<?php  } else if($_GPC['step']==5) { ?>
	<div class="panel panel-default guide">
		<div class="panel-heading">
			高级设置
		</div>
		
		<div class="panel-body">
			<h4 class="alert alert-info">您绑定的微信公众号：<em><?php  echo $account['name'];?></em>，请按照下列引导完成配置。</h4>
			<div class="list-group">
				<?php  if($account['isconnect'] == 0) { ?>
				<div class="list-group-item">
					<h5 class="page-header">登录 <a href="https://mp.weixin.qq.com/" target="_blank">微信公众平台</a>，点击左侧菜单最后一项，进入 [ <em>开发者中心</em> ]</h5>
					<div class="con">
						<div class="img"><img src="resource/images/guide-01.png"/></div>
						<p># 如果您未成为开发者，请勾选页面上的同意协议，再点击 [ <em>成为开发者</em> ] 按钮</p>
					</div>
				</div>
				<div class="list-group-item">
					<h5 class="page-header">在开发者中心，找到［<em> 服务器配置</em> ］栏目下URL和Token设置</h5>
					<div class="con">
						<div class="img"><img src="resource/images/guide-02.png"/></div>
						<p># 将以下链接链接填入对应输入框：</p>
						<form action="" method="post" class="form-horizontal" role="form">
							<div class="form-group">
								<label class="col-xs-12 col-sm-2 col-md-1 col-lg-1 control-label">URL:</label>
								<div class="col-sm-9 col-xs-12 input-group ">
									<p class="form-control-static"><a href="javascript:;" title="点击复制Token"><?php  echo $_W['siteroot'];?>api.php?id=<?php  echo $account['acid'];?></a></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-12 col-sm-2 col-md-1 col-lg-1 control-label">Token:</label>
								<div class="col-sm-9 col-xs-12 input-group">
									<p class="form-control-static"> <a href="javascript:;" title="点击复制Token"><?php  echo $account['token'];?></a></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-12 col-sm-2 col-md-1 col-lg-1 control-label">EncodingAESKey:</label>
								<div class="col-sm-10 input-group">
									<p class="form-control-static"> <a href="javascript:;" title="点击复制EncodingAESKey"><?php  echo $account['encodingaeskey'];?></a></p>
								</div>
							</div>
						</form>
						<p># 如果以前已填写过URL和Token，请点击[<em> 修改配置 </em>] ，再填写上述链接</p>
						<p># 请点击 [<em> 启用 </em>] ，以启用服务器配置：</p>
						<div class="img"><img src="resource/images/guide-03.png" width="524"></div>
					</div>
				</div>
				<?php  } ?>
				<div class="list-group-item">
					<h5 class="page-header">
						<?php  if($account['isconnect'] == 1) { ?>
							<em>公众号 <?php  echo $account['name'];?> 接入成功</em>
						<?php  } else { ?>
						
							<em>公众号 <?php  echo $account['name'];?> 正在等待接入……请及时按照以上步骤操作接入公众平台</em>
						<?php  } ?>
					</h5>
					<div class="con">
						<?php  if($account['isconnect'] == 0) { ?>
							<p># 检查公众平台配置</p>
							<p># 编辑公众号 <a href="<?php  echo url('account/bind/post', array('acid' => $acid, 'uniacid' => $account['uniacid']))?>"><?php  echo $account['name'];?></a></p>
							<a href="javascript:window.location.reload();" class="btn btn-success" style="color:#FFF">检测是否接入成功</a>&nbsp;&nbsp;&nbsp;<a href="<?php  echo url('account/switch', array('uniacid' => $uniacid))?>" class="btn btn-primary" style="color:#FFF">暂不接入，先去查看管理功能</a>
							<a href="<?php  echo url('')?>" class="btn btn-info" style="color:#FFF">返回公众号列表</a>
						<?php  } else { ?>
							<a href="<?php  echo url('account/switch', array('uniacid' => $uniacid))?>" class="btn btn-primary" style="color:#FFF">管理公众号</a>
						<?php  } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php  } ?>
</div>
<script type="text/javascript">
	require(['jquery', 'util', 'filestyle'], function($, u){
		$(function(){
			$('p.form-control-static a').each(function(){
				u.clip(this, $(this).text());
			});
			$('#username').blur(function(){
				if ($('#username').val()) {
					var type = $('input[name="type"]:checked').val() ? $('input[name="type"]:checked').val() : 1;
					$('#imgverify').attr('src', '<?php  echo url("utility/wxcode")?>username=' + $('#username').val()+'&r='+Math.round(new Date().getTime()));
					$('#imgverify').parent().parent().parent().show();
					return false;
				}
			});
			$('#toggle').click(function(){
				if ($('#username').val()) {
					var type = $('input[name="type"]:checked').val() ? $('input[name="type"]:checked').val() : 1;
					$('#imgverify').attr('src', '<?php  echo url("utility/wxcode")?>username=' + $('#username').val()+'&r='+Math.round(new Date().getTime()));
					$('#imgverify').parent().parent().parent().show();
					return false;
				}
			});
			
			$(".form-group").find(':file').filestyle({buttonText: '上传图片'});
			
			$('div[id^="panel"]').hide();
			$('#panel_'+$('input:radio[name="type"]:checked').val()).show();
			$("input[name='type']").click(function(){
				$('div[id^="panel"]').hide();
				$('#panel_'+$(this).val()).show();
			});
		});
	});
	function tokenGen() {
		var letters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		var token = '';
		for(var i = 0; i < 32; i++) {
			var j = parseInt(Math.random() * (31 + 1));
			token += letters[j];
		}
		$(':text[name="wetoken"]').val(token);
	}

	function EncodingAESKeyGen() {
		var letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		var token = '';
		for(var i = 0; i < 43; i++) {
			var j = parseInt(Math.random() * 61 + 1);
			token += letters[j];
		}
		$(':text[name="encodingaeskey"]').val(token);
	}

</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>