<?php
/**
 * 集阅读模块定义
 *
 * @author 别具一格
 * @url http://www.qdaygroup.com/
 */
defined('IN_IA') or exit('Access Denied');

class ju_readModule extends WeModule {
	public $table_reply = 'ju_read_reply';
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
		if ($rid == 0) {
			$reply = array(
				'title'=> '集阅读活动开始了!',
				'description' => '集阅读活动开始啦！',
				'topimg' => $_W['siteroot'].'addons/ju_read/template/style/img/1.jpg',
				'bgcolor' => '#266f98',
				'starttime' => time(),
				'endtime' => time() + 10 * 84400,
				'status' => 1,
				'tips' => '抵用劵都是在原价基础上抵用，特价产品不加入本次活动。',
			);
		}else{
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$data = iunserializer($reply['prizes']);
		}
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
		global $_W,$_GPC;
		$id = intval($_GPC['reply_id']);
		$i = 1;
		foreach ($_GPC['prizename'] as $index => $row) {
			if (empty($row)) {
				continue;
			}
			$data[$i] = array(
				'id' => $index + 1,
				'prizename' => $_GPC['prizename'][$index],
				'neednum' => $_GPC['neednum'][$index],
				'prizenum' => $_GPC['prizenum'][$index],
				'prizesy' => $_GPC['prizesy'][$index],
				);
			$i ++ ;
		}
		$insert = array(
			'rid' => $rid,
			'uniacid' => $_W['uniacid'],
			'title' => $_GPC['title'],
			'thumb' => $_GPC['thumb'],
			'description' => $_GPC['description'],
			'starttime' => strtotime($_GPC['time'][start]),
			'endtime' => strtotime($_GPC['time'][end]),
			'status' => intval($_GPC['status']),
			'topimg' => $_GPC['topimg'],
			'bgcolor' => $_GPC['bgcolor'],
			'pagestyle' => htmlspecialchars_decode($_GPC['pagestyle']),
			'address' => htmlspecialchars_decode($_GPC['address']),
			'tips' => $_GPC['tips'],
			'linkurl' => $_GPC['linkurl'],
			'adimg' => $_GPC['adimg'],
			'tel' => $_GPC['tel'],
			'copyright' => $_GPC['copyright'],
			'prizes' => iserializer($data),
			'createtime' => TIMESTAMP,
			);
		if (empty($id)) {
			pdo_insert($this->table_reply, $insert);
		} else {
			unset($insert['createtime']);
			pdo_update($this->table_reply, $insert, array('id' => $id));
		}
	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
		$replies = pdo_fetchall("SELECT id  FROM ".tablename($this->table_reply)." WHERE rid = '$rid'");
		$deleteid = array();
		if (!empty($replies)) {
			foreach ($replies as $index => $row) {
				$deleteid[] = $row['id'];
			}
		}
		pdo_delete($this->table_reply, "id IN ('".implode("','", $deleteid)."')");
	}

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
			//字段验证, 并获得正确的数据$dat
			$this->saveSettings($dat);
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}

}