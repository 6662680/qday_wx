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
    $condition= "WHERE `weid` = $weid  ORDER BY `displayorder` DESC ";
    $list= pdo_fetchall('SELECT * FROM '.tablename('amouse_weicard_bg')." $condition LIMIT ".($pindex -1) * $psize.','.$psize);
    $total= pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename('amouse_weicard_bg').$condition);
    $pager= pagination($total, $pindex, $psize);

}elseif($op == 'post') {
    $id= intval($_GPC['id']);
    load()->func('tpl');
    load()->func('file');
    if($id > 0) {
        $bg= pdo_fetch('SELECT * FROM '.tablename('amouse_weicard_bg')." WHERE weid=:weid AND id=:id", array(':weid' => $_W['uniacid'], ':id' => $id));
    }

    if(checksubmit('submit')) {
        $img= trim($_GPC['img']) ? trim($_GPC['img']) : message('请上传背景图片图片！');
        $insert= array(
            'displayorder' =>intval($_GPC['displayorder']),
            'img' => $img,
            'weid' =>$weid);

        if(!empty($_FILES['img']['tmp_name'])) {
            file_delete($_GPC['thumb-old']);
            $upload= file_upload($_FILES['img']);
            if(is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['img']= $upload['path'];
        }

        if(empty($id)) {
            pdo_insert('amouse_weicard_bg', $insert);
        } else {
            pdo_update('amouse_weicard_bg', $insert, array('id' => $id));
        }
        message('更新背景图片数据成功！', $this->createWebUrl('bg', array('op' => 'display', 'name' => 'amouse_weicard')), 'success');
    }
}elseif($op == 'deleteop') { //删除
    $id= intval($_GPC['id']);
    load()->func('file');

    $row = pdo_fetch("SELECT id, img FROM ".tablename('amouse_weicard_bg')." WHERE id = :id", array(':id' => $id));
    if (empty($row)) {
        message('抱歉，背景图片不存在或是已经被删除！');
    }
    if (!empty($row['img'])) {
        file_delete($row['img']);
    }

    pdo_delete("amouse_weicard_bg", array("weid" =>$weid,'id' => $id));
    message('删除数据成功！', $this->createWebUrl('bg', array('op' => 'display', 'name' => 'amouse_weicard')), 'success');
}

include $this->template('web/bg');