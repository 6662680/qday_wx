<?php
/**
 * @param $upfile
 * @param $upload_path
 * @param $maxfilesize
 * @param $uptypes
 * @return string
 * 上传企业营业执照
 */
function uploadlicensImage($upfile, $upload_path, $maxfilesize, $uptypes){
    $name = $upfile['name'];
    $type = $upfile['type'];
    $size = $upfile['size'];
    $tmp_name = $upfile['tmp_name'];
    $error = $upfile['error'];

    if(intval($error) > 0){
        message('上传错误：错误代码：'.$error, 'referer', 'error');
    }else {
        if($maxfilesize > 0){
            if($size > $maxfilesize * 1024 * 1024){
                message('上传文件过大'.$_FILES["file"]["error"], 'referer', 'error');
            }
        }
        //判断文件的类型
        if (!in_array($type, $uptypes)) {
            message('上传文件类型不符：'.$type, 'referer', 'error');
        }
        //存放目录
        if(!file_exists($upload_path)){
            mkdir($upload_path);
        }
        //移动文件
        if(!move_uploaded_file($tmp_name, $upload_path.date("YmdHis").'_'.$name)){
            message('移动文件失败，请检查服务器权限', 'referer', 'error');
        }
        //营业执照进行缩略
        $srcfile = $upload_path.date("YmdHis").'_'.$name;
        $desfile = $upload_path.date("YmdHis").'_thumb_'.$name;
        $ret = file_image_thumb($srcfile, $desfile, '320');
        if(!is_array($ret)){
            //路径存入数据库
            return date("YmdHis").'_thumb_'.$name;
        }
    }
}

function uploadAvatarImage($upfile, $upload_path, $maxfilesize, $uptypes){
    $name = $upfile['name'];
    $type = $upfile['type'];
    $size = $upfile['size'];
    $tmp_name = $upfile['tmp_name'];
    $error = $upfile['error'];
    //上传路径
    if(intval($error) > 0){
        exit('上传错误：错误代码：'.$error);
    }else {

        //上传文件大小0为不限制，默认2M

        if($maxfilesize > 0){
            if($size > $maxfilesize * 1024 * 1024){
                exit('上传文件过大'.$_FILES["file"]["error"]);
            }
        }

        //允许上传的图片类型

        //判断文件的类型
        if (!in_array($type, $uptypes)) {
            exit('上传文件类型不符：'.$type);
        }
        //存放目录
        if(!file_exists($upload_path)){
            mkdir($upload_path);
        }
        //取文件后缀
        //$suffix = strrev( substr(strrev($name), 0, strpos(strrev($name), '.')));
        //移动文件
        $source_filename = $person_id.'_'.date("Ymd");
        $target_filename = $person_id.'_'.date("Ymd").'.thumb.jpg';

        if(!move_uploaded_file($tmp_name, $upload_path.$source_filename)){
            exit('移动文件失败');
        }
        //营业执照进行缩略
        $srcfile = $upload_path.$source_filename;
        $desfile = $upload_path.$target_filename;
        //文件操作类
        load()->func('file');
        $ret = file_image_thumb($srcfile, $desfile, 320);
        //$ret = file_image_crop($srcfile, $desfile, 400, 400 ,5);//裁剪
        if(!is_array($ret)){
            //路径存入数据库
            $person_data['headimgurl'] = $target_filename;
        }
        //删除原图
        unlink($srcfile);
    }

}
?>