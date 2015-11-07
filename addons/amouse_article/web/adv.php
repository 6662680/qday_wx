<?php
    global $_GPC, $_W;
    $op= $_GPC['op'] ? $_GPC['op'] : 'display';
    $weid= $_W['uniacid'];

    if($op == 'display') {
        $pindex= max(1, intval($_GPC['page']));
        $psize= 20; //每页显示
        $condition= "WHERE `weid` = $weid";
        if(!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%".$_GPC['keyword']."%'";
        }
        $list= pdo_fetchall('SELECT * FROM '.tablename('fineness_adv')." $condition LIMIT ".($pindex -1) * $psize.','.$psize);
        $total= pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename('fineness_adv').$condition);
        $pager= pagination($total, $pindex, $psize);
    }elseif($op == 'post') {
        $id= intval($_GPC['id']);

        load()->func('tpl');

        if($id > 0) {
            $adv= pdo_fetch('SELECT * FROM '.tablename('fineness_adv')." WHERE weid=:weid AND id=:id", array(':weid' => $_W['uniacid'], ':id' => $id));
        }

        if(checksubmit('submit')) {
            $title= trim($_GPC['title']) ? trim($_GPC['title']) : message('请填写幻灯片名称！');
            $logo= trim($_GPC['thumb']) ? trim($_GPC['thumb']) : message('请上传幻灯片图片！');
            $insert= array('title' => $title,
                'link' => $_GPC['link'],
                'thumb' => $_GPC['thumb'],
                'weid' => $_W['uniacid']);

            if(!empty($_FILES['thumb']['tmp_name'])) {
                file_delete($_GPC['thumb-old']);
                $upload= file_upload($_FILES['thumb']);
                if(is_error($upload)) {
                    message($upload['message'], '', 'error');
                }
                $data['thumb']= $upload['path'];
            }

            if(empty($id)) {
                pdo_insert('fineness_adv', $insert);
            } else {
                if(pdo_update('fineness_adv', $insert, array('id' => $id)) === false) {
                    message('更新幻灯片数据失败, 请稍后重试.', 'error');
                }
            }
            message('更新幻灯片数据成功！', $this->createWebUrl('adv', array('op' => 'display', 'name' => 'zqwyx_heixiu')), 'success');
        }
    }elseif($op == 'deleteop') { //删除

        if(isset($_GPC['delete'])) {
            $ids= implode(",", $_GPC['delete']);
            $sqls= "delete from  ".tablename('fineness_adv')."  where id in(".$ids.")";
            pdo_query($sqls);
            message('删除成功！', referer(), 'success');
        }
        $id= intval($_GPC['id']);
        $temp= pdo_delete("fineness_adv", array("weid" => $_W['uniacid'],'id' => $id));
        message('删除数据成功！', $this->createWebUrl('adv', array('op' => 'display', 'name' => 'zqwyx_heixiu')), 'success');
    }

    include $this->template('adv');

?>
