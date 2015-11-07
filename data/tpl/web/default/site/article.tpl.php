<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($do == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo url('site/article/post');?>">添加文章</a></li>
	<li <?php  if($do == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo url('site/article/display');?>">文章列表</a></li>
</ul>
<style>
.table td span{display:inline-block;margin-top:4px;}
.table td input{margin-bottom:0;}
</style>
<?php  if($do == 'display') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="a" value="article" />
			<input type="hidden" name="c" value="site" />
			<input type="hidden" name="do" value="display" />
			
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">关键字</label>
				<div class="col-sm-8 col-md-8 col-lg-8 col-xs-12">
					<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 control-label">文章分类</label>
				<div class="col-sm-8 col-md-8 col-lg-8 col-xs-12">
					<?php  echo tpl_form_field_category_2level('category', $parent, $children, $_GPC['category']['parentid'], $_GPC['category']['childid']);?>
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
					<th style="width:180px;">属性</th>
					<th style="width:100px; text-align:right;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td><?php  if(!empty($item['pcate'])) { ?><span><?php  echo $item['displayorder'];?></span><?php  } else { ?><?php  echo $item['displayorder'];?><?php  } ?></td>
					<td>
						<?php  if(!empty($item['pcate'])) { ?><span class="text-error">[<?php  echo $category[$item['pcate']]['name'];?>]</span><?php  } ?><?php  if(!empty($item['ccate'])) { ?><span class="text-info">[<?php  echo $category[$item['ccate']]['name'];?>]</span><?php  } ?>
						<a href="<?php  echo url('site/article/post/', array('id' => $item['id']))?>" style="color:#333;"><?php  echo $item['title'];?></a>
					</td>
					<td>
						<?php  if($item['ishot']) { ?><span class="label label-success">头条</span><?php  } ?>
						<?php  if($item['iscommend']) { ?><span class="label label-warning">推荐</span><?php  } ?>
					</td>
					<td style="text-align:right;">
						<a href="<?php  echo url('site/article/post', array('id' => $item['id']))?>" title="编辑" data-toggle="tooltip" data-placement="top" class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a>
						<a onclick="return confirm('此操作不可恢复，确认吗？'); return false;" href="<?php  echo url('site/article/delete', array('id' => $item['id']))?>" title="删除" data-toggle="tooltip" data-placement="top" class="btn btn-default btn-sm"><i class="fa fa-times"></i></a>
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
<?php  } else if($do == 'post') { ?>
<div class="clearfix">
<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
	<div class="panel panel-default">
		<div class="panel-heading">文章管理</div>
		<div class="panel-body">
				<input type="hidden" name="id" value="<?php  echo $item['id'];?>">
				<?php  if(!empty($item) && empty($item['linkurl']) && $id > 0) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">访问地址</label>
						<div class="col-sm-8 col-xs-12">
							<p class="form-control-static"><a href="<?php  echo $_W['siteroot'];?>/app/index.php?c=site&a=site&do=detail&id=<?php  echo $item['id'];?>&i=<?php  echo $_W['uniacid'];?>" target="_blank"><?php  echo $_W['siteroot'];?>/app/index.php?c=site&a=site&do=detail&id=<?php  echo $item['id'];?>&i=<?php  echo $_W['uniacid'];?></a></p>
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
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">文章触发关键字</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control" placeholder="" name="keyword" value="<?php  echo $keywords;?>">
						<div class="help-block">添加关键字以后,系统将生成一条图文规则,用户可以通过输入关键字来阅读文章</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">自定义属性</label>
					<div class="col-sm-8 col-xs-12">
						<label class="checkbox-inline"><input type="checkbox" name="option[hot]" value="1" <?php  if($item['ishot']) { ?> checked<?php  } ?>> 头条[h]</label>
						<label class="checkbox-inline"><input type="checkbox" name="option[commend]" value="1" <?php  if($item['iscommend']) { ?> checked<?php  } ?>> 推荐[c]</label>
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
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
						<label class="checkbox-inline">
							<input type="checkbox" name="incontent" value="1" <?php  if(!empty($item['incontent'])) { ?> checked="checked" <?php  } ?> /> 封面图片显示在正文中
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">文章类别</label>
					<div class="col-sm-8 col-xs-12">
						<?php  echo tpl_form_field_category_2level('category', $parent, $children, $pcate, $ccate)?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">选择内容模板</label>
					<div class="col-sm-8 col-xs-12">
						<select name="template" class="form-control">
							<option value="">使用默认设置</option>
							<?php  if(is_array($template)) { foreach($template as $v) { ?>
							<option value="<?php  echo $v['name'];?>"<?php  if($item['template'] == $v['name']) { ?> selected<?php  } ?>><?php  echo $v['title'];?></option>
							<?php  } } ?>
						</select>
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
						<?php  echo tpl_ueditor('content', $item['content']);?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">直接链接</label>
					<div class="col-sm-8 col-xs-12">
						<?php  echo tpl_form_field_link('linkurl', $item['linkurl']);?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">阅读次数</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" name="click" value="<?php  echo $item['click'];?>" class="form-control"/>
						<div class="help-block">默认为0。您可以设置一个初始值,阅读次数会在该初始值上增加。</div>
					</div>
				</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">积分设置</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">是否赠送积分</label>
				<div class="col-sm-8 col-xs-12">
					<label class="radio-inline"><input type="radio" name="credit[status]" value="1" <?php  if($item['credit']['status'] == 1) { ?> checked<?php  } ?> id="credit1"> 赠送</label>
					<label class="radio-inline"><input type="radio" name="credit[status]" value="0" <?php  if($item['credit']['status'] == 0) { ?> checked<?php  } ?> id="credit0"> 不赠送</label>
					<span class="help-block">设置赠送积分后,粉丝在分享时赠送积分.粉丝的好友在点击阅读时,也会赠送积分</span>
				</div>
			</div>
			<div id="credit-status1" <?php  if($item['credit']['status'] == 1) { ?>style="display:block"<?php  } else { ?>style="display:none"<?php  } ?>>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">赠送积分上限</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="credit[limit]" value="<?php  echo $item['credit']['limit'];?>">
					<span class="help-block">设置赠送积分的上限,到达上限后将不再赠送积分</span>
					<?php  if(!empty($id)) { ?>
						<span class="help-block">已经赠送了<strong class="text-danger"> <?php  echo $credit_num;?> </strong>积分,还可以赠送<strong class="text-danger"> <?php  echo $credit_yu;?> </strong>积分</span>
					<?php  } ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">转发赠送积分</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control"  name="credit[share]" value="<?php  echo $item['credit']['share'];?>">
					<span class="help-block">设置转发时赠送积分</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">阅读赠送积分</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="credit[click]" value="<?php  echo $item['credit']['click'];?>">
					<span class="help-block">设置阅读时赠送给分享人的积分</span>
				</div>
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
	$('#credit1').click(function(){
		$('#credit-status1').show();
	});
	$('#credit0').click(function(){
		$('#credit-status1').hide();
	});
</script>

<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
