<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<script type="text/javascript">
	require(['jquery'], function($){
		$(':radio[name="msg_history"]').click(function(){
			if($(':radio[name=msg_history]:checked').val() == '1') {
				$('.msgd').show();
			} else {
				$('.msgd').hide();
			}
		});
		<?php  if($settings['msg_history'] == '0') { ?> 
			$(".msgd").hide();
		<?php  } ?>
	});
</script>
	<div class="clearfix">
		<form action="" method="post" class="form-horizontal form">
			<div class="panel panel-default">
				<div class="panel-heading">
					统计分析 <span>设定公众号码统计分析的相关功能，这个设置是针对当前公众号的</span>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">开启历史消息记录</label>
						<div class="col-sm-9 col-xs-12">
							<label for="msg_history_1" class="radio-inline"><input type="radio" name="msg_history" id="msg_history_1" value="1" <?php  if($settings['msg_history'] == '1') { ?>checked="checked"<?php  } ?>> 是</label>
							<label for="msg_history_0" class="radio-inline"><input type="radio" name="msg_history" id="msg_history_0" value="0"  <?php  if($settings['msg_history'] == '0') { ?>checked="checked"<?php  } ?>> 否</label>
							<div class="help-block">开启此项后，系统将记录用户与系统的往来消息记录。</div>
						</div>
					</div>
					<div class="form-group msgd">
						<label class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">历史消息记录天数</label>
						<div class="col-sm-9 col-xs-12">
							<input type="text" name="msg_maxday" class="form-control" value="<?php  echo $settings['msg_maxday'];?>" />
							<div class="help-block">设置保留历史消息记录的天数，为0则为保留全部。</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-3 col-lg-2 control-label">开启利用率统计</label>
						<div class="col-sm-9 col-xs-12">
							<label for="rule_use_1" class="radio-inline"><input type="radio" id="rule_use_1" name="use_ratio" value="1" <?php  if($settings['use_ratio'] == '1') { ?>checked="checked"<?php  } ?>> 是</label>
							<label for="rule_use_0" class="radio-inline"><input type="radio" id="rule_use_0" name="use_ratio" value="0"  <?php  if($settings['use_ratio'] == '0') { ?>checked="checked"<?php  } ?>> 否</label>
							<div class="help-block">开启此项后，系统将记录系统中的规则的使用情况，并生成走势图。</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-12">
						<input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1" />
						<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				</div>
			</div>
		</form>
	</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
