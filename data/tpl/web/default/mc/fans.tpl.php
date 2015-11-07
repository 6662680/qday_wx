<?php defined('IN_IA') or exit('Access Denied');?><?php  $newUI = true;?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style>
.table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td{white-space:nowrap;}
</style>
<ul class="nav nav-tabs">
	<li <?php  if($do == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo url('mc/fans/display');?>">粉丝列表</a></li>
	<?php  if($do == 'view' && !empty($fanid)) { ?><li class="active"><a href="<?php  echo url('mc/fans/view', array('id' => $fanid));?>">粉丝详情</a></li><?php  } ?>
</ul>
<?php  if($do == 'display') { ?>
<script type="text/javascript">
	var running = false;
	window.onbeforeunload = function(e) {
		if(running) {
			return (e || window.event).returnValue = '正在进行粉丝数据同步中, 离开此页面将会中断操作.';
		}
	}
	
	require(['angular', 'jquery', 'util'], function(angular, $, u){
		$('#form1').submit(function(){
			if($(":checkbox[name='delete[]']:checked").size() > 0){
				return confirm('删除后不可恢复，您确定删除吗？');
			}
			alert('没有选择粉丝');
			return false;
		});
		angular.module('app', []).controller('advAPI', function($scope, $http) {
			$scope.adv = {
				running : false,
				syncState : '',
				downloadState : '',
				enabled : <?php  if($account && ($account['type'] == '1' && $account['level'] >= '3')) { ?>true<?php  } else { ?>false<?php  } ?>
			};	
			$scope.sync = function(){
				if($(":checkbox:checked").size() <= 0){
					alert('没有选择粉丝');
					return;
				}
				u.message('正在同步粉丝信息<br>请不要离开页面或进行其他操作,同步成功后系统会自动刷新本页面');
				$scope.adv.running = running = true;
				var fanids = [];
				$(':checkbox:checked').each(function(){
					var fanid = parseInt($(this).val());
					if(!isNaN(fanid)) {
						fanids.push(fanid);
					}
				});
				var params = {};
				params.method = 'sync';
				params.fanids = fanids;
				$http.post(location.href, params).success(function(dat){
					$scope.adv.running = running = false;
					if(dat == 'success') {
						location.reload();
					} else {
						message('未知错误, 请稍后重试.', location.href, 'error')
					}
				});
			};
			$scope.download = function(next, count){
				$scope.adv.running = running = true;
				var params = {};
				params.method = 'download';
				if(next) {
					params.next = next;
				}
				if(!count) {
					count = 0;
				}
				$http.post(location.href, params).success(function(dat){
					if(dat.errno || dat.type == 'error' || dat.type == 'info') {
						$scope.adv.downloadState = '';
						$scope.adv.running = running = false;
						u.message(dat.message, location.href, 'error');
						return;
					}

					count += dat.count;
					if((dat.total <= count) || (!dat.count && !dat.next)) {
						$scope.adv.downloadState = '';
						$scope.adv.running = running = false;
						u.message('粉丝下载完成,系统将开始更新粉丝数据,请不要离开页面', "<?php  echo url('mc/fans/initsync', array('acid' => $acid))?>", 'success');
						return;
					} else {
						$scope.download(dat.next, count);
						$scope.adv.downloadState = '(' + count + '/' + dat.total + ')';
					}
				});
			}
		});
		angular.bootstrap(document, ['app']);
	});
</script>
<div class="clearfix">
	<div class="alert alert-info">
		如果您的公众号类型为："认证订阅号" 或 "认证服务号",您可以使用粉丝分组功能。点击这里 <a href="<?php  echo url('mc/fangroup')?>">"同步粉丝分组"</a>
	</div>
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
				<input type="hidden" name="c" value="mc" />
				<input type="hidden" name="a" value="fans" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">所属公众号</label>
					<div class="col-sm-9 col-md-8 col-lg-8 col-xs-12">
						<select class="form-control" name="acid">
							<option value="">所有公众号</option>
							<?php  if(is_array($accounts)) { foreach($accounts as $acc) { ?>
							<option value="<?php  echo $acc['acid'];?>"<?php  if($acc['acid'] == $acid) { ?> selected="selected"<?php  } ?>><?php  echo $acc['name'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">昵称</label>
					<div class="col-sm-9 col-md-8 col-lg-8 col-xs-12">
						<input type="text" class="form-control" name="nickname" value="<?php  echo $nickname;?>"/>
					</div>
				</div>
					<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户组</label>
					<div class="col-sm-9 col-md-8 col-lg-8 col-xs-12">
						<label class="radio-inline">
							<input type="radio" name="type" value="" <?php  if($type == '') { ?>checked="checked"<?php  } ?>/> 不指定
						</label>
						<label class="radio-inline">
							<input type="radio" name="type" value="bind" <?php  if($type == 'bind') { ?>checked="checked"<?php  } ?>/> 已经注册为会员
						</label>
						<label class="radio-inline">
							<input type="radio" name="type" value="unbind" <?php  if($type == 'unbind') { ?>checked="checked"<?php  } ?>/> 未注册为会员
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否关注</label>
					<div class="col-sm-9 col-md-8 col-lg-8 col-xs-12">
						<label class="radio-inline">
							<input type="radio" name="follow" value="" <?php  if($follow == '') { ?>checked="checked"<?php  } ?>/> 不限
						</label>
						<label class="radio-inline">
							<input type="radio" name="follow" value="1" <?php  if($follow == '1') { ?>checked="checked"<?php  } ?>/> 已关注
						</label>
						<label class="radio-inline">
							<input type="radio" name="follow" value="2" <?php  if($follow == '2') { ?>checked="checked"<?php  } ?>/> 取消关注
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">关注/取消关注时间</label>
					<div class="col-sm-9 col-md-8 col-lg-8 col-xs-12">
						<?php  load()->func('tpl');?>
						<?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $starttime),'endtime'=>date('Y-m-d', $endtime)));?>
					</div>
					<div class="pull-right col-xs-12 col-sm-3 col-md-2 col-lg-2">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<form action="?<?php  echo $_SERVER['QUERY_STRING'];?>" method="post" id="form1">
	<div class="panel panel-default">
	<div class="panel-body table-responsive" ng-controller="advAPI">
		<table class="table table-hover"  style="width:100%;z-index:-10;" cellspacing="0" cellpadding="0">
			<thead class="navbar-inner">
				<tr>
					<th style="width:30px;">删？</th>
					<th style="width:90px;">头像</th>
					<th style="width:150px;">昵称</th>
					<th style="width:180px;">对应用户</th>
					<th style="width:80px;">是否关注</th>
					<th style="width:180px;">关注/取消时间</th>
					<th style="min-width:70px;" class="text-right">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td><input type="checkbox" name="delete[]" value="<?php  echo $item['fanid'];?>" /></td>
					<td><img src="<?php  if(!empty($item['user']['avatar'])) { ?><?php  echo $item['user']['avatar'];?><?php  } else { ?>resource/images/noavatar_middle.gif<?php  } ?>" width="48"></td>
					<td><?php  echo $item['user']['nickname'];?></td>
					<td>
						<?php  if(empty($item['uid'])) { ?>
						<a href="<?php  echo url('mc/member/post', array('uid' => $item['uid'],'openid' => $item['openid'], 'fanid' => $item['fanid']));?>" class="text-danger" title="该用户尚未注册会员，请为其手动注册！">[ 注册为会员 ]</a>
						<?php  } else { ?>
						<a href="<?php  echo url('mc/member/post', array('uid'=>$item['uid']));?>"><span><?php  if($item['user']['niemmo_effective'] == 1) { ?><?php  echo $item['user']['niemmo'];?><?php  } else { ?><?php  echo $item['uid'];?><?php  } ?></span></a>
						<?php  } ?>
					</td>
					<td>
					<?php  if($item['follow'] == '1') { ?>
						<span class="label label-success">已关注 </span> 
					<?php  } else if($item['unfollowtime'] <> '0') { ?>
						<span class="label label-warning" >取消关注 </span>
					<?php  } else { ?>
						<span class="label label-danger">未关注 </span>
					<?php  } ?>
					</td>
					<td>
						<?php  if($item['follow'] == '1') { ?><?php  echo date('Y-m-d H:i:s', $item['followtime'])?><?php  } else if(!empty($item['unfollowtime'])) { ?><?php  echo date('Y-m-d H:i:s', $item['unfollowtime'])?><?php  } else { ?>&nbsp;<?php  } ?>
					</td>
					<td><?php  echo $item['account'];?></td>
					<td class="text-right" style="overflow:visible;">
						<?php  if(!empty($groups[$acid])) { ?>
						<div class="btn-group">
							<button class="btn btn-danger btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
								<span id="<?php  echo $item['fanid'];?>-name"><?php  echo $groups[$item['acid']][$item['groupid']]['name'];?></span>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
							<?php  if(!empty($groups[$item['acid']])) { ?>
								<?php  if(is_array($groups[$item['acid']])) { foreach($groups[$item['acid']] as $li) { ?>
									<li><a href="javascript:;" class="groupedit" title="<?php  echo $li['name'];?>" data-acid="<?php  echo $item['acid'];?>" data-groupid="<?php  echo $li['id'];?>" data-openid="<?php  echo $item['openid'];?>" data-fanid="<?php  echo $item['fanid'];?>"><?php  echo $li['name'];?></a></li>
								<?php  } } ?>
							<?php  } ?>
							</ul>
						</div>
						<?php  } ?>
						<a href="<?php  echo url('mc/notice/tpl', array('id' => $item['fanid']));?>" id="<?php  echo $item['fanid'];?>" class="btn btn-success btn-sm sms">发送消息</a>
						<a href="<?php  echo url('mc/fans/view', array('id' => $item['fanid']));?>" class="btn btn-default btn-sm">查看详情</a>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
		<table class="table table-hover">
			<tr>
				<td width="30">
					<input type="checkbox" onclick="var ck = this.checked;$(':checkbox').each(function(){this.checked = ck});" />
				</td>
				<td class="text-left">
					<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
					<input type="submit" class="btn btn-primary span2" name="submit" value="删除" /> &nbsp; 
					<input type="button" class="btn btn-default" name="submit" value="同步粉丝信息{{adv.enabled ? (adv.running ? adv.syncState : '') : '(<?php  if(empty($account)) { ?>请指定公众号<?php  } else { ?>需要高级接口<?php  } ?>)'}}" ng-click="sync();" ng-disabled="!adv.enabled || adv.running" /> &nbsp;
					<input type="button" class="btn btn-default" name="submit" value="下载所有粉丝{{adv.enabled ? (adv.running ? adv.downloadState : '') : '(<?php  if(empty($account)) { ?>请指定公众号<?php  } else { ?>需要高级接口<?php  } ?>)'}}" ng-click="download();" ng-disabled="!adv.enabled || adv.running" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span class="help-block">同步粉丝信息: 选定粉丝后, 访问公众平台获取特定粉丝的相关资料, 如果已对应用户, 那么将会把未登记的资料填充至关联用户. 需要为认证微信服务号, 或者高级易信号</span>
					<span class="help-block">下载所有粉丝: 访问公众平台下载所有粉丝列表(这个操作不能获取粉丝资料, 只能获取粉丝标志). 需要为认证微信服务号, 或者高级易信号</span>
				</td>
			</tr>
		</table>
	</div>
	</div>
	<?php  echo $pager;?>
	</form>
</div>
<script>
	$('.btn, .title').hover(function(){
		$(this).tooltip('show');
	},function(){
		$(this).tooltip('hide');
	});

	require(['util', 'jquery'], function(u, $){
		$('.groupedit').click(function(){
			var acid = $(this).attr('data-acid');
			var groupid = $(this).attr('data-groupid');
			var openid = $(this).attr('data-openid');
			var fanid = $(this).attr('data-fanid');
			var groupname = $(this).html();
			if(!acid) {
				u.message('公众号id错误', '', 'error');
			}
			if(!openid) {
				u.message('粉丝openid错误', '', 'error');
			}
			$.post('<?php  echo url("mc/fans/updategroup");?>', {'openid' : openid, 'acid' : acid, 'groupid': groupid}, function(data){
				var data = $.parseJSON(data);
				if(data.status == 'error') {
					u.message(data.mess, '', 'error');
				} else if(data.status == 'success'){
					$('#' + fanid + '-name').html(groupname);
				}
			});
		});

/*
		//发送消息
		$('.sms').click(function(){
			var fanid = $(this).attr('id');
			if(!fanid) {
				u.message('粉丝id错误', '', 'error');
				return;
			}

			$.get("<?php  echo url('mc/notice/fans')?>&fanid=" + fanid, function(data){
				var obj = u.dialog('发送客服消息', data, '');
				obj.modal('show');
				return;
			});
		});
*/
	});

</script>
<?php  } ?>
<?php  if($do == 'view') { ?>
<div class="form-horizontal form">
	<div class="panel panel-default">
		<div class="panel-heading">
			粉丝详情
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">对应会员</label>
				<div class="col-sm-10">
					<span class="help-block"><?php  if(empty($row['uid'])) { ?><?php  echo $row['user'];?><?php  } else { ?><a href="<?php  echo url('mc/member/post', array('uid'=>$row['uid']));?>"><?php  echo $row['user'];?></a><?php  } ?></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝编号</label>
				<div class="col-sm-10">
					<span class="help-block"><?php  echo $row['openid'];?></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">所属公众号</label>
				<div class="col-sm-10">
					<span class="help-block"><?php  echo $row['account'];?></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否关注</label>
				<div class="col-sm-10">
					<span class="help-block"><?php  if($row['follow'] == '1') { ?> <span class="label label-success" >已订阅 </span> <?php  } else if($row['unfollowtime'] <> '0') { ?> <span class="label label-warning"> 取消关注 </span> <?php  } else { ?> <span class="label label-danger" >未订阅 </span><?php  } ?></span>
				</div>
			</div>
			<?php  if($row['follow'] == '1' && $row['followtime'] <> '0') { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">关注时间</label>
				<div class="col-sm-10">
					<span class="help-block"><?php  echo date('Y-m-d H:i:s', $row['followtime'])?></span>
				</div>
			</div>
			<?php  } else if($row['unfollowtime'] <> '0') { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">取消关注时间</label>
				<div class="col-sm-10">
					<span class="help-block"><?php  echo date('Y-m-d H:i:s', $row['unfollowtime'])?></span>
				</div>
			</div>
			<?php  } else { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">关注时间</label>
				<div class="col-sm-10">
					<span class="help-block">未记录</span>
				</div>
			</div>
			<?php  } ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
				<div class="col-sm-10">
					<span class="help-block"><a href="javascript:history.go(-1);" class="btn btn-primary">返回</a></span>
				</div>
			</div>
		</div>
	</div>
</div>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
