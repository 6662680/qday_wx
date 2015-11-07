<?php

/**
 * 摇一摇拔河模块
 *
 */
defined('IN_IA') or exit('Access Denied');

class hl_tugModule extends WeModule {

    public $tablename = 'hl_tug_reply';

    public function fieldsFormDisplay($rid = 0) {
        global $_W;
        load()->func('tpl');
        if (!empty($rid)) {
            $reply = pdo_fetch("SELECT * FROM " . tablename($this->tablename) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
        } else {
            $reply = array(
                'teama' => '红队',
                'teamb' => '蓝队',
                'joinlimit' => 100,
                'timelimit'=>90
            );
        }
        include $this->template('form');
    }

    public function fieldsFormValidate($rid = 0) {
        return true;
    }

    public function fieldsFormSubmit($rid = 0) {
        global $_GPC, $_W;
        $id = intval($_GPC['reply_id']);
        $insert = array(
            'rid' => $rid,
            'title' => $_GPC['title'],
            'joinlimit' => intval($_GPC['joinlimit']),
            'timelimit' => intval($_GPC['timelimit']),
            'picture' => $_GPC['picture'],
            'weid' => $_W['uniacid'],
            'teama' => $_GPC['teama'],
            'teamapic' => $_GPC['teamapic'],
            'teamb' => $_GPC['teamb'],
            'teambpic' => $_GPC['teambpic'],
            'ad1' => $_GPC['ad1'],
            'ad2' => $_GPC['ad2'],
            'ad3' => $_GPC['ad3'],
            'ad4' => $_GPC['ad4'],
            'description' => $_GPC['description'],
            'rule' => htmlspecialchars_decode($_GPC['rule']),
            'status' => intval($_GPC['statuss']),
        );

        if (empty($id)) {
            pdo_insert($this->tablename, $insert);
        } else {

            pdo_update($this->tablename, $insert, array('id' => $id));
        }
    }

    public function ruleDeleted($rid = 0) {

        return true;
    }

    public function settingsDisplay($settings) {
        global $_GPC, $_W;
        if (checksubmit()) {
            $cfg = array();
            $cfg['appid'] = $_GPC['appid'];
            $cfg['secret'] = $_GPC['secret'];
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
        load()->func('tpl');
        include $this->template('setting');
    }

}
