<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta content="Croaker" name="author">
<meta name="Description" content="<?php  echo $reply['description'];?>" />
<meta content="application/xhtml+xml;charset=UTF-8" http-equiv="Content-Type">
<meta content="no-cache,must-revalidate" http-equiv="Cache-Control">
<meta content="no-cache" http-equiv="pragma">
<meta content="0" http-equiv="expires">
<meta content="telephone=no, address=no" name="format-detection">
<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
<link href="../addons/stonefish_scratch/template/css/square.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../addons/stonefish_scratch/template/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="../addons/stonefish_scratch/template/js/WeixinApi.js"></script>	
<script type="text/javascript" src="../addons/stonefish_scratch/template/js/wScratchPad.js"></script>
<title><?php  echo $reply['title'];?></title>
<script>
$(document).ready(function() {
	//if(parseInt($("#nums").html())>0){
		var baseurl = $("#baseurl").val();
		var isplayed = false;
		var datatip;
		$('.wipe').wScratchPad({
			bg: baseurl+'template/images/touming.png',
			fg: baseurl+'template/images/wipe.png',
			scratchDown: function (e, percent) {
				if(percent>0){
					if(!isplayed){
						if(datatip.success==1||datatip.success==0){
						    if(datatip.success==1){
							    $('#awardname').html(datatip.award['prizetype']+"-"+datatip.award['prizename']);
							    $("#panelimg").css({"background-image": "url("+datatip.award['prizepic']+")"});
							    $("#cccc").text(datatip.award['prizetype']+"-"+datatip.award['prizename']);
							}
							if(datatip.success==0){
							    $('#awardname').html(datatip.msg);
							}
							if($("#credit_now").length>0){
						        $("#credit_now").text(parseInt($("#credit_now").text())-<?php  echo $reply['credit_times'];?>);
						    }
						    if($("#count").length>0){
						    	$("#count").text(parseInt($("#count").text())+1);
						    }
						    if($("#totalcount").length>0){
							    $("#totalcount").text(parseInt($("#totalcount").text())+1)
						    }
						    if($("#number_times").length>0){
							    $("#number_times").text(parseInt($("#number_times").text())-1);
						    }
						    $("#panel-content").css({"height": ""+datatip.height+"px"});
						}						
					}
					isplayed = true;
				}
				if (percent > 50) {
					this.reset();
					if(datatip.success==1){
					    $("#panel_box").show();
						$("#wipestart").show();
					    $("#credit_now").text(datatip.credit_now);
						$("#result_info1").show();
						$("#result_info2").hide();
					}else{
					    $("#panelimg").css({"background-image": "url(../addons/stonefish_scratch/template/images/icon_prize_useless.png)"});
						$("#cccc").text(datatip.msg);					   
					    $("#panel_box").show();
						$("#wipestart").show();
					    $("#credit_now").text(datatip.credit_now);
						$("#result_info1").hide();
						$("#result_info2").show();
					}
					if(datatip.isfans){
					    $("#result_info").show();
					    $("#result_info1").hide();
						$("#result_info2").hide();
				    }else{
					    $("#result_info").hide();
				    }
				}
			}
		});
	//}
	$("#wipestart").click(function(){
	    if ($("#msg").val()!=''){
		    $("#panelimg").css({"background-image": "url(../addons/stonefish_scratch/template/images/icon_prize_useless.png)"});
			$("#cccc").text($("#msg").val());			
			$("#panel_box").show();
			$("#result_info1").hide();
			$("#result_info2").show();
		}else{
		    $("#wipestart").hide();
			$.ajax({
				type: "get",
				dataType:'json',
				url: '<?php  echo $this->createMobileUrl('get_award', array('rid' => $rid))?>',
				success: function(data) {						        
					datatip = data;
					if(datatip.success==2){
						$("#wipestart").show();
						$("#panelimg").css({"background-image": "url(../addons/stonefish_scratch/template/images/icon_prize_useless.png)"});
						$("#cccc").text(datatip.msg);
					    $("#panel_box").show();
						$("#result_info1").hide();						
						$("#result_info2").show();
						$("#msg").val(datatip.msg);
					}
				}
			});
		}
		
	});
	$("#shareimg").click(function(){
		$("#pop_share").fadeToggle();
	});
	$("#pop_share").click(function(){
		$("#pop_share").fadeToggle();
	});
	$("#panel-close").click(function(){
		$("#panel_box").hide();		
		isplayed = false;
		percent = 0;		
	});
	$("#savebtn").click(function(){
		$("#panel_box").hide();
		isplayed = false;
		percent = 0;
	});
	$("#closebtn").click(function(){
		$("#panel_box").hide();
		isplayed = false;
		percent = 0;
	});
	$("#share_close").click(function(){
		$("#share_box").hide();
	});
	$("#sharebtn").click(function(){
		$("#share_box").hide();
	});
});
</script>
</head>
<body onselectstart="return true;" ondragstart="return false;">
<input type="hidden" value="../addons/stonefish_scratch/" id="baseurl"/>
<input type="hidden" value="<?php  echo $msg;?>" id="msg"/>
<div style="max-width:500px;">
    <div class="nou">
	    <div style="max-width:100%">
		<?php  if(!empty($reply['adpic'])) { ?><?php  if(!empty($reply['adpicurl'])) { ?><a href="<?php  echo $reply['adpicurl'];?>"><?php  } ?><img id="top_img" style="max-width: 100%;height: auto;width: auto\9;"  src="<?php  echo toimage($reply['adpic'])?>" width="100%" border="0"><?php  if(!empty($reply['adpicurl'])) { ?></a><?php  } ?><?php  } ?>
        </div>       
        <div class="zhuan1" id="lottery">
		    <?php  if($reply['opportunity']==2) { ?>
			<div class="mingdan">
                <div class="biaoti">我的<?php  echo $creditnames;?> <span class="n" id="credit_now"><?php  echo intval($credit[$reply['credit_type']])?></span> 个  每次 <span class="n"><?php  echo $reply['credit_times'];?></span> <?php  echo $creditnames;?></div>
            </div>
			<?php  } ?>
			<?php  if($reply['opportunity']==1) { ?>
			<div class="mingdan">
                <div class="biaoti"> 赠送 <span class="n"><?php  echo $reply['number_times'];?></span> 次抽奖机会 剩余 <span class="n" id="number_times"><?php  echo $reply['number_times']-$fans['totalnum']?></span> 次</div>
            </div>
			<?php  } ?>
			<?php  if($reply['opportunity']==0) { ?>
			<div class="mingdaninfo">
				<div class="box">					
					<div class="Detail" style="color:#FFF;text-align:center;"><?php  echo $detail;?></div>
				</div>
            </div>
			<?php  } ?>
			<div class="con">
			<div class="guagua">
			<div class="guaguabg">
		        <p style="display:table;height:100px;width:100%;">
			        <span style="z-index:999;display:table-cell;vertical-align:middle;" id="awardname"></span>
		        </p>
		        <div class="wipe" id="wipe"></div>
				<div class="wipestart" id="wipestart"><img src="../addons/stonefish_scratch/template/images/wipestart.png"></div>
	        </div>
			</div>
			</div>
			<div class="mingdan">
                <div class="biaoti">已有 <span class="n"><?php  echo $reply['xuninum']+$reply['fansnum']?></span> 人参与此活动</div>
            </div>
			<?php  if(!empty($award_list)&&$reply['awardnum']) { ?>
			<div class="mingdan">
                <div class="biaoti"><marquee behavior="scroll" scrolldelay="200"><?php  echo $award_list;?></marquee></div>
            </div>
			<?php  } ?>
			<?php  if($share['sharenum']>0) { ?>
			<div class="boxwhite">
                <div id="shareimg" class="share"><img src="<?php  echo $share['share_picurl'];?>"></div>
            </div>			
			<?php  } ?>
			<div style="height:55px;">&nbsp;&nbsp;</div>
        </div>
    </div>	
	<div class="panel-box" id="panel_box">
        <div class="panel-content" id="panel-content">
            <div class="panel-close" id="panel-close"></div>
            <span class="icon-prize-useless" id="panelimg"></span><br/><div id="cccc"><?php  echo $reply['ticketinfo'];?></div>
			<div id="result_info"<?php  if(!$isfans) { ?> style="display:none"<?php  } ?>>
			<div id="isfans" style="display:none"><?php  echo $isfans;?></div>
			    <hr class="common-hr" />
                <?php  if($reply['isrealname']) { ?><label><?php  echo $isfansname['0'];?></label><input name="text" class="px" id="realname" value="<?php  echo $profile['realname'];?>" type="text" placeholder="请输入<?php  echo $isfansname['0'];?>"><?php  } ?>
				<?php  if($reply['ismobile']) { ?><label><?php  echo $isfansname['1'];?></label><input name="tel" class="px" id="mobile" value="<?php  echo $profile['mobile'];?>" type="text" placeholder="请输入<?php  echo $isfansname['1'];?>"><?php  } ?>
				<?php  if($reply['isqq']) { ?><label><?php  echo $isfansname['2'];?></label><input name="tel" class="px" id="qq" value="<?php  echo $profile['qq'];?>" type="text" placeholder="请输入<?php  echo $isfansname['2'];?>"><?php  } ?>
				<?php  if($reply['isemail']) { ?><label><?php  echo $isfansname['3'];?></label><input name="email" class="px" id="email" value="<?php  echo $profile['email'];?>" type="text" placeholder="请输入<?php  echo $isfansname['3'];?>"><?php  } ?>
				<?php  if($reply['isaddress']) { ?><label><?php  echo $isfansname['4'];?></label><input name="text" class="px" id="address" value="<?php  echo $profile['address'];?>" type="text" placeholder="请输入<?php  echo $isfansname['4'];?>"><?php  } ?>
				<?php  if($reply['isgender']) { ?><label><?php  echo $isfansname['5'];?></label><select name="gender" id="gender" class="form-control">
						<option value="0"<?php  if($profile['gender']==0) { ?> selected <?php  } ?>>选择<?php  echo $isfansname['5'];?></option>
						<option value="1"<?php  if($profile['gender']==1) { ?> selected <?php  } ?>>男</option>
						<option value="2"<?php  if($profile['gender']==2) { ?> selected <?php  } ?>>女</option>
					</select><?php  } ?>
				<?php  if($reply['istelephone']) { ?><label><?php  echo $isfansname['6'];?></label><input name="text" class="px" id="telephone" value="<?php  echo $profile['telephone'];?>" type="text" placeholder="请输入<?php  echo $isfansname['6'];?>"><?php  } ?>
				<?php  if($reply['isidcard']) { ?><label><?php  echo $isfansname['7'];?></label><input name="text" class="px" id="idcard" value="<?php  echo $profile['idcard'];?>" type="text" placeholder="请输入<?php  echo $isfansname['7'];?>"><?php  } ?>
				<?php  if($reply['iscompany']) { ?><label><?php  echo $isfansname['8'];?></label><input name="text" class="px" id="company" value="<?php  echo $profile['company'];?>" type="text" placeholder="请输入<?php  echo $isfansname['8'];?>"><?php  } ?>
				<?php  if($reply['isoccupation']) { ?><label><?php  echo $isfansname['9'];?></label><input name="text" class="px" id="occupation" value="<?php  echo $profile['occupation'];?>" type="text" placeholder="请输入<?php  echo $isfansname['9'];?>"><?php  } ?>
				<?php  if($reply['isposition']) { ?><label><?php  echo $isfansname['10'];?></label><input name="text" class="px" id="position" value="<?php  echo $profile['position'];?>" type="text" placeholder="请输入<?php  echo $isfansname['10'];?>"><?php  } ?>
                <div id="result_info_tip"></div>
				<div class="btn-layout">
                    <input type="reset" class="btn-reset" value="重填"/>
					<input class="btn-confirm" name="确定" id="save-btn" type="button" value="确定">
                </div>
            </div>
			<div id="result_info1" style="display:none">
			    <div style="margin-top:10px;"><input class="btn-confirm" name="放入百宝箱" id="savebtn" type="button" value="放入百宝箱"> </div>
			</div>
			<div id="result_info2" style="display:none">
			    <div style="margin-top:10px;"><input class="btn-confirm" name="关闭" id="closebtn" type="button" value="关闭"> </div>
			</div>
        </div>
    </div>
	<div class="panel-box" id="share_box">
        <div class="panel-content" id="panel-content">
            <div class="panel-close" id="share_close"></div>
            <div id="share_tip"></div>
			<hr class="common-hr" />			
			<div style="margin-top:10px;" id="share_miao"><input class="btn-confirm" name="关闭" id="sharebtn" type="button" value="关闭"> </div>
        </div>
    </div>
