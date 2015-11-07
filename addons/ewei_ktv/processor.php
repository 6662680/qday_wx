<?php

/**
 * 微ktv
 *
 */
defined('IN_IA') or exit('Access Denied');

class Ewei_ktvModuleProcessor extends WeModuleProcessor {

    public function respond() {
        global $_W;
        $rid = $this->rule;
         if ($rid) {
            $reply = pdo_fetch("SELECT * FROM " . tablename('ktv2_reply') . " WHERE rid = :rid", array(':rid' => $rid));
            if ($reply) {
                $sql = 'SELECT id,title,description,thumb FROM ' . tablename('ktv2') . ' WHERE `weid`=:weid AND `id`=:ktvid';
                $ktv = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':ktvid' => $reply['ktvid']));
                $news = array();
                $news[] = array(
                    'title' => $ktv['title'],
                    'description' => strip_tags($ktv['description']),
                    'picurl' => $_W['attachurl'] . $ktv['thumb'],
                    //'url' => create_url('mobile/module/ktv2', array('name' => 'ktv2', 'op'=>'detail', 'id' => $ktv['id'], 'weid' => $_W['uniacid']))
                    'url'=>$this->createMobileUrl('detail', array('hid' => $ktv['id']))
                     
                );
                return $this->respNews($news);
            }
        }

        $this->module['config']['picurl'] = $_W['attachurl'] . $this->module['config']['picurl'];
        return $this->respNews($this->module['config']);
    }

}
