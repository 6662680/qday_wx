<?php

defined('IN_IA') or exit('Access Denied');
class wdl_wifiModuleProcessor extends WeModuleProcessor {

    public $table_reply = 'wdl_wifi_reply';
    public $table_authlist = 'wdl_wifi_authentication';
    public $get_auth_url = 'http://wx.rippletek.com/Portal/Wx/get_auth_url';
    public $get_auth_token = 'http://wx.rippletek.com/Portal/Wx/get_auth_token';
    public $retrieve_node = 'https://api.authcat.org/node_api/retrieve_node';
    
    public function respond() {
        global $_W;
        $rid = $this->rule;
        $uniacid = $_W['uniacid'];
        $sql = "SELECT * FROM " . tablename($this->table_reply) . " WHERE `rid`=:rid LIMIT 1";
        $row = pdo_fetch($sql, array(
            ':rid' => $rid
        ));
        $routerid = intval($row['routerid']);
        if (empty($routerid)) {
            return $this->respText("请确认您操作的路由器已经维护");
        }
        $rowrouter = $this->getnode_info($routerid);
        if (empty($rowrouter)) {
            return $this->respText("指定关联节点不存在！");
        }
        if ($rowrouter['is_portal'] == false) {
            return $this->respText("该该路由器未启用认证，请后台设置为启用！");
        }
        $openid = $this->message['from'];
        $auth_url = $this->get_authurl($routerid,$openid);

        if ($rowrouter['wx_phone_only'] != true) {
            $auth_token = $this->get_authtoken($routerid,$openid);
        }
        $authdata = array(
          'uniacid' => $uniacid,
          'routerid' => $routerid,
          'fromuser' => $openid,
          'createtime' => time(),
          );
        if ($auth_url['status'] == 0 || $auth_token['status'] == 0 ) //接受认证
        {
            $urlText = "<a href='{$auth_url['auth_url']}' >直接点击</a>";
            $row['oktip'] = str_replace('{url}', $urlText, $row['oktip']);
            $row['oktip'] = str_replace('{key}', $auth_token['auth_token'], $row['oktip']);
            $authdata['result'] = 1;
            $authdata['resultmemo'] = '认证链接:' . $auth_url['auth_url'] . ' 验证码:' . $auth_token['auth_token'];
            pdo_insert($this->table_authlist, $authdata);
            return $this->respText($row['oktip']);
        } else {
            $authdata['result'] = 0;
            $authdata['resultmemo'] = $auth_url['err_msg'].$auth_token['err_msg'];
            pdo_insert($this->table_authlist, $authdata);
            return $this->respText($auth_url['err_msg'].$auth_token['err_msg']);
        }
    }
    
    public function getnode_info($node) {
        $node = intval($node);
        $data = array(
            'api_id' => $this->module['config']['nodeid'],
            'api_key' => $this->module['config']['nodekey'],
            'node' => $node,
        );
        $item = ihttp_post($this->retrieve_node, json_encode($data));
        $item = json_decode($item['content'], true);
        return $item;
    }

    public function get_authurl($node,$openid){
      $data = array(
        'api_id' => $this->module['config']['authid'],
        'api_key' => $this->module['config']['authkey'],
        'node' => $node,
        'openid' => $openid,
            );
      $auth_url = ihttp_post($this->get_auth_url, json_encode($data));
      $auth_url = json_decode($auth_url['content'],true);
      return $auth_url;
    }

    public function get_authtoken($node,$openid){
      $data = array(
        'api_id' => $this->module['config']['authid'],
        'api_key' => $this->module['config']['authkey'],
        'node' => $node,
        'openid' => $openid,
            );
      $auth_token = ihttp_post($this->get_auth_token, json_encode($data));
      $auth_token = json_decode($auth_token['content'],true);
      return $auth_token;
    }
}

?>