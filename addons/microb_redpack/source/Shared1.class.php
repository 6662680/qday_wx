<?php

/*
 * 分享操作
 */
class Shared {

    /**
     * 增加一次助力记录
     *      如果from, to 已经存在, 失败
     *   id uniacid from to dateline  
     * 
     * @param $entity 助力结构 from, to, dateline
     * @return int|error
     */
    public function createHelp($entity) {
        global $_W;
        $rec = array_elements(array('from', 'to', 'dateline'), $entity);
        $rec['uniacid'] = $_W['uniacid'];
        
        require_once MB_ROOT . '/source/Fans.class.php';
        $f = new Fans();
        $fromExists = $f->getOne($entity['from']);
        $toExists = $f->getOne($entity['to']);
        
        if(!empty($fromExists) && !empty($toExists)){
            $pars = array();
            $pars[':uniacid'] = $_W['uniacid'];
            $pars[':from'] = $rec['from'];
            $pars[':to'] = $rec['to'];
            $sqll = 'SELECT * FROM ' . tablename('mbrp_helps') . ' WHERE `uniacid`=:uniacid AND `from`=:from AND `to` =:to';
            $exists = pdo_fetch($sqll, $pars);
            if(!empty($exists)) {
                return error(-3, '已经助力过了');
            }
            $ret = pdo_insert('mbrp_helps', $rec);
            if(!empty($ret)) {
                return pdo_insertid();
            } else {
                return error(-2, '数据保存失败, 请稍后重试');
            }
        } else {
            return error(-1, '用户不存在');
        }
    }

