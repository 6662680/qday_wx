<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<div class="main">
	<ul class="nav nav-tabs">
		<li<?php  if($_GPC['do'] == 'manage') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('manage');?>">刮刮卡管理</a></li>
		<li<?php  if($_GPC['do'] == 'post') { ?> class="active"<?php  } ?>><a href="<?php  echo url('platform/reply/post',array('m'=>'wdl_scratch'));?>">添加刮刮卡规则</a></li>
		<li<?php  if($_GPC['do'] == 'awardlist') { ?> class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('awardlist',array('name'=>'scratch', 'rid' => $rid));?>">中奖名单</a></li>
	</ul>
    <div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="site" />
			<input type="hidden" name="a" value="entry" />
        	<input type="hidden" name="m" value="wdl_scratch" />
        	<input type="hidden" name="do" value="awardlist" />
        	<input type="hidden" name="rid" value="<?php  echo $_GPC['rid'];?>" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
				<div class="col-xs-12 col-sm-8 col-lg-9">
					<input class="form-control" name="keywords" id="" type="text" value="<?php  echo $_GPC['keywords'];?>" placeholder="可查询SN码,手机号"> 
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
				<div class="col-xs-12 col-sm-8 col-lg-9">
					<select name="status" class="form-control" style="float:left">
                    	<option value="" <?php  if($_GPC['status']=='') { ?>selected<?php  } ?>>全部</option>
                        <option value="1" <?php  if($_GPC['status']=='1') { ?>selected<?php  } ?>>已中奖</option>
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
			<a class="btn btn-default" href="<?php  echo $this->createWebUrl('download',array('rid'=>$rid))?>"><i class="icon-download-alt"></i>导出SN码和兑奖信息</a>
			<a class="btn btn-default" href="<?php  echo $this->createWebUrl('awardlist',array('rid'=>$rid))?>">全部</a>
			<a class="btn btn-default" href="<?php  echo $this->createWebUrl('awardlist',array('rid'=>$rid,'status'=>1))?>">已中奖</a>
			<a class="btn btn-default" href="<?php  echo $this->createWebUrl('awardlist',array('rid'=>$rid,'status'=>2))?>">已兑换</a>
        </div>
    </div>
	<div class="alert" style="margin-bottom:0;">
		本次活动奖品总数：<?php  echo $num1;?>个　　抽中未兑换：<?php  echo $num2;?>个　　抽中已兑换：<?php  echo $num3;?>个　　 领取规则 : 先到先得！
	</div>
</div>
	<div style="position:relative">
		<div class="panel-body table-responsive">
			<table class="table table-hover" style="position:relative">
			<thead class="navbar-inner">
				<tr>
					<th style="width:50px;">序号</th>
					<th style="width:150px;">SN码</th>
					<th style="width:80px;">奖品类别</th>
					<th style="width:80px;">状态</th>
					<th width="120px">领取者手机号</th>
					<th style="width:120px;">领取者微信码</th>
					<th style="width:120px;">中奖时间</th>
					<th style="width:120px;">使用时间</th>
					<th style="width:300px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $row) { ?>
				<tr>
					<td><?php  echo $row['id'];?></td>
                	<td><?php  echo $row['award_sn'];?></td>
					<td><?php  echo $row['name'];?></td>
					<td><?php  if($row['status']==0) { ?><span class="label label-default">未领取</span>
						<?php  } else if($row['status']==1) { ?><span class="label label-success">已中奖</span>
						<?php  } else { ?><span class="label label-warning">已兑奖</span><?php  } ?></td>
					<td><span class="label label-info phone" data-fans="<?php  echo $row['from_user'];?>"}>显示手机号</span></td>
					<td><?php  echo $row['from_user'];?></td>
					<td><?php  echo date('Y/m/d H:i',$row['createtime']);?></td>
					<td><?php  if($row['consumetime'] == 0) { ?>未使用<?php  } else { ?><?php  echo date('Y/m/d H:i',$row['consumetime']);?><?php  } ?></td>
					<td>
						<a class="btn btn-default" href="#" onclick="drop_confirm('确认设置为未中奖?','<?php  echo $this->createWebUrl('setstatus',array('status'=>0,'id'=>$row['id'],'rid'=>$row['rid']))?>');"><i class="fa fa-ban"></i> 未中奖</a>
						<a class="btn btn-default" href="#" onclick="drop_confirm('确认设置为已中奖?','<?php  echo $this->createWebUrl('setstatus',array('status'=>1,'id'=>$row['id'],'rid'=>$row['rid']))?>');"><i class="fa fa-times-circle-o"></i> 已抽中</a>
						<a class="btn btn-default" href="#" onclick="drop_confirm('确认设置为已兑奖?','<?php  echo $this->createWebUrl('setstatus',array('status'=>2,'id'=>$row['id'],'rid'=>$row['rid']))?>');"><i class="fa fa-check-circle-o"></i> 已兑奖</a>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
</div>
<?php  echo $pager;?>
</div>
<script>
$(".phone").click(function() {
	$(".phone").addClass('label-teal');
	$(".phone").text('显示手机号');
	obj=$(this);
	obj.text('加载中...');
	fans=obj.attr('data-fans');
	$.post("<?php  echo $this->createWebUrl('getphone',array('rid'=>$rid))?>", { fans: fans},
	function(data){
		obj.removeClass('label-teal');
		obj.text(data);
	});

});
	function drop_confirm(msg, url){
    if(confirm(msg)){
        window.location = url;
    }
}
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>