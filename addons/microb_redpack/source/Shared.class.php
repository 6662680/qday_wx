<?php

class Shared {
    private $activity = null;
    
    function __construct($activity) {
        if(empty($activity) || $activity['type'] != 'shared') {
            trigger_error('参数错误, 活动类型不符合', E_USER_ERROR);
        }
        $this->activity = $activity;
    }

    /**
     * 增加一次助力记录
     *      如果owner, helper 已经存在, 失败
     *   id uniacid owner helper dateline
     *
     * @param $entity 助力结构 owner, helper, dateline
     * @return int|error
     */
    public function createHelp($entity) {
        global $_W;
        $rec = array_elements(array('owner', 'helper', 'dateline'), $entity);
        $rec['uniacid'] = $_W['uniacid'];
        $rec['activity'] = $this->activity['actid'];

        require_once MB_ROOT . '/source/Fans.class.php';
        $f = new Fans();
        $ownerExists = $f->getOne($entity['owner']);
        $helperExists = $f->getOne($entity['helper']);

        if(!empty($ownerExists) && !empty($helperExists)){
            $pars = array();
            $pars[':uniacid'] = $_W['uniacid'];
            $pars[':activity'] = $this->activity['actid'];
            $pars[':helper'] = $rec['helper'];
            
            if(!empty($this->activity['tag']['limit'])) {
                $sql = 'SELECT COUNT(*) FROM ' . tablename('mbrp_helps') . ' WHERE `uniacid`=:uniacid AND `activity`=:activity AND `helper` =:helper';
                $count = pdo_fetchcolumn($sql, $pars);
                if($this->activity['tag']['limit'] <= $count) {
                    return error(-1, "超过次数限制, 本次活动限制每人帮助好友次数不能超过 {$this->activity['tag']['limit']} 次");
                }
            }

            $pars[':owner'] = $rec['owner'];
            $sql = 'SELECT * FROM ' . tablename('mbrp_helps') . ' WHERE `uniacid`=:uniacid AND `activity`=:activity AND `owner`=:owner AND `helper` =:helper';
            $exists = pdo_fetch($sql, $pars);
            if(!empty($exists)) {
                return error(-3, '已经帮助过这位好友了');
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
     * 获取指定用户的被助力总数
     *
     * @param $uid 用户编号
     * @return int
     */
    public function helpsCount($uid) {
        global $_W;
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':activity'] = $this->activity['actid'];
        $pars[':owner'] = $uid;
        $sql = 'SELECT COUNT(*) FROM ' . tablename('mbrp_helps') . 'WHERE `uniacid`=:uniacid AND `activity`=:activity AND `owner`=:owner';
        $total = pdo_fetchcolumn($sql, $pars);
        return $total;
    }

    /**
     * 获取指定用户助力特定用户的名次
     * @param $owner     接受助力的用户
     * @param $helper    发起助力的用户
     * @return int       返回大于等于0的整数, 如果没有助力过, 返回0
     */
    public function getHelpRank($owner, $helper) {
        global $_W;
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':activity'] = $this->activity['actid'];
        $pars[':owner'] = $owner;
        $pars[':helper'] = $helper;
        $sql = 'SELECT * FROM ' . tablename('mbrp_helps') . 'WHERE `uniacid`=:uniacid AND `activity`=:activity AND `owner`=:owner AND `helper`=:helper';
        $exists = pdo_fetch($sql, $pars);
        if(empty($exists)) {
            return 0;
        } else {
            $sql = 'SELECT COUNT(*) FROM ' . tablename('mbrp_helps') . "WHERE `uniacid`=:uniacid AND `activity`=:activity AND `owner`=:owner AND `dateline` < {$exists['dateline']}";
            unset($pars[':helper']);
            $total = pdo_fetchcolumn($sql, $pars);
            return $total + 1;
        }
    }

    /**
     * 获取助力记录列表
     * @param array $filters
     *      owner    int 接受助力用户
     *      helper   int 发起助力的用户
     *
     * @param int $pindex
     * @param int $psize
     * @param int $total
     * @return ds(array)
     */
    public function getAllHelps($filters = array(), $pindex = 0, $psize = 15, &$total = 0) {
        global $_W;
        $condition = '`uniacid`=:uniacid AND `activity`=:activity';
        $pars = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':activity'] = $this->activity['actid'];
        if(!empty($filters['owner'])) {
            $condition .= ' AND `owner`=:owner';
            $pars[':owner'] = $filters['owner'];
        }
        if(!empty($filters['helper'])) {
            $condition .= ' AND `helper`=:helper';
            $pars[':helper'] = $filters['helper'];
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
}