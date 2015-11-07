<?php
/**
 * 微酒店
 *
 */
defined('IN_IA') or exit('Access Denied');

include "model.php";

class Foot_bathModuleSite extends WeModuleSite {
    public $_img_url = '../addons/foot_bath/template/style/img/';

    public $_css_url = '../addons/foot_bath/template/style/css/';

    public $_script_url = '../addons/foot_bath/template/style/js/';

    public $_search_key = '__hotel2_search';

    public $_from_user = '';

    public $_weid = '';

    public $_version = 0;

    public $_hotel_level_config = array(5 => '五星级酒店', 4 => '四星级酒店', 3 => '三星级酒店', 2 => '两星级以下', 15 => '豪华酒店', 14 => '高档酒店', 13 => '舒适酒店', 12 => '经济型酒店', );

    public $_set_info = array();

    public $_user_info = array();
    public $t_id;//足浴师id
    function __construct()
    {
        $this->_set_info = $this->get_hotel_set();
    }

    private function get_hotel_set()
    {
        global $_GPC, $_W;
        $weid = $_W['uniacid'];
        $set = pdo_fetch("select * from " . tablename('fb_set') . " where weid=:weid limit 1", array(":weid" => $weid));
        if (!$set) {
            $set = array(
                "user" => 1,
                "bind" => 1,
                "reg" => 1,
                "ordertype" => 1,
                "regcontent" => "",
                "paytype1" => 0,
                "paytype2" => 0,
                "paytype3" => 0,
                "is_unify" => 0,
                "version" => 0,
                "tel" => "",
            );
        }
        return $set;
    }
    //入口文件
    public function doMobileIndex(){
        global $_GPC, $_W;
        $sql = 'SELECT * FROM ' . tablename('fb_package').'where `is_show`=0';//字段为0表示显示
        $list = pdo_fetchall($sql);
        foreach($list as $key=>&$val){
            $arr=unserialize($val['s_item']);
            $temp=array();
            foreach($arr as $k=>$v){
                $sql  = 'SELECT * FROM ' . tablename('fb_item').'where id=:id';
                $param=array(':id'=>$v);
                $temp[] = pdo_fetch($sql,$param);
            }
            $val['s_item']=$temp;
        }
        $thumbs=array();//统计推荐
        foreach($list as $key=>$val){
            if($val['is_recommend']==1){
               $thumbs[]=$val['thumb'];
            }
        }
        $thumbscount=count($thumbs);
        //选择足浴师
        if(isset($this->t_id)){
            $t_id=$this->t_id;
            $sql = 'SELECT * FROM ' . tablename('fb_technician').'where `id`=:id';
            $param=array(':id'=>$t_id);
            $t_list = pdo_fetch($sql,$param);
        }
        include $this->template('detail');
    }
    public function doMobileTechni(){
        global $_GPC, $_W;
        $sql = 'SELECT * FROM ' . tablename('fb_technician').'where `state`=0';//字段为0表示显示
        $list = pdo_fetchall($sql);
       //print_r($list);die();
        include $this->template('techni');
    }
    //选择技师后提交
    public function doMobileCheckTechni(){
        global $_GPC, $_W;
        if(empty($_GPC['checkmember'])){
            message('请选择技师',$this->createMobileUrl('techni'),'error');
        }else{
            $this->t_id=$_GPC['checkmember'];
        }
        $this->doMobileIndex();
    }
    public function doWebPackManage(){
        global $_GPC, $_W;
    }
    public function doMobileOrder(){
        global $_GPC, $_W;
        checkauth();//订单提交用户登录验证
        load()->func('tpl');
        if(empty($_GPC['t_id'])){
            message('请选择足浴师');
        }else{
            $t_id=$_GPC['t_id'];//足浴师id
            $p_id=$_GPC['id'];//套餐id
            $sql = 'SELECT * FROM ' . tablename('fb_technician').'where `id`=:id';
            $param=array(':id'=>($_GPC['t_id']));
            $t_detail = pdo_fetch($sql,$param);//技师详情
            $sql = 'SELECT * FROM ' . tablename('fb_package').'where `id`=:id';
            $param=array(':id'=>($_GPC['id']));
            $p_detail = pdo_fetch($sql,$param);//套餐详情
            $price=$_GPC['price'];//价格
            $bdate = date('Y-m-d',time());
            $day = 1;
        }
        $paysetting = uni_setting($_W['uniacid'], array('payment', 'creditbehaviors'));
        $_W['account'] = array_merge($_W['account'], $paysetting);
        //die();
        include $this->template('order');
    }

