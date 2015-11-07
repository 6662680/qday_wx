<?php

/**
 * 微ktv
 *
 * @url
 */
defined('IN_IA') or exit('Access Denied');

include "../addons/ewei_ktv/model.php";

class Ewei_ktvModule extends WeModule {

    public $_img_url = '../addons/ewei_ktv/template/style/img/';
    public $_css_url = '../addons/ewei_ktv/template/style/css/';
    public $_script_url = '../addons/ewei_ktv/template/style/js/';
    public $_ktv_level_config = array(5 => '五星级ktv', 4 => '四星级ktv', 3 => '三星级ktv', 2 => '两星级以下', 15 => '豪华ktv', 14 => '高档ktv', 13 => '舒适ktv', 12 => '经济型ktv',);

    public function fieldsFormDisplay($rid = 0) {
        global $_W;
        if ($rid) {
            $reply = pdo_fetch("SELECT * FROM " . tablename('ktv2_reply') . " WHERE weid=:weid and rid = :rid limit 1", array(':weid' => $_W['uniacid'], ':rid' => $rid));
            $sql = 'SELECT id,title,description,thumb FROM ' . tablename('ktv2') . ' WHERE `weid`=:weid AND `id`=:ktvid';
            $ktv = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':ktvid' => $reply['ktvid']));
        }
        include $this->template('form');
    }
  public function settingsDisplay($settings) {
        global $_GPC, $_W;
        if (checksubmit()) {
            if (empty($_GPC['sendmail']) || empty($_GPC['senduser']) || empty($_GPC['sendpwd'])) {
                message('请完整填写邮件配置信息', 'refresh', 'error');
            }
            if ($_GPC['host'] == 'smtp.qq.com' || $_GPC['host'] == 'smtp.gmail.com') {
                $secure = 'ssl';
                $port = '465';
            } else {
                $secure = 'tls';
                $port = '25';
            }
            $result = $this->sendmail($_GPC['host'], $secure, $port, $_GPC['sendmail'], $_GPC['senduser'], $_GPC['sendpwd'], $_GPC['sendmail']);
            $cfg = array(
                'host' => $_GPC['host'],
                'secure' => $secure,
                'port' => $port,
                'sendmail' => $_GPC['sendmail'],
                'senduser' => $_GPC['senduser'],
                'sendpwd' => $_GPC['sendpwd'],
                'status' => $result
            );
            if ($result == 1) {
                $this->saveSettings($cfg);
                message('邮箱配置成功', 'refresh');
            } else {
                message('邮箱配置信息有误', 'refresh', 'error');
            }
        }
        include $this->template('setting');
    }
    
    public function fieldsFormValidate($rid = 0) {
        global $_GPC;
        $ktvid = intval($_GPC['ktv']);
        if ($ktvid) {
            $sql = 'SELECT * FROM ' . tablename('ktv2') . " WHERE `id`=:ktvid";
            $params = array();
            $params[':ktvid'] = $ktvid;
            $ktv = pdo_fetch($sql, $params);
            if (!empty($ktv)) {
                return '';
            }
        }
        return '没有选择合适的ktv';
    }

    public function fieldsFormSubmit($rid) {
        global $_GPC, $_W;
        $ktvid = intval($_GPC['ktv']);
        $record = array();
        $record['ktvid'] = $ktvid;
        $record['rid'] = $rid;
        $record['weid'] = $_W['uniacid'];
        $reply = pdo_fetch("SELECT * FROM " . tablename('ktv2_reply') . " WHERE rid = :rid limit 1", array(':rid' => $rid));
        if ($reply) {
            pdo_update('ktv2_reply', $record, array('id' => $reply['id']));
        } else {
            pdo_insert('ktv2_reply', $record);
        }
    }

    public function ruleDeleted($rid) {
        pdo_delete('ktv2_reply', array('rid' => $rid));
    }

 

}
