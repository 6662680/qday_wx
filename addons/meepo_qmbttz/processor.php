<?php
/**
 * 冰桶挑战模块处理程序
 *
 * @author meepo
 * @url http://bbs.b2ctui.com/forum.php
 */
defined('IN_IA') or exit('Access Denied');

class Meepo_qmbttzModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		global $_W;
		$this -> module['config']['picurl'] = $_W['attachurl'] . $this -> module['config']['picurl'];
		return $this -> respNews($this -> module['config']);
	}
}