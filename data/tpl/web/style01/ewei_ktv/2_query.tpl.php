<?php defined('IN_IA') or exit('Access Denied');?><table class="table table-hover" style="min-width:850px;">
    <tbody>   
        <?php  if(is_array($ds)) { foreach($ds as $item) { ?>
        <tr>
            <td><?php  echo $item['title'];?></td>
            <td style="width:80px;"><a href="javascript:;" onclick='select_ktv(<?php  echo json_encode($item);?>)'>选择</a></td>
        </tr>
        <?php  } } ?>
        <?php  if(count($ds)<=0) { ?>
        <tr>
            <td colspan='4' align='center'>未找到ktv</td>
        </tr>
        <?php  } ?>
    </tbody>
</table>
