<?php

/**
 * 预约与调查模块微站定义
 *
 * @author WeEngine Team
 * @url http://www.qdaygroup.com
 */
defined('IN_IA') or exit('Access Denied');

class We7_researchModuleSite extends WeModuleSite {

    public function getHomeTiles() {
        global $_W;
        $urls = array();
        $list = pdo_fetchall("SELECT title, reid FROM " . tablename('research') . " WHERE weid = '{$_W['uniacid']}'");
        if (!empty($list)) {
            foreach ($list as $row) {
                $urls[] = array('title' => $row['title'], 'url' => $_W['siteroot']."app/".$this->createMobileUrl('research', array('id' => $row['reid'])));
            }
        }
        return $urls;
    }

    public function doWebQuery() {
        global $_W, $_GPC;
        $kwd = $_GPC['keyword'];
        $sql = 'SELECT * FROM ' . tablename('research') . ' WHERE `weid`=:weid AND `title` LIKE :title ORDER BY reid DESC LIMIT 0,8';
        $params = array();
        $params[':weid'] = $_W['uniacid'];
        $params[':title'] = "%{$kwd}%";
        $ds = pdo_fetchall($sql, $params);
        foreach ($ds as &$row) {
            $r = array();
            $r['title'] = $row['title'];
            $r['description'] = cutstr(strip_tags($row['description']), 50);
            $r['thumb'] = $row['thumb'];
            $r['reid'] = $row['reid'];
            $row['entry'] = $r;
        }
        include $this->template('query');
    }

    public function doWebDetail() {
        global $_W, $_GPC;
        $rerid = intval($_GPC['id']);
        $sql = 'SELECT * FROM ' . tablename('research_rows') . " WHERE `rerid`=:rerid";
        $params = array();
        $params[':rerid'] = $rerid;
        $row = pdo_fetch($sql, $params);
        if (empty($row)) {
            message('访问非法.');
        }
        $sql = 'SELECT * FROM ' . tablename('research') . ' WHERE `weid`=:weid AND `reid`=:reid';
        $params = array();
        $params[':weid'] = $_W['uniacid'];
        $params[':reid'] = $row['reid'];
        $activity = pdo_fetch($sql, $params);
        if (empty($activity)) {
            message('非法访问.');
        }
        $sql = 'SELECT * FROM ' . tablename('research_fields') . ' WHERE `reid`=:reid ORDER BY `refid`';
        $params = array();
        $params[':reid'] = $row['reid'];
        $fields = pdo_fetchall($sql, $params);
        if (empty($fields)) {
            message('非法访问.');
        }
        $ds = $fids = array();
        foreach ($fields as $f) {
            $ds[$f['refid']]['fid'] = $f['title'];
            $ds[$f['refid']]['type'] = $f['type'];
            $ds[$f['refid']]['refid'] = $f['refid'];
            $fids[] = $f['refid'];
        }

        $fids = implode(',', $fids);
        $row['fields'] = array();
        $sql = 'SELECT * FROM ' . tablename('research_data') . " WHERE `reid`=:reid AND `rerid`='{$row['rerid']}' AND `refid` IN ({$fids})";
        $fdatas = pdo_fetchall($sql, $params);

        foreach ($fdatas as $fd) {
            $row['fields'][$fd['refid']] = $fd['data'];
        }

        // 兼容会员居住地字段
        foreach ($ds as $value) {
            if ($value['type'] == 'reside') {
                $row['fields'][$value['refid']] = '';
                foreach ($fdatas as $fdata) {
                    if ($fdata['refid'] == $value['refid']) {
                        $row['fields'][$value['refid']] .= $fdata['data'];
                    }
                }
                break;
            }
        }

        include $this->template('detail');
    }

