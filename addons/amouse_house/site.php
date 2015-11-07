<?php
/**
 * @url
 */
defined('IN_IA') or exit('Access Denied');
require_once "jssdk.php";
class Amouse_HouseModuleSite extends WeModuleSite {

    //后台管理程序 web文件夹下
    public function __web($f_name) {
        global $_W, $_GPC;
        checklogin();
        $weid= $_W['uniacid'];
        //每个页面都要用的公共信息，今后可以考虑是否要运用到缓存
        include_once 'web/'.strtolower(substr($f_name, 5)).'.php';
    }

    //首页
    public function doMobileIndex(){
        $this->__mobile(__FUNCTION__);
    }

    public function doMobileNew(){
        $this->__mobile(__FUNCTION__);
    }

    public function doMobileDetail(){
        $this->__mobile(__FUNCTION__);
    }

    //新楼盘
    public function doMobileList(){
        $this->__mobile(__FUNCTION__);
    }

    public function __mobile($f_name){
        global $_W,$_GPC;
        $openid=$_W['fans']['from_user'] ;
        $weid=$_W['uniacid'];
        $setting= $this->get_sysset($weid);
        $oauth_openid="amouse_house_zombie_".$_W['uniacid'];
        if (empty($_COOKIE[$oauth_openid])) {
            if(!empty($setting) && $setting['isoauth'] == '0') {
                $this->checkCookie();
            }
        }
        include_once 'mobile/'.strtolower(substr($f_name,8)).'.php';
    }
    //提交信息
    public function doMobileNewSubmit() {
        global $_GPC, $_W;
        $wxid= !empty($_GPC['wxid']) ? $_GPC['wxid'] : $_W['fans']['from_user'];
        $data = array(
            'weid'=> $_W['uniacid'],
            'title'=> $_GPC['title'],
            'price'=> $_GPC['price'],
            'square_price'=> $_GPC['square_price'],
            'area'=>$_GPC['area'],
            'house_type'=> $_GPC['house_type'],
            'floor'=> $_GPC['floor'],
            'orientation'=> $_GPC['orientation'],
            'createtime'=> TIMESTAMP,
            'type'=> $_GPC['type'],
            'status'=> 0,
            'recommed'=>0,
            'contacts'=> $_GPC['contacts'],
            'phone'=> $_GPC['phone'],
            'introduction'=> $_GPC['introduction'],
            'openid'=> $wxid,
        );
        pdo_insert('amouse_house',$data);
        return $this->heixiuJson(1,$wxid,$_GPC['type']);
        exit;
    }

    public function heixiuJson($resultCode,$resultData, $resultMsg) {
        $jsonArray = array(
            'resultCode' => $resultCode,
            'resultData' => $resultData,
            'resultMsg' => $resultMsg
        );
        $jsonStr = json_encode($jsonArray);
        return $jsonStr;
    }

    public function doMobileUpdateCount(){
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $op = !empty($_GPC['op']) ? $_GPC['op'] : 'read';
        $result['state'] = 0 ;
        $result['msg'] = ' ';
        $detail = pdo_fetch("SELECT * FROM " . tablename('amouse_newflats') . " WHERE `id`=:id", array(':id'=>$id));
        if($detail){
            if($op=="read"){
                $data=array('readcount'=>$detail['readcount']+1);
                pdo_update('amouse_newflats', $data, array('id' => $id));
            }elseif($op=="like"){
                $data2=array('like'=>$detail['like']+1);
                pdo_update('amouse_newflats',$data2, array('id' => $id));
            }
            $result['msg'] = ' ';
            $result['state'] = 1 ;
        }
        message($result, '', 'ajax');
    }

    private function checkIsWeixin(){
        $user_agent= $_SERVER['HTTP_USER_AGENT'];
        if(strpos($user_agent, 'MicroMessenger') === false) {
            echo "本页面仅支持微信访问!非微信浏览器禁止浏览!";
            exit;
        }
    }

    private function checkCookie() {
        global $_W,$_GPC;
        $weid=$_W['uniacid'];
        $setting= $this->get_sysset($weid);
        $oauth_openid= "amouse_house_zombie_".$weid;
        if(empty($_COOKIE[$oauth_openid])) {
            if(!empty($setting) && $setting['isoauth'] == '0') {
                if(!empty($setting) && !empty($setting['appid']) && !empty($setting['appsecret'])) { // 判断是否是借用设置
                    $appid= $setting['appid'];
                    $secret= $setting['appsecret'];
                }
            }
            $url =  $_W['siteroot']."app/".substr($this->createMobileUrl('userinfo',array(),true),2);
            $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
            header("location:$oauth2_code");
            exit;
        }
    }


