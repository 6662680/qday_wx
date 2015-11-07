<?php defined('IN_IA') or exit('Access Denied');?><form method="post" action="<?php  echo url('mc/creditmanage/manage')?>" id="form1">
	<input type="hidden" name="do" value="manage">
	<input type="hidden" name="uid" value="<?php  echo $data['uid'];?>">
	<div class="table-responsive">
	<table class="table" style="min-width:568px;">
	<tr>
		<td style="border-top:none;vertical-align:middle;width:80px;"><strong>用户ID</strong><br> <?php  echo $data['uid'];?></td>
		<td style="border-top:none;vertical-align:middle;width:90px;"><strong>姓名</strong> <br><?php  if($data['realname']) { ?> <?php  echo $data['realname'];?> <?php  } else { ?> 未完善 <?php  } ?></td>
		<td style="border-top:none;vertical-align:middle;"><strong>邮箱</strong> <br><?php  if($data['email']) { ?> <?php  echo $data['email'];?> <?php  } else { ?> 未完善 <?php  } ?></td>
		<td style="border-top:none;vertical-align:middle"><strong>手机</strong> <br><?php  if($data['mobile']) { ?> <?php  echo $data['mobile'];?> <?php  } else { ?> 未完善 <?php  } ?></td>
	</tr>
	<?php  if(is_array($creditnames)) { foreach($creditnames as $index => $creditname) { ?>
		<tr>
			<td style="border-top:none;vertical-align:middle"><strong><?php  echo $creditname['title'];?></strong></td>
			<td style="border-top:none;vertical-align:middle"><strong><?php  echo $data[$index];?></strong></td>
			<td style="border-top:none;vertical-align:middle">
				<label><input type="radio" style="vertical-align:-1px;" name="<?php  echo $index;?>_type" value="1" checked> 增加</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label><input type="radio" style="vertical-align:-1px;" name="<?php  echo $index;?>_type" value="2"> 减少</label>
			</td>
			<td style="border-top:none;vertical-align:middle;width:100px;"> 
				<input type="text" name="<?php  echo $index;?>_value"  value="" class="form-control">
			</td>
		<tr>
	<?php  } } ?>
		<tr>
			<td colspan="2" style="border-top:none;vertical-align:middle;width:120px"><strong>积分操作备注</strong></td>
			<td colspan="2" style="border-top:none;vertical-align:middle">
				<textarea class="form-control" style="width:350px;" name="remark"></textarea>
			</td>
		</tr>
	<input name="token" type="hidden" value="<?php  echo $_W['token'];?>"/>
	</table>
	</div>
</form>

