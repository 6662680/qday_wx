<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
	<ol class="breadcrumb">
		<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
		<li><a href="<?php  echo url('system/welcome');?>">系统</a></li>
		<li class="active">公众号列表</li>
	</ol>
	<div class="clearfix" style="margin-bottom:5em;">
		<form action="./index.php" method="get" role="form">
			<input type="hidden" name="c" value="account">
			<input type="hidden" name="a" value="display">
			<div class="form-group">
				<div class="input-group">
					<input type="text" class="form-control <?php  if(empty($_GPC['keyword']) && !empty($_GPC['s_uniacid'])) { ?>hide<?php  } ?>" placeholder="请输入微信公众号名称" name="keyword" id="s_keyword" value="<?php  echo $_GPC['keyword'];?>">
					<input type="text" class="form-control <?php  if(empty($_GPC['s_uniacid'])) { ?>hide<?php  } ?>" placeholder="请输入微信公众号ID" name="s_uniacid" id="s_uniacid" value="<?php  echo $_GPC['s_uniacid'];?>">
					<div class="input-group-btn">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
						<ul class="dropdown-menu dropdown-menu-right" role="menu">
							<li><a href="javascript:;" onclick="$('#s_uniacid').addClass('hide').val('');$('#s_keyword').removeClass('hide');">根据公众号名称搜索</a></li>
							<li><a href="javascript:;" onclick="$('#s_uniacid').removeClass('hide');$('#s_keyword').addClass('hide').val('');">根据公众号ID搜索</a></li>
						</ul>
					</div>
				</div>
			</div>
		</form>
		<div class="input-group">
			<a class="btn btn-primary" href="<?php  echo url('account/post-step');?>"><i class="fa fa-plus"></i> 添加公众号</a>
		</div>
		<ul class="list-unstyled account">
			<?php  if(is_array($list)) { foreach($list as $uni) { ?>
			<li>
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="row" style="font-size:16px; line-height:40px; color:#666;">
							<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">主公众号名称：<span><?php  echo $uni['name'];?></span></div>
							<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 edit-group">服务套餐：<span  id="<?php  echo $uni['group']['id'];?>"><?php  echo $uni['group']['name'];?></span> <i class="fa fa-pencil" id="<?php  echo $uni['uniacid'];?>" style="cursor:pointer"></i></div>
							<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 text-right" style="position:static;">
								<?php  if($uni['role'] == 'founder') { ?>
									<a href="<?php  echo url('account/post-acid', array('uniacid' => $uni['uniacid']))?>" data-toggle="tooltip" data-placement="bottom" title="添加子公众号"><i class="fa fa-plus"></i></a> 
									<a href="<?php  echo url('account/permission', array('uniacid' => $uni['uniacid']))?>" data-toggle="tooltip" data-placement="bottom" title="添加操作用户"><i class="fa fa-user"></i></a>
									<a href="<?php  echo url('account/post', array('uniacid' => $uni['uniacid']))?>" data-toggle="tooltip" data-placement="bottom" title="编辑帐号信息"><i class="fa fa-pencil"></i></a>
									<a href="<?php  echo url('account/delete', array('uniacid' => $uni['uniacid']))?>" onclick="return confirm('删除主公众号其所属的子公众号及其它数据会全部删除，确认吗？');return false;" data-toggle="tooltip" data-placement="bottom" title="删除"><i class="fa fa-times"></i></a>
									<a href="<?php  echo url('account/switch', array('uniacid' => $uni['uniacid']))?>" class="manage" target="_balnk" data-toggle="tooltip" data-placement="bottom" title="管理" style="border-left:1px #DDD dashed; padding-left:10px; margin-left:10px;"><i class="fa fa-cog fa-spin"></i></a>
								<?php  } else if($uni['role'] == 'manager') { ?>
									<a href="<?php  echo url('account/post-acid', array('uniacid' => $uni['uniacid']))?>" data-toggle="tooltip" data-placement="bottom" title="添加子公众号"><i class="fa fa-plus"></i></a> 
									<a href="<?php  echo url('account/post', array('uniacid' => $uni['uniacid']))?>" data-toggle="tooltip" data-placement="bottom" title="编辑帐号信息"><i class="fa fa-pencil"></i></a>
									<a href="<?php  echo url('account/delete', array('uniacid' => $uni['uniacid']))?>" onclick="return confirm('删除主公众号其所属的子公众号及其它数据会全部删除，确认吗？');return false;" data-toggle="tooltip" data-placement="bottom" title="删除"><i class="fa fa-times"></i></a>
									<a href="<?php  echo url('account/switch', array('uniacid' => $uni['uniacid']))?>" class="manage" target="_balnk" data-toggle="tooltip" data-placement="bottom" title="管理" style="border-left:1px #DDD dashed; padding-left:10px; margin-left:10px;"><i class="fa fa-cog fa-spin"></i></a>
								<?php  } else if($uni['role'] == 'operator') { ?>
									<a href="<?php  echo url('account/switch', array('uniacid' => $uni['uniacid']))?>" class="manage" target="_balnk" data-toggle="tooltip" data-placement="bottom" title="管理"><i class="fa fa-cog fa-spin"></i></a>
								<?php  } ?>
							</div>
						</div>
					</div>
					<ul class="panel-body list-group ">
						<?php  if(is_array($uni['details'])) { foreach($uni['details'] as $account) { ?>
						<li class=" row list-group-item" style="line-height:60px;">
							<div class="col-xs-12 col-sm-12 col-md-2 col-lg-1">
								<img <?php  if(file_exists(IA_ROOT . '/attachment/headimg_'.$account['acid'].'.jpg')) { ?> src="<?php  echo $_W['attachurl'];?>headimg_<?php  echo $account['acid'];?>.jpg?acid=<?php  echo $account['acid'];?>"<?php  } else if($account['type'] == '1') { ?>src="resource/images/gw-wx.gif"<?php  } else { ?>src="resource/images/gw-yx.png"<?php  } ?> class="" width="50" height="50"  onerror="this.src='resource/images/gw-wx.gif'" />
							</div>
							<div class="col-xs-12 col-sm-12 col-md-3 col-lg-4 item">
								<span class="label label-default"><?php  echo $types[$account['type']]['title'];?></span>&nbsp;<?php  echo $account['name'];?>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 item">
								接入状态 : <?php  if($account['isconnect'] == 1) { ?><span class="text-success"><i class="fa fa-check-circle"></i>成功接入<?php  echo $types[$account['type']]['title'];?></span><?php  } else { ?><span class="text-warning"><i class="fa fa-times-circle"></i>未接入<?php  echo $types[$account['type']]['title'];?></span> <i class="text-danger fa fa-question-circle" data-toggle="tooltip" data-placement="top" title='公众号接入状态显示“未接入”解决方案：进入微信公众平台，依次选择: 开发者中心 -> 修改配置，然后将对应公众号在
								平台的url和token复制到微信公众平台对应的选项，公众平台会自动进行检测'></i><?php  } ?>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 text-right" style="padding-bottom:5px;">
								<?php  if($uni['role'] <> 'operator') { ?>
									<a href="<?php  echo url('account/bind/details', array('acid' => $account['acid'], 'uniacid' => $account['uniacid']))?>" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="查看详细信息"><i class="fa fa-bar-chart-o"></i></a>
									<a href="<?php  echo url('account/bind/post', array('acid' => $account['acid'], 'uniacid' => $account['uniacid']))?>" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-pencil"></i></a>
									<?php  if(count($uni['details']) > 1) { ?>
										<a href="<?php  echo url('account/bind/delete', array('acid' => $account['acid'], 'uniacid' => $account['uniacid']))?>" onclick="return confirm('确认删除吗？');return false;" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="删除"><i class="fa fa-times"></i></a>
									<?php  } ?>
								<?php  } ?>
							</div>
						</li>
						<?php  } } ?>
					</ul>
				</div>
			</li>
			<?php  } } ?>
		</ul>
	<?php  echo $pager;?>
	</div>
<script>
	function toggleDetails(elm) {
		$(elm).parent().parent().parent().parent().nextAll().slideToggle();
	}

	require(['bootstrap'],function($){
		$('.account .panel-heading a, .btn, .fa-question-circle').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});

	//处理套餐变更
	require(['jquery', 'util'], function($, u){
		$('.account').delegate(".edit-group i","click",function(){
			var content = $(this).parent('div').html();
			var uniacid = $(this).attr('id');
			var groupid = $(this).parent().find('span').attr('id');
			
			var html = '<div class="input-group input-group-sm" style="line-height:30px; margin-top:5px;">' +
							'<select class="form-control" id="groupid" title="' + uniacid + '">' + 
								'<option value="0">基础服务</option>';
								<?php  if(is_array($group_account)) { foreach($group_account as $list) { ?>
									html += '<option value="<?php  echo $list['id'];?>"><?php  echo $list['name'];?></option>';
								<?php  } } ?>
				html = html +
							'</select>' +
							'<span class="input-group-btn"><button class="btn btn-primary btn-confirm" id="btn-confirm-' + uniacid + '">确定</button></span>' +
						'</div>';
			
			$(this).parent('div').html(html);
			$('select[title="' + uniacid + '"] option[value="' + groupid + '"]').attr('selected', true);
			
			$('#btn-confirm-' + uniacid).click(function() {
				var groupid = $('select[title="' + uniacid + '"] option:selected').val();
				$.post(location.href, {'groupid' : groupid, 'uniacid' : uniacid}, function(data){
					if(data == 'illegal-uniacid') {
						$('#btn-confirm-' + uniacid).parent().parent().parent().html(content);
						u.message('您没有操作该公众号的权限');
					} else if (data == 'illegal-group') {
						$('#btn-confirm-' + uniacid).parent().parent().parent().html(content);
						u.message('您没有使用该服务套餐的权限');
					} else {
						var content_edit = '服务套餐：<span id="' + groupid + '">' + data + '</span> <i class="fa fa-pencil" id="' + uniacid + '" style="cursor:pointer"></i>';
						$('#btn-confirm-' + uniacid).parent().parent().parent().html(content_edit);
					}
				});
			});
		});
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>