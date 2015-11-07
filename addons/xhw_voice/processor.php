<?php
/**
 * 好声音模块微站定义
 *

 * @url http://www.qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

class Xhw_voiceModuleProcessor extends WeModuleProcessor {

    public function respond() {
        global $_W;


        if (!$this->inContext && $this->message['type'] == "voice") {
            $this->beginContext(600);
            $_SESSION['mediaid'] = $this->message['mediaid'];
            $_SESSION['ok'] = 1;
            return $this->respText("欢迎参加好声音投稿,刚才这首自己还满意吗？

很满意:发一张照片来作为封面

不满意:如需重唱请回复c");
        } elseif (!$this->inContext && is_numeric($this->message['content']) && empty($_SESSION['pp'])) {
            $this->beginContext(60); //锁定60秒
            $_SESSION['pp'] = 1;
            $_SESSION['content'] = $this->message['content'];
            $_SESSION['rand'] = random(4, true);
            return $this->respText("为防止恶意刷票，请回复验证码：" . $_SESSION['rand']);
        } elseif ($_SESSION['pp'] == 1) {
            if ($this->message['content'] != $_SESSION['rand']) {
                return $this->respText("验证码错误，请重新回复验证码：" . $_SESSION['rand']);
            } else {
                global $_W;
                $rid = $this->rule;
                $openid = $this->message['from'];
                $arr = pdo_fetch("SELECT * FROM " . tablename('xhw_voice') . " WHERE rid = :rid", array(':rid' => $rid));
                $rid = $arr['id'];
                $id = $arr['id'];
                $mynum = $arr['mynum'];
                $day = $arr['day'];
                if ($arr['starttime'] - time() > 0) {
                    return $this->respText("您好，活动将在" . date("Y-m-d H:i:s", $arr['starttime']) . "时开放投票");
                } elseif ($arr['endtime'] - time() < 0) {
                    return $this->respText("您好，活动已经于" . date("Y-m-d H:i:s", $arr['endtime']) . "时结束");
                }
                $numid = $_SESSION['content'];
                $this->endContext();
                $arr = pdo_fetch("SELECT * FROM " . tablename('xhw_voice_reg') . " WHERE id = :id", array(':id' => $numid));
                if (empty($arr)) {
                    return $this->respText("好像没有这个人哦，请核对ID号是否有误，去活动首页找找吧");
                }
                if ($arr['pass'] != '1') {
                    return $this->respText("您投票的用户还未通过审核，请稍后再试!要不去活动首页给其他人投票吧");
                }
                $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                if ($day) {
                    $today = 1;
                }
                $data = array(':openid' => $openid, ':rid' => $rid, ':numid' => $numid, ':time' => $today);
                $arr = pdo_fetch("SELECT * FROM " . tablename('xhw_voice_log') . " WHERE openid = :openid AND rid = :rid AND numid = :numid AND time > :time", $data);
                if (!empty($arr)) {
                    if ($day) {
                        return $this->respText("您已经投过了，只能为同一个投票一次!");
                    }
                    return $this->respText("您今天已经帮TA投过啦，明天再来给TA投票吧!");
                }
                $data = array(':openid' => $openid, ':rid' => $rid, ':time' => $today);
                $mylognum = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('xhw_voice_log') . " WHERE  openid = :openid AND rid = :rid AND time > :time", $data);
                if ($mynum == "0") {
                    $mynum = "100000";
                }
                if ($mylognum >= $mynum) {
                    return $this->respText("您今天已达投票上限,明天再来吧!");
                }

                $data = array('rid' => $rid, 'openid' => $openid, 'numid' => $numid, 'time' => time());
                pdo_insert(xhw_voice_log, $data);
                $arr = pdo_fetch("SELECT * FROM " . tablename('xhw_voice_reg') . " WHERE id = :id", array(':id' => $numid));
                $num = intval($arr['num']) + 1;
                $data = array('num' => $num);
                pdo_update('xhw_voice_reg', $data, array('id' => $numid));
                return $this->respText("您已经成功为 " . $numid . "号 " . $arr['nickname'] . " 投了一票!");
            }
        } elseif (!$this->inContext) {//关键词触发
            global $_W;
            $rid = $this->rule;
            $fromuser = $this->message['from'];
            if ($rid) {
                $reply = pdo_fetch("SELECT * FROM " . tablename('xhw_voice') . " WHERE rid = :rid", array(':rid' => $rid));
                if ($reply) {
                    $news = array();
                    $news[] = array(
                        'title' => $reply['title'],
                        'description' => $reply['smalltext'],
                        'picurl' => $reply['photo'],
                        'url' => $this->createMobileUrl('index', array('id' => $reply['id'])),
                    );
                    return $this->respNews($news);
                }
            }
            return null;
        } elseif ($_SESSION['ok'] == 1) {
            if ($this->message['type'] == "image") {
                global $_W, $_GPC;
                $mediaid = $_SESSION['mediaid'];
                $openid = $this->message['from'];
                $nickname = $_W['fans']['nickname'];
                //$avatar=$_W['fans']['avatar'];
                $avatar = $this->message['picurl'];
                pdo_insert('xhw_voice_reg', array(
                    'weid' => $_W['uniacid'],
                    'mediaid' => $mediaid,
                    'openid' => $openid,
                    'nickname' => $nickname,
                    'avatar' => $avatar,
                ));

                $arr = pdo_fetch("SELECT * FROM " . tablename('xhw_voice_reg') . " WHERE mediaid = :mediaid", array(':mediaid' => $mediaid));
                $id = $arr['id'];
                //$id = pdo_insertid();
                //返回数据
                $this->endContext();
                $news = array();
                $news[] = array(
                    'title' => "请填写资料完成报名",
                    'description' => "还差最后一步,点击这里填写报名",
                    'picurl' => "../addons/xhw_voice/logo.jpg",
                    'url' => $this->createmobileUrl('reg', array('do' => 'reg', 'id' => $id)),
                );
                return $this->respNews($news);
                // return $this->respText("还差最后一步 ==》<a href='".$this->createmobileUrl('reg',array('do'=>'reg', 'id'=>$id))."'>点击这里</a>");  
            } elseif ($this->message['content'] == "c") {
                $_SESSION['ok'] = 2;
                return $this->respText("重新录制一首歌发给我吧");
            } else {
                return $this->respText("没问题请直接上传头像作为封面，如需重唱请回复c");
            }
        } else {
            if ($this->message['type'] == "voice") {
                $_SESSION['mediaid'] = $this->message['mediaid'];
                $_SESSION['ok'] = 1;
                return $this->respText("我们已经收到您的语音，刚才这首还满意吗？

很满意:发一张照片来作为封面

不满意:如需重唱请回复c");
            } else {
                return $this->respText("只能发送语音消息");
            }
        }
    }

}