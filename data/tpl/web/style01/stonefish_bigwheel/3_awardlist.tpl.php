<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="main">
	<ul class="nav nav-tabs">
		<li<?php  if($_GPC['do'] == 'manage') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('manage');?>">大转盘管理</a></li>
		<li<?php  if($_GPC['do'] == 'post') { ?> class="active"<?php  } ?>><a href="<?php  echo url('platform/reply/post',array('m'=>'stonefish_bigwheel'));?>">添加大转盘规则</a></li>
		<li<?php  if($_GPC['do'] == 'fanslist') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('fanslist',array('name'=>'bigwheel', 'rid' => $rid));?>">参与粉丝</a></li>
		<li<?php  if($_GPC['do'] == 'awardlist') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('awardlist',array('name'=>'bigwheel', 'rid' => $rid));?>">中奖名单</a></li>
		<?php  if($stonefish_branch) { ?><li<?php  if($_GPC['do'] == 'branch') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('branch',array('rid' => $rid));?>">商家赠送项</a></li><?php  } ?>
		<li><a href="<?php  echo url('platform/reply/post',array('m'=>'stonefish_bigwheel', 'rid' => $rid));?>">编辑大转盘规则</a></li>
	</ul>
    <div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="site" />
			<input type="hidden" name="a" value="entry" />
        	<input type="hidden" name="m" value="stonefish_bigwheel" />
        	<input type="hidden" name="do" value="awardlist" />
        	<input type="hidden" name="rid" value="<?php  echo $_GPC['rid'];?>" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
				<div class="col-xs-12 col-sm-8 col-lg-9">
					<input class="form-control" name="keywords" id="" type="text" value="<?php  echo $_GPC['keywords'];?>" placeholder="可查询SN码,手机号"> 
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">奖品类别</label>
				<div class="col-xs-12 col-sm-8 col-lg-9">
					<select name="award" class="form-control" style="float:left">
                    	<option value="" <?php  if($_GPC['status']=='') { ?>selected<?php  } ?>>全部</option>
						<?php  if(is_array($award)) { foreach($award as $awards) { ?>
						<option value="<?php  echo $awards['prizetype'];?>" <?php  if($_GPC['award']==$awards['prizetype']) { ?>selected<?php  } ?>><?php  echo $awards['prizetype'];?></option>						
						<?php  } } ?>
                	</select> 
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
				<div class="col-xs-12 col-sm-8 col-lg-9">
					<select name="status" class="form-control" style="float:left">
                    	<option value="" <?php  if($_GPC['status']=='') { ?>selected<?php  } ?>>全部</option>
                        <option value="0" <?php  if($_GPC['status']=='0') { ?>selected<?php  } ?>>被取消</option>
						<option value="1" <?php  if($_GPC['status']=='1') { ?>selected<?php  } ?>>未兑换</option>
                        <option value="2" <?php  if($_GPC['status']=='2') { ?>selected<?php  } ?>>已兑换</option>
                	</select>
				</div>
                <div class=" col-xs-12 col-sm-2 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="panel panel-default">
<div class="panel-heading">
	<div class="row-fluid">
    	<div class="span8 control-group">
			<a class="btn btn-default<?php  if($_GPC['status']=='') { ?> btn-primary active<?php  } ?>" href="<?php  echo $this->createWebUrl('awardlist',array('rid'=>$rid,'award'=>$_GPC['award']))?>">全　部</a>
			<a class="btn btn-default<?php  if($_GPC['status']=='0') { ?> btn-primary active<?php  } ?>" href="<?php  echo $this->createWebUrl('awardlist',array('rid'=>$rid,'status'=>0,'award'=>$_GPC['award']))?>">被取消</a>
			<a class="btn btn-default<?php  if($_GPC['status']=='1') { ?> btn-primary active<?php  } ?>" href="<?php  echo $this->createWebUrl('awardlist',array('rid'=>$rid,'status'=>1,'award'=>$_GPC['award']))?>">未兑换</a>
			<a class="btn btn-default<?php  if($_GPC['status']=='2') { ?> btn-primary active<?php  } ?>" href="<?php  echo $this->createWebUrl('awardlist',array('rid'=>$rid,'status'=>2,'award'=>$_GPC['award']))?>">已兑换</a>			
        </div>
    </div>
	<div class="alert" style="margin-bottom:0;">本次活动奖品总数：<?php  echo $num1;?>个　　抽中被取消：<?php  echo $num4;?>个　　抽中未兑换：<?php  echo $num2;?>个　　抽中已兑换：<?php  echo $num3;?>个</div>
	<div class="row-fluid">
    	<div class="span8 control-group">
			<a class="btn btn-default<?php  if($_GPC['award']=='') { ?> btn-primary active<?php  } ?>" href="<?php  echo $this->createWebUrl('awardlist',array('rid'=>$rid,'status'=>$_GPC['status']))?>">全　部</a>
			<?php  if(is_array($award)) { foreach($award as $awardp) { ?>
			<a class="btn btn-default<?php  if($_GPC['award']==$awardp['prizetype']) { ?> btn-primary active<?php  } ?>" href="<?php  echo $this->createWebUrl('awardlist',array('rid'=>$rid,'status'=>$_GPC['status'],'award'=>$awardp['prizetype']))?>"><?php  echo $awardp['prizetype'];?></a>
			<?php  } } ?>			
        </div>
    </div>
	<div class="alert" style="margin-bottom:0;">
		<?php  if(is_array($award)) { foreach($award as $awardt) { ?>
		    <?php  echo $awardt['prizetype'];?>:<?php  echo $awardt['prizedraw'];?>个/<?php  echo $awardt['prizetotal'];?>个　　
		<?php  } } ?>			
	</div>
	<div class="row-fluid">
    	<div class="span8 control-group">			
			<a class="btn btn-primary" href="<?php  echo $this->createWebUrl('download',array('rid'=>$rid,'status'=>$_GPC['status'],'award'=>$_GPC['award']))?>"><i class="fa fa-download"></i> 导出<?php  echo $statustitle;?>兑奖信息</a>
        </div>
    </div>
</div>
	<form method="post" class="form-horizontal" id="form1">
	<input type="hidden" name="op" value="del" />
	<div style="position:relative">
		<div class="panel-body table-responsive">
			<table class="table table-hover" style="position:relative">
			<thead class="navbar-inner">
				<tr>
					<th style="width:50px;">删？</th>
					<th style="width:150px;">SN码</th>
					<th style="width:80px;">奖品类别</th>
					<th style="width:90px;">状态</th>
					<th width="120px">领取者姓名</th>
					<th style="width:120px;">领取者手机号</th>
					<th style="width:120px;">中奖时间</th>
					<th style="width:120px;">使用时间</th>
					<th style="width:180px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $row) { ?>
				<tr>
					<td><input type="checkbox" name="uid[]" value="<?php  echo $row['id'];?>" class=""></td>
                	<td><?php  echo $row['award_sn'];?></td>
					<td><?php  echo $row['name'];?></td>
					<td><?php  if($row['status']==0) { ?><span class="label label-danger">被取消
						<?php  } else if($row['status']==1) { ?><span class="label label-warning">未兑奖
						<?php  } else { ?><span class="label label-success">已兑奖<?php  } ?>
						<?php  if($row['xuni']==0) { ?>/真实</span>
						<?php  } else { ?>/虚拟</span><?php  } ?></td>
					<td><a href="javascript:void(0)" fansID="<?php  echo $row['fansID'];?>" class="btn btn-default btn-sm userinfo" style="width:100px;" data-toggle="tooltip" data-placement="top" title="用户兑奖参数项"><i class="fa fa-child"></i> <?php  echo $row['realname'];?></a></td>
					<td><?php  echo $row['mobile'];?></td>
					<td><?php  echo date('Y/m/d H:i',$row['createtime']);?></td>
					<td><?php  if($row['consumetime'] == 0) { ?>未使用<?php  } else { ?><?php  echo date('Y/m/d H:i',$row['consumetime']);?><?php  } ?></td>
					<td>
						<?php  if($row['status']==0) { ?><a class="btn btn-danger" href="#" onclick="drop_confirm('确认设置为已中奖?','<?php  echo $this->createWebUrl('setstatus',array('status'=>1,'id'=>$row['id'],'rid'=>$row['rid']))?>');"><i class="fa fa-ban"></i> 恢复中奖</a><?php  } ?>
						<?php  if($row['status']==1) { ?><a class="btn btn-default" href="#" onclick="drop_confirm('确认设置为未中奖?','<?php  echo $this->createWebUrl('setstatus',array('status'=>0,'id'=>$row['id'],'rid'=>$row['rid']))?>');"><i class="fa fa-times-circle-o"></i> 取消中奖</a><?php  } ?>
						<?php  if($row['status']==2) { ?><?php  if($row['credit_type']=='physical') { ?><a class="btn btn-success" href="#" onclick="drop_confirm('确认设置为未兑奖?','<?php  echo $this->createWebUrl('setstatus',array('status'=>3,'id'=>$row['id'],'rid'=>$row['rid']))?>');"><i class="fa fa-check-circle-o"></i> 取消兑奖</a><?php  } else { ?><a class="btn btn-success"><i class="fa fa-check-circle-o"></i> 虚拟奖品无法取消</a><?php  } ?>
						<?php  } else if($row['status']==1) { ?>
						<a class="btn btn-warning" href="#" onclick="drop_confirm('确认设置为已兑奖?','<?php  echo $this->createWebUrl('setstatus',array('status'=>2,'id'=>$row['id'],'rid'=>$row['rid']))?>');"><i class="fa fa-check-circle-o"></i> 确认兑奖</a>
						<?php  } ?>
					</td>
				</tr>
				<?php  } } ?>
				<tr>
					<td><input type="checkbox" name="" onclick="var ck = this.checked;$(':checkbox').each(function(){this.checked = ck});"></td>
					<td colspan="8"><input name="token" type="hidden" value="<?php  echo $_W['token'];?>" /><input type="submit" name="submit" class="btn btn-primary" value="删除选中的中奖记录"></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
