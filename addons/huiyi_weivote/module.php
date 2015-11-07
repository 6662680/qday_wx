<?php
/**
 * 微信投票
 * 模块定义
 * @author yyy
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Huiyi_weivoteModule extends WeModule {

    public $table_reply ='huiyi_weivote_reply';
    public $table_vote  ='huiyi_weivote_vote';
    public $table_option='huiyi_weivote_option';
    public $table_log   ='huiyi_weivote_log';

	public function fieldsFormDisplay($rid = 0) {
        global $_W,$_GPC;
        //$weid = $_W['weid'];
        $uniacid = $_W['uniacid'];//统一公众号 ID
        $votes = pdo_fetchall("SELECT id,title FROM ".tablename($this->table_vote)." WHERE uniacid = :uniacid ORDER BY `id` DESC", array(':uniacid' => $uniacid));
        if (!empty($rid)) {
            $reply = pdo_fetch("SELECT * FROM ".tablename($this -> table_reply)." WHERE rid = :rid", array(':rid' => $rid));
        }
        load()->func('tpl');
        include $this->template('rule');
	}

	public function fieldsFormValidate($rid = 0) {
        //规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
        global $_GPC;
        //此处服务端验证表单数据的完整性，直接返回错误信息。
        //if (empty($_GPC['content'])) {
        //    return '请填写回复内容';
        //}
        return '';
	}

	public function fieldsFormSubmit($rid) {
        //规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
        global $_W, $_GPC;
        /*
         * 此处各种验证通过后，需要进行入库操作。
         * 入库时需要注意，此处数据可能为更新操作也可能为新增数据。
         */
        $data = array(
            'rid' => $rid,
            'vid' => intval($_GPC['vid']),
            'title' => $_GPC['title'],
            'picture' => $_GPC['picture'],
            'description' => $_GPC['description'],
        );
        $id = pdo_fetchcolumn("SELECT id FROM ".tablename($this -> table_reply)." WHERE rid = :rid", array(':rid' => $rid));
        if (empty($id)) {
            pdo_insert($this -> table_reply, $data);
        } else {
            pdo_update($this -> table_reply, $data, array('id' => $id));
        }
	}

	public function ruleDeleted($rid) {
        //删除规则时调用，这里 $rid 为对应的规则编号
        /*
         * 此处可能需要一些权限及数据方面的判断
         * 除了表数据可能还需要删除一些附带的图片等资源
         */
        pdo_delete($this -> table_reply, array('rid' => $rid));
	}

	public function settingsDisplay($settings) {
        global $_W, $_GPC;
        //点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
        //在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
        if(checksubmit('submit')) {
            //字段验证, 并获得正确的数据$dat
            $dat['level'] = $_GPC['level'];
			//$dat = array(
			//	'uniac_type'  => $_GPC['uniac_type'],
             //   'appid'  => $_GPC['appid'],
			//	'appsecret'  => $_GPC['appsecret']
            //);
            $this->saveSettings($dat);
            message('配置参数更新成功！', referer(), 'success');
        }
        //这里来展示设置项表单
        include $this->template('settings');
	}

}