<?php
/**
 * 2048游戏模块定义
 *
 * @author Libi
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
class amouse_ecardModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
		global $_W;
		/*
		load()->func('tpl');
        if (!empty($rid)) {
            $reply = pdo_fetch("SELECT * FROM " . tablename('amouse_ecard_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
        }
        if (!$reply) {
            $reply = array(
                "status" => "1",
                "tpl" => "0",
                "description" => "公众账号启用微名片，粉丝才能创建微名片", 
                "thumb" => "../addons/amouse_ecard/icon.jpg",
            );
        }
		
		include $this->template('web/form');*/
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
		global $_W, $_GPC;
       /* $id = intval($_GPC['reply_id']);
        $insert = array(
			'rid' => $rid,
            'title' => $_GPC['title'], 
            'description' => $_GPC['description'],
            'weid' => $_W['uniacid'], 
            'createtime' => time(),
		);

		if (empty($id)) {
            $id = pdo_insert('amouse_ecard_reply', $insert);
		} else {
            unset($insert['rid']);
			pdo_update('amouse_ecard_reply', $insert, array('id' => $id));
		}

        $insert['id'] = $id;
        $d = pdo_fetch("select * from " . tablename('amouse_ecard_reply') . " where rid=:rid limit 1", array(":rid" => $rid));
        $this->write_cache($rid, $d);*/
        return true;
	}

    function write_cache($filename, $data){
        global $_W;
        $path = "/addons/amouse_ecard";
        $filename = IA_ROOT . $path . "/data/" . $filename . ".txt";
        load()->func('file');
        mkdirs(dirname($filename));
        file_put_contents($filename, base64_encode(json_encode($data)));
        @chmod($filename, $_W['config']['setting']['filemode']);
        return is_file($filename);
    }

    public function ruleDeleted($rid = 0) {
        pdo_delete('amouse_ecard_reply', array('rid' => $rid));
        
    }


}