    public function doWebManage() {
        global $_W, $_GPC;
        $reid = intval($_GPC['id']);
        $sql = 'SELECT * FROM ' . tablename('research') . ' WHERE `weid`=:weid AND `reid`=:reid';
        $params = array();
        $params[':weid'] = $_W['uniacid'];
        $params[':reid'] = $reid;
        $activity = pdo_fetch($sql, $params);
        if (empty($activity)) {
            message('非法访问.');
        }
        $sql = 'SELECT * FROM ' . tablename('research_fields') . ' WHERE `reid`=:reid ORDER BY `refid`';
        $params = array();
        $params[':reid'] = $reid;
        $fields = pdo_fetchall($sql, $params);
        if (empty($fields)) {
            message('非法访问.');
        }
        $ds = array();
        foreach ($fields as $f) {
            $ds[$f['refid']] = $f['title'];
        }
        $starttime = empty($_GPC['daterange']['start']) ? strtotime('-1 month') : strtotime($_GPC['daterange']['start']);
        $endtime = empty($_GPC['daterange']['end']) ? TIMESTAMP : strtotime($_GPC['daterange']['end']) + 86399;
        $select = array();
        if (!empty($_GPC['select'])) {
            foreach ($_GPC['select'] as $field) {
                if (isset($ds[$field])) {
                    $select[] = $field;
                }
            }
        }

        $pindex = max(1, intval($_GPC['page']));
        $psize = 50;
        $sql = 'SELECT * FROM ' . tablename('research_rows') . " WHERE `reid`=:reid AND `createtime` > {$starttime} AND `createtime` < {$endtime} ORDER BY `createtime` DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $params = array();
        $params[':reid'] = $reid;
        $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('research_rows') . " WHERE `reid`=:reid AND `createtime` > {$starttime} AND `createtime` < {$endtime}", $params);
        $pager = pagination($total, $pindex, $psize);
        $list = pdo_fetchall($sql, $params);
        if ($select) {
            $fids = implode(',', $select);
            foreach ($list as &$r) {
                $r['fields'] = array();
                $sql = 'SELECT data, refid FROM ' . tablename('research_data') . " WHERE `reid`=:reid AND `rerid`='{$r['rerid']}' AND `refid` IN ({$fids})";
                $fdatas = pdo_fetchall($sql, $params);
                foreach ($fdatas as $fd) {
                    if (false == array_key_exists($fd['refid'], $r['fields'])) {
                        $r['fields'][$fd['refid']] = $fd['data'];
                    } else {
                        $r['fields'][$fd['refid']] .= '--' . $fd['data'];
                    }
                }
            }
        }

        /* 如果调查项目类型为图片，处理fields字段信息 */
        foreach ($list as $key => &$value) {
            if(is_array($value['fields'])){
                foreach ($value['fields'] as &$v) {
                    $img = '<div align="center"><img src="';
                    if (substr($v, 0, 6) == 'images') {
                        $v = $img . $_W['attachurl'] . $v . '" style="width:50px;height:50px;"/></div>';
                    }
                }
                unset($v);
            }
        }

