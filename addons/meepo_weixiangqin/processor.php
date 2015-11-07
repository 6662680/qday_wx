<?php
/**
 * 调用第三方数据接口处理类
 * 
 * [WeEngine System] Copyright (c) 2013 qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

class Meepo_weixiangqinModuleProcessor extends WeModuleProcessor
{
	public function respond()
	{
		global $_W, $_GPC;
		$weid = $_W['uniacid'];
		$openid = $this->message['from'];
		$content = $this->message['content'];
		$cfg = $this->module['config'];
		if ($content == "相亲" || $content == "交友") {
			$title = !empty($cfg['title']) ? $cfg['title'] : '微相亲';
			$description = !empty($cfg['description']) ? $cfg['description'] : '本平台提供真实的相亲交友机会！';
			$picurl = !empty($cfg['picurl']) ? $_W['attachurl'] . $cfg['picurl'] : 'http://www.baidu.com/img/bdlogo.gif';
			$news = array('title' => $title, 'description' => $description, 'picurl' => $picurl, 'url' => $this->buildSiteUrl($this->createMobileUrl('alllist')),);
			return $this->respNews($news);
		} elseif ($content == "相亲活动" || $content == "交友活动") {
			$title = !empty($cfg['huodongtitle']) ? $cfg['huodongtitle'] : '相亲交友活动';
			$description = !empty($cfg['kefuphone']) ? "本平台提供真实的相亲交友机会！ 客服电话:" . $cfg['kefuphone'] : '本平台提供真实的相亲交友机会！';
			$picurl = !empty($cfg['huodongpicurl']) ? $_W['attachurl'] . $cfg['huodongpicurl'] : 'http://www.baidu.com/img/bdlogo.gif';
			$news = array('title' => $title, 'description' => $description, 'picurl' => $picurl, 'url' => $this->buildSiteUrl($this->createMobileUrl('huodongindex')),);
			return $this->respNews($news);
		}
	}
}
