<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo url('mc/creditmanage/display')?>">会员积分</a></li>
</ul>
<?php  if($do=='display') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form" id="form">
		<input type="hidden" name="c" value="mc">
		<input type="hidden" name="a" value="creditmanage">
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字类型</label>
				<div class="col-sm-8 col-xs-12">
					<select name="type" class="form-control">
						<option value="4" <?php  if($_GPC['type'] == 4) { ?>selected<?php  } ?>>真实姓名</option>
						<option value="3" <?php  if($_GPC['type'] == 3) { ?>selected<?php  } ?>>昵称</option>
						<option value="2" <?php  if($_GPC['type'] == 2) { ?>selected<?php  } ?>>手机号</option>
						<option value="1" <?php  if($_GPC['type'] == 1 || empty($_GPC['type'])) { ?>selected<?php  } ?>>用户UID</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="keyword" id="keyword" value="<?php  echo $_GPC['keyword'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">积分范围</label>
				<div class="col-sm-4 col-xs-12">
					<input type="text" class="form-control" name="minimal" value="<?php  echo $_GPC['minimal'];?>" placeholder="请输入最小值" />
				</div>
				<div class="col-sm-4 col-xs-12">
					<input type="text" class="form-control" name="maximal" value="<?php  echo $_GPC['maximal'];?>" placeholder="请输入最大值" />
				</div>
				<div class="pull-right col-xs-12 col-sm-2 col-md-2 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="alert alert-info" role="alert"><i class="fa fa-exclamation-circle"></i> 如果扫描来自会员中心的付款码信息，请选择关键字类型为用户UID，然后根据扫码结果搜索满足条件的用户。</div>
<div class="panel panel-default">
<div class="panel-body table-responsive">
	<table class="table table-hover">
		<input type="hidden" name="do" value="del" />
		<thead class="navbar-inner">
			<tr>
				<th style="min-width:44px;">会员编号</th>
				<th style="min-width:44px;">昵称</th>
				<th style="min-width:60px;">真实姓名</th>
				<th style="min-width:100px;">手机</th>
				<th>邮箱</th>
				<?php  if(is_array($creditnames)) { foreach($creditnames as $creditname) { ?>
				<th style="min-width:45px;"><?php  echo $creditname['title'];?></th>
				<?php  } } ?>
				<th>操作</th>
			</tr>
		</thead>
		<?php  if(is_array($list)) { foreach($list as $li) { ?>
		<thead>
			<tr>
				<td style="vertical-align:middle"><?php  echo $li['uid'];?></td>
				<td style="vertical-align:middle"><?php  echo $li['nickname'];?></td>
				<td style="vertical-align:middle"><?php  echo $li['realname'];?></td>
				<td style="vertical-align:middle"><?php  echo $li['mobile'];?></td>
				<td style="vertical-align:middle"><?php  echo $li['email'];?></td>
				<?php  if(is_array($creditnames)) { foreach($creditnames as $index => $creditname) { ?>
				<td style="vertical-align:middle"><?php  echo $li[$index];?></td>
				<?php  } } ?>
				<td>
					<a href="javascript:void(0)" id="<?php  echo $li['uid'];?>" class="btn btn-default btn-sm recharge" data-toggle="tooltip" data-placement="top" title="充值"><i class="fa fa-dollar"></i></a>
					<a href="<?php  echo url('mc/creditmanage/stat', array('type' => '1', 'uid' => $li['uid']))?>" id="<?php  echo $li['uid'];?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="积分变动日志"><i class="fa fa-clock-o"></i></a>
				</td>
			</tr>
		</thead>
		<?php  } } ?>
	</table>
</div>
</div>
<?php  echo $pager;?>
<div id="footer" class="hide">
		<span name="submit" id="submit" class="pull-right btn btn-primary" onclick="$('#form1').submit();">保存</span>
</div>
<script>
	require(['jquery', 'util'], function($, u){
		$('.recharge').click(function(){
			var uid = parseInt($(this).attr('id'));
			$.get("<?php  echo url('mc/creditmanage/modal')?>&uid=" + uid, function(data){
				if(data == 'dataerr') {
					u.message('未找到指定会员', '', 'error');
				} else {
					var obj = u.dialog('会员积分操作', data, $('#footer').html());
				}
				obj.modal('show');
			});
		})
		$('#keyword').focus().select().change(function(){
			$('#form').submit();
		});
		var status = "<?php  echo $status;?>";
		if(status == 1) {
			var uid = "<?php  echo $uid;?>";
			$.get("<?php  echo url('mc/creditmanage/modal')?>&uid=" + uid, function(data){
				if(data == 'dataerr') {
					u.message('未找到指定会员', '', 'error');
				} else {
					var obj = u.dialog('会员积分操作', data, $('#footer').html());
				}
				obj.modal('show');
			});
		}
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>