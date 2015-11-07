<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php  echo $reply['title'];?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=yes;" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="email=no" name="format-detection" />
    <link href="../addons/stonefish_bigwheel/template/css/square.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../addons/stonefish_bigwheel/template/js/WeixinApi.js"></script>
</head>
<style type="text/css">
#lottery table td.active{background-image:url(<?php  echo toimage($reply['bigwheelimgbg'])?>);}
</style>
<body>
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
                <div class="biaoti"><?php  if(!empty($msg)) { ?><?php  echo $msg;?><?php  } else { ?>赠送 <span class="n"><?php  echo $reply['number_times'];?></span> 次抽奖机会 剩余 <span class="n" id="number_times"><?php  echo $reply['number_times']-$fans['totalnum']?></span> 次<?php  } ?></div>
            </div>
			<?php  } ?>
			<?php  if($reply['opportunity']==0) { ?>
			<div class="mingdaninfo">
				<div class="box">					
					<div class="Detail" style="color:#FFF;text-align:center;"><?php  echo $detail;?></div>
				</div>
            </div>
			<?php  } ?>
            <table id="tb" style="max-width: 100%;background-color: #FD6830;" align="center;" >
                <tr>
					<?php  $i=0?>
					<?php  if(is_array($prize0_3)) { foreach($prize0_3 as $prizes0_3) { ?>
					<td class="playnormal lottery-unit lottery-unit-<?php  echo $i;?>">
                        <div class="xx">
                            <?php  if(!empty($prizes0_3['prizepic'])) { ?><img src="<?php  echo toimage($prizes0_3['prizepic'])?>" /><?php  } ?>
                        </div>
                        <?php  if(empty($prizes0_3['prizepic'])) { ?><div class="ee"><?php  echo $prizes0_3['prizetype'];?></div><?php  } ?>
                    </td>
					<?php  $i++?>
					<?php  } } ?>                    
                </tr>
				<tr>
                    <td class="playnormal lottery-unit lottery-unit-11">
                        <div class="xx">
                            <?php  if(!empty($prize11['prizepic'])) { ?><img src="<?php  echo toimage($prize11['prizepic'])?>" /><?php  } ?>
                        </div>
                        <?php  if(empty($prize11['prizepic'])) { ?><div class="ee"><?php  echo $prize11['prizetype'];?></div><?php  } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td class="playnormal lottery-unit lottery-unit-4">
                        <div class="xx">
                            <?php  if(!empty($prize4['prizepic'])) { ?><img src="<?php  echo toimage($prize4['prizepic'])?>" /><?php  } ?>
                        </div>
                         <?php  if(empty($prize4['prizepic'])) { ?><div class="ee"><?php  echo $prize4['prizetype'];?></div><?php  } ?>
                    </td>
                </tr>
                <tr>
                    <td class="playnormal lottery-unit lottery-unit-10">
                        <div class="xx">
                            <?php  if(!empty($prize10['prizepic'])) { ?><img src="<?php  echo toimage($prize10['prizepic'])?>" /><?php  } ?>
                        </div>
                         <?php  if(empty($prize10['prizepic'])) { ?><div class="ee"><?php  echo $prize10['prizetype'];?></div><?php  } ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td class="playnormal lottery-unit lottery-unit-5">
                        <div class="xx">
                            <?php  if(!empty($prize5['prizepic'])) { ?><img src="<?php  echo toimage($prize5['prizepic'])?>" /><?php  } ?>
                        </div>
                         <?php  if(empty($prize5['prizepic'])) { ?><div class="ee"><?php  echo $prize5['prizetype'];?></div><?php  } ?>
                    </td>
                </tr>
                <tr>
                    <?php  $i=9?>
					<?php  if(is_array($prize6_9)) { foreach($prize6_9 as $prizes6_9) { ?>
					<td class="playnormal lottery-unit lottery-unit-<?php  echo $i;?>">
                        <div class="xx">
                            <?php  if(!empty($prizes6_9['prizepic'])) { ?><img src="<?php  echo toimage($prizes6_9['prizepic'])?>" /><?php  } ?>
                        </div>
                         <?php  if(empty($prizes6_9['prizepic'])) { ?><div class="ee"><?php  echo $prizes6_9['prizetype'];?></div><?php  } ?>
                    </td>
					<?php  $i--?>
					<?php  } } ?>
                </tr>
            </table>
            <div class="liji">                
                <a href="javascript:square()" id="btn"><span id="liji1" class="liji1" style="display:none"><?php  echo $running;?></span>
				<img id="liji2" class="liji2" src="<?php  echo toimage($reply['bigwheelimgan'])?>" width="132" border="0" align="middle" /></a>
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
<script type="text/javascript" src="../addons/stonefish_bigwheel/template/js/jquery-1.8.3.min.js"></script>
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
				},4000)
				return
			} else {
			    $("#result_info_tip").text(data.msg);
				return
			}
			},"json")
        });
		
