<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style>
.table>tbody>tr>td{vertical-align:middle;}
</style>
<?php  if(!empty($module)) { ?>
	<ol class="breadcrumb" style="padding:5px 0;">
		<li><a href="<?php  echo url('home/welcome/ext');?>"><i class="fa fa-cogs"></i> &nbsp; 扩展功能</a></li>
		<li><a href="<?php  echo url('home/welcome/ext', array('m' => $module['name']));?>"><?php  echo $types[$module['type']]['title'];?>模块 - <?php  echo $module['title'];?></a></li>
		<li class="active"><?php  echo $tytitle[$type['name']];?></li>
	</ol>
<?php  } ?>
<ul class="nav nav-tabs">
	<?php  if($type['name'] == 'home') { ?><li<?php  if($do == 'display') { ?> class="active"<?php  } ?>><a href="<?php  echo url('site/nav/home', array('m' => $m, 'mtid' => $_GPC['mtid'], 'f' => $_GPC['f']));?>">微站首页导航图标</a></li><?php  } ?>
	<?php  if($type['name'] == 'profile') { ?><li<?php  if($do == 'display') { ?> class="active"<?php  } ?>><a href="<?php  echo url('site/nav/profile', array('m' => $m, 'mtid' => $_GPC['mtid'], 'f' => $_GPC['f']));?>">个人中心功能条目</a></li><?php  } ?>
	<?php  if($type['name'] == 'shortcut') { ?><li<?php  if($do == 'display') { ?> class="active"<?php  } ?>><a href="<?php  echo url('site/nav/shortcut', array('m' => $m, 'mtid' => $_GPC['mtid'], 'f' => $_GPC['f']));?>">快捷菜单</a></li><?php  } ?>
	<?php  if(empty($module)) { ?>
	<li<?php  if($do == 'post' && empty($id)) { ?> class="active"<?php  } ?>><a href="<?php  echo url('site/nav/' . $type['name'], array('foo' => 'post', 'mtid' => $_GPC['mtid'], 'f' => $_GPC['f']));?>"><i class="fa fa-plus"></i> 添加条目</a></li>
	<?php  } ?>
	<?php  if($do == 'post' && !empty($id)) { ?>
	<li class="active"><a href="<?php  echo url('site/nav/' . $type['name'], array('id' => $id, 'foo' => 'post', 'mtid' => $_GPC['mtid'], 'f' => $_GPC['f']));?>"><i class="fa fa-edit"></i> 编辑条目</a></li>
	<?php  } ?>
	<?php  if($type['name'] == 'shortcut' && isset($_GPC['f'])) { ?>
	<li><a href="<?php  echo url('site/multi/quickmenu', array('mtid' => $_GPC['mtid'], 'f' => $_GPC['f']))?>">快捷菜单风格</a></li>
	<?php  } ?>
