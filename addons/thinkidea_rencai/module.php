<?php
/**
 * 微招聘模块定义
 *
 * 
 */
defined('IN_IA') or exit('Access Denied');

class Thinkidea_rencaiModule extends WeModule {
	/**
	 * 自定义回复表
	 * @var unknown
	 */
	public $tablename = 'thinkidea_rencai_reply';
	
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE acid = :acid AND rid = :rid ORDER BY `id` DESC", array(':acid' => $_W['uniacid'], ':rid' => $rid));
		}
		load()->func('tpl');
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0) {
		return true;
	}

	public function fieldsFormSubmit($rid) {
		global $_GPC, $_W;
		$id = intval($_GPC['reply_id']);
		$data = array(
			'acid' => $_W['uniacid'],
			'rid' => $rid,
			'title' => $_GPC['title'],
			'avatar' => $_GPC['avatar'],
			'description' => $_GPC['description'],
			'dateline' => time()
		);
		if(empty($id)) {
			pdo_insert($this->tablename, $data);
		}else {
			pdo_update($this->tablename, $data, array('id' => $id));
		}
	}

	public function ruleDeleted($rid) {
	}

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		if(checksubmit()) {
			$dat = array(
//                    'colspan' => empty($_GPC['colspan']) ? 3 : intval($_GPC['colspan']),
                    'payroll' => $_GPC['payroll'],
                    'welfare' => $_GPC['welfare'],
                    'educational' => $_GPC['educational'],
                    'positiontype' => $_GPC['positiontype'],
                    'workexperience' => $_GPC['workexperience'],
                    'companytype' => $_GPC['companytype'],
                    'scale' => $_GPC['scale'],
					'qrcode' => $_GPC['qrcode'],
					'telephone' => $_GPC['telephone'],
                    'isopenaudit' => $_GPC['isopenaudit'],
                    'viewresumenums' => empty($_GPC['viewresumenums']) ?  intval($_GPC['viewresumenums']) : 5,
                    'isopenindexhot' => empty($_GPC['isopenindexhot']) ?  intval($_GPC['isopenindexhot']) : 5,
                    'indextopnums' => empty($_GPC['indextopnums']) ?  intval($_GPC['indextopnums']) : 5,
                    'indexhotnums' => empty($_GPC['indexhotnums']) ?  intval($_GPC['indexhotnums']) : 5,
                    'indexlastnums' => empty($_GPC['indexlastnums']) ?  intval($_GPC['indexlastnums']) : 5,
                    'indexcompanynums' => empty($_GPC['indexcompanynums']) ?  intval($_GPC['indexcompanynums']) : 5,
					'isopenlicense' => $_GPC['isopenlicense'],
					'maxfilesize' => $_GPC['maxfilesize'],
					'headimgurlsize' => $_GPC['headimgurlsize'],
					'headimgurlwidth' => $_GPC['headimgurlwidth']
			);
			$this->saveSettings($dat);
			message('配置参数更新成功！', referer(), 'success');
		}
		load()->func('tpl');
		include $this->template('setting');
	}

}