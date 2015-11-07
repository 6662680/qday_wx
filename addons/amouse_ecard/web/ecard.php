<?php
    //shizhongying qq:214983937
    global $_GPC, $_W;
    $op= $_GPC['op'] ? $_GPC['op'] : 'display';
    $weid= $_W['uniacid'];
    if($op == 'display') {
        $pindex= max(1, intval($_GPC['page']));
        $psize= 20; //每页显示
        $condition= "WHERE `weid` = $weid";
        if(!empty($_GPC['keyword'])) {
            $condition .= " AND realname LIKE '%".$_GPC['keyword']."%'";
        }
        $list= pdo_fetchall('SELECT * FROM '.tablename('amouse_weicard_member')." $condition LIMIT ".($pindex -1) * $psize.','.$psize);
        $total= pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename('amouse_weicard_member').$condition);
        $pager= pagination($total, $pindex, $psize);
    }elseif($op == 'deleteop') { //删除
        if(isset($_GPC['delete'])) {
            $ids= implode(",", $_GPC['delete']);
            $sqls= "delete from  ".tablename('amouse_weicard_member')."  where id in(".$ids.")";
            pdo_query($sqls);
            message('删除成功！', referer(), 'success');
        }
        $id= intval($_GPC['id']);
        $temp= pdo_delete("amouse_weicard_member", array("weid" =>$weid,'id' => $id));
        message('删除数据成功！', $this->createWebUrl('ecard', array('op' => 'display')), 'success');
    }

    include $this->template('web/ecard');
?>
