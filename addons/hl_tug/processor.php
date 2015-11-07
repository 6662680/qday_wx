<?php

/**
 * 摇一摇抽奖模块
 *
 */
defined('IN_IA') or exit('Access Denied');

class hl_tugModuleProcessor extends WeModuleProcessor {

    public function respond() {
        global $_W;
        $rid = $this->rule;
        $sql = "SELECT * FROM " . tablename('hl_tug_reply') . " WHERE `rid`=:rid LIMIT 1";
        $row = pdo_fetch($sql, array(':rid' => $rid));
        if (empty($row['id'])) {
            return array();
        }

        return $this->respNews(array(
                    'Title' => $row['title'],
                    'Description' => $row['description'],
                    'PicUrl' => tomedia( $row['picture'] ),
                    'Url' => $this->createMobileUrl('index', array('id' => $rid)),
        ));
    }

}
