<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>

<style>
    body {
        font-family: MicroSoft YaHei, simHei;
        background: #a87480 url('../addons/we7_shake/template/image/bg.jpg');
        background-position: center;
        background-attachment: fixed;
        margin: 0;
        padding: 0;
    }
    a {
        text-decoration: none;
    }
    .common-bg {
        filter: Alpha(opacity=70);
        background: #000;
        background: rgba(0, 0, 0, 0.7);
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    .shake {
        width: 1000px;
        margin: 0 auto;
    }
    .shake-top {
        height: 120px;
        padding-top: 20px;
        margin-bottom: 20px;
        background: url('<?php  echo tomedia($reply['logo']);?>') no-repeat 0 15px;
    }
    .shake-top-info {
        width: 630px;
        height: 100px;
    }
    .shake-topic {
        margin: 0 10px;
        color: #EEE;
        line-height: 50px;
    }
    .shake-topic .msg_tit {
        display: block;
        height: 50px;
        font-size: 32px;
        font-family: simHei;
        font-weight: bold;
        line-height: 60px;
        margin: 0;
    }
    .shake-topic .msg_cnt {
        display: block;
        height: 50px;
        font-size: 16px;
        font-weight: bold;
    }
    .shake-topic .msg_tit strong, .shake-topic .msg_cnt span {
        color: #ef2e2e;
    }
    .shake-box {
        height: 610px;
        overflow: hidden;
    }
    .shake-box > .pull-right {
        width: 630px;
        margin: 0;
    }
    .shake-box li {
        display: block;
        height: 35px;
        margin-bottom: 23px;
        border: 1px #000 solid;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    .shake-box li .img-circle {
        width: 53px;
        height: 53px;
        line-height: 70px;
        text-align: center;
        background: #e93f3f;
        border: 1px #000 solid;
        position: absolute;
        margin-top: -10px;
        margin-left: -1px;
        overflow: hidden;
    }
    .shake-box li .img-circle i {
        font-size: 40px;
        color: #FFF;
    }
    .shake-box li .shake-progress {
        display: inline-block;
        height: 20px;
        background: #fff100;
        border: 1px #000 solid;
        margin-top: 8px;
        margin-left: -5px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    .shake-box li:nth-child(even) .shake-progress {
        background: #98d110;
    }
    .shake-box li .pull-right {
        width: 70%;
    }
    .shake-box li .pull-left {
        line-height: 37px;
        height: 37px;
        width: 30%;
        overflow: hidden;
    }
    .shake-box li .shake-num {
        display: inline-block;
        font-size: 28px;
        color: #FFF;
        position: absolute;
        width: 35px;
        text-align: right;
    }
    .shake-box li .shake-name {
        display: inline-block;
        color: #FFF;
        font-size: 14px;
        width: 74%;
        padding-left: 23%;
        padding-right: 3%;
        text-align: right;
    }
    .shake-box > .pull-left {
        width: 360px;
    }
    .shake-time {
        margin: 0 auto;
        width: 200px;
        height: 200px;
        line-height: 200px;
        text-align: center;
        color: #EEE;
        font-size: 80px;
        background: rgba(0, 0, 0, 0.7);
        border: 8px #EEE solid;
        margin-top: 100px;
    }
    .shake-qrcode {
        display: block;
        width: 320px;
        height: 320px;
        margin: 30px auto 0 auto;
    }
    .shake-pic {
        width: 360px;
        height: 420px;
        margin-bottom: 10px;
        overflow: hidden;
    }
    .shake-info {
        color: #FFF;
        font-size: 20px;
        padding: 10px;
    }
    .shake-avatar {
        width: 80px;
        float: left;
        margin: 5px;
    }
</style>

<div class="shake">
    <div class="shake-top">
        <div class="shake-top-info common-bg pull-right">
            <div class="shake-topic">
                <h1 class="msg_tit" style="display: block;">搜索公众号 <strong><?php  echo $_W['account']['name'];?></strong></h1>
                <h1 class="msg_tit" style="display: none;">添加公众号 <strong></strong></h1>
                <span class="msg_cnt">发送 <?php  if(is_array($reply['keyword'])) { foreach($reply['keyword'] as $row) { ?>
                    <span class="red Topic_cnt"><?php  echo $row['content'];?></span>，<?php  } } ?> 登记后进入摇一摇界面
                </span>
            </div>
        </div>
    </div>
    <div class="shake-box">
        <?php  if($reply['status'] == 0) { ?>
        <div class="alert alert-warning text-center" style="font-size:20px;" role="alert">已经有
            <span id="container-total"><?php  echo $total;?></span> 个用户加入摇一摇
        </div>
        <div class="pull-left" style="text-align:center;">
            <div class="shake-time img-circle" id="description" style="display:none;"><?php  echo $reply['countdown'];?></div>
            <img class="img-rounded shake-qrcode" src="<?php  echo tomedia($reply['qrcode']);?>">
            <div class="shake-button btn btn-warning btn-lg" onclick="start()" style="width:300px; margin-top:60px;">
                开始
            </div>
        </div>
        <div class="pull-right" id="container-join">
            <?php  if(is_array($list)) { foreach($list as $row) { ?>
            <img id="head-<?php  echo $row['openid'];?>" class="img-circle shake-avatar" src="<?php  echo $fans[$row['openid']]['avatar'];?>">
            <?php  } } ?>
        </div>
        <?php  } else { ?>
        <!-- 开始 -->
        <div class="pull-left">
            <?php  if($reply['background']) { ?>
            <div class="shake-pic img-rounded">
                <img src="<?php  echo tomedia($reply['background']);?>" style="width:360px;">
            </div>
            <?php  } ?>
            <?php  if($reply['rule']) { ?>
            <div class="shake-info common-bg">
                <?php  echo $reply['rule'];?>
            </div>
            <?php  } ?>
        </div>
        <ul class="pull-right" id="shakecount-list">
            <?php  $i = 1;?>
            <?php  if(is_array($list)) { foreach($list as $item) { ?>
            <li id="shakecount-<?php  echo $item['openid'];?>" class="common-bg <?php  if($i%2==0) { ?>even<?php  } ?>">
                <div class="pull-left">
                    <img class="img-circle shake-avatar" style="margin-left:-45px;"
                         src="<?php  echo $fans[$item['openid']]['avatar'];?>">
                    <span class="shake-num"><?php  echo $i++;?></span>
                    <span class="shake-name"><?php  echo $fans[$item['openid']]['nickname'];?></span>
                </div>
                <div class="pull-right">
                    <span class="shake-progress" style="<?php  if($item['shakecount']) { ?>width:<?php  echo round($item['shakecount'] / $reply['maxshake'], 4) * 100?>%;<?php  } else { ?>width:0%;<?php  } ?>"></span>
                </div>
            </li>
            <?php  } } ?>
        </ul>
        <?php  } ?>
    </div>
</div>

<?php  if($reply['status'] == 0) { ?>
<script type="text/javascript">
    var countdown = <?php echo $reply['countdown'] ? $reply['countdown'] : 10?>;
    var lastupdatetime = '<?php  echo $lastupdatetime;?>';
    function start() {
        $('.shake-qrcode').hide();
        $('#description').show().html(countdown--);
        if (countdown <= 0) {
            //更新摇一摇状态
            $.post('<?php  echo $this->createWebUrl('changestatus', array('id' => $reply['rid'], 'status' => 1))?>', function () {location.reload();});
            return false;
        } else {
            setTimeout(function () {
                start();
            }, 1000);
        }
    }
    function getJoin() {
        $.getJSON('<?php  echo $this->createWebUrl('getjoin', array('rid' => $reply['rid']))?>', {'lastupdatetime': '1417342323'}, function (s) {
            if (s.message) {
                for (item in s.message.list) {
                    if (!$('#head-' + s.message.list[item].openid).size()) {
                        $('#container-join').prepend('<img id="head-' + s.message.list[item].openid + '" class="img-circle shake-avatar" src="' + s.message.list[item].avatar + '">');
                    }
                }
                $('#container-total').html(s.message.total);
            }
            setTimeout(function () {
                getJoin();
            }, 3000);
        });
    }

    setTimeout(function () {
        getJoin();
    }, 5000);

</script>

<?php  } else if($reply['status'] == 1) { ?>

<script type="text/javascript">
    function refresh() {
        $.getJSON('<?php  echo $this->createWebUrl('getrank', array('id' => $reply['id'], 'weid' => $_W['uniacid']))?>', function (s) {
            if (s.message.status == 1) {
                var html = '';
                var avatar;
                var progress, num = 1, beforeitem = '';
                for (i in s.message.message) {
                    $('#shakecount-' + s.message.message[i].openid).removeClass('even');
                    progress = Math.round(s.message.message[i].shakecount / <?php  echo $reply['maxshake'];?> * 10000) / 100;
                    $('#shakecount-' + s.message.message[i].openid).find('.shake-progress').css('width', progress + '%');
                    $('#shakecount-' + s.message.message[i].openid).find('.shake-num').html(num);
                    if (num == 1) {
                        $('#shakecount-list').prepend($('#shakecount-' + s.message.message[i].openid));
                        beforeitem = s.message.message[i].openid;
                    } else {
                        $('#shakecount-' + s.message.message[i].openid).insertAfter($('#shakecount-' + beforeitem));
                        beforeitem = s.message.message[i].openid;
                    }
                    if (num % 2 == 0) {
                        $('#shakecount-' + s.message.message[i].openid).addClass('even');
                    }
                    num = num + 1;
                }
            } else {
                if (s.redirect) {
                    location.href = s.redirect;
                }
                return false;
            }
            setTimeout(function () {
                refresh();
            }, 1000);
        });
    }

    $(function () {
        refresh();
    });

</script>

<?php  } ?>

</body>
</html>