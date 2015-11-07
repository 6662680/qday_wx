<?php
/**
 * 图片魔方模块处理程序
 *
 * @author 伊索科技
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class scene_cubeModuleProcessor extends WeModuleProcessor {
    public $table_reply  = 'scene_cube_reply';
    public $table_list  = 'scene_cube_list';

    public function respond() {
        global $_W;
        $rid = $this->rule;
        $fromuser = $this->message['from'];

        if($rid) {
            $reply = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE rid = :rid", array(':rid' => $rid));
            if($reply) {
                $sql = 'SELECT id,reply_title,reply_thumb,reply_description FROM ' . tablename($this->table_list) . ' WHERE `weid`=:weid AND `id`=:list_id';
                $activity = pdo_fetch($sql, array(':weid' => $_W['weid'], ':list_id' => $reply['list_id']));
                $news = array();
                $news[] = array(
                    'title' => $activity['reply_title'],
                    'description' =>trim(strip_tags($activity['reply_description'])),
                    'picurl' =>empty($activity['reply_thumb'])?'':($activity['reply_thumb']),
                    'url' => $this->createMobileUrl('show', array('id' => $activity['id'])),
                );
                return $this->respNews($news);
            }
        }
        return null;
    }
}