        if (checksubmit('export', 1)) {
            $sql = 'SELECT title FROM ' . tablename('research_fields') . " AS f JOIN " . tablename('research_rows') . " AS r ON f.reid='{$params[':reid']}' GROUP BY title ORDER BY refid";
            $tableheader = pdo_fetchall($sql, $params);
            $tablelength = count($tableheader);
            $tableheader[] = array('title' => '创建时间');
            /* 获取预约数据 */
            $sql = 'SELECT * FROM ' . tablename('research_rows') . " WHERE `reid`=:reid AND `createtime` > {$starttime} AND `createtime` < {$endtime} ORDER BY `createtime` DESC";
            $params = array();
            $params[':reid'] = $reid;
            $list = pdo_fetchall($sql, $params);
            if (empty($list)) {
                message('暂时没有预约数据');
            }
            foreach ($list as &$r) {
                $r['fields'] = array();
                $sql = 'SELECT data, refid FROM ' . tablename('research_data') . " WHERE `reid`=:reid AND `rerid`='{$r['rerid']}'";
                $fdatas = pdo_fetchall($sql, $params);
                foreach ($fdatas as $fd) {
                    $r['fields'][$fd['refid']] = $fd['data'];
                }
            }

            /* 处理预约数据 */
            $data = array();
            foreach ($list as $key => $value) {
                if (!empty($value['fields'])) {
                    foreach ($value['fields'] as $field) {
                        $data[$key][] = str_replace(array("\n", "\r", "\t"), '', $field);
                    }
                }
                $data[$key]['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
            }


            /* 输入到CSV文件 */
            $html = "\xEF\xBB\xBF";
            /* 输出表头 */
            foreach ($tableheader as $value) {
                $html .= $value['title'] . "\t ,";
            }
            $html .= "\n";
            /* 输出内容 */
            foreach ($data as $value) {
                for ($i = 0; $i < $tablelength; $i++) {
                    $html .= $value[$i] . "\t ,";
                }
                $html .= $value['createtime'] . "\t ,";
                $html .= "\n";
            }

            /* 输出CSV文件 */
            header("Content-type:text/csv");
            header("Content-Disposition:attachment; filename=全部数据.csv");
            echo $html;
            exit();
        }
        include $this->template('manage');
    }

    public function doWebDisplay() {
        global $_W, $_GPC;
        if ($_W['ispost']) {
            $reid = intval($_GPC['reid']);
            $switch = intval($_GPC['switch']);
            $sql = 'UPDATE ' . tablename('research') . ' SET `status`=:status WHERE `reid`=:reid';
            $params = array();
            $params[':status'] = $switch;
            $params[':reid'] = $reid;
            pdo_query($sql, $params);
            exit();
        }
        $sql = 'SELECT * FROM ' . tablename('research') . ' WHERE `weid`=:weid';
        $status =$_GPC['status'];
        if($status!=''){
            $sql.=" and status=".intval($status);
        }
        $ds = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
        foreach ($ds as &$item) {
            $item['isstart'] = $item['starttime'] > 0;
            $item['switch'] = $item['status'];
            $item['link'] =$_W['siteroot']."app/".$this->createMobileUrl('research', array('id' => $item['reid']));
        }
        include $this->template('display');
    }

    public function doWebDelete() {
        global $_W, $_GPC;
        $reid = intval($_GPC['id']);
        if ($reid > 0) {
            $params = array();
            $params[':reid'] = $reid;
            $sql = 'DELETE FROM ' . tablename('research') . ' WHERE `reid`=:reid';
            pdo_query($sql, $params);
            $sql = 'DELETE FROM ' . tablename('research_rows') . ' WHERE `reid`=:reid';
            pdo_query($sql, $params);
            $sql = 'DELETE FROM ' . tablename('research_fields') . ' WHERE `reid`=:reid';
            pdo_query($sql, $params);
            $sql = 'DELETE FROM ' . tablename('research_data') . ' WHERE `reid`=:reid';
            pdo_query($sql, $params);
            message('操作成功.', referer());
        }
        message('非法访问.');
    }

    public function doWebResearchDelete() {
        global $_W, $_GPC;
        $id = intval($_GPC['id']);
        if (!empty($id)) {
            pdo_delete('research_rows', array('rerid' => $id));
        }
        message('操作成功.', referer());
    }

