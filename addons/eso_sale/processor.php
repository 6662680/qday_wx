<?php



defined('IN_IA') or exit('Access Denied');

class eso_saleModuleProcessor extends WeModuleProcessor {
	
	public function respond() {
		global $_W;
		$this->module['config']['picurl'] = $_W['attachurl'] . $this->module['config']['picurl'];
		return $this->respNews($this->module['config']);
	}
}
