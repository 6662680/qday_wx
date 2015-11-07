<?php defined('IN_IA') or exit('Access Denied');?>
<header style='position:fixed;bottom:0;'>
    <h1 class="ktvtitle"></h1>
    <!--<h1 class="ktvtitle">ktv详情</h1>-->
      <div class="lefthead" onClick="location.href='javascript:history.go(-1);'">
        <div class="header_return"></div>
    </div>
    
    <div class="righthead">
        <a class="header_home" href="<?php  echo $this->createMobileUrl('index', array('hid' => $hid))?>">&nbsp;</a>
        <?php  if($this->_set_info['is_unify']==1 && !empty($this->_set_info['tel'])) { ?>
        <a class="header_tel __hreftel__" href="tel:<?php  echo $this->_set_info['tel']?>"></a>
        <?php  } ?>
        <a class="header_order" href="<?php  echo $this->createMobileUrl('orderlist')?>">&nbsp;</a>
    </div>
</header>
  <script type="text/javascript">
document.addEventListener("WeixinJSBridgeReady", function onBridgeReady() {
WeixinJSBridge.call("hideToolbar");
});
</script>