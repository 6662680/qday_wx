<?php defined('IN_IA') or exit('Access Denied');?><div class="page-header">
	<h4><i class="fa fa-android"></i> 会员统计情况</h4>
</div>
<div class="panel panel-default">
	<div class="panel-body table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th style="width:400px;">公众号</th>
				<th style="width:400px;">会员数量</th>
				<th ></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php  echo $uniaccount['name'];?></td>
				<td>
					<p><?php  echo $uniaccount['membernum'];?></p>
				</td>
				<td> 
					<a href="<?php  echo url('mc/member/display');?>">查看</a>
				</td>
			</tr>
		</tbody>
	</table>
	</div>
</div>

<div class="page-header">
	<h4><i class="fa fa-android"></i> 主公号粉丝统计情况</h4>
</div>
<div class="panel panel-default">
	<div class="panel-body table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th style="width:400px;">主公众号</th>
				<th style="width:400px;">粉丝数量</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php  echo $uniaccount['name'];?></td>
				<td>
					<p><?php  echo $uniaccount['fansnum'];?></p>
				</td>
				<td> 
					<a href="<?php  echo url('mc/fans/display');?>">查看</a>
				</td>
			</tr>
		</tbody>
	</table>
	</div>
</div>
<div class="page-header">
	<h4><i class="fa fa-android"></i> 子公号粉丝统计情况</h4>
</div>
<div class="panel panel-default">
	<div class="panel-body table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th style="width:400px;">子公众号</th>
				<th style="width:400px;">粉丝数量</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php  if(is_array($accounts)) { foreach($accounts as $acid => $acc) { ?>
			<tr>
				<td><?php  echo $acc['name'];?></td>
				<td>
					<p><?php  echo $acc['fansnum'];?></p>
				</td>
				<td> 
					<a href="<?php  echo url('mc/fans/display', array('acid'=>$acid));?>">查看</a>
				</td>
			</tr>
			<?php  } } ?>
		</tbody>
	</table>
	</div>
</div>

<div class="page-header">
	<h4><i class="fa fa-android"></i> 营销统计情况</h4>
</div>
<div class="panel panel-default">
	<div class="panel-body table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th style="width:400px;">营销方式</th>
				<th style="width:400px;"></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>打折券</td>
				<td>
					<?php  if(is_array($coupons)) { foreach($coupons as $row) { ?>
					<p><?php  echo $row['title'];?></p>
					<?php  } } ?>
				</td>
				<td> 
					<?php  if(is_array($coupons)) { foreach($coupons as $row) { ?>
						<p><a href="<?php  echo url('activity/coupon/post', array('id'=>$row['couponid']));?>">查看</a></p>
					<?php  } } ?>
				</td>
			</tr>
		</tbody>
		<tbody>
			<tr>
				<td>代金券</td>
				<td>
					<?php  if(is_array($tokens)) { foreach($tokens as $row) { ?>
					<p><?php  echo $row['title'];?></p>
					<?php  } } ?>
				</td>
				<td> 
					<?php  if(is_array($tokens)) { foreach($tokens as $row) { ?>
						<p><a href="<?php  echo url('activity/coupon/post', array('id'=>$row['couponid']));?>">查看</a></p>
					<?php  } } ?>
				</td>
			</tr>
		</tbody>
	</table>
	</div>
</div>
