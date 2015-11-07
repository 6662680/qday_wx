<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style>
	body{background:#d2e6e9; padding-bottom:63px; font-family:Helvetica, Arial, sans-serif;}
	a{color:#666666;}a:hover{color:#3ebacc;}
	.profile-box{padding:10px 0; background:transparent url('resource/images/home-bg.jpg') no-repeat; background-size:100% 100%;}
	.form-header{clear:both;height:20px;line-height:15px;margin-left:15px; margin-top:20px;border-left:5px solid #000000;padding:5px;font-weight:bold;postion:relative;}
	.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus{color: #3ebacc;}
	.btn.btn-primary{background: #56c6d6; color: #FFF; border: 0;}
	@media screen and (max-width: 767px) {.tpl-calendar div,.tpl-district-container div{margin-bottom:10px;} .empty{display:none;}}
	.btn-group-top-box{padding:10px 0; border-bottom:1px solid #a5d7de;}
	.btn-group-top{margin:0 auto; overflow:hidden; width:200px; display:block;}
	.btn-group-top .btn{width:100px; -webkit-box-shadow:none; box-shadow:none; border-color:#5ac5d4; color:#5ac5d4; background:#d1e5e9;}
	.btn-group-top .btn:hover{color:#FFF; background:#addbe1;}
	.btn-group-top .btn.active{color:#FFF; background:#5ac5d4;}
</style>
<script>
	require(['bootstrap'], function($){
		$(function(){
			$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				if($(e.target).attr('href') == '#tab2') {
					$('#tab2').addClass('fadeInRight');
					$('.more').addClass('active');
					$('.basic').removeClass('active');
				} else {
					$('#tab1').addClass('fadeInLeft');
					$('.more').removeClass('active');
					$('.basic').addClass('active');
				}
			});
		});
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/toolbar', TEMPLATE_INCLUDEPATH)) : (include template('common/toolbar', TEMPLATE_INCLUDEPATH));?>
<div class="profile">
	<div class="tabbable">
		<div class="btn-group-top-box">
			<div class="btn-group btn-group-top">
				<a href="#tab1" data-toggle="tab" class="btn btn-default basic active">基本资料</a>
				<a href="#tab2" data-toggle="tab" class="btn btn-default more">更多资料</a>
			</div>
		</div>
		<div class="profile-box">
		<form class="tab-content clearfix" action="<?php  echo url('mc/profile');?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="from_user" value="<?php  echo $_W['fans']['from_user'];?>" />
		<div class="tab-pane active animated" id="tab1">
			<?php  if(!empty($mcFields['avatar'])) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['avator']['title'];?></label>
				<div class="col-sm-12 col-xs-12">
					<?php  echo tpl_fans_form('avatar', $profile['avatar']);?>
				</div>
			</div>
			<?php  } ?>
			<?php  if(!empty($mcFields['nickname'])) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['nickname']['title'];?></label>
				<div class="col-sm-12 col-xs-12">
					<?php  echo tpl_fans_form('nickname',$profile['nickname'] );?>
				</div>
			</div>
			<?php  } ?>
			<?php  if(!empty($mcFields['realname'])) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['realname']['title'];?></label>
				<div class="col-sm-12 col-xs-12">
					<?php  echo tpl_fans_form('realname',$profile['realname'] );?>
				</div>
			</div>
			<?php  } ?>
			<?php  if(!empty($mcFields['gender'])) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['gender']['title'];?></label>
				<div class="col-sm-12 col-xs-12">
					<?php  echo tpl_fans_form('gender',$profile['gender']);?>
				</div>
			</div>
			<?php  } ?>
			<?php  if(!empty($mcFields['birthyear'])) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['birthyear']['title'];?></label>
				<div class="col-sm-12 col-xs-12">
					<?php  echo tpl_fans_form('birth',array('year' => $profile['birthyear'],'month' => $profile['birthmonth'],'day' => $profile['birthday']));?>
				</div>
			</div>
			<?php  } ?>
			<?php  if(!empty($mcFields['resideprovince'])) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['resideprovince']['title'];?></label>
				<div class="col-sm-12 col-xs-12">
					<?php  echo tpl_fans_form('reside',array('province' => $profile['resideprovince'],'city' => $profile['residecity'],'district' => $profile['residedist']));?>
				</div>
			</div>
			<?php  } ?>
			<?php  if(!empty($mcFields['address'])) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['address']['title'];?></label>
				<div class="col-sm-12 col-xs-12">
					<?php  echo tpl_fans_form('address',$profile['address']);?>
				</div>
			</div>
			<?php  } ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['mobile']['title'];?></label>
				<div class="col-sm-12 col-xs-12">
					<?php  if($profile['mobile']) { ?>
					<input type="text" disabled value="<?php  echo $profile['mobile'];?>" class="form-control">
					<?php  } else { ?>
					<?php  echo tpl_fans_form('mobile',$profile['mobile']);?>
					<?php  } ?>
				</div>
			</div>
			<?php  if(!empty($mcFields['qq'])) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['qq']['title'];?></label>
				<div class="col-sm-12 col-xs-12">
					<?php  echo tpl_fans_form('qq',$profile['qq']);?>
				</div>
			</div>
			<?php  } ?>
			<?php  if(!empty($mcFields['email'])) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['email']['title'];?></label>
				<div class="col-sm-12 col-xs-12">
					<?php  if($profile['email']) { ?>
					<input type="text" disabled value="<?php  echo $profile['email'];?>" class="form-control">
					<?php  } else { ?>
					<?php  echo tpl_fans_form('email', $profile['email']);?>
					<?php  } ?>
				</div>
			</div>
			<?php  } ?>
		</div>

		<div class="tab-pane animated" id="tab2">
		<?php  if(!empty($mcFields['telephone'])) { ?>
		<h5 class="form-header">联系方式</h5>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['telephone']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('telephone',$profile['telephone']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['msn'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['msn']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('msn',$profile['msn']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['taobao'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['taobao']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('taobao',$profile['taobao']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['alipay'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['alipay']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('alipay',$profile['alipay']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['studentid'])) { ?>
		<h5 class="form-header empty" style="border:none;"></h5>
		<h5 class="form-header">教育情况</h5>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['studentid']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('studentid',$profile['studentid']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['grade'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['grade']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('grade',$profile['grade']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['graduateschool'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['graduateschool']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('graduateschool',$profile['graduateschool']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['education'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['education']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('education',$profile['education']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['company'])) { ?>
		<h5 class="form-header empty" style="border:none;"></h5>
		<h5 class="form-header">工作情况</h5>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['company']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('company',$profile['company']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['occupation'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['occupation']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('occupation',$profile['occupation']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['position'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['position']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('position',$profile['position']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['revenue'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['revenue']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('revenue',$profile['revenue']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['constellation'])) { ?>
		<h5 class="form-header empty" style="border:none;"></h5>
		<h5 class="form-header">个人情况</h5>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['constellation']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('constellation',$profile['constellation']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['zodiac'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['zodiac']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('zodiac',$profile['zodiac']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['nationality'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['nationality']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('nationality',$profile['nationality']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['height'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['height']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('height',$profile['height']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['weight'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['weight']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('weight',$profile['weight']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['bloodtype'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['bloodtype']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('bloodtype',$profile['bloodtype']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['idcard'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['idcard']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('idcard',$profile['idcard']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['zipcode'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['zipcode']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('zipcode',$profile['zipcode']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['site'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['site']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('site',$profile['site']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['affectivestatus'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['affectivestatus']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('affectivestatus',$profile['affectivestatus']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['lookingfor'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['lookingfor']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('lookingfor',$profile['lookingfor']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['bio'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['bio']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('bio',$profile['bio']);?>
			</div>
		</div>
		<?php  } ?>
		<?php  if(!empty($mcFields['interest'])) { ?>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"><?php  echo $mcFields['interest']['title'];?></label>
			<div class="col-sm-12 col-xs-12">
				<?php  echo tpl_fans_form('interest',$profile['interest']);?>
			</div>
		</div>
		<?php  } ?>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-12 control-label"></label>
			<div class="col-sm-12 col-xs-12" style="text-align:center">
				<button type="submit" class="btn btn-primary btn-block" name="submit" value="提交">提交</button>
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
		</form>
		</div>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('mc/footer', TEMPLATE_INCLUDEPATH)) : (include template('mc/footer', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
