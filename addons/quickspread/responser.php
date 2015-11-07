<?php

require_once APP_PHP . 'wechatapi.php';
require_once APP_PHP . 'usermanager.php';

class QRResponser {

    private static $t_follow = 'quickspread_follow';
    private static $t_channel = 'quickspread_channel';

    function __construct() {
        
    }

    public function respondText($from_user) {
        /* 用户请求传单算法
         * 1. 获得用户uid
         * 2. 立即通知用户正在生成二维码
         * 3. 查询qr表，如果
         *   3.1 uid在qr表中不存在，则立即创建二维码，并插入qr表，然后返回信息
         *   3.2 uid在qr表中存在，则直接返回信息(第二期需要判断二维码有效时间，如果超过3天，则需要重新上传，更新media_id到qr表
         * 4. 将qr信息推送给用户
         * 5. 同步回复一个空字符串，结束本次请求
         */
        global $_W;
        WeUtility::logging('step1', '');
        $weapi = new WechatAPI();
        //$content = $this->message['content'];
        // 1. 获取uid
        // $from_user = $this->message['from'];
        // 3. 查询qr表
        $qr_mgr = new UserManager($from_user);
        $ch = $qr_mgr->getActiveChannel(); // 当前只允许一个channel，后继版本扩充，2014-12-7
        $ch = WechatUtil::decode_channel_param($ch, $ch['bgparam']);
        $channel = $ch['channel'];
        $qr = $qr_mgr->getQR($channel, $from_user);
        // 没有缓存， 或者缓存过期
        WeUtility::logging('step3', $qr['createtime'] . '<' . $ch['createtime']);
        if (empty($qr) or $qr['createtime'] < $ch['createtime']) {
            // 2. 立即通知用户
            WeUtility::logging('step3.0', $from_user);
            if (!empty($ch['genqr_info1'])) {
                $ret = $weapi->sendText($from_user, $ch['genqr_info1']);
            }
            WeUtility::logging('step3.1', '');
            // 3.1 uid在qr表中不存在，则立即创建二维码，并插入qr表，然后返回信息
            $scene_id = $qr_mgr->getNextAvaliableSceneID();
            list($media_id, $target_file_url) = $this->genImage($weapi, $scene_id, $channel, $from_user);
            if (!empty($media_id) and ! empty($ch['genqr_info2'])) {
                $ret = $weapi->sendText($from_user, $ch['genqr_info2']);
            }
            if (!empty($scene_id)) {
                WeUtility::logging('begin setQR', '');
                // 老的QR不删除，因为二维码已经生成并且发布流传，删除后其他人关注后无法发放积分
                $qr_mgr->newQR($scene_id, $target_file_url, $media_id, $channel);
                WeUtility::logging('end setQR', '');
            } else {
                $ret = $weapi->sendText($from_user, '海报已经发完，活动暂停。下一期活动即将开始，会通知给大家。谢谢！');
                exit(0);
            }
        } else {
            if (!empty($ch['genqr_info3'])) {
                $ret = $weapi->sendText($from_user, $ch['genqr_info3']);
            }
            WeUtility::logging('step3.2', '');
            // 3.2 uid在qr表中存在，则直接返回信息
            $media_id = $qr['media_id'];
            $target_file_url = $qr['qr_url'];
        }
        // 4. 将qr信息推送给用户
        WeUtility::logging('step4', $media_id);

        if (!empty($media_id)) {
            $ret = $weapi->sendImage($from_user, $media_id);
        } else {
            $ret = $weapi->sendText($from_user, "您的专属海报已生成成功,打开后长按图片保存到手机后转发到朋友圈或微信群就能赚积分换话费啦!之前保存的专属海报依然有效，直接转发即可！");
            $ret = $weapi->sendText($from_user, "<a href='$target_file_url'>【点击这里查看您的专属海报】</a>");
        }
        // 5. 同步回复一个空字符串，结束本次请求
        WeUtility::logging('step5', '');
        exit(0);
    }

