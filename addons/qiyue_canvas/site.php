<?php

/**
 * 画图分享模块微站定义
 *
 */
defined('IN_IA') or exit('Access Denied');

class Qiyue_canvasModuleSite extends WeModuleSite {
    /* ----- 功能函数 ----- */

    // 返回全部
    public function all_list($params = array()) {
        global $_GPC, $_W;
        extract($params);
        $result = array();
        $pindex = max(1, intval($_GPC['page']));
        $psize = $psize ? $psize : '20';
        $where = "WHERE `uniacid` = :uniacid AND ischeck=:ischeck";
        $paras = array();
        $paras[':uniacid'] = $_W['uniacid'];
        $paras[':ischeck'] = intval($ischeck);
        $sql = "SELECT * FROM " . tablename('qiyue_canvas') . $where . ' ORDER BY createtime DESC, id DESC';
        $result['list'] = pdo_fetchall($sql . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $paras);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('qiyue_canvas') . $where, $paras);
        if ($_W['isajax']) {
            $context['ajaxcallback'] = 1;
            $result['pager'] = pagination($total, $pindex, $psize, '', $context);
        } else {
            $result['pager'] = pagination($total, $pindex, $psize);
        }

        if (!empty($result['list'])) {
            foreach ($result['list'] as &$row) {
                $row['url'] = url('mobile/module/index', array('name' => 'site', 'id' => $row['id'], 'weid' => $_W['weid']));
            }
        }
        return $result;
    }

    // 返回单条
    public function item_fetch($id) {
        $item = array();
        if (intval($id)) {
            $item = pdo_fetch("SELECT * FROM " . tablename('qiyue_canvas') . " WHERE id=:id", array(':id' => $id));
        }
        return $item;
    }

    // 审核
    public function item_check($id) {
        $check = $this->item_fetch($id);
        if ($check['id']) {
            $item = pdo_update('qiyue_canvas', array('ischeck' => 1), array('id' => $check['id']));
            return true;
        }
        return false;
    }

    /* ----- WEB 端 ----- */

    // 例表
    public function doWebList($ischeck = 1) {
        global $_GPC, $_W;
        // AJAX
        if ($_W['isajax']) {
            $op = $_GPC['op'];
            $id = intval($_GPC['id']);
            $result = array('state' => -1, 'message' => '');
            if ($op == 'delete') {
                $item = $this->item_fetch($id);
                if ($item['id']) {
                    load()->func('file');
                    file_delete($item['photo']);
                    pdo_delete('qiyue_canvas', array('id' => $item['id']));
                    $result['state'] = 0;
                }
            } elseif ($op == 'check') {
                if ($this->item_check($id)) {
                    $result['state'] = 0;
                }
            }
            message($result, '', 'ajax');
        }
        $title = '图片管理';
        $result = $this->all_list(array('ischeck' => $ischeck));
        include $this->template('manage');
    }

    public function doWebCheck() {
        $this->doWebList(0);
    }

    /* ----- 移动 端 ----- */

    public function doMobileIndex() {
        global $_W, $_GPC, $action;
        //上传
        if ($action == 'upload' && $_W['ispost']) {
            $dataStr = $_GPC['dataStr'];
            if ($dataStr) {
                load()->func('file');
                $path = IA_ROOT . '/' . $_W['config']['upload']['attachdir'] . '/';
                mkdirs(dirname($path));
                $filename = date('YmdHis', time()) . $this->no_make_password(13);
                // $filename = random(30);
                $path = 'images/' . $_W['uniacid'] . '/' . date('Y/m/') . $filename . '.jpg';
                $data = base64_decode($dataStr);
                file_write($path, $data);
                echo $filename;
                if ($_W['uniacid']) {
                    $add = array();
                    $add['uniacid'] = $_W['uniacid'];
                    $add['attach'] = $path;
                    $add['createtime'] = TIMESTAMP;
                    pdo_insert('qiyue_canvas', $add);
                }
                exit;
            }
        }
        $config = $this->module['config'];
        $fors = array('bg', 'paper', 'logo', 'share_icon', 'banner_img');
        foreach ($fors as &$val) {
            if ($config[$val]) {
                $config[$val] = tomedia($config[$val]);
            } else {
                $config[$val] = MODULE_URL . 'template/mobile/images/' . $val . '.jpg';
            }
        }
        include $this->template('index');
    }

    public function doMobileList() {
        global $_W, $_GPC, $action;
        $config = $this->module['config'];
        $title = $config['title'];
        $ischeck = intval($_GPC['ischeck']);
        $result = $this->all_list(array('ischeck' => $ischeck, 'psize' => 12));
        include $this->template('list');
    }

    /* ----- 功能函数 ----- */

    //取得随机数(数字)
    private function no_make_password($pw_length) {
        $low_ascii_bound = 48;
        $upper_ascii_bound = 57;
        $notuse = array(58, 59, 60, 61, 62, 63, 64, 73, 79, 91, 92, 93, 94, 95, 96, 108, 111);
        while ($i < $pw_length) {
            mt_srand((double) microtime() * 1000000);
            $randnum = mt_rand($low_ascii_bound, $upper_ascii_bound);
            if (!in_array($randnum, $notuse)) {
                $password1 = $password1 . chr($randnum);
                $i++;
            }
        }
        return $password1;
    }

}
