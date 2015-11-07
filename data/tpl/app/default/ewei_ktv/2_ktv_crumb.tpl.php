<?php defined('IN_IA') or exit('Access Denied');?><?php  if(is_array($list)) { foreach($list as $row) { ?>
<ul class="hot_list_tab" onClick="location.href='<?php  echo $this->createMobileUrl('detail', array('hid' => $row['id']))?>'" ktvid="374783" data-full="0">
    <li class="hot_img">
        <img width="67px" height="50px" src="<?php  echo tomedia($row['thumb'])?>">
    </li>

    <li class="hot_ktv">
        <p class="name"><?php  echo $row['title'];?></p>
        <br>
        <p>
            <?php  echo $this->_ktv_level_config[$row['level']]?>
        </p>
        <p class="placeholders"></p>
    </li>

    <li class="hot_price">
        <dfn>¥</dfn><strong><?php  echo $row['m_price'];?></strong><span class="txt_gray">起</span>
    </li>
</ul>
<?php  } } ?>