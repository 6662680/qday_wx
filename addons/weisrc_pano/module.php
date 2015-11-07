<?php
/**
 * 360全景
 *
 * www.weisrc.com
 *
 * 作者:情天
 *
 * qq:15595755
 */
defined('IN_IA') or exit('Access Denied');
define('CONTROLLER', 'weisrc_pano');

class weisrc_panoModule extends WeModule
{
    public $name = 'weisrc_panoModule';
    public $title = '';
    public $ability = '';
    public $tablename = 'weisrc_pano_reply';

    public function fieldsFormSubmit($rid = 0)
    {
        global $_GPC, $_W;
        $id = intval($_GPC['reply_id']);

        $data = array(
            'rid' => $rid,
            'weid' => $_W['uniacid'],
            'title' => $_GPC['title'],
            'type' => intval($_GPC['casetype']),
            'description' => $_GPC['description'],
            'picture' => $_GPC['picture'],
            'picture1' => $_GPC['picture1'],
            'picture2' => $_GPC['picture2'],
            'picture3' => $_GPC['picture3'],
            'picture4' => $_GPC['picture4'],
            'picture5' => $_GPC['picture5'],
            'picture6' => $_GPC['picture6'],
            'music' => $_GPC['music'],
            'description' => $_GPC['description'],
            'status' => intval($_GPC['status']),
            'dateline' => TIMESTAMP
        );

        if (checksubmit('submit')) {
            if (empty($id)) {
                pdo_insert($this->tablename, $data);
            } else {
                unset($data['dateline']);
                pdo_update($this->tablename, $data, array('id' => $id));
            }
        }
    }

    public function fieldsFormDisplay($rid = 0)
    {
        global $_W;
        load()->func('tpl');
        if (!empty($rid)) {
            $reply = pdo_fetch("SELECT * FROM " . tablename($this->tablename) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
        }
        include $this->template('form');
    }

    public function fieldsFormValidate($rid = 0)
    {
        return true;
    }

    //删除规则
    public function ruleDeleted($rid = 0)
    {
        global $_W;
        $replies = pdo_fetchall("SELECT id FROM " . tablename($this->tablename) . " WHERE rid = '$rid'");
        $deleteid = array();
        if (!empty($replies)) {
            foreach ($replies as $index => $row) {
                $deleteid[] = $row['id'];
            }
        }
        pdo_delete($this->tablename, "id IN ('" . implode("','", $deleteid) . "')");
        return true;
    }
}
