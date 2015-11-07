<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<div class="main">
	<ul class="nav nav-tabs">
		<li class="active"><a href="<?php  echo $this->createWebUrl('package',array('op'=>'list'));?>">套餐管理</a></li>
		<li><a href="<?php  echo $this->createWebUrl('packedit',array('op'=>'add'));?>">添加套餐</a></li>
	</ul>


	<div class="panel panel-default">
		<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
			<tr><th class='with-checkbox' style='width:30px;'>
				<input type="checkbox" class="check_all" /></th>
				<th style="width:120px;">套餐名称</th>
				<th style='width:100px;'>价格</th>
				<th style='width:100px;'>会员价</th>
				<th style='width:120px;'>服务项目</th>
				<th style='width:100px;'>详情</th>
				<th style='width:100px;'>是否显示</th>
				<th style='width:100px;'>是否推荐</th>
				<th style="width:550px;">操作</th>
			</tr>
			</thead>
			<tbody>
			<?php  if(is_array($list)) { foreach($list as $item) { ?>
            <tr>
                <td class="with-checkbox">
                <input type="checkbox" name="check" value="<?php  echo $item['id'];?>"></td>
                <td><?php  echo $item['p_name'];?></td>
                <td><?php  echo $item['price'];?></td>
                <td><?php  echo $item['m_price'];?></td>
                <td>
                    <?php  if(is_array($item['s_item'])) { foreach($item['s_item'] as $v) { ?>
                        <span style="margin-right: 5px;"><?php  echo $v['item_name'];?></span>
                    <?php  } } ?>
                </td>
                <td><?php  echo $item['detail'];?></td>
                <td>
                    <?php  if($item['is_show']==0) { ?>
                    <span class='label label-success'>显示</span>
                    <?php  } else { ?>
                    <span class='label label-default'>隐藏</span>
                    <?php  } ?>
                </td>
                <td>
                    <?php  if($item['is_recommend']==1) { ?>
                    <span class='label label-success'>推荐</span>
                    <?php  } else { ?>
                    <span class='label label-default'>不推荐</span>
                    <?php  } ?>
                </td>
                <td>
                    <a class="btn  btn-default btn-sm" rel="tooltip" href="<?php  echo $this->createWebUrl('packedit',array('op'=>'edit','id'=>$item['id']))?>" title="编辑" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-default" href="#" onclick="drop_confirm('您确定要删除吗?', '<?php  echo $this->createWebUrl('packedit',array('op'=>'delete', 'id'=>$item['id']))?>');" title="删除" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-times"></i></a>
                </td>
            </tr>
			<?php  } } ?>
			<tr>
				<td colspan="7">
					<input type="button" class="btn btn-primary" name="deleteall" value="删除选择的" />
				</td>
			</tr>
			</tbody>
			<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
		</table>
	</div>
	</div>
	<?php  echo $pager;?>
</div>
<script>
	require(['bootstrap'],function($){
		$('.btn').tooltip();
	});
</script>
<script>
	$(function(){
		$(".check_all").click(function(){
			var checked = $(this).get(0).checked;
			$(':checkbox').each(function(){this.checked = checked});
		});
		$("input[name=deleteall]").click(function(){
			var check = $("input:checked");
			if(check.length<1){
				err('请选择要删除的记录!');
				return false;
			}
			if( confirm("确认要删除选择的记录?")){
				var id = new Array();
				check.each(function(i){
					id[i] = $(this).val();
				});
				$.post("<?php  echo $this->createWebUrl('hotel',array('op'=>'deleteall'))?>", {idArr:id},function(data){
					if (data.errno ==0)
					{
						location.reload();
					} else {
						alert(data.error);
					}
				},'json');
			}
		});

		$(".edit_all").click(function(){
			var name = $(this).attr('name');
			var check = $("input:checked");
			if(check.length<1){
				err('请选择要操作的记录!');
				return false;
			}

			var id = new Array();
			check.each(function(i){
				id[i] = $(this).val();
			});
			$.post("<?php  echo $this->createWebUrl('hotel',array('op'=>'showall'))?>", {idArr:id,show_name:name},function(data){
				if (data.errno ==0)
				{
					location.reload();
				} else {
					alert(data.error);
				}
			},'json');
		});
	});
</script>
<script>
	function drop_confirm(msg, url){
		if(confirm(msg)){
			window.location = url;
		}
	}
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
