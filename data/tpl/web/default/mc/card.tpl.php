<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style>
.card{position:relative; width:400px; max-height:218px; overflow:hidden;}
.cardsn{position:absolute; color:#666; right:20px; bottom:10px; text-shadow:0 -1px 0 rgba(255, 255, 255, 0.5); font-size:16px;}
.cardtitle{position:absolute; right:20px; top:10px; color:#ffffff; font-size:16px; text-shadow:0 -1px 0 rgba(255, 255, 255, 0.5);}
.cardlogo{position:absolute; top:10px; left:20px;}
</style>
<ul class="nav nav-tabs">
	<li <?php  if($do == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo url('mc/card/display');?>">会员卡设置</a></li>
	<li <?php  if($do == 'manage') { ?> class="active"<?php  } ?>><a href="<?php  echo url('mc/card/manage');?>">会员卡管理</a></li>
</ul>

<!-- 会员卡设置 -->
<?php  if($do == 'display') { ?>
<div class="clearfix">
<form action="" class="form-horizontal form" method="post" enctype="multipart/form-data" id="form1">
	<div class="panel panel-default">
		<div class="panel-heading">
			是否启用会员卡：
			<input type="checkbox" name="flag" value="1" <?php  if(intval($setting['status'])==1) { ?> checked="checked" <?php  } ?> data="<?php  echo $setting['id'];?>"/>
		</div>
	</div>
	<?php  if(($setting['status'] != 0)) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				会员卡预览
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
						<div class="card img-rounded">
							<div class="cardsn" style="color:<?php  if(!empty($setting['color']['number'])) { ?><?php  echo $setting['color']['number'];?><?php  } ?>">卡号：<?php  echo $setting['format'];?></div>
							<div class="cardtitle" style="<?php  if(!empty($setting['color']['title'])) { ?><?php  echo $setting['color']['title'];?><?php  } ?>"><?php  if(!empty($setting['title'])) { ?><?php  echo $setting['title'];?><?php  } ?></div>
							<div class="cardlogo"><img src="<?php  if(!empty($setting['logo'])) { ?><?php  echo tomedia($setting['logo'])?><?php  } else { ?>../attachment/images/global/card/logo.png<?php  } ?>"></div>
							<img class="cardbg" 
								src="<?php  if(empty($setting['background']['image'])) { ?>
										../attachment/images/global/card/1.png
									 <?php  } else if($setting['background']['background'] == 'system') { ?>
									 	../attachment/images/global/card/<?php  echo $setting['background']['image'];?>.png
									 <?php  } else { ?>
									 	<?php  echo tomedia($setting['background']['image']);?>
									 <?php  } ?>"
									width="400px" height="218px" />
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel panel-default" id="cardmain">
			<div class="panel-heading">
				会员卡设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">名称<span style="color:red">*</span></label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="title" value="<?php  echo $setting['title'];?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">名称颜色</label>
					<div class="col-sm-9 col-xs-12">
						<?php  if(!empty($setting['color']['title'])) { ?>
							<?php  echo tpl_form_field_color('color-title', $setting['color']['title']);?>
						<?php  } else { ?>
							<?php  echo tpl_form_field_color('color-title');?>
						<?php  } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">卡号颜色</label>
					<div class="col-sm-9 col-xs-12">
						<?php  if(!empty($setting['color']['number'])) { ?>
							<?php  echo tpl_form_field_color('color-number', $setting['color']['number']);?>
						<?php  } else { ?>
							<?php  echo tpl_form_field_color('color-number');?>
						<?php  } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">卡号设置<span style="color:red">*</span></label>
					<div class="col-sm-9 col-xs-12">
						<input name="format" type="text"  value="<?php  echo $setting['format'];?>" class="form-control" />
						<span class="help-block">
						<p>"*"代表任意随机数字，<span style="color:red">"#"代表流水号码, "#"必须连续出现,且只能存在一组.</span></p>
						<p>卡号规则样本："WQ2013*****#####***"</p>
						注意：规则位数过小会造成卡号生成重复概率增大，过多的重复卡密会造成卡密生成终止
						卡密规则中不能带有中文及其他特殊符号
						为了避免卡密重复，随机位数最好不要少于8位
						</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">使用说明<span style="color:red">*</span></label>
					<div class="col-sm-9 col-xs-12">
						<textarea class="form-control" name="description" rows="6"><?php  echo $setting['description'];?></textarea>
						<span class="help-block">请填写会员卡的使用说明。</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">背景图案</label>
					<div class="col-sm-9 col-xs-12">
						<label for="isshow1" class="radio-inline">
							<input type="radio" name="background" value="system" id="isshow1" onclick="$('#system').show();$('#user').hide();" checked="checked" <?php  if(empty($setting['background']['background']) ||$setting['background']['background'] == 'system') { ?> checked<?php  } ?> autocomplete="off"> 系统
						</label>&nbsp;&nbsp;&nbsp;
						<label for="isshow2" class="radio-inline">
							<input type="radio" name="background" value="user" id="isshow2" onclick="$('#system').hide();$('#user').show();" <?php  if(!empty($setting['background']['background']) && $setting['background']['background'] == 'user') { ?> checked<?php  } ?> autocomplete="off"> 自定义
						</label>
					</div>
				</div>
				<div class="form-group"  id="system" <?php  if(!empty($setting['background']['background']) && $setting['background']['background'] != 'system') { ?> style="display:none;"<?php  } ?>>
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
						<select style="width: 227px" class="form-control" id="select_bg" name="system-bg">
							<?php  for ($i=1; $i<=23; $i++) {?>
								<option value="<?php  echo $i;?>" <?php  if(!empty($setting['background']['image']) && $setting['background']['image'] == $i) { ?> selected<?php  } ?>>背景<?php  echo $i;?></option>
							<?php  } ?>
						</select>
					</div>
				</div>
				<div class="form-group" id="user" <?php  if(empty($setting['background']['background']) || $setting['background']['background'] != 'user') { ?> style="display:none;"<?php  } ?>>
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
					<div class="col-sm-9 col-xs-12">
						<?php  if(!empty($setting['background']['background'])) { ?>
							<?php echo tpl_form_field_image('user-bg', $setting['background']['background'] == 'user' ? $setting['background']['image'] : '');?>
						<?php  } else { ?>
							<?php  echo tpl_form_field_image('user-bg');?>
						<?php  } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">LOGO</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_image('logo', $setting['logo']);?>
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="panel panel-default">
			<div class="panel-heading">
				会员卡资料
			</div>
			<div class="panel-body table-responsive">
				<table class="table table-hover">
				<thead>
					<tr>
						<th style="min-width:200px;">资料名称</th>
						<th style="width:100px;">必填</th>
						<th style="width:300px;">关联默认值</th>
						<th style="width:120px;"></th>
					</tr>
				</thead>
				<tbody id="re-items">
					<?php  if(!empty($setting['fields'])) { ?>
						<?php  if(is_array($setting['fields'])) { foreach($setting['fields'] as $item) { ?>
							<?php  if($item['bind'] != 'realname' && $item['bind'] != 'mobile') { ?>
								<tr>
									<td><input name="fields[title][]" type="text" class="form-control" value="<?php  echo $item['title'];?>" /></td>
									<td>
										<input type="checkbox" title="必填项"  <?php  if($item['require']) { ?> checked="checked"<?php  } ?>/>
										<input type="hidden" name="fields[require][]" value="<?php  if($item['require']) { ?>1<?php  } else { ?>0<?php  } ?>"/>
									</td>
									<td>
										<select name="fields[bind][]" class="form-control">
											<?php  if(is_array($fields)) { foreach($fields as $k => $v) { ?>
												<?php  if(!empty($v)) { ?>
													<option value="<?php  echo $k;?>"<?php  if($k == $item['bind']) { ?> selected="selected"<?php  } ?>><?php  echo $v;?></option>
												<?php  } ?>
											<?php  } } ?>
										</select>
									</td>
									<td>
										<a href="javascript:;" class="fa fa-arrows" title="拖动调整此条目显示顺序"></a> &nbsp; 
										<a href="javascript:;" onclick="deleteItem(this)" title="删除此条目" class="fa fa-times-circle" ></a>
									</td>
								</tr>
							<?php  } else { ?>
								<tr>
									<td><input name="fields[title][]" type="text" class="form-control" value="<?php  echo $item['title'];?>" readonly/></td>
									<td>
										<input type="checkbox" title="必填项" checked="checked" disabled/>
										<input type="hidden" name="fields[require][]" value="1" readonly/>
									</td>
									<td>
										<select name="fields[bind][]" class="form-control" readonly>
											<option value="<?php  echo $item['bind'];?>" selected="selected"><?php  if($item['bind'] == 'realname') { ?>真实姓名<?php  } else { ?>手机<?php  } ?></option>
										</select>
									</td>
									<td>
										<a href="javascript:;" class="fa fa-arrows" title="拖动调整此条目显示顺序"></a> &nbsp; 
									</td>
								</tr>
							<?php  } ?>
						<?php  } } ?>
					<?php  } else { ?>
						<tr>
							<td><input name="fields[title][]" type="text" class="form-control" value="姓名" readonly/></td>
							<td>
								<input type="checkbox" title="必填项" checked="checked" disabled/>
								<input type="hidden" name="fields[require][]" value="1" readonly/>
							</td>
							<td>
								<select name="fields[bind][]" class="form-control" readonly>
									<option value="realname" selected="selected">真实姓名</option>
								</select>
							</td>
							<td>
								<a href="javascript:;" class="fa fa-arrows" title="拖动调整此条目显示顺序"></a> &nbsp; 
							</td>
						</tr>
						<tr>
							<td><input name="fields[title][]" type="text" class="form-control" value="手机号码" readonly/></td>
							<td>
								<input type="checkbox" title="必填项" checked="checked" disabled/>
								<input type="hidden" name="fields[require][]" value="1" readonly/>
							</td>
							<td>
								<select name="fields[bind][]" class="form-control" readonly>
									<option value="mobile" selected="selected">手机</option>
								</select>
							</td>
							<td>
								<a href="javascript:;" class="fa fa-arrows" title="拖动调整此条目显示顺序"></a> &nbsp; 
							</td>
						</tr>
					<?php  } ?>
				</tbody>
			</table>
			<div class="alert alert-block alert-new">
					<a href="javascript:;" onclick="addItem();"><i class="fa fa-plus-circle" title="添加填写项目"></i> 添加填写项目</a>
				</div>
				<span class="help-block">用户领取会卡时，需要完善此处设置的信息。<span class="text-danger">(注:系统会自动绑定:真实姓名和手机号码,编辑后,请点击提交按钮)</span></span>
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-sm-12">
				<button type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">提交</button>
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	<?php  } ?>
</div>
<script type="text/javascript">
	require(['jquery.ui', 'bootstrap.switch', 'util'], function($, $, u){
		$(function(){
			$(":checkbox[name='flag']").bootstrapSwitch();
			$(':checkbox').on('switchChange.bootstrapSwitch', function(e, state){
				$this = $(this);
				var status = this.checked ? 1 : 0;
				$.post(location.href, {status:status}, function(resp){
					if(resp != 'success') {
						u.message('操作失败, 请稍后重试.')
					} else {
						u.message('操作成功', location.href, 'success');
					}
				});
			});
			$('.btn').hover(function(){
				$(this).tooltip('show');
			},function(){
				$(this).tooltip('hide');
			});
		});
	
		$(function(){
			$('#re-items').sortable({handle: '.fa-arrows'});
			$('#form1').submit(function() {
				$(':checkbox').each(function(){
					if($(this).prop('checked') == true) {
						$(this).next().val('1');
					} else {
						$(this).next().val('0');
					}
				});
			});
			//会员卡预览
			$('#cardmain').mouseover(function() {
				var a = $('input[name="title"]').val();
				var b = $('input[name="color-title"]').val();
				var c = $('input[name="color-number"]').val();
				var d = '卡号：'+$('input[name="format"]').val();
				if($("#system").css("display") != 'none') {
					var e = '../attachment/images/global/card/'+$('select[name="system-bg"]').val()+'.png';
					$('.cardbg').attr("src", e);
				} else if($("#user").css("display") != 'none') {
					var e = $('input[name="user-bg"]').val();
					if(e.indexOf("http://") == -1 && e.indexOf("https://") == -1) {
						e = "<?php  echo $_W['attachurl'];?>"+e;
					}
					$('.cardbg').attr("src", e);
				}
				var f = $('input[name="logo"]').val();
				if(f.indexOf("http://") == -1 && f.indexOf("https://") == -1) {
					f = "<?php  echo $_W['attachurl'];?>"+f;
				}
				$('.cardtitle').html(a).css("color", b);
				$('.cardsn').html(d).css("color", c);
				$('.cardlogo img').attr("src", f);
			});
		});
	});

	function addItem() {
		var html = '' +
				'<tr>' +
					'<td><input name="fields[title][]" type="text" class="form-control" /></td>' +
					'<td><input type="checkbox" title="必填项" />' +
					'<input type="hidden" name="fields[require][]" value="0"/></td>' +
					'<td>' +
						'<select name="fields[bind][]" class="form-control">' +
						<?php  if(is_array($fields)) { foreach($fields as $k => $v) { ?><?php  if(!empty($v)) { ?>'<option value="<?php  echo $k;?>"><?php  echo $v;?></option>' + <?php  } ?><?php  } } ?>
						'</select>' +
					'</td>' +
					'<td><a href="javascript:;" class="fa fa-arrows" title="拖动调整此条目显示顺序" ></a> &nbsp; <a href="javascript:;" onclick="deleteItem(this)" class="fa fa-times-circle"  title="删除此条目"></a></td>' +
				'</tr>';
		$('#re-items').append(html);
	}
	function deleteItem(o) {
		$(o).parent().parent().remove();
	}
</script>
<?php  } ?>


<!-- 会员卡列表 -->
<?php  if(($do == 'manage') && ($setting['status'] == 1)) { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form" id="form">
		<input type="hidden" name="c" value="mc">
		<input type="hidden" name="a" value="card">
		<input type="hidden" name="do" value="manage">
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">卡号</label>
				<div class="col-sm-8 col-xs-12">
					<input type="text" class="form-control" name="cardsn" value="<?php  echo $_GPC['cardsn'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">使用状态</label>
				<div class="col-sm-6 col-md-8 col-lg-8 col-xs-12">
					<select name="status" class="form-control">
						<option value="" selected="selected">不限</option>
						<option value="1" <?php  if($status == 1) { ?> selected="selected" <?php  } ?>>启用</option>
						<option value="0" <?php  if($status === 0) { ?> selected="selected" <?php  } ?>>禁用</option>
					</select>
				</div>
				<div class="pull-right col-xs-12 col-sm-3 col-md-2 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="panel panel-default">
<div class="panel-body table-responsive">
	<table class="table table-hover">
		<thead class="navbar-inner">
			<tr>
				<th>卡号</th>
				<th>姓名</th>
				<th>手机号</th>
				<th>领卡时间</th>
				<th>状态</th>
				<th>是否开启</th>
				<th>操作</th>
			</tr>
		</thead>
		<?php  if(is_array($list)) { foreach($list as $row) { ?>
			<tr>
				<td><?php  echo $row['cardsn'];?></td>
				<td><?php  echo $row['realname'];?></td>
				<td><?php  echo $row['mobile'];?></td>
				<td><?php  echo date('Y-m-d H:i:s', $row['createtime']);?></td>
				<td>
					<?php  if($row['status'] == 1) { ?>
						<span class="label label-success">可用</span>
					<?php  } else { ?>
						<span class="label label-warning">禁用</span>
					<?php  } ?>
				</td>
				<td>
					<input type="checkbox" value="1" <?php  if(intval($row['status'])==1) { ?> checked="checked" <?php  } ?> data="<?php  echo $row['id'];?>"/>
				</td>
				<td>
					<a href="<?php  echo url('mc/card/delete', array('cardid' => $row['id']));?>;" onclick="return confirm('此操作不可恢复，确认删除？');return false;" data-toggle="tooltip" data-placement="top" title="" class="btn btn-default btn-sm" data-original-title="删除"><i class="fa fa-times"></i></a>
					<a href="<?php  echo url('mc/member/post', array('uid' => $row['uid']));?>;"  class="btn btn-default btn-sm" data-original-title="更多会员信息"><i class="fa fa-eye"></i></a>
				</td>
			</tr>
		<?php  } } ?>
	</table>
</div>
</div>
<?php  echo $pager;?>
<script type="text/javascript">
	require(['bootstrap.switch', 'util'], function($, u){
		$(function(){
			var cardsn = $('input[name="cardsn"]')[0];
			cardsn.selectionStart = cardsn.value.length;
			cardsn.focus();
			$(':checkbox').bootstrapSwitch();
			$(':checkbox').on('switchChange.bootstrapSwitch', function(e, state){
				$this = $(this);
				var cardid = $this.attr('data');
				var status = this.checked ? 1 : 0;
				$.post(location.href, {cardid:cardid, status:status}, function(resp){
					if(resp != 'success') {
						u.message('操作失败, 请稍后重试.')
					}
					<?php  if(!empty($module)) { ?>
					else {
						window.setTimeout(function(){location.href = location.href;}, 300);
					}
					<?php  } else { ?>
						if (status == 1) {
							$this.parent().parent().parent().prev().html('<span class="label label-success">可用</span>');
						} else {
							$this.parent().parent().parent().prev().html('<span class="label label-warning">禁用</span>');
						}
					<?php  } ?>
				});
			});
			$('.btn').hover(function(){
				$(this).tooltip('show');
			},function(){
				$(this).tooltip('hide');
			});
		});
	});
</script>
<?php  } ?>


<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>