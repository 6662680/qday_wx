<?php defined('IN_IA') or exit('Access Denied');?>    <div class="table-responsive" style="height:300px;overflow-Y: auto; overflow-X:hidden;">
	    <table class="table" style="min-width:568px;">
				<tr>
				    <td style="border-top:none;vertical-align:middle;"><strong>用户ID</strong> <?php  echo $data['fansID'];?></td>
				    <td style="border-top:none;vertical-align:middle;"><strong>姓名</strong> <?php  if($data['realname']) { ?> <?php  echo $data['realname'];?> <?php  } else { ?> 未完善 <?php  } ?></td>
				    <td style="border-top:none;vertical-align:middle"><strong>手机</strong> <?php  if($data['mobile']) { ?> <?php  echo $data['mobile'];?> <?php  } else { ?> 未完善 <?php  } ?></td>
				</tr>
		</table>
		<table class="table" style="min-width:568px;">
				<tr>
					<th style="width:130px;">奖品类别</th>
					<th style="width:120px;">状态</th>
					<th style="width:140px;">中奖时间</th>
					<th style="width:140px;">使用时间</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $row) { ?>
				<tr>
					<td><?php  echo $row['name'];?></td>
					<td><?php  if($row['status']==0) { ?><span class="label label-danger">被取消</span>
						<?php  } else if($row['status']==1) { ?><span class="label label-warning">未兑奖</span>
						<?php  } else { ?><span class="label label-success">已兑奖</span><?php  } ?>
						<?php  if($row['xuni']==0) { ?><span class="label label-success">真实</span>						
						<?php  } else { ?><span class="label label-default">虚拟</span><?php  } ?>
						</td>					
					<td><?php  echo date('Y/m/d H:i',$row['createtime']);?></td>
					<td><?php  if($row['consumetime'] == 0) { ?>未使用<?php  } else { ?><?php  echo date('Y/m/d H:i',$row['consumetime']);?><?php  } ?></td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
	</div>