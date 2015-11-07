<?php

defined('IN_IA') or exit('Access Denied');

require IA_ROOT . '/addons/quickspread/define.php';
require APP_PHP . 'wechatutil.php';
require APP_PHP . 'wechatapi.php';
require APP_PHP . 'usermanager.php';
require_once APP_PHP . 'responser.php';

class QuickSpreadModuleProcessor extends WeModuleProcessor {

    private static $t_follow = 'quickspread_follow';
    private static $t_channel = 'quickspread_channel';

    public function respond() {
        WeUtility::logging('message xx', json_encode($this->message));
        $this->refreshUserInfo($this->message['from']);
        WeUtility::logging("Processor:SUBSCRIBE", $this->message);
        //return $this->respText('已经关注');
        if ($this->message['msgtype'] == 'text' ||
                ($this->message['msgtype'] == 'event' and $this->message['event'] == 'CLICK')) {
            $resp = $this->respondText();
        } else if ($this->message['msgtype'] == 'event') {
            if ($this->message['event'] == 'unsubscribe') {
                return $this->responseEmptyMsg();
            } else if ($this->message['event'] == 'subscribe') {
                $scene_id = $this->message['eventkey'];
                WeUtility::logging("Processor:SUBSCRIBE", $scene_id);
                $resp = $this->respondSubscribe();
                WeUtility::logging("Processor:SUBSCRIBE done", $scene_id);
            } elseif ($this->message['event'] == 'SCAN') {
                $scene_id = $this->message['eventkey'];
                WeUtility::logging("Processor:SCAN", $scene_id);
                $resp = $this->respondScan();
                WeUtility::logging("Processor:SCAN done", $scene_id);
            }
        }
        return $resp;
    }

    private function respondText() {
        global $_W;
        // start a reponser thread using curl
        $url = $_W['siteroot'] . WechatUtil::createMobileUrl('RunTask', $this->modulename, array('from_user' => $this->message['from']));

        $ret = WechatUtil::http_request($url, null, null, 4000);
        WeUtility::logging("Running task", $url . "==>" . json_encode($ret));
        // responseEmptyMsg and exit
        return $this->responseEmptyMsg();
    }

    private function responseEmptyMsg() {
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        exit(0);
    }

    private function refreshUserInfo($from_user) {
        $follower = $from_user;
        $qr_mgr = new UserManager('');
        $userInfo = $qr_mgr->getUserInfo($follower);
        if (empty($userInfo) || empty($userInfo['nickname']) || empty($userInfo['avatar'])) {
            $weapi = new WechatAPI();
            $userInfo = $weapi->getUserInfo($follower);
            $qr_mgr->saveUserInfo($userInfo);
        }
        WeUtility::logging('refresh', $userInfo);
    }

    private function respondScan() {
        $scene_id = $this->message['eventkey'];
        if (empty($scene_id)) {
            WeUtility::logging('subscribe', 'no scene id');
            return $this->respText('欢迎关注微信号!');
        }
        // 2. 读取qr表，找到分享者uid，channel
        WeUtility::logging('getQRByScene', $scene_id);
        $qr_mgr = new UserManager('');
        $qr = $qr_mgr->getQRByScene($scene_id);
        if (empty($qr)) {
            return $this->respText('您好,已经关注');
        }
        $channel = $qr_mgr->getChannel($qr['channel']);
        if (empty($channel)) {
            return $this->respText('欢迎回来，您已经关注');
        }
        $response = array();
        $userInfo = $qr_mgr->getUserInfo($this->message['from']);
        $channel['title'] = preg_replace('/\[nickname\]/', $userInfo['nickname'], $channel['title']);
        $channel['desc'] = preg_replace('/\[nickname\]/', $userInfo['nickname'], $channel['desc']);
        $response[] = array(
            'title' => $channel['title'],
            'description' => $channel['desc'],
            'picurl' => tomedia( $channel['thumb'] ),
            'url' => $this->buildSiteUrl($channel['url'])
        );
        return $this->respNews($response);
        //return $this->respText('已经关注');
    }

