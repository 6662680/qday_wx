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
<body>
<div style="max-width:500px;">
    <div class="nou">
	    <div style="max-width:100%">
		<?php  if(!empty($reply['adpic'])) { ?><?php  if(!empty($reply['adpicurl'])) { ?><a href="<?php  echo $reply['adpicurl'];?>"><?php  } ?><img id="top_img" style="max-width: 100%;height: auto;width: auto\9;"  src="<?php  echo toimage($reply['adpic'])?>" width="100%" border="0"><?php  if(!empty($reply['adpicurl'])) { ?></a><?php  } ?><?php  } ?>
        </div>       
        <div class="zhuan1" id="lottery">
			<div class="mingdan">
                <h2 class="biaoti"><?php  if(!empty($msg)) { ?><?php  echo $msg;?><?php  } else { ?>我的奖品<?php  } ?></h2>
            </div>
            <?php  if(!empty($award) || !empty($awardw)) { ?>
			<div class="mingdaninfo">
				<div class="box">					
					<div class="Detail">
						<?php  if(!empty($awardw)) { ?><p><strong>未兑奖项</strong></p><?php  } ?>
						<?php  if(is_array($awardw)) { foreach($awardw as $roww) { ?>
						<?php  if($roww['num']==1) { ?>
						<p>你中了：<?php  if(empty($roww['name'])) { ?>感谢参与<?php  } else { ?><?php  echo $roww['name'];?> - <?php  echo $roww['description'];?><?php  } ?></p>
						<?php  } else { ?>
						<p>你中了：<?php  if(empty($roww['name'])) { ?>感谢参与<?php  } else { ?><?php  echo $roww['name'];?> - <?php  echo $roww['description'];?> X<?php  echo $roww['num'];?><?php  } ?></p>
						<?php  } ?>
						<p class="line"></p>
						<?php  } } ?>
						<?php  if(!empty($award)) { ?><p><strong>已兑奖项</strong></p><?php  } ?>
						<?php  if(is_array($award)) { foreach($award as $row) { ?>
						<?php  if($row['num']==1) { ?>
						<p>你兑了：<?php  if(empty($row['name'])) { ?>感谢参与<?php  } else { ?><?php  echo $row['name'];?> - <?php  echo $row['description'];?><?php  } ?></p>
						<?php  } else { ?>
						<p>你兑了：<?php  if(empty($row['name'])) { ?>感谢参与<?php  } else { ?><?php  echo $row['name'];?> - <?php  echo $row['description'];?>  X<?php  echo $row['num'];?><?php  } ?></p>
						<?php  } ?>
						<p class="line"></p>						
						<?php  } } ?>
						<?php  if($reply['opportunity']==1) { ?>
						<p>请前往您的服务网点进行兑奖<br/>
						网点：<?php  echo $business['title'];?><br/>
						地址：<?php  echo $business['address'];?><br/>
						电话：<a href="tel:<?php  echo $business['phone'];?>"><?php  echo $business['phone'];?></a></p>
						<?php  if(!empty($awardw)) { ?>
						<?php  if(!empty($business['password'])) { ?>
						<span id="result_duijiang">
						<input name="bid" type="hidden" value="<?php  echo $business['id'];?>">
						<p><input name="password" class="px" id="password" type="text" placeholder="请网点工作人员输入密码"></p>
                        <p><input class="pxbtn" name="提 交" id="save-duijiang" type="button" value="工作人员提交兑奖"></p>
						</span>
						<?php  } else { ?>
						 <p><input class="pxbtn" type="button" value="向工作人员展示兑奖"></p>
						<?php  } ?>
						<?php  } else { ?>
						<p><input class="pxbtn" type="button" value="已成功兑奖！"></p>
						<?php  } ?>
						<?php  } else { ?>
						<p>本次兑奖码已经关联你的微信号，你可向公众号发送【<?php  $tempArr=explode(',',$reply['keyword']);echo $tempArr['0'];?>】进行查询!<br/>
						<?php  echo $reply['ticket_information'];?></p>
						<?php  } ?>
					</div>
				</div>
			</div>
			<?php  } ?>
			<div style="height:45px;">&nbsp;&nbsp;</div>
        </div>
    </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>
<script type="text/javascript">
    $("#save-duijiang").bind("click",function () {
            var btn1 = $(this);            
			var password = $("#password").val();
            if (password == '') {
                alert("请输入兑奖密码");
                return
            }
			
            var submitData1 = {
					code: $("#sncode").text(),
					password: password,
            };
           	$.post('<?php  echo $this->createMobileUrl('duijiang', array('rid' => $rid,'fansID' => $fansID))?>', submitData1, function(data1) {
			if (data1.success == true) {
				alert(data1.msg);
				$("#result_duijiang").html(data1.msg);
				return
			} else {
			    $("#password").val(data1.msg);
				return
			}
			},"json")
    });
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('jssdkhide', TEMPLATE_INCLUDEPATH)) : (include template('jssdkhide', TEMPLATE_INCLUDEPATH));?>
</body>
</html>