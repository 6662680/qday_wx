<?php

/**
 * 微相册模块定义
 *
 * @author WeEngine Team
 * @url http://www.qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

class We7_albumModule extends WeModule {

    public function fieldsFormDisplay($rid = 0) {
        //要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
        global $_W, $_GPC;
        if (!empty($rid)) {
            $reply = pdo_fetchall("SELECT * FROM " . tablename('album_reply') . " WHERE rid = :rid", array(':rid' => $rid));
            if (!empty($reply)) {
                foreach ($reply as $row) {
                    $albumids[$row['albumid']] = $row['albumid'];
                }
                $album = pdo_fetchall("SELECT id, title, thumb, content FROM " . tablename('album') . " WHERE id IN (" . implode(',', $albumids) . ")", array(), 'id');
            }
        }
        include $this->template('rule');
    }

    public function fieldsFormValidate($rid = 0) {
        //规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
        return '';
    }

    public function fieldsFormSubmit($rid) {
        //规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
        global $_W, $_GPC;
        
        //删除旧的
        pdo_query("delete from ".tablename('album_reply')." where rid=:rid",array(':rid'=>$rid));
        
        if (!empty($_GPC['albumid'])) {
            foreach ($_GPC['albumid'] as $aid) {
                pdo_insert('album_reply', array(
                    'rid' => $rid,
                    'albumid' => $aid,
                ));
            }
        }
    }

    public function ruleDeleted($rid) {
        //删除规则时调用，这里 $rid 为对应的规则编号
    }

    public function settingsDisplay($settings) {
       global $_GPC, $_W;
                                
       load()->func('tpl');
        if(checksubmit()) {
            $cfg = $settings;
            $cfg['album']['listtype'] = $_GPC['album']['listtype'];
            $cfg['album']['toppic'] = $_GPC['toppic'];

            if($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
               
        include $this->template('setting'); 
    }

}
