<?php
/**
 * 文本投票
 * @author yyy
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Huiyi_weivoteModuleProcessor extends WeModuleProcessor {

    public $table_reply ='huiyi_weivote_reply';
    public $table_vote  ='huiyi_weivote_vote';
    public $table_option='huiyi_weivote_option';
    public $table_log   ='huiyi_weivote_log';

    public function respond() {
        global $_W;
        $rid = $this->rule;

        $sql = "SELECT * FROM " . tablename($this -> table_reply) . " WHERE `rid`=:rid LIMIT 1";
        $reply = pdo_fetch($sql, array(':rid' => $rid));
        if (empty($reply['id'])) {
            return array();
        }

        checkauth();

        $sql = "SELECT * FROM " . tablename($this -> table_vote) . " WHERE `id`=:id LIMIT 1";
        $vote = pdo_fetch($sql, array(':id' => $reply['vid']));
        if (empty($vote['id'])) {
            return array();
        }

        //$member = $this->getMember();
        //if (empty($member['nickname']) || empty($member['avatar'])) {
        //    $message = '发表话题前请<a target="_blank" href="'.$this->buildSiteUrl($this->createMobileUrl('register')).'">登记</a>您的信息。';

        $title = $reply['title'] == '' ? '微投票': $reply['title'];
        $description = $reply['description'] == '' ? '微投票活动': $reply['description'];
        $picUrl = $_W['attachurl'] . $reply['picture'];
        $url = $this->createMobileUrl('Index', array('id' => $vote['id']));

        /**
         * 预定义的操作, 构造返回图文消息结构
         * @param array $news 回复的图文定义(定义为元素集合, 每个元素结构定义为 title - string: 新闻标题, description - string: 新闻描述, picurl - string: 图片链接, url - string: 原文链接)
         * @return array 返回的消息数组结构
         */
        return $this->respNews(array(
            'title' => $title,
            'description' => $description,
            'picUrl' => $picUrl,
            'url' => $url,
        ));
    }

}