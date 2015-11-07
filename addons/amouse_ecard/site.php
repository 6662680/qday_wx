<?php
/**
 * 模块 0.6
 *
 * @author
 * @url
 */
defined('IN_IA') or exit('Access Denied');
define('AE_ROOT', IA_ROOT . '/addons/amouse_ecard');
class Amouse_ecardModuleSite extends WeModuleSite{
    public function __mobile($f_name){
        global $_W,$_GPC;
        $openid=$_W['fans']['from_user'] ;
        $weid=$_W['uniacid'];
        $setting= $this->get_sysset($weid);
       // $this->checkIsWeixin();

        include_once 'mobile/'.strtolower(substr($f_name,8)).'.php';
    }
    public function doMobileIndex()  { //首页
        require_once "jssdk.php";
        $this->__mobile(__FUNCTION__);
    }

    //小店商城
    public function doMobileShop(){
        $this->__mobile(__FUNCTION__);
    }

    //隐私设置
    public function doMobilePrivateSet(){
        $this->__mobile(__FUNCTION__);
    }
    //编辑名片
    public function doMobSettingEdit(){
        global $_W,$_GPC;
        $from_user =$_W['openid'];
        $op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
        $id = intval($_GPC['id']);
        $cid = intval($_GPC['cid']);
        $member = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid']);
        $card = pdo_fetch(" SELECT * FROM ".tablename('amouse_weicard_card')." WHERE from_user='".$from_user."' AND weid=".$_W['uniacid']);
        include $this->template('qianxian/index_edit');
    }

    //背景图片设置
    public function doMobileBackground(){
        $this->__mobile(__FUNCTION__);
    }

    public function doMobileIndexEdit(){
        $this->__mobile(__FUNCTION__);
    }
    //公司简介
    public function doMobileCompany(){
        $this->__mobile(__FUNCTION__);
    }
    //公司地址
    public function doMobileGps(){
        $this->__mobile(__FUNCTION__);
    }
    //我的人脉
    public function doMobileRenmai(){
        $this->__mobile(__FUNCTION__);
    }
    //推荐人脉
    public function doMobilerecommed(){
        $this->__mobile(__FUNCTION__);
    }
    //音乐
    public function doMobileMusic(){
        $this->__mobile(__FUNCTION__);
    }

    //个人风采
    public function doMobilePresence(){
        $this->__mobile(__FUNCTION__);
    }


    //个人信息
    public function doMobileInfo(){
        require_once "jssdk.php";
        $this->__mobile(__FUNCTION__);
    }
    //个人相册
    public function doMobilePhoto(){
        $this->__mobile(__FUNCTION__);
    }
    //收藏TA
    public function doMobileCollect(){
        $this->__mobile(__FUNCTION__);
    }
    //点赞功能
    //status,0表示未关注或未创建名片，1表示点赞成功，2表示点赞失败,3表示已经点赞过了
    public function doMobileLike(){
        require_once "jssdk.php";
        $this->__mobile(__FUNCTION__);
    }
    //点赞列表
    public function doMobileLikes(){
       $this->__mobile(__FUNCTION__);
    }

    //分享名片
    public function doMobileShare(){
        require_once "jssdk.php";
        $this->__mobile(__FUNCTION__);
    }

    //保存音乐
    public function doMobileSavemusic(){
        global $_GPC, $_W;
        $musicid = intval($_GPC['musicid']);
        $mid = intval($_GPC['mid']);
        $weid = $_W['uniacid'];
        $from_user = $_W['openid'];
        $insert1 = array(
            'weid' => $this->weid,
            'mid' => $mid,
            'musicid' => $musicid,
            'from_user' => $from_user
        );
        $bjyy = pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_bjyy')." WHERE from_user='".$from_user."' AND weid=".$_W['uniacid'] );
        if(empty($bjyy)){
           $add = pdo_insert('amouse_weicard_bjyy', $insert1);
           $bid = pdo_insertid();
        }else{
           $bid=$bjyy['id'];
           pdo_update('amouse_weicard_bjyy',$insert1,array('id'=>$bjyy['id']));
        }
        return $this->heixiuJson(1, $bid,'');
        exit;
    }


    public function getQRImage2($id, $openid) {
        global $_W;
        $weicard = pdo_fetch("select * from " . tablename('amouse_weicard_member') . " where id=:cardid and openid=:openid limit 1", array(":cardid" => $id, ":openid" => $openid));

        require_once 'source/phpqrcode.php';
        $path = "/addons/amouse_ecard";
        $filename = IA_ROOT . $path . "/data/ecard_" . $id . ".png";
        load()->func('file');
        mkdirs(dirname(__FILE__));
        @chmod($filename, $_W['config']['setting']['filemode']);
        $chl = "BEGIN:VCARD\nVERSION:3.0" . //vcard头信息
            "\nFN:" . $weicard['realname'] .
            "\nTEL:" . $weicard['mobile'] .
            "\nEMAIL:" . $weicard['email'] .
            "\nTITLE:" . $weicard['job'] .
            "\nORG:" . $weicard['company'] .
            "\nROLE:" . $weicard['department'] .
            "\nX-QQ:" . $weicard['qq'] .
            "\nADR;WORK;POSTAL:" . $weicard['address'] .
            "\nEND:VCARD"; //vcard尾信息

        $filename2 = "..".$path."/data/ecard_" . $id . ".png";
        QRcode::png($chl, $filename, QR_ECLEVEL_L, 100);
        echo  $filename2;
    }

