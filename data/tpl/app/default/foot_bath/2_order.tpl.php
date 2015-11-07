<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>套餐预订</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="apple-mobile-web-app-title" content="套餐预订"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta content="telephone=no" name="format-detection"/>
    <link href="<?php  echo $this->_css_url?>css.css" rel="stylesheet"/>
    <link href="<?php  echo $this->_css_url?>jquery-ui-1.10.3.css" rel="stylesheet"/>
    <link rel="Stylesheet" type="text/css" href="<?php  echo $this->_css_url?>mian.css">
    <script language='javascript' src='<?php  echo $this->_script_url?>jquery.js'></script>
    <script language='javascript' src='<?php  echo $this->_script_url?>jquery-ui-1.10.3.min.js'></script>
    <script language='javascript' src='<?php  echo $this->_script_url?>common.js'></script>
    <script language='javascript' src='<?php  echo $this->_script_url?>jquery.form.js'></script>
    <!--<script type="application/x-javascript">addEventListener('DOMContentLoaded', function () {-->
        <!--setTimeout(function () {-->
            <!--scrollTo(0, 1);-->
        <!--}, 0);-->
    <!--}, false);</script>-->
    <style type="text/css">html, body, #main-content {
        height: 100%;
    }

    .h100 {
        height: 88%;
        height: -moz-calc(100% - 93px);
        height: -webkit-calc(100% - 93px);
        height: calc(100% - 93px);
    }

    .cui-form-select {
        height: 38px;
        line-height: 38px;
    }

    .sleBG {
        height: 38px;
        line-height: 38px;
    }

    .novip {
        font-size: 14px;
        padding-bottom: 5px;
    }

    #orderinfo .ui-grid-a, #checkin-name li {
        height: 38px;
        position: relative;
    }

    #orderinfo .ui-grid-a .ui-block-a {
        width: 25%;
        line-height: 38px;
        padding-left: 10px;
    }

    #orderinfo .ui-grid-a .ui-block-b {
        width: 75%;
    }

    input.ui-input-text {
        width: 90%;
    }

    #orderinfo #checkin-name {
        height: auto;
    }

    #orderinfo #pay-info {
        height: auto;
    }

    #pay-info p {
        text-align: center;
        padding: 5px;
    }

    #disabled {
        display: none;
    }

    .ui-li-count {
        display: none !important;
    }

    .ui-bar-c {
        height: 40px;
    }

    .ui-selectmenu .ui-title {
        display: none;
    }

    #remark {
    }

    .guarantee {
        display: none;
        background-color: #FFF1D5;
        height: 35px;
        padding: 10px;
        color: #666666;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .room_num {
        top: 3px;
        right: 5px;
    }

    .paybtn-box {
        height: 45px;
        width: 100%;
    }

    /*.paybtn{position: fixed;left: 0px;bottom: 0px;} */
    .jsH_ydwxcs {
        position: relative !important;
    }

    .jsH {
        /*position:fixed;*/
        z-index: 9999;
    }

    #paytype_div {
        position: absolute; width: 100%; height:100%; left: 0px; top: 0px; z-index: 16531; display: none;
    }

    .pay-info {margin-left: 30px;}

</style>

</head>

