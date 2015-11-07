<?php
defined('IN_IA') or die('Access Denied');
require_once 'inc/support/returndata.class.php';
require_once 'inc/web/manage.inc.php';
require_once 'inc/mobile/check.inc.php';
require_once 'inc/mobile/vote.inc.php';
class Huiyi_weivoteModuleSite extends WeModuleSite
{
    public $table_prefix = 'huiyi_weivote_';
    public $table_reply = 'huiyi_weivote_reply';
    public $table_vote = 'huiyi_weivote_vote';
    public $table_option = 'huiyi_weivote_option';
    public $table_log = 'huiyi_weivote_log';
    public function doWebManageVoteList()
    {
        checklogin();
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        if (checksubmit('delete')) {
            pdo_delete($this->table_vote, ' id  IN  (\'' . implode('\',\'', $_GPC['select']) . '\')');
            message('删除成功！', $this->createWebUrl('ManageVoteList', array('page' => $_GPC['page'])));
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $condition = '';
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
        }
        $rd = Web_ManageVoteList($this, $uniacid, $pindex, $psize, $condition);
        if ($rd->getState()) {
            $list = $rd->getData('list');
            $total = $rd->getData('total');
            $pager = $rd->getData('pager');
            include $this->template('manage-votelist');
        }
    }
    public function doWebManageVoteAdd()
    {
        checklogin();
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        if (!empty($id)) {
            $item = pdo_fetch('SELECT * FROM ' . tablename($this->table_vote) . ' WHERE id = :id', array(':id' => $id));
            if (empty($item)) {
                message('抱歉，投票活动不存在或是已经删除！', '', 'error');
            }
        } else {
            $item = array('max_vote_day' => 1, 'max_vote_count' => 1, 'type_vote' => 1, 'name_state' => 1, 'state' => 0, 'start_time' => TIMESTAMP, 'end_time' => TIMESTAMP + 86399 * 7, 'pageSize' => 10);
        }
        if (checksubmit('submit')) {
            if (empty($_GPC['title'])) {
                message('请输入活动标题！');
            }
            $rd = Web_ManageVoteAdd($this, $uniacid);
            if ($rd->getState()) {
                message('投票信息更新成功！', $this->createWebUrl('ManageVoteList', array()), 'success');
            }
        }
        load()->func('tpl');
        include $this->template('manage-voteadd');
    }
    public function doWebManageVoteDelete()
    {
        checklogin();
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $rd = Web_ManageVoteDelete($this, $uniacid, $id);
        if ($rd->getState()) {
            message('删除成功！', referer(), 'success');
        } else {
            message('抱歉，投票活动不存在或是已经删除！', '', 'error');
        }
    }
    public function doWebManageOptionList()
    {
        checklogin();
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        if (checksubmit('submit')) {
            $rd = Web_ManageOptionAdd($this, $uniacid, $id);
            if ($rd->getState()) {
                message('投票信息更新成功！', $this->createWebUrl('ManageOptionList', array('id' => $id)), 'success');
            }
        }
        $rd = Web_ManageOptionList($this, $uniacid, $id);
        if ($rd->getState()) {
            $vote = $rd->getData('vote');
            $list = $rd->getData('list');
            $total = $rd->getData('total');
            load()->func('tpl');
            include $this->template('manage-optionlist');
        }
    }
    public function doWebManageOptionDelete()
    {
        checklogin();
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $rd = Web_ManageOptionDelete($this, $uniacid);
    }
    public function doWebManageLogCount()
    {
        checklogin();
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        if (checksubmit('submit')) {
            $rd = Web_ManageLogCountAdd($this, $uniacid, $id);
            if ($rd->getState()) {
                message('票数信息更新成功！', $this->createWebUrl('ManageLogCount', array('id' => $id)), 'success');
            }
        }
        $rd = Web_ManageLogCount($this, $uniacid, $id);
        if ($rd->getState()) {
            $vote = $rd->getData('vote');
            $list = $rd->getData('list');
            load()->func('tpl');
            include $this->template('manage-logcount');
        }
    }
    public function doWebManageLog()
    {
        checklogin();
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        if (checksubmit('delete')) {
            pdo_delete($this->table_log, ' id  IN  (\'' . implode('\',\'', $_GPC['select']) . '\')');
            message('删除成功！', $this->createWebUrl('ManageLog', array('id' => $id, 'page' => $_GPC['page'])));
        }
        $vote = pdo_fetch('SELECT * FROM ' . tablename($this->table_vote) . ' WHERE id = :id', array(':id' => $id));
        if (empty($vote)) {
            message('抱歉，投票活动不存在或是已经删除！', '', 'error');
        }
        $log_count = $vote['log_count'];
        $joiner_count = $vote['joiner_count'];
        $log_all_count = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_log) . " WHERE uniacid = '{$uniacid}' AND vid = '{$id}'");
        $pindex = max(1, intval($_GPC['page']));
        $psize = 50;
        $condition = '';
        if (!empty($_GPC['keyword']) && $_GPC['keyword'] != '') {
            if (is_numeric($_GPC['keyword'])) {
                $condition .= ' and ( code = ' . $_GPC['keyword'] . ' or title like \'%' . $_GPC['keyword'] . '%\' )';
            } else {
                $condition .= ' and ( title like \'%' . $_GPC['keyword'] . '%\' or from_user like \'%' . $_GPC['keyword'] . '%\' )';
            }
        }
        $rd = Web_ManageLog($this, $uniacid, $pindex, $psize, $condition, $id);
        if ($rd->getState()) {
            $list = $rd->getData('list');
            $total = $rd->getData('total');
            $pager = $rd->getData('pager');
            include $this->template('manage-log');
        }
    }
    public function doWebManageLogDelete()
    {
        checklogin();
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $rd = Web_ManageLogDelete($this, $uniacid, $id);
        if ($rd->getState()) {
            message('删除成功！', referer(), 'success');
        } else {
            message('抱歉，投票记录不存在或是已经删除！', '', 'error');
        }
    }
    public function doWebManageLogState()
    {
        checklogin();
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $vid = intval($_GPC['vid']);
        $oid = intval($_GPC['oid']);
        $state = intval($_GPC['state']);
        $data = array('state' => $state);
        if (pdo_update($this->table_log, $data, array('id' => $id))) {
            if ($state == 1) {
                $rd = Web_ManageLogPlus($this, $uniacid, $vid, $oid, -1);
            } else {
                $rd = Web_ManageLogPlus($this, $uniacid, $vid, $oid);
            }
            message('记录操作成功！', referer(), 'success');
        } else {
            message('不存在投票记录，请刷新后重试！', referer(), 'success');
        }
    }
    public function doWebManageResult()
    {
        checklogin();
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $vote = pdo_fetch('SELECT * FROM ' . tablename($this->table_vote) . ' WHERE id = :id', array(':id' => $id));
        if (empty($vote)) {
            message('抱歉，投票活动不存在或是已经删除！', '', 'error');
        }
        $log_count = $vote['log_count'];
        $joiner_count = $vote['joiner_count'];
        $rd = Mobile_GetWeivoteOptions($this, $uniacid, $id);
        $weivote_options = $rd->getData('weivote_options');
        $weivote_options_index = $rd->getData('weivote_options_index');
        $weivote_options_count = $rd->getData('weivote_options_count');
        $rd = Mobile_GetWeivoteOptionsView($this, $uniacid, $vote, $weivote_options_index);
        $weivote_options_view = $rd->getData();
        if (count($weivote_options_view) > 0) {
            $weivote_options_view = Web_ManageOptionSort($weivote_options_view, 'log_count', 'desc');
        }
        include $this->template('manage-result');
    }
    public function doMobileIndex()
    {
        global $_W, $_GPC;
        $weid = $_W['weid'];
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $rd = Mobile_CheckView($this, $uniacid);
        if (!$rd->getState()) {
            return $rd->toMessage();
        }
        $rd = Mobile_GetWeivoteVote($this, $uniacid, $id);
        if (!$rd->getState()) {
            return $rd->toMessage();
        }
        $weivote_vote = $rd->getData();
        $title = $weivote_vote['title'];
        $desc = $weivote_vote['description'];
        $pic = $_W['attachurl'] . $weivote_vote['picture'];
        include $this->template('index');
    }
    public function doMobileAjaxDefault()
    {
        global $_W, $_GPC;
        $weid = $_W['weid'];
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $rd = Mobile_GetWeivoteVote($this, $uniacid, $id);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $weivote_vote = $rd->getData();
        $page_switch = $weivote_vote['page_switch'];
        $page_size = $weivote_vote['page_size'];
        $log_count = $weivote_vote['log_count'];
        $joiner_count = $weivote_vote['joiner_count'];
        $rd = Mobile_GetWeivoteOptions($this, $uniacid, $id, '', $page_switch, $page_size);
        $weivote_options = $rd->getData('weivote_options');
        $weivote_options_index = $rd->getData('weivote_options_index');
        $weivote_options_count = $rd->getData('weivote_options_count');
        $page_count = $rd->getData('page_count');
        $page_no = $rd->getData('page_no');
        $page_start = $rd->getData('page_start');
        $page_end = $rd->getData('page_end');
        $page_nos = $rd->getData('page_nos');
        $rd = Mobile_GetWeivoteOptionsView($this, $uniacid, $weivote_vote, $weivote_options_index);
        $weivote_options_view = $rd->getData();
        $rd = Mobile_ClickCount($this, $uniacid, $weivote_vote);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $json_data = array('attachurl' => $_W['attachurl'], 'title' => $weivote_vote['title'], 'desc' => $weivote_vote['description'], 'pic' => $_W['attachurl'] . $weivote_vote['picture'], 'start_time' => date('Y-m-d H:i', $weivote_vote['start_time']), 'end_time' => date('Y-m-d H:i', $weivote_vote['end_time']), 'log_count' => $log_count, 'joiner_count' => $joiner_count, 'page_switch' => $page_switch, 'weivote_vote' => $weivote_vote, 'weivote_options' => $weivote_options, 'weivote_options_index' => $weivote_options_index, 'weivote_options_count' => $weivote_options_count, 'weivote_options_view' => $weivote_options_view, 'page_count' => $page_count, 'page_no' => $page_no, 'page_start' => $page_start, 'page_end' => $page_end, 'page_nos' => $page_nos);
        $rd->setData($json_data);
        return $rd->toJson();
    }
    public function doMobileAjaxVoterSearch()
    {
        global $_W, $_GPC;
        $weid = $_W['weid'];
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $voteKey = $_GPC['voteKey'];
        $voteKey = str_replace('%', '', $voteKey);
        $voteKey = str_replace('\'', '', $voteKey);
        $voteKey = str_replace('delete', '', $voteKey);
        $voteKey = str_replace('select', '', $voteKey);
        $voteKey = str_replace('update', '', $voteKey);
        $voteKey = str_replace('drop', '', $voteKey);
        $voteKey = str_replace('where', '', $voteKey);
        $voteKey = str_replace('"', '', $voteKey);
        $voteKey = str_replace('=', '', $voteKey);
        $rd = Mobile_GetWeivoteVote($this, $uniacid, $id);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $weivote_vote = $rd->getData();
        $rd = Mobile_GetWeivoteOptions($this, $uniacid, $id, $voteKey);
        $weivote_options = $rd->getData('weivote_options');
        $weivote_options_index = $rd->getData('weivote_options_index');
        $weivote_options_count = $rd->getData('weivote_options_count');
        $rd = Mobile_GetWeivoteOptionsView($this, $uniacid, $weivote_vote, $weivote_options_index);
        $weivote_options_view = $rd->getData();
        $json_data = array('weivote_options_view' => $weivote_options_view);
        $rd->setData($json_data);
        return $rd->toJson();
    }
    public function doMobileAjaxVoterPage()
    {
        global $_W, $_GPC;
        $weid = $_W['weid'];
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $page_no = intval($_GPC['pageNo']);
        $rd = Mobile_GetWeivoteVote($this, $uniacid, $id);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $weivote_vote = $rd->getData();
        $page_switch = $weivote_vote['page_switch'];
        $page_size = $weivote_vote['page_size'];
        $log_count = $weivote_vote['log_count'];
        $joiner_count = $weivote_vote['joiner_count'];
        $rd = Mobile_GetWeivoteOptions($this, $uniacid, $id, '', $page_switch, $page_size, $page_no);
        $weivote_options = $rd->getData('weivote_options');
        $weivote_options_index = $rd->getData('weivote_options_index');
        $weivote_options_count = $rd->getData('weivote_options_count');
        $page_count = $rd->getData('page_count');
        $page_no = $rd->getData('page_no');
        $page_start = $rd->getData('page_start');
        $page_end = $rd->getData('page_end');
        $page_nos = $rd->getData('page_nos');
        $rd = Mobile_GetWeivoteOptionsView($this, $uniacid, $weivote_vote, $weivote_options_index);
        $weivote_options_view = $rd->getData();
        $json_data = array('weivote_options_view' => $weivote_options_view, 'page_switch' => $page_switch, 'page_count' => $page_count, 'page_no' => $page_no, 'page_start' => $page_start, 'page_end' => $page_end, 'page_nos' => $page_nos);
        $rd->setData($json_data);
        return $rd->toJson();
    }
    public function doMobileAjaxVoter()
    {
        global $_W, $_GPC;
        $weid = $_W['weid'];
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $oid = $_GPC['oid'];
        $rd = Mobile_GetWeivoteVote($this, $uniacid, $id);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $weivote_vote = $rd->getData();
        $rd = Mobile_GetWeivoteOption($this, $uniacid, $id, $oid);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $weivote_option = $rd->getData();
        $json_data = array('attachurl' => $_W['attachurl'], 'title' => $weivote_vote['title'], 'desc' => $weivote_vote['description'], 'pic' => $_W['attachurl'] . $weivote_vote['picture'], 'start_time' => date('Y-m-d H:i', $weivote_vote['start_time']), 'end_time' => date('Y-m-d H:i', $weivote_vote['end_time']), 'weivote_vote' => $weivote_vote, 'weivote_option' => $weivote_option);
        $rd->setData($json_data);
        return $rd->toJson();
    }
    public function doMobileAjaxSubmitVoter()
    {
        global $_W, $_GPC;
        $weid = $_W['weid'];
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $openid = Mobile_GetOpenid();
        $oid = $_GPC['oid'];
        $clientip = $_W['clientip'];
        $isajax = $_W['isajax'];
        $rd = Mobile_GetWeivoteVote($this, $uniacid, $id);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $weivote_vote = $rd->getData();
        $rd = Mobile_GetMember($this, $uniacid, $id, $openid);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $fans = $rd->getData('fans');
        $member = $rd->getData('member');
        $follow = $fans['follow'];
        $rd = Mobile_CheckSubmit($this, $uniacid, $isajax, $openid, $follow, $weivote_vote, $member, $oid, $clientip);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $oidArr = array();
        $oidstr = '';
        if (!is_array($oid)) {
            array_push($oidArr, $oid);
            $oidstr .= $oid;
        } else {
            if (is_array($oid) && count($oid) > 0) {
                $oidArr = $oid;
                foreach ($oid as $oid_one) {
                    $oidstr .= $oid_one . ',';
                }
                $oidstr = substr($oidstr, 0, strlen($oidstr) - 1);
            }
        }
        if (count($oidArr) >= 0) {
            $rd = Mobile_GetWeivoteOptions($this, $uniacid, $id);
            $weivote_options_index = $rd->getData('weivote_options_index');
            foreach ($oidArr as $oid_one) {
                $weivote_option = $weivote_options_index[$oid_one];
                $data = array('uniacid' => $uniacid, 'create_time' => TIMESTAMP, 'state' => 0, 'oid' => $oid_one, 'code' => $weivote_option['code'], 'title' => $weivote_option['title'], 'realname' => $member['realname'], 'qq' => $member['qq'], 'mobile' => $member['mobile'], 'weixinno' => $member['msn'], 'from_user' => $openid, 'clientip' => $clientip, 'vid' => $id);
                if (pdo_insert($this->table_log, $data)) {
                    $rd = Mobile_LogOptionCount($this, $uniacid, $weivote_vote, $weivote_option);
                    if (!$rd->getState()) {
                        return $rd->toJson();
                    }
                }
            }
            $rd = Mobile_LogVoteCount($this, $uniacid, $weivote_vote, $weivote_option, count($oidArr));
            if (!$rd->getState()) {
                return $rd->toJson();
            }
        }
        $rd->setCode(100);
        $rd->setNode('boMobileAjaxSubmitVoter');
        $rd->setMsg('投票成功');
        $rd->setUrl('ajax/result.html');
        return $rd->toJson();
    }
    public function doMobileAjaxResult()
    {
        global $_W, $_GPC;
        $weid = $_W['weid'];
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $rd = Mobile_GetWeivoteVote($this, $uniacid, $id);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $weivote_vote = $rd->getData();
        $log_count = $weivote_vote['log_count'];
        $joiner_count = $weivote_vote['joiner_count'];
        $rd = Mobile_GetWeivoteOptions($this, $uniacid, $id);
        $weivote_options = $rd->getData('weivote_options');
        $weivote_options_index = $rd->getData('weivote_options_index');
        $weivote_options_count = $rd->getData('weivote_options_count');
        $page_count = $rd->getData('page_count');
        $page_no = $rd->getData('page_no');
        $page_start = $rd->getData('page_start');
        $page_end = $rd->getData('page_end');
        $page_nos = $rd->getData('page_nos');
        $rd = Mobile_GetWeivoteOptionsView($this, $uniacid, $weivote_vote, $weivote_options_index);
        $weivote_options_view = $rd->getData();
        $json_data = array('attachurl' => $_W['attachurl'], 'title' => $weivote_vote['title'], 'desc' => $weivote_vote['description'], 'pic' => $_W['attachurl'] . $weivote_vote['picture'], 'start_time' => date('Y-m-d H:i', $weivote_vote['start_time']), 'end_time' => date('Y-m-d H:i', $weivote_vote['end_time']), 'log_count' => $log_count, 'joiner_count' => $joiner_count, 'weivote_vote' => $weivote_vote, 'weivote_options' => $weivote_options, 'weivote_options_index' => $weivote_options_index, 'weivote_options_count' => $weivote_options_count, 'weivote_options_view' => $weivote_options_view);
        $rd->setData($json_data);
        $rd->setCode(100);
        $rd->setNode('doMobileAjaxResult');
        $rd->setMsg('投票成功');
        $rd->setUrl('ajax/result.html');
        return $rd->toJson();
    }
    public function doMobileAjaxReg()
    {
        global $_W, $_GPC;
        $weid = $_W['weid'];
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $openid = Mobile_GetOpenid();
        $rd = Mobile_GetWeivoteVote($this, $uniacid, $id);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $weivote_vote = $rd->getData();
        $rd = Mobile_GetMember($this, $uniacid, $id, $openid);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $fans = $rd->getData('fans');
        $member = $rd->getData('member');
        $follow = $fans['follow'];
        $rd = new ReturnData(null);
        $rd->addData('member', $member);
        return $rd->toJson();
    }
    public function doMobileAjaxSubmitReg()
    {
        global $_W, $_GPC;
        $weid = $_W['weid'];
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $openid = Mobile_GetOpenid();
        $rd = Mobile_GetMember($this, $uniacid, $id, $openid);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $fans = $rd->getData('fans');
        $member = $rd->getData('member');
        $follow = $fans['follow'];
        $rd = Mobile_CheckFans($this, $uniacid, $openid, $follow);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $data = array('realname' => $_GPC['realname'], 'mobile' => $_GPC['mobile'], 'qq' => $_GPC['qq'], 'msn' => $_GPC['weixinno']);
        if (empty($data['realname'])) {
            $rd->setCode(200);
            $rd->setNode('doMobileAjaxSubmitReg');
            $rd->setMsg('请填写您的真实姓名!');
            $rd->setUrl('ajax/default.html');
            return $rd->toJson();
        }
        if (empty($data['mobile'])) {
            $rd->setCode(201);
            $rd->setNode('doMobileAjaxSubmitReg');
            $rd->setMsg('请填写您的手机号码!');
            $rd->setUrl('ajax/default.html');
            return $rd->toJson();
        }
        $rd = Mobile_UpdateMember($this, $uniacid, $fans['uid'], $data);
        if (!$rd->getState()) {
            return $rd->toJson();
        }
        $rd->setNode('doMobileAjaxSubmitReg');
        $rd->setMsg('登记成功');
        $rd->setUrl('ajax/default.html');
        return $rd->toJson();
    }
    public function doMobileCover()
    {
        $title = '多活动封面';
        include $this->template('cover');
    }
    public function doMobilePay()
    {
        global $_W, $_GPC;
        checkauth();
        $params['tid'] = date('YmdH');
        $params['user'] = $_W['member']['uid'];
        $params['fee'] = floatval($_GPC['price']);
        $params['title'] = '测试支付公众号名称';
        $params['ordersn'] = random(5, 1);
        $params['virtual'] = false;
        if (checksubmit('submit')) {
            if ($_GPC['type'] == 'credit') {
                $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
                $credtis = mc_credit_fetch($_W['member']['uid']);
                if ($credtis[$setting['creditbehaviors']['currency']] < $params['fee']) {
                    message('抱歉，您帐户的余额不够支付该订单，请充值！', '', 'error');
                }
            }
        } else {
            $this->pay($params);
        }
    }
    public function payResult($params)
    {
        $fee = intval($params['fee']);
        $data = array('status' => $params['result'] == 'success' ? 1 : 0);
        if ($params['type'] == 'wechat') {
            $data['transid'] = $params['tag']['transaction_id'];
        }
        if ($params['from'] == 'return') {
            if ($params['type'] == 'credit') {
                message('支付成功！', $this->createMobileUrl('index1'), 'success');
            } elseif ($params['type'] == 'delivery') {
                message('请您在收到货物时付清货款！', $this->createMobileUrl('index1'), 'success');
            } else {
                message('支付成功！', '../../' . $this->createMobileUrl('index1'), 'success');
            }
        }
    }
}
?>