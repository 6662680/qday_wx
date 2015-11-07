<?php

/**
 * 送粽子模块
 *
 * [天蓝创想] www.v0591.com 5517286@qq.com
 */
defined('IN_IA') or exit('Access Denied');

class Hl_zzzModule extends WeModule {

    public $tablename = 'zzz_reply';

    public function fieldsFormDisplay($rid = 0) {
        global $_W;
        if (!empty($rid)) {
            $reply = pdo_fetch("SELECT * FROM " . tablename($this->tablename) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
        } else {
            $reply = array(
                'periodlottery' => 1,
                'maxlottery' => 5,
                'prace_times' => 10,
                'start_time' => TIMESTAMP,
                'end_time' => TIMESTAMP + 3600 * 24 * 7,
                'smallunit' => '冰花',
                'bigunit' => '杯',
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
            'picture' => $_GPC['picture'],
            'title' => $_GPC['title'],
            'prace_times' => intval($_GPC['prace_times']),
            'description' => $_GPC['description'],
            'smallunit' => $_GPC['smallunit'],
            'bigunit' => $_GPC['bigunit'],
            'bgurl' => $_GPC['bgurl'],
            'periodlottery' => 1,
            'maxlottery' => intval($_GPC['maxlottery']),
            'rule' => htmlspecialchars_decode($_GPC['rule']),
            'start_time' => strtotime($_GPC['start_time']),
            'end_time' => strtotime($_GPC['end_time']),
            'guzhuurl' => $_GPC['guzhuurl'],
            'sharevalue' => intval($_GPC['sharevalue'])
        );
        if (empty($id)) {
            pdo_insert($this->tablename, $insert);
        } else {
            if (!empty($_GPC['picture'])) {
                load()->func('file');
                file_delete($_GPC['picture-old']);
            } else {
                unset($insert['picture']);
            }
            pdo_update($this->tablename, $insert, array('id' => $id));
        }
    }

    public function ruleDeleted($rid = 0) {
        global $_W;
        $replies = pdo_fetchall("SELECT id, picture FROM " . tablename($this->tablename) . " WHERE rid = '$rid'");
        $deleteid = array();
        load()->func('file');
        if (!empty($replies)) {
            foreach ($replies as $index => $row) {
                file_delete($row['picture']);
                $deleteid[] = $row['id'];
            }
        }
        pdo_delete($this->tablename, "id IN ('" . implode("','", $deleteid) . "')");
        return true;
    }

}