<body id="ctripPage" style='padding-bottom:40px;'>
    <div id="main-content">
        <section id="ydwxcsH"></section>
        <header>
            <h1 class="hoteltitle">套餐预订</h1>

            <div class="lefthead" onClick="location.href='javascript:history.back();'">
                <div class="header_return"></div>
            </div>
            <div class="righthead">
                <a class="header_tel __hreftel__" href="tel:<?php  echo $tel;?>"></a>
                <a id="imgHome" class="header_home" href="<?php  echo $this->createMobileUrl('index')?>">&nbsp;</a>
                <a class="header_order" href="<?php  echo $this->createMobileUrl('orderlist')?>">&nbsp;</a>
            </div>
        </header>
        <form name="form1" method="POST" action="<?php  echo $this->createMobileUrl('OrderSubmit')?>" id='data_form'>
            <div class="h100">
                <div class="jsM"></div>
                <div class="novip" style="display: block;">
                </div>
                <div class="conlist">
                    <div style="line-height: 25px;" id="des">
                        <div><span class="bold size16"><?php  echo $reply['title'];?></span></div>
                        <div><?php  echo $room['title'];?></div>
                        <div>
                            <span class="bold">技师姓名（编号）：</span>
                            <?php  if(!empty($t_detail)) { ?><span class="bold span2 inday"><?php  echo $t_detail['name'];?></span><?php  } ?>
                        </div>
                        <div>
                            <span class="bold">套餐：</span>
                            <?php  if(!empty($p_detail)) { ?><span class="bold span2 inday"><?php  echo $p_detail['p_name'];?></span><?php  } ?>
                        </div>
                    </div>
                </div>
                <div style="padding: 0px 10px">
                    <div class="conlist">
                        <div id="orderinfo">
                            <!--<div id="checkin-name">
                                <div class="ui-txt clear-input-box" data-d="1">
                                    <input type="text" id="uname" name="uname" value="<?php  echo $realname;?>" required="required" data-lid="0" class="checkin-name" placeholder="入住人（必填）">
                                    <a class="clear-input " href="javascript:;" style="display: none;"><span></span></a>
                                </div>
                            </div>-->
                            <div class="ui-txt clear-input-box">
                                <input type="text" data-contact="contact_name" id="contact_name" name="contact_name" value="<?php  echo $realname;?>" required="required" placeholder="联系人（必填）">
                                <a class="clear-input " href="javascript:;" style="display: none;"><span></span></a>
                            </div>
                            <div class="ui-txt clear-input-box">
                                <input type="tel" data-contact="contact-tel" id="mobile" name="mobile" placeholder="手机号（必填）" required="required" value="<?php  echo $mobile;?>" maxlength="11">
                                <a class="clear-input " href="javascript:;" style="display: none;"><span></span></a>
                            </div>
                            <div class="ui-txt clear-input-box" id="handlerCheckInDate">
                                <em class="orderWrite_txt " id="Check_In_Date">
                                    <input style="width:80%;" name="bdate" id="bdate" class=" bdate datepicker" value="<?php  echo date('Y-m-d',strtotime($bdate))?>" type="text" readonly="readonly" />
                                    <span style="float: right;text-indent: 10px;line-height: 40px;">预订日期</span>
                                </em>
                                <a class="clear-input " href="javascript:;" style="display: none;"><span></span></a>
                            </div>
                            <div class="ui-txt clear-input-box">
                                <textarea style="border: none;background: #f2f2f2;height: 38px;width: 100%;font-size: 16px;text-indent: 10px;box-shadow: none!important;" name="detail" placeholder="详情"></textarea>
                                <a class="clear-input " href="javascript:;" style="display: none;"><span></span></a>
                            </div>
                            <div class="ui-txt" id="btn_paytype">
                                <div class="ui-grid-a">
                                    <div class="ui-block-a">
                                        <?php  if(empty($this->_set_info['paytype1']) && empty($this->_set_info['paytype2'])) { ?>
                                        <span id='paytype_name'>在线预订</span>
                                        <input type="hidden" name="paytype" id="paytype" value='3'/>
                                        <?php  } else { ?>
                                        <span id='paytype_name'>支付方式</span>
                                        <input type="hidden" name="paytype" id="paytype"/>
                                        <?php  } ?>
                                    </div>
                                </div>
                            </div>
                            <p id="notice" class="orange size12">
                            </p>
                        </div>
                    </div>
                </div>
                <div class="xfqbox white_li" style="display: none">
                    <span class="coupon checkno" data-select="0"></span>使用消费券
                    <span class="amont"></span>元
                    <a href="../Market/hotxfq.html">消费券使用说明&gt;</a>
                </div>
                <div class="paybtn-box">
                    <div class="ui-grid-a paybtn">
                        <div class="ui-block-a">应付总价：<dfn class="fff">¥</dfn>
                            <strong class="size20 fff" id="price"><?php  echo $price;?></strong></div>
                        <div id="button" class="ui-block-b">
                            <input style="width:80%;" name="btime"  id="btime" value="" type="hidden" readonly="readonly" />
                            <strong id="data_submit">提交订单</strong></div>
                        <div id="orderAlert" class="ui-block-b" style="display: none;">
                            <strong>请稍候...</strong></div>
                    </div>
                </div>

            </div>

            <div class="cui-select-float-box cui-form-select-float" tabindex="1" id="paytype_div">
                <div class="cui-select-head">
                    <div class="cui-select-close">关闭</div>
                    <div class="cui-select-title">选择支付方式</div>
                </div>
                <div class="cui-select-cont-box" style="height:100%;background:#fff">
                    <?php  if($this->_set_info['paytype1'] == 1 && !empty($_W['account']['payment']['credit']['switch'])) { ?>
                    <div class="cui-select-option cui-option-over" data-value="1" data-name='余额支付'>
                        <b>余额支付</b>
                        <span class="pay-info">会员卡余额支付</span>
                    </div>
                    <?php  } ?>
                    <?php  if($this->_set_info['paytype2'] == 21 && !empty($_W['account']['payment']['wechat']['switch'])) { ?>
                    <div class="cui-select-option cui-option-over" data-value="21" data-name='微支付'>
                        <b>微信支付</b>
                        <span class="pay-info">每笔最高1万, 每日限额1万</span>
                    </div>
                    <?php  } ?>
                    <?php  if($this->_set_info['paytype2'] == 22 && !empty($_W['account']['payment']['alipay']['switch'])) { ?>
                    <div class="cui-select-option cui-option-over" data-value="22" data-name='支付宝'>
                        <b style="margin-right: 15px;">支付宝</b>
                        <span class="pay-info">每笔最高3万, 每日限额5万</span>
                    </div>
                    <?php  } ?>
                    <?php  if($this->_set_info['paytype2'] == 23 && !empty($_W['account']['payment']['alipay']['switch']) &&!empty($_W['account']['payment']['wechat']['switch'])) { ?>
                    <div class="cui-select-option cui-option-over" data-value="21" data-name='微支付'>
                        <b>微信支付</b>
                        <span class="pay-info">每笔最高1万, 每日限额1万</span>
                    </div>
                    <div class="cui-select-option cui-option-over" data-value="22" data-name='支付宝'>
                        <b style="margin-right: 15px;">支付宝</b>
                        <span class="pay-info">每笔最高3万, 每日限额5万</span>
                    </div>
                    <?php  } ?>
                    <?php  if($this->_set_info['paytype3'] == 1) { ?>
                    <div class="cui-select-option cui-option-over" data-value="3" data-name='到店支付'>
                        <b>到店支付</b>
                        <span class="pay-info">入住时支付</span>
                    </div>
                    <?php  } ?>
                </div>
            </div>
            <input type="hidden" name="t_name" value="<?php  echo $t_detail['name'];?>">
            <input type="hidden" name="p_name" value="<?php  echo $p_detail['p_name'];?>">
            <input type="hidden" name="t_id" value="<?php  echo $t_id;?>"/>
            <input type="hidden" name="p_id" value="<?php  echo $p_id;?>"/>
            <input type="hidden" name="price" value="<?php  echo $price;?>"/>
            <input type="hidden" name="submit" value="1"/>
            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>"/>
        </form>

        <input type="hidden" id="page_id" value="212098">
    </div>

