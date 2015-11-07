<?php defined('IN_IA') or exit('Access Denied');?><div class="main">
    <div class="panel panel-default">
    <div class="panel-body">
        <table class="table table-hover">
            <thead class="navbar-inner">
                <tr>
                    <th style="max-width:20px;">传单ID</th>
                    <th style="max-width:300px;">传单名</th>
                    <th style="max-width:20px;">使用状态</th>
                    <th style="text-align:right; max-width:60px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($mylist)) { foreach($mylist as $item) { ?>
                <tr style="color:green">
                    <td><?php  echo $item['channel'];?></td>
                    <td><?php  echo $item['title'];?></td>
                    <td><?php  if($item['active']) { ?>使用中<?php  } ?></td>
                    <td style="text-align:right;">
                        <a class="btn btn-default" href="<?php  echo $this->createWebUrl('Spread', array('op' => 'active', 'channel'=>$item['channel']))?>" title="设置为当前使用的传单" class="btn btn-small">
                            <i class="fa fa-heart" <?php  if($item['active']) { ?>style='color:red'<?php  } ?>></i> 
                            设置为当前使用的传单</a>
                        <a class="btn btn-default"  href="<?php  echo $this->createWebUrl('Spread', array('op' => 'post', 'channel'=>$item['channel']))?>" title="编辑" class="btn btn-small"><i class="fa fa-edit"></i> 编辑</a>
                        <a class="btn btn-default"  href="<?php  echo $this->createWebUrl('Spread', array('op' => 'delete', 'channel'=>$item['channel']))?>" title="删除" onclick="return confirm('此操作不可恢复，确定？');
                    return false;" class="btn btn-small"><i class="fa fa-remove"></i> 删除</a>
                    </td>
                </tr>
                <?php  } } ?>
            </tbody>
        </table>
        <?php  echo $pager;?>
    </div>
</div>
</div>
