<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-3-21
 * Time: 下午12:59
 * To change this template use File | Settings | File Templates.
 */
global $_W, $_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$weid= $_W['uniacid'] ;
load()->func('tpl');
$category = pdo_fetchall("SELECT * FROM ".tablename('fineness_article_category')." WHERE uniacid =$weid ORDER BY parentid ASC, displayorder ASC, id ASC ", array(), 'id');
$parent = array();
$children = array();
if (!empty($category)) {
    $children = '';
    foreach ($category as $cid => $cate) {
        if (!empty($cate['parentid'])) {
            $children[$cate['parentid']][] = $cate;
        } else {
            $parent[$cate['id']] = $cate;
        }
    }
}

if ($op == 'display') {
    if (checksubmit('submit')) { //排序
        if (!empty($_GPC['displayorder'])) {
            foreach ($_GPC['displayorder'] as $id => $displayorder) {
                pdo_update('fineness_article', array('displayorder' => $displayorder), array('id' => $id));
            }
            message('文章排序更新成功！', $this->createWebUrl('paper', array('op' => 'display')), 'success');
        }
    }

    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $condition = '';
    $params = array();
    if (!empty($_GPC['keyword'])) {
        $condition .= " AND title LIKE :keyword";
        $params[':keyword'] = "%{$_GPC['keyword']}%";
    }

    if (!empty($_GPC['category']['childid'])) {
        $cid = intval($_GPC['category']['childid']);
        $condition .= " AND ccate = '{$cid}'";
    } elseif (!empty($_GPC['category']['parentid'])) {
        $cid = intval($_GPC['category']['parentid']);
        $condition .= " AND pcate = '{$cid}'";
    }


    $list = pdo_fetchall("SELECT * FROM ".tablename('fineness_article')." WHERE weid = '{$weid}' $condition ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('fineness_article') . " WHERE weid = '{$weid}'");

    $pager = pagination($total, $pindex, $psize);

} elseif ($op == 'post') {
    $id = intval($_GPC['id']);
    load()->func('file');
    $pcate = $_GPC['pcate'];
    $ccate = $_GPC['ccate'];
    if ($id>0) {
        $item = pdo_fetch("SELECT * FROM ".tablename('fineness_article')." WHERE id = :id" , array(':id' => $id));
        var_dump($item['outLink']);
        if (empty($item)) {
            message('抱歉，文章不存在或是已经删除！', '', 'error');
        }
        $pcate = $item['pcate'];
        $ccate = $item['ccate'];
        $item['credit'] = iunserializer($item['credit']) ? iunserializer($item['credit']) : array();
        if(!empty($item['credit']['limit'])) {
            $credit_num = pdo_fetchcolumn('SELECT SUM(credit_value) FROM ' . tablename('mc_handsel') . ' WHERE uniacid = :uniacid AND module = :module AND sign = :sign', array(':uniacid' => $_W['uniacid'], ':module' => 'article', ':sign' => md5(iserializer(array('id' => $id)))));
            if(is_null($credit_num)) $credit_num = 0;
            $credit_yu = (($item['credit']['limit'] - $credit_num) < 0) ? 0 : $item['credit']['limit'] - $credit_num;
        }
    } else{
        $item['bg_music_switch'] ='1';
        $item['credit'] = array();
    }

    if (checksubmit('submit')) {
        empty($_GPC['title']) ? message('亲,标题不能为空') : $title= $_GPC['title'];

        $data = array(
            'weid' => $_W['uniacid'],
            'pcate' => intval($_GPC['category']['parentid']),
            'ccate' => intval($_GPC['category']['childid']),
            'template' => $_GPC['template'],
            'templatefile' => "themes/detail".$_GPC['template'],
            'title' => $title,
            'content' => htmlspecialchars_decode($_GPC['content']),
            'description' => $_GPC['description'],
            'bg_music_switch'=>$_GPC['bg_music_switch'],
            'displayorder' => intval($_GPC['displayorder']),
            'outLink' => $_GPC['outLink'],
            'author'=>$_GPC['author'],
            'createtime' => TIMESTAMP,
        );

        if (!empty($_GPC['thumb'])) {
            $data['thumb'] = $_GPC['thumb'];
        } elseif (!empty($_GPC['autolitpic'])) {
            $match = array();
            preg_match('/attachment\/(.*?)(\.gif|\.jpg|\.png|\.bmp)/', $_GPC['content'], $match);
            if (!empty($match[1])) {
                $data['thumb'] = $match[1].$match[2];
            } else {
                preg_match('/(http|https):\/\/(.*?)(\.gif|\.jpg|\.png|\.bmp)/', $_GPC['content'], $match);
                $data['thumb'] = $match[0];
            }
        }
        if(!empty($_GPC['credit']['status'])) {
            $credit['status'] = intval($_GPC['credit']['status']);
            $credit['limit'] = intval($_GPC['credit']['limit']) ? intval($_GPC['credit']['limit']) : message('请设置积分上限');
            $credit['share'] = intval($_GPC['credit']['share']) ? intval($_GPC['credit']['share']) : message('请设置分享时赠送积分多少');
            $credit['click'] = intval($_GPC['credit']['click']) ? intval($_GPC['credit']['click']) : message('请设置阅读时赠送积分多少');
            $data['credit'] = iserializer($credit);
        } else {
            $data['credit'] = iserializer(array('status' => 0, 'limit' => 0, 'share' => 0, 'click' => 0));
        }

        if($_GPC['bg_music_switch']==1){
            $data['musicurl'] =$_GPC['musicurl'] ;
        }else{
            $data['musicurl'] = '' ;
        }
        if (!empty($_FILES['thumb']['tmp_name'])) {
           file_delete($_GPC['thumb_old']);
            $upload = file_upload($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['thumb'] = $upload['path'];
        }

        if (empty($id)) {
            pdo_insert('fineness_article', $data);
        } else {
            unset($data['createtime']);
            pdo_update('fineness_article', $data, array('id' => $id));
        }
        message('文章更新成功！', $this->createWebUrl('paper', array('foo' => 'display')), 'success');
    }
} elseif ($op == 'delete') {
    load()->func('file');
    $id = intval($_GPC['id']);
    $row = pdo_fetch("SELECT id, thumb FROM ".tablename('fineness_article')." WHERE id = :id", array(':id' => $id));
    if (empty($row)) {
        message('抱歉，文章不存在或是已经被删除！');
    }
    if (!empty($row['thumb'])) {
        file_delete($row['thumb']);
    }

    pdo_delete('fineness_article', array('id' => $id));
    message('删除成功！', referer(), 'success');
}

include $this->template('article');