<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-6
 * Time: 下午12:24
 * To change this template use File | Settings | File Templates.
 */

global $_W,$_GPC;
//$this->checkBrower();
$from_user = $_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = $_GPC['id'];
//$cid = $_GPC['cid'];

$member = pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_member')." WHERE id=".$id );
$card = pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_card')." WHERE mid=".$member['id']);
$renmaiNum = pdo_fetchcolumn("SELECT COUNT(id) FROM ".tablename('amouse_weicard_mycollect')." WHERE mid=".$member['id'] );
$guanzhuUrl = pdo_fetchcolumn("SELECT guanzhuUrl FROM ".tablename('amouse_weicard_sysset')." WHERE weid=".$_W['uniacid'] );

//生成二维码图片
$imgName = $member['id']."amouseerweima_".$_W['uniacid'].".png";
$shareimg = toimage($member['headimg']);
$path = "/addons/amouse_ecard";
$filename = IA_ROOT . $path . "/data/".$imgName;
$imgUrl = "addons/amouse_eicard/data/$imgName";
$a = $this->CreateQRImage($imgName,$linkUrl,$imgUrl);
//判断查看信息的人是否是本人,0表示本人，1表示非本人
$isself = 1;
if($member['from_user'] ==$from_user){
    $isself =0;
    $renmai = pdo_fetchall( " SELECT m.id, m.company, m.job, m.realname, m.headimg FROM ".tablename('amouse_weicard_mycollect')." AS c LEFT JOIN ".tablename('amouse_weicard_member')." AS m ON m.id=c.collect_mid WHERE c.mid=".$id." AND c.weid=".$_W['uniacid']." ORDER BY c.createtime DESC LIMIT 30  " );
    //关注链接
    include $this->template('qianxian/info');
    exit();
}
$ismember = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid'] );

//被查看人数，已cookie判断
$cookies = $_W['weud']."ecard".$member['id'];
if(!isset($_COOKIE[$cookies])){
    setcookie($cookies,1,time()+86400*30);
    $card['view'] = intval($card['view']) +1;
    pdo_update('amouse_weicard_card',$card,array('id'=>$card['id']));
}
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
    $myMember = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid']);
    $checkCollect = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_mycollect')." WHERE mid=".$myMember." AND collect_mid=".$id );
    $checkCollect2 = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_mycollect')." WHERE collect_mid=".$myMember." AND mid=".$id );
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

include $this->template('qianxian/info_view');