    public function doMobileUserinfo() {
        global $_GPC, $_W;
        $weid= $_W['uniacid']; //当前公众号ID
        load()->func('communication');
        //用户不授权返回提示说明
        if($_GPC['code'] == "authdeny") {
            $url = $this->createMobileUrl('index', array(),true);
            $url2 =  $_W['siteroot']."app/".substr($url,2);
            header("location:$url2");
            exit('authdeny');
        }
        //高级接口取未关注用户Openid
        if(isset($_GPC['code'])) {
            //第二步：获得到了OpenID
            $serverapp= $_W['account']['level'];
            $setting= $this->get_sysset($weid);
            if(!empty($setting) && !empty($setting['appid']) && !empty($setting['appsecret'])) { // 判断是否是借用设置
                $appid= $setting['appid'];
                $secret= $setting['appsecret'];
            }
            $state= $_GPC['state'];
            //1为关注用户, 0为未关注用户
            $rid= $_GPC['id'];
            //查询活动时间
            $code= $_GPC['code'];
            $oauth2_code= "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
            $content= ihttp_get($oauth2_code);
            $token= @ json_decode($content['content'], true);
            if(empty($token) || !is_array($token)
                || empty($token['access_token']) || empty($token['openid'])) {
                echo '<h1>获取微信公众号授权'.$code.'失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />'.$content['meta'].'<h1>';
                exit;
            }
            $from_user= $token['openid'];
            //未关注用户和关注用户取全局access_token值的方式不一样
            if($state == 1) {
                $oauth2_url= "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";
                $content= ihttp_get($oauth2_url);
                $token_all= @ json_decode($content['content'], true);
                if(empty($token_all) || !is_array($token_all) || empty($token_all['access_token'])) {
                    echo '<h1>获取微信公众号授权失败[无法取得access_token], 请稍后重试！ 公众平台返回原始数据为: <br />'.$content['meta'].'<h1>';
                    exit;
                }
                $access_token= $token_all['access_token'];
                $oauth2_url= "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";
            } else {
                $access_token= $token['access_token'];
                $oauth2_url= "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";
            }

            //使用全局ACCESS_TOKEN获取OpenID的详细信息
            $content= ihttp_get($oauth2_url);
            $info= @ json_decode($content['content'], true);
            if(empty($info) || !is_array($info) || empty($info['openid']) || empty($info['nickname'])) {
                echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！<h1>';
                exit;
            }

            $row= array('nickname' => $info["nickname"], 'realname' => $info["nickname"], 'gender' => $info['sex']);
            if(!empty($info["country"])) {
                $row['nationality']= $info["country"];
            }
            if(!empty($info["province"])) {
                $row['resideprovince']= $info["province"];
            }
            if(!empty($info["city"])) {
                $row['residecity']= $info["city"];
            }
            if(!empty($info["headimgurl"])) {
                $row['avatar']= $info["headimgurl"];
            }
            fans_update($info['openid'], $row);
            $oauth_openid= "amouse_house_zombie_".$_W['uniacid'];
            setcookie($oauth_openid, $info['openid'], time() + 3600 * 240);
            $url =   $_W['siteroot']."app/".substr($this->createMobileUrl('index',array()),2);
            header("location:$url");
            exit;
        } else {
            echo '<h1>网页授权域名设置出错!</h1>';
            exit;
        }
    }

    //房产管理
    public function doWebHouse() {
        $this->__web(__FUNCTION__);
    }
    //订单导出
    public function doWebExport() {
        $this->__web(__FUNCTION__);
    }
    //单条订单导出
    public function doWebExport2() {
        $this->__web(__FUNCTION__);
    }

    //新楼盘管理
    public function doWebPremises(){
        $this->__web(__FUNCTION__);
    }

    public function get_sysset($weid=0) {
        global $_GPC, $_W;
        return pdo_fetch("SELECT * FROM ".tablename('amouse_sysset')." WHERE weid=:weid limit 1", array(':weid' => $weid));
    }

    //参数设置
    public function doWebSysset() {
        global $_W, $_GPC;
        $weid= $_W['uniacid'];
        $set= $this->get_sysset($weid);
        load()->func('tpl');
        if(checksubmit('submit')) {
            $data= array(
                'weid' => $weid,
                'guanzhuUrl'=>$_GPC['guanzhuUrl'],
                'copyright'=>$_GPC['copyright'],
                'newflat_images'=>$_GPC['newflat_images'],
                'appid_share'=>$_GPC['appid_share'],
                'appsecret_share'=>$_GPC['appsecret_share'],
                'isoauth' => $_GPC['isoauth']
            );
            if($_GPC['isoauth']==0){
                $data['appid']=$_GPC['appid'] ;
                $data['appsecret']=$_GPC['appsecret'];
            }else{
                $data['appid']='' ;
                $data['appsecret']='';
            }
            if(!empty($set)) {
                pdo_update('amouse_sysset', $data, array('id' => $set['id']));
            } else {
                pdo_insert('amouse_sysset', $data);
            }
            message('更新参数设置成功！', 'refresh');
        }
        if(!isset($set['isoauth'])) {
            $set['isoauth']= 1;
        }
        include $this->template('web/sysset');
    }
}
?>