    private function genImage($weapi, $scene_id, $channel, $from_user) {
        global $_W;
        $rand_file = $from_user . rand() . '.jpg';
        $att_target_file = 'qr-image-' . $rand_file;
        $att_head_cache_file = 'head-image-' . $rand_file;
        $target_file = ATTACH_DIR . $att_target_file;
        $target_file_url = $_W['attachurl'] . $att_target_file;
        $head_cache_file = ATTACH_DIR . $att_head_cache_file;
        $qr_file = $weapi->getLimitQR($scene_id);
        $ch = pdo_fetch("SELECT * FROM " . tablename(self::$t_channel) . " WHERE channel=:channel AND weid=:weid", array(":channel" => $channel, ":weid" => $_W['weid']));
        $ch = WechatUtil::decode_channel_param($ch, $ch['bgparam']);

        $enableHead = $ch['avatarenable'];
        $enableName = $ch['nameenable'];
        if (empty($ch)) {
            $ret = $weapi->sendText($from_user, "您所请求的专属海报二维码已经失效, 请联系客服人员");
            exit(0);
        } else if (empty($ch['bg'])) {
            $bg_file = APP_PHP . 'images/bg.jpg';
        } else {
            $bg_file = $_W['attachurl'] . $ch['bg'];
        }
        // 基础模式
        WeUtility::logging('step merge 1', "merge bgfile {$bg_file} and qrfile {$qr_file}");
        $this->mergeImage($bg_file, $qr_file, $target_file, array('left' => $ch['qrleft'], 'top' => $ch['qrtop'], 'width' => $ch['qrwidth'], 'height' => $ch['qrheight']));
        WeUtility::logging('step merge 1 done', '');
        // 扩展功能：昵称、图像
        if (1) {
            $fans = WechatUtil::fans_search($from_user, array('nickname', 'avatar'));
            if (!empty($fans)) {
                // 昵称
                if ($enableName) {
                    if (strlen($fans['nickname']) > 0) {
                        WeUtility::logging('step wirte text 1', $fans);
                        // $this->writeText($target_file, $target_file, '我是' . $fans['nickname'], array('size'=>30, 'left'=>150, 'top'=>50));
                        $this->writeText($target_file, $target_file, $fans['nickname'], array('size' => $ch['namesize'], 'left' => $ch['nameleft'], 'top' => $ch['nametop']));
                        WeUtility::logging('step wirte text 1 done', '');
                    }
                }
                // 头像
                if ($enableHead) {
                    if (strlen($fans['avatar']) > 10) {
                        $head_file = $fans['avatar'];
                        $head_file = preg_replace('/\/0$/i', '/96', $head_file);
                        WeUtility::logging('step merge 2', $head_file);
                        $this->mergeImage($target_file, $head_file, $target_file, array('left' => $ch['avatarleft'], 'top' => $ch['avatartop'], 'width' => $ch['avatarwidth'], 'height' => $ch['avatarheight']));
                        WeUtility::logging('step merge 2 done', '');
                        WeUtility::logging('IamInMergeFile', $target_file . $head_file);
                    } else {
                        WeUtility::logging('NoAvatarFile', $fans['avatar']);
                    }
                }
            } else {
                WeUtility::logging('NOT merge avatar and nickname', $from_user);
            }
        }
        WeUtility::logging('step upload 1', '');
        $media_id = $weapi->uploadImage($target_file);
        WeUtility::logging('step upload 1 done', '');
        WeUtility::logging('genImage', $media_id);
        if (!empty($media_id)) {
            $nowtime = time();
            pdo_query("INSERT INTO " . tablename('core_attachment') . " (uniacid,uid,filename,attachment,type,createtime) VALUES "
                    . "({$_W['weid']}, {$_W['weid']}, 'head_cache', '{$att_head_cache_file}', 1, {$nowtime}),"
                    . "({$_W['weid']}, {$_W['weid']}, 'post_cache', '{$att_target_file}', 1, {$nowtime})");
        } else { // in case 45009, api freq out of limit ;
            $ret = $weapi->sendText($from_user, "专属二维码已经生成, 点击这里:<a href='$target_file_url'>查看您的专属二维码</a>, 保存到手机后转发给好友就能拿话费!");
        }
        return array($media_id, $target_file_url);
    }

    private function imagecreate($bg) {
        $bgImg = @imagecreatefromjpeg($bg);
        if (FALSE == $bgImg) {
            $bgImg = @imagecreatefrompng($bg);
        }
        if (FALSE == $bgImg) {
            $bgImg = @imagecreatefromgif($bg);
        }
        return $bgImg;
    }

    private function mergeImage($bg, $qr, $out, $param) {
        list($bgWidth, $bgHeight) = getimagesize($bg);
        list($qrWidth, $qrHeight) = getimagesize($qr);
        extract($param);
        $bgImg = $this->imagecreate($bg);
        $qrImg = $this->imagecreate($qr);
        imagecopyresized($bgImg, $qrImg, $left, $top, 0, 0, $width, $height, $qrWidth, $qrHeight);
        ob_start();
        // output jpeg (or any other chosen) format & quality
        imagejpeg($bgImg, NULL, 100);
        $contents = ob_get_contents();
        ob_end_clean();
        imagedestroy($bgImg);
        imagedestroy($qrImg);
        $fh = fopen($out, "w+");
        fwrite($fh, $contents);
        fclose($fh);
    }

    private function writeText($bg, $out, $text, $param = array()) {
        list($bgWidth, $bgHeight) = getimagesize($bg);
        extract($param);
        $im = imagecreatefromjpeg($bg);
        $black = imagecolorallocate($im, 0, 0, 0);
        $font = APP_FONT . 'msyhbd.ttf';
        //$text = 'hello';
        $white = imagecolorallocate($im, 255, 255, 255);
        imagettftext($im, $size, 0, $left, $top + $size / 2, $white, $font, $text);
        ob_start();
        // output jpeg (or any other chosen) format & quality
        imagejpeg($im, NULL, 100);
        $contents = ob_get_contents();
        ob_end_clean();
        imagedestroy($im);
        $fh = fopen($out, "w+");
        fwrite($fh, $contents);
        fclose($fh);
    }

}
