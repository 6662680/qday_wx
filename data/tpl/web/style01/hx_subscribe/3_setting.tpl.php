<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
		<div class="panel panel-default">
			<div class="panel-heading">
				参数设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">奖励类型</label>
					<div class="col-sm-9 col-xs-12">
						<?php  if(is_array($creditnames['creditnames'])) { foreach($creditnames['creditnames'] as $scredit => $creditname) { ?>
            			<label style="margin-top:5px;"><input type="radio" value="<?php  echo $scredit;?>" name="credit_type" <?php  if($settings['credit_type'] == $scredit ) { ?>checked<?php  } ?>><?php  echo $creditname['title'];?></label>
            			<?php  } } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">自关注获得</label>
					<div class="col-sm-6 col-xs-12">
            			<input type="text" name="credit_subscribe" id="credit_subscribe" class="form-control" value="<?php  echo $settings['credit_subscribe'];?>" />
        				<p class="help-block">用户首次关注时获得的奖励</p>
    				</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">一级邀请好友</label>
					<div class="col-sm-6 col-xs-12">
            			<input type="text" name="credit_lever_1" id="credit_lever_1" class="form-control" value="<?php  echo $settings['credit_lever_1'];?>" />
        				<p class="help-block">用户通过你的分享链接关注平台</p>
    				</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">二级邀请好友</label>
					<div class="col-sm-6 col-xs-12">
            			<input type="text" name="credit_lever_2" id="credit_lever_2" class="form-control" value="<?php  echo $settings['credit_lever_2'];?>" />
        				<p class="help-block">用户通过你的下一级用户分享链接关注平台</p>
    				</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">红包兑换限额</label>
					<div class="col-sm-6 col-xs-12">
            			<input type="text" name="out_limit" id="out_limit" class="form-control" value="<?php  echo $settings['out_limit'];?>" />
        				<p class="help-block">用户通过你的下一级用户分享链接关注平台</p>
    				</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">初始参加人数</label>
					<div class="col-sm-6 col-xs-12">
            			<input type="text" name="start_num" id="start_num" class="form-control" value="<?php  echo $settings['start_num'];?>" />
        				<p class="help-block">用户通过你的下一级用户分享链接关注平台</p>
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
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>