<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<ul class="nav nav-tabs">
	<li><a href="<?php  echo url('site/multi/display');?>">微站管理</a></li>
	<?php  if($do == 'post' && !$id) { ?><li class="active"><a href="<?php  echo url('multi/post');?>">添加微站</a></li><?php  } ?>
	<?php  if($do == 'post' && $id) { ?><li class="active"><a href="<?php  echo url('multi/post', array('id' => $id));?>">编辑微站</a></li><?php  } ?>
</ul>

<style>
	.template .item{position:relative;display:block;float:left;border:1px #ddd solid;border-radius:5px;background-color:#fff;padding:5px;width:190px;margin:0 20px 20px 0; overflow:hidden;}
	.template .title{margin:5px auto;line-height:2em;}
	.template .title a{text-decoration:none;}
	.template .item img{width:178px;height:270px; cursor:pointer;}
	.template .active.item-style img, .template .item-style:hover img{width:178px;height:270px;border:3px #009cd6 solid;padding:1px; }
	.template .title .fa{display:none}
	.template .active .fa.fa-check{display:inline-block;position:absolute;bottom:33px;right:6px;color:#FFF;background:#009CD6;padding:5px;font-size:14px;border-radius:0 0 6px 0;}
	.template .fa.fa-times{cursor:pointer;display:inline-block;position:absolute;top:10px;right:6px;color:#D9534F;background:#ffffff;padding:5px;font-size:14px;text-decoration:none;}
	.template .fa.fa-times:hover{color:red;}
	.template .item-bg{width:100%; height:342px; background:#000; position:absolute; z-index:1; opacity:0.5; margin:-5px 0 0 -5px;}
	.template .item-build-div1{position:absolute; z-index:2; margin:-5px 10px 0 5px; width:168px;}
	.template .item-build-div2{text-align:center; line-height:30px; padding-top:150px;}
	@media screen and (min-width:992px){#ListStyle{width:890px; margin:100px auto;}}
</style>

<form class="form-horizontal form" action="" method="post">
	<div class="clearfix">
		<div class="panel panel-default">
			<div class="panel-heading">
				站点风格选择
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">站点标识</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="title" class="form-control" value="<?php  echo $multi['title'];?>" />
						<div class="help-block">用于区分于其他微站。比如:中秋专题</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否启用</label>
					<div class="col-sm-9 col-xs-12">
						<label class="radio-inline"><input type="radio" name="status" value="1" <?php  if($multi['status'] == '' || $multi['status'] == 1) { ?>checked<?php  } ?>>是</label>
						<label class="radio-inline"><input type="radio" name="status" value="0" <?php  if($multi['status'] === 0) { ?>checked<?php  } ?>>否</label>
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-12 col-sm-4 col-md-3 col-lg-2 control-label">选择微站风格</label>
					<div class="col-sm-8 col-xs-12">
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ListStyle">选择风格</button>
						<input type="hidden" name="styleid" id="styleid" value="<?php  echo $multi['styleid'];?>" />
					</div>
				</div>

				<!-- 风格列表 -->
				<div class="modal fade" id="ListStyle" aria-hidden="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title">微站模板风格列表</h4>
						</div>
						<div class="modal-body template clearfix">
							<?php  if(is_array($styles)) { foreach($styles as $style) { ?>
							<div class="item item-style <?php  if($multi['styleid'] == $style['id']) { ?> active <?php  } ?>">
								<div class="title">
									<div style="overflow:hidden; height:28px;"><?php  echo $style['name'];?></div>
									<a href="javascript:;" onclick="$('#styleid').val(<?php  echo $style['id'];?>);$('#ListStyle').modal('hide');">
										<img src="<?php  echo $_W['siteroot'];?>app/themes/<?php  echo $style['tname'];?>/preview.jpg" class="img-rounded">
									</a>
									<span class="fa fa-check"></span>
								</div>
								<div class="btn-group  btn-group-justified">
									<a href="javascript:;" class="btn btn-default btn-xs" onclick="$('#styleid').val(<?php  echo $style['id'];?>);$('#ListStyle').modal('hide');">选择风格</a>
								</div>
							</div>
							<?php  } } ?>

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						</div>
					</div>
				</div>

			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				站点信息
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">绑定域名</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="bindhost" class="form-control" value="<?php  echo $multi['bindhost'];?>" />
						<span class="help-block">绑定时请先将域名解析指向到本服务器，请只填写host部分，例如：www.baidu.com</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">微站标题</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="sitename" class="form-control" value="<?php  echo $multi['site_info']['sitename'];?>" />
						<span class="help-block">微站默认的标题，如果为空则显示公众号名称</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">keywords</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="keywords" class="form-control" value="<?php  echo $multi['site_info']['keywords'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">description</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="description" class="form-control" value="<?php  echo $multi['site_info']['description'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">底部自定义</label>
					<div class="col-sm-9 col-xs-12">
						<textarea style="height:150px;" class="form-control" cols="70" name="footer" autocomplete="off"><?php  echo $multi['site_info']['footer'];?></textarea>
						<span class="help-block">自定义底部信息，支持HTML</span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</div>
</form>

<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
