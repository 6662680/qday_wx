<?php

/**
 * 贺卡模块处理程序
 *
 * @author 超级无聊
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Wl_hekaModuleProcessor extends WeModuleProcessor {

    public function respond() {
        global $_W;
        $rid = $this->rule;
        $sql = "SELECT * FROM " . tablename('heka_reply') . " WHERE `rid`=:rid LIMIT 1";
        $row = pdo_fetch($sql, array(':rid' => $rid));
        if (empty($row['id'])) {
            return array();
        }
        return $this->respNews(array(
                    'Title' => $row['title'],
                    'Description' => $row['description'],
                    'PicUrl' => empty($row['picture']) ? '' : ($_W['attachurl'] . $row['picture']),
                    'Url' => $this->createMobileUrl('index', array('id' => $rid)),
        ));
    }

}