    public function doWebPost() {
        global $_W, $_GPC;
        $reid = intval($_GPC['id']);
        $hasData = false;
        if ($reid) {
            $sql = 'SELECT COUNT(*) FROM ' . tablename('research_rows') . ' WHERE `reid`=' . $reid;
            if (pdo_fetchcolumn($sql) > 0) {
                $hasData = true;
            }
        }
        if (checksubmit()) {
            $record = array();
            $record['title'] = trim($_GPC['activity']);
            $record['weid'] = $_W['uniacid'];
            $record['description'] = trim($_GPC['description']);
            $record['content'] = trim($_GPC['content']);
            $record['information'] = trim($_GPC['information']);
            if (!empty($_GPC['thumb'])) {
                $record['thumb'] = $_GPC['thumb'];
                load()->func('file');
                file_delete($_GPC['thumb-old']);
            }
            $record['status'] = intval($_GPC['status']);
            $record['inhome'] = intval($_GPC['inhome']);
            $record['pretotal'] = intval($_GPC['pretotal']);
            $record['starttime'] = strtotime($_GPC['starttime']);
            $record['endtime'] = strtotime($_GPC['endtime']);
            $record['noticeemail'] = trim($_GPC['noticeemail']);
            if (empty($reid)) {
                $record['status'] = 1;
                $record['createtime'] = TIMESTAMP;
                pdo_insert('research', $record);
                $reid = pdo_insertid();
                if (!$reid) {
                    message('保存预约失败, 请稍后重试.');
                }
            } else {
                if (pdo_update('research', $record, array('reid' => $reid)) === false) {
                    message('保存预约失败, 请稍后重试.');
                }
            }

            if (!$hasData) {
                $sql = 'DELETE FROM ' . tablename('research_fields') . ' WHERE `reid`=:reid';
                $params = array();
                $params[':reid'] = $reid;
                pdo_query($sql, $params);
                foreach ($_GPC['title'] as $k => $v) {
                    $field = array();
                    $field['reid'] = $reid;
                    $field['title'] = trim($v);
                    $field['displayorder'] = range_limit($_GPC['displayorder'][$k], 0, 254);
                    $field['type'] = $_GPC['type'][$k];
                    $field['essential'] = $_GPC['essentialvalue'][$k] == 'true' ? 1 : 0;
                    $field['bind'] = $_GPC['bind'][$k];
                    $field['value'] = $_GPC['value'][$k];
                    $field['value'] = urldecode($field['value']);
                    $field['description'] = $_GPC['desc'][$k];
                    pdo_insert('research_fields', $field);
                }
            }
            message('保存预约成功.', 'refresh');
        }

        $types = array();
        $types['number'] = '数字(number)';
        $types['text'] = '字串(text)';
        $types['textarea'] = '文本(textarea)';
        $types['radio'] = '单选(radio)';
        $types['checkbox'] = '多选(checkbox)';
        $types['select'] = '选择(select)';
        $types['calendar'] = '日历(calendar)';
        $types['email'] = '电子邮件(email)';
        $types['image'] = '上传图片(image)';
        $types['range'] = '日期范围(range)';
        $types['reside'] = '居住地(reside)';
        $fields = fans_fields();
        if ($reid) {
            $sql = 'SELECT * FROM ' . tablename('research') . ' WHERE `weid`=:weid AND `reid`=:reid';
            $params = array();
            $params[':weid'] = $_W['uniacid'];
            $params[':reid'] = $reid;
            $activity = pdo_fetch($sql, $params);
            $activity['starttime'] && $activity['starttime'] = date('Y-m-d H:i:s', $activity['starttime']);
            $activity['endtime'] && $activity['endtime'] = date('Y-m-d H:i:s', $activity['endtime']);
            if ($activity) {
                $sql = 'SELECT * FROM ' . tablename('research_fields') . ' WHERE `reid`=:reid ORDER BY `refid`';
                $params = array();
                $params[':reid'] = $reid;
                $ds = pdo_fetchall($sql, $params);
            }
        }
        if (empty($activity['endtime'])) {
            $activity['endtime'] = date('Y-m-d', strtotime('+1 day'));
        }
        include $this->template('post');
    }