<?php  include $this->template('hotel_msg')?>

<script type="text/javascript">
    $(function () {
        $("#data_form").ajaxForm();
        var Rooms = 1;
        var price = <?php  echo $price;?>;
        var sum = 0;
    /*    var max_room = <?php  echo $max_room;?>;*/

        /*$(".list_num_inc").click(function () {
            if (Rooms < max_room) {
                Rooms++;
                if (Rooms == max_room){
                    $("#show_room_num").show();
                }
                resetRooms();
            }
        });*/
        /*$(".list_num_dec").click(function () {
            if (Rooms > 1) {
                Rooms--;
                resetRooms();
            }
        });*/
        $(".cui-select-option").click(function(){
            $(".cui-select-option").removeClass("cui-option-current");
            var obj = $(this);
            obj.addClass("cui-option-current");
            $("#paytype").val(obj.attr("data-value"));
            $("#paytype_name").html(obj.attr("data-name"));
            $("#paytype_div").hide();
            
        });
        $(".cui-select-close").click(function(){
            $("#paytype_div").hide();
        })
        <?php  if(!empty($this->_set_info['paytype1']) || !empty($this->_set_info['paytype2'])) { ?>
             $("#btn_paytype").click(function(){
               $("#paytype_div").show();     
             });
        <?php  } ?>

        /*loadRooms();

        function loadRooms() {
            if (max_room == 1) {
                $("#show_room_num").show();
                resetRooms();
            }
        }*/

       /* function resetRooms() {
            var c = ".list_num_dec", a = "num_invalid", b = ".list_num_inc";
            $(b).removeClass(a);
            $(c).addClass(a);
            $(".list_num").html(Rooms);
            $("#nums").val(Rooms);
            sum = Rooms*price;
            $("#price").html(sum);

            if (max_room > 1) {
                if (Rooms == 1) {
                    $(c).addClass(a);
                    $(b).removeClass(a)
                } else if (Rooms == max_room) {
                    $(c).removeClass(a);
                    $(b).addClass(a)
                } else {
                    $(c).removeClass(a);
                    $(b).removeClass(a)
                }
            } else {
                $(b).addClass(a);
                $(c).addClass(a);
            }
        }*/

        $("#data_submit").click(function(){
            /*if ($.trim($("#uname").val()) == '') {
                show_msg("请填写入住人", 2000);
                $("#uname").focus();
                return false;
            }*/
            if ($.trim($("#contact_name").val()) == '') {
                show_msg("请填写联系人", 2000);
                $("#contact_name").focus();
                return false;
            }
            var str =$.trim($("#mobile").val());
            var isMobile = $.trim(str) !== '' && /^1[3|4|5|8][0-9]\d{4,8}$/.test($.trim(str));
            if (!isMobile) {
                show_msg("请填写正确的手机号", 2000);
                $("#mobile").focus();
                return false;
            }

           if($("#paytype").val()==''){
                 show_msg("请选择支付方式!", 2000);
                 return false;
           }
            $("#button").hide();
            $("#orderAlert").show();

            show_loading();

            $("#data_form").ajaxSubmit({
                success:function(data){
                    data  =eval("(" + data +")");
                    hide_loading();
                    //console.log(data);
                    if(data.result==1){
                        show_msg("预定订单提交成功!", 2000);
                        setTimeout(function(){location.href = data.url;},2000);
                    }
                    else{
                        $("#button").show();
                        $("#orderAlert").hide();
                        show_msg(data.error, 2000);
                        return false;
                    }
                }
            });
        });
        $("#payBtn").click(function () {
            show_msg("登录名和密码不能为空", 2000);
        });
    });