    /**
     * 获取指定用户的助力总数
     * @param $uid 用户编号
     * @return int
     */
    public function helpsCount($uid) {
        global $_W;
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':from'] = $uid;
        $sql = 'SELECT COUNT(*) FROM ' . tablename('mbrp_helps') . 'WHERE `uniacid`=:uniacid AND `from` = :from';
        $total = pdo_fetchcolumn($sql, $pars);
        return $total;
    }

    /**
     * 获取指定用户助力特定用户的名次
     * @param $from     接受助力的用户
     * @param $to       发起助力的用户
     * @return int      返回大于等于0的整数, 如果没有助力过, 返回0
     */
    public function getHelpRank($from, $to) {
        global $_W;
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':from'] = $from;
        $pars[':to'] = $to;
        $sql = 'SELECT * FROM ' . tablename('mbrp_helps') . 'WHERE `uniacid`=:uniacid AND `from`=:from AND `to`=:to';
        $exists = pdo_fetch($sql, $pars);
        if(empty($exists)) {
            return 0;
        } else {
            $sql = 'SELECT COUNT(*) FROM ' . tablename('mbrp_helps') . "WHERE `uniacid`=:uniacid AND `from`=:from AND `dateline` < {$exists['dateline']}";
            $pars = array();
            $pars[':uniacid'] = $_W['uniacid'];
            $pars[':from'] = $from;
            $total = pdo_fetchcolumn($sql, $pars);
            return $total + 1;
        }
    }

    /**
     * 获取助力记录列表
     * @param array $filters 
     *      from    int 接受助力用户
 *          to      int 发起助力的用户
     * 
     * @param int $pindex
     * @param int $psize
     * @param int $total
     * @return ds(array)
     */
    public function getAllHelps($filters = array(), $pindex = 0, $psize = 15, &$total = 0) {
        global $_W;
        $condition = '`uniacid`=:uniacid';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        if(!empty($filters['from'])) {
            $condition .= ' AND `from`=:from';
            $pars[':from'] = $filters['from'];
        }
        if(!empty($filters['to'])) {
            $condition .= ' AND `to`=:to';
            $pars[':to'] = $filters['to'];
        }
        $sql = 'FROM ' . tablename('mbrp_helps') . "WHERE {$condition}";
        if($pindex > 0) {
            $total = pdo_fetchcolumn("SELECT COUNT(*) {$sql}", $pars);
            $start = ($pindex - 1) * $psize;
            $sql .= " ORDER BY `id` DESC LIMIT {$start},{$psize}";
            $ds = pdo_fetchall("SELECT * {$sql}", $pars);
        } else {
            $ds = pdo_fetchall("SELECT * {$sql}", $pars);
        }
        return $ds;
    }

    /**
     * 创建一条红包领取记录
     *      每个用户(uid)只能创建一条领取记录
     * @param  array $entity
     * @return int|error
     */
    public function createRecord($entity) {
        global $_W;
        $rec = array_elements(array('uid', 'type', 'helps', 'fee', 'snapshoot', 'dateline','status'), $entity);
        $rec['uniacid'] = $_W['uniacid'];
        $sql = 'SELECT * FROM ' . tablename('mbrp_records') . ' WHERE `uniacid`=:uniacid AND `uid`=:uid';
        $pars = array();
        $pars[':uniacid'] = $rec['uniacid'];
        $pars[':uid'] = $rec['uid'];
        $exists = pdo_fetch($sql, $pars);
        if(!empty($exists)) {
            return error(-2, '已经存在的UID号');
        }
        $ret = pdo_insert('mbrp_records', $rec);
        if(!empty($ret)) {
            return pdo_insertid();
        }else{
            return error(-1, '记录保存失败, 请稍后重试');
        }
    }

    /**
     * 更新一条红包记录的状态
     *      如果状态已经 success, 则不能修改状态
     *
     * @param int       $uid        用户编号
     * @param string    $status     状态
     * @param bool      $isRid      如果true, 则按照领取编号更新, 否则(默认)按照用户编号更新
     * @return bool
     */
    public function touchRecord($uid, $status = 'success', $isRid = false) {
        global $_W;
        $condition = '`uniacid`=:uniacid AND `status`=:status';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':status'] = 'success';
        $pars[':uid'] = $uid;
        if($isRid) {
            $condition .= ' AND `id`=:uid';
        } else {
            $condition .= ' AND `uid`=:uid';
        }
        $exists = pdo_fetch('SELECT * FROM ' . tablename('mbrp_records') . " WHERE {$condition}", $pars);
        if(!empty($exists)) {
            return error(-2, '状态已经是 success');
        }
        
        
        $rec = array();
        $rec['status'] = $status;
        
        $filter = array();
        $filter['uniacid'] = $_W['uniacid'];
        if($isRid) {
            $filter['id'] = $uid;
        } else {
            $filter['uid'] = $uid;
        }
        $ret = pdo_update('mbrp_records', $rec, $filter);
        if($ret !== false) {
            return true;
        }else{
            return error(-1, '数据更新失败, 请稍后重试');
        }
    }

    /**
     * 查询一条领取记录
     *      可以按照用户, 或者领取记录编号查询
     * @param int $uid          用户编号
     * @param bool $isRid       如果true, 则按照领取编号查询, 否则(默认)按照用户编号查询
     * @return array
     */
    public function getOneRecord($uid, $isRid = false) {
        global $_W;
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        if($isRid){
            $pars[':id'] = $uid;
            $sql = 'SELECT * FROM ' . tablename('mbrp_records') . ' WHERE `uniacid` =:uniacid AND `id` =:id';
        } else {
            $pars[':uid'] = intval($uid);
            $sql = 'SELECT * FROM ' . tablename('mbrp_records') . ' WHERE `uniacid` =:uniacid AND `uid` =:uid';
        }
        $ret= pdo_fetch($sql, $pars);
        if(!empty($ret)) {
            return $ret;
        } else {
            return array();
        }
    }

    /**
     * 获取红包发放总金额
     * xmc
     */
    public function  getSumFee() {
        global $_W;
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $sql = 'SELECT sum(fee) as total_fee FROM ' . tablename('mbrp_records') . ' WHERE `uniacid` =:uniacid ';
        $ret= pdo_fetch($sql, $pars);
        if(!empty($ret)) {
            return $ret['total_fee'];
        } else {
            return array();
        }
    }

    public function  getTotalFans() {
        global $_W;
        $pars = array();
//        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':id'] = 1;
        $sql = 'SELECT total FROM ' . tablename('mbrp_static') . ' WHERE id = :id ';
        $ret= pdo_fetch($sql, $pars);
        if(!empty($ret)) {
            return $ret['total'];
        } else {
            return array();
        }
    }
}