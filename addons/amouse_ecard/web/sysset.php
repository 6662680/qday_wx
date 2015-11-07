<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-3-28
 * Time: 下午5:16
 * To change this template use File | Settings | File Templates.
 */
global $_W, $_GPC;
$weid= $_W['uniacid'];
$set= $this->get_sysset($weid);
load()->func('tpl');
if(checksubmit('submit')) {
    $data= array(
        'weid' => $weid,
        'guanzhuUrl'=>$_GPC['guanzhuUrl'],
        'cnzz'=>$_GPC['cnzz'],
        'copyright'=>$_GPC['copyright'],
        'appid_share'=>$_GPC['appid_share'],
        'appsecret_share'=>$_GPC['appsecret_share'],
    );

    if(!empty($set)) {
        pdo_update('amouse_weicard_sysset', $data, array('id' => $set['id']));
    } else {
        pdo_insert('amouse_weicard_sysset', $data);
    }
    $this->write_cache("sysset_" .$weid, $data);
    message('更新参数设置成功！', 'refresh');
}

include $this->template('web/sysset');