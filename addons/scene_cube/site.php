<?php
defined('IN_IA') or exit('Access Denied');

class Scene_cubeModuleSite extends WeModuleSite {

    public $modulename = 'scene_cube';
    public $typeArr = array('1' => array('type' => 'pure', 'name' => '单图片展', 'desc' => '场景：单一图片显示,建议大小640*1008,或者相同比例,具体参考实际'), '2' => array('type' => 'btn', 'name' => '按钮链接', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。中下部有按钮图片实现连接具体参考实际'), '3' => array('type' => 'share', 'name' => '按钮分享', 'desc' => '场景3：图片全屏显示建议大小640*1008,或者相同比例。中下部有按钮图片点击出现分享图燿具体参考实际'), '4' => array('type' => 'tel', 'name' => '按钮拨号', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。中下部有按钮图片实现一键拨卿具体参考实际'), '5' => array('type' => 'video', 'name' => 'Mp4视频播放', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。视频格式，需要mp4格式(具体参考实际'), '6' => array('type' => 'map', 'name' => '地图导航', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。百度地图导膿具体参考实际'), '7' => array('type' => 'youku', 'name' => '优酷视频', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。视频图燿优酷地址(具体参考实际'), '8' => array('type' => 'amap', 'name' => '地图详细信息', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。地噿电话+导航详细信息(具体参考实际'), '9' => array('type' => 'bmap', 'name' => '地图详细信息', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。地噿电话+导航+表单详细信息(具体参考实际'), '10' => array('type' => 'bigpic', 'name' => '单图片放', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。地噿电话+导航+表单详细信息(具体参考实际'), '11' => array('type' => 'b2pics', 'name' => '多图展示', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例,张小图，多图展示(具体参考实际'), '12' => array('type' => 't2pics', 'name' => '文字多图介绍', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。图燿文字介绍(具体参考实际'), '13' => array('type' => 'm2yuyue', 'name' => '预约拨号信息', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。预约拨号信忿具体参考实际'), '14' => array('type' => 'intro', 'name' => '多图文字', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。多图文嫿具体参考实际'), '15' => array('type' => 't2btn', 'name' => '按钮链接', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。中下部有文字实现连挿具体参考实际'), '16' => array('type' => 't2share', 'name' => '按钮分享', 'desc' => '场景3：图片全屏显示建议大小640*1008,或者相同比例。中下部有文字点击出现分享图燿具体参考实际'), '17' => array('type' => 't2tel', 'name' => '按钮一键拨', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。中下部有文字实现一键拨卿具体参考实际'), '21' => array('type' => 'map21', 'name' => '地图&多图展示', 'desc' => '场景：图片全屏显示建议大小640*1008,或者相同比例。地噿多图展示(具体参考实际'), '22' => array('type' => 'yuyue22', 'name' => '预约展示', 'desc' => '场景：头部小图展示或者相同比例，个自定义字段，一个时间字段开县具体参考实际'), '31' => array('type' => 'maplink', 'name' => '地图链接', 'desc' => '场景：图片全屏显示建议大小640*1008。链接到地图展示页面，一键导膿具体参考实际'), '32' => array('type' => 'layer32', 'name' => '双层图片展示', 'desc' => '场景：图片全屏显示建议大小640*1008。上下双层图片展示，(具体参考实际'), '33' => array('type' => 'map33', 'name' => '地图文字链接', 'desc' => '场景：图片全屏显示建议大小640*1008。地图文字链接展示，一键导膿具体参考实际'), '34' => array('type' => 'layer34', 'name' => '双层图片展示', 'desc' => '场景：图片全屏显示建议大小640*1008。上下双层图片展示，具体参考实际'), '35' => array('type' => 'pics35', 'name' => '多层图片展示', 'desc' => '场景：图片全屏显示建议大小640*1008。多层图片展示，具体参考实际'));
    public $class_type = array('0' => '', '1' => 'alert-success', '2' => 'alert-info', '3' => 'alert-error');
    public function doMobileshow()
    {
        global $_GPC, $_W;
        $weid = intval($_W['weid']);
        if (empty($weid)) {
            message('抱歉，参数错误！', '', 'error');
        }
        $id = intval($_GPC['id']);
        if (empty($id)) {
            message('抱歉，参数错误！', '', 'error');
        }
        $list = pdo_fetch('SELECT * FROM' . tablename('scene_cube_list') . ' WHERE `id`=:id  AND `weid`=:weid', array(':weid' => $weid, ':id' => $id));
        if (empty($list)) {
            message('场景已经不存在');
        }
        if ($list['start_time'] > $_W['timestamp']) {
            message('' . $list['title'] . '》微场景未开发敬请期待~');
        }
        if ($list['end_time'] < $_W['timestamp']) {
            message('您来迟了,' . $list['title'] . '》微场景已经结束~');
        }
        pdo_update('scene_cube_list', array('hits' => $list['hits'] + 1), array('id' => $list['id']));
        $items = pdo_fetchall('SELECT * FROM' . tablename('scene_cube_page') . ' WHERE `list_id`=:list_id ORDER BY `listorder` desc,id asc', array(':list_id' => $list['id']));
        include $this->template('../' . $list['iden'] . '/show');
    }
    public function doMobilesumbit()
    {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        if (empty($id)) {
            $return = array('data' => 200, 'success' => false, 'message' => '提交数据失败');
            die(json_encode($return));
        }
        $list = pdo_fetch('SELECT * FROM' . tablename('scene_cube_list') . ' WHERE `id`=:id  AND `weid`=:weid', array(':weid' => $_W['weid'], ':id' => $id));
        if (empty($list)) {
            $return = array('data' => 200, 'success' => false, 'message' => '提交数据失败');
            die(json_encode($return));
        }
        include_once 'template/' . $list['iden'] . '/sumbit.php';
    }
    public function doMobilecomment()
    {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        $psize = 0;
        if ($_W['ispost']) {
            if (empty($_GPC['count'])) {
                $insert = array('content' => $_GPC['content'], 'from' => $_GPC['from'], 'weid' => $_W['weid'], 'list_id' => $_GPC['id'], 'create_time' => time(), 'from_user' => $_W['fans']['from_user']);
                pdo_insert('scene_cube_comment', $insert);
                $id = pdo_insertid();
                $return = array('data' => array('id' => $id, 'date' => date('Y-m-d H:i:s')), 'success' => 1, 'message' => '提交成功');
                if ($_GPC['iscomment'] == 0) {
                    pdo_update('scene_cube_list', array('iscomment' => 1), array('id' => $_GPC['id']));
                }
            } else {
                $pindex = intval($_GPC['start']);
                $count = pdo_fetchcolumn('SELECT count(id) FROM' . tablename('scene_cube_comment') . ' WHERE `list_id`=:id', array(':id' => $id));
                $list = pdo_fetchall('SELECT * FROM' . tablename('scene_cube_comment') . ' WHERE `list_id`=:id  order by create_time desc  LIMIT ' . $pindex * $psize . ',10', array(':id' => $id));
                $return = array('success' => 1, 'data' => array('count' => $count));
                foreach ($list as $v) {
                    $return['data']['data'][] = array('id' => $v['id'], 'from' => $v['from'], 'content' => $v['content'], 'date' => date('Y-m-d H:i:s', $v['create_time']));
                }
            }
        }
        echo json_encode($return);
    }
    public function doMobileshare()
    {
        global $_GPC, $_W;
        $weid = intval($_W['weid']);
        if (empty($weid)) {
            message('抱歉，参数错误！', '', 'error');
        }
        $id = intval($_GPC['id']);
        pdo_query('update ' . tablename('scene_cube_list') . ' set shares=shares+1 where id=' . $id);
    }
    public function doMobilemap()
    {
        global $_GPC, $_W;
        include $this->template('map');
    }
    public function doMobilelist()
    {
        global $_GPC, $_W;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 6;
        $condition = '';
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
        }
        $list = pdo_fetchall('SELECT * FROM ' . tablename('scene_cube_list') . " WHERE weid = '{$_W['weid']}' {$condition} ORDER BY  id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        if ($pindex > 1) {
            $return = array();
            foreach ($list as $row) {
                $return[] = array('name' => $row['title'], 'pic_url' => toimage($row['share_thumb']), 'views' => $row['hits'], 'id' => $row['id'], 'link' => $_W['siteroot'] . $this->createMobileUrl('show', array('id' => $row['id'])), 'type' => '1');
            }
            echo json_encode($return);
            die;
        }
        include $this->template('list');
    }
    public function doWebbook()
    {
        global $_W, $_GPC;
        $weid = intval($_W['uniacid']);
        if (empty($weid)) {
            message('抱歉，参数错误！', '', 'error');
        }
        $id = intval($_GPC['id']);
        $ids = $_GPC['idArr'];
        if (empty($id) && empty($ids)) {
            message('抱歉，参数错误！', '', 'error');
        }
        $foo = $_GPC['foo'];
        if ($foo == 'setstatus') {
            pdo_update('scene_cube_book', array('status' => $_GPC['status']), array('id' => intval($_GPC['book_id'])));
            message('状态修改', $this->createWeburl('book', array('id' => $id)), 'success');
        } elseif ($foo == 'delete') {
            $id = $_GPC['id'];
            if (empty($id)) {
                message('参数错误', '', 'error');
            }
            pdo_delete('scene_cube_book', array('id' => $id, 'weid' => $_W['account']['uniacid']));
            message('删除成功', referer(), 'success');
        } elseif ($foo == 'deleteall') {
            foreach ($ids as $k => $v) {
                pdo_delete('scene_cube_book', array('id' => $v, 'weid' => $_W['account']['uniacid']));
            }
            echo json_encode(array('errno' => 0));
            die;
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $condition = '';
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
        }
        $list = pdo_fetchall('SELECT * FROM' . tablename('scene_cube_book') . ' WHERE `weid`=:weid AND list_id=:list_id LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $_W['weid'], ':list_id' => $id));
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('scene_cube_book') . ' WHERE `weid`=:weid AND list_id=:list_id LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $_W['weid'], ':list_id' => $id));
        $pager = pagination($total, $pindex, $psize);
        $s_id = intval($_GPC['s_id']);
        if (!empty($s_id)) {
            $app = pdo_fetch('select iden,title from ' . tablename('scene_cube_app') . ' where id=' . $s_id . '');
        }
        include $this->template('book');
    }
    public function doWebmanager()
    {
        global $_W, $_GPC;
        $weid = $_W['weid'];
        $foo = !empty($_GPC['foo']) ? $_GPC['foo'] : 'display';
        if ($foo == 'create') {
            $step = empty($_GPC['step']) ? 1 : intval($_GPC['step']);
            if ($step == 1) {
                $condition = '';
                if (!empty($_GPC['keyword'])) {
                    $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
                }
                if ($_W['isfounder']) {
                    $list = pdo_fetchall('select * from ' . tablename('scene_cube_app') . ' where 1=1 ' . $condition . ' order by id desc');
                } else {
                    $list = pdo_fetchall('select * from ' . tablename('scene_cube_app') . ' where isshow=1 ' . $condition . ' order by id desc');
                }
                include $this->template('applist');
                die;
            } elseif ($step == 2) {
                if (empty($_GPC['s_id'])) {
                    message('参数错误');
                }
                $s_id = intval($_GPC['s_id']);
                $app = pdo_fetch('select iden,title,price,author,series,thumb,qrcode from ' . tablename('scene_cube_app') . ' where id=' . $s_id . '');
                if (!empty($app['iden'])) {
                    $iden = $app['iden'];
                } else {
                    $iden = $_GPC['iden'];
                }
                if (empty($iden)) {
                    message('场景不存在，请确认操作');
                }
                $isallow = pdo_fetch('select status,appnums from ' . tablename('scene_cube_manage') . '  where weid=:weid AND appid=:appid', array(':weid' => $_W['weid'], ':appid' => $s_id));
                if ($isallow['status'] == 1) {
                    $used = pdo_fetchcolumn('select count(id) from ' . tablename('scene_cube_list') . ' where weid=:weid AND s_id=:s_id', array(':weid' => $_W['weid'], ':s_id' => $s_id));
                    if ($isallow['appnums'] == 0) {
                        $leftnum = 999;
                    } else {
                        $leftnum = $isallow['appnums'] - $used;
                        if ($leftnum < 0) {
                            $leftnum = 0;
                        }
                    }
                    $create_url = $this->createWeburl('post', array('step' => 3, 's_id' => $s_id, 'iden' => $iden));
                }
                if (empty($app['price'])) {
                    $app['price'] = 12000;
                }
                include $this->template($iden . '/item');
                die;
            }
        } elseif ($foo == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }
            $list = pdo_fetchall('SELECT * FROM ' . tablename('scene_cube_list') . " WHERE weid = '{$_W['weid']}' {$condition} ORDER BY  id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('scene_cube_list') . " WHERE weid = '{$_W['weid']}' {$condition}");
            $pager = pagination($total, $pindex, $psize);
        } elseif ($foo = 'delete') {
            $id = intval($_GPC['id']);
            if (empty($id)) {
                message('参数错误', '', 'error');
            }
            $sql = 'SELECT * FROM' . tablename('scene_cube_list') . "WHERE `id`={$id}";
            $list = pdo_fetchall($sql);
            if (empty($list)) {
                message('场景不存在', '', 'error');
            }
            pdo_delete('scene_cube_list', array('id' => $id));
            pdo_delete('scene_cube_page', array('list_id' => $id));
            message('删除成功', referer(), 'success');
        }
        include $this->template('manager');
    }
    public function doWebpost()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $s_id = intval($_GPC['s_id']);
        $app = pdo_fetch('select iden,title from ' . tablename('scene_cube_app') . ' where id=' . $s_id . '');
        if ($app != false) {
            $iden = $app['iden'];
            if (empty($iden)) {
                $iden = $_GPC['iden'];
            }
        }
        if (empty($iden)) {
            message('场景不存在');
        }
        if (empty($iden)) {
            message('场景不存在，请确认操作');
        }
        $id = intval($_GPC['id']);
        if ($id > 0) {
            $item = pdo_fetch('select * from ' . tablename('scene_cube_list') . ' where id=:id AND weid=:weid', array(':weid' => $_W['weid'], ':id' => $id));
        } else {
            $isallow = pdo_fetch('select status,appnums from ' . tablename('scene_cube_manage') . '  where weid=:weid AND appid=:appid', array(':weid' => $_W['weid'], ':appid' => $s_id));
            if ($isallow['status'] == 1) {
                $used = pdo_fetchcolumn('select count(id) from ' . tablename('scene_cube_list') . ' where weid=:weid AND s_id=:s_id', array(':weid' => $_W['weid'], ':s_id' => $s_id));
                if ($isallow['appnums'] == 0) {
                    $leftnum = 999;
                } else {
                    $leftnum = $isallow['appnums'] - $used;
                    if ($leftnum < 0) {
                        $leftnum = 0;
                    }
                }
            }
            if ($leftnum < 1) {
                message('您的授权数量已经用完，请继续购买');
            }
        }
        if ($_W['ispost']) {
            $insert = array('s_id' => $s_id, 'iden' => $iden);
            $fields = array('weid', 'title', 'cover', 'cover1', 'cover2', 'reply_title', 'reply_thumb', 'share_thumb', 'reply_description', 'share_title', 'share_content', 'share_cb_url', 'share_cb_tel', 'first_type', 'first_btn_select', 'first_btn_url', 'first_btn_tel', 'bg_music_switch', 'bg_music_url', 'bg_music_icon', 'cover_title', 'cover_subtitle', 'tongji', 'isshake');
            foreach ($_GPC as $k => $v) {
                if (in_array($k, $fields)) {
                    $insert[$k] = $_GPC[$k];
                }
            }
            if (!empty($_GPC['valid_time'])) {
                list($_s, $_e) = explode('-', $_GPC['valid_time']);
                $insert['start_time'] = strtotime($_s);
                $insert['end_time'] = strtotime($_e);
            } else {
                $insert['start_time'] = time();
                $insert['end_time'] = strtotime('+7 day');
            }
            $insert['weid'] = $_W['weid'];
            if ($item == false) {
                $temp = pdo_insert('scene_cube_list', $insert);
            } else {
                $temp = pdo_update('scene_cube_list', $insert, array('id' => $item['id']));
            }
            if ($temp === false) {
                $this->message('数据提交失败');
            } else {
                $this->message('数据提交成功', $this->createWeburl('manager'), 'success');
            }
        }
        if ($item == false) {
            $item = array('reply_thumb' => $_W['siteroot'] . 'addons/scene_cube/style/img/default_cover.jpg', 'share_tips' => $_W['siteroot'] . 'addons/scene_cube/style/img/default_bg.jpg', 'cover' => $_W['siteroot'] . 'addons/scene_cube/style/img/default_bg.jpg', 'bg_music_url' => $_W['siteroot'] . 'addons/scene_cube/style/mp3/YouGotMe.mp3', 'bg_music_switch' => 1, 'bg_music_icon' => 1, 'start_time' => time(), 'end_time' => strtotime('+7 day'));
        }
        include $this->template($iden . '/post');
    }
    public function doWebdel()
    {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        if (empty($id)) {
            message('参数错误', '', 'error');
        }
        $sql = 'SELECT * FROM' . tablename('scene_cube_list') . "WHERE `id`={$id} AND weid={$_W['weid']}";
        $list = pdo_fetchall($sql);
        if (empty($list)) {
            message('场景不存在', '', 'error');
        }
        pdo_delete('scene_cube_list', array('id' => $id));
        pdo_delete('scene_cube_page', array('list_id' => $id));
        $this->message('删除成功', $this->createWeburl('manager'), 'success');
    }
    public function doWeblistpage()
    {
        global $_W, $_GPC;
        $list_id = intval($_GPC['list_id']);
        if (empty($list_id)) {
            message('参数错误', '', 'error');
        }
        $item = pdo_fetch('select * from ' . tablename('scene_cube_list') . ' where id=:id AND weid=:weid', array(':weid' => $_W['weid'], ':id' => $list_id));
        $list = pdo_fetchall('select * from ' . tablename('scene_cube_page') . ' where list_id=:list_id AND weid=:weid order by listorder asc,id desc', array(':weid' => $_W['weid'], ':list_id' => $list_id));
        if ($item != false) {
            $app = pdo_fetch('select iden,title from ' . tablename('scene_cube_app') . ' where id=' . $item['s_id'] . '');
        }
        include $this->template($item['iden'] . '/list');
    }
    public function doWebpage()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        $weid = $_W['weid'];
        $list_id = intval($_GPC['list_id']);
        if (empty($list_id)) {
            message('参数错误', '', 'error');
        }
        $list = pdo_fetch('select * from ' . tablename('scene_cube_list') . ' where id=:id AND weid=:weid', array(':weid' => $_W['weid'], ':id' => $list_id));
        if ($list != false) {
            $app = pdo_fetch('select iden,title from ' . tablename('scene_cube_app') . ' where id=' . $list['s_id'] . '');
        }
        include_once 'template/' . $list['iden'] . '/page.php';
    }
    public function doWebQuery()
    {
        global $_W, $_GPC;
        $kwd = $_GPC['keyword'];
        $params = array();
        $params[':weid'] = $_W['weid'];
        if (!empty($kwd)) {
            $sql = 'SELECT id,reply_title,reply_thumb,reply_description FROM ' . tablename('scene_cube_list') . ' WHERE `weid`=:weid AND `title` LIKE :reply_title';
            $params[':reply_title'] = "%{$kwd}%";
        } else {
            $sql = 'SELECT id,reply_title,reply_thumb,reply_description FROM ' . tablename('scene_cube_list') . ' WHERE `weid`=:weid';
        }
        $ds = pdo_fetchall($sql, $params);
        foreach ($ds as $k => $row) {
            $r = array();
            $r['title'] = $row['reply_title'];
            $r['description'] = $row['reply_description'];
            $r['thumb'] = toimage($row['reply_thumb']);
            $r['mid'] = $row['id'];
            $ds[$k]['entry'] = $r;
        }
        include $this->template('query');
    }
    public function doWebitemer()
    {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        $weid = $_W['weid'];
		load()->func('file');
        if ($_GPC['foo'] == 'delete') {
            $picid = $_GPC['id'];
            pdo_delete('scene_cube_items', array('id' => $picid));
            file_delete($_GPC['attachment']);
            message('删除成功', referer(), 'success');
        }
        $list = pdo_fetch('SELECT * FROM' . tablename('scene_cube_list') . ' WHERE `id`=:id', array(':id' => $id));
        if (empty($list)) {
            message('参数错误', '', 'error');
        }
        $items = pdo_fetchall('SELECT * FROM' . tablename('scene_cube_items') . ' WHERE `boxid`=:boxid AND `weid` = :weid ORDER BY `index`', array(':boxid' => $id, ':weid' => $weid));
        if (checksubmit()) {
            if (!empty($_GPC['attachment-new'])) {
                foreach ($_GPC['attachment-new'] as $k => $v) {
                    $data = array('weid' => $weid, 'boxid' => intval($_GPC['id']), 'attachment' => $_GPC['attachment-new'][$k], 'index' => intval($_GPC['index-new'][$k]));
                    pdo_insert('scene_cube_items', $data);
                }
            }
            if (!empty($_GPC['attachment'])) {
                foreach ($_GPC['attachment'] as $k => $v) {
                    pdo_update('scene_cube_items', array('index' => $_GPC['index'][$k]), array('id' => $k));
                }
            }
            $itype = $_GPC['itype'];
            foreach ($itype as $k => $v) {
                switch ($v) {
                    case 1:
                        pdo_update('scene_cube_items', array('video' => '', 'lat' => 0, 'lng' => 0, 'video_thumb' => '', 'address' => '', 'tel' => '', 'wechat' => '', 'map_thumb' => '', 'wurl' => ''), array('id' => $k, 'weid' => $_W['weid']));
                        break;
                    case 2:
                        pdo_update('scene_cube_items', array('video' => '', 'lat' => 0, 'lng' => 0, 'video_thumb' => '', 'address' => '', 'tel' => '', 'wechat' => '', 'map_thumb' => '', 'wurl' => ''), array('id' => $k, 'weid' => $_W['weid']));
                        pdo_update('scene_cube_items', array('video' => $_GPC['video'][$k], 'video_thumb' => $_GPC['video_thumb'][$k]), array('id' => $k, 'weid' => $_W['weid']));
                        break;
                    case 3:
                        $wurl = $_GPC['wurl'][$k];
                        if (substr($wurl, 0, 4) != 'http') {
                            $wurl = 'http:' . '\\' . '\\' . $wurl;
                        }
                        pdo_update('scene_cube_items', array('video' => '', 'lat' => 0, 'lng' => 0, 'video_thumb' => '', 'address' => '', 'tel' => '', 'wechat' => '', 'map_thumb' => '', 'wurl' => ''), array('id' => $k, 'weid' => $_W['weid']));
                        pdo_update('scene_cube_items', array('lng' => $_GPC['lng'][$k], 'lat' => $_GPC['lat'][$k], 'address' => $_GPC['address'][$k], 'tel' => $_GPC['tel'][$k], 'wechat' => $_GPC['wechat'][$k], 'map_thumb' => $_GPC['map_thumb'][$k], 'wurl' => $wurl), array('id' => $k, 'weid' => $_W['weid']));
                        break;
                }
            }
            message('操作成功', $this->createWeburl('itemer', array('id' => $id)));
        }
        include $this->template('itemer');
    }
    public function doWebcomment()
    {
        global $_W, $_GPC;
        $op = $_GPC['op'];
        if ($op == 'ok') {
            $id = intval($_GPC['cid']);
            pdo_update('scene_cube_comment', array('status' => 1), array('id' => $id));
            message('审核通过', $this->createWeburl('comment', array('id' => $_GPC['list_id'])));
        } elseif ($op == 'del') {
            $id = intval($_GPC['cid']);
            pdo_delete('scene_cube_comment', array('id' => $id));
            message('删除成功', $this->createWeburl('comment', array('id' => $_GPC['list_id'])));
        } else {
            $list_id = intval($_GPC['id']);
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND content LIKE '%{$_GPC['keyword']}%'";
            }
            $list = pdo_fetchall('SELECT * FROM ' . tablename('scene_cube_comment') . " WHERE list_id = '{$list_id}'  {$condition} ORDER BY  id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('scene_cube_comment') . " WHERE list_id = '{$list_id}'  {$condition}");
            $pager = pagination($total, $pindex, $psize);
            $s_id = intval($_GPC['s_id']);
            if (!empty($s_id)) {
                $app = pdo_fetch('select iden,title from ' . tablename('scene_cube_app') . ' where id=' . $s_id . '');
            }
            include $this->template('comment');
        }
    }
    public function doWebcomment_ajax()
    {
        global $_W, $_GPC;
        $op = $_GPC['op'];
        if ($op == 'sh') {
            $ids = $_GPC['tid'];
            if (is_array($ids)) {
                foreach ($ids as $v) {
                    $id = intval($v);
                    if ($id > 0) {
                        pdo_update('scene_cube_comment', array('status' => 1), array('id' => $id, 'list_id' => $_GPC['list_id']));
                    }
                }
            } else {
                var_dump($ids);
                die;
                pdo_update('scene_cube_comment', array('status' => 1), array('id' => $ids, 'list_id' => $_GPC['list_id']));
            }
        } elseif ($op == 'del') {
            $ids = $_GPC['tid'];
            if (is_array($ids)) {
                foreach ($ids as $v) {
                    $id = intval($v);
                    if ($id > 0) {
                        pdo_delete('scene_cube_comment', array('id' => $id, 'list_id' => $_GPC['list_id']));
                    }
                }
            } else {
                pdo_delete('scene_cube_comment', array('id' => $ids, 'list_id' => $_GPC['list_id']));
            }
        }
        $return = array('errno' => 0, 'error' => '', 'url' => $this->createWeburl('comment', array('list_id' => $list_id, 'list_id' => $_GPC['list_id'], 's_id' => $s_id)));
        echo json_encode($return);
    }
    public function doWebauthor()
    {
        global $_W, $_GPC;
        $session = json_decode(base64_decode($_GPC['__session']), true);
        unset($session['agent']);
        unset($session['lastvisit']);
        unset($session['lastip']);
        $session['weid'] = $_W['weid'];
        $session = base64_encode(json_encode($session));
        include $this->template('author');
    }
    public function doWebapp()
    {
        global $_W, $_GPC;
        load()->func('tpl');
        if ($_W['isfounder']) {
            if ($_GPC['op'] == 'accredit') {
                $appid = intval($_GPC['appid']);
                $app = pdo_fetch('select * from ' . tablename('scene_cube_app') . "  where id={$appid}");
                if ($app == false) {
                    message('场景不存在，或者已经被删除');
                }
                $item = pdo_fetch('select * from ' . tablename('scene_cube_manage') . '  where weid=:weid AND appid=:appid', array(':weid' => $_W['weid'], ':appid' => $appid));
                if ($_W['ispost']) {
                    $insert = array('weid' => $_W['weid'], 'appid' => $appid, 'appnums' => intval($_GPC['appnums']), 'status' => intval($_GPC['status']));
                    if (!empty($_GPC['valid_time'])) {
                        list($_s, $_e) = explode('-', $_GPC['valid_time']);
                        $insert['start_time'] = strtotime($_s);
                        $insert['end_time'] = strtotime($_e);
                    } else {
                        $insert['start_time'] = time();
                        $insert['end_time'] = strtotime('+7 day');
                    }
                    if ($item == false) {
                        $insert['create_time'] = time();
                        $temp = pdo_insert('scene_cube_manage', $insert);
                    } else {
                        $temp = pdo_update('scene_cube_manage', $insert, array('id' => $item['id']));
                    }
                    if ($temp == false) {
                        $this->message('数据保存失败');
                    } else {
                        $this->message('数据保存成功', $this->createWeburl('app'), 'success');
                    }
                }
                if (empty($item['start_time']) || empty($item['end_time'])) {
                    $item['start_time'] = time();
                    $item['end_time'] = strtotime('+7 day');
                }
                if (empty($item['status'])) {
                    $item['status'] = 0;
                }
                if (empty($item['appnums'])) {
                    $item['appnums'] = 0;
                }
            } elseif ($_GPC['op'] == 'post') {
                $id = intval($_GPC['appid']);
                if ($id > 0) {
                    $item = pdo_fetch('select * from ' . tablename('scene_cube_app') . "  where id={$id}");
                }
                if ($_W['ispost']) {
                    $insert = array('listorder' => intval($_GPC['listorder']), 'iden' => $_GPC['iden'], 'price' => intval($_GPC['price']), 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'qrcode' => $_GPC['qrcode'], 'author' => $_GPC['author'], 'series' => $_GPC['series'], 'isshow' => intval($_GPC['isshow']));
                    if ($item == false) {
                        $insert['create_time'] = time();
                        $temp = pdo_insert('scene_cube_app', $insert);
                    } else {
                        $temp = pdo_update('scene_cube_app', $insert, array('id' => $item['id']));
                    }
                    if ($temp == false) {
                        $this->message('数据保存失败');
                    } else {
                        $this->message('数据保存成功', $this->createWeburl('app'), 'success');
                    }
                }
                if ($item == false) {
                    $item = array('listorder' => 0, 'author' => 'Scene App', 'series' => '场景应用');
                }
            } elseif ($_GPC['op'] == 'del') {
                $id = intval($_GPC['id']);
                $list_count = pdo_fetchcolumn('select count(*) from ' . tablename('scene_cube_list') . ' where s_id=' . $id);
                if ($list_count > 0) {
                    message('无法删除，存在场景应用');
                } else {
                    $temp = pdo_delete('scene_cube_app', array('id' => $id));
                    if ($temp == false) {
                        message('删除场景应用成功', $this->createWeburl('app'));
                    } else {
                        message('删除场景应用失败');
                    }
                }
            } elseif ($_GPC['op'] == 'import') {
                $appid = intval($_GPC['appid']);
                $app = pdo_fetch('select iden,title from ' . tablename('scene_cube_app') . "  where id={$appid}");
                if ($app == false) {
                    message('场景不存在，无法导入');
                }
                include_once 'template/' . $app['iden'] . '/import.php';
                die;
            } else {
                $pindex = max(1, intval($_GPC['page']));
                $psize = 10;
                $condition = '';
                if (!empty($_GPC['keyword'])) {
                    $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
                }
                $list = pdo_fetchall('select * from ' . tablename('scene_cube_app') . ' order by id desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize);
                $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('scene_cube_app') . "  {$condition}");
                $pager = pagination($total, $pindex, $psize);
            }
            include $this->template('appmanage');
        } else {
            message('无权操作app管理，请用创世人登录');
        }
    }
    public function doFormatMoney($money)
    {
        $tmp_money = strrev($money);
        $format_money = "";
        for ($i = 3; $i < strlen($money); $i += 3) {
            $format_money .= substr($tmp_money, 0, 3) . ',';
            $tmp_money = substr($tmp_money, 3);
        }
        $format_money .= $tmp_money;
        $format_money = '￥' . strrev($format_money);
        return $format_money;
    }

    private function message($msg, $redirect = '', $type = '')
    {
        global $_W;
        if ($_W['isajax'] || $type == 'ajax') {
            $vars = array();
            if ($type == 'success') {
                $vars['errno'] = 0;
            } else {
                $vars['errno'] = -1;
            }
            $vars['error'] = $msg;
            $vars['url'] = $redirect;
            die(json_encode($vars));
        }
        message($msg, $redirect, $type);
    }
    private function _mtype($_idArr)
    {
        if (empty($_idArr)) {
            return $this->typeArr;
        } else {
            $return = array();
            foreach ($_idArr as $v) {
                $return[$v] = $this->typeArr[$v];
            }
        }
        return $return;
    }
}
?>