    //生成二维码图片
    public function CreateQRImage($imgName,$linkUrl,$imgUrl) {
        global $_W;
        $share = $imgName;
        $dir = $imgUrl;
        load()->func('file');
        $flag = file_exists($dir);
        $imgUrl = "../addons/amouse_ecard/data/$imgName";
        if($flag == false){
            require_once AE_ROOT.'/source/phpqrcode.php';
            $value = $linkUrl;
            $errorCorrectionLevel = "L";
            $matrixPointSize = "4";
           $a=QRcode::png($linkUrl, $imgUrl, $errorCorrectionLevel, $matrixPointSize);
        }
        return null;
    }

    //ajax图片上传功能
    private function fileUpload($file, $type) {
        global $_W;
        set_time_limit(0);
        load()->func('file');
        $_W['uploadsetting'] = array();
        $_W['uploadsetting']['images']['folder'] = 'images';
        $_W['uploadsetting']['images']['extentions'] = array('jpg', 'png', 'gif');
        $_W['uploadsetting']['images']['limit'] = 50000;
        $result = array();
        $upload = file_upload($file, 'image');
        if (is_error($upload)) {
            message($upload['message'], '', 'ajax');
        }
        $result['url'] = $upload['url'];
        $result['error'] = 0;
        $result['filename'] = $upload['path'];
        return $result;
    }

    public function doMobileUploadImage() {
        global $_W;
        if (empty($_FILES['file']['name'])) {
            $result['message'] = '请选择要上传的文件！';
            exit(json_encode($result));
        }
        if ($file = $this->fileUpload($_FILES['file'], 'image')) {
            if ($file['error']) {
                exit('0');
                //exit(json_encode($file));
            }
            $result['url'] = $_W['config']['upload']['attachdir'] . $file['filename'];
            $result['error'] = 0;
            $result['filename'] = $file['filename'];
            $result['flag'] = $imgurl;
            exit(json_encode($result));
        }
    }

    function write_cache($filename, $data) {
        global $_W;
        $path =  "/addons/amouse_ecard";
        $filename = IA_ROOT . $path."/data/".$filename.".txt";
        load()->func('file');
        mkdirs(dirname(__FILE__));
        file_put_contents($filename, base64_encode( json_encode($data) ));
        @chmod($filename, $_W['config']['setting']['filemode']);
        return is_file($filename);
    }

    //上传图片
    function tpl_form_field_icon_image($name, $value){
        $thumb = empty($value) ? 'images/global/nopic.jpg' : $value;
        $thumb = tomedia($thumb);
        $html = <<<EOF
<input type="hidden" name="$name" value="$value"  autocomplete="off" readonly="readonly">
<a class="upload-btn" href="#"><i class="ico-upload" onclick="appupload(this)"></i></a>
<div class="row">
<a class="sync-btn" href=""><label for="headUpload">
<img width="120px" class="fillIn-avatar-thumbnail"  src="$thumb">
</label></a>
</div>
<script>
window.appupload = window.appupload || function(obj){
require(['jquery', 'util'], function($, u){
    u.image(obj, function(src){
        $(obj).parent().prev().val(src);
        $(obj).parent().next().find('img').attr('src',u.tomedia(src));
    });
});
}
</script>
EOF;
        return $html;
    }



    //后台管理程序 web文件夹下
    public function __web($f_name) {
        global $_W, $_GPC;
        checklogin();
        $weid = $_W['uniacid'];
        //每个页面都要用的公共信息，今后可以考虑是否要运用到缓存
        include_once 'web/' . strtolower(substr($f_name, 5)) . '.php';
    }

    //名片管理
    public function doWebecard(){
        $this->__web(__FUNCTION__);
    }

    private function checkAuth() {
        global $_W;
        checkauth();
    }

    //导出
    public function doWebExport(){
        $this->__web(__FUNCTION__);
    }

    public function get_sysset($weid = 0){
        global $_W;
        $path = "/addons/amouse_ecard";
        $filename = IA_ROOT . $path . "/data/sysset_" . $_W['uniacid'] . ".txt";
        if (is_file($filename)) {
            $content = file_get_contents($filename);
            if (empty($content)) {
                return false;
            }
            return json_decode(base64_decode($content), true);
        }
        return pdo_fetch("SELECT * FROM " . tablename('amouse_weicard_sysset') . " WHERE weid=:weid limit 1", array(':weid' => $weid));
    }

