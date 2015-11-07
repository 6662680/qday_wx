<?php

/**
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Hx_alertModuleSite extends WeModuleSite {

    public $reply = 'hx_alert_reply';
    public $list = 'hx_alert_list';

    public function getHomeTiles() {
        global $_W;
        $urls = array();
        $list = pdo_fetchall("SELECT name, id FROM " . tablename('rule') . " WHERE uniacid = '{$_W['uniacid']}' AND module = 'hx_alert'");
        if (!empty($list)) {
            foreach ($list as $row) {
                $urls[] = array('title' => $row['name'], 'url' => $this->createMobileUrl('index', array('id' => $row['id'])));
            }
        }
        return $urls;
    }

    public function doMobileindex() {
        global $_GPC, $_W;
        $id = intval($_GPC['rid']);
        $reply = pdo_fetch("SELECT * FROM ".tablename($this->reply). "WHERE rid = '{$id}'");
        if (empty($reply)) {
            message('抱歉，非法访问');
        }
        //print_r($reply);
        include $this->template('index');
    }

    public function doMobileget() {
        global $_GPC, $_W;
        $insert = array(
            'rid' => intval($_GPC['rid']),
            'uniacid' => $_W['uniacid'],
            'title' => $_GPC['title'],
            'loops' => intval($_GPC['loop']),
            'items' => $_GPC['items'],
            'createtime' => time(),
            );
        pdo_insert($this->list,$insert);
        $id = pdo_insertid();
        exit(json_encode(array('state'=>'ok','mid'=>$id)));
    }

    public function doMobilealert() {
        global $_GPC, $_W;
        $id = intval($_GPC['mid']);
        $list = pdo_fetch("SELECT * FROM ".tablename($this->list). "WHERE id = '{$id}'");
        $reply = pdo_fetch("SELECT * FROM ".tablename($this->reply). "WHERE rid = '{$list['rid']}'");
        if (empty($list)) {
            message('抱歉，您参与的游戏已结束',$this->createMobileUrl('index'),'error');
        }
        include $this->template('alert');
    }

}
