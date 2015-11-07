<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php  load()->func('tpl')?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<div class="main">
    <ul class="nav nav-tabs">
        <li><a href="<?php  echo $this->createWebUrl('technician',array('op'=>'list'));?>">技师管理</a></li>
        <?php  if($op=='add') { ?>
        <li class="active"><a href="<?php  echo $this->createWebUrl('TechnicianEdit',array('op'=>'add'));?>">添加技师</a></li>
        <?php  } else { ?>
        <li class="active"><a href="<?php  echo $this->createWebUrl('TechnicianEdit',array('op'=>'edit','id'=>$id));?>">编辑技师</a></li>
        <?php  } ?>
    </ul>
    <form action="<?php  echo $url;?>" class="form-horizontal form" method="post" enctype="multipart/form-data" onsubmit="return formcheck()">
        <input type="hidden" name="id" value="<?php  echo $item['id'];?>">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">名字（编号）</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="name" value="<?php  echo $item['name'];?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">性别</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline">
                            <input type="radio" name="gender" value="0" <?php  if($item['gender'] == 0) { ?>checked<?php  } ?>/>男
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="gender" value="1" <?php  if($item['gender'] == 1) { ?>checked<?php  } ?>/>女
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">照片</label>
                    <div class="col-sm-9 col-xs-12">
                        <?php  echo tpl_form_field_image('photo',$item['photo'])?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">等级</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="lever" id="title" value="<?php  echo $item['lever'];?>" class="form-control">
                        <span class="help-block">数字越大，等级越高</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">详情</label>
                    <div class="col-sm-9 col-xs-12">
                            <textarea name="detail"  style="height:100px;width:100%;" class="form-control" cols="70"><?php  echo $item['detail'];?></textarea>
                            <span class="help-block">套餐详情（选填）</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio-inline">
                            <input type="radio" name="state" value="0" <?php  if($item['state'] == 0) { ?>checked<?php  } ?>/>显示
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="state" value="1" <?php  if($item['state'] == 1) { ?>checked<?php  } ?>/>隐藏
                        </label>
                        <span class='help-block'>手机前台是否显示</span>
                    </div>
                </div>
            </div>
        </div>
    <div class="form-group col-sm-12">
        <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
        <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
    </div>

    </form>
</div>
<script type="text/javascript">
    // kindeditor($('.richtext-clone'));






</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
