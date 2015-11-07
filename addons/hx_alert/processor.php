<?php
/**
 * 弹死你模块处理程序
 *
 * @author 华轩科技
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Hx_alertModuleProcessor extends WeModuleProcessor {
	public $reply = 'hx_alert_reply';
	public $list = 'hx_alert_list';
	public function respond() {
		global $_W;
        $rid = $this->rule;
        $sql = "SELECT * FROM " . tablename($this->reply) . " WHERE `rid`=:rid LIMIT 1";
        $row = pdo_fetch($sql, array(':rid' => $rid));
        if (empty($row['id'])) {
            return array();
        }
        return $this->respNews(array(
                    'Title' => $row['title'],
                    'Description' => $row['description'],
                    'PicUrl' => empty($row['picture']) ? '' : ($_W['attachurl'] . $row['picture']),
                    'Url' => $this->createMobileUrl('index', array('rid' => $rid)),
        ));
	}
}