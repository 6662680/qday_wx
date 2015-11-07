<?php
/**
 */
defined('IN_IA') or exit('Access Denied');

require_once "jssdk.php";
include_once IA_ROOT . '/addons/amouse_article/model.php';
class Amouse_articleModuleSite extends WeModuleSite {

    public function doMobileIndex() {
        global $_GPC, $_W;
        $weid=$_W['uniacid'];
        $set=  pdo_fetch("SELECT * FROM ".tablename('fineness_sysset')." WHERE weid=:weid limit 1", array(':weid' => $weid));
        $cid = intval($_GPC['cid']);
        //幻灯片
        $advlist = pdo_fetchall("SELECT * FROM " . tablename('fineness_adv') . " WHERE weid =$weid  ORDER BY id DESC");
        $op= $_GPC['op'];
		  $wechat=  pdo_fetch("SELECT * FROM ".tablename('account_wechats')." WHERE acid=:acid AND uniacid=:uniacid limit 1", array(':acid' => $weid,':uniacid' => $weid));
        if($op=='wemedia'){
            if($cid==0){
                $condition .= "where 1=1 ";
            }else{
                $condition .= " WHERE  ccate = '{$cid}' OR pcate = '{$cid}' ";
                $category = pdo_fetch("SELECT name FROM " . tablename('fineness_article_category') . " WHERE id = '{$cid}'");
            }
            $sql = "SELECT * FROM " . tablename('fineness_article') . $condition . ' ORDER BY displayorder DESC';
            $result = pdo_fetchall($sql);
			 
            include $this->template('themes/list12');
            exit;
        }else{
            $category = pdo_fetch("SELECT * FROM " . tablename('fineness_article_category') . " WHERE id = '{$cid}'");
            //独立选择分类模板
            $title = $category['name'];
            if(!empty($category['thumb'])) {
                $shareimg = toimage($category['thumb']);
            }else{
                $shareimg=IA_ROOT.'/addons/amouse_article/icon.jpg';
            }
        }

        if(!empty($category['template'])) {
            include $this->template($category['templatefile']);
            exit;
        }
        $url=$_W['siteroot']."app/".substr($this->createMobileUrl('Index',array('cid'=>$cid,'uniacid'=>$weid),true),2);
        $sql = "SELECT * FROM " . tablename('fineness_article').' ORDER BY displayorder DESC';
        $result = pdo_fetchall($sql);

        include $this->template('themes/list12');
    }


    public function doMobileDetail() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $weid=$_W['uniacid'];

        $set=  pdo_fetch("SELECT * FROM ".tablename('fineness_sysset')." WHERE weid=:weid limit 1", array(':weid' => $weid));
        $detail = pdo_fetch("SELECT * FROM " . tablename('fineness_article') . " WHERE `id`=:id and weid=:weid", array(':id'=>$id,':weid' => $weid));
        $shareimg = toimage($detail['thumb']);
        $url=$_W['siteroot']."app/".substr($this->createMobileUrl('detail',array('id'=>$id,'uniacid'=>$weid),true),2);
       if($detail['bg_music_switch']==1){
           if (strexists($detail['musicurl'], 'http://')||strexists($detail['musicurl'], 'https://')) {
               $detail['musicurl'] = $detail['musicurl'];
           } else {
               $detail['musicurl'] = $_W['attachurl'] . $detail['musicurl'];
           }
       }
       if (!empty($detail['outLink'])) {
            if(strtolower(substr($detail['outLink'], 0, 4)) != 'tel:' && !strexists($detail['outLink'], 'http://') && !strexists($detail['outLink'], 'https://')) {
                $detail['outLink'] = $_W['siteroot'] . 'app/' . $detail['outLink'];
            }
            header('Location: '. $detail['outLink']);
            exit;
        }
        $op= $_GPC['op'];
        if($op=='wemedia'){
            $wechat=  pdo_fetch("SELECT * FROM ".tablename('account_wechats')." WHERE acid=:acid AND uniacid=:uniacid limit 1", array(':acid' => $weid,':uniacid' => $weid));
            include $this->template('themes/detail5');
            exit;
        }
        $wechat=  pdo_fetch("SELECT * FROM ".tablename('account_wechats')." WHERE acid=:acid AND uniacid=:uniacid limit 1", array(':acid' => $weid,':uniacid' => $weid));

        if(!empty($detail['template'])) {
            include $this->template($detail['templatefile']);
            exit;
        }

        include $this->template('detail');
    }


    //后台管理程序 web文件夹下
    public function __web($f_name){
        global $_W, $_GPC;
        checklogin();
        $weid = $_W['uniacid'];
        //每个页面都要用的公共信息，今后可以考虑是否要运用到缓存
        include_once 'web/' . strtolower(substr($f_name, 5)) . '.php';
    }

    //分类关联
    public function doWebCategory() {
        $this->__web(__FUNCTION__);
    }

    //文章关联
    public function doWebPaper() {
        $this->__web(__FUNCTION__);
    }

    //系统设置
    public function doWebSysset() {
        $this->__web(__FUNCTION__);
    }

    //一键关注设置
    public function doWebHutui() {
        $this->__web(__FUNCTION__);
    }

    //幻灯片管理
    public function doWebAdv()
    {
        $this->__web(__FUNCTION__);
    }

               //一键关注
    public function doMobileTuijian() {
        global $_GPC, $_W;
        $weid=$_W['uniacid'];
        $cfg = $this->module['config'];
        $list = pdo_fetchall("SELECT * FROM ".tablename('wx_tuijian')." WHERE weid=:weid ORDER BY createtime DESC ", array(':weid' => $weid)) ;
        include $this->template('tuijian');
    }

}
?>