    //参数设置
    public function doWebSysset(){
        $this->__web(__FUNCTION__);
    }

    //背景图片
    public function doWebBg(){
        $this->__web(__FUNCTION__);
    }
    //行业
    public function doWebIndustry(){
        $this->__web(__FUNCTION__);
    }

    //音乐
    public function doWebMusic(){
        $this->__web(__FUNCTION__);
    }


    private function checkCookie($rid) {
        global $_W, $_GPC;
        $weid = $_W['uniacid'];
        $setting = $this->get_sysset($weid);
        $oauth_openid = "amouse_weicard_201504012101_001_" . $rid . '_' . $weid;
        if (empty($_COOKIE[$oauth_openid])) {
            if (!empty($setting) && !empty($setting['appid']) && !empty($setting['appsecret'])) { // 判断是否是借用设置
                $appid = $setting['appid'];
                $secret = $setting['appsecret'];
            }
            $url = $_W['siteroot'] . "app/" . substr($this->createMobileUrl('userinfo', array('rid' => $rid), true), 2);
            $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
            header("location:$oauth2_code");
            exit;
        }
    }


    public function doMobileUserinfo() {
        global $_GPC, $_W;
        $weid = $_W['uniacid']; //当前公众号ID
        load()->func('communication');
        $rid= $_GPC['rid'];
        //用户不授权返回提示说明
        if ($_GPC['code'] == "authdeny") {
            $url = $this->createMobileUrl('index', array('id' => $rid), true);
            $url2 = $_W['siteroot'] . "app/" . substr($url, 2);
            header("location:$url2");
            exit('authdeny');
        }
        //高级接口取未关注用户Openid
        if (isset($_GPC['code'])) {
            //第二步：获得到了OpenID
            $serverapp = $_W['account']['level'];
            $setting = $this->get_sysset($weid);
            if (!empty($setting) && !empty($setting['appid']) && !empty($setting['appsecret'])) { // 判断是否是借用设置
                $appid = $setting['appid'];
                $secret = $setting['appsecret'];
            }
            $state = $_GPC['state'];
            //1为关注用户, 0为未关注用户
            $code = $_GPC['code'];
            $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
            $content =ihttp_get($oauth2_code);
            $token = @json_decode($content['content'], true);
            if (empty($token) || !is_array($token)
                || empty($token['access_token']) || empty($token['openid'])
            ) {
                echo '<h1>获取微信公众号授权' . $code . '失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
                exit;
            }
            $from_user = $token['openid'];
            //未关注用户和关注用户取全局access_token值的方式不一样
            if ($state == 1) {
                $oauth2_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret . "";
                $content = ihttp_get($oauth2_url);
                $token_all = @ json_decode($content['content'], true);
                if (empty($token_all) || !is_array($token_all) || empty($token_all['access_token'])) {
                    echo '<h1>获取微信公众号授权失败[无法取得access_token], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
                    exit;
                }
                $access_token = $token_all['access_token'];
                $oauth2_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $from_user . "&lang=zh_CN";
            } else {
                $access_token = $token['access_token'];
                $oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$from_user . "&lang=zh_CN";
            }

            //使用全局ACCESS_TOKEN获取OpenID的详细信息
            $content=ihttp_get($oauth2_url);
            $info= @json_decode($content['content'], true);
            if (empty($info) || !is_array($info) || empty($info['openid']) || empty($info['nickname'])) {
                echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！<h1>';
                exit;
            }

            $row = array('nickname' => $info["nickname"], 'realname' => $info["nickname"], 'gender' => $info['sex']);
            if (!empty($info["country"])) {
                $row['nationality'] = $info["country"];
            }
            if (!empty($info["province"])) {
                $row['resideprovince'] = $info["province"];
            }
            if (!empty($info["city"])) {
                $row['residecity'] = $info["city"];
            }
            if (!empty($info["headimgurl"])) {
                $row['avatar'] = $info["headimgurl"];
            }
            fans_update($info['openid'], $row);
            $oauth_openid = "amouse_weicard_201504012101_001_".$rid.'_'.$_W['uniacid'];
            setcookie($oauth_openid, $info['openid'], time() + 3600 * 240);

            $newfans = false;
            $fans = pdo_fetch("select * from " . tablename('amouse_weicard_fans') . " where rid=:rid and openid=:openid limit 1", array(":rid" => $rid, ":openid" => $info['openid']));
            if (!empty($fans)) {
                pdo_update("amouse_weicard_fans", array("headimg" => $info["headimgurl"]), array("openid" => $info['openid']));
            } else {
                $fans = array(
                    "rid" => $rid,
                    "weid"=>$weid,
                    "openid" => $info['openid'],
                    "headimg" => $info["headimgurl"],
                    "createtime" => time()
                );
                pdo_insert("amouse_weicard_fans", $fans);
                $newfans = true;
            }
            $url = $_W['siteroot']."app/".substr($this->createMobileUrl('index', array('id' => $rid,'openid'=>$fans['openid'])), 2);
            header("location:$url");
            exit;
        } else {
            echo '<h1>网页授权域名设置出错!</h1>';
            exit;
        }
    }

