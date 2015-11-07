<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('mc/header', TEMPLATE_INCLUDEPATH)) : (include template('mc/header', TEMPLATE_INCLUDEPATH));?>
<style>
	.mnav-box ul,.mnav-box ul li{padding:0px; margin:0px;}
	.mnav-box{color:#606366; background:transparent url('resource/images/home-bg.jpg') no-repeat; background-size:100% 100%;}
	.mnav-box ul{border-top:10px solid #e4e9e8; list-style:none; background:transparent -webkit-gradient(linear,0 0, 0 10%,from(rgba(90,197,212,1)), to(rgba(90,197,212,1))) center top; -webkit-background-size:100% 2px; padding-top:2px; background-repeat:no-repeat;}
	.mnav-box ul:first-child{background:none; border-top:0; padding-top:0;}
	.mnav-box ul li{ border-bottom:1px solid #dddddd; position:relative; height:48px; padding: 12px 15px; overflow:hidden;}
	.mnav-box ul li .mnav-box-list{color:#606366; font-size:15px; text-decoration:none; -webkit-box-sizing:border-box; overflow:hidden;}
	.mnav-box ul li .mnav-box-list>i{width:25px; margin-right:10px; color:#8dd1db; text-align:center; font-size:20px;}
	.mnav-box ul li .mnav-box-list .mnav-title{display:inline-block; padding-right:15px;}
	.mnav-box ul li .mnav-box-list > .pull-right{display:inline-block; font-size:22px; line-height:0; color:#888; position:absolute; right:15px; top:12px;}
	.mnav-box ul li .mnav-box-list > .pull-right .btn{background:#56c6d6; color:#FFF; border:0; border-radius:1px; margin-top:-5px; width:100px; height:32px; font-size:17px; white-space:pre-line;}
	.alert-bg{background:#000; width:100%; position:absolute; top:0; z-index:100; opacity:0.5;}
</style>

<!--  默认显示 -->
<?php  if($do == 'display') { ?>
	<div class="mnav-box">
		<ul>
			<li>
				<a class="mnav-box-list" href="<?php  echo url('entry', array('m' => 'recharge', 'do' => 'pay'));?>">
					<i class="fa fa-money"></i>
					<span>余额充值</span>
					<span class="pull-right"><i class="fa fa-angle-right"></i></span>
				</a>
			</li>
			<?php  if($_W['card_permission']) { ?>
				<li>
					<a class="mnav-box-list" href="<?php  echo url('wechat/card');?>">
						<i class="fa fa-tags"></i>
						<span>微信卡券</span>
						<span class="pull-right"><i class="fa fa-angle-right"></i></span>
					</a>
				</li>
			<?php  } ?>
			<li>
				<a class="mnav-box-list" href="<?php  echo url('activity/coupon/mine');?>">
					<i class="fa fa-money"></i>
					<span>我的折扣券</span>
					<span class="pull-right"><i class="fa fa-angle-right"></i></span>
				</a>
			</li>
			<li>
				<a class="mnav-box-list" href="<?php  echo url('activity/token/mine');?>">
					<i class="fa fa-money"></i>
					<span>我的代金券</span>
					<span class="pull-right"><i class="fa fa-angle-right"></i></span>
				</a>
			</li>
			<li>
				<a class="mnav-box-list" href="<?php  echo url('activity/goods/mine');?>">
					<i class="fa fa-money"></i>
					<span>我的真实物品</span>
					<span class="pull-right"><i class="fa fa-angle-right"></i></span>
				</a>
			</li>
			<!--<li>-->
				<!--<a class="mnav-box-list" href="<?php  echo url('activity/partimes/mine');?>">-->
					<!--<i class="fa fa-money"></i>-->
					<!--<span>我的活动参与次数</span>-->
					<!--<span class="pull-right"><i class="fa fa-angle-right"></i></span>-->
				<!--</a>-->
			<!--</li>-->
		</ul>
	<?php  if(is_array($groups)) { foreach($groups as $name => $navs) { ?>
		<ul>
			<?php  if(is_array($navs)) { foreach($navs as $nav) { ?>
			<li>
				<a class="mnav-box-list" href="<?php  echo $nav['url'];?>">
					<i class="fa fa-fw <?php  if(empty($nav['css']['icon']['icon'])) { ?> fa-money<?php  } else { ?> <?php  echo $nav['css']['icon']['icon'];?><?php  } ?>"></i>
					<span class="mnav-title"><?php  echo $nav['name'];?></span>
					<span class="pull-right"><i class="fa fa-angle-right"></i></span>
				</a>
			</li>
			<?php  } } ?>
		</ul>
	<?php  } } ?>
	<?php  if(!empty($others)) { ?>
		<ul>
			<?php  if(is_array($others)) { foreach($others as $nav) { ?>
			<li>
				<a class="mnav-box-list" href="<?php  echo $nav['url'];?>">
					<i class="fa fa-fw <?php  if(empty($nav['css']['icon']['icon'])) { ?> fa-money<?php  } else { ?> <?php  echo $nav['css']['icon']['icon'];?><?php  } ?>"></i>
					<?php  echo $nav['name'];?>
					<span class="pull-right"><i class="fa fa-angle-right"></i></span>
				</a>
			</li>
			<?php  } } ?>
			<?php  if(isset($setting['uc']['status']) && $setting['uc']['status'] == '1') { ?>
			<?php  if(!empty($ucUser)) { ?>
			<li>
				<a class="mnav-box-list" href="<?php  echo url('mc/uc', array('foo' => 'unbind'))?>">
					<i class="fa fa-fw fa-user"></i>
					已绑定<?php  echo $setting['uc']['title'];?>账号(<?php  echo $ucUser['username'];?>), 点击解除绑定
				</a>
			</li>
			<?php  } else { ?>
			<li>
				<a class="mnav-box-list" href="<?php  echo url('mc/uc', array('foo' => 'bind'))?>">
					<i class="fa fa-fw fa-user"></i>
					绑定<?php  echo $setting['uc']['title'];?>账号
				</a>
			</li>
			<?php  } ?>
			<?php  } ?>
		</ul>
	<?php  } ?>
		<ul>
			<li>
				<a class="mnav-box-list" href="<?php  echo url('mc/bond/mobile')?>">
					<i class="fa fa-mobile-phone"></i>
					<span class=""><?php  if(!empty($profile['mobile'])) { ?>修改<?php  } else { ?>绑定<?php  } ?>手机号</span>
					<span class="pull-right"><span class="btn btn-default"><?php  if(!empty($profile['mobile'])) { ?>修改<?php  } else { ?>绑定<?php  } ?></span></span>
				</a>
			</li>
			<?php  if($reregister) { ?>
				<li class="alert-li">
					<a class="mnav-box-list" href="<?php  echo url('mc/bond/email')?>">
						<i class="fa fa-unlock-alt"></i>
						<span class="">重置重要资料</span>
						<span class="pull-right"><span class="btn btn-default">重置</span></span>
					</a>
				</li>
			<?php  } ?>
			<li>
				<a class="mnav-box-list" href="<?php  echo url('mc/bond/address');?>">
					<i class="fa fa-map-marker"></i>
					<span class="">收货地址</span>
					<span class="pull-right"><span class="btn btn-default">修改</span></span>
				</a>
			</li>
			<li>
				<a class="mnav-box-list" href="<?php  echo url('mc/home/login_out');?>">
					<i class="fa fa-sign-out"></i>
					<span class="">退出系统</span>
					<span class="pull-right"><i class="fa fa-angle-right"></i></span>
				</a>
			</li>
		</ul>
</div>
<?php  } ?>

<script>
	<?php  if($reregister) { ?>
		function alert_close(a) {
			a.parents('.alert').remove();
			$('.alert-bg').remove();
			$('.alert-li').removeClass("alert-li");
			require(['util'], function(util) {
				util.cookie.set('we7emailtips', 1, 3600);
			});
		}
		$(function() {
			require(['util'], function(util) {
				var alert_html;
				var alert_url = $('.alert-li').find('a').attr('href');
				alert_html = '<div class="alert alert-warning" role="alert" style="position:absolute; z-index:101; margin:-150px 30px 0 30px;">';
				alert_html += '<span>尊敬的微信用户，为了您的账号安全，请及时完善重要资料，以防账号被非法份子利用！</span>';
				alert_html += '<div style="margin-top:10px;"><button type="button" class="btn btn-warning pull-left" onclick="alert_close($(this));">稍后提醒我</button><a class="btn btn-info pull-right" href="'+alert_url+'">现在就完善</a></div>';
				alert_html += '</div>';
				if(!util.cookie.get('we7emailtips') && $('.alert-li').html().length > 0) {
					$('body').append('<div class="alert-bg" style="height:'+(parseInt($('body').height())+63)+'px;"></div>');
					$('.alert-li').parent().prepend(alert_html);
				}
			});
		});
	<?php  } ?>
</script>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('mc/footer', TEMPLATE_INCLUDEPATH)) : (include template('mc/footer', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>