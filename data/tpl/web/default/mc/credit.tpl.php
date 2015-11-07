<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li<?php  if($do == 'display') { ?> class="active"<?php  } ?>><a href="<?php  echo url('mc/credit/display');?>">积分列表</a></li>
	<li<?php  if($do == 'strategy') { ?> class="active"<?php  } ?>><a href="<?php  echo url('mc/credit/strategy');?>">积分策略</a></li>
</ul>
<?php  if($do == 'display') { ?>
<script type="text/javascript">
require(['jquery', 'util'], function($, u){
	$("#form1").submit(function(){
		var colarr=new Array();
		var col;
		var bool=true;
		$(":checkbox[name^=enabled]:checked").each(function(){
			var key=$(this).attr('name').substr(8,7);
			if($("#"+key).val().trim()==''){
				u.message('启用某个积分后，对应的积分名称不能为空.', '', 'error');
				bool = false;
			}
		})

		$(":text.form-control").each(function(index){
			value=$(this).val().trim();
			if(value !=""){
				for(col in colarr){
					if(colarr[col]==value){
						u.message('积分名称重复.', '', 'error');
						bool = false;
					}		
				}
				colarr[index+1]=value;	
			}	
		})
		if(!bool) return false;
	});
});
</script>
<form action="" method="post" id="form1">
<div class="panel panel-default">
<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:100px;">启用否？</th>
					<th>积分</th>
					<th style="min-width:120px;">积分名称</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($credits)) { foreach($credits as $key => $item) { ?>
					<tr>
						<td  style="vertical-align:middle;">
							<input type="checkbox"  value="1" name="enabled[<?php  echo $key;?>]"<?php  if($key == 'credit1' || $key == 'credit2') { ?>disabled checked<?php  } ?> <?php  if($item['enabled'] == '1') { ?>checked<?php  } ?>/>
						</td>
						<td style="vertical-align:middle;"><?php  echo $key;?></td>
						<td><input type="text" class="form-control" style="width:150px;" id="<?php  echo $key;?>" placeholder="" name="title[<?php  echo $key;?>]" value="<?php  echo $item['title'];?>"></td>
					</tr>
				<?php  } } ?>
			</tbody>
		</table>
</div>
</div>
	<div>
		<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
		<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" />
	</div>
</form>
<?php  } ?>
<?php  if($do == 'strategy') { ?>
<div class="clearfix">
	<div class="alert alert-danger">
		请谨慎修改积分行为参数
	</div>
	<div class="form-horizontal form">
	<form action="" method="post">
		<div class="panel panel-default">
			<div class="panel-heading">
				积分行为参数
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">基本&营销</label>
					<div class="col-sm-9 col-xs-12">
						<select name="activity" class="form-control">
							<?php  if(is_array($credits)) { foreach($credits as $key => $item) { ?>
							<option <?php  if($creditbehaviors['activity']==$key) { ?>  selected <?php  } ?> value="<?php  echo $key;?>"><?php  echo $item['title'];?></option>
							<?php  } } ?>
						</select>
						<span class="help-block">请设置使用系统内置营销功能时, 默认关联的积分类型. (具体功能或模块可能会提供独立的设置选项, 这里设置的是没有特殊选项时系统的默认值)</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">交易&支付(余额)</label>
					<div class="col-sm-9 col-xs-12">
						<select name="currency" class="form-control">
							<?php  if(is_array($credits)) { foreach($credits as $key => $item) { ?>
							<option <?php  if($creditbehaviors['currency']==$key) { ?>  selected <?php  } ?> value="<?php  echo $key;?>"><?php  echo $item['title'];?></option>
							<?php  } } ?>
						</select>
						<span class="help-block">请设置系统支付或者购买时使用的积分, 这个积分一般是使用实际货币购买(充值)的.</span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
			<input type="submit" class="btn btn-primary col-lg-1" onclick="return confirm('您确定修改积分行为参数吗？');" name="submit" value="提交" />
		</div>
	</form>
	</div>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>