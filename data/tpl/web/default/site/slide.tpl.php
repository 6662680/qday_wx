<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($do == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo url('site/slide/display', array('mtid' => $_GPC['mtid'], 'f' => $_GPC['f']));?>">管理</a></li>
	<li <?php  if($do == 'post' && empty($id)) { ?>class="active"<?php  } ?>><a href="<?php  echo url('site/slide/post', array('mtid' => $_GPC['mtid'], 'f' => $_GPC['f']));?>">添加</a></li>
	<?php  if($do == 'post' && $id) { ?>
	<li <?php  if($do == 'post' && !empty($id)) { ?>class="active"<?php  } ?>><a href="<?php  echo url('site/slide/post', array('id'=>$id, 'mtid' => $_GPC['mtid'], 'f' => $_GPC['f']));?>">编辑</a></li>
	<?php  } ?>
</ul>
<style>
.table td span{display:inline-block;margin-top:4px;}
.table td input{margin-bottom:0;}
</style>
<?php  if($do == 'display') { ?>
<script>
	require(['bootstrap'],function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});
</script>
<div class="main">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="site">
			<input type="hidden" name="a" value="slide">
			<input type="hidden" name="do" value="display"/>
			<input type="hidden" name="f" value="<?php  echo $_GPC['f'];?>" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">所属微站</label>
					<div class="col-sm-7 col-lg-8 col-md-8 col-xs-12">
						<select class="form-control" name="mtid" id="search">
							<?php  if(is_array($multis)) { foreach($multis as $multi) { ?>
							<option value="<?php  echo $multi['id'];?>" <?php  if($multi['id'] == $_GPC['mtid'] || ($multi['id'] == $default_site && $_GPC['mtid'] == '')) { ?>selected<?php  } ?>><?php  echo $multi['title'];?><?php  if($default_site == $multi['id']) { ?>[默认微站]<?php  } ?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
				<div class="form-group clearfix">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
					<div class="col-sm-7 col-lg-8 col-md-8 col-xs-12">
						<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
					</div>
					<div class="col-xs-12 col-sm-3 col-md-2 col-lg-1">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<form class="form-horizontal" action="" method="post">
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th style="width:120px;">排序</th>
						<th>标题</th>
						<th>所属微站</th>
						<th style="width:100px; text-align:right;">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td style="width:100px;"><input type="text" class="form-control"  name="displayorder[<?php  echo $item['id'];?>]" value="<?php  echo $item['displayorder'];?>" /></td>
						<td style="vertical-align:middle"><?php  echo $item['title'];?></td>
						<td><?php  if($item['multiid'] == 0 || $item['multiid'] == $default_site) { ?><?php  echo $multis[$default_site]['title'];?> <span class="label label-success">默认微站</span><?php  } else { ?><?php  echo $multis[$item['multiid']]['title'];?><?php  } ?></td>
						<td style="text-align:right;">
							<a href="<?php  echo url('site/slide/post', array('id' => $item['id'], 'f' => $_GPC['f']))?>" data-toggle="tooltip" data-placement="top" title="编辑" class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a>
							<a onclick="return confirm('此操作不可恢复，确认吗？'); return false;" href="<?php  echo url('site/slide/delete', array('id' => $item['id'], 'f' => $_GPC['f'], 'mtid' => $_GPC['mtid']))?>" data-toggle="tooltip" data-placement="top" title="删除" class="btn btn-default btn-sm"><i class="fa fa-times"></i></a>
						</td>
					</tr>
					<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
				<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" />
			</div>
		</div>
		<?php  echo $pager;?>
	</form>
</div>
<script type="text/javascript">
<!--
	var category = <?php  echo json_encode($children)?>;
//-->
</script>
<?php  } else if($do == 'post') { ?>
<div class="main">
	<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data" onsubmit="return formcheck(this)">
		<input type="hidden" name="id" value="<?php  echo $item['id'];?>">
		<div class="panel panel-default">
			<div class="panel-heading">
				幻灯片管理
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">分配到微站</label>
					<div class="col-sm-10 col-xs-12">
						<select class="form-control" name="multiid">
							<?php  if(is_array($multis)) { foreach($multis as $multi) { ?>
								<option value="<?php  echo $multi['id'];?>" <?php  if($item['multiid'] == $multi['id'] || $_GPC['mtid'] == $multi['id']) { ?>selected<?php  } ?>><?php  echo $multi['title'];?><?php  if($multi['id'] == $default_site) { ?>[默认微站]<?php  } ?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">排序</label>
					<div class="col-sm-10 col-xs-12">
						<input type="text" class="form-control" placeholder="" name="displayorder" value="<?php  echo $item['displayorder'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">标题</label>
					<div class="col-sm-10 col-xs-12">
						<input type="text" class="form-control" placeholder="" name="title" value="<?php  echo $item['title'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">缩略图</label>
					<div class="col-sm-10 col-xs-12">
						<?php  echo tpl_form_field_image('thumb', $item['thumb'])?>
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">链接</label>
					<div class="col-sm-10 col-xs-12">
						<?php  echo tpl_form_field_link('url', $item['url'], array('css' => array('input' => ' input-sm', 'btn' => ' btn-sm')));?>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<button type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">提交</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<script type="text/javascript">
<!--
	var category = <?php  echo json_encode($children)?>;
//-->
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
