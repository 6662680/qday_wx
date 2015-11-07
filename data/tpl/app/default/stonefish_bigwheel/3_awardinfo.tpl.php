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
        <div class="zhuan1">
		    <div class="mingdan">
                <h2 class="biaoti">奖品设置</h2>
            </div>
            <div class="mingdaninfo">
				<?php  if($reply['show_num']>=1) { ?>
				<div>
				<?php  if(is_array($prize)) { foreach($prize as $prizes) { ?>
				<p style="text-align:center; margin:0px 20px;"><img src="<?php  echo toimage($prizes['prizepic'])?>"><br/><?php  echo $prizes['prizetype'];?> - <?php  echo $prizes['prizename'];?><?php  if($reply['show_num']==2) { ?> X<?php  echo $prizes['prizetotal'];?><?php  } ?></p>
				<p class="line"></p>
				<?php  } } ?>
				</div>
				<?php  } ?>
				<div><?php  echo htmlspecialchars_decode($reply['award_info'])?></div>
            </div>			
			<div style="height:45px;">&nbsp;&nbsp;</div>
        </div>
    </div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('footer', TEMPLATE_INCLUDEPATH)) : (include template('footer', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('jssdkhide', TEMPLATE_INCLUDEPATH)) : (include template('jssdkhide', TEMPLATE_INCLUDEPATH));?>
</body>
</html>