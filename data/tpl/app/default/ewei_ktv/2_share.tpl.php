<?php defined('IN_IA') or exit('Access Denied');?><?php  echo register_jssdk();?>
<script type="text/javascript">
    // 微ktv分享
    wx.ready(function () {
        sharedata = {
            title: document.title,
            desc: <?php  if(empty($shareDesc)) { ?>document.title + " - <?php  echo $_W['account']['name'];?>"<?php  } else { ?>"<?php  echo $shareDesc;?>"<?php  } ?>,
            link: "<?php  echo $_W['siteurl'];?>",
            imgUrl: <?php  if(empty($shareThumb)) { ?>"<?php  echo $_W['siteroot'];?>addons/ewei_ktv/preview.jpg"<?php  } else { ?>"<?php  echo $shareThumb;?>"<?php  } ?>

        };
        wx.onMenuShareAppMessage(sharedata);
        wx.onMenuShareTimeline(sharedata);
    });
</script>
