<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
<ol class="breadcrumb">
	<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
	<li><a href="<?php  echo url('system/welcome');?>">系统</a></li>
	<li class="active">查看日志</li>
</ol>
<ul class="nav nav-tabs">
	<li<?php  if($do == 'wechat' || $do == 'detail') { ?> class="active"<?php  } ?>><a href="<?php  echo url('system/logs/wechat');?>">微信日志</a></li>
	<li<?php  if($do == 'system') { ?> class="active"<?php  } ?>><a href="<?php  echo url('system/logs/system');?>">系统日志</a></li>
	<li<?php  if($do == 'database') { ?> class="active"<?php  } ?>><a href="<?php  echo url('system/logs/database');?>">数据库日志</a></li>
</ul>
<div class="clearfix">
	<h5 class="page-header">日志信息</h5>
	<!-- 筛选功能 -->
	<?php  if(($do != 'wechat')) { ?>
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="system">
			<input type="hidden" name="a" value="logs">
			<input type="hidden" name="do" value="<?php  echo $do;?>">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-1 control-label">日期范围</label>
					<div class="col-sm-6 col-lg-8 col-xs-12">
						<?php  echo tpl_form_field_daterange('time', array('starttime'=>$_GPC['time']['start'],'endtime'=>$_GPC['time']['end']));?>
					</div>
					<div class="pull-right col-xs-12 col-sm-1 col-lg-2">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php  } ?>
	
	<!-- 微信日志 -->
	<?php  if(($do == 'wechat')) { ?>
		<div class="panel panel-info">
			<div class="panel-heading">筛选</div>
			<div class="panel-body">
				<form action="./index.php" method="get" class="form-horizontal" role="form">
				<input type="hidden" name="c" value="system">
				<input type="hidden" name="a" value="logs">
				<input type="hidden" name="do" value="<?php  echo $do;?>">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 col-lg-1 control-label">日期选择</label>
						<div class="col-sm-6 col-lg-8 col-xs-12">
							<select name="searchtime" class="form-control">
							<?php  if(is_array($tree)) { foreach($tree as $row) { ?>
							<option value="<?php  echo $row;?>" <?php  if($_GPC['searchtime'] == $row) { ?>selected<?php  } ?>><?php  echo $row;?></option>
							<?php  } } ?>
						</select>
						</div>
						<div class="pull-right col-xs-12 col-sm-1 col-lg-2">
							<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<pre>
		<?php  echo $contents;?>
		</pre>
		<!-- <div class="table-responsive">
			<table class="table table-hover">
				<thead class="navbar-inner">
					<tr>
						<th style="width:350px;">日志类型</th>
						<th>日志文件</th>
						<th>记录时间</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td><a href="#"><?php  echo $item['type'];?></a></td>
						<td><a href="#"><?php  echo $item['filename'];?></a></td>
						<td><?php  echo $item['createtime'];?></td>
						<td>
							<a href="<?php  echo url('system/logs/detail', array('more' => $item['filename']))?>" title="查看详情" class="btn btn-default btn-sm">查看详情</a>
						</td>
					</tr>
					<?php  } } ?>
				</tbody>
			</table>
		</div> -->
	<?php  } ?>
	
	<!-- 系统日志 -->
	<?php  if(($do == 'system')) { ?>
	<div class="panel panel-default">
		<div class="table-responsive panel-body">
			<table class="table table-hover">
				<thead class="navbar-inner">
					<tr>
						<th style="width:150px;">日志类型</th>
						<th style="width:150px;">页面执行时间</th>
						<th style="width:150px;">页面URL</th>
						<th style="width:200px;">日志记录时间</th>
					</tr>
				</thead>
				<tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td><?php  echo $item['type'];?></td>
						<td><?php  echo $item['runtime'];?></td>
						<td><a href="<?php  echo $item['runurl'];?>" target="_blank" ><?php  echo $item['runurl'];?></a></td>
						<td><?php  echo $item['createtime'];?></td>
					</tr>
					<?php  } } ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php  echo $pager;?>
	<?php  } ?>
	
	<!-- 数据据日志 -->
	<?php  if(($do == 'database')) { ?>
	<div class="panel panel-default">
		<div class="table-responsive panel-body">
			<table class="table table-hover">
				<thead class="navbar-inner">
					<tr>
						<th style="width:150px;">日志类型</th>
						<th style="width:150px;">SQL执行时间</th>
						<th style="width:150px;">SQL语句</th>
						<th style="width:200px;">日志记录时间</th>
					</tr>
				</thead>
				<tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td><?php  echo $item['type'];?></td>
						<td><?php  echo $item['runtime'];?></td>
						<td><?php  echo $item['runsql'];?></td>
						<td><?php  echo $item['createtime'];?></td>
					</tr>
					<?php  } } ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php  echo $pager;?>
	<?php  } ?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>
