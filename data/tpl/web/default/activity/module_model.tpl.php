<?php defined('IN_IA') or exit('Access Denied');?><div class="clearfix user-browser">
	<div class="form-horizontal">
		<table class="table tb" style="min-width:568px;">
			<thead>
			<tr>
				<th style="width: 35px;" class="text-center">选择</th>
				<th style="width: 100px;">模块名称</th>
				<th style="width: 100px;">标识</th>
			</tr>
			</thead>
			<tbody class="module-list">
			<?php  if(is_array($new)) { foreach($new as $row) { ?>
			<tr id="module-<?php  echo $row['name'];?>">
				<td class="text-center"><input type="checkbox" id="chk_module_<?php  echo $row['name'];?>" name="modules[]" value="<?php  echo $row['name'];?>"></td>
				<td><label for="chk_module_<?php  echo $row['name'];?>" style="font-weight:normal;" class="title"><?php  echo $row['title'];?></label></td>
				<td><label class="label label-info"><?php  echo $row['name'];?></label></td>
			</tr>
			<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<script>
	var selected = $('#form1 input[name="module-select"]').val();
	if(selected) {
		selected = selected.split('@');
		for(var j=0;j<selected.length;j++) {
			$('#module-' + selected[j] + ' :checkbox').prop('checked', true);
		}
	}
</script>