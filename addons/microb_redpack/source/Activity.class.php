<?php

class Activity {
    public function create($entity, $gifts = array()) {
        global $_W;
        $rec = array_elements(array('title', 'type', 'start', 'end', 'rules', 'guide', 'banner', 'share', 'limit', 'tag'), $entity);
        $rec['uniacid'] = $_W['uniacid'];
        $rec['amount'] = 0;
        $condition = '`uniacid`=:uniacid AND `title`=:title';
        $pars = array();
        $pars[':uniacid'] = $rec['uniacid'];
        $pars[':title'] = $rec['title'];
        $sql = 'SELECT * FROM ' . tablename('mbrp_activities') . " WHERE {$condition}";
        $exists = pdo_fetch($sql, $pars);
        if(!empty($exists)) {
            return error(-1, '这个活动名称已经使用, 请更换');
        }

        $ret = pdo_insert('mbrp_activities', $rec);
        if(!empty($ret)) {
            $id = pdo_insertid();
            foreach($gifts as $gift) {
                $r = array_elements(array('gift', 'quantity', 'rate'), $gift);
                $r['activity'] = $id;
                pdo_insert('mbrp_activity_gifts', $r);
            }
            return $id;
        }
        return false;
    }

    public function modify($id, $entity, $gifts = array()) {
        global $_W;
        $id = intval($id);
        $rec = array_elements(array('title', 'type', 'start', 'end', 'rules', 'guide', 'banner', 'share', 'limit', 'tag'), $entity);
        $rec['uniacid'] = $_W['uniacid'];
        $condition = '`uniacid`=:uniacid AND `title`=:title AND `actid`!=:id';
        $pars = array();
        $pars[':uniacid'] = $rec['uniacid'];
        $pars[':title'] = $rec['title'];
        $pars[':id'] = $id;
        $sql = 'SELECT * FROM ' . tablename('mbrp_activities') . " WHERE {$condition}";
        $exists = pdo_fetch($sql, $pars);
        if(!empty($exists)) {
            return error(-1, '这个活动名称已经使用, 请更换');
        }

        $ret = pdo_update('mbrp_activities', $rec, array('actid'=>$id, 'uniacid'=>$rec['uniacid']));
        if($ret !== false) {
            $sql = 'DELETE FROM ' . tablename('mbrp_activity_gifts') . " WHERE `activity`='{$id}'";
            pdo_query($sql);
            foreach($gifts as $gift) {
                $r = array_elements(array('gift', 'quantity', 'rate'), $gift);
                $r['activity'] = $id;
                pdo_insert('mbrp_activity_gifts', $r);
            }
        }
        return $ret !== false;
    }

    public function remove($id) {
        global $_W;
        $pars = array();
        $pars[':id'] = $id;
        pdo_query('DELETE FROM ' . tablename('mbrp_activity_gifts') . " WHERE `activity`=:id", $pars);

        $pars[':uniacid'] = $_W['uniacid'];
        pdo_query('DELETE FROM ' . tablename('mbrp_activities') . " WHERE `uniacid`=:uniacid AND `actid`=:id", $pars);
        pdo_query('DELETE FROM ' . tablename('mbrp_records') . " WHERE `uniacid`=:uniacid AND `activity`=:id", $pars);
        return true;
    }

