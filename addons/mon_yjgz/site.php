<?php
/**
 * @author codeMonkey
 * qq:631872807
 */
defined('IN_IA') or exit('Access Denied');
define("YYZF_MODULENAME", "mon_yjgz");



class Mon_yjgzModuleSite extends WeModuleSite
{



    public $weid;
    public $oauth;
    public $table_gz="mon_yjgz";
    public $table_gz_item="mon_yjgz_item";
    public function __construct() {
        global $_W;
        $this->weid = IMS_VERSION<0.6?$_W['weid']:$_W['uniacid'];

    }


    public function  doWebGz(){
        global $_W,$_GPC;


        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'display') {

            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_gz) . " WHERE weid =:weid  ORDER BY createtime DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $this->weid));
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_gz) . " WHERE weid =:weid ", array(':weid' => $this->weid));
            $pager = pagination($total, $pindex, $psize);

        } else if ($operation == 'delete') {


            $id = $_GPC['id'];

            pdo_delete($this->table_gz_item, array(
                'yid' => $id
            ));

            pdo_delete($this->table_gz, array(
                'id' => $id
            ));



            message('删除成功！', referer(), 'success');
        }





        include $this->template("gz_manage");


    }

    public  function  doWebGzEdit(){
        global $_W,$_GPC;





            $id = intval($_GPC['id']);

            if(!empty($id)){
                $gz = pdo_fetch("SELECT * FROM " . tablename($this->table_gz) . " WHERE id = :id", array(
                    ':id' => $id
                ));
            }

            if(checksubmit('submit')){

                $data = array(
                    'weid' => $this->weid,
                    'title' => $_GPC["title"],
                    'banner_pic' => $_GPC['banner_pic'],
                    'banner_desc' =>htmlspecialchars_decode( $_GPC['banner_desc']),
                    'createtime' => TIMESTAMP
                );

                if (! empty($id)) {
                    pdo_update($this->table_gz, $data, array(
                        'id' => $id
                    ));
                } else {
                    pdo_insert($this->table_gz, $data);

                }

                message('更新关注成功！', $this->createWebUrl('Gz', array(

                )), 'success');
            }

        load()->func('tpl');
        include $this->template("gz_edit");

    }








    public function  doWebGzItem(){

        global $_W,$_GPC;


        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        $yid=$_GPC['yid'];

        if ($operation == 'display') {

            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_gz_item) . " WHERE yid =:yid  ORDER BY sort asc   LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':yid' => $yid));
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_gz_item) . " WHERE yid =:yid ", array(':yid' =>$yid));
            $pager = pagination($total, $pindex, $psize);

        } else if ($operation == 'delete') {


            $id = $_GPC['id'];

            pdo_delete($this->table_gz_item, array(
                'id' => $id
            ));




            message('删除成功！', referer(), 'success');
        }





        include $this->template("gz_item_manage");


    }


    public  function  doWebGzItemEdit(){
        global $_W,$_GPC;



        $yid = intval($_GPC['yid']);

        $id = intval($_GPC['id']);

        if(!empty($id)){
            $gz_item = pdo_fetch("SELECT * FROM " . tablename($this->table_gz_item) . " WHERE id = :id", array(
                ':id' => $id
            ));
        }

        if(checksubmit('submit')){

            $data = array(
                'title' => $_GPC["title"],
                'yid'=>$yid,
                'icon'=>$_GPC['icon'],
                'sort'=>$_GPC['sort'],
                'i_url'=>$_GPC['i_url'],
                'i_desc' =>htmlspecialchars_decode( $_GPC['i_desc']),

            );

            if (! empty($id)) {
                pdo_update($this->table_gz_item, $data, array(
                    'id' => $id
                ));
            } else {
                pdo_insert($this->table_gz_item, $data);

            }

            message('更新关注成功！', $this->createWebUrl('GzItem', array(
                'yid'=>$yid
            )), 'success');
        }

        load()->func('tpl');
        include $this->template("gz_item_edit");

    }


    public function  doMobileIndex(){
        global $_W,$_GPC;

        $yid=$_GPC['yid'];

        $gz = pdo_fetch("SELECT * FROM " . tablename($this->table_gz) . " WHERE id = :id", array(
            ':id' => $yid
        ));




        $list= pdo_fetchall("SELECT * FROM " . tablename($this->table_gz_item) . " WHERE yid =:yid  ORDER BY sort asc    ", array(':yid' => $yid));

        include $this->template('index');




    }

    public function  str_murl($url){
        global $_W,$_GPC;

        return $_W['siteroot']."app".str_replace('./','/',$url);
    }

}