</div>
<?php  if($share['sharenum']>0) { ?><div id="pop_share"><img src="<?php  echo $share['share_pic'];?>" width="100%" alt="分享到朋友圈"/></div><?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>
<script type="text/javascript">
        $("#save-btn").bind("click",function () {
            var btn = $(this);
            <?php  if($reply['isrealname']) { ?>
			var realname = $("#realname").val();
            if (realname == '') {
				$("#result_info_tip").text("请输入<?php  echo $isfansname['0'];?>");
                return
            }
			var partten = /[\u4e00-\u9fa5]/g;
            if(!partten.test(realname)){
               $("#result_info_tip").text("请输入正确的<?php  echo $isfansname['0'];?>");
			   return;
            }
			<?php  } ?>
			<?php  if($reply['ismobile']) { ?>
			var mobile = $("#mobile").val();
            if (mobile == '') {
                $("#result_info_tip").text("请输入<?php  echo $isfansname['1'];?>");
                return
            }
			var partten = /^1\d{10}$/;
            if(!partten.test(mobile)){
               $("#result_info_tip").text("请输入正确的<?php  echo $isfansname['1'];?>");
			   return;
            }
			<?php  } ?>
			<?php  if($reply['isqq']) { ?>
			var qq = $("#qq").val();
            if (qq == '') {
                $("#result_info_tip").text("请输入<?php  echo $isfansname['2'];?>");
                return
            }			
            var partten = /^[1-9]{1}\d{4,11}$/;
            if(!partten.test(qq)){
               $("#result_info_tip").text("请输入正确的<?php  echo $isfansname['2'];?>");
			   return;
            }
			<?php  } ?>
			<?php  if($reply['isemail']) { ?>
			var email = $("#email").val();
            if (email == '') {
                $("#result_info_tip").text("请输入<?php  echo $isfansname['3'];?>");
                return
            }
			var partten = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
            if(!partten.test(email)){
               $("#result_info_tip").text("请输入正确的<?php  echo $isfansname['3'];?>");
			   return;
            }
			<?php  } ?>
			<?php  if($reply['isaddress']) { ?>
			var address = $("#address").val();
            if (address == '') {
                $("#result_info_tip").text("请输入<?php  echo $isfansname['4'];?>");
                return
            }
			<?php  } ?>
			<?php  if($reply['isgender']) { ?>
			var gender = $("#gender").val();
            if (gender == '0') {
                $("#result_info_tip").text("请选择<?php  echo $isfansname['5'];?>");
                return
            }
			<?php  } ?>
			<?php  if($reply['istelephone']) { ?>
			var telephone = $("#telephone").val();
            if (telephone == '') {
                $("#result_info_tip").text("请输入<?php  echo $isfansname['6'];?>");
                return
            }
			<?php  } ?>
			<?php  if($reply['isidcard']) { ?>
			var idcard = $("#idcard").val();
            if (idcard == '') {
                $("#result_info_tip").text("请输入<?php  echo $isfansname['7'];?>");
                return
            }
			<?php  } ?>
			<?php  if($reply['iscompany']) { ?>
			var company = $("#company").val();
            if (company == '') {
                $("#result_info_tip").text("请输入<?php  echo $isfansname['8'];?>");
                return
            }
			<?php  } ?>
			<?php  if($reply['isoccupation']) { ?>
			var occupation = $("#occupation").val();
            if (occupation == '') {
                $("#result_info_tip").text("请输入<?php  echo $isfansname['9'];?>");
                return
            }
			<?php  } ?>
			<?php  if($reply['isposition']) { ?>
			var position = $("#position").val();
            if (position == '') {
                $("#result_info_tip").text("请输入<?php  echo $isfansname['10'];?>");
                return
            }
			<?php  } ?>
            var submitData = {
                    code: $("#sncode").text(),
                    <?php  if($reply['isrealname']) { ?>realname: realname,<?php  } ?>
					<?php  if($reply['ismobile']) { ?>mobile: mobile,<?php  } ?>
					<?php  if($reply['isqq']) { ?>qq: qq,<?php  } ?>
					<?php  if($reply['isemail']) { ?>email: email,<?php  } ?>
					<?php  if($reply['isaddress']) { ?>address: address,<?php  } ?>
					<?php  if($reply['isgender']) { ?>gender: gender,<?php  } ?>
					<?php  if($reply['istelephone']) { ?>telephone: telephone,<?php  } ?>
					<?php  if($reply['isidcard']) { ?>idcard: idcard,<?php  } ?>
					<?php  if($reply['iscompany']) { ?>company: company,<?php  } ?>
					<?php  if($reply['isoccupation']) { ?>occupation: occupation,<?php  } ?>
					<?php  if($reply['isposition']) { ?>position: position,<?php  } ?>
            };
           	$.post('<?php  echo $this->createMobileUrl('settel', array('rid' => $rid))?>', submitData, function(data) {
			if (data.success == true) {
				$("#result_info").html("<br/><br/>" + data.msg + "<br/><div id='share_miao'>3秒后自动关闭</div>");
				djstime(3);
				setTimeout(function () {
				    $("#panel_box").hide();
					$("#result_info").hide();
					$("#isfans").text('0');
					isplayed = false;
		            percent = 0;
				},4000)
				return
			} else {
			    $("#result_info_tip").text(data.msg);
				return
			}
			},"json")
        });
/*倒计时*/
function djstime(miao){
	var e1=$("#share_miao").first();
	var i=miao;
	var interval=setInterval(function(){
		e1.html(i+"秒自动关闭");
		$("#share_miao").css("line-height","35px");
		i--;
		if(i<0){
			$("#share_miao").css({cursor:"pointer"});
			$("#share_miao").css("line-height","18px");						
			clearInterval(interval);	
		}
	},1000);
}
</script>
<!-- 微信分享设置 -->
<script>var require = { urlArgs: 'v=<?php  echo date('YmdH');?>' };</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?php  echo $_W['siteroot'];?>app/resource/js/require.js"></script>
<script src="<?php  echo $_W['siteroot'];?>app/resource/js/app/config.js"></script>
<script type="text/javascript">
	// jssdk config 对象
	jssdkconfig = <?php  echo json_encode($_W['account']['jssdkconfig']);?> || {};
	// 是否启用调试
	jssdkconfig.debug = false;
	
	jssdkconfig.jsApiList = [
		'checkJsApi',
		'onMenuShareTimeline',
		'onMenuShareAppMessage',
		'onMenuShareQQ',
		'onMenuShareWeibo',
		'showOptionMenu'
	];

	wx.config(jssdkconfig);
	require(['jquery', 'util'], function($, util){
		var sharedata = {
	        "imgUrl" : "<?php  echo $shareimg;?>",
	        "link" : "<?php  echo $sharelink;?>",
	        "desc" : "<?php  echo $sharedesc;?>",
	        "title" : "<?php  echo $sharetitle;?>"
	    };		
		
		wx.ready(function () {
			wx.showOptionMenu();
			wx.onMenuShareAppMessage({
			    title: sharedata.title,
			    desc: sharedata.desc,
			    link: sharedata.link,
			    imgUrl: sharedata.imgUrl,			    
			    <?php  if(!empty($share['share_cancel'])) { ?>
				// 分享取消
				cancel: function (res) {
			        $("#share_tip").text("<?php  echo $share['share_cancel'];?>");
			        $("#share_miao").text("5秒后自动关闭");
			        $("#share_box").show();
			        djstime(5);
			        setTimeout(function () { 
			            $("#share_box").hide();
			        }, 6000);
			    },
				<?php  } ?>
				<?php  if(!empty($share['share_fail'])) { ?>
				// 分享取消
			    fail: function (res) {
			        $("#share_tip").text("<?php  echo $share['share_fail'];?>");
			        $("#share_miao").text("5秒后自动关闭");
			        $("#share_box").show();
			        djstime(5);
			        setTimeout(function () { 
			            $("#share_box").hide();
			        }, 6000);
			    },
				<?php  } ?>
				<?php  if(!empty($share['share_confirm'])) { ?>
				// 分享成功
				success : function(resp) {
				    // 分享成功了，我们是不是可以做一些分享统计呢？
				    $.getJSON('<?php  echo $this->createMobileUrl('get_share', array('rid' => $rid,'from_user' => $page_from_user))?>', function(data){
			    	    if(data.success==1) {
                    	    $("#share_tip").text("<?php  echo $share['share_confirm'];?>");
			   	     	}else{
			        	    $("#share_tip").text(data.msg);
			    	    }
			    	    $("#share_miao").text("5秒后自动关闭");
			    	    $("#share_box").show();
			   	        djstime(5);
			    	    setTimeout(function () { 
                    	    $("#share_box").hide();
                	    }, 6000);
				    });
				},
				<?php  } ?>
			});
			wx.onMenuShareTimeline({
			    title: sharedata.title,
			    desc: sharedata.desc,
			    link: sharedata.link,
			    imgUrl: sharedata.imgUrl,			    
			    <?php  if(!empty($share['share_cancel'])) { ?>
				// 分享取消
				cancel: function (res) {
			        $("#share_tip").text("<?php  echo $share['share_cancel'];?>");
			        $("#share_miao").text("5秒后自动关闭");
			        $("#share_box").show();
			        djstime(5);
			        setTimeout(function () { 
			            $("#share_box").hide();
			        }, 6000);
			    },
				<?php  } ?>
				<?php  if(!empty($share['share_fail'])) { ?>
				// 分享取消
			    fail: function (res) {
			        $("#share_tip").text("<?php  echo $share['share_fail'];?>");
			        $("#share_miao").text("5秒后自动关闭");
			        $("#share_box").show();
			        djstime(5);
			        setTimeout(function () { 
			            $("#share_box").hide();
			        }, 6000);
			    },
				<?php  } ?>
				<?php  if(!empty($share['share_confirm'])) { ?>
				// 分享成功
				success : function(resp) {
				    // 分享成功了，我们是不是可以做一些分享统计呢？
				    $.getJSON('<?php  echo $this->createMobileUrl('get_share', array('rid' => $rid,'from_user' => $page_from_user))?>', function(data){
			    	    if(data.success==1) {
                    	    $("#share_tip").text("<?php  echo $share['share_confirm'];?>");
			   	     	}else{
			        	    $("#share_tip").text(data.msg);
			    	    }
			    	    $("#share_miao").text("5秒后自动关闭");
			    	    $("#share_box").show();
			   	        djstime(5);
			    	    setTimeout(function () { 
                    	    $("#share_box").hide();
                	    }, 6000);
				    });
				},
				<?php  } ?>
			});
			wx.onMenuShareQQ({
			    title: sharedata.title,
			    desc: sharedata.desc,
			    link: sharedata.link,
			    imgUrl: sharedata.imgUrl,			    
			    <?php  if(!empty($share['share_cancel'])) { ?>
				// 分享取消
				cancel: function (res) {
			        $("#share_tip").text("<?php  echo $share['share_cancel'];?>");
			        $("#share_miao").text("5秒后自动关闭");
			        $("#share_box").show();
			        djstime(5);
			        setTimeout(function () { 
			            $("#share_box").hide();
			        }, 6000);
			    },
				<?php  } ?>
				<?php  if(!empty($share['share_fail'])) { ?>
				// 分享取消
			    fail: function (res) {
			        $("#share_tip").text("<?php  echo $share['share_fail'];?>");
			        $("#share_miao").text("5秒后自动关闭");
			        $("#share_box").show();
			        djstime(5);
			        setTimeout(function () { 
			            $("#share_box").hide();
			        }, 6000);
			    },
				<?php  } ?>
				<?php  if(!empty($share['share_confirm'])) { ?>
				// 分享成功
				success : function(resp) {
				    // 分享成功了，我们是不是可以做一些分享统计呢？
				    $.getJSON('<?php  echo $this->createMobileUrl('get_share', array('rid' => $rid,'from_user' => $page_from_user))?>', function(data){
			    	    if(data.success==1) {
                    	    $("#share_tip").text("<?php  echo $share['share_confirm'];?>");
			   	     	}else{
			        	    $("#share_tip").text(data.msg);
			    	    }
			    	    $("#share_miao").text("5秒后自动关闭");
			    	    $("#share_box").show();
			   	        djstime(5);
			    	    setTimeout(function () { 
                    	    $("#share_box").hide();
                	    }, 6000);
				    });
				},
				<?php  } ?>
			});
			wx.onMenuShareWeibo({
			    title: sharedata.title,
			    desc: sharedata.desc,
			    link: sharedata.link,
			    imgUrl: sharedata.imgUrl,			    
			    <?php  if(!empty($share['share_cancel'])) { ?>
				// 分享取消
				cancel: function (res) {
			        $("#share_tip").text("<?php  echo $share['share_cancel'];?>");
			        $("#share_miao").text("5秒后自动关闭");
			        $("#share_box").show();
			        djstime(5);
			        setTimeout(function () { 
			            $("#share_box").hide();
			        }, 6000);
			    },
				<?php  } ?>
				<?php  if(!empty($share['share_fail'])) { ?>
				// 分享取消
			    fail: function (res) {
			        $("#share_tip").text("<?php  echo $share['share_fail'];?>");
			        $("#share_miao").text("5秒后自动关闭");
			        $("#share_box").show();
			        djstime(5);
			        setTimeout(function () { 
			            $("#share_box").hide();
			        }, 6000);
			    },
				<?php  } ?>
				<?php  if(!empty($share['share_confirm'])) { ?>
				// 分享成功
				success : function(resp) {
				    // 分享成功了，我们是不是可以做一些分享统计呢？
				    $.getJSON('<?php  echo $this->createMobileUrl('get_share', array('rid' => $rid,'from_user' => $page_from_user))?>', function(data){
			    	    if(data.success==1) {
                    	    $("#share_tip").text("<?php  echo $share['share_confirm'];?>");
			   	     	}else{
			        	    $("#share_tip").text(data.msg);
			    	    }
			    	    $("#share_miao").text("5秒后自动关闭");
			    	    $("#share_box").show();
			   	        djstime(5);
			    	    setTimeout(function () { 
                    	    $("#share_box").hide();
                	    }, 6000);
				    });
				},
				<?php  } ?>
			});
			
		});
	});
</script>
</body>
</html>