    public function getOne($id) {
        global $_W;
        $condition = '`uniacid`=:uniacid AND `actid`=:id';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':id'] = $id;
        $sql = 'SELECT * FROM ' . tablename('mbrp_activities') . " WHERE {$condition}";
        $entity = pdo_fetch($sql, $pars);
        if(!empty($entity)) {
            $sql = 'SELECT * FROM ' . tablename('mbrp_activity_gifts') . " WHERE `activity`='{$id}'";
            $gifts = pdo_fetchall($sql);
            foreach($gifts as &$gift) {
                $sql = 'SELECT `title`,`type`,`tag` FROM ' . tablename('mbrp_gifts') . " WHERE `id`='{$gift['gift']}'";
                $gg = pdo_fetch($sql);
                $gift['title'] = $gg['title'];
                $gift['type'] = $gg['type'];
                $gift['tag'] = @unserialize($gg['tag']);
            }
            $entity['gifts'] = $gifts;
            $entity['share'] = @unserialize($entity['share']);
            $entity['limit'] = @unserialize($entity['limit']);
            $entity['tag'] = @unserialize($entity['tag']);
        }
        return $entity;
    }

    public function getAll($filters, $pindex = 0, $psize = 20, &$total = 0) {
        global $_W;
        $condition = '`uniacid`=:uniacid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        if(!empty($filters['type'])) {
            $condition .= ' AND `type`=:type';
            $pars[':type'] = $filters['type'];
        }
        if(!empty($filters['title'])) {
            $condition .= ' AND `title` LIKE :title';
            $pars[':title'] = "%{$filters['title']}%";
        }
        $sql = "SELECT * FROM " . tablename('mbrp_activities') . " WHERE {$condition} ORDER BY `start` DESC";
        if($pindex > 0) {
            $sql = "SELECT COUNT(*) FROM " . tablename('mbrp_activities') . " WHERE {$condition}";
            $total = pdo_fetchcolumn($sql, $pars);
            $start = ($pindex - 1) * $psize;
            $sql = "SELECT * FROM " . tablename('mbrp_activities') . " WHERE {$condition} ORDER BY `start` DESC LIMIT {$start},{$pindex}";
        }
        $ds = pdo_fetchall($sql, $pars);
        if(!empty($ds)) {
            foreach($ds as &$row) {
                $sql = "SELECT COUNT(*) FROM " . tablename('mbrp_activity_gifts') . " WHERE `activity`=" . intval($row['actid']);
                $row['gifts'] = pdo_fetchall($sql);
            }
        }
        return $ds;
    }

    public function getRecords($filters, $pindex = 0, $psize = 20, &$total = 0) {
        global $_W;
        $condition = '`r`.`uniacid`=:uniacid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        if(!empty($filters['activity'])) {
            $condition .= ' AND `r`.`activity`=:activity';
            $pars[':activity'] = $filters['activity'];
        }
        if(!empty($filters['owner'])) {
            $condition .= ' AND `r`.`uid`=:owner';
            $pars[':owner'] = $filters['owner'];
        }
        if(!empty($filters['nickname'])) {
            $condition .= ' AND `f`.`nickname` LIKE :nickname';
            $pars[':nickname'] = "%{$filters['nickname']}%";
        }
        if(!empty($filters['status'])) {
            $condition .= ' AND `r`.`status`=:status';
            $pars[':status'] = $filters['status'];
        }

        $fields = "`r`.`id`,`f`.`openid`, `f`.`proxy`, `f`.`nickname`, `f`.`gender`, `f`.`state`, `f`.`city`, `f`.`avatar`, `r`.`uid`, `r`.`activity`, `r`.`gift`, `r`.`fee`, `r`.`log`, `r`.`status`, `r`.`created`, `r`.`completed`";
        $sql = "SELECT {$fields} FROM " . tablename('mbrp_records') . " AS `r` LEFT JOIN " . tablename('mbrp_fans') . " AS `f` ON (`r`.`uid` = `f`.`uid`)";
        $sql .= " WHERE {$condition} ORDER BY `completed` DESC, `created` DESC";
        if($pindex > 0) {
            $sql = "SELECT COUNT(*) FROM " . tablename('mbrp_records') . " AS `r` LEFT JOIN " . tablename('mbrp_fans') . " AS `f` ON (`r`.`uid` = `f`.`uid`)";
            $sql .= " WHERE {$condition}";
            $total = pdo_fetchcolumn($sql, $pars);
            $start = ($pindex - 1) * $psize;
            
            $sql = "SELECT {$fields} FROM" . tablename('mbrp_records') . " AS `r` LEFT JOIN " . tablename('mbrp_fans') . " AS `f` ON (`r`.`uid` = `f`.`uid`)";
            $sql .= " WHERE {$condition} ORDER BY `completed` DESC, `created` DESC LIMIT {$start},{$psize}";
        }
        $ds = pdo_fetchall($sql, $pars);
        if(!empty($ds)) {
            require_once MB_ROOT . '/source/Gift.class.php';
            require_once MB_ROOT . '/source/Fans.class.php';
            $g = new Gift();
            $f = new Fans();
            foreach($ds as &$row) {
                $row['gift'] = $g->getOne($row['gift']);
                $row['profile'] = $f->getProfile($row['uid']);
            }
        }
        return $ds;
    }

    public function queryRecordCode($code) {
        global $_W;
        $condition = "`uniacid`=:uniacid AND `code`=:code";
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':code'] = $code;
        $sql = "SELECT * FROM " . tablename('mbrp_records') . " WHERE {$condition}";
        $rec = pdo_fetch($sql, $pars);
        if(empty($rec)) {
            return error(-1, '消费码错误');
        }
        if($rec['status'] == 'complete') {
            $time = date('Y-m-d H:i', $rec['completed']);
            $rec['error'] = error(-2, "这个消费码已经于 {$time} 使用过了");
        }
        require_once MB_ROOT . '/source/Gift.class.php';
        $g = new Gift();
        $gift = $g->getOne($rec['gift']);
        if(empty($gift)) {
            $rec['error'] = error(-3, '这个消费码的商品已经失效了');
        }
        if($gift['type'] != 'coupon') {
            $rec['error'] = error(-3, '这个消费码的商品属于现金产品, 不能被消费');
        }
        $rec['tag'] = @unserialize($rec['tag']);
        $rec['gift'] = $gift;
        $rec['activity'] = $this->getOne($rec['activity']);
        return $rec;
    }

    public function confirm($id) {
        global $_W;
        $filters = array();
        $filters['uniacid'] = $_W['uniacid'];
        $filters['id'] = $id;
        
        $rec = array();
        $rec['status'] = 'complete';
        $rec['completed'] = TIMESTAMP;
        return pdo_update('mbrp_records', $rec, $filters);
    }

    public function calcCount($id) {
        global $_W;
        $condition = '`activity`=:id';
        $pars = array();
        $pars[':id'] = $id;
        $ret = array();
        $sql = 'SELECT SUM(`quantity`) FROM ' . tablename('mbrp_activity_gifts') . " WHERE {$condition}";
        $ret['total'] = pdo_fetchcolumn($sql, $pars);

        $condition = '`activity`=:id AND `uniacid`=:uniacid AND `gift`!=0';
        $pars = array();
        $pars[':id'] = $id;
        $pars[':uniacid'] = $_W['uniacid'];
        $sql = 'SELECT COUNT(*) FROM ' . tablename('mbrp_records') . " WHERE {$condition}";
        $ret['already'] = pdo_fetchcolumn($sql, $pars);
        $ret['surplus'] = $ret['total'] - $ret['already'];
        return $ret;
    }
    
    public function getRecord($uid, $actid) {
        global $_W;
        $condition = "`uniacid`=:uniacid AND `uid`=:uid AND `activity`=:activity";
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':uid'] = $uid;
        $pars[':activity'] = $actid;
        $sql = 'SELECT * FROM ' . tablename('mbrp_records') . " WHERE {$condition}";
        $rec = pdo_fetch($sql, $pars);
        return $rec;
    }

    public function grap($user, $activity) {
        global $_W;
        $uniacid = $_W['uniacid'];
        $pointer = rand(0, 9999);
        $down = 0;
        $up = 0;
        $hitGift = null;
        foreach($activity['gifts'] as $gift) {
            $up += $gift['rate'] * 100;

            $condition = '`activity`=:id AND `uniacid`=:uniacid AND `gift`=:gift';
            $pars = array();
            $pars[':id'] = $activity['actid'];
            $pars[':uniacid'] = $uniacid;
            $pars[':gift'] = $gift['id'];
            $sql = "SELECT COUNT(*) FROM " . tablename('mbrp_records') . " WHERE {$condition}";
            $count = pdo_fetchcolumn($sql, $pars);
            if($count < $gift['quantity'] && $pointer > $down && $up >= $pointer) {
                $hitGift = $gift;
                break;
            }
            $down = $up;
        }
        $r = array();
        $r['uniacid'] = $uniacid;
        $r['uid'] = $user['uid'];
        $r['activity'] = $activity['actid'];
        $r['log'] = '';
        $r['created'] = TIMESTAMP;
        $r['completed'] = 0;

        if(empty($hitGift)) {
            $r['fee'] = '0';
            $r['gift'] = '0';
            $r['status'] = 'none';
        } else {
            if($hitGift['type'] == 'cash') {
                $fee = rand($hitGift['tag']['downline'] * 100, $hitGift['tag']['upline'] * 100);
                $r['fee'] = sprintf('%.2f', $fee / 100);
                
                //记录总数
                $sql = "UPDATE " . tablename('mbrp_activities') . ' SET `amount`=`amount`+:amount WHERE `actid`=:actid';
                $pars = array();
                $pars[':amount'] = floatval($r['fee']);
                $pars[':actid'] = $activity['actid'];
                pdo_query($sql, $pars);
            } else {
                $r['fee'] = $this->generateCode();
            }
            $r['gift'] = $hitGift['gift'];
            $r['status'] = 'created';
        }

        $ret = pdo_insert('mbrp_records', $r);
        if(empty($ret)) {
            return error(-2, '红包领取失败, 请稍后来重试');
        } else {
            $r['id'] = pdo_insertid();
            return $r;
        }
    }

    public function generateCode() {
        global $_W;
        do{
            $code = random(10, true);
            $condition = '`uniacid`=:uniacid AND `code`=:code';
            $pars = array();
            $pars[':uniacid'] = $_W['uniacid'];
            $pars[':code'] = $code;
            $sql = "SELECT * FROM " . tablename('mbrp_records') . " WHERE {$condition}";
            $exists = pdo_fetch($sql, $pars);
        } while(!empty($exists));
        return $code;
    }
}