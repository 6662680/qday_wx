<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-5
 * Time: 下午1:37
 * To change this template use File | Settings | File Templates.
 */
global $_GPC, $_W;
$op= $_GPC['op'] ? $_GPC['op'] : 'display';
$weid= $_W['uniacid'];
if($op == 'display') {
    $pindex= max(1, intval($_GPC['page']));
    $psize= 20; //每页显示
    $condition= "WHERE `weid` = $weid ";
    $list= pdo_fetchall('SELECT * FROM '.tablename('amouse_weicard_music')." $condition LIMIT ".($pindex -1) * $psize.','.$psize);
    $total= pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename('amouse_weicard_music').$condition);
    $pager= pagination($total, $pindex, $psize);

}elseif($op == 'post') {
    $id= intval($_GPC['id']);
    load()->func('tpl');
    load()->func('file');
    if($id > 0) {
        $music= pdo_fetch('SELECT * FROM '.tablename('amouse_weicard_music')." WHERE weid=:weid AND id=:id", array(':weid' => $weid, ':id' => $id));
    }

    if(checksubmit('submit')) {
        $img= trim($_GPC['musicImg']) ? trim($_GPC['musicImg']) : message('请上传背景音乐图片！');
        $insert= array(
            'musicName' =>$_GPC['musicName'],
            'musicImg' => $img,
            'musicSinger' =>$_GPC['musicSinger'],
            'musicUrl' =>$_GPC['musicUrl'],
            'weid' =>$weid);

        if(empty($id)) {
            pdo_insert('amouse_weicard_music', $insert);
        } else {
            pdo_update('amouse_weicard_music', $insert, array('id' => $id));
        }
        message('更新背景音乐数据成功！', $this->createWebUrl('music', array('op' => 'display', 'name' => 'amouse_weicard')), 'success');
    }
}elseif($op == 'deleteop') { //删除
    $id= intval($_GPC['id']);
    load()->func('file');

    $row = pdo_fetch("SELECT id, img FROM ".tablename('amouse_weicard_music')." WHERE id = :id", array(':id' => $id));
    if (empty($row)) {
        message('抱歉，背景音乐不存在或是已经被删除！');
    }
    if (!empty($row['img'])) {
        file_delete($row['img']);
    }

    pdo_delete("amouse_weicard_music", array("weid" =>$weid,'id' => $id));
    message('删除数据成功！', $this->createWebUrl('music', array('op' => 'display', 'name' => 'amouse_weicard')), 'success');
}

include $this->template('web/music');