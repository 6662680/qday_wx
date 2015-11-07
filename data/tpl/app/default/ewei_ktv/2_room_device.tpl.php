<?php defined('IN_IA') or exit('Access Denied');?><div id="cui-cid856232567" class="cui-view-page roomdetail-view-page emptyImage" style="position: absolute; left: 0px; top: 0px; width: 100%; z-index: 6947; height: 979px;">
    <div class="cui-view-page-content">
        <div class="roomdetail-box">
            <section class="roomdetailinfo">
                <span class="imgclose" onclick="clear_device()"><em>x</em></span>
                <ul class="infotop2">
                    <li class="toptxt imgempty" data-title=""><?php  echo $item['title'];?></li>
                </ul>
                <ul class="infocontent">
                    <li class="ui-grid-a">
                        <?php  if($item['area_show'] == 1) { ?>
                        <div class="ui-block-a"><span class="list area"></span><?php  echo $item['area'];?>平方米</div>
                        <?php  } ?>
                        <?php  if($item['floor_show'] == 1) { ?>
                        <div class="ui-block-b"><span><span class="list floor"></span><?php  echo $item['floor'];?>楼</span></div>
                        <?php  } ?>
                    </li>

                    <li class="ui-grid-a">
                        <?php  if($item['smoke_show'] == 1) { ?>
                        <div class="ui-block-a"><span class="list nosmoking"></span><?php  echo $item['smoke'];?></div>
                        <?php  } ?>
                        <?php  if($item['bed_show'] == 1) { ?>
                        <div class="ui-block-b"><span class="list bed"></span><?php  echo $item['bed'];?></div>
                        <?php  } ?>
                    </li>

                    <li class="ui-grid-a">
                        <?php  if($item['persons_show'] == 1) { ?>
                        <div class="ui-block-a"><span><span class="list people"></span><?php  echo $item['persons'];?>人</span></div>
                        <?php  } ?>
                        <?php  if($item['bedadd_show'] == 1) { ?>
                        <div class="ui-block-b"><span class="list extrabed"></span><?php  echo $item['bedadd'];?></div>
                        <?php  } ?>
                    </li>

                    <?php  if($item['sales'] != '') { ?>
                    <p><span class="ktvicon ktv_cu">促</span><br>
                        <?php  echo $item['sales'];?>
                    </p>
                    <?php  } ?>

                    <?php  if($item['device'] != '') { ?>
                    <p><span class="ktv_li">说明</span><br>
                        <?php  echo $item['device'];?>
                    </p>
                    <?php  } ?>
                </ul>
                <ul class="infobottom ui-grid-a">
                    <li class="ui-block-a"><dfn>¥</dfn><strong class="price size20" data-cny="RMB"><?php  echo $price;?></strong></li>
                    <li class="ui-block-b">
                        <?php  if($has == 0) { ?>
                        <p class="room_btn" style='text-align:right;'>
                            <input value="满房" class="ui-btn-order bookBtn" type="button" style="width: 60px;-webkit-appearance: none;border-radius:0px;background:#ccc;color:#fff">
                        </p>
                        <?php  } else { ?>
                        <?php  if(($this->_set_info['ordertype'] == 1)) { ?>
                        <input type="button" class="btn-order" value="在线预订" style="-webkit-appearance: none;border-radius:0px;" extension=""
                               onclick="location.href='<?php  echo $this->createMobileUrl('order', array('hid' => $hid, 'id' => $item['id'], 'price' => $price, 'total_price' => $total_price))?>'">
                        <?php  } ?>
                        <input value="电话预订" class="btn-order" onclick="location.href='tel:<?php  echo $tel;?>'" type="button"  style="-webkit-appearance: none;border-radius:0px;"/>
                        <?php  } ?>

                </ul>
            </section>
        </div>
    </div>
</div>