    public function doMobileResearch() {
        global $_W, $_GPC;
        $reid = intval($_GPC['id']);
        $sql = 'SELECT * FROM ' . tablename('research') . ' WHERE `weid`=:weid AND `reid`=:reid';
        $params = array();
        $params[':weid'] = $_W['uniacid'];
        $params[':reid'] = $reid;
        $activity = pdo_fetch($sql, $params);
        $title = $activity['title'];
        if ($activity['status'] != '1') {
            message('当前预约活动已经停止.');
        }
        if (!$activity) {
            message('非法访问.');
        }
        if ($activity['starttime'] > TIMESTAMP) {
            message('当前预约活动还未开始！');
        }
        if ($activity['endtime'] < TIMESTAMP) {
            message('当前预约活动已经结束！');
        }
        $sql = 'SELECT * FROM ' . tablename('research_fields') . ' WHERE `reid` = :reid ORDER BY `displayorder` DESC';
        $params = array();
        $params[':reid'] = $reid;
        $ds = pdo_fetchall($sql, $params);
        if (!$ds) {
            message('非法访问.');
        }

        $initRange = $initCalendar = false;
        $binds = array();
        foreach ($ds as &$r) {
            if ($r['type'] == 'range') {
                $initRange = true;
            }
            if ($r['type'] == 'calendar') {
                $initCalendar = true;
            }
            if ($r['value']) {
                $r['options'] = explode(',', $r['value']);
            }
            if ($r['bind']) {
                $binds[$r['type']] = $r['bind'];
            }
            if ($r['type'] == 'reside') {
                $reside = $r;
            }
        }

        if (checksubmit('submit')) {
            $pretotal = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('research_rows') . " WHERE reid = :reid AND openid = :openid", array(':reid' => $reid, ':openid' => $_W['fans']['from_user']));
            if ($pretotal >= $activity['pretotal']) {
                message('抱歉,每人只能预约' . $activity['pretotal'] . "次！", referer(), 'error');
            }
            $row = array();
            $row['reid'] = $reid;
            $row['openid'] = $_W['fans']['from_user'];
            $row['createtime'] = TIMESTAMP;
            $datas = $fields = $update = array();
            foreach ($ds as $value) {
                $fields[$value['refid']] = $value;
            }

            foreach ($_GPC as $key => $value) {
                if (strexists($key, 'field_')) {
                    $bindFiled = substr(strrchr($key, '_'), 1);
                    if (!empty($bindFiled)) {
                        $update[$bindFiled] = $value;
                    }
                    $refid = intval(str_replace('field_', '', $key));
                    $field = $fields[$refid];
                    if ($refid && $field) {
                        $entry = array();
                        $entry['reid'] = $reid;
                        $entry['rerid'] = 0;
                        $entry['refid'] = $refid;
                        if (in_array($field['type'], array('number', 'text', 'calendar', 'email', 'textarea', 'radio', 'range', 'select', 'image'))) {
                            $entry['data'] = strval($value);
                        }
                        if (in_array($field['type'], array('checkbox'))) {
                            if (!is_array($value))
                                continue;
                            $entry['data'] = implode(';', $value);
                        }
                        $datas[] = $entry;
                    }
                }
            }
            if ($_FILES) {
                load()->func('file');
                foreach ($_FILES as $key => $file) {
                    if (strexists($key, 'field_')) {
                        $refid = intval(str_replace('field_', '', $key));
                        $field = $fields[$refid];
                        if ($refid && $field && $file['name'] && $field['type'] == 'image') {
                            $entry = array();
                            $entry['reid'] = $reid;
                            $entry['rerid'] = 0;
                            $entry['refid'] = $refid;
                            $ret = file_upload($file);
                            if (!$ret['success']) {
                                message('上传图片失败, 请稍后重试.');
                            }
                            $entry['data'] = trim($ret['path']);
                            $datas[] = $entry;
                        }
                    }
                }
            }

            // 兼容会员居住地字段
            if (!empty($_GPC['reside'])) {
                if (in_array('reside', $binds)) {
                    $update['resideprovince'] = $_GPC['reside']['province'];
                    $update['residecity'] = $_GPC['reside']['city'];
                    $update['residedist'] = $_GPC['reside']['district'];
                }
                foreach ($_GPC['reside'] as $key => $value) {
                    $resideData = array('reid' => $reside['reid']);
                    $resideData['rerid'] = 0;
                    $resideData['refid'] = $reside['refid'];
                    $resideData['data'] = $value;
                    $datas[] = $resideData;
                }
            }

            // 更新关联会员资料
            if (!empty($update)) {
                load()->model('mc');
                mc_update($_W['member']['uid'], $update);
            }

            if (empty($datas)) {
                message('非法访问.', '', 'error');
            }

            if (pdo_insert('research_rows', $row) != 1) {
                message('保存失败.');
            }
            $rerid = pdo_insertid();
            if (empty($rerid)) {
                message('保存失败.');
            }
            foreach ($datas as &$r) {
                $r['rerid'] = $rerid;
                pdo_insert('research_data', $r);
            }
            if (empty($activity['starttime'])) {
                $record = array();
                $record['starttime'] = TIMESTAMP;
                pdo_update('research', $record, array('reid' => $reid));
            }
            //发送预约
            if (!empty($datas) && !empty($activity['noticeemail'])) {
                foreach ($datas as $row) {
                   $img = "<img src='{$_W['attachurl']}";
		     //$img = "<img src='";
                    /* 如果预约项目类型是上传图片 */
                    if (substr($row['data'], 0, 6) == 'images') {
                      $body = $fields[$row['refid']]['title'] . ':' . $img . $row['data'] . " ' width='90';height='120'/>";
                                           // $body = $fields[$row['refid']]['title'] . ':' . $img . tomedia($row['data']) . ' />';
		    }
                    $body .= '<h4>' . $fields[$row['refid']]['title'] . ':' . $row['data'] . '</h4>';
                }
                load()->func('communication');
                ihttp_email($activity['noticeemail'], $activity['title'] . '的预约提醒', $body);
            }
            message($activity['information'], 'refresh');
        }