</form>
</div>
<div style="text-align:center;"><?php  echo $pager;?></div>
</div>
<div id="guanbi" class="hide">
	<span type="button" class="pull-right btn btn-primary" data-dismiss="modal" aria-hidden="true">关闭</span>
</div>
<script>
require(['jquery', 'util'], function($, u){
		$('#form1').submit(function(){
		    if($(":checkbox[name='uid[]']:checked").size() > 0){
			    var check = $(":checkbox[name='uid[]']:checked");
			    if( confirm("确认要删除选择的粉丝中奖记录?")){
		            var id = new Array();
				    var rid = <?php  echo $rid;?>;
		            check.each(function(i){
			            id[i] = $(this).val();
		            });
		            $.post('<?php  echo $this->createWebUrl('deleteaward')?>', {idArr:id,rid:rid},function(data){
			        if (data.errno ==0){
						location.reload();
			        } else {
				        alert(data.error);
			        }
		            },'json');
		        }
		    }else{
		        u.message('没有选择粉丝', '', 'error');
		        return false;
		    }
	    });
		$('.userinfo').click(function(){
			var fansID = parseInt($(this).attr('fansID'));
			$.get("<?php  echo url('site/entry/userinfo',array('m' => 'stonefish_bigwheel','rid' => $rid))?>&fansID=" + fansID, function(data){
				if(data == 'dataerr') {
					u.message('未找到指定粉丝资料', '', 'error');
				} else {
					var obj = u.dialog('粉丝资料兑奖参数', data, $('#guanbi').html());
				}
				obj.modal('show');
			});
		})
		
	});
	function drop_confirm(msg, url){
    if(confirm(msg)){
        window.location = url;
    }
}
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>