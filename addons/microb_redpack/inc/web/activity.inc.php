<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;

if(!pdo_tableexists('mbrp_gifts')) {
    message('本次模块更新内容较多, 需要将模块卸载后重新安装');
}
$this->checkLicense();
$modulePublic = '../addons/microb_redpack/static/';
$foo = $_GPC['foo'];
$foos = array('list', 'create', 'modify', 'delete', 'records');
$foo = in_array($foo, $foos) ? $foo : 'list';
require_once MB_ROOT . '/source/Activity.class.php';

if($foo == 'create') {
    if($_W['ispost']) {
        $input = $_GPC;
        $input['rules'] = htmlspecialchars_decode($input['rules']);
        $input['start'] = strtotime($input['time']['start'] . ':00');
        $input['end'] = strtotime($input['time']['end'] . ':59');
        $input['share'] = serialize($input['share']);
        $input['limit'] = serialize($input['limit']);
        if($input['type'] == 'game') {
            $input['tag'] = serialize($input['game']);
        } elseif($input['type'] == 'shared') {
            $input['tag'] = serialize($input['shared']);
        } else {
            $input['tag'] = serialize($input['tag']);
        }
        $gifts = array();
        foreach($input['gifts']['id'] as $k => $v) {
            $gifts[] = array(
                'gift'      => $v,
                'quantity'  => $input['gifts']['quantity'][$k],
                'rate'      => $input['gifts']['rate'][$k]
            );
        }

        $a = new Activity();
        $ret = $a->create($input, $gifts);
        if(is_error($ret)) {
            message($ret['message']);
        } else {
            message("成功创建活动", $this->createWebUrl('activity'));
        }
    }
    $activity = array();
    $time = array();
    $time['start'] = date('Y-m-d 00:00');
    $time['end'] = date('Y-m-d 15:00');
    $activity['gifts'] = array();
    $activity['type'] = 'shared';

    load()->func('tpl');
    include $this->template('activity-form');
}

if($foo == 'modify') {
    $id = $_GPC['id'];
    $id = intval($id);
    $a = new Activity();
    $activity = $a->getOne($id);
    if(empty($activity)) {
        $this->error('访问错误');
    }
    if($_W['ispost']) {
        $input = $_GPC;
        $input['rules'] = htmlspecialchars_decode($input['rules']);
        $input['start'] = strtotime($input['time']['start'] . ':00');
        $input['end'] = strtotime($input['time']['end'] . ':59');
        $input['share'] = serialize($input['share']);
        $input['limit'] = serialize($input['limit']);
        if($input['type'] == 'game') {
            $input['tag'] = serialize($input['game']);
        } elseif($input['type'] == 'shared') {
            $input['tag'] = serialize($input['shared']);
        } else {
            $input['tag'] = serialize($input['tag']);
        }
        $gifts = array();
        foreach($input['gifts']['id'] as $k => $v) {
            $gifts[] = array(
                'gift'      => $v,
                'quantity'  => $input['gifts']['quantity'][$k],
                'rate'      => $input['gifts']['rate'][$k]
            );
        }

        $a = new Activity();
        $ret = $a->modify($id, $input, $gifts);
        if(is_error($ret)) {
            message($ret['message']);
        } else {
            message("成功编辑活动", $this->createWebUrl('activity'));
        }
    }
    $time = array();
    $time['start'] = date('Y-m-d H:i', $activity['start']);
    $time['end'] = date('Y-m-d H:i', $activity['end']);
    if($activity['type'] == 'game') {
        $game = $activity['tag'];
    } elseif($activity['type'] == 'shared') {
        $shared = $activity['tag'];
    }
    load()->func('tpl');
    include $this->template('activity-form');
}

if($foo == 'records') {
    $id = $_GPC['id'];
    $id = intval($id);
    $a = new Activity();
    $activity = $a->getOne($id);
    if(empty($activity)) {
        $this->error('访问错误');
    }
    $filters = array();
    $filters['activity'] = $id;
    $filters['nickname'] = $_GPC['nickname'];
    
    $pindex = intval($_GPC['page']);
    $pindex = max($pindex, 1);
    $psize = 15;
    $total = 0;

    $ds = $a->getRecords($filters, $pindex, $psize, $total);
    $pager = pagination($total, $pindex, $psize);
    
    include $this->template('activity-records');
}

if($foo == 'delete') {
    $id = $_GPC['id'];
    $id = intval($id);
    $a = new Activity();
    $ret = $a->remove($id);
    if(is_error($ret)) {
        message($ret['message']);
    } else {
        message('操作成功', $this->createWebUrl('activity'));
    }
}

if($foo == 'list') {
    $a = new Activity();
    $ds = $a->getAll(array());
    if(is_array($ds)) {
        foreach($ds as &$row) {
            $url = $this->createMobileUrl('activity', array('actid' => $row['actid']));
            $row['surl'] = $url;
            $url = substr($url, 2);
            $url = $_W['siteroot'] . 'app/' . $url;
            $row['url'] = $url;
            $row['count'] = $a->calcCount($row['actid']);
        }
        unset($row);
    }
    
    include $this->template('activity-list');
}
?>