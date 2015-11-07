<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($op == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('article',array('op'=>'post'));?>">添加文章</a></li>
	<li <?php  if($op == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('article',array('op'=>'display'));?>">文章列表</a></li>
</ul>
<style>
.table td span{display:inline-block;margin-top:4px;}
.table td input{margin-bottom:0;}
</style>
<?php  if($op == 'display') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="site" />
			<input type="hidden" name="a" value="entry" />
			<input type="hidden" name="m" value="hx_subscribe" />
			<input type="hidden" name="do" value="article" />
			<input type="hidden" name="op" value="display" />
			
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
				<div class="col-sm-8 col-md-8 col-lg-8 col-xs-12">
					<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
				</div>
				<div class="pull-right col-xs-12 col-sm-2 col-md-2 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>

		</form>
	</div>
</div>

<div class="panel panel-default">
	<div class="table-responsive panel-body">
		<table class="table">
			<thead>
				<tr>
					<th style="width:50px">排序</th>
					<th>标题</th>
					<th style="width:100px; text-align:right;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td><?php  echo $item['displayorder'];?></td>
					<td>
						<a href="<?php  echo $this->createWebUrl('article',array('op'=>'post','id' => $item['id']));?>" style="color:#333;"><?php  echo $item['title'];?></a>
					</td>
					<td style="text-align:right;">
						<a href="<?php  echo $this->createWebUrl('article',array('op'=>'post','id' => $item['id']));?>" title="编辑" data-toggle="tooltip" data-placement="top" class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a>
						<a onclick="return confirm('此操作不可恢复，确认吗？'); return false;" href="<?php  echo $this->createWebUrl('article',array('op'=>'delete','id' => $item['id']));?>" title="删除" data-toggle="tooltip" data-placement="top" class="btn btn-default btn-sm"><i class="fa fa-times"></i></a>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<?php  echo $pager;?>
<script type="text/javascript">
	var category = <?php  echo json_encode($children)?>;
	require(['bootstrap'],function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});
</script>
<?php  } else if($op == 'post') { ?>
<div class="clearfix">
<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
	<div class="panel panel-default">
		<div class="panel-heading">文章管理</div>
		<div class="panel-body">
				<input type="hidden" name="id" value="<?php  echo $item['id'];?>">
				<?php  if(!empty($item) && empty($item['linkurl'])) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">访问地址</label>
						<div class="col-sm-8 col-xs-12">
							<p class="form-control-static"><a href="/app/index.php?c=site&a=site&do=detail&id=<?php  echo $item['id'];?>&i=<?php  echo $_W['uniacid'];?>" target="_blank">/app/index.php?c=site&a=site&do=detail&id=<?php  echo $item['id'];?>&i=<?php  echo $_W['uniacid'];?></a></p>
							<div class="help-block">您可以根据此地址，添加回复规则，设置访问。</div>
						</div>
					</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">排序</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" placeholder="" name="displayorder" value="<?php  echo $item['displayorder'];?>">
						<span class="help-block">文章的显示顺序，越大则越靠前</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">标题</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" placeholder="" name="title" value="<?php  echo $item['title'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">文章来源</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" placeholder="" name="source" value="<?php  echo $item['source'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">文章作者</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" id="writer" name="author" value="<?php  echo $item['author'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">缩略图</label>
					<div class="col-sm-8 col-xs-12">
						<?php  echo tpl_form_field_image('thumb', $item['thumb'])?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
						<label>
						封面（大图片建议尺寸：360像素 * 200像素）
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">简介</label>
					<div class="col-sm-8 col-xs-12">
						<textarea class="form-control" name="description" rows="5"><?php  echo $item['description'];?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label"></label>
					<div class="col-sm-8">
						<div class="help-block"><label class="checkbox-inline"><input type="checkbox" name="autolitpic" value="1"<?php  if(empty($item['thumb'])) { ?> checked="true"<?php  } ?>>提取内容的第一个图片为缩略图</label></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">内容</label>
					<div class="col-sm-8 col-xs-12">
						<textarea class="form-control richtext" name="content" rows="10"><?php  echo $item['content'];?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">关注素材</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" placeholder="" name="linkurl" value="<?php  echo $item['linkurl'];?>">
						<br>
						（必须填写正确的引导关注页面：例如：http://www.dwz.cn/012wz，否则文章分享后打不开！）
					</div>

				</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-12">
			<input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</div>
</form>
</div>
<script type="text/javascript">
	var category = <?php  echo json_encode($children)?>;
	require(['jquery', 'util'], function($, u){
		$(function(){
			u.editor($('.richtext')[0]);
		});
		$('#credit1').click(function(){
			$('#credit-status1').show();
		});
		$('#credit0').click(function(){
			$('#credit-status1').hide();
		});
	});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
