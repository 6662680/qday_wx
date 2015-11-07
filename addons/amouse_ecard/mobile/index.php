<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-5
 * Time: 下午1:31
 * To change this template use File | Settings | File Templates.
 */
//首页
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
load()->func('file');
load()->func('tpl');
$from_user = empty($_W['openid'])?$_GPC['wid']:$_W['openid'];
//如果用户头像为空或不存在，使用高级接口
/*
$check = pdo_fetchcolumn("SELECT headimg FROM ".tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid']);
if (empty($check)) {
    //测试高级接口
    $oauth_openid = "eweicard" .$_W['uniacid'];
    if (empty($_COOKIE[$oauth_openid])) {
        $appid = $_W['account']['key'];
        $secret = $_W['account']['secret'];
        //是否为高级号
        $serverapp = $_W['account']['level'];
        if ($serverapp == 2) {
            //借用的
            $url = $_W['siteroot'].$this->createMobileUrl('Oauth2',array('list_id' =>$_GPC['list_id'],'do' => 'index'));
            $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
            header("location:$oauth2_code");
        }
    }
}*/

$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
if ($op == 'list') {
    //判断是否关注
    if (empty($from_user)){
        header('Location: '. $setting['guanzhuUrl']);
        exit;
    }
       // message("请关注公众号".$_W['account']['name']);
    //判断是否已注册
    $member = pdo_fetch("SELECT * FROM " . tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=" . $_W['uniacid']);
    //$erweima=$this->getQRImage($member['id'],$member['openid']);
    if (empty($member)) {
        $member['headimg']="../addons/amouse_ecard/style/images/header.png";
        $industrys = pdo_fetchall("SELECT * FROM " . tablename('amouse_weicard_industry') );
        include $this->template('regist');
        exit();
    } else {
        $card = pdo_fetch("SELECT * FROM " . tablename('amouse_weicard_card')." WHERE from_user='".$from_user."' AND weid=" .$_W['uniacid']);
        if(empty($card)||$card == false) {
            $data2 = array(
            'weid' => $_W['uniacid'],
            'from_user' =>$from_user,
            'mid' =>$member['id'],
            'mobile' => '18888888888',
            'weixin' => 0,
            'email' => 0,
            'address' =>'无锡新区' );

            $flag2 = pdo_insert('amouse_weicard_card', $data2);
            $card = pdo_fetch(" SELECT * FROM ".tablename('amouse_weicard_card')." WHERE from_user='".$from_user."' AND weid=".$_W['uniacid']);
        }

        $linkUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('share', array('id'=>$member['id'],'cid' =>$card['id'],'wid'=>$member['openid']),true);
        $shareimg = toimage($member['headimg']);
        /*//生成二维码图片
        $imgName = $member['id']."amouseerweima_".$_W['uniacid'].".png";

        $path = "/addons/amouse_ecard";
        $filename = IA_ROOT . $path . "/data/".$imgName;
        $imgUrl = "addons/amouse_eicard/data/$imgName";
        $a = $this->CreateQRImage($imgName,$linkUrl,$imgUrl);*/

        $isCompany = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_companyinfo')." WHERE mid=".$member['id']);
        $isPhoto = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_photo')." WHERE mid=" . $member['id']);
        $ispresence = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_presence')." WHERE mid=".$member['id']);
        //背景音乐
        $isbjyy = pdo_fetchcolumn("SELECT musicid FROM ".tablename('amouse_weicard_bjyy')." WHERE mid=".$member['id']);
        if (!empty($isbjyy)){
           $musicUrl=pdo_fetchcolumn("SELECT musicUrl FROM ".tablename('amouse_weicard_music')." WHERE id=".$isbjyy);
            if (strexists($musicUrl, 'http://')||strexists($musicUrl, 'https://')) {
                $musicUrl=$musicUrl;
            } else {
                $musicUrl= $_W['attachurl'] .$musicUrl;
            }
        }
        //人脉数量
        $renmai = pdo_fetchcolumn("SELECT COUNT(m.id) FROM ".tablename('amouse_weicard_mycollect') . " AS c LEFT JOIN " . tablename('amouse_weicard_member')." AS m ON m.id=c.collect_mid WHERE c.mid=".$member['id']." AND m.weid=".$_W['uniacid']." AND c.weid=".$_W['uniacid']);

        //$b=$this->getQRImage($member['id'],$from_user);
        include $this->template('qianx_index');
        exit();
    }
}

//注册数据提交
if ($op== 'post') {
    !empty($_GPC['realname']) ? trim($_GPC['realname']) : message('请填写您的姓名');
    !empty($_GPC['job']) ? trim($_GPC['job']) : message('请填写您的职业');
    !empty($_GPC['telphone']) ? trim($_GPC['telphone']) : message('请填写您的手机号码');
    $data = array(
        'weid' => $_W['uniacid'],
        'openid' => $from_user,
        'realname' => $_GPC['realname'],
        'mobile' => $_GPC['telphone'],
        'company' => $_GPC['company'],
        'headimg' => $_GPC['headimg'],
        'job' => $_GPC['job'],
        'industry' => $_GPC['industry'],
        'department' => $_GPC['department'],
        'province' => $_GPC['province'],
        'address' => $_GPC['address'],
        'qq' => $_GPC['qq'],
        'weixin' => $_GPC['weixin'],
        'qianming' => $_GPC['qianming'],
        'email' => $_GPC['email'],
        'myattention' => $_GPC['myattention'],
        'myfocus' => $_GPC['myfocus'],
        'companyAddress' => $_GPC['companyAddress'],
        'createtime' => TIMESTAMP,
    );

    if (empty($_GPC['id'])) {
        //检测是否已经被注册了
        $check = pdo_fetchcolumn("SELECT id FROM " . tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid']);
        if (!empty($check)) message('您已经注册过了！');
        $flag = pdo_insert('amouse_weicard_member', $data);
        $mid = pdo_insertid();
        //同时注册名片表
        $date2 = array(
            'weid' =>$_W['uniacid'],
            'from_user'=>$from_user,
            'mid' => $mid,
            'mobile' => 0,
            'weixin' => 0,
            'email' => 0,
            'address' => 0,
        );
        $flag2 = pdo_insert('amouse_weicard_card', $date2);
        if ($flag == false) {
            message('提交数据失败，请返回重试');
        } else {
            message('提交数据成功', $this->createMobileUrl('index', array('op'=>'list','weid'=>$_W['uniacid'],'wid'=>$from_user),true),'success');
        }
    } else {
        $data['id'] = $_GPC['id'];
        $data['status'] = $_GPC['status'];
        pdo_update('amouse_weicard_member', $data, array('id' => $_GPC['id']));
        message('提交数据成功', $this->createMobileUrl('index', array('op' => 'list','weid' =>$_W['uniacid'],'wid'=>$from_user),true),'success');
    }
}
//编辑
if ($op == 'edit') {
$id = intval($_GPC['id']);
if (empty($id)) {
    $id=pdo_fetchcolumn("SELECT id FROM " .tablename('amouse_weicard_member')." WHERE openid='".$_W['openid']."' AND weid=".$_W['uniacid']);
}

$member=pdo_fetch("SELECT * FROM " . tablename('amouse_weicard_member')." WHERE id=".$id);
$industrys=pdo_fetchall("SELECT * FROM " . tablename('amouse_weicard_industry') . " ORDER BY `displayorder` ASC " );
$member=pdo_fetch(" SELECT * FROM " . tablename('amouse_weicard_member') . " WHERE openid='".$from_user."' AND weid=".$_W['uniacid']);
}

include $this->template('regist');
