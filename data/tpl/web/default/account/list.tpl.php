<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
	<ol class="breadcrumb">
		<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
		<li><a href="<?php  echo url('account/display');?>">公众号列表</a></li>
		<li class="active">编辑主公众号</li>
	</ol>
	<ul class="nav nav-tabs">
		<li><a href="<?php  echo url('account/post/basic', array('uniacid' => $uniacid));?>">账号基本信息</a></li>
		<?php  if($_W['isfounder']) { ?>
			<li<?php  if($do == 'permission') { ?> class="active"<?php  } ?>><a href="<?php  echo url('account/permission', array('uniacid' => $uniacid));?>">账号操作员列表</a></li>
		<?php  } ?>
		<li class="active"><a href="<?php  echo url('account/post/list', array('uniacid' => $uniacid));?>">子公众号列表</a></li>
		<li><a href="<?php  echo url('account/switch', array('uniacid' => $uniacid));?>" style="color:#d9534f;"><i class="fa fa-cog fa-spin fa-fw"></i> 管理此公众号功能</a></li>
	</ul>
	<div class="clearfix">
		<h5 class="page-header">子公众号列表</h5>
		<div class="input-group">
			<a class="btn btn-primary" href="<?php  echo url('account/bind/post', array('uniacid' => $uniacid));?>"><i class="fa fa-plus"></i> 添加子公众号</a>
		</div>
		
		<ul class="list-unstyled account list-group">
			<?php  if(is_array($accounts)) { foreach($accounts as $account) { ?>
			<li class="list-group-item" style="line-height:60px;">
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<img <?php  if(file_exists(IA_ROOT . '/attachment/headimg_'.$account['acid'].'.jpg')) { ?> src="<?php  echo $_W['attachurl'];?>headimg_<?php  echo $account['acid'];?>.jpg?acid=<?php  echo $account['acid'];?>"<?php  } else if($account['type'] == '1') { ?>src="<?php  echo $_W['attachurl'];?>headimg_weixin.jpg"<?php  } else { ?>src="<?php  echo $_W['attachurl'];?>headimg_yixin.jpg"<?php  } ?> class="img-circle" width="50" height="50" onerror="this.src='resource/images/gw-wx.gif'">
						</div>
						<div class="col-xs-12 col-sm-4">
							<?php  echo $account['name'];?> &nbsp; <span class="label label-default"><?php  echo $types[$account['type']]['title'];?></span>
						</div>
						<div class="col-xs-12 col-sm-4">
							接入状态: <?php  if($account['isconnect'] == 1) { ?><span class="text-success"><i class="fa fa-check-circle"></i>成功接入<?php  echo $types[$account['type']]['title'];?></span><?php  } else { ?><span class="text-warning"><i class="fa fa-times-circle"></i>未接入<?php  echo $types[$account['type']]['title'];?></span><?php  } ?>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-4 col-md-4 col-lg-3 text-right">
					<a href="<?php  echo url('account/bind/details', array('acid' => $account['acid'], 'uniacid' => $account['uniacid']))?>" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="查看详细信息"><i class="fa fa-bar-chart-o"></i></a>
					<a href="<?php  echo url('account/bind/post', array('acid' => $account['acid'], 'uniacid' => $account['uniacid']))?>" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-pencil"></i></a>
					<a href="<?php  echo url('account/bind/delete', array('acid' => $account['acid'], 'uniacid' => $account['uniacid']))?>" onclick="return confirm('确认删除吗？');return false;" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="删除"><i class="fa fa-times"></i></a>
				</div>
			</div>
			</li>
			<?php  } } ?>
		</ul>
	</div>
	<script>
		require(['bootstrap'],function($){
			$('.account .btn').hover(function(){
				$(this).tooltip('show');
			},function(){
				$(this).tooltip('hide');
			});
		});
	</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>