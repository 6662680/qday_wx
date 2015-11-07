<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="main">
<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo url('mc/business');?>">商户管理</a></li>
</ul>

<?php  if(($do == 'display') && ($list['status'] == 1)) { ?>
	<form action="" class="form-horizontal form" method="post" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php  echo $item['id'];?>">
		<div class="panel panel-default">
			<div class="panel-heading">商户基本信息</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">名称</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" name="title" value="<?php  if(!empty($item['title'])) { ?><?php  echo $item['title'];?><?php  } ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">宣传图</label>
					<div class="col-sm-8 col-xs-12">
						<?php  if(!empty($item['thumb'])) { ?>
							<?php  echo tpl_form_field_image('thumb', $item['thumb'])?>
						<?php  } else { ?>
							<?php  echo tpl_form_field_image('thumb')?>
						<?php  } ?>
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">行业</label>
					<?php  echo tpl_form_field_industry('industry', $item['industry1'], $item['industry2'])?>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">简介</label>
					<div class="col-sm-8 col-xs-12">
						<textarea style="height:100px;" class="form-control" name="content" id="reply-add-text"><?php  if(!empty($item['content'])) { ?><?php  echo $item['content'];?><?php  } ?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">电话</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" name="phone" value="<?php  if(!empty($item['phone'])) { ?><?php  echo $item['phone'];?><?php  } ?>"  class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">QQ</label>
					<div class="col-sm-8 col-xs-12">
						<input type="text" name="qq" value="<?php  if(!empty($item['qq'])) { ?><?php  echo $item['qq'];?><?php  } ?>"  class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">地区</label>
					<div class="col-sm-9 col-xs-12">
						<?php  if(!empty($reside)) { ?>
							<?php  echo tpl_form_field_district('dis', $reside)?>
						<?php  } else { ?>
							<?php  echo tpl_form_field_district('dis')?>
						<?php  } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">详细地址</label>
					<div class="col-sm-8 col-xs-12">
						<div class="input-append"><input type="text" name="address" value="<?php  if(!empty($item['address'])) { ?><?php  echo $item['address'];?><?php  } ?>"  class="form-control" /></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">坐标</label>
					<div class="col-sm-8 col-xs-12">
						<?php  if(!empty($item)) { ?>
							<?php  echo tpl_form_field_coordinate('baidumap', $item)?>
						<?php  } else { ?>
							<?php  echo tpl_form_field_coordinate('baidumap')?>
						<?php  } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<button type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">提交</button>
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>

	</div>
<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
