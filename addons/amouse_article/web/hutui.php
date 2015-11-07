<?php
/**
 */

global $_W, $_GPC;
$weid= $_W['uniacid'];
$foo = !empty($_GPC['foo']) ? $_GPC['foo'] : 'display';
if ($foo == 'display') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $condition = '';
    $params = array();
    if (!empty($_GPC['keyword'])) {
        $condition .= " AND title LIKE :keyword";
        $params[':keyword'] = "%{$_GPC['keyword']}%";
    }
    $list = pdo_fetchall("SELECT * FROM ".tablename('wx_tuijian')." WHERE weid = '{$weid}' $condition ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('wx_tuijian') . " WHERE weid = '{$weid}'");
    $pager = pagination($total, $pindex, $psize);

} elseif ($foo == 'post') {
    load()->func('tpl');
    $id = intval($_GPC['id']);
    if ($id>0) {
        $item = pdo_fetch("SELECT * FROM ".tablename('wx_tuijian')." WHERE id = :id" , array(':id' => $id));
        if (empty($item)) {
            message('抱歉，推荐微信不存在或是已经删除！', '', 'error');
        }
    }
    if (checksubmit('submit')) {
        empty($_GPC['title']) ? message('亲,标题不能为空') : $title= $_GPC['title'];
        empty($_GPC['thumb']) ? message('亲,缩略图不能为空') : $thumb= $_GPC['thumb'];
        $data = array(
            'weid' =>$weid,
            'title' => $title,
            'description' => $_GPC['description'],
            'guanzhuUrl' => $_GPC['guanzhuUrl'],
            'thumb'=>$thumb,
            'clickNum' =>0,
            'createtime' => TIMESTAMP,
        );
        if (empty($id)) {
            pdo_insert('wx_tuijian', $data);
        } else {
            unset($data['createtime']);
            pdo_update('wx_tuijian', $data, array('id' => $id));
        }
        message('推荐微信更新成功！', $this->createWebUrl('hutui', array('foo' => 'display')), 'success');
    }
}elseif($foo == 'recommed'){//推荐
    $id= intval($_GPC['id']);
    $recommed= intval($_GPC['hot']);
    if($recommed==1){
        $msg='推荐';
    }elseif($recommed==0){
        $msg='取消推荐';
    }
    if($id > 0) {
        pdo_update('wx_tuijian',array('hot' =>$recommed), array('id' => $id)) ;
        message($msg.'成功！', $this->createWebUrl('hutui', array('op' => 'display')), 'success');
    }
}elseif ($foo == 'delete') {
    $id = intval($_GPC['id']);
    $row = pdo_fetch("SELECT id, thumb FROM ".tablename('wx_tuijian')." WHERE id = :id", array(':id' => $id));
    if (empty($row)) {
        message('抱歉，推荐微信不存在或是已经被删除！');
    }
    if (!empty($row['thumb'])) {
        file_delete($row['thumb']);
    }
    pdo_delete('wx_tuijian', array('id' => $id));
    message('删除成功！', referer(), 'success');
}

include $this->template('wxtj');