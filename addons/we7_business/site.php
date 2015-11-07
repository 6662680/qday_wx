<?php
/**
 * @author WeEngine Team
 * @url http://www.qdaygroup.com/forum.php?mod=forumdisplay&fid=36&filter=typeid&typeid=1
 */
defined('IN_IA') or exit('Access Denied');

class We7_businessModuleSite extends WeModuleSite {

    public function doWebPost() {
        global $_GPC, $_W;
        if (empty($_GPC['do'])) {
            $_GPC['do'] = 'post';
        }
        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch("SELECT * FROM " . tablename('business') . " WHERE id = :id", array(':id' => $id));
            if (empty($item)) {
                message('抱歉，商户不存在或是已经删除！', '', 'error');
            }
        }
        if (checksubmit('submit')) {
            if (empty($_GPC['title'])) {
                message('请输入商户名称！');
            }
            $data = array(
                'weid' => $_W['weid'],
                'title' => $_GPC['title'],
                'content' => htmlspecialchars_decode($_GPC['content']),
                'phone' => $_GPC['phone'],
                'qq' => $_GPC['qq'],
                'province' => $_GPC['district']['province'],
                'city' => $_GPC['district']['city'],
                'dist' =>$_GPC['district']['district'],
                'address' => $_GPC['address'],
                'lng' => $_GPC['baidumap']['lng'],
                'lat' => $_GPC['baidumap']['lat'],
                'industry1' => $_GPC['industry']['parent'],
                'industry2' => $_GPC['industry']['child'],
                'createtime' => TIMESTAMP,
            );
            if (!empty($_GPC['thumb'])) {
                $data['thumb'] = $_GPC['thumb'];
				load()->func('file');
                file_delete($_GPC['thumb-old']);
            }
            if (empty($id)) {
                pdo_insert('business', $data);
            } else {
                unset($data['createtime']);
                pdo_update('business', $data, array('id' => $id));
            }
            message('商户信息更新成功！', $this->createWebUrl('display'), 'success');
        }
        include $this->template('post');
    }

    public function doWebDisplay() {
        global $_W, $_GPC;
        if (empty($_GPC['do'])) {
            $_GPC['do'] = 'display';
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $condition = '';
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
        }
        if( is_array($_GPC['industry']) ){
            if (!empty($_GPC['industry']['parent'])) {
            $condition .= " AND industry1 = '{$_GPC['industry']['parent']}'";
            }
            if (!empty($_GPC['industry']['child'])) {
                $condition .= " AND industry2 = '{$_GPC['industry']['child']}'";
            }
        }
        
        $list = pdo_fetchall("SELECT * FROM " . tablename('business') . " WHERE weid = '{$_W['weid']}' $condition ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('business') . " WHERE weid = '{$_W['weid']}' $condition");
        $pager = pagination($total, $pindex, $psize);
        include $this->template('display');
    }

    public function doWebDelete() {
        global $_GPC;
        $id = intval($_GPC['id']);
        $item = pdo_fetch("SELECT * FROM " . tablename('business') . " WHERE id = :id", array(':id' => $id));
        if (empty($item)) {
            message('抱歉，商户不存在或是已经删除！', '', 'error');
        }
        if (!empty($item['thumb'])) {
			load()->func('file');
            file_delete($item['thumb']);
        }
        pdo_delete('business', array('id' => $item['id']));
        message('删除成功！', referer(), 'success');
    }

    public function doMobileDetail() {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        $item = pdo_fetch("SELECT * FROM " . tablename('business') . " WHERE id = :id", array(':id' => $id));
        if (empty($item)) {
            message('抱歉，该商家不存在或是已经被删除！');
        }
        $content = strip_tags($item['content']);
        $content = cutstr($content, 50, true);
        include $this->template('detail');
    }

}