</ul>
<?php  if($do == 'post') { ?>
<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
<div class="main">
	<input type="hidden" name="id" value="<?php  echo $id;?>" />
	<input type="hidden" name="foo" value="post" />
	<input type="hidden" name="do" value="<?php  echo $_GPC['do'];?>" />
	<input type="hidden" name="templateid" value="<?php  echo $template['id'];?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			微站导航
		</div>
		<div class="panel-body">
		<?php  if($_GPC['do'] != 'profile') { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">分配到微站</label>
				<div class="col-sm-9 col-xs-12">
					<select class="form-control" name="multid">
						<?php  if(is_array($multis)) { foreach($multis as $multi) { ?>
							<option value="<?php  echo $multi['id'];?>" <?php  if($item['multiid'] == $multi['id'] || $_GPC['mtid'] == $multi['id']) { ?>selected<?php  } ?>><?php  echo $multi['title'];?><?php  if($multi['id'] == $default_site) { ?>[默认微站]<?php  } ?></option>
						<?php  } } ?>
					</select>
					<span class="help-block">每个导航链接只能在一个微站上使用。</span>
				</div>
			</div>
		<?php  } ?>
		<?php  if($_GPC['do'] == 'home') { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">导航显示位置</label>
				<div class="col-sm-9 col-xs-12">
					<select name="section" class="form-control">
					<option value="0">不设置位置</option>
					<?php  for ($i=1; $i<=10; $i++) {?>
					<option <?php  if($item['section'] == $i) { ?> selected<?php  } ?> value="<?php  echo $i;?>">位置<?php  echo $i;?></option>
					<?php  }?>
					</select>
					<span class="help-block">
						设置位置后可以将导航菜单显示到模板对应的位置中。（可以同时设置多个导航在同一个位置中，会根据排序大小依次显示），显示的位置必须要有模板支持。
					</span>
					<strong class="text-danger">注意：如果您添加了模板未设置的位置时，该链接将不会显示，您可以在导航列表中查看。</strong>
				</div>
			</div>
		<?php  } ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">名称</label>
				<div class="col-sm-9 col-xs-12">
					<input name="position" type="hidden" value="<?php  echo $type['position'];?>" />
					<input type="text" class="form-control" name="title" id="name" value="<?php  echo $item['name'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">描述</label>
				<div class="col-sm-9 col-xs-12">
					<textarea style="height:200px;" class="form-control" name="description" cols="70"><?php  echo $item['description'];?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">链接</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_form_field_link('url', $item['url']);?>
					<span class="help-block">指定这个导航的链接目标</span>
					<strong>使用微站链接:</strong>
					<a href="javascript:;" class="btn btn-default btn-sm" data-toggle="modal" data-target="#settel">一键拨号</a>
					<a href="javascript:;" class="btn btn-default btn-sm" data-toggle="modal" data-target="#setnav">一键导航</a>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
				<div class="col-sm-9 col-xs-12">
					<label for="status_1" class="radio-inline"><input autocomplete="off" type="radio" name="status" id="status_1" value="1" <?php  if($item['status'] == 1 || empty($item)) { ?> checked="checked"<?php  } ?> /> 显示</label>
					<label for="status_0" class="radio-inline"><input autocomplete="off" type="radio" name="status" id="status_0" value="0" <?php  if(!empty($item) && $item['status'] == 0) { ?> checked="checked"<?php  } ?> /> 隐藏</label>
					<span class="help-block">设置导航菜单的显示状态</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
				<div class="col-sm-9 col-xs-12">
					<input type="text" class="form-control" name="displayorder" value="<?php  echo $item['displayorder'];?>" />
					<span class="help-block">导航排序，越大越靠前</span>
				</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			导航样式
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">图标类型</label>
				<div class="col-sm-9 col-xs-12">
					<label for="system" class="radio-inline" >
						<input type="radio" value="1" name="icontype" id="system" autocomplete="off" <?php  if(empty($item['fileicon'])) { ?> checked="checked" <?php  } ?> /> 系统内置
					</label>&nbsp;&nbsp;&nbsp;
					<label for="define" class="radio-inline" >
						<input type="radio" value="2" name="icontype" id="define" autocomplete="off" <?php  if(!empty($item['fileicon'])) { ?> checked="checked" <?php  } ?> /> 自定义上传
					</label>
					<span class="help-block">请选择系统的默认图标或者自己上传图标</span>
				</div>
			</div>
			<div class="form-group system-icon">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">系统图标</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_form_field_icon('icon[icon]', $item['css']['icon']['icon']);?>
					<span class="help-block">导航的背景图标，系统提供了丰富的图标ICON</span>
				</div>
			</div>
			<div class="form-group system-icon">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">图标颜色</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_form_field_color('icon[color]', $item['css']['icon']['color']);?>
					<span class="help-block">图标颜色，上传图标时此设置项无效</span>
				</div>
			</div>
			<div class="form-group system-icon">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">图标大小</label>
				<div class="col-sm-9 col-xs-12">
					<div class="input-group">
						<input class="form-control" type="text" name="icon[size]" id="icon" value="<?php  if($item['css']['icon']['width']) { ?><?php  echo $item['css']['icon']['width'];?><?php  } else { ?>35<?php  } ?>">
						<span class="input-group-addon">px</span>
					</div>
					<span class="help-block">图标的尺寸大小，单位为像素，上传图标时此设置项无效</span>
				</div>
			</div>
			<div class="form-group define-icon" style="display:none;">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">上传图标</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_form_field_image('iconfile', $item['fileicon']);?>
					<span class="help-block">自定义上传图标图片，“系统图标”优先于此项</span>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group col-sm-12">
		<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
		<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" />
	</div>
</div>
</form>
<!-- 一键拨号 -->
<div id="settel" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog" style="width:40%;margin:200px auto;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">一键拨号</h4>
			</div>
			<div class="modal-body">
				<form action="" method="post"  class="form-horizontal" role="form" enctype="multipart/form-data">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">号码</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="telphone" value="" />
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" id="gettel">确定</button>
			</div>
		</div>
	</div>
</div>
<!-- 一键导航 -->
<div id="setnav" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog" style="width:60%;margin:200px auto;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">一键导航</h4>
			</div>
			<div class="modal-body">
				<form action="" method="post"  class="form-horizontal" role="form" enctype="multipart/form-data">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">标题</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="navtitle" value="" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">详细地址</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" class="form-control" name="address" value="" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">地理位置</label>
						<div class="col-sm-9 col-xs-12" style="margin-left:-15px;">
							<?php  echo tpl_form_field_coordinate('baidumap', $settings['baidumap'])?>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" id="getnav">确定</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	require(['jquery'], function($){
		var icontype = $('input[name="icontype"]:checked').val();
		if (icontype == 1) {
			$(".system-icon").show();
			$(".define-icon").hide();
		} else {
			$(".system-icon").hide();
			$(".define-icon").show();
		}
		/*获取一键拨号信息*/
		$('#gettel').click(function() {
			var tel = $(":text[name='telphone']").val();
			$(":text[name='url']").val('tel:' + tel);
		});
		/*获取一键导航信息*/
		$('#getnav').click(function() {
			var title = $(":text[name='navtitle']").val();
			var address = $(":text[name='address']").val();
			var lng = $(":text[name='baidumap[lng]']").val();
			var lat = $(":text[name='baidumap[lat]']").val();
			var navinfo = 'http://api.map.baidu.com/marker?location=' + lat + ','+ lng + '&title=' + title + '&name=' + title + '&content=' + address + '&output=html&src=we7';
			$(":text[name='url']").val(navinfo);
		});
		/*选择图标类型按钮切换*/
		$("#system").click(function() {
			$(".system-icon").show();
			$(".define-icon").hide();
		});
		$("#define").click(function() {
			$(".system-icon").hide();
			$(".define-icon").show();
		});
	});
