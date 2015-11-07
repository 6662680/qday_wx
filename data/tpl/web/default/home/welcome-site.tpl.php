<?php defined('IN_IA') or exit('Access Denied');?><style>
	.template .item{position:relative;display:block;float:left;border:1px #ddd solid;border-radius:5px;background-color:#fff;padding:5px;width:190px;margin:0 10px 10px 0;}
	.template .title{margin:5px auto;line-height:2em;}
	.template .item img{width:178px;height:270px;}
	.clear{clear:both;}
	.home-container{width:100%; overflow:hidden; margin:.6em .3em;}
	.home-container .tile{float:left; display:block; text-decoration:none; outline:none; width:6em; height:6em; margin:.4em;}
	.home-container i{display:block; color:#333; height:1em; overflow: hidden; font-size:2em; margin:.25em .2em;}
	.home-container span{display:block; width:100%; overflow:hidden;}
</style>
<div class="page-header">
	<h4><i class="fa fa-comments"></i> 当前站点</h4>
</div>
<div class="panel panel-default row">
	<div class="table-responsive panel-body">
	<table class="table">
		<tr>
			<td style="width:200px; border-top:none;">
				<div class="">
					<div class="item">
						<div class="title">
							<img src="../app/themes/<?php  echo $template['name'];?>/preview.jpg" class="img-rounded" />
						</div>
					</div>
				</div>
			</td>
			<td style="border-top:none;">
				<p>
					<strong>微站入口触发关键字 : </strong>
					<?php  if(is_array($keywords)) { foreach($keywords as $keyword) { ?>
						<span class="label label-success"><?php  echo $keyword['content'];?></span>
					<?php  } } ?>
				</p>
				<p><a href="javascript:;" onclick="preview_home('<?php  echo $setting['styleid'];?>', '<?php  echo $setting['id'];?>');return false;" class="btn btn-default">预览</a></p>
			</td>
		</tr>
	</table>
	</div>
</div>
<div class="page-header">
	<h4><i class="fa fa-android"></i> 站点导航图标</h4>
</div>
<div class="panel panel-default">
	<div class="panel-body table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th style="width:200px">导航及菜单</th>
				<th>概况</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>微站首页导航图标</td>
				<td>
					<div class="home-container">
						<?php  if(is_array($home_navs)) { foreach($home_navs as $nav) { ?>
						<a href="javascript:viod(0);" class="tile text-center btn btn-default">
							<?php  if(!empty($nav['icon'])) { ?>
							<i style="background:url(<?php  echo $_W['attachurl'];?><?php  echo $nav['icon'];?>) no-repeat;background-size:cover;<?php  echo $nav['css']['icon']['style'];?> height:1em; margin:.25em .4em;"></i>
							<?php  } else { ?>
							<i class="fa <?php  echo $nav['css']['icon']['icon'];?>"></i>
							<?php  } ?>
							<span style="<?php  echo $nav['css']['name'];?>" title="<?php  echo $nav['name'];?>"><?php  echo $nav['name'];?></span>
						</a>
						<?php  } } ?>
					</div>
				</td>
			</tr>
			<tr>
				<td>个人中心导航条目</td>
				<td>
					<div class="home-container">
						<?php  if(is_array($profile_navs)) { foreach($profile_navs as $nav) { ?>
						<a href="javascript:viod(0);" class="tile text-center btn btn-default">
							<?php  if(!empty($nav['icon'])) { ?>
							<i style="background:url(<?php  echo $_W['attachurl'];?><?php  echo $nav['icon'];?>) no-repeat;background-size:cover;<?php  echo $nav['css']['icon']['style'];?>"></i>
							<?php  } else { ?>
							<i class="fa <?php  echo $nav['css']['icon']['icon'];?>"></i>
							<?php  } ?>
							<span style="<?php  echo $nav['css']['name'];?>" title="<?php  echo $nav['name'];?>"><?php  echo $nav['name'];?></span>
						</a>
						<?php  } } ?>
					</div>
				</td>
			</tr>
			<tr>
				<td>快捷菜单</td>
				<td>
					<div class="home-container">
						<?php  if(is_array($shortcut_navs)) { foreach($shortcut_navs as $nav) { ?>
						<a href="javascript:viod(0);" class="tile text-center btn btn-default">
							<?php  if(!empty($nav['icon'])) { ?>
							<i style="background:url(<?php  echo $_W['attachurl'];?><?php  echo $nav['icon'];?>) no-repeat;background-size:cover;<?php  echo $nav['css']['icon']['style'];?>"></i>
							<?php  } else { ?>
							<i class="fa <?php  echo $nav['css']['icon']['icon'];?>"></i>
							<?php  } ?>
							<span style="<?php  echo $nav['css']['name'];?>" title="<?php  echo $nav['name'];?>"><?php  echo $nav['name'];?></span>
						</a>
						<?php  } } ?>
					</div>
					<p style="padding-left:7px"><strong>当前使用的快捷菜单模板 : </strong><span class="label label-success"><?php  echo $quickmenu['template'];?></span>
					<a href="javascript:;" onclick="preview_quick('<?php  echo $quickmenu['template'];?>', '<?php  echo $setting['id'];?>');return false;" class="btn btn-default">预览快捷菜单</a></p>
				</td>
			</tr>
		</tbody>
	</table>
</div>
</div>

<div class="page-header">
	<h4><i class="fa fa-cogs"></i> 幻灯片设置</h4>
</div>
<div class="panel panel-default row">
	<div class="panel-body table-responsive">
	<table class="table">
		<tr>
			<td style="border-top:0;">
				<?php  if(!empty($slides)) { ?>
					<div id="carousel-example-generic" class="carousel slide" style="width:600px" data-ride="carousel">
						<ol class="carousel-indicators">
						<?php  if(is_array($slides)) { foreach($slides as $a => $slide) { ?> 
							<li data-target="#carousel-example-generic" data-slide-to="0"  <?php  if($a == 0) { ?>class="active"<?php  } ?>></li>
						<?php  } } ?>
						</ol>
						<div class="carousel-inner">
							<?php  if(is_array($slides)) { foreach($slides as $a => $slide) { ?> 
							<div class="item <?php  if($a == 0) { ?>active<?php  } ?>">
								<img src="<?php  echo $slide['thumb'];?>" alt="<?php  echo $slide['title'];?>" style="height:300px; margin-left:auto; margin-right: auto;">
								<div class="carousel-caption">
									<h5><?php  echo $slide['title'];?></h5>
								</div>
							</div>
							<?php  } } ?>
						</div>
						<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
							<span class="glyphicon glyphicon-chevron-left"></span>
						</a>
						<a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
							<span class="glyphicon glyphicon-chevron-right"></span>
						</a>
					</div>
				<?php  } ?>
			</td>
		</tr>
	</table>
</div>
</div>
<script type="text/javascript">
	function preview_home(styleid, multiid) {
		require(['jquery', 'util'], function($, u){
			var content = '<iframe width="320" scrolling="yes" height="480" frameborder="0" src="about:blank"></iframe>';
			var footer =
								'<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>';
			var dialog = u.dialog('预览模板', content, footer);
			dialog.find('iframe').on('load', function(){
				$('a', this.contentWindow.document.body).each(function(){
					if ($(this).attr('href').substr(0, 4) != 'http') {
						if ($(this).attr('href').substr(0, 2) == './') {
							$(this).attr('href', $(this).attr('href') + 's=' + styleid);
						} else {
							$(this).attr('href', 'http://' + $(this).attr('href'));
						}
					}
				});
			});
			var url = '../app/<?php  echo murl('home')?>&s=' + styleid + 't=' + multiid;
			dialog.find('iframe').attr('src', url);
			dialog.find('.modal-dialog').css({'width': '322px'});
			dialog.find('.modal-body').css({'padding': '0', 'height': '480px'});
			dialog.modal('show');
		});
	}
	function preview_quick(name, multiid) {
		require(['jquery', 'util'], function($, u){
			var content = '<iframe width="320" scrolling="yes" height="480" frameborder="0" src="about:blank"></iframe>';
			var footer =
					'			<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>';
			var dialog = u.dialog('预览快捷菜单', content, footer);
			var url = "../app/<?php  echo murl('utility/preview/shortcut')?>file=" + name + "&t=" + multiid;
			dialog.find('iframe').attr('src', url);
			dialog.find('.modal-dialog').css({'width': '322px'});
			dialog.find('.modal-body').css({'padding': '0', 'height': '480px'});
			dialog.modal('show');
		});
	}
</script>