    //获取中英文首字母
    public function getfirstchar($s0){
        $s=iconv('UTF-8','gb2312', $s0);
        if (ord($s0)>128) { //汉字开头
            $asc=ord($s{0})*256+ord($s{1})-65536;
            if($asc>=-20319 and $asc<=-20284)return "A";
            if($asc>=-20283 and $asc<=-19776)return "B";
            if($asc>=-19775 and $asc<=-19219)return "C";
            if($asc>=-19218 and $asc<=-18711)return "D";
            if($asc>=-18710 and $asc<=-18527)return "E";
            if($asc>=-18526 and $asc<=-18240)return "F";
            if($asc>=-18239 and $asc<=-17923)return "G";
            if($asc>=-17922 and $asc<=-17418)return "I";
            if($asc>=-17417 and $asc<=-16475)return "J";
            if($asc>=-16474 and $asc<=-16213)return "K";
            if($asc>=-16212 and $asc<=-15641)return "L";
            if($asc>=-15640 and $asc<=-15166)return "M";
            if($asc>=-15165 and $asc<=-14923)return "N";
            if($asc>=-14922 and $asc<=-14915)return "O";
            if($asc>=-14914 and $asc<=-14631)return "P";
            if($asc>=-14630 and $asc<=-14150)return "Q";
            if($asc>=-14149 and $asc<=-14091)return "R";
            if($asc>=-14090 and $asc<=-13319)return "S";
            if($asc>=-13318 and $asc<=-12839)return "T";
            if($asc>=-12838 and $asc<=-12557)return "W";
            if($asc>=-12556 and $asc<=-11848)return "X";
            if($asc>=-11847 and $asc<=-11056)return "Y";
            if($asc>=-11055 and $asc<=-10247)return "Z";
        }else if(ord($s)>=48 and ord($s)<=57){ //数字开头
            switch(iconv_substr($s,0,1,'utf-8'))
            {
                case 1:return "Y";
                case 2:return "E";
                case 3:return "S";
                case 4:return "S";
                case 5:return "W";
                case 6:return "L";
                case 7:return "Q";
                case 8:return "B";
                case 9:return "J";
                case 0:return "L";
            }
        }else if(ord($s)>=65 and ord($s)<=90){ //大写英文开头
            return substr($s,0,1);
        }else if(ord($s)>=97 and ord($s)<=122){ //小写英文开头
            return strtoupper(substr($s,0,1));
        }
        else
        {
            return iconv_substr($s0,0,1,'utf-8');//中英混合的词语，不适合上面的各种情况，因此直接提取首个字符即可
        }

    }

    public function hehe($string = null) {
        // 将字符串分解为单元
        $name = $string;
        preg_match_all("/./us", $string, $match);
        if(count($match[0])>12){
            $mname = '';
            for($i=0; $i<12; $i++){
                $mname = $mname.$match[0][$i];
            }
            $name = $mname.'...';
        }
        return $name;
    }


    //二维数组排序
    public function sysSortArray($ArrayData,$KeyName1,$SortOrder1 = "SORT_ASC",$SortType1 = "SORT_REGULAR")
    {
        if(!is_array($ArrayData))
        {
            return $ArrayData;
        }
        $ArgCount = func_num_args();
        for($I = 1;$I < $ArgCount;$I ++)
        {
            $Arg = func_get_arg($I);
            if(!preg_match("/SORT/",$Arg))
            {
                $KeyNameList[] = $Arg;
                $SortRule[] = '$'.$Arg;
            }
            else
            {
                $SortRule[] = $Arg;
            }
        }
        foreach($ArrayData AS $Key => $Info)
        {
            foreach($KeyNameList AS $KeyName)
            {
                ${$KeyName}[$Key] = $Info[$KeyName];
            }
        }
        $EvalString = 'array_multisort('.join(",",$SortRule).',$ArrayData);';
        eval ($EvalString);
        return $ArrayData;
    }


    public function heixiuJson($resultCode, $resultData, $resultMsg){
        $jsonArray = array(
            'resultCode' => $resultCode,
            'resultData' => $resultData,
            'resultMsg' => $resultMsg
        );
        $jsonStr = json_encode($jsonArray);
        return $jsonStr;
    }


    private function checkIsWeixin() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
            exit;
        }
    }
}
?>