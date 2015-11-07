<?php defined('IN_IA') or exit('Access Denied');?><script type="text/javascript">
	function clicklink(href) {
		if(href=='tel:'){
			require(['util'],function(u){
				u.message('请添加一键拨号号码.');
			});
			return;
		}
		
		if($.isFunction(<?php  echo $callback;?>)){
			<?php  echo $callback;?>(href);
		}
	}
	function linkModal(a) {
		$(".link-browser").addClass('hide');
		$(".link-modal > div").addClass('hide');
		$(a).removeClass('hide');
	}
	function retrunLinkBrowser() {
		$(".link-browser").removeClass('hide');
		$(".link-modal > div").addClass('hide');
	}
</script>

<style type="text/css">
.link-browser ul li{width: 120px; }
.list-group .list-group-item a{color:#428bca;}
.link-browser .page-header, .link-modal .page-header{margin:40px 0 10px;}
.link-browser .page-header:first-child, .link-modal .page-header:first-of-type{margin-top:0;}
.link-browser div.btn, .link-modal div.btn{min-width:100px; text-align:center; margin:5px 2px;}
</style>

<!--二级页面-->
<div class="link-modal">
	<!--一键拨号-->
	<div id="telphone-modal" class="hide">
		<ol class="breadcrumb">
			<li><a href="javascript:;" onclick="retrunLinkBrowser();">选择器首页</a></li>
			<li><a href="javascript:;" onclick="retrunLinkBrowser();">系统默认链接</a></li>
			<li class="active">一键拨号</li>
		</ol>
		<div class="form-group list-group-item clearfix">
			<label class="col-xs-12 col-sm-2 col-md-2 control-label" style="margin-top:5px;">号码</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" name="telphone" id="telphone" value="" />
			</div>
			<div class="col-sm-4">
				<a href="javascript:;" onclick="clicklink('tel:' + $('#telphone').val());" style="display:block;margin-top:5px;">一键拨号</a>
			</div>
		</div>
	</div>
	<?php  if(is_array($modulemenus)) { foreach($modulemenus as $moduletype => $modules) { ?>
		<?php  if(is_array($modules)) { foreach($modules as $modulekey => $module) { ?>
			<div id="<?php  echo $module['name'];?>" class="hide">
				<ol class="breadcrumb">
					<li><a href="javascript:;" onclick="retrunLinkBrowser();">选择器首页</a></li>
					<li><a href="javascript:;" onclick="retrunLinkBrowser();"><?php  echo $modtypes[$moduletype]['title'];?></a></li>
					<li class="active"><?php  echo $module['title'];?></li>
				</ol>
				<?php  if(is_array($linktypes)) { foreach($linktypes as $linktypekey => $linktype) { ?>
					<?php  if(!empty($module[$linktypekey])) { ?>
						<div class="page-header">
							<h4><i class="fa fa-folder-open-o"></i> <?php  echo $linktype;?></h4>
						</div>
						<?php  if(is_array($module[$linktypekey])) { foreach($module[$linktypekey] as $m) { ?>
							<div class="btn btn-default" onclick="clicklink('<?php  echo $m['url'];?>');" title="<?php  echo $m['title'];?>"><?php  echo cutstr($m['title'],6);?></div>
						<?php  } } ?>
					<?php  } ?>
				<?php  } } ?>
			</div>
		<?php  } } ?>
	<?php  } } ?>
</div>

<!--一级页面-->
<div class="link-browser">
	<div class="page-header">
		<h4><i class="fa fa-folder-open-o"></i> 系统默认链接</h4>
	</div>
	<?php  if(is_array($sysmenus)) { foreach($sysmenus as $m) { ?>
		<div class="btn btn-default" onclick="clicklink('<?php  echo $m['url'];?>');" title="<?php  echo $m['title'];?>"><?php  echo $m['title'];?></div>
	<?php  } } ?>
	<div class="btn btn-default" onclick="linkModal('#telphone-modal')">一键拨号</div>
	<div class="page-header">
		<h4><i class="fa fa-folder-open-o"></i> 多微站首页链接</h4>
	</div>
	<?php  if(is_array($multimenus)) { foreach($multimenus as $multi) { ?>
		<div class="btn btn-default" onclick="clicklink('<?php  echo $multi['url'];?>');" title="<?php  echo $multi['title'];?>"><?php  echo $multi['title'];?></div>
	<?php  } } ?>
	<?php  if(is_array($modulemenus)) { foreach($modulemenus as $moduletype => $modules) { ?>
	<div class="page-header">
		<h4><i class="fa fa-folder-open-o"></i> <?php  echo $modtypes[$moduletype]['title'];?></h4>
	</div>
		<?php  if(is_array($modules)) { foreach($modules as $modulekey => $module) { ?>
		<div class="btn btn-default" onclick="linkModal('#<?php  echo $module['name'];?>')" title="<?php  echo $module['title'];?>"><?php  echo $module['title'];?></div>
		<?php  } } ?>
	<?php  } } ?>
</div>