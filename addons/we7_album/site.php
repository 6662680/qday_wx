<?php

/**
 * @author WeEngine Team
 */
defined('IN_IA') or exit('Access Denied');

class We7_albumModuleSite extends WeModuleSite {

    public function doMobileDetailMore() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $list = pdo_fetchall("SELECT * FROM " . tablename('album_photo') . " WHERE albumid = :albumid ORDER BY displayorder DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':albumid' => $id));
        include $this->template('detail_more');
    }

    public function doMobileDetail() {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        $album = pdo_fetch("SELECT * FROM " . tablename('album') . " WHERE id = :id", array(':id' => $id));
        if (empty($album)) {
            message('相册不存在或是已经被删除！');
        }
        $_W['styles']  = $this->module['config']['album'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $result['list'] = pdo_fetchall("SELECT * FROM " . tablename('album_photo') . " WHERE albumid = :albumid ORDER BY displayorder DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':albumid' => $album['id']));
        $url = "app/index.php?c=entry&m=we7_album&do=detail&id={$album['id']}&i={$_W['uniacid']}";
        //360全景
        if ($album['type'] == 1 && $_GPC['gettype'] == 'xml') {
            $result['list'] = pdo_fetchall("SELECT * FROM " . tablename('album_photo') . " WHERE albumid = :albumid ORDER BY displayorder ASC", array(':albumid' => $album['id']));
            header("Content-type: text/xml");
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
			<panorama id="" hideabout="1">
				<view fovmode="0" pannorth="0">
					<start pan="5.5" fov="80" tilt="1.5"/>
					<min pan="0" fov="80" tilt="-90"/>
					<max pan="360" fov="80" tilt="90"/>
				</view>
				<userdata title="" datetime="2013:05:23 21:01:02" description="" copyright="" tags="" author="" source="" comment="" info="" longitude="" latitude=""/>
				<hotspots width="180" height="20" wordwrap="1">
					<label width="180" backgroundalpha="1" enabled="1" height="20" backgroundcolor="0xffffff" bordercolor="0x000000" border="1" textcolor="0x000000" background="1" borderalpha="1" borderradius="1" wordwrap="1" textalpha="1"/>
					<polystyle mode="0" backgroundalpha="0.2509803921568627" backgroundcolor="0x0000ff" bordercolor="0x0000ff" borderalpha="1"/>
				</hotspots>
				<media/>
				<input tilesize="700" tilescale="1.014285714285714" tile0url="' . $_W['attachurl'] . $result['list']['0']['attachment'] . '" tile1url="' . $_W['attachurl'] . $result['list']['1']['attachment'] . '" tile2url="' . $_W['attachurl'] . $result['list']['2']['attachment'] . '" tile3url="' . $_W['attachurl'] . $result['list']['3']['attachment'] . '" tile4url="' . $_W['attachurl'] . $result['list']['4']['attachment'] . '" tile5url="' . $_W['attachurl'] . $result['list']['5']['attachment'] . '"/>
				<autorotate speed="0.200" nodedelay="0.00" startloaded="1" returntohorizon="0.000" delay="5.00"/>
				<control simulatemass="1" lockedmouse="0" lockedkeyboard="0" dblclickfullscreen="0" invertwheel="0" lockedwheel="0" invertcontrol="1" speedwheel="1" sensitivity="8"/>
			</panorama>';
            return $xml;
        }
        include $this->template('detail');
    }

    public function doWebList() {
        global $_W, $_GPC;
        load()->func('file');
        $foo = !empty($_GPC['foo']) ? $_GPC['foo'] : 'display';
        $category = pdo_fetchall("SELECT * FROM " . tablename('album_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        if (!empty($category)) {
            $children = '';
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        }
        load()->func('tpl');
        if ($foo == 'create') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $sql = 'SELECT * FROM ' . tablename('album') . ' WHERE `id` = :id AND `weid` = :weid';
                $params = array(':id' => $id, ':weid' => $_W['uniacid']);
                $item = pdo_fetch($sql, $params);
                if (empty($item)) {
                    message('抱歉，相册不存在或是已经删除！', '', 'error');
                }
            }
            if (checksubmit('submit')) {
                if (empty($_GPC['title'])) {
                    message('请输入相册名称！');
                }
                $data = array(
                    'weid' => $_W['uniacid'],
                    'title' => $_GPC['title'],
                    'content' => $_GPC['content'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'isview' => intval($_GPC['isview']),
                    'type' => intval($_GPC['type']),
                    'thumb' => $_GPC['thumb'],
                    'pcate' => intval($_GPC['pcate']),
                    'ccate' => intval($_GPC['ccate'])
                );
                if (empty($id)) {
                    $data['createtime'] = TIMESTAMP;
                    pdo_insert('album', $data);
                } else {
                    pdo_update('album', $data, array('id' => $id));
                }
                message('相册更新成功！', $this->createWebUrl('list', array('foo' => 'display')), 'success');
            }
            include $this->template('album');

        } elseif ($foo == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 12;
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }
            if (!empty($_GPC['cate_2'])) {
                $cid = intval($_GPC['cate_2']);
                $condition .= " AND ccate = '{$cid}'";
            } elseif (!empty($_GPC['cate_1'])) {
                $cid = intval($_GPC['cate_1']);
                $condition .= " AND pcate = '{$cid}'";
            }
            if (istrlen($_GPC['isview']) > 0) {
                $condition .= " AND isview = '" . intval($_GPC['isview']) . "'";
            }
            $list = pdo_fetchall("SELECT * FROM " . tablename('album') . " WHERE weid = '{$_W['uniacid']}' $condition ORDER BY displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('album') . " WHERE weid = '{$_W['uniacid']}' $condition");
            $pager = pagination($total, $pindex, $psize);
            if (!empty($list)) {
                foreach ($list as &$row) {
                    $row['total'] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('album_photo') . " WHERE albumid = :albumid", array(':albumid' => $row['id']));
                }
            }
            include $this->template('album');
        } elseif ($foo == 'photo') {
            $id = intval($_GPC['albumid']);
            $sql = 'SELECT * FROM ' . tablename('album') . ' WHERE `id` = :id AND `weid` = :weid';
            $params = array(':id' => $id, ':weid' => $_W['uniacid']);
            $album = pdo_fetch($sql, $params);
            if (empty($album)) {
                message('相册不存在或是已经被删除！');
            }
            if (checksubmit('submit')) {
                if (!empty($_GPC['attachment-new'])) {
                    foreach ($_GPC['attachment-new'] as $index => $row) {
                        if (empty($row)) {
                            continue;
                        }
                        $data = array(
                            'weid' => $_W['uniacid'],
                            'albumid' => intval($_GPC['albumid']),
                            'title' => $_GPC['title-new'][$index],
                    
                            'description' => $_GPC['description-new'][$index],
                            'attachment' => $_GPC['attachment-new'][$index],
                            'displayorder' => $_GPC['displayorder-new'][$index],
                        );
                        pdo_insert('album_photo', $data);
                    }
                }
                if (!empty($_GPC['attachment'])) {
                    foreach ($_GPC['attachment'] as $index => $row) {
                        if (empty($row)) {
                            continue;
                        }
                        $data = array(
                            'weid' => $_W['uniacid'],
                            'albumid' => intval($_GPC['albumid']),
                            'title' => $_GPC['title'][$index],
                            'description' => $_GPC['description'][$index],
                            'attachment' => $_GPC['attachment'][$index],
                            'displayorder' => $_GPC['displayorder'][$index],
                        );
                        pdo_update('album_photo', $data, array('id' => $index));
                    }
                }
                message('相册更新成功！', $this->createWebUrl('list', array('foo' => 'photo', 'albumid' => $album['id'])));
            }

            if ($album['type'] == 0) {
                $photos = pdo_fetchall("SELECT * FROM " . tablename('album_photo') . " WHERE albumid = :albumid ORDER BY displayorder DESC", array(':albumid' => $album['id']));
            } else {
                $photos = pdo_fetchall("SELECT * FROM " . tablename('album_photo') . " WHERE albumid = :albumid ORDER BY displayorder ASC", array(':albumid' => $album['id']));
            }
            include $this->template('album');
        } elseif ($foo == 'delete') {
            $type = $_GPC['type'];
            $id = intval($_GPC['id']);
            if ($type == 'photo') {
                if (!empty($id)) {
                    $sql = 'SELECT `id`, `attachment` FROM ' . tablename('album_photo') . ' WHERE `id` = :id AND `weid` = :weid';
                    $params = array(':id' => $id, ':weid' => $_W['uniacid']);
                    $item = pdo_fetch($sql, $params);
                    if (empty($item)) {
                        message('图片不存在或是已经被删除！');
                    }
                    pdo_delete('album_photo', array('id' => $item['id']));
                } else {
                    $item['attachment'] = $_GPC['attachment'];
                }
                file_delete($item['attachment']);
            } elseif ($type == 'album') {
                $sql = 'SELECT * FROM ' . tablename('album') . ' WHERE `id` = :id AND `weid` = :weid';
                $params = array(':id' => $id, ':weid' => $_W['uniacid']);
                $album = pdo_fetch($sql, $params);
                if (empty($album)) {
                    message('相册不存在或是已经被删除！');
                }
                $photos = pdo_fetchall("SELECT id, attachment FROM " . tablename('album_photo') . " WHERE albumid = :albumid", array(':albumid' => $id));
                if (!empty($photos)) {
                    foreach ($photos as $row) {
                        file_delete($row['attachment']);
                    }
                }
                pdo_delete('album', array('id' => $id));
                pdo_delete('album_photo', array('albumid' => $id));
            }
            message('删除成功！', referer(), 'success');
        } elseif ($foo == 'cover') {
            $id = intval($_GPC['albumid']);
            $attachment = $_GPC['thumb'];
            if (empty($attachment)) {
                 message('抱歉，参数错误，请重试！', '', 'error');
            }
            $sql = 'SELECT * FROM ' . tablename('album') . ' WHERE `id` = :id AND `weid` = :weid';
            $params = array(':id' => $id, ':weid' => $_W['uniacid']);
            $item = pdo_fetch($sql, $params);
            if (empty($item)) {
                message('抱歉，相册不存在或是已经删除！', '', 'error');
            }
            pdo_update('album', array('thumb' => $attachment), array('id' => $id));
            message('设置封面成功！', '', 'success');
        }
    }

    public function doWebQuery() {
        global $_W, $_GPC;
        $kwd = $_GPC['keyword'];
        $sql = 'SELECT * FROM ' . tablename('album') . ' WHERE `weid`=:weid AND `title` LIKE :title';
        $params = array();
        $params[':weid'] = $_W['uniacid'];
        $params[':title'] = "%{$kwd}%";
        $ds = pdo_fetchall($sql, $params);
        foreach ($ds as &$row) {
            $r = array();
            $r['id'] = $row['id'];
            $r['title'] = $row['title'];
            $r['content'] = cutstr($row['content'], 30, '...');
            $r['thumb'] = toimage( $row['thumb'] );
            $row['entry'] = $r;
        }
        include $this->template('query');
    }

    public function doWebDelete() {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        pdo_delete('album_reply', array('id' => $id));
        message('删除成功！', referer(), 'success');
    }

    public function doMobileList() {
        global $_W, $_GPC;
        $_W['styles']  = $this->module['config']['album'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        
        $pcate = $_GPC['pcate'];
        $ccate = $_GPC['ccate'];
        $show_category   = true;
        if($pcate=='' && $ccate==''){
                $category = pdo_fetchall("SELECT * FROM " . tablename('album_category') . " WHERE weid = '{$_W['uniacid']}' and parentid=0 and enabled=1 ORDER BY displayorder DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
                if (!empty($category)) {
                    $children = '';
                    foreach ($category as &$cate) {
                        if(empty($cate['parentid'])){
                              $cate['url'] = $this->createMobileUrl('list',array('pcate'=>$cate['id']));
                        }
                        else{
                              $cate['url'] = $this->createMobileUrl('list',array('pcate'=>$cate['parentid'],'ccate'=>$cate['id']));
                        }
                       
                     
                        $children = pdo_fetchall("SELECT * FROM " . tablename('album_category') . " WHERE parentid={$cate['id']} and enabled=1 ORDER BY displayorder desc");
                        foreach ($children as &$c) {
                            $c['url'] = $this->createMobileUrl('list',array('pcate'=>$c['parentid'],'ccate'=>$c['id']));
                          }
                        unset($c);
                        $cate['children'] = $children;
                       
                    }   
                    unset($cate);
                }
        }
        else {
            $condition = "";
            $ccate = intval($_GPC['ccate']);
            $pcate = intval($_GPC['pcate']);
            if(!empty($pcate) && !empty($ccate)){
               $condition.="  and pcate={$pcate} and ccate={$ccate} ";    
            }
            else if(!empty($pcate)){
                $condition.="  and pcate={$pcate}";
            }
            else if(!empty($ccate)){
                 $condition.="  and ccate={$ccate} ";
            }
            $pc = pdo_fetchcolumn("select name from " . tablename('album_category') . " WHERE id=:id limit 1",array(':id'=>$pcate));
            $cc = pdo_fetchcolumn("select name from " . tablename('album_category') . " WHERE id=:id limit 1",array(':id'=>$ccate));
           
            $sql = "SELECT * FROM " . tablename('album') . " WHERE weid = '{$_W['uniacid']}' AND isview = '1' $condition ORDER BY displayorder DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
            $list  = pdo_fetchall($sql);
 
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('album') . " WHERE weid = '{$_W['uniacid']}' AND isview = '1' $condition");
            $pager = pagination($total, $pindex, $psize);
            $show_category   = false;
        }
        include $this->template('list');
    }
 public function doMobileListMore() {
        global $_GPC, $_W;
       
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
         $category = pdo_fetchall("SELECT * FROM " . tablename('album_category') . " WHERE weid = '{$_W['uniacid']}' and parentid=0 and enabled=1 ORDER BY displayorder DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
                if (!empty($category)) {
                    $children = '';
                    foreach ($category as &$cate) {
                        if(empty($cate['parentid'])){
                              $cate['url'] = $this->createMobileUrl('list',array('pcate'=>$cate['id']));
                        }
                        else{
                              $cate['url'] = $this->createMobileUrl('list',array('pcate'=>$cate['parentid'],'ccate'=>$cate['id']));
                        }
                       
                     
                        $children = pdo_fetchall("SELECT * FROM " . tablename('album_category') . " WHERE parentid={$cate['id']} and enabled=1 ORDER BY displayorder desc");
                        foreach ($children as &$c) {
                            $c['url'] = $this->createMobileUrl('list',array('pcate'=>$c['parentid'],'ccate'=>$c['id']));
                          }
                        unset($c);
                        $cate['children'] = $children;
                       
                    }   
                    unset($cate);
                }
        include $this->template('list_more');
    }
    public function getAlbumTiles() {
        global $_W;
        $urls = array();
        $albums = pdo_fetchall("SELECT id, title FROM " . tablename('album') . " WHERE isview = '1' AND weid = '{$_W['uniacid']}'");
        if (!empty($albums)) {
            foreach ($albums as $row) {
                $urls[] = array('title' => $row['title'], 'url' => $this->createMobileUrl('detail', array('id' => $row['id'])));
            }
        }
        $category  = pdo_fetchall("SELECT id, name,parentid FROM " . tablename('album_category') . " WHERE weid = '{$_W['uniacid']}'");
        if (!empty($category)) {
            foreach ($category as $row) {
                if(empty($row['parentid'])){
                    $urls[] = array('title' =>"分类: ". $row['name'], 'url' => $this->createMobileUrl('list', array('pcate'=>$row['id'])));
                }
                else{
                    $urls[] = array('title' =>"分类: ". $row['name'], 'url' => $this->createMobileUrl('list', array('pcate'=>$row['parentid'],'ccate'=>$row['id'])));
                }
            }
        }
        return $urls;
    }

    public function doWebCategory() {
        global $_GPC, $_W;
           load()->func('tpl');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update('album_category', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
            }
            $children = array();
            $category = pdo_fetchall("SELECT * FROM " . tablename('album_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
            foreach ($category as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($category[$index]);
                }
            }
            include $this->template('category');
        } elseif ($operation == 'post') {
            $parentid = intval($_GPC['parentid']);
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $category = pdo_fetch("SELECT * FROM " . tablename('album_category') . " WHERE id = '$id'");
                
                if( empty($category['parentid'])){
                    $url = "../app/index.php?c=entry&m=we7_album&do=list&pcate={$category['id']}&i={$_W['uniacid']}";
                            
                }
                else{
                    $url = "../app/index.php?c=entry&m=we7_album&do=list&pcate={$category['parentid']}&ccate={$category['id']}&i={$_W['uniacid']}";
                }
            } else {
                $category = array(
                    'displayorder' => 0,
                );
            }
            if (!empty($parentid)) {
                $parent = pdo_fetch("SELECT id, name FROM " . tablename('album_category') . " WHERE id = '$parentid'");
                if (empty($parent)) {
                    message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
                }
            }
            if (checksubmit('submit')) {
                if (empty($_GPC['catename'])) {
                    message('抱歉，请输入分类名称！');
                }
                $data = array(
                    'weid' => $_W['uniacid'],
                    'name' => $_GPC['catename'],
                    'enabled' => intval($_GPC['enabled']),
                    'displayorder' => intval($_GPC['displayorder']),
             
                    'description' => $_GPC['description'],
                    'parentid' => intval($parentid),
                    'thumb'=>$_GPC['thumb']
                );
              

                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update('album_category', $data, array('id' => $id));
                } else {
                    pdo_insert('album_category', $data);
                    $id = pdo_insertid();
                }
                message('更新分类成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
            }
            include $this->template('category');
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $category = pdo_fetch("SELECT id, parentid FROM " . tablename('album_category') . " WHERE id = '$id'");
            if (empty($category)) {
                message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display')), 'error');
            }
            pdo_delete('album_category', array('id' => $id, 'parentid' => $id), 'OR');
            message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
        }
    }

}
