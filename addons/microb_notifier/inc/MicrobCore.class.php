<?php
/**
 * 微小店辅助工具
 */
defined('IN_IA') or exit('Access Denied');

class MicrobCore {
    private $account = null;

    public function __construct($account) {
        $this->account = $account;
    }

    public function submitNotify($openid, $trade) {
        global $_W;

        $sql = "SELECT * FROM " . tablename('mb_store_notifies') . ' WHERE `type`=:type AND `uniacid`=:uniacid';
        $pars = array();
        $pars[':type'] = 'submit';
        $pars[':uniacid'] = $_W['weid'];
        $setting = pdo_fetch($sql, $pars);
        if(empty($setting)) {
            return;
        }

        $caption = str_replace(array('{nickname}'), array($trade['buyer']), $setting['caption']);
        $remark = str_replace(array('{nickname}'), array($trade['buyer']), $setting['remark']);

        $params = array();
        $params['touser'] = $openid;
        $params['template_id'] = $setting['template'];
        $params['url'] = '';
        $params['topcolor'] = '#ff0000';
        $params['data']['first']            = array('value' => $caption, `color` => '#173177');
        $params['data']['orderMoneySum']    = array('value' => sprintf('%.2f', $trade['details'][0]['price']), `color` => '#173177');
        $params['data']['orderProductName'] = array('value' => $trade['details'][0]['title'] . "\n", `color` => '#173177');
        $params['data']['Remark']           = array('value' => $remark, `color` => '#173177');
        return $content = $this->notify($params);
    }

    private function notify($params) {
        $access = $this->fetchAccess();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access;
        $params = json_encode($params);
        $content = ihttp_post($url, $params);
        if(is_error($content)) {
            return $content;
        } else {
            return @json_decode($content['content'], true);
        }
    }

    private function fetchUserInfo($openid) {
        $token = $this->fetchAccess();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$token}&openid={$openid}&lang=zh_CN";
        $response = ihttp_get($url);
        if(is_error($response)) {
            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
        }
        $result = @json_decode($response['content'], true);
        if(empty($result)) {
            return error(-2, "接口调用失败, 错误信息: {$response}");
        } elseif (!empty($result['errcode'])) {
            return error(-3, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']}");
        }
        $ret = array();
        $ret['nickname']        = $result['nickname'];
        $ret['gender']          = $result['sex'];
        $ret['residecity']      = $result['city'];
        $ret['resideprovince']  = $result['province'];
        $ret['avatar']          = $result['headimgurl'];
        if(!empty($ret['avatar'])) {
            $ret['avatar'] = rtrim($ret['avatar'], '0');
            $ret['avatar'] .= '132';
        }
        $ret['original'] = $result;
        return $ret;
    }

    public function fetchAccess($force = false) {
        global $_W;
        if($force) {
            $row = array();
            $row['access_token'] = '';
            pdo_update('account_wechats', $row, array('acid' => $_W['acid']));
        }
        load()->classs('weixin.account');
        $accObj= WeixinAccount::create($this->account['acid']);
        return $accObj->fetch_token();
    }
}
