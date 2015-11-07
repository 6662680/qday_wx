<?php
/**
 * Created by IntelliJ IDEA.
 * User: shizhongying
 * QQ:214983937
 * Date: 15-4-6
 * Time: 下午1:19
 * To change this template use File | Settings | File Templates.
 */
global $_W,$_GPC;

$from_user = empty($_W['openid'])?$_GPC['wid']:$_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = $_GPC['id'];
$cid = $_GPC['cid'];
//如果是从人脉页面跳转过来的，则没有cid
if($op == 'renmai'){
    $cid = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_card')." WHERE mid=".$id);
    if(empty($cid)){
        message('名片不存在！');
    }
}
$member=pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_member')." WHERE id=".$id);
$card=pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_card')." WHERE id=".$cid);
$guanzhuUrl=pdo_fetchcolumn("SELECT guanzhuUrl FROM ".tablename('amouse_weicard_sysset')." WHERE weid=".$_W['uniacid']);
//生成二维码图片
$linkUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('share', array('id'=>$member['id'],'cid' =>$card['id'],'wid'=>$member['openid']),true);
//生成二维码图片
$imgName = $member['id']."amouseerweima_".$_W['uniacid'].".png";
$shareimg = toimage($member['headimg']);
$path = "/addons/amouse_eicard";
$filename = IA_ROOT . $path . "/data/".$imgName;
$imgUrl = "addons/amouse_ecard/data/$imgName";
$a = $this->CreateQRImage($imgName,$linkUrl,$imgUrl);

$isCompany=pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_companyinfo')." WHERE mid=".$member['id']);
$isPhoto=pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_photo')." WHERE mid=".$member['id']);
$ispresence=pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_presence')." WHERE mid=".$member['id']);
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
$renmai=pdo_fetchcolumn("SELECT COUNT(m.id) FROM ".tablename('amouse_weicard_mycollect')." AS c LEFT JOIN ".tablename('amouse_weicard_member')." AS m ON m.id=c.collect_mid WHERE c.mid=".$id." AND c.weid=".$_W['uniacid']." AND m.weid=".$_W['uniacid']);

if($member['from_user'] ==$from_user){
    include $this->template('qianx_index');
    exit();
}else{
    //隐私设置
    //0代表全部可见，1代表互相收藏可见，2代表自己可见
    $mobile = $card['mobile'];
    $email = $card['email'];
    $weixin = $card['weixin'];
    $address = $card['address'];
    $qq = $card['qq'];
    //当等于2时检测双方是否关注
    //然后简化成0代表可见，2代表不可见
    if($mobile == 1 || $email==1 || $weixin==1 || $address==1 || $qq==1){
        $myMember = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid'] );
        //如果查看人是未公众号的人或未创建名片的人，则非0直接不可见
        if(empty($_W['openid']) || empty($myMember)){
            $member==2;
            $email==2;
            $weixin==2;
            $address==2;
            $qq==2;
        }else{

            $checkCollect = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_mycollect')." WHERE mid=".$myMember." AND collect_mid=".$id." AND weid=".$_W['uniacid']);
            $checkCollect2 = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_mycollect')." WHERE collect_mid=".$myMember." AND mid=".$id." AND weid=".$_W['uniacid']);
            if(!empty($checkCollect) && !empty($checkCollect2)){
                if($mobile == 1) $member==0;
                if($email == 1) $email==0;
                if($weixin == 1) $weixin==0;
                if($address == 1) $address==0;
                if($qq == 1) $qq==0;
            }else{
                if($mobile == 1) $member==2;
                if($email == 1) $email==2;
                if($weixin == 1) $weixin==2;
                if($address == 1) $address==2;
                if($qq == 1) $qq==2;
            }
        }
    }
    $ismember = pdo_fetchcolumn( " SELECT id FROM ".tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid']);
    //帮助链接
    $tiaozhuanUrl = pdo_fetchcolumn( " SELECT guanzhuUrl FROM ".tablename('amouse_weicard_sysset')." WHERE weid=".$_W['uniacid']);
    include $this->template('qianxian/share');
}