</script>
<?php  } else if($do == 'display') { ?>
<script type="text/javascript">
	require(['bootstrap.switch', 'util'], function($, u){
		$(function(){
			$(':checkbox').bootstrapSwitch();
			$(':checkbox').on('switchChange.bootstrapSwitch', function(e, state){
				$this = $(this);
				var dat = $this.attr('data');
				var ret = this.checked ? 1 : 0;
				$.post(location.href, {dat: dat, ret: ret}, function(resp){
					if(resp != 'success') {
						u.message('操作失败, 请稍后重试.')
					}
					<?php  if(!empty($module)) { ?>
					else {
						window.setTimeout(function(){location.href = location.href;}, 300);
					}
					<?php  } ?>
				});
			});
			$('.btn').hover(function(){
				$(this).tooltip('show');
			},function(){
				$(this).tooltip('hide');
			});
		});
	});
</script>

<?php  if($_GPC['do'] != 'profile') { ?>
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
				<input type="hidden" name="c" value="site" />
				<input type="hidden" name="a" value="nav" />
				<input type="hidden" name="f" value="<?php  echo $_GPC['f'];?>" />
				<?php  if($_GPC['do'] == 'home') { ?>
					<input type="hidden" name="do" value="home" />
				<?php  } else if($_GPC['do'] == 'shortcut') { ?>
					<input type="hidden" name="do" value="shortcut" />
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">微站</label>
					<div class="col-sm-9 col-md-8 col-lg-8 col-xs-12">
						<select class="form-control" name="mtid" id="search">
							<?php  if(is_array($multis)) { foreach($multis as $multi) { ?>
								<option value="<?php  echo $multi['id'];?>" <?php  if($multi['id'] == $_GPC['mtid'] || ($multi['id'] == $default_site && $_GPC['mtid'] == '')) { ?>selected<?php  } ?>><?php  echo $multi['title'];?><?php  if($default_site == $multi['id']) { ?>[默认微站]<?php  } ?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
			</form>
		</div>
	</div>
	<script>
		$('#search').change(function(){
			$('#form1').submit();
		});
	</script>
<?php  } ?>
<?php  if($_GPC['do'] == 'home') { ?>
<div class="panel panel-warning">
	<div class="panel-body">
		当前使用的风格为：<?php  echo $style['name'];?>，模板文件为：<?php  echo $template['title'];?>（/app/themes/<?php  echo $template['name'];?>）。<?php  if(!empty($template['sections'])) { ?>此模板提供 <span class="label label-warning"><?php  echo $template['sections'];?></span> 个导航位置，您可以指定导航在特定的位置显示，未指位置的导航将无法显示<?php  } else { ?>此模板未提供导航位置功能<?php  } ?>
	</div>
</div>
<?php  } ?>
<form action="" method="post">
<div class="clearfix">
	<div class="stat panel panel-default">
		<?php  if($type['name'] == 'home') { ?>
		<div class="panel-heading">
			选择要显示在微站首页的信息 <span class="text-muted">这里提供了<?php  if($mod) { ?>"<?php  echo $mod['title'];?>"功能中<?php  } ?>能够显示在微站首页的信息, 你可以选择性的自定义或显示隐藏</span>
		</div>
		<?php  } ?>
		<?php  if($type['name'] == 'profile') { ?>
		<div class="panel-heading">
			选择要显示在微站个人中心的信息 <span class="text-muted">这里提供了<?php  if($mod) { ?>"<?php  echo $mod['title'];?>"功能中<?php  } ?>能够显示在微站个人中心的信息, 你可以选择性的自定义或显示隐藏</span>
		</div>
		<?php  } ?>
		<?php  if($type['name'] == 'shortcut') { ?>
		<div class="panel-heading">
			选择要显示在微站快捷选项的信息 <span class="text-muted">这里提供了<?php  if($mod) { ?>"<?php  echo $mod['title'];?>"功能中<?php  } ?>能够显示在微站快捷选项的信息(需要微站模板支持), 你可以选择性的自定义或显示隐藏</span>
		</div>
		<?php  } ?>
		
		<div class="table-responsive panel-body">
			<table class="table table-hover">
				<thead class="navbar-inner">
				<tr>
					<th class="text-center" style="width:50px;">ID</th>
					<th class="text-center" style="width:60px;;">图标</th>
					<th class="text-center" style="width:150px">标题</th>
					<th class="text-center" style="width:200px;">链接</th>
					<th class="text-center" style="width:80px;">来源</th>
					<th class="text-center" style="width:80px;">排序</th>
					<?php  if($_GPC['do'] == 'home' && !empty($template['sections'])) { ?><th class="text-center" style="width:100px;">位置</th><?php  } ?>
					<th class="text-center" style="width:100px;">操作</th>
					<th class="text-right" style="width:130px;">是否在微站上显示</th>
				</tr>
				</thead>
				<tbody>
				<?php  if(is_array($ds)) { foreach($ds as $item) { ?>
				<?php  if(empty($module)) { ?>
					<?php  if($item['status'] || empty($item['module'])) { ?>
					<tr>
						<td class="text-center" style="width:50px;"><?php  echo $item['id'];?></td>
						<td class="text-center" style="width:60px;">
							<?php  if(!empty($item['icon'])) { ?>
							<img src="<?php  echo tomedia($item['icon']);?>" style="width:30px !important;" />
							<?php  } else { ?>
							<i class="<?php  echo $item['css']['icon']['icon'];?> fa fa-2x"></i>
							<?php  } ?>
						</td>
						<td class="text-center" style="width:150px;">
							<?php  if($item['remove']) { ?>
							<input type="text" class="form-control input-sm" name="title[<?php  echo $item['id'];?>]" value="<?php  echo $item['title'];?>" />
							<?php  } else { ?>
							<?php  echo $item['title'];?>
							<?php  } ?>
						</td>
						<td style="width:200px; white-space:nowrap;">
							<?php  echo tpl_form_field_link('url['.$item['id'].']', $item['url'], array('css' => array('input' => ' input-sm', 'btn' => ' btn-sm')));?>
						</td>
						<td class="text-center" style="width:80px;">
							<?php  if($item['module']) { ?><?php  echo $modules[$item['module']]['title'];?><?php  } ?>
							<?php  echo $froms[$item['from']];?>
						</td>
						<td class="text-center" style="width:80px;">
							<?php  if($item['remove']) { ?>
							<input type="text" class="form-control input-sm" name="displayorder[<?php  echo $item['id'];?>]" value="<?php  echo $item['displayorder'];?>" />
							<?php  } else { ?>
							无效
							<?php  } ?>
						</td>
						<?php  if($_GPC['do'] == 'home' && !empty($template['sections'])) { ?>
						<td class="text-center" style="width:100px;">
							<select name="section[<?php  echo $item['id'];?>]" class="form-control">
								<option value="0">不显示</option>
								<?php  for ($i = 1; $i <= $template['sections']; $i++) {?>
								<option <?php  if($item['section'] == $i) { ?> selected<?php  } ?> value="<?php  echo $i;?>">位置<?php  echo $i;?></option>
								<?php  }?>
							</select>
						</td>
						<?php  } ?>
						<td class="text-center" style="width:100px;">
							<div class="text-center" >
								<a class="btn btn-default btn-sm" href="<?php  echo url('site/nav/'.$_GPC['do'], array('id' => $item['id'],'foo' =>'post', 'mtid' => $_GPC['mtid'], 'f' => $_GPC['f']));?>" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-edit"></i></a>&nbsp;
								<a class="btn btn-default btn-sm" href="<?php  echo url('site/nav/'.$_GPC['do'], array('id' => $item['id'],'foo'=>'delete', 'name' => $modulename));?>" data-toggle="tooltip" data-placement="top" title="删除"><i class="fa fa-times"></i></a>
							</div>
						</td>
						<td class="text-right" style="width:130px;">
							<input type="checkbox" value="1"<?php  if(intval($item['status'])==1) { ?> checked="checked"<?php  } ?> data="<?php  echo base64_encode(json_encode($item));?>"/>
						</td>
					</tr>
					<?php  } ?>
				<?php  } else { ?>
				<tr>
					<td class="text-center" style="width:50px;"><?php  echo $item['id'];?></td>
					<td class="text-center" style="width:60px;">
						<?php  if(!empty($item['icon'])) { ?>
						<img src="<?php  echo tomedia($item['icon']);?>" style="width:30px !important;" />
						<?php  } else { ?>
						<i class="<?php  echo $item['css']['icon']['icon'];?> fa fa-2x"></i>
						<?php  } ?>
					</td>
					<td class="text-center" style="width:150px;">
						<?php  if($item['remove']) { ?>
						<input type="text" class="form-control input-sm" name="title[<?php  echo $item['id'];?>]" value="<?php  echo $item['title'];?>" />
						<?php  } else { ?>
						<?php  echo $item['title'];?>
						<?php  } ?>
					</td>
					<td class="text-center" style="width:200px; white-space:nowrap;">
						<?php  if($item['remove']) { ?>
						<?php  echo tpl_form_field_link('url['.$item['id'].']', $item['url'], array('css' => array('input' => ' input-sm', 'btn' => ' btn-sm')));?>
						<?php  } else { ?>
						<?php  echo $item['url'];?>
						<?php  } ?>
					</td>
					<td class="text-center" style="width:80px;">
						<?php  if($item['module']) { ?>"<?php  echo $modules[$item['module']]['title'];?>" <?php  } ?>
					</td>
					<td class="text-center"  style="width:80px;">
						<?php  if($item['remove']) { ?>
						<input type="text" class="form-control input-sm" name="displayorder[<?php  echo $item['id'];?>]" value="<?php  echo $item['displayorder'];?>" />
						<?php  } else { ?>
						无效
						<?php  } ?>
					</td>
					<?php  if($_GPC['do'] == 'home' && !empty($template['sections'])) { ?>
					<td class="text-center" style="width:100px;">
						<select name="section" class="form-control">
							<option value="0">不设置位置</option>
							<?php  for ($i=1; $i<=10; $i++) {?>
							<option <?php  if($item['section'] == $i) { ?> selected<?php  } ?> value="<?php  echo $i;?>">位置<?php  echo $i;?></option>
							<?php  }?>
						</select>
					</td>
					<?php  } ?>
					<td class="text-center" style="width:100px;">
						<?php  if($item['remove']) { ?>
						<div class="text-center">
							<a class="btn btn-default btn-sm" href="<?php  echo url('site/nav/'.$_GPC['do'], array('id' => $item['id'],'foo' =>'post'));?>" title="编辑"><i class="fa fa-edit"></i></a>&nbsp;
							<a class="btn btn-default btn-sm" href="<?php  echo url('site/nav/'.$_GPC['do'], array('id' => $item['id'],'foo'=>'delete', 'name' => $modulename));?>" title="删除"><i class="fa fa-times"></i></a>
						</div>
						<?php  } ?>
					</td>
					<td class="text-right" style="width:130px;">
						<input type="checkbox" value="1"<?php  if(intval($item['status'])==1) { ?> checked="checked"<?php  } ?> data="<?php  echo base64_encode(json_encode($item));?>"/>
					</td>
				</tr>
				<?php  } ?>
				<?php  } } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="form-group col-sm-12">
		<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
		<input type="hidden" name="foo" value="saves" />
		<input type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交" />
	</div>
</div>
</form>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
