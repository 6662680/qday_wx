<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-5
 * Time: 下午11:25
 * To change this template use File | Settings | File Templates.
 */
global $_W,$_GPC;
//$this->checkBrower();
$from_user = empty($_W['openid'])?'oNuWyjkzsEGpSuweXdDeauFLrkqM':$_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = $_GPC['id'];
if($op == 'list'){
    $images=pdo_fetchall("SELECT * FROM ".tablename('amouse_weicard_bg')." WHERE weid=".$_W['uniacid']." ORDER BY `displayorder` DESC");
}
if($op == 'post'){
    $id = $_GPC['mid'];
    $card = pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_card')." WHERE mid=".$id);
    $imgurl = pdo_fetchcolumn(" SELECT `img` FROM ".tablename('amouse_weicard_bg')." WHERE id=".$_GPC['id']);
    $card['bgimg'] = $imgurl;
    pdo_update('amouse_weicard_card',$card,array('id'=>$card['id']));
    $member = pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid'] );
    $imgName = $member['id']."amouseerweima_".$_W['uniacid'].".png";
    $linkUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('share', array('id'=>$member['id'],'cid' =>$card['id'],'weid' =>$_W['uniacid']));
    $path = "/addons/amouse_eicard";
    $filename = IA_ROOT . $path . "/data/".$imgName;
    $a = $this->erweima($imgName,$linkUrl,$filename);
    $isCompany = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_companyinfo')." WHERE mid=".$member['id']);
    $isPhoto = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_photo')." WHERE mid=".$member['id']);
    $isFengcai = pdo_fetchcolumn("SELECT id FROM ".tablename('amouse_weicard_presence')." WHERE mid=".$member['id']);
    //人脉数量
    $renmai = pdo_fetchcolumn( " SELECT COUNT(m.id) FROM ".tablename('amouse_weicard_mycollect')." AS c LEFT JOIN ".tablename('amouse_weicard_member')." AS m ON m.id=c.collect_mid WHERE c.mid=".$member['id']." AND c.weid=".$_W['uniacid'] );
    include $this->template('qianx_index');
    exit();
}

if($op == 'imgupload'){
    $id = $_GPC['mid'];
    $imgurl = $_GPC['headimg'];
    /*$insert= array(
        'displayorder' =>0,
        'img' => $imgurl,
        'weid' =>$_W['uniacid']);
    pdo_insert('amouse_weicard_bg', $insert);*/
    $card = pdo_fetch("SELECT * FROM ".tablename('amouse_weicard_card')." WHERE mid=".$id);
    $card['bgimg'] = $imgurl;
    pdo_update('amouse_weicard_card',$card,array('id'=>$card['id']));
    $member = pdo_fetch( " SELECT * FROM ".tablename('amouse_weicard_member')." WHERE openid='".$from_user."' AND weid=".$_W['uniacid']);

    include $this->template('qianx_index');
    exit();
}
include $this->template('qianxian/background');