    public function doWebfbset() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        if (checksubmit('submit')) {
            $data = array(
                'weid' => $_W['uniacid'],
                'location_p' => $_GPC['district']['province'],
                'location_c' => $_GPC['district']['city'],
                'location_a' => $_GPC['district']['district'],
                'version' => $_GPC['version'],
                'user' => $_GPC['user'],
                'reg' => $_GPC['reg'],
                'regcontent' => $_GPC['regcontent'],
                'bind' => $_GPC['bind'],
                'ordertype' => $_GPC['ordertype'],
                'paytype1' => $_GPC['paytype1'],
                'paytype2' => $_GPC['paytype2'],
                'paytype3' => $_GPC['paytype3'],
                'is_unify' => $_GPC['is_unify'],
                'tel' => $_GPC['tel'],
                'email' => $_GPC['email'],
                'mobile' => $_GPC['mobile'],
            );
            if (!empty($id)) {
                pdo_update("fb_set", $data, array("id" => $id));
            } else {
                pdo_insert("fb_set", $data);
            }
            message("保存设置成功!", referer(), "success");
        }
        $sql = 'SELECT * FROM ' . tablename('fb_set') . ' WHERE `weid` = :weid';//查找设置的支付方式
        $set = pdo_fetch($sql, array(':weid' => $_W['uniacid']));
        if (empty($set)) {
            $set = array('user' => 1, 'reg' => 1, 'bind' => 1);
        }
        include $this->template("fbset");
    }

    public function doWebOrder(){
        global $_GPC, $_W;
        $sql = 'SELECT * FROM ' . tablename('fb_order');
        $list = pdo_fetchall($sql);
        $op=$_GPC['op'];
        if($op=='edit'){
            $id = $_GPC['id'];
            if (!empty($id)) {
                $orderList = pdo_fetch("SELECT * FROM " . tablename('fb_order') . " WHERE id = :id", array(':id' => $id));
                if (empty($orderList)) {
                    message('抱歉，订单不存在或是已经删除！', '', 'error');
                }
                if (checksubmit('submit')) {
                    $data = array(
                        'status' => $_GPC['status']
                    );
                    pdo_update('fb_order', $data, array('id' => $id));
                    message('订单信息处理完成！', $this->createWebUrl('order','','success'));
                }
            }
            $btime=date('Y-m-d',$orderList['btime']);
            $time=date('Y-m-d',$orderList['time']);
            //print_r(date('Y-m-d',1442383836));die();
            $member_info = pdo_fetch("SELECT from_user,isauto FROM " . tablename('hotel2_member') . " WHERE id = :id LIMIT 1", array(':id' => $item['memberid']));
            include $this->template('order_form');
            exit;
        }/*elseif(){

        }*/
        include $this->template('order');
    }

    //订单列表
    public function doMobileorderlist()
    {
        global $_GPC, $_W;
        $weid =$_W['uniacid'];
        $openid =$_W['fans']['from_user'];
        checkauth();//订单提交用户登录验证
        $ac = $_GPC['ac'];
       // var_dump($openid);
        //die();

        if ($ac == "getDate") {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 5;
            $sql = 'SELECT * FROM ' . tablename('fb_order').' WHERE `weid`=:weid AND `openid`=:openid';//查询订单
            $params = array(':weid' => $weid,':openid'=>$openid);

            $where=" FROM ".tablename('fb_order')."WHERE `weid`=:weid  AND `openid`=:openid ";
            $count_sql = "SELECT COUNT(".tablename('fb_order').".id)".$where;//查询订单第一列

            if($pindex > 0) {
                // 需要分页
                $start = ($pindex - 1) * $psize;
                $sql .= " LIMIT {$start},{$psize}";
            }
            $list = pdo_fetchall($sql,$params);
            //var_dump($list);die();
            $total = pdo_fetchcolumn($count_sql,$params);
            $page_array = get_page_array($total, $pindex, $psize);
            $data = array();
            $data['result'] = 1;
            ob_start();
            include $this->template('order_crumb');
            $data['code'] = ob_get_contents();
            ob_clean();
            $data['total'] = $total;
            $data['isshow'] = $page_array['isshow'];
            if ($page_array['isshow'] == 1) {
                $data['nindex'] = $page_array['nindex'];
            }
            die(json_encode($data));
        } else {
            include $this->template('orderlist');
       }
    }


    //订单详情
    public function doMobileorderdetail()
    {
        global $_GPC, $_W;
        $weid =$_W['uniacid'];
        $openid =$_W['fans']['from_user'];
        $id = $_GPC['id'];
        checkauth();//订单提交用户登录验证
        $sql = 'SELECT * FROM ' . tablename('fb_order') . ' WHERE `id` = :id AND `weid`=:weid AND `openid`=:openid';
        $params=array();
        $params[':id'] = $id;
        $params[':weid']=$weid;
        $params[':openid']=$openid;
        $item = pdo_fetch($sql, $params);

        $packageid=$item['p_id'];
        $psql='SELECT * FROM ' . tablename('fb_package') . ' WHERE `id` = :id';
        $argu=array(':id'=>$packageid);
        $result = pdo_fetch($psql,$argu);
        $itemIdList=unserialize($result['s_item']);//反序列化出套餐中所有的id
        $itemList=array();
        foreach($itemIdList as $key=>$val){
           $account = pdo_fetch('SELECT * FROM ' . tablename('fb_item') . ' WHERE `id` = :id', array('id'=>$val));//查找出所有项目条目
           $itemList[]=$account['item_name'];
        }
        if ($this->_set_info['is_unify'] == 1) {
            $tel = $this->_set_info['tel'];
        } else {
            $tel = $item['phone'];
        }
        if(!empty($_W['member']['uid'])) {
            $member = mc_fetch($_W['member']['uid'], array('credit1', 'credit2'));
        }
        $params['module'] = "ewei_hotel";
        $params['ordersn'] = $item['ordersn'];
        $params['tid'] = $item['id'];
        $params['user'] = $_W['fans']['from_user'];
        $params['fee'] = $item['price'];
        $params['title'] = $_W['account']['name'] . "酒店订单{$item['ordersn']}";
        // 设置分享信息
        $shareDesc = $item['address'];
        $shareThumb = tomedia($item['thumb']);
        include $this->template('orderdetail');
    }


    public function doMobileOrderSubmit(){
        global $_GPC, $_W;
        checkauth();
        if (empty($_GPC['contact_name'])) {
            die(json_encode(array("result" => 0, "error" => "联系人不能为空!")));
        }
        if (empty($_GPC['mobile'])) {
            die(json_encode(array("result" => 0, "error" => "手机号不能为空!")));
        }
        $btime=strtotime($_GPC['bdate']);//获取日期转换成时间戳
        $data = array(
            'weid' => $_W['uniacid'],
            'openid' => $_W['fans']['from_user'],
            't_id' => $_GPC['t_id'],//技师id
            't_name' => $_GPC['t_name'],//技师名字
            'p_id' =>$_GPC['p_id'],//套餐id
            'p_name' =>$_GPC['p_name'],//套餐id
            'contact_name' => $_GPC['contact_name'],//联系人
            'mobile' => $_GPC['mobile'],//联系人电话
            'detail' => $_GPC['detail'],//详情
            'ordersn' => date('md') . sprintf("%04d", $_W['fans']['id']) . random(4, 1),//订单编号
            'btime' => $btime,//预订时间
            'price' => $_GPC['price'],//总价
            'paytype' => $_GPC['paytype'],//支付类型
            'status' => $_GPC['status'],//订单状态
            'time' =>TIMESTAMP,//下单时间
        );
       // print_r($data);die();
        $result = pdo_insert('fb_order', $data);
        if($result){
            $url=$this->createMobileUrl('index');
            die(json_encode(array("result" => 1, "url" =>$url)));
        }
        include $this->template('order');
    }

    public function doWebItem(){
        global $_GPC, $_W;
        $sql = 'SELECT * FROM ' . tablename('fb_item');
        $list = pdo_fetchall($sql);
        include $this->template('item');
    }

    public function doWebTechnician(){
        global $_GPC, $_W;
        $sql = 'SELECT * FROM ' . tablename('fb_technician');
        $list = pdo_fetchall($sql);
        include $this->template('technician');
    }
    public function doWebTechnicianEdit(){
        global $_GPC, $_W;
        $sql  = 'SELECT * FROM ' . tablename('fb_item');
        $list = pdo_fetchAll($sql);
        $op=$_GPC['op'];
        if($op=='add'){
            $url=$this->createWebUrl('technicianedit',array('op'=>'add'));
            if (checksubmit('submit')){
                if(empty($_GPC['name'])){
                    message('请输入技师的姓名（编号）');
                }
                $data=array(
                    'name'=>$_GPC['name'],
                    'gender'=>$_GPC['gender'],
                    'photo'=>$_GPC['photo'],
                    'lever'=>$_GPC['lever'],
                    'detail'=>$_GPC['detail'],
                    'state'=>$_GPC['state']
                );
                $result = pdo_insert('fb_technician', $data);
                if($result){
                    message('添加成功', $this->createWebUrl('Technician'), 'success');
                }
            }
        }elseif($op=='edit'){
            $id=$_GPC['id'];
            $url=$this->createWebUrl('technicianedit',array('op'=>'edit','id'=>$id));
            if (checksubmit('submit')){
                $data=array(
                    'name'=>$_GPC['name'],
                    'gender'=>$_GPC['gender'],
                    'photo'=>$_GPC['photo'],
                    'lever'=>$_GPC['lever'],
                    'detail'=>$_GPC['detail'],
                    'state'=>$_GPC['state']
                );
                //pdo_update('account', array('uniacid' => '100'), array('acid' => '10'));
                $result = pdo_update('fb_technician', $data,array('id'=>$id));
                if($result){
                    message('更新成功', $this->createWebUrl('Technician'), 'success');
                }
            }
            $sql  = 'SELECT * FROM ' . tablename('fb_technician').'where id=:id';
            $param=array(':id'=>$id);
            $item = pdo_fetch($sql,$param);
            $arr=unserialize($item['s_item']);
            $item['s_item']=$arr;
        }elseif($op=='delete'){
            $id=$_GPC['id'];
            $result=pdo_delete('fb_technician', array('id' => $id));
            if($result){
                message('删除成功', $this->createWebUrl('technician'), 'success');
            }
        }
        include $this->template('technician_edit');
    }
    public function doWebItemedit(){
        global $_GPC, $_W;
        $op=$_GPC['op'];
            if($op=='add'){
                $url=$this->createWebUrl('itemedit',array('op'=>'add'));
                if (checksubmit('submit')) {
                    if (empty($_GPC['name'])) {
                        message('请输入项目名称');
                    }else{
                        $name   = $_GPC['name'];
                        $resutl = pdo_insert('fb_item', array('item_name' => $name));
                        if ($resutl) {
                            message('项目添加成功', $this->createWebUrl('item'), 'success');
                        }
                    }
                }
            }elseif($op=='edit'){
                $url=$this->createWebUrl('itemedit',array('op'=>'edit'));
                if(empty($_GPC['id'])){
                    message('参数错误!');
                }else{
                    $name=$_GPC['name'];
                    if (checksubmit('submit')){
                        $resutl=pdo_update('fb_item', array('item_name' =>$name),array('id' =>$_GPC['id']));
                        if ($resutl) {
                            message('修改成功', $this->createWebUrl('item'), 'success');
                        }
                    }
                    $id=$_GPC['id'];
                    $sql = 'SELECT * FROM ' . tablename('fb_item').'where id=:id';
                    $param=array(':id'=>$id);
                    $item = pdo_fetch($sql,$param);
                }
            }elseif($op='delete'){
                if(!empty($_GPC['id'])){
                    $resutl=pdo_delete('fb_item', array('id' =>$_GPC['id']));
                    if ($resutl) {
                        message('删除成功', $this->createWebUrl('item'), 'success');
                    }
                }
            }
        include $this->template('item_edit');
    }
    public function doWebPackage(){
        global $_GPC, $_W;
        $sql  = 'SELECT * FROM ' . tablename('fb_package');
        $list = pdo_fetchall($sql);
        //print_r($list);
        foreach($list as $key=>&$val){
            $arr=unserialize($val['s_item']);
            $temp=array();
            foreach($arr as $k=>$v){
                $sql  = 'SELECT * FROM ' . tablename('fb_item').'where id=:id';
                $param=array(':id'=>$v);
                $temp[] = pdo_fetch($sql,$param);
            }
            $val['s_item']=$temp;
        }
        include $this->template('package');
    }
    public function doWebPackedit(){
        global $_GPC, $_W;
        $sql  = 'SELECT * FROM ' . tablename('fb_item');
        $list = pdo_fetchAll($sql);
        $op=$_GPC['op'];
        if($op=='add'){
            $url=$this->createWebUrl('packedit',array('op'=>'add'));
            if (checksubmit('submit')){
                if(empty($_GPC['name'])){
                    message('请输入套餐名称');
                }
                $data=array(
                    'p_name'=>$_GPC['name'],
                    'thumb'=>$_GPC['thumb'],
                    'price'=>$_GPC['price'],
                    'm_price'=>$_GPC['mprice'],
                    's_item'=>serialize($_GPC['item']),
                    'detail'=>$_GPC['detail'],
                    'is_show'=>$_GPC['is_show'],
                    'is_recommend'=>$_GPC['is_recommend']
                );
                $result = pdo_insert('fb_package', $data);
                if($result){
                    message('添加成功', $this->createWebUrl('package'), 'success');
                }
            }
        }elseif($op=='edit'){
            $id=$_GPC['id'];
            $url=$this->createWebUrl('packedit',array('op'=>'edit','id'=>$id));
            if (checksubmit('submit')){
                $data=array(
                    'p_name'=>$_GPC['name'],
                    'thumb'=>$_GPC['thumb'],
                    'price'=>$_GPC['price'],
                    'm_price'=>$_GPC['mprice'],
                    's_item'=>serialize($_GPC['item']),
                    'detail'=>$_GPC['detail'],
                    'is_show'=>$_GPC['is_show'],
                    'is_recommend'=>$_GPC['is_recommend']
                );
                //pdo_update('account', array('uniacid' => '100'), array('acid' => '10'));
                $result = pdo_update('fb_package', $data,array('id'=>$id));
                if($result){
                    message('更新成功', $this->createWebUrl('package'), 'success');
                }
            }
            $sql  = 'SELECT * FROM ' . tablename('fb_package').'where id=:id';
            $param=array(':id'=>$id);
            $item = pdo_fetch($sql,$param);
            $arr=unserialize($item['s_item']);
            $item['s_item']=$arr;
        }elseif($op=='delete'){
            $id=$_GPC['id'];
            $result=pdo_delete('fb_package', array('id' => $id));
            if($result){
                message('删除成功', $this->createWebUrl('package'), 'success');
            }
        }
        include $this->template('pack_edit');
    }

}
