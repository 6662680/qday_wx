<?php
/**
 * community模块定义
 *
 * @author 
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Xfeng_communityModule extends WeModule {
	public $comslide    	= 'community_slide';
	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
	}

	public function settingsDisplay($settings) {
	
	global $_GPC, $_W;
	
	if(checksubmit()) {
	
		$cfg = array(
			'cname'        => $_GPC['cname'],
			'tel'          => $_GPC['tel'],
			'verify'	   => $_GPC['verify'],
			'businesscode' => $_GPC['businesscode'],
			'verifycode'   => $_GPC['verifycode'],
			'report_type'  => $_GPC['report_type'],
			'reportid'     => $_GPC['reportid'],
			'resgisterid'  => $_GPC['resgisterid'],
			'sms_account'  => $_GPC['sms_account'],
			'print_status' => $_GPC['print_status'],
			'print_type'   => $_GPC['print_type'],
			'print_usr'    => $_GPC['print_usr'],
			'print_nums'   => $_GPC['print_nums'],
			'print_bottom' => $_GPC['print_bottom'],
	);
		$this->saveSettings($cfg);
		message('保存成功', 'refresh');
	}
	
	include $this->template('setting');
	
	}

}