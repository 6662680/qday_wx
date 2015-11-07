<?php
/**
 */
defined('IN_IA') or exit('Access Denied');
class Mon_houseModuleSite extends WeModuleSite
{
    private $table_house = "mon_house";
    private $table_agent = "mon_house_agent";
    private $table_house_type = "mon_house_type";
    private $table_house_timg = "mon_house_timage";
    private $table_house_item="mon_house_item";
    private $table_house_order="mon_house_order";
    public $weid;
    public function __construct() {
        global $_W;
        $this->weid = IMS_VERSION<0.6?$_W['weid']:$_W['uniacid'];
    }
    public function doWebHouseSetting()
    {
        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));

            $psize = 20;

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_house) . " WHERE weid =:weid  ORDER BY createtime DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(
                ":weid" => $this->weid
            ));
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_house) . " WHERE weid = '{$this->weid}'");
            $pager = pagination($total, $pindex, $psize);


        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            pdo_delete($this->table_agent,array('hid'=>$id));
            pdo_delete($this->table_house_order,array('hid'=>$id));
            pdo_delete($this->table_house_timg,array('hid'=>$id));

            pdo_delete($this->table_house_type,array('hid'=>$id));
            pdo_delete($this->table_house_item,array('hid'=>$id));

            pdo_delete($this->table_house,array('id'=>$id));


            message('删除成功！', referer(), 'success');
        }

        $version = IMS_VERSION<0.6?'':'_advance';
        include $this->template('manage'.$version);
    }

    public function  doWebunitSetting()
    {
        global $_W, $_GPC;

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        $hid = $_GPC['hid'];

        $houses = pdo_fetchall("SELECT * FROM " . tablename($this->table_house) . " WHERE weid =:weid  ORDER BY createtime DESC, id DESC ", array(
            ":weid" => $this->weid
        ));

        if (empty($hid)) {
            $hid = $houses[0]["id"];
        }


        if ($operation == 'post') { // 添加
            $id = intval($_GPC['id']);

            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_house_type) . " WHERE id = :id", array(
                    ':id' => $id
                ));
                if (empty($item)) {
                    message('抱歉，户型删除或不存在！', '', 'error');
                }


            }
            if (checksubmit('submit')) {

                if (empty($_GPC['hid'])) {
                    message('请选择楼盘!');
                }

                if (empty($_GPC['tname'])) {
                    message('请填写顾问姓名!');
                }


                $data = array(
                    'hid' => $hid,
                    'tname' => $_GPC['tname'],
                    'sort' => $_GPC['sort']
                );
                if (!empty($id)) {
                    pdo_update($this->table_house_type, $data, array(
                        'id' => $id
                    ));
                } else {
                    pdo_insert($this->table_house_type, $data);
                }

                message('更新户型信息成功！', $this->createWebUrl('unitSetting', array(
                    'name' => 'monhouse',
                    'op' => 'display',
                    'hid' => $hid
                )), 'success');
            }
        } elseif ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));

            $psize = 20;

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_house_type) . " WHERE hid =:hid  ORDER BY   sort asc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(":hid" => $hid));
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_house_type) . " WHERE hid =:hid", array(":hid" => $hid));
            $pager = pagination($total, $pindex, $psize);
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);


            pdo_delete($this->table_house_timg, array(
                'tid' => $id
            ));//删除用户表


            pdo_delete($this->table_house_type, array(
                'id' => $id
            ));//删除用户表


            message('删除成功！', referer(), 'success');
        }


        $version = IMS_VERSION<0.6?'':'_advance';

        include $this->template("house_unit".$version);


    }
    /**
     * 户型图片
     */
    public function  doWebunitImgSetting()
    {
        global $_W, $_GPC;

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        $hid = $_GPC['hid'];
        $tid=$_GPC['tid'];

        $houses = pdo_fetchall("SELECT * FROM " . tablename($this->table_house) . " WHERE weid =:weid  ORDER BY createtime DESC, id DESC ", array(
            ":weid" => $_W['weid']
        ));

        if (empty($hid)) {
            $hid = $houses[0]["id"];
        }

        $house_types=pdo_fetchall("SELECT * from ".tablename($this->table_house_type)."  WHERE hid=:hid order by sort asc",array(":hid"=>$hid) );


        if(empty($tid)){
            $tid=$house_types[0]["id"];

        }


        if ($operation == 'post') { // 添加
            $id = intval($_GPC['id']);

            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_house_timg) . " WHERE id = :id", array(
                    ':id' => $id
                ));
                if (empty($item)) {
                    message('抱歉，户型图删除或不存在！', '', 'error');
                }


            }
            if (checksubmit('submit')) {

                if (empty($_GPC['hid'])) {
                    message('请选择楼盘!');
                }


                if (empty($_GPC['hid'])) {
                    message('请选择楼盘!');
                }


                if (empty($_GPC['tid'])) {
                    message('请选择户型!');
                }


                $data = array(
                    'hid' => $hid,
                    'tid'=>$tid,
                    'pre_img'=>$_GPC['pre_img'],
                    'img'=>$_GPC['img']
                );


                if (!empty($id)) {
                    pdo_update($this->table_house_timg, $data, array(
                        'id' => $id
                    ));
                } else {
                    pdo_insert($this->table_house_timg, $data);
                }
                message('更新户型图信息成功！', $this->createWebUrl('unitImgSetting', array(
                    'name' => 'monhouse',
                    'op' => 'display',
                    'hid' => $hid,
                    'tid'=>$tid
                )), 'success');
            }
        } elseif ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));

            $psize = 20;

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_house_timg) . " WHERE hid =:hid  and tid=:tid ORDER BY   id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(":hid" => $hid,":tid"=>$tid));


            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_house_timg) . " WHERE hid =:hid and tid=:tid", array(":hid" => $hid,":tid"=>$tid));

            $pager = pagination($total, $pindex, $psize);
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);


            pdo_delete($this->table_house_timg, array(
                'id' => $id
            ));//删除用户表


            message('删除成功！', referer(), 'success');
        }

        $version = IMS_VERSION<0.6?'':'_advance';

        if(IMS_VERSION>=0.6){

            load()->func('tpl');
        }

        include $this->template("house_unit_img".$version);


    }

    public function  doWebOrderManager(){

        global $_GPC,$_W;
        $hid=$_GPC['hid'];
        $house=$this->findHouse($hid);
        if(empty($house)){
            message("楼盘删除或不许存在");
        }

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';


        if ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));

            $psize = 20;

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_house_order) . " WHERE hid =:hid  ORDER BY createtime DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(
                ":hid" => $hid
            ));
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_house_order) . " WHERE  hid =:hid",array(":hid"=>$hid));
            $pager = pagination($total, $pindex, $psize);


        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);

            // 删除活动

            pdo_delete($this->table_house_order, array(
                'id' => $id
            )); // 删除用户表


            message('删除成功！', referer(), 'success');
        }



        $version = IMS_VERSION<0.6?'':'_advance';

        include $this->template("order_list".$version);



    }

    public function  doWebQueryTypes(){
        global $_W,$_GPC;
        $hid=$_GPC['hid'];
        $house_types=pdo_fetchall("SELECT * from ".tablename($this->table_house_type)."  WHERE hid=:hid order by sort asc",array(":hid"=>$hid) );
        echo json_encode($house_types);

    }


    public function  doWebOrderDowload(){

        require_once 'download.php';
    }

    public function doWebAgentSetting()
    {


        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        $hid = $_GPC['hid'];

        $houses = pdo_fetchall("SELECT * FROM " . tablename($this->table_house) . " WHERE weid =:weid  ORDER BY createtime DESC, id DESC ", array(
            ":weid" => $_W['weid']
        ));

        if (empty($hid)) {
            $hid = $houses[0]["id"];
        }

        if ($operation == 'post') { // 添加
            $id = intval($_GPC['id']);

            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_agent) . " WHERE id = :id", array(
                    ':id' => $id
                ));
                if (empty($item)) {
                    message('抱歉，顾问删除或不存在！', '', 'error');
                }


            }
            if (checksubmit('submit')) {

                if (empty($_GPC['hid'])) {
                    message('请选择楼盘!');
                }

                if (empty($_GPC['gname'])) {
                    message('请填写顾问姓名!');
                }

                if (empty($_GPC['tel'])) {
                    message('请填写顾问联系电话!');
                }

                if (empty($_GPC['workyear'])) {
                    message('请填写顾问工作年限!');
                }


                if (empty($_GPC['headimgurl'])) {
                    message('请上传顾问头像');
                }


                $data = array(
                    'hid' => $hid,
                    'gname' => $_GPC['gname'],
                    'tel' => $_GPC['tel'],
                    'workyear' => $_GPC['workyear'],
                    'headimgurl' => $_GPC['headimgurl']
                );
                if (!empty($id)) {
                    pdo_update($this->table_agent, $data, array(
                        'id' => $id
                    ));
                } else {
                    pdo_insert($this->table_agent, $data);
                }
                message('更新顾问信息成功！', $this->createWebUrl('agentSetting', array(
                    'name' => 'monhouse',
                    'op' => 'display',
                    'hid' => $hid
                )), 'success');
            }
        } elseif ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));

            $psize = 20;

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_agent) . " WHERE hid =:hid  ORDER BY   id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(":hid" => $hid));
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_agent) . " WHERE hid =:hid", array(":hid" => $hid));
            $pager = pagination($total, $pindex, $psize);
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);




            pdo_delete($this->table_agent, array(
                'id' => $id
            ));



            message('删除成功！', referer(), 'success');
        }


        $version = IMS_VERSION<0.6?'':'_advance';

        if(IMS_VERSION>=0.6){

            load()->func('tpl');
        }


        include $this->template('agent'.$version);


    }

    public function  doMobileIndex()
    {
        global $_W, $_GPC;

        $hid=$_GPC['hid'];
        $house=$this->findHouse($hid);
        if(empty($house)){

            message("楼盘删除或不存在");

        }



        include $this->template("index");
    }

    /*
     * 概览
     * */

    public function  doMobileOverview()
    {
        global $_GPC,$_GPC;
        global $_W, $_GPC;

        $hid=$_GPC['hid'];
        $house=$this->findHouse($hid);
        if(empty($house)){

            message("楼盘删除或不存在");

        }

        $house_items=pdo_fetchall("select * from ".tablename($this->table_house_item)." where hid=:hid order by sort asc",array(":hid"=>$hid));




        include $this->template("overview");

    }

    /**
     * 户型
     */
    public function  doMobileunitType()
    {

        global $_W, $_GPC;

        $hid=$_GPC['hid'];
        $house=$this->findHouse($hid);
        if(empty($house)){

            message("楼盘删除或不存在");

        }


        $house_tyeps=pdo_fetchall("select * from ".tablename($this->table_house_type)." where hid=:hid order by sort asc",array(":hid"=>$hid));



        include $this->template("unit_type");

    }


    public function findUnitImgs($tid){
        $house_imgs=pdo_fetchall("select * from ".tablename($this->table_house_timg)." where tid=:tid order by id desc",array(":tid"=>$tid));

        return $house_imgs;
    }

    /**
     * 预约报名
     */
    public function  doMobileOrder()
    {

        global $_W, $_GPC;

        $hid=$_GPC['hid'];
        $house=$this->findHouse($hid);
        if(empty($house)){

            message("楼盘删除或不存在");

        }


        include $this->template("order");

    }


    /**
     * 报名
     */
    public function  doMobileSorder(){
        global $_W, $_GPC;

        $hid=$_GPC['hid'];

        $house=$this->findHouse($hid);

        $res=array();


        if(empty($house)){
            $res['result']='error';
            $res['info']='楼盘不存在';
        }else{

            $tel=$_GPC['tel'];
            $uname=$_GPC['uname'];

            $user=pdo_fetch("SELECT * FROM  ".tablename($this->table_house_order)." where hid=:hid and tel=:tel",array(":hid"=>$hid,":tel"=>$tel));

            if(!empty($user)){
                $res['result']='error';
                $res['info']='用户已存在';

            }else{


                $data = array(
                    'hid' => $hid,
                    'tel' => $tel,
                    'createtime' => TIMESTAMP,
                    'uname' => $uname
                );

                pdo_insert($this->table_house_order, $data);


                $res['result']='succ';

            }
        }

        echo   json_encode($res);
    }


    /**
     * 封面
     */
    public  function  doMobileConver(){
        global $_W, $_GPC;
        $hid=$_GPC['hid'];

        $house=$this->findHouse($hid);

        if(empty($house)){
            message("楼盘删除或不存在");
        }



        include $this->template("conver");


    }


    public function  doMobileIntro(){
        global $_W, $_GPC;
        $hid=$_GPC['hid'];

        $house=$this->findHouse($hid);
        if(empty($house)){
            message("楼盘删除或不存在");
        }



        include $this->template("intro");

    }
    /**
     * 顾问
     */
    public function  doMobileagent()
    {
        global $_W, $_GPC;

        $hid=$_GPC['hid'];

        $agents=pdo_fetchall("select * from ".tablename($this->table_agent)." where hid=:hid order by id desc",array(":hid"=>$hid));


        include $this->template("agent");
    }



    /**
     * 目录
     */
    public function  doMobilemulu()
    {
        include $this->template("mulu");

    }


    public  function  findHouse($hid){

        $house=pdo_fetch("SELECT * FROM  ".tablename($this->table_house)." where id=:hid",array(":hid"=>$hid));
        return $house;

    }


    public function  img(){
        global $_W, $_GPC;

       
        return $_W['attachurl'];


    }


}