</script>


    <script type="text/javascript">
        $(function(){
            var Days = <?php  echo $day;?>;
            resetDays();
            $(document).ready(function() {
                $(".datepicker").datepicker({
                    minDate:0,
                    showMonthAfterYear: true,
                    closeText:'关闭',   // 只有showButtonPanel: true才会显示出来
                    duration: 'fast',
                    showAnim:'fadeIn',
                    //showOn:'button',   // 在输入框旁边显示按钮触发，默认为：focus。还可以设置为both
                    //buttonImageOnly: true,        // 不把图标显示在按钮上，即去掉按钮
                    //buttonText:'选择日期',
                    showButtonPanel: true,
                    //showOtherMonths: true,
                    dateFormat:'yy-mm-dd'
                });
            });

            jQuery(function ($) {
                $.datepicker.regional['zh-CN'] = {
                    closeText: '关闭',
                    prevText: '<上月',
                    nextText: '下月>',
                    currentText: '今天',
                    monthNames: ['一月', '二月', '三月', '四月', '五月', '六月',
                        '七月', '八月', '九月', '十月', '十一月', '十二月'],
                    monthNamesShort: ['一', '二', '三', '四', '五', '六',
                        '七', '八', '九', '十', '十一', '十二'],
                    dayNames: ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
                    dayNamesShort: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
                    dayNamesMin: ['日', '一', '二', '三', '四', '五', '六'],
                    weekHeader: '周',
                    dateFormat: 'yy年mm月dd日',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: true,
                    yearSuffix: '年'
                };
                $.datepicker.setDefaults($.datepicker.regional['zh-CN']);
            });

            $.datepicker._gotoToday = function (id) {
                var target = $(id);
                var inst = this._getInst(target[0]);
                if (this._get(inst, 'gotoCurrent') && inst.currentDay) {
                    inst.selectedDay = inst.currentDay;
                    inst.drawMonth = inst.selectedMonth = inst.currentMonth;
                    inst.drawYear = inst.selectedYear = inst.currentYear;
                }
                else {
                    var date = new Date();
                    inst.selectedDay = date.getDate();
                    inst.drawMonth = inst.selectedMonth = date.getMonth();
                    inst.drawYear = inst.selectedYear = date.getFullYear();
                    this._setDateDatepicker(target, date);
                    this._selectDate(id, this._getDateDatepicker(target));
                }
                this._notifyChange(inst);
                this._adjustDate(target);
            }
            function resetDays() {
                var c = "#Room_Reduce", a = "num_invalid", b = "#Room_Add";
                $(b).removeClass(a);
                $(c).addClass(a);
                $("#Room_Num").text(Days);
                if (Days == 1) {
                    $(c).addClass(a);
                    $(b).removeClass(a)
                } else if (Days == 28) {
                    $(c).removeClass(a);
                    $(b).addClass(a)
                } else {
                    $(c).removeClass(a);
                    $(b).removeClass(a)
                }
            }
            $("#submitDate").click(function () {
                show_loading();
                var bdate = $(".datepicker").val().replace("年","-").replace("月","-").replace("日","");
                $.post("<?php  echo $this->createMobileUrl('ajaxData')?>",{ac:'time', bdate:bdate, day:Days, hid:'<?php  echo $hid;?>'},function(data){
                    data = eval("(" + data +")");
                    if(data.result==1){
                        location.href = data.url;
                    } else {
                        return false;
                    }
                });
            });
        });

    </script>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('share', TEMPLATE_INCLUDEPATH)) : (include template('share', TEMPLATE_INCLUDEPATH));?>

</body>
</html>