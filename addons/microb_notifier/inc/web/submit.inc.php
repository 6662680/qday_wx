<?php
global $_W, $_GPC;

$t = $_GPC['t'];
$t = in_array($t, array('display', 'help')) ? $t : 'display';
if($t == 'display') {
    $type = 'submit';
    $types = array('submit');
    $type = in_array($type, $types) ? $type : 'submit';

    $sql = "SELECT * FROM " . tablename('mb_store_notifies') . ' WHERE `type`=:type AND `uniacid`=:uniacid';
    $pars = array();
    $pars[':type'] = $type;
    $pars[':uniacid'] = $_W['uniacid'];
    $setting = pdo_fetch($sql, $pars);

    if(checksubmit()) {
        $input = array();
        $input['url'] = trim($_GPC['url']);
        $input['template'] = trim($_GPC['template']);
        $input['caption'] = $_GPC['caption'];
        $input['remark'] = $_GPC['remark'];
        if(empty($input['template']) || empty($input['caption']) || empty($input['remark'])) {
            message('必须输入完整');
        }
        $input['type'] = $type;
        $input['uniacid'] = $_W['uniacid'];
        $input['content'] = '';
        if(empty($setting)) {
            $ret = pdo_insert('mb_store_notifies', $input);
        } else {
            $ret = pdo_update('mb_store_notifies', $input, array('id'=>$setting['id']));
        }
        if($ret !== false) {
            message('保存成功', 'refresh');
        } else {
            message('保存失败, 请稍后重试');
        }
    }
    include $this->template('submit');
}

if($t == 'help') {
    include $this->template('submit-help');
}