var lottery={
	index:-1,	//当前转动到哪个位置，起点位置
	count:0,	//总共有多少个位置
	timer:0,	//setTimeout的ID，用clearTimeout清除
	speed:20,	//初始转动速度
	times:0,	//转动次数
	cycle:40,	//转动基本次数：即至少需要转动多少次再进入抽奖环节
	prize:-1,	//中奖位置
	init:function(id){
		if ($("#"+id).find(".lottery-unit").length>0) {
			$lottery = $("#"+id);
			$units = $lottery.find(".lottery-unit");
			this.obj = $lottery;
			this.count = $units.length;
			$lottery.find(".lottery-unit-"+this.index).addClass("active");
		};
	},
	roll:function(){
		var index = this.index;
		var count = this.count;
		var lottery = this.obj;
		$(lottery).find(".lottery-unit-"+index).removeClass("active");
		index += 1;
		if (index>count-1) {
			index = 0;
		};
		$(lottery).find(".lottery-unit-"+index).addClass("active");
		this.index=index;
		return false;
	},
	stop:function(index){
		this.prize=index;	
		return false;		
	}
};

function roll(){
	lottery.times += 1;
	lottery.roll();
	if (lottery.times > lottery.cycle+10 && lottery.prize==lottery.index) {
		clearTimeout(lottery.timer);
		lottery.prize=-1;
		lottery.times=0;
		click=false;
	}else{
		if (lottery.times<lottery.cycle) {
			lottery.speed -= 10;
		}else if(lottery.times==lottery.cycle) {
			lottery.prize = lottery.prizes;
			setTimeout(function () { 
                $("#panel_box").show();
				$("#credit_now").text(lottery.credit_now);
            }, 1500);
		}else{
			if (lottery.times > lottery.cycle && ((lottery.prize==0 && lottery.index==7) || lottery.prize==lottery.index+1)) {
				//lottery.speed += 110;
			}else{
				//lottery.speed += 20;
			}
		}
		if (lottery.speed<40) {
			lottery.speed=40;
		};
		//console.log(lottery.times+'^^^^^^'+lottery.speed+'^^^^^^^'+lottery.prize);
		lottery.timer = setTimeout(roll,lottery.speed);
		
	}
	return false;	
}

var click=false;

window.onload=function(){
	<?php  if($isfans) { ?>
		$("#panel_box").show();
		$("#panel-content").css({"height": "<?php  echo $isfansh;?>px"});
	<?php  } ?>
	lottery.init('lottery');
	$("#btn").click(function(){
	    if($("#isfans").text()==1){
		$("#panel_box").show();
		$("#panel-content").css({"height": "<?php  echo $isfansh;?>px"});
	    }else{
		if (click) {
			return false;
		}else{
			$.getJSON('<?php  echo $this->createMobileUrl('get_award', array('rid' => $rid))?>', function(data){
			    if(data.success==1) {
                    lottery.prizes = data.prizetype-1;
					lottery.credit_now = data.credit_now;
					$("#panelimg").css({"background-image": "url("+data.award['prizepic']+")"});
					if(data.award['credit_type']=='spaceprize'){
					    $("#cccc").text(data.award['prizetype']+"-"+data.award['prizename']+"-增送"+data.sn+"");
					}else{
					    $("#cccc").text(data.award['prizetype']+"-"+data.award['prizename']);
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
					lottery.speed=100;
			        roll();
			        click=true;
                }else{
                    $("#panelimg").css({"background-image": "url(../addons/stonefish_bigwheel/template/images/icon_prize_useless.png)"});
					$("#panel_box").show();
					$("#result_info1").hide();
					$("#cccc").text(data.msg);
					return false;
                }
				$("#panel-content").css({"height": ""+data.height+"px"});
				if(data.isfans){
					$("#result_info").show();
					$("#result_info1").hide();					
				}else{
					$("#result_info").hide();
					if(data.award['credit_type']=='spaceprize'){
					    $("#result_info2").show();
						$("#result_info1").hide();
				    }else{
					    $("#result_info1").show();
						$("#result_info2").hide();
					}					
				}				
			});				   
			return false;
		}
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
	});
	$("#savebtn").click(function(){
		$("#panel_box").hide();
	});
	$("#closebtn").click(function(){
		$("#panel_box").hide();
	});
	$("#share_close").click(function(){
		$("#share_box").hide();
	});
	$("#sharebtn").click(function(){
		$("#share_box").hide();
	});
};
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