    private function respondSubscribe() {
        /* 有新用户通过二维码订阅本账号, 处理流程如下：
         * 1. 判断是否设置scene id，如果没有设置则直接回复默认消息，如果设置了scene id，则读取scene id
         * 2. 读取qr表，找到分享者uid，channel
         * 3. 将本次引流事件记录到follow表
         * 4. 推送channel指定消息给用户
         */
        $follower = $this->message['from'];
        list($dummy, $scene_id) = explode('_', $this->message['eventkey']);
        /* 记录用户的基本信息:图像，昵称，地址 */
        $qr_mgr = new UserManager('');
        $weapi = new WechatAPI();
        $userInfo = $weapi->getUserInfo($follower);
        $qr_mgr->saveUserInfo($userInfo);

        if (empty($scene_id)) {
            WeUtility::logging('subscribe', 'no scene id');
            return $this->respText('欢迎关注微信号!');
        }
        // 2. 读取qr表，找到分享者uid，channel
        WeUtility::logging('getQRByScene', $scene_id);
        $qr = $qr_mgr->getQRByScene($scene_id);
        if (empty($qr)) {
            WeUtility::logging('subscribe', 'qr not found using scene ' . $scene_id);
            return $this->respText('欢迎关注微信号!');
        }

        // 3. 将本次引流事件记录到follow表
        $leader = $qr['from_user'];
        $qr_mgr = new UserManager($leader);
        // 4. 推送channel指定消息给用户
        $channel = $qr_mgr->getChannel($qr['channel']);
        if (empty($channel)) {
            WeUtility::logging('subscribe', 'channel not found using channel ' . $qr['channel']);
            return $this->respText('欢迎关注微信号!');
        }
        if ($qr_mgr->isNewUser($follower)) {
            WeUtility::logging('record followship', $qr);
            $qr_mgr->processSubscribe($follower, $qr['channel']);
            /* 最后，给上线发一个通知 */
            $this->notifyUpLevel($weapi, $leader);
            $this->notifyLeader($weapi, $leader, $follower);
        } else {
            $this->notifyLeaderScanEvent($weapi, $leader, $follower);
        }
        $response = array();

        $channel['title'] = preg_replace('/\[nickname\]/', $userInfo['nickname'], $channel['title']);
        $channel['desc'] = preg_replace('/\[nickname\]/', $userInfo['nickname'], $channel['desc']);
        $response[] = array(
            'title' => $channel['title'],
            'description' => $channel['desc'],
            'picurl' => tomedia( $channel['thumb'] ),
            'url' => $this->buildSiteUrl($channel['url'])
        );

        return $this->respNews($response);
    }

    private function notifyLeader($weapi, $leader, $follower) {
        global $_W;
        WeUtility::logging('notifyLeader begin', $leader);
        $follower_fans = WechatUtil::fans_search($follower, array('nickname'));
        if (!empty($leader)) {
            $weapi->sendText($leader, '通过您的努力，您的朋友' . $follower_fans['nickname'] . '成为了您忠诚的支持者，您也获得了相应的积分奖励，请注意查收！');
        }
        WeUtility::logging('notifyLeader end', $leader);
    }

    // 下线多次扫码该二维码，除首次外，均调用本接口通知上线有扫码动作，制造繁荣景象。
    // 本扫码不加分
    private function notifyLeaderScanEvent($weapi, $leader, $follower) {
        global $_W;
        WeUtility::logging('notifyLeaderScanEvent begin', $leader);
        $follower_fans = WechatUtil::fans_search($follower, array('nickname'));
        if (!empty($leader)) {
            $weapi->sendText($leader, '通过您的努力，您的朋友' . $follower_fans['nickname'] . '又通过你发出的二维码登陆本站啦！');
        }
        WeUtility::logging('notifyLeaderScanEvent end', $leader);
    }

    private function notifyUpLevel($weapi, $this_level_openid) {
        global $_W;
        WeUtility::logging('notifyUpLevel begin', $this_level_openid);
        $uplevel = pdo_fetch("SELECT * FROM " . tablename(self::$t_follow) . " WHERE weid=:weid AND follower=:follower", array(":weid" => $_W['weid'], ":follower" => $this_level_openid));
        WeUtility::logging('notifyUpLevel begin2', $this_level_openid);
        if (!empty($uplevel)) {
            $fans = WechatUtil::fans_search($this_level_openid, array('nickname'));
            WeUtility::logging('notifyUpLevel sendText begin', $uplevel['leader']);
            $weapi->sendText($uplevel['leader'], '您的朋友' . $fans['nickname'] . '又获得了一个新的支持者，您也得到了相应积分奖励，请注意查收!');
        }
        WeUtility::logging('notifyUpLevel', $uplevel);
    }

}

/* end class */

