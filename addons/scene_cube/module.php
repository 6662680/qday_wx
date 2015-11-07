<?php
/**
 * 图片魔方模块定义
 *
 * @author 伊索科技
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Scene_cubeModule extends WeModule {
    public $table_reply  = 'scene_cube_reply';
    public $table_list  = 'scene_cube_list';

    public function fieldsFormDisplay($rid = 0) {
        global $_W;
        if($rid) {
            $reply = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE rid = :rid", array(':rid' => $rid));
            $sql = 'SELECT id,reply_title,reply_thumb,reply_description FROM ' . tablename($this->table_list) . ' WHERE `weid`=:weid AND `id`=:list_id';
            $activity = pdo_fetch($sql, array(':weid' => $_W['weid'], ':list_id' => $reply['list_id']));
        }
        include $this->template('form');
    }

    public function fieldsFormValidate($rid = 0) {
        global $_W, $_GPC;
        $list_id= intval($_GPC['activity']);
        if(!empty($list_id)) {
            $sql = 'SELECT * FROM ' . tablename($this->table_list) . " WHERE `id`=:list_id";
            $params = array();
            $params[':list_id'] = $list_id;
            $activity = pdo_fetch($sql, $params);
            return ;
            if(!empty($activity)) {
                return '';
            }
        }
        return '没有选择合适的场景';
    }

    public function fieldsFormSubmit($rid) {
        global $_GPC;
        $list_id = intval($_GPC['activity']);
        $record = array();
        $record['list_id'] = $list_id;
        $record['rid'] = $rid;
        $reply = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE rid = :rid", array(':rid' => $rid));
        if($reply) {
            pdo_update($this->table_reply, $record, array('id' => $reply['id']));
        } else {
            pdo_insert($this->table_reply, $record);
        }
    }

    public function ruleDeleted($rid) {
        pdo_delete($this->table_reply, array('rid' => $rid));
    }
    public function doUploadMusic() {
        global $_W;

        if (empty($_FILES['imgFile']['name'])) {
            $result['message'] = '请选择要上传的音乐！';
            exit(json_encode($result));
        }

        if ($_FILES['imgFile']['error'] != 0) {
            $result['message'] = '上传失败，请重试！';
            exit(json_encode($result));
        }
        if ($file = $this->fileUpload($_FILES['imgFile'], 'music')) {
            if (!$file['success']) {
                exit(json_encode($file));
            }
            $result['url'] = $_W['config']['upload']['attachdir'] . $file['path'];
            $result['error'] = 0;
            $result['filename'] = $file['path'];
            exit(json_encode($result));
        }
    }
    private function fileUpload($file, $type) {
        global $_W;
        set_time_limit(0);
        $_W['uploadsetting'] = array();
        $_W['uploadsetting']['music']['folder'] = 'music/' . $_W['weid'];
        $_W['uploadsetting']['music']['extentions'] = array('mp3', 'wma', 'wav', 'amr');
        $_W['uploadsetting']['music']['limit'] = 50000;
        $result = array();
        $upload = file_upload($file, 'music');
        if (is_error($upload)) {
            message($upload['message'], '', 'ajax');
        }
        $result['url'] = $_W['config']['upload']['attachdir'].$upload['path'];
        $result['error'] = 0;
        $result['filename'] = $upload['path'];
        return $result;
    }
    public function doManageMusic(){
        global $_W,$_GPC	;
        $dir = $_GPC['dir'] ? $_GPC['dir'] : '';
        $path = !empty($_GPC['path']) ? $_GPC['path'] : $_W['weid'] . '/';
        $order = empty($_GPC['order']) ? 'name' : strtolower($_GPC['order']);
        $rootpath = IA_ROOT . '/resource/attachment/music/';
        $exts = array('gif', 'jpg', 'jpeg', 'png', 'bmp');

        if (empty($path)) {
            $currentpath = $rootpath;
            $parentpath = '';
        } else {
            $currentpath = $rootpath . $path;
            $parentpath = preg_replace('/(.*?)[^\/]+\/$/', '$1', $path);
        }
        if (preg_match('/\.\./', $currentpath)) {
            echo 'Access is not allowed.';
            exit;
        }
        //最后一个字符不是/
        if (!preg_match('/\/$/', $currentpath)) {
            echo 'Parameter is not valid.';
            exit;
        }

        function cmp_func($a, $b) {
            global $order;
            if ($a['is_dir'] && !$b['is_dir']) {
                return -1;
            } else if (!$a['is_dir'] && $b['is_dir']) {
                return 1;
            } else {
                if ($order == 'size') {
                    if ($a['filesize'] > $b['filesize']) {
                        return 1;
                    } else if ($a['filesize'] < $b['filesize']) {
                        return -1;
                    } else {
                        return 0;
                    }
                } else if ($order == 'type') {
                    return strcmp($a['filetype'], $b['filetype']);
                } else {
                    return strcmp($a['filename'], $b['filename']);
                }
            }
        }
        //遍历目录取得文件信息
        $files = array();
        if (is_dir($currentpath)) {
            if ($handle = opendir($currentpath)) {
                while (false !== ($filename = readdir($handle))) {
                    if ($filename{0} == '.') continue;
                    $file = $currentpath . $filename;
                    if (is_dir($file)) {
                        $files[] = array(
                            'filename' => $filename,
                            'is_dir' => true,
                            'is_photo' => false,
                            'has_file' => true,
                            'filesize' => 0,
                            'filetype' => '',
                            'datetime' => date('Y-m-d H:i:s', filemtime($file)),
                        );
                    } else {
                        $fileext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $files[] = array(
                            'filename' => $filename,
                            'is_dir' => false,
                            'is_photo' => in_array($fileext, $exts),
                            'has_file' => false,
                            'filesize' => filesize($file),
                            'filetype' => $fileext,
                            'dir_path' => '',
                            'datetime' => date('Y-m-d H:i:s', filemtime($file)),
                        );
                    }
                }
            }
        }
        usort($files, 'cmp_func');

        $result = array();
        $result['moveup_dir_path'] = $parentpath;
        $result['current_dir_path'] = $path;
        $result['current_url'] = $_W['attachurl'] . '/music/' . $path;
        $result['total_count'] = count($files);
        $result['file_list'] = $files;
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($result);

    }
}