        // 兼容会员居住地字段
        foreach ($binds as $key => $value) {
            if ($value == 'reside') {
                unset($binds[$key]);
                $binds[] = 'resideprovince';
                $binds[] = 'residecity';
                $binds[] = 'residedist';
                break;
            }
        }


        if (!empty($_W['fans']['from_user']) && !empty($binds)) {
            $profile = fans_search($_W['fans']['from_user'], $binds);
            if ($profile['gender']) {
                if ($profile['gender'] == '0')
                    $profile['gender'] = '保密';
                if ($profile['gender'] == '1')
                    $profile['gender'] = '男';
                if ($profile['gender'] == '2')
                    $profile['gender'] = '女';
            }
            foreach ($ds as &$r) {
                if ($profile[$r['bind']]) {
                    $r['default'] = $profile[$r['bind']];
                }
            }
        }
        load()->func('tpl');
        include $this->template('submit');
    }

    public function doMobileMyResearch() {
        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $rows = pdo_fetchall("SELECT * FROM " . tablename('research_rows') . " WHERE openid = :openid", array(':openid' => $_W['fans']['from_user']));
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $reids[$row['reid']] = $row['reid'];
                }
                $research = pdo_fetchall("SELECT * FROM " . tablename('research') . " WHERE reid IN (" . implode(',', $reids) . ")", array(), 'reid');
            }
        } elseif ($operation == 'detail') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename('research_rows') . " WHERE openid = :openid AND rerid = :rerid", array(':openid' => $_W['fans']['from_user'], ':rerid' => $id));
            if (empty($row)) {
                message('我的预约不存在或是已经被删除！');
            }
            $research = pdo_fetch("SELECT * FROM " . tablename('research') . " WHERE reid = :reid", array(':reid' => $row['reid']));
            $research['content'] = htmlspecialchars_decode($research['content']);
            $research['fields'] = pdo_fetchall("SELECT a.title, a.type, b.data FROM " . tablename('research_fields') . " AS a LEFT JOIN " . tablename('research_data') . " AS b ON a.refid = b.refid WHERE a.reid = :reid AND b.rerid = :rerid", array(':reid' => $row['reid'], ':rerid' => $id));
        }
        include $this->template('research');
    }

}
