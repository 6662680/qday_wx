<?php

/**
 * 微酒店
 *
 */
defined('IN_IA') or exit('Access Denied');

class Ewei_hotelModuleProcessor extends WeModuleProcessor {

    public function respond() {
        global $_W;
        $rid = $this->rule;
         if ($rid) {
            $reply = pdo_fetch("SELECT * FROM " . tablename('hotel2_reply') . " WHERE rid = :rid", array(':rid' => $rid));
            if ($reply) {
                $sql = 'SELECT id,title,description,thumb FROM ' . tablename('hotel2') . ' WHERE `weid`=:weid AND `id`=:hotelid';
                $hotel = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':hotelid' => $reply['hotelid']));
                $news = array();
                $news[] = array(
                    'title' => $hotel['title'],
                    'description' => strip_tags($hotel['description']),
                    'picurl' => $_W['attachurl'] . $hotel['thumb'],
                    //'url' => create_url('mobile/module/hotel2', array('name' => 'hotel2', 'op'=>'detail', 'id' => $hotel['id'], 'weid' => $_W['uniacid']))
                    'url'=>$this->createMobileUrl('detail', array('hid' => $hotel['id']))
                     
                );
                return $this->respNews($news);
            }
        }

        $this->module['config']['picurl'] = $_W['attachurl'] . $this->module['config']['picurl'];
        return $this->respNews($this->module['config']);
    }

}
