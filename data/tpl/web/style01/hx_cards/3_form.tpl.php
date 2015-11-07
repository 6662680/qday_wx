<?php defined('IN_IA') or exit('Access Denied');?><div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h5 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">基本参数</a>
      </h5>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
        <input type="hidden" name="reply_id" value="<?php  echo $reply['id'];?>" />
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动标题</label>
          <div class="col-sm-9">
            <input class="form-control" type="text" value="<?php  echo $reply['title'];?>" class="span2" name="title">
            <div class="help-block">活动的标题</div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">缩略图</label>
          <div class="col-sm-9">
            <?php  echo tpl_form_field_image('thumb',$reply['thumb'],'', $options);?>
            <div class="help-block">图文消息的缩略图</div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动页面logo</label>
          <div class="col-sm-9">
            <?php  echo tpl_form_field_image('logo',$reply['logo'],'', $options);?>
            <div class="help-block">活动页面logo,请使用png背景透明图片</div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动简介</label>
          <div class="col-sm-9">
            <textarea class="form-control" name="description"><?php  echo $reply['description'];?></textarea>
            <div class="help-block">图文消息的简介</div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动时间</label>
          <div class="col-sm-4">
            <?php  echo tpl_form_field_daterange('time', array('start'=>date('Y-m-d H:i:s',$reply['starttime']),'end'=>date('Y-m-d H:i:s',$reply['endtime'])), true)?>
            <div class="help-block">输入活动的起止时间</div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动状态</label>
          <div class="col-sm-2">
             <label><input type="radio" value="0" name="status" <?php  if($reply['status'] == 0 ) { ?>checked<?php  } ?>>结束</label>
             <label><input type="radio" value="1" name="status" <?php  if($reply['status'] == 1 ) { ?>checked<?php  } ?>>正常</label>
             <label><input type="radio" value="2" name="status" <?php  if($reply['status'] == 2 ) { ?>checked<?php  } ?>>暂停</label>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">可参与活动的用户组</label>
          <div class="col-sm-4">
            <select name="groupid" class="form-control" multiple="multiple">
              <option value="0" <?php  if($reply['groupid'] == '0') { ?>selected<?php  } ?>>全部会员组</option>
              <?php  if(is_array($groups)) { foreach($groups as $group) { ?>
              <option value="<?php  echo $group['groupid'];?>" <?php  if($reply['groupid'] == $group['groupid']) { ?>selected<?php  } ?>><?php  echo $group['title'];?></option>
              <?php  } } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">每次抽奖所需消耗</label>
          <div class="col-sm-4">
          	<div class="input-group">
              <span class="input-group-addon">
            <?php  if(is_array($creditnames['creditnames'])) { foreach($creditnames['creditnames'] as $scredit => $creditname) { ?>
             <label><input type="radio" value="<?php  echo $scredit;?>" name="need_type" <?php  if($reply['need_type'] == $scredit ) { ?>checked<?php  } ?>><?php  echo $creditname['title'];?></label>
            <?php  } } ?>
            </span>
            <input class="form-control" type="text" value="<?php  echo $reply['need_num'];?>" class="span2" name="need_num">
        </div>
          </div>
        </div>
        
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">每人最多获奖次数</label>
          <div class="col-sm-2">
            <div class="input-group">
              <input class="form-control" type="text" value="<?php  echo $reply['awardnum'];?>" class="span2" name="awardnum">
              <span class="input-group-addon">次</span>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">每人最多抽奖次数</label>
          <div class="col-sm-2">
            <div class="input-group">
              <input class="form-control" type="text" value="<?php  echo $reply['playnum'];?>" class="span2" name="playnum">
              <span class="input-group-addon">次</span>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">每人每天最多抽奖次数</label>
          <div class="col-sm-2">
            <div class="input-group">
              <input class="form-control" type="text" value="<?php  echo $reply['dayplaynum'];?>" class="span2" name="dayplaynum">
              <span class="input-group-addon">次</span>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">转发赠送抽奖次数</label>
          <div class="col-sm-4">
            <div class="input-group">
              <span class="input-group-addon">每转发</span>
              <input class="form-control" type="text" value="<?php  echo $reply['zfcs'];?>" class="span2" name="zfcs">
              <span class="input-group-addon">次，增加总数</span>
              <input class="form-control" type="text" value="<?php  echo $reply['zjcs'];?>" class="span2" name="zjcs">
              <span class="input-group-addon">次</span>
            </div>
          </div>
        </div>
       <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">每次抽奖赠送</label>
          <div class="col-sm-6" >
            <div class="input-group">
              <span class="input-group-addon">
              <?php  if(is_array($creditnames['creditnames'])) { foreach($creditnames['creditnames'] as $scredit => $creditname) { ?>
              <label><input type="radio" value="<?php  echo $scredit;?>" name="give_type" <?php  if($reply['give_type'] == $scredit ) { ?>checked<?php  } ?>><?php  echo $creditname['title'];?></label>
              <?php  } } ?>
              </span>
              <input class="form-control" type="text" value="<?php  echo $reply['give_num'];?>" class="span2" name="give_num">
              <span class="input-group-addon">
              <label><input type="checkbox" name="onlynone" value="1" <?php  if($reply['onlynone'] == 1 ) { ?>checked<?php  } ?>> 仅送给未中奖的用户</label>
              </span>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义未中奖说明</label>
          <div class="col-sm-9">
          	<textarea class="form-control" name="noprize" style="height: 150px;"><?php  echo $reply['noprize'];?></textarea>
            <div class="help-block">自定义未中奖说明，一行一个</div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">活动说明</label>
          <div class="col-sm-9">
            <textarea class="form-control" name="tips"><?php  echo $reply['tips'];?></textarea>
            <div class="help-block">活动页面的说明</div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">备注</label>
          <div class="col-sm-9">
            <textarea class="form-control" name="remark"><?php  echo $reply['remark'];?></textarea>
            <div class="help-block">活动页面的备注</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h5 class="panel-title">
        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">奖品设置</a>
      </h5>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-5 control-label">综合中奖率</label>
          <div class="col-sm-2">
            <div class="input-group">
              <input class="form-control" type="text" value="<?php  echo $reply['rate'];?>" class="span2" name="rate">
              <span class="input-group-addon">%</span>
            </div>
            中奖率必须为整数
          </div>
        </div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#home" role="tab" data-toggle="tab">奖品一</a>
          </li>
          <li role="presentation">
            <a href="#profile" role="tab" data-toggle="tab">奖品二</a>
          </li>
          <li role="presentation">
            <a href="#messages" role="tab" data-toggle="tab">奖品三</a>
          </li>
          <li role="presentation">
            <a href="#settings" role="tab" data-toggle="tab">奖品四</a>
          </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="home">
            <h5>奖品一参数设置</h5>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">选择奖品</label>
              <div class="col-sm-9" style="padding-top: 7px;">
                <?php  $i = 1;?>
                <?php  if(is_array($creditnames['creditnames'])) { foreach($creditnames['creditnames'] as $scredit => $creditname) { ?>
                <?php  $t = str_replace("$('#p1_".$i."').hide()","$('#p1_".$i."').show()","$('#p1_1').hide();$('#p1_2').hide();$('#p1_3').hide();$('#p1_4').hide();$('#p1_5').hide();");?>
                <label>
                  <input type="radio" value="<?php  echo $scredit;?>" name="p1_type" onclick="$('#p1_zkq').hide();$('#p1_djq').hide();$('#p1_zswp').hide();<?php  echo $t;?>" <?php  if($prizes['p1_type'] == $scredit ) { ?>checked<?php  } ?>><?php  echo $creditname['title'];?></label>
                <?php  $i++;?>
                <?php  } } ?>
                <label>
                  <input type="radio" value="2" name="p1_type" onclick="$('#p1_jf').hide();$('#p1_zkq').show();$('#p1_djq').hide();$('#p1_zswp').hide();$('#p1_1').hide();$('#p1_2').hide();$('#p1_3').hide();$('#p1_4').hide();$('#p1_5').hide();" <?php  if($prizes['p1_type'] == 2 ) { ?>checked<?php  } ?>>折扣券</label>
                <label>
                  <input type="radio" value="3" name="p1_type" onclick="$('#p1_jf').hide();$('#p1_zkq').hide();$('#p1_djq').show();$('#p1_zswp').hide();$('#p1_1').hide();$('#p1_2').hide();$('#p1_3').hide();$('#p1_4').hide();$('#p1_5').hide();" <?php  if($prizes['p1_type'] == 3 ) { ?>checked<?php  } ?>>代金券</label>
                <label>
                  <input type="radio" value="4" name="p1_type" onclick="$('#p1_jf').hide();$('#p1_zkq').hide();$('#p1_djq').hide();$('#p1_zswp').show();$('#p1_1').hide();$('#p1_2').hide();$('#p1_3').hide();$('#p1_4').hide();$('#p1_5').hide();" <?php  if($prizes['p1_type'] == 4 ) { ?>checked<?php  } ?>>真实物品</label>
              </div>
            </div>
            <?php  $j = 1;?>
            <?php  if(is_array($creditnames['creditnames'])) { foreach($creditnames['creditnames'] as $scredit => $creditname) { ?>
            <div class="form-group" id="p1_<?php  echo $j;?>" <?php  if($prizes['p1_type'] != $scredit) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">赠送<?php  echo $creditname['title'];?></label>
              <div class="col-sm-4">
                <div class="input-group">
                  <?php  $v = p1_.$scredit;?>
                  <input class="form-control" type="text" value="<?php  echo $prizes['p1_score'];?>" name="<?php  echo $v;?>" id="<?php  echo $v;?>">
                  <span class="input-group-addon">个</span>
                </div>
              </div>
            </div>
            <?php  $j++;?>
            <?php  } } ?>
            <div class="form-group" id="p1_zkq" <?php  if($prizes['p1_type'] != 2 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">折扣券</label>
              <div class="col-sm-4">
                <select name="p1_2" class="form-control">
                  <?php  if(is_array($couponlists)) { foreach($couponlists as $couponlist) { ?>
                  <?php  $num = $couponlist['amount'] - $couponlist['dosage'];?>
                  <option value="<?php  echo $couponlist['couponid'];?>" <?php  if($prizes['p1_score'] == $couponlist['couponid']) { ?>selected<?php  } ?>><?php  echo $couponlist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=coupon&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group" id="p1_djq" <?php  if($prizes['p1_type'] != 3 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">代金券</label>
              <div class="col-sm-4">
                <select name="p1_3" class="form-control">
                  <?php  if(is_array($tokenlists)) { foreach($tokenlists as $tokenlist) { ?>
                <?php  $num = $tokenlist['amount'] - $tokenlist['dosage'];?>
                  <option value="<?php  echo $tokenlist['couponid'];?>" <?php  if($prizes['p1_score'] == $tokenlist['couponid']) { ?>selected<?php  } ?>><?php  echo $tokenlist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=token&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group" id="p1_zswp" <?php  if($prizes['p1_type'] != 4 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">真实物品</label>
              <div class="col-sm-4">
                <select name="p1_4" class="form-control">
                  <?php  if(is_array($goodslists)) { foreach($goodslists as $goodslist) { ?>
                <?php  $num = $goodslist['total'] - $goodslist['num'];?>
                  <option value="<?php  echo $goodslist['id'];?>" <?php  if($prizes['p1_score'] == $goodslist['id']) { ?>selected<?php  } ?>><?php  echo $goodslist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=goods&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">奖品数量</label>
              <div class="col-sm-4">
                <div class="input-group">
                  <input class="form-control" type="text" value="<?php  echo $prizes['p1_num'];?>" name="p1_num">
                  <span class="input-group-addon">份</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">奖品图片</label>
              <div class="col-sm-4">
                <?php  echo tpl_form_field_image('p1_thumb',$prizes['p1_thumb'],'', $options);?>
                <div class="help-block">图文消息的缩略图</div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="profile">
            <h5>奖品二参数设置</h5>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">选择奖品</label>
              <div class="col-sm-9" style="padding-top: 7px;">
                <?php  $i = 1;?>
                <?php  if(is_array($creditnames['creditnames'])) { foreach($creditnames['creditnames'] as $scredit => $creditname) { ?>
                <?php  $t = str_replace("$('#p2_".$i."').hide()","$('#p2_".$i."').show()","$('#p2_1').hide();$('#p2_2').hide();$('#p2_3').hide();$('#p2_4').hide();$('#p2_5').hide();");?>
                <label>
                  <input type="radio" value="<?php  echo $scredit;?>" name="p2_type" onclick="$('#p2_zkq').hide();$('#p2_djq').hide();$('#p2_zswp').hide();<?php  echo $t;?>" <?php  if($prizes['p2_type'] == $scredit ) { ?>checked<?php  } ?>><?php  echo $creditname['title'];?></label>
                <?php  $i++;?>
                <?php  } } ?>
                <label>
                  <input type="radio" value="2" name="p2_type" onclick="$('#p2_jf').hide();$('#p2_zkq').show();$('#p2_djq').hide();$('#p2_zswp').hide();$('#p2_1').hide();$('#p2_2').hide();$('#p2_3').hide();$('#p2_4').hide();$('#p2_5').hide();" <?php  if($prizes['p2_type'] == 2 ) { ?>checked<?php  } ?>>折扣券</label>
                <label>
                  <input type="radio" value="3" name="p2_type" onclick="$('#p2_jf').hide();$('#p2_zkq').hide();$('#p2_djq').show();$('#p2_zswp').hide();$('#p2_1').hide();$('#p2_2').hide();$('#p2_3').hide();$('#p2_4').hide();$('#p2_5').hide();" <?php  if($prizes['p2_type'] == 3 ) { ?>checked<?php  } ?>>代金券</label>
                <label>
                  <input type="radio" value="4" name="p2_type" onclick="$('#p2_jf').hide();$('#p2_zkq').hide();$('#p2_djq').hide();$('#p2_zswp').show();$('#p2_1').hide();$('#p2_2').hide();$('#p2_3').hide();$('#p2_4').hide();$('#p2_5').hide();" <?php  if($prizes['p2_type'] == 4 ) { ?>checked<?php  } ?>>真实物品</label>
              </div>
            </div>
            <?php  $j = 1;?>
            <?php  if(is_array($creditnames['creditnames'])) { foreach($creditnames['creditnames'] as $scredit => $creditname) { ?>
            <div class="form-group" id="p2_<?php  echo $j;?>" <?php  if($prizes['p2_type'] != $scredit) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">赠送<?php  echo $creditname['title'];?></label>
              <div class="col-sm-4">
                <div class="input-group">
                  <?php  $v = p2_.$scredit;?>
                  <input class="form-control" type="text" value="<?php  echo $prizes['p2_score'];?>" id="<?php  echo $v;?>" name="<?php  echo $v;?>">
                  <span class="input-group-addon">个</span>
                </div>
              </div>
            </div>
            <?php  $j++;?>
            <?php  } } ?>
            <div class="form-group" id="p2_zkq" <?php  if($prizes['p2_type'] != 2 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">折扣券</label>
              <div class="col-sm-4">
                <select name="p2_2" class="form-control">
                  <?php  if(is_array($couponlists)) { foreach($couponlists as $couponlist) { ?>
                <?php  $num = $couponlist['amount'] - $couponlist['dosage'];?>
                  <option value="<?php  echo $couponlist['couponid'];?>" <?php  if($prizes['p2_score'] == $couponlist['couponid']) { ?>selected<?php  } ?>><?php  echo $couponlist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=coupon&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group" id="p2_djq" <?php  if($prizes['p2_type'] != 3 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">代金券</label>
              <div class="col-sm-4">
                <select name="p2_3" class="form-control">
                  <?php  if(is_array($tokenlists)) { foreach($tokenlists as $tokenlist) { ?>
                <?php  $num = $tokenlist['amount'] - $tokenlist['dosage'];?>
                  <option value="<?php  echo $tokenlist['couponid'];?>" <?php  if($prizes['p2_score'] == $tokenlist['couponid']) { ?>selected<?php  } ?>><?php  echo $tokenlist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=token&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group" id="p2_zswp" <?php  if($prizes['p2_type'] != 4 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">真实物品</label>
              <div class="col-sm-4">
                <select name="p2_4" class="form-control">
                  <?php  if(is_array($goodslists)) { foreach($goodslists as $goodslist) { ?>
                <?php  $num = $goodslist['total'] - $goodslist['num'];?>
                  <option value="<?php  echo $goodslist['id'];?>" <?php  if($prizes['p2_score'] == $goodslist['id']) { ?>selected<?php  } ?>><?php  echo $goodslist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=goods&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">奖品数量</label>
              <div class="col-sm-4">
                <div class="input-group">
                  <input class="form-control" type="text" value="<?php  echo $prizes['p2_num'];?>" name="p2_num">
                  <span class="input-group-addon">份</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">奖品图片</label>
              <div class="col-sm-4">
                <?php  echo tpl_form_field_image('p2_thumb',$prizes['p2_thumb'],'', $options);?>
                <div class="help-block">图文消息的缩略图</div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="messages">

            <h5>奖品三参数设置</h5>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">选择奖品</label>
              <div class="col-sm-9" style="padding-top: 7px;">
                <?php  $i = 1;?>
                <?php  if(is_array($creditnames['creditnames'])) { foreach($creditnames['creditnames'] as $scredit => $creditname) { ?>
                <?php  $t = str_replace("$('#p3_".$i."').hide()","$('#p3_".$i."').show()","$('#p3_1').hide();$('#p3_2').hide();$('#p3_3').hide();$('#p3_4').hide();$('#p3_5').hide();");?>
                <label>
                  <input type="radio" value="<?php  echo $scredit;?>" name="p3_type" onclick="$('#p3_zkq').hide();$('#p3_djq').hide();$('#p3_zswp').hide();<?php  echo $t;?>" <?php  if($prizes['p3_type'] == $scredit ) { ?>checked<?php  } ?>><?php  echo $creditname['title'];?></label>
                <?php  $i++;?>
                <?php  } } ?>
                <label>
                  <input type="radio" value="2" name="p3_type" onclick="$('#p3_jf').hide();$('#p3_zkq').show();$('#p3_djq').hide();$('#p3_zswp').hide();$('#p3_1').hide();$('#p3_2').hide();$('#p3_3').hide();$('#p3_4').hide();$('#p3_5').hide();" <?php  if($prizes['p3_type'] == 2 ) { ?>checked<?php  } ?>>折扣券</label>
                <label>
                  <input type="radio" value="3" name="p3_type" onclick="$('#p3_jf').hide();$('#p3_zkq').hide();$('#p3_djq').show();$('#p3_zswp').hide();$('#p3_1').hide();$('#p3_2').hide();$('#p3_3').hide();$('#p3_4').hide();$('#p3_5').hide();" <?php  if($prizes['p3_type'] == 3 ) { ?>checked<?php  } ?>>代金券</label>
                <label>
                  <input type="radio" value="4" name="p3_type" onclick="$('#p3_jf').hide();$('#p3_zkq').hide();$('#p3_djq').hide();$('#p3_zswp').show();$('#p3_1').hide();$('#p3_2').hide();$('#p3_3').hide();$('#p3_4').hide();$('#p3_5').hide();" <?php  if($prizes['p3_type'] == 4 ) { ?>checked<?php  } ?>>真实物品</label>
              </div>
            </div>
            <?php  $j = 1;?>
            <?php  if(is_array($creditnames['creditnames'])) { foreach($creditnames['creditnames'] as $scredit => $creditname) { ?>
            <div class="form-group" id="p3_<?php  echo $j;?>" <?php  if($prizes['p3_type'] != $scredit) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">赠送<?php  echo $creditname['title'];?></label>
              <div class="col-sm-4">
                <div class="input-group">
                  <?php  $v = p3_.$scredit;?>
                  <input class="form-control" type="text" value="<?php  echo $prizes['p3_score'];?>" id="<?php  echo $v;?>" name="<?php  echo $v;?>">
                  <span class="input-group-addon">个</span>
                </div>
              </div>
            </div>
            <?php  $j++;?>
            <?php  } } ?>
            <div class="form-group" id="p3_zkq" <?php  if($prizes['p3_type'] != 2 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">折扣券</label>
              <div class="col-sm-4">
                <select name="p3_2" class="form-control">
                  <?php  if(is_array($couponlists)) { foreach($couponlists as $couponlist) { ?>
                <?php  $num = $couponlist['amount'] - $couponlist['dosage'];?>
                  <option value="<?php  echo $couponlist['couponid'];?>" <?php  if($prizes['p3_score'] == $couponlist['couponid']) { ?>selected<?php  } ?>><?php  echo $couponlist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=coupon&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group" id="p3_djq" <?php  if($prizes['p3_type'] != 3 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">代金券</label>
              <div class="col-sm-4">
                <select name="p3_3" class="form-control">
                  <?php  if(is_array($tokenlists)) { foreach($tokenlists as $tokenlist) { ?>
                <?php  $num = $tokenlist['amount'] - $tokenlist['dosage'];?>
                  <option value="<?php  echo $tokenlist['couponid'];?>" <?php  if($prizes['p3_score'] == $tokenlist['couponid']) { ?>selected<?php  } ?>><?php  echo $tokenlist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=token&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group" id="p3_zswp" <?php  if($prizes['p3_type'] != 4 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">真实物品</label>
              <div class="col-sm-4">
                <select name="p3_4" class="form-control">
                  <?php  if(is_array($goodslists)) { foreach($goodslists as $goodslist) { ?>
                <?php  $num = $goodslist['total'] - $goodslist['num'];?>
                  <option value="<?php  echo $goodslist['id'];?>" <?php  if($prizes['p3_score'] == $goodslist['id']) { ?>selected<?php  } ?>><?php  echo $goodslist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=goods&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">奖品数量</label>
              <div class="col-sm-4">
                <div class="input-group">
                  <input class="form-control" type="text" value="<?php  echo $prizes['p3_num'];?>" name="p3_num">
                  <span class="input-group-addon">份</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">奖品图片</label>
              <div class="col-sm-4">
                <?php  echo tpl_form_field_image('p3_thumb',$prizes['p3_thumb'],'', $options);?>
                <div class="help-block">图文消息的缩略图</div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="settings">

            <h5>奖品四参数设置</h5>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">选择奖品</label>
              <div class="col-sm-9" style="padding-top: 7px;">
                <?php  $i = 1;?>
                <?php  if(is_array($creditnames['creditnames'])) { foreach($creditnames['creditnames'] as $scredit => $creditname) { ?>
                <?php  $t = str_replace("$('#p4_".$i."').hide()","$('#p4_".$i."').show()","$('#p4_1').hide();$('#p4_2').hide();$('#p4_3').hide();$('#p4_4').hide();$('#p4_5').hide();");?>
                <label>
                  <input type="radio" value="<?php  echo $scredit;?>" name="p4_type" onclick="$('#p4_zkq').hide();$('#p4_djq').hide();$('#p4_zswp').hide();<?php  echo $t;?>" <?php  if($prizes['p4_type'] == $scredit ) { ?>checked<?php  } ?>><?php  echo $creditname['title'];?></label>
                <?php  $i++;?>
                <?php  } } ?>
                <label>
                  <input type="radio" value="2" name="p4_type" onclick="$('#p4_jf').hide();$('#p4_zkq').show();$('#p4_djq').hide();$('#p4_zswp').hide();$('#p4_1').hide();$('#p4_2').hide();$('#p4_3').hide();$('#p4_4').hide();$('#p4_5').hide();" <?php  if($prizes['p4_type'] == 2 ) { ?>checked<?php  } ?>>折扣券</label>
                <label>
                  <input type="radio" value="3" name="p4_type" onclick="$('#p4_jf').hide();$('#p4_zkq').hide();$('#p4_djq').show();$('#p4_zswp').hide();$('#p4_1').hide();$('#p4_2').hide();$('#p4_3').hide();$('#p4_4').hide();$('#p4_5').hide();" <?php  if($prizes['p4_type'] == 3 ) { ?>checked<?php  } ?>>代金券</label>
                <label>
                  <input type="radio" value="4" name="p4_type" onclick="$('#p4_jf').hide();$('#p4_zkq').hide();$('#p4_djq').hide();$('#p4_zswp').show();$('#p4_1').hide();$('#p4_2').hide();$('#p4_3').hide();$('#p4_4').hide();$('#p4_5').hide();" <?php  if($prizes['p4_type'] == 4 ) { ?>checked<?php  } ?>>真实物品</label>
              </div>
            </div>
            <?php  $j = 1;?>
            <?php  if(is_array($creditnames['creditnames'])) { foreach($creditnames['creditnames'] as $scredit => $creditname) { ?>
            <div class="form-group" id="p4_<?php  echo $j;?>" <?php  if($prizes['p4_type'] != $scredit) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">赠送<?php  echo $creditname['title'];?></label>
              <div class="col-sm-4">
                <div class="input-group">
                  <?php  $v = p4_.$scredit;?>
                  <input class="form-control" type="text" value="<?php  echo $prizes['p4_score'];?>" id="<?php  echo $v;?>" name="<?php  echo $v;?>">
                  <span class="input-group-addon">个</span>
                </div>
              </div>
            </div>
            <?php  $j++;?>
            <?php  } } ?>
            <div class="form-group" id="p4_zkq" <?php  if($prizes['p4_type'] != 2 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">折扣券</label>
              <div class="col-sm-4">
                <select name="p4_2" class="form-control">
                  <?php  if(is_array($couponlists)) { foreach($couponlists as $couponlist) { ?>
                <?php  $num = $couponlist['amount'] - $couponlist['dosage'];?>
                  <option value="<?php  echo $couponlist['couponid'];?>" <?php  if($prizes['p4_score'] == $couponlist['couponid']) { ?>selected<?php  } ?>><?php  echo $couponlist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=coupon&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group" id="p4_djq" <?php  if($prizes['p4_type'] != 3 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">代金券</label>
              <div class="col-sm-4">
                <select name="p4_3" class="form-control">
                  <?php  if(is_array($tokenlists)) { foreach($tokenlists as $tokenlist) { ?>
                <?php  $num = $tokenlist['amount'] - $tokenlist['dosage'];?>
                  <option value="<?php  echo $tokenlist['couponid'];?>" <?php  if($prizes['p4_score'] == $tokenlist['couponid']) { ?>selected<?php  } ?>><?php  echo $tokenlist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=token&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group" id="p4_zswp" <?php  if($prizes['p4_type'] != 4 ) { ?>style="display: none;"<?php  } ?>>
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">真实物品</label>
              <div class="col-sm-4">
                <select name="p4_4" class="form-control">
                  <?php  if(is_array($goodslists)) { foreach($goodslists as $goodslist) { ?>
                <?php  $num = $goodslist['total'] - $goodslist['num'];?>
                  <option value="<?php  echo $goodslist['id'];?>" <?php  if($prizes['p4_score'] == $goodslist['id']) { ?>selected<?php  } ?>><?php  echo $goodslist['title'];?>(<?php  echo $num;?>张可用)</option>
                  <?php  } } ?>
                </select>
              </div>
              <div style="padding-top: 7px;">
                <a href="./index.php?c=activity&a=goods&do=post" target="_blank" ><span class="glyphicon glyphicon-plus"></span> 添加</a>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">奖品数量</label>
              <div class="col-sm-4">
                <div class="input-group">
                  <input class="form-control" type="text" value="<?php  echo $prizes['p4_num'];?>" name="p4_num">
                  <span class="input-group-addon">份</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-xs-12 col-sm-3 col-md-1 control-label">奖品图片</label>
              <div class="col-sm-4">
                <?php  echo tpl_form_field_image('p4_thumb',$prizes['p4_thumb'],'', $options);?>
                <div class="help-block">图文消息的缩略图</div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingThree">
      <h5 class="panel-title">
        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">分享信息</a>
      </h5>
    </div>
    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
      <div class="panel-body">
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
          <div class="col-sm-9">
            <input class="form-control" type="text" value="<?php  echo $reply['share_title'];?>" class="span2" name="share_title">
            <div class="help-block">分享给好友或朋友圈时的标题</div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图片</label>
          <div class="col-sm-9">
            <?php  echo tpl_form_field_image('share_img',$reply['share_img'],'', $options);?>
            <div class="help-block">分享给好友或朋友圈时的图片</div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
          <div class="col-sm-9">
            <textarea class="form-control" name="share_content"><?php  echo $reply['share_content'];?></textarea>
            <div class="help-block">分享给好友或朋友圈时的描述</div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-xs-12 col-sm-3 col-md-2 control-label">引导关注链接</label>
          <div class="col-sm-9">
            <input class="form-control" type="text" value="<?php  echo $reply['share_url'];?>" class="span2" name="share_url">
            <div class="help-block">
              用户未关注时访问跳转链接! 推荐用微信平台的素材库，转成短地址
              <a target="_blank" href="http://www.dwz.cn/">短网址转换</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  window.initReplyController = function($scope) {
    
  };
  window.validateReplyForm = function(form, $, _, util, $scope) {
    
    return true;
  };
</script>