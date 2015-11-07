<?php

class Game {

    public function create($entity) {
        global $_W;
        $rec = array_elements(array('activity', 'uid'), $entity);
        $rec['uniacid'] = $_W['uniacid'];
        $rec['item'] = 'key';
        $rec['status'] = 'created';
        $rec['created'] = TIMESTAMP;
        $rec['completed'] = 0;
        
        $ret = pdo_insert('mbrp_trades', $rec);
        if(!empty($ret)) {
            return pdo_insertid();
        } else {
            return error(-1, '数据保存失败, 请稍后重试');
        }
    }
    
    public function touch($id, $status) {
        $rec = array();
        $rec['status'] = $status;
        if($status == 'paid') {
            $rec['paid'] = TIMESTAMP;
        }
        if($status == 'completed') {
            $rec['completed'] = TIMESTAMP;
        }
        
        $ret = pdo_update('mbrp_trades', $rec, array('id'=>$id));
        if($ret !== false) {
            return true;
        }else{
            return error(-1, '数据更新失败, 请稍后重试');
        }
    }
    
    public function pool($actid, $increment) {
        require_once MB_ROOT . '/source/Activity.class.php';
        $a = new Activity();
        $activity = $a->getOne($actid);
        if(!empty($activity)) {
            $game = $activity['tag'];
            $game['pool'] += floatval($increment);
            $rec = array();
            $rec['tag'] = serialize($game);
            pdo_update('mbrp_activities', $rec, array('actid'=>$actid));
        }
    }

    public function calcQuantity($actid, $uid) {
        global $_W;
        $sql = "SELECT COUNT(*) FROM " . tablename('mbrp_trades') . " WHERE `uniacid`=:uniacid AND `activity`=:activity AND `uid`=:uid AND `status`='paid'";
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':activity'] = $actid;
        $pars[':uid'] = $uid;

        $ret = pdo_fetchcolumn($sql, $pars);
        return intval($ret);
    }

    public function pay($tid) {
        $trade = $this->getOne($tid);
        if(!empty($trade) && $trade['status'] == 'created') {
            $this->touch($tid, 'paid');
            require_once MB_ROOT . '/source/Activity.class.php';
            $a = new Activity();
            $activity = $a->getOne($trade['activity']);
            $this->pool($trade['activity'], $activity['tag']['price']);
        }
    }

    public function getOne($id, $filters = array()) {
        global $_W;
        $sql = "SELECT * FROM " . tablename('mbrp_trades') . " WHERE `uniacid`=:uniacid AND `id`=:id";
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':id'] = $id;
        if(!empty($filters['uid'])) {
            $sql .= " AND `uid`=:uid";
            $pars[':uid'] = $filters['uid'];
        }
        if(!empty($filters['activity'])) {
            $sql .= " AND `activity`=:activity";
            $pars[':activity'] = $filters['activity'];
        }
        
        $ret= pdo_fetch($sql, $pars);
        if(!empty($ret)) {
            return $ret;
        }else{
            return array();
        }
    }

    public function getAll($filters = array(), $pindex = 0, $psize = 15, &$total = 0) {
        global $_W;
        $condition = '`uniacid`=:uniacid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        if(!empty($filters['uid'])) {
            $condition .= " AND `uid`=:uid";
            $pars[':uid'] = $filters['uid'];
        }
        if(!empty($filters['activity'])) {
            $condition .= " AND `activity`=:activity";
            $pars[':activity'] = $filters['activity'];
        }
        if(!empty($filters['status'])) {
            $condition .= " AND `status`=:status";
            $pars[':status'] = $filters['status'];
        }
        $sql = 'FROM ' . tablename('mbrp_trades') . " WHERE {$condition}";
        if($pindex > 0){
            $total = pdo_fetchcolumn("SELECT COUNT(*) {$sql}", $pars);
            $start = ($pindex - 1) * $psize;
            $sql .= " ORDER BY `id` DESC LIMIT {$start},{$psize}";
            $ds = pdo_fetchall("SELECT * {$sql}", $pars);
        } else {
            $sql .= " ORDER BY `id` DESC";
            $ds = pdo_fetchall("SELECT * {$sql}", $pars);
        }
        return $ds;
    }
    
    public function getTrades($filters, $pindex = 0, $psize = 20, &$total = 0) {
        global $_W;
        $condition = '`t`.`uniacid`=:uniacid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        if(!empty($filters['activity'])) {
            $condition .= ' AND `t`.`activity`=:activity';
            $pars[':activity'] = $filters['activity'];
        }
        if(!empty($filters['owner'])) {
            $condition .= ' AND `t`.`uid`=:owner';
            $pars[':owner'] = $filters['owner'];
        }
        if(!empty($filters['nickname'])) {
            $condition .= ' AND `f`.`nickname` LIKE :nickname';
            $pars[':nickname'] = "%{$filters['nickname']}%";
        }
        if(!empty($filters['status'])) {
            if($filters['status'] == 'valid') {
                $condition .= " AND `t`.`status`!='created'";
            } else {
                $condition .= ' AND `t`.`status`=:status';
                $pars[':status'] = $filters['status'];
            }
        }

        $fields = "`t`.`id`,`f`.`openid`, `f`.`proxy`, `f`.`nickname`, `f`.`gender`, `f`.`state`, `f`.`city`, `f`.`avatar`, `t`.`uid`, `t`.`activity`, `t`.`item`, `t`.`paid`, `t`.`status`, `t`.`created`, `t`.`completed`";
        $sql = "SELECT {$fields} FROM " . tablename('mbrp_trades') . " AS `t` LEFT JOIN " . tablename('mbrp_fans') . " AS `f` ON (`t`.`uid` = `f`.`uid`)";
        $sql .= " WHERE {$condition} ORDER BY `completed` DESC, `created` DESC";
        if($pindex > 0) {
            $sql = "SELECT COUNT(*) FROM " . tablename('mbrp_trades') . " AS `t` LEFT JOIN " . tablename('mbrp_fans') . " AS `f` ON (`t`.`uid` = `f`.`uid`)";
            $sql .= " WHERE {$condition}";
            $total = pdo_fetchcolumn($sql, $pars);
            $start = ($pindex - 1) * $psize;

            $sql = "SELECT {$fields} FROM" . tablename('mbrp_trades') . " AS `t` LEFT JOIN " . tablename('mbrp_fans') . " AS `f` ON (`t`.`uid` = `f`.`uid`)";
            $sql .= " WHERE {$condition} ORDER BY `completed` DESC, `created` DESC LIMIT {$start},{$psize}";
        }
        $ds = pdo_fetchall($sql, $pars);
        if(!empty($ds)) {
            require_once MB_ROOT . '/source/Fans.class.php';
            $f = new Fans();
            foreach($ds as &$row) {
                $row['profile'] = $f->getProfile($row['uid']);
            }
        }
        return $ds;
    }
}