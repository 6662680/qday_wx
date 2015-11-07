<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/header-gw', TEMPLATE_INCLUDEPATH));?>
<ol class="breadcrumb">
	<li><a href="./?refresh"><i class="fa fa-home"></i></a></li>
	<li><a href="<?php  echo url('account/display');?>">公众号列表</a></li>
	<li><a href="<?php  echo url('account/post', array('uniacid' => $uniacid));?>">编辑主公众号</a></li>
	<li class="active">账号操作员列表</li>
</ol>
<?php  if($_GPC['reference'] != 'solution') { ?>
<ul class="nav nav-tabs">
	<li<?php  if($do == 'basic') { ?> class="active"<?php  } ?>><a href="<?php  echo url('account/post/basic', array('uniacid' => $uniacid));?>">账号基本信息</a></li>
	<?php  if($_W['isfounder']) { ?>
		<li class="active"><a href="<?php  echo url('account/permission', array('uniacid' => $uniacid));?>">账号操作员列表</a></li>
	<?php  } ?>
	<li<?php  if($do == 'details') { ?> class="active"<?php  } ?>><a href="<?php  echo url('account/post/list', array('uniacid' => $uniacid));?>">子公众号列表</a></li>
	<li><a href="<?php  echo url('account/switch', array('uniacid' => $uniacid));?>" style="color:#d9534f;"><i class="fa fa-cog fa-spin fa-fw"></i> 管理此公众号功能</a></li>
</ul>
<?php  } ?>
<div class="clearfix">
	<h5 class="page-header">设置可操作用户</h5>
	<div class="alert alert-info">
		<i class="fa fa-exclamation-circle"></i> 操作员不允许删除公众号和编辑公众号资料，管理员无此限制
	</div>
	<div class="panel panel-default">
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th style="width:50px;">选择</th>
					<th style="width:80px;">用户ID</th>
					<th style="width:150px;">用户名</th>
					<th style="width:200px;">角色</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
			<?php  if(is_array($permission)) { foreach($permission as $row) { ?>
				<tr <?php  if(!empty($_GPC['fromuid']) && $_GPC['fromuid']== $row['uid']) { ?>style="background:#dddddd;"<?php  } ?>>
					<td class="row-first"><input class="member" type="checkbox" value="<?php  echo $row['id'];?>" /></td>
					<td><?php  echo $row['uid'];?></td>
					<td><?php  echo $member[$row['uid']]['username'];?></td>
					<td>
						<label for="radio_<?php  echo $row['uid'];?>_1" class="radio-inline" style="padding-top:0; float:left; width:70px;"><input type="radio" name="role[<?php  echo $row['uid'];?>]" targetid="<?php  echo $row['uid'];?>" id="radio_<?php  echo $row['uid'];?>_1" value="operator" <?php  if(empty($row['role']) || $row['role'] == 'operator') { ?> checked<?php  } ?> /> 操作员</label>
						<label for="radio_<?php  echo $row['uid'];?>_2" class="radio-inline" style="padding-top:0; float:left; width:70px;"><input type="radio" name="role[<?php  echo $row['uid'];?>]" targetid="<?php  echo $row['uid'];?>" id="radio_<?php  echo $row['uid'];?>_2" value="manager" <?php  if($row['role'] == 'manager') { ?> checked<?php  } ?> /> 管理员</label>
					</td>
					<td><?php  if(!in_array($member[$row['uid']]['uid'], $founders)) { ?><a href="<?php  echo url('user/edit', array('uid' => $member[$row['uid']]['uid']));?>">编辑用户</a>&nbsp;|&nbsp;<a href="<?php  echo url('user/permission/menu', array('uid' => $member[$row['uid']]['uid'], 'uniacid' => $uniacid));?>">设置权限</a>&nbsp;|&nbsp;<a href="<?php  echo url('user/permission', array('uid' => $row['uid']))?>" target="_blank">查看操作权限</a><?php  } ?></td>
				</tr>
			<?php  } } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5">
						<input id="btn-add" class="btn btn-primary" type="button" value="添加账号操作员">
						<input id="btn-revo" class="btn btn-default" type="button" value="删除选定操作">
						<a class="btn" href="#" onclick="addUserPanel(this)">如果是添加一个新用户，请先添加该用户</a>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	</div>
</div>

<script type="text/javascript">
var seletedUserIds = <?php  echo json_encode($uids);?>;
require(['biz'], function(biz){
	$(function(){
		$('#btn-add').click(function(){
			biz.user.browser(seletedUserIds, function(us){
				$.post('<?php  echo url('account/permission', array('uniacid' => $uniacid, 'reference' => $_GPC['reference']));?>', {'do': 'auth', uid: us}, function(dat){
					if(dat == 'success') {
						location.reload();
					} else {
						alert('操作失败, 请稍后重试, 服务器返回信息为: ' + dat);
					}
				});
			},{mode:'invisible'});
		});
		$('#btn-revo').click(function(){
			$chks = $(':checkbox.member:checked');
			if($chks.length >0){
				if(!confirm('确认删除当前选择的用户?')){
					return;
				}
				var ids = [];
				$chks.each(function(){
					ids.push(this.value);
				});
				$.post('<?php  echo url('account/permission', array('uniacid' => $uniacid));?>',{'do':'revos', 'ids': ids},function(dat){
					if(dat == 'success') {
						location.reload();
					} else {
						alert('操作失败, 请稍后重试, 服务器返回信息为: ' + dat);
					}
				});
			}
		});
		$("input[name^='role[']").click(function(){
			$.post('<?php  echo url('account/permission/role', array('uniacid' => $uniacid));?>', {'uid' : $(this).attr('targetid'), 'role' : $(this).val()}, function(dat){
				if(dat != 'success') {
					u.message('设置管理员角色失败', "<?php  echo url('account/permission', array('uniacid' => $uniacid))?>", 'error');
				}
			});
		});
	});
});

function addUserPanel() {
	require(['util'], function(util){
		util.ajaxshow('<?php  echo url('user/create');?>', '添加管理员', {'width': 800});
	});
}
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer-gw', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-gw', TEMPLATE_INCLUDEPATH));?>
