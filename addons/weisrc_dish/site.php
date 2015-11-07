<?php
/**
 * 微点餐
 *
 */
defined('IN_IA') or exit('Access Denied');
include "../addons/weisrc_dish/model.php";
include "../addons/weisrc_dish/templateMessage.php";
define('CUR_MOBILE_DIR', 'dish/');
define('LOCK', 'Li4vYWRkb25zL3dlaXNyY19kaXNoL3RlbXBsYXRlL2ltYWdlcy92ZXJzaW9uLmNzcw==');

class weisrc_dishModuleSite extends WeModuleSite
{
    //模块标识
    public $modulename = 'weisrc_dish';

    public $msg_status_success = 1;
    public $msg_status_bad = 0;
    public $_debug = '1'; //default:0
    public $_weixin = '1'; //default:1

    public $_appid = '';
    public $_appsecret = '';
    public $_accountlevel = '';

    public $_weid = '';
    public $_fromuser = '';
    public $_nickname = '';
    public $_headimgurl = '';

    public $_auth2_openid = '';
    public $_auth2_nickname = '';
    public $_auth2_headimgurl = '';

    public $table_area = 'weisrc_dish_area';
    public $table_blacklist = 'weisrc_dish_blacklist';
    public $table_cart = 'weisrc_dish_cart';
    public $table_category = 'weisrc_dish_category';
    public $table_email_setting = 'weisrc_dish_email_setting';
    public $table_goods = 'weisrc_dish_goods';
    public $table_intelligent = 'weisrc_dish_intelligent';
    public $table_nave = 'weisrc_dish_nave';
    public $table_order = 'weisrc_dish_order';
    public $table_order_goods = 'weisrc_dish_order_goods';
    public $table_print_order = 'weisrc_dish_print_order';
    public $table_print_setting = 'weisrc_dish_print_setting';
    public $table_reply = 'weisrc_dish_reply';
    public $table_setting = 'weisrc_dish_setting';
    public $table_sms_checkcode = 'weisrc_dish_sms_checkcode';
    public $table_sms_setting = 'weisrc_dish_sms_setting';
    public $table_store_setting = 'weisrc_dish_store_setting';
    public $table_mealtime = 'weisrc_dish_mealtime';
    public $table_stores = 'weisrc_dish_stores';

    function __construct()
    {
        global $_W, $_GPC;
        $this->_fromuser = $_W['fans']['from_user']; //debug
        if ($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost:8888') {
            $this->_fromuser = 'debug';
        }

        $this->_weid = $_W['uniacid'];
        $account = $_W['account'];

        $this->_auth2_openid = 'auth2_openid_' . $_W['uniacid'];
        $this->_auth2_nickname = 'auth2_nickname_' . $_W['uniacid'];
        $this->_auth2_headimgurl = 'auth2_headimgurl_' . $_W['uniacid'];

        $this->_appid = '';
        $this->_appsecret = '';
        $this->_accountlevel = $account['level']; //是否为高级号

        $lock_path = base64_decode(LOCK);
        //if (!file_exists($lock_path)) {
            //message(base64_decode('5a+55LiN6LW377yM5oKo5L2/55So55qE5LiN5piv5q2j54mI6L2v5Lu277yM6LSt5Lmw5q2j54mI6K+36IGU57O7cXE6MTU1OTU3NTU='));
        //} else {
          //  $file_content = file_get_contents($lock_path);
         //   $validation_code = $this->authorization();
            //$this->code_compare($file_content, $validation_code);
        //}

        if ($this->_accountlevel == 4) {
            $this->_appid = $account['key'];
            $this->_appsecret = $account['secret'];
        }
    }

    //导航首页
    public function doMobileWapIndex()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $method = 'wapindex'; //method
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2();
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }

        if (empty($from_user)) {
            message('会话已经过时，请从微信端重新发送关键字登录！');
        }

        $storeid = intval($_GPC['storeid']);
        if (!empty($storeid)) {
            $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE id=" . $storeid);
        } else {
            $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . "  WHERE weid=:weid ORDER BY id DESC LIMIT 1", array(':weid' => $weid));
            $storeid = $store['id'];
        }

        if (empty($store)) {
            message('商家不存在！');
        }

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid=:weid ORDER BY id DESC LIMIT 1", array(':weid' => $weid));
        $title = $setting['title'];
        if (!empty($setting)) {
            $storeid = $setting['storeid'];
        } else {
            $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . "  WHERE weid=:weid  ORDER BY id DESC LIMIT 1", array(':weid' => $weid));
            $storeid = $store['id'];
        }

        $nave = pdo_fetchall("SELECT * FROM " . tablename($this->table_nave) . " WHERE weid=:weid AND status=1 ORDER BY displayorder DESC,id DESC", array(':weid' => $weid));

        include $this->template('dish_index');
    }

    //菜品列表
    public function doMobileWapList()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $title = '全部菜品';
        $do = 'list';

        $storeid = intval($_GPC['storeid']);
        if ($storeid == 0) {
            $storeid = $this->getStoreID();
        }
        if (empty($storeid)) {
            message('请先选择门店', $this->createMobileUrl('waprestlist'));
        }

        $method = 'waplist'; //method
        $url = $_W['siteroot'] .'app/'. $this->createMobileUrl($method, array('storeid' => $storeid), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2();
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }

        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $condition = '';

        if (!empty($_GPC['ccate'])) {
            $cid = intval($_GPC['ccate']);
            $condition .= " AND ccate = '{$cid}'";
        } elseif (!empty($_GPC['pcate'])) {
            $cid = intval($_GPC['pcate']);
            $condition .= " AND pcate = '{$cid}'";
        }

        $children = array();
        $category = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid = :weid AND storeid=:storeid ORDER BY  displayorder DESC,id DESC", array(':weid' => $weid, ':storeid' => $storeid));

        $cid = intval($category[0]['id']);
        $category_in_cart = pdo_fetchall("SELECT goodstype,count(1) as 'goodscount' FROM " . tablename($this->table_cart) . " GROUP BY weid,storeid,goodstype,from_user  having weid = '{$weid}' AND storeid='{$storeid}' AND from_user='{$from_user}'");
        $category_arr = array();
        foreach ($category_in_cart as $key => $value) {
            $category_arr[$value['goodstype']] = $value['goodscount'];
        }

        $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = '{$weid}' AND storeid={$storeid} AND status = '1' AND pcate={$cid} ORDER BY displayorder DESC, subcount DESC, id DESC ");

        $dish_arr = $this->getDishCountInCart($storeid);

        $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE  storeid=:storeid AND from_user=:from_user AND weid=:weid", array(':storeid' => $storeid, ':from_user' => $from_user, ':weid' => $weid));
        $totalcount = 0;
        $totalprice = 0;
        foreach ($cart as $key => $value) {
            $totalcount = $totalcount + $value['total'];
            $totalprice = $totalprice + $value['total'] * $value['price'];
        }

        //智能点餐
        $intelligents = pdo_fetchall("SELECT 1 FROM " . tablename($this->table_intelligent) . " WHERE weid={$weid} AND storeid={$storeid} GROUP BY name ORDER by name");

        include $this->template('dish_list');
    }

    //我的菜单
    public function doMobileWapMenu()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $title = '我的菜单';
        $do = 'menu';
        $storeid = intval($_GPC['storeid']);

        $this->check_black_list();
        $mealtimes = pdo_fetchall("SELECT * FROM " . tablename($this->table_mealtime) . " WHERE weid=:weid AND storeid=0 ORDER BY id ASC", array(':weid' => $weid));

        //begintime
        //endtime
        //3600 * 2
        $select_mealtime = '';
        foreach($mealtimes as $key => $value) {
            $tmptime = intval(strtotime(date('Y-m-d ') . $value['begintime']));
            $nowtime = intval(TIMESTAMP + 1800 * 1);
            if ($tmptime > $nowtime) {
                $select_mealtime .= '<option value="' . $value['begintime'] . '~' . $value['endtime'] . '">' . $value['begintime'] . '~' . $value['endtime'] . '</option>';
            }
        }

        //message($select_mealtime);

//        {loop $mealtimes $item}
//        <option value="{$item['begintime']}~{$item['endtime']}">{$item['begintime']}~{$item['endti//                                {/loop}
        //$inhour = $this->check_mealtime();
//        if ($inhour == 0) {
//            message('店铺未在营业时间中!');
//        }

        if (empty($storeid)) {
            message('请先选择门店', $this->createMobileUrl('waprestlist'));
        }

        $method = 'wapmenu'; //method
        $url = $_W['siteroot'] .'app/'. $this->createMobileUrl($method, array('storeid' => $storeid), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2();
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }
        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid=:weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $storeid));
        $flag = false;
        $issms = intval($store['is_sms']);
        $checkcode = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid  AND from_user=:from_user AND status=1 ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user));
        if ($issms == 1 && empty($checkcode)) {
            $flag = true;
        }

        $user = fans_search($from_user);

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid=:weid LIMIT 1", array(':weid' => $weid));

        $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " a LEFT JOIN " . tablename('weisrc_dish_goods') . " b ON a.goodsid=b.id WHERE a.weid=:weid AND a.from_user=:from_user AND a.storeid=:storeid", array(':weid' => $weid, ':from_user' => $from_user, ':storeid' => $storeid));

        if (!empty($from_user) && !(empty($weid))) {
            $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE weid=:weid AND from_user=:from_user ORDER BY id DESC LIMIT 1", array(':from_user' => $from_user, ':weid' => $weid));
        }

        $my_order_total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE storeid=:storeid AND from_user=:from_user ", array(':from_user' => $from_user, ':storeid' => $storeid));
        $my_order_total = intval($my_order_total);

        //智能点餐
        $intelligents = pdo_fetchall("SELECT 1 FROM " . tablename($this->table_intelligent) . " WHERE weid={$weid} AND storeid={$storeid} GROUP BY name ORDER by name");
        include $this->template('dish_menu');
    }

    public function check_black_list()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $item = pdo_fetch("SELECT * FROM " . tablename($this->table_blacklist) . " WHERE weid=:weid AND from_user=:from_user LIMIT 1", array(':weid' => $weid, ':from_user' => $from_user));

        if (!empty($item) && $item['status'] == 0) {
            message('你在黑名单中,不能进行相关操作...');
        }
    }

    public function check_mealtime()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $timelist = pdo_fetchall("SELECT * FROM " . tablename($this->table_mealtime) . " WHERE weid=:weid AND storeid=0 ", array(':weid' => $weid));

        $nowtime = intval(date("Hi"));

        foreach ($timelist  as $key => $value) {
            $begintime = intval(str_replace(':', '', $value['begintime']));
            $endtime = intval(str_replace(':', '', $value['endtime']));

            if ($nowtime >= $begintime && $nowtime <= $endtime) {
                return 1;
            }
        }
        return 0;
    }

    //门店列表
    public function doMobileWapRestList()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $do = 'rest';
        $title = '我的菜单';

        $method = 'waprestlist'; //method
        $url = $_W['siteroot'] .'app/'. $this->createMobileUrl($method);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2();
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }

        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        $areaid = $_GPC['areaid'];
        if ($areaid != 0) {
            $strWhere = " AND areaid={$areaid} ";
        }

        $area = pdo_fetchall("SELECT * FROM " . tablename($this->table_area) . " where weid = :weid ORDER BY displayorder DESC", array(':weid' => $weid));

        $restlist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " where weid = :weid and is_show=1 {$strWhere} ORDER BY displayorder DESC", array(':weid' => $weid));

        include $this->template('dish_rest_list');
    }

    //门店实景
    public function doMobileWapShopShow()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;
        $title = '商家店面';

        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请先选择门店', $this->createMobileUrl('waprestlist'));
        }

        $method = 'wapshopshow'; //method
        $url = $_W['siteroot'] .'app/'. $this->createMobileUrl($method, array('storeid' => $storeid), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2();
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }

        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE id=:id", array(':id' => $storeid));
        if (empty($store)) {
            message('没有相关数据!');
        }

        $store['thumb_url'] = unserialize($store['thumb_url']);

        include $this->template('dish_shop_show');
    }

    //智能点餐_选人数
    public function doMobileWapSelect()
    {
        global $_GPC, $_W;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $title = '微点餐';
        $storeid = intval($_GPC['storeid']);
        if ($storeid == 0) {
            $storeid = $this->getStoreID();
        }
        if (empty($storeid)) {
            message('请先选择门店', $this->createMobileUrl('waprestlist'));
        }
        $method = 'wapselect'; //method
        $url = $_W['siteroot'] . $this->createMobileUrl($method, array('storeid' => $storeid), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2();
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }
        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        $intelligents = pdo_fetchall("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE weid=:weid AND storeid=:storeid GROUP BY name ORDER by name", array(':weid' => $weid, ':storeid' => $storeid));
        include $this->template('dish_select');
    }

    //智能点餐_菜单页
    public function doMobileWapSelectList()
    {
        global $_GPC, $_W;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $title = '微点餐';
        $num = intval($_GPC['num']);
        if ($num <= 0) {
            message('非法参数');
        }

        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请先选择门店', $this->createMobileUrl('waprestlist'), true);
        }
        $method = 'wapselectlist'; //method
        $url = $_W['siteroot'] .'app/'. $this->createMobileUrl($method, array('storeid' => $storeid, true));
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2();
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }

        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        $intelligent_count = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->table_intelligent) . " WHERE name=:name AND weid=:weid AND storeid=:storeid", array(':name' => $num, ':weid' => $weid, ':storeid' => $storeid));

        //智能菜单id
        $intelligentid = intval($_GPC['intelligentid']);
        if ($intelligent_count > 1) {
            //随机抽取推荐菜单
            $intelligent = pdo_fetch("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE name=:name AND weid=:weid AND storeid=:storeid AND id<>:id ORDER BY RAND() limit 1", array(':name' => $num, ':weid' => $weid, ':storeid' => $storeid, ':id' => $intelligentid));
        } else {
            $intelligent = pdo_fetch("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE name=:name AND weid=:weid AND storeid=:storeid ORDER BY RAND() limit 1", array(':name' => $num, ':weid' => $weid, ':storeid' => $storeid));
        }

        //随机套餐id
        $intelligentid = intval($intelligent['id']);

        //读取相关产品
        $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE FIND_IN_SET(id, '{$intelligent['content']}') AND weid=:weid AND storeid=:storeid", array(':weid' => $weid, ':storeid' => $storeid));

        $total_money = 0;
        foreach ($goods as $key => $value) {
            $goods_arr[$value['id']] = array(
                'id' => $value['id'],
                'pcate' => $value['pcate'],
                'title' => $value['title'],
                'thumb' => $value['thumb'],
                'isspecial' => $value['isspecial'],
                'productprice' => $value['productprice'],
                'unitname' => $value['unitname'],
                'marketprice' => $value['marketprice'],
                'subcount' => $value['subcount'],
                'taste' => $value['taste'],
                'description' => $value['description']);
            $goods_tmp[] = $value['pcate'];
            $total_money += $value['isspecial'] == 1 ? intval($value['productprice']) : intval($value['marketprice']);
        }
        $condition = trim(implode(',', $goods_tmp));
        //读取类别
        $categorys = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid=:weid AND storeid=:storeid AND FIND_IN_SET(id, '{$condition}') ORDER BY displayorder DESC", array(':weid' => $weid, ':storeid' => $storeid));
        include $this->template('dish_select_list');
    }

    //我的订单
    public function doMobileOrderList()
    {
        global $_GPC, $_W;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $do = 'order';

        $storeid = intval($_GPC['storeid']);
        if ($storeid == 0) {
            $storeid = $this->getStoreID();
        }
        if (empty($storeid)) {
            message('请先选择门店', $this->createMobileUrl('waprestlist'));
        }
        $method = 'orderlist'; //method
        $url = $_W['siteroot'] .'app/'. $this->createMobileUrl($method, array('storeid' => $storeid), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $from_user = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2();
                if (!empty($userinfo)) {
                    $from_user = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->getCode($url);
                }
            }
        }
        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        //已确认
        $order_list_part1 = pdo_fetchall("SELECT a.* FROM " . tablename($this->table_order) . " AS a LEFT JOIN " . tablename($this->table_stores) . " AS b ON a.storeid=b.id  WHERE a.status=1 AND a.storeid={$storeid} AND a.from_user='{$from_user}' ORDER BY a.id DESC LIMIT 20");
        //数量
        $order_total_part1 = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE status=1 AND storeid={$storeid} AND from_user='{$from_user}' ORDER BY id DESC");
        foreach ($order_list_part1 as $key => $value) {
            $order_list_part1[$key]['goods'] = pdo_fetchall("SELECT a.*,b.title FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$value['id']}");
        }

        //未确认
        $order_list_part2 = pdo_fetchall("SELECT a.* FROM " . tablename($this->table_order) . " AS a LEFT JOIN " . tablename($this->table_stores) . " AS b ON a.storeid=b.id  WHERE a.status=0 AND a.storeid={$storeid} AND a.from_user='{$from_user}' ORDER BY a.id DESC LIMIT 20");
        //数量
        $order_total_part2 = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE status=0 AND storeid={$storeid} AND from_user='{$from_user}' ORDER BY id DESC");
        foreach ($order_list_part2 as $key => $value) {
            $order_list_part2[$key]['goods'] = pdo_fetchall("SELECT a.*,b.title FROM " . tablename($this->table_order_goods) . " AS a LEFT JOIN " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$value['id']}");
        }

        $order_list_part3 = pdo_fetchall("SELECT a.* FROM " . tablename($this->table_order) . " AS a LEFT JOIN " . tablename($this->table_stores) . " AS b ON a.storeid=b.id  WHERE (a.status=2) AND a.storeid={$storeid} AND a.from_user='{$from_user}' ORDER BY a.id DESC LIMIT 20");
        //数量
        $order_total_part3 = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE (status=2) AND storeid={$storeid} AND from_user='{$from_user}' ORDER BY id DESC");
        foreach ($order_list_part3 as $key => $value) {
            $order_list_part3[$key]['goods'] = pdo_fetchall("SELECT a.*,b.title FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$value['id']}");
        }

        $order_list_part4 = pdo_fetchall("SELECT a.* FROM " . tablename($this->table_order) . " AS a LEFT JOIN " . tablename($this->table_stores) . " AS b ON a.storeid=b.id  WHERE (a.status=3) AND a.storeid={$storeid} AND a.from_user='{$from_user}' ORDER BY a.id DESC LIMIT 20");
        //数量
        $order_total_part4 = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename($this->table_order) . " WHERE (status=3) AND storeid={$storeid} AND from_user='{$from_user}' ORDER BY id DESC");
        foreach ($order_list_part4 as $key => $value) {
            $order_list_part4[$key]['goods'] = pdo_fetchall("SELECT a.*,b.title FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$value['id']}");
        }

        //智能点餐
        $intelligents = pdo_fetchall("SELECT 1 FROM " . tablename($this->table_intelligent) . " WHERE weid={$weid} AND storeid={$storeid} GROUP BY name ORDER by name");

        include $this->template('dish_order_list');
    }


    //获取各个分类被选中菜品的数量
    public function doMobileGetDishNumOfCategory()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        $storeid = intval($_GPC['storeid']);

        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        $data = array();
        $category_in_cart = pdo_fetchall("SELECT goodstype,count(1) as 'goodscount' FROM " . tablename($this->table_cart) . " GROUP BY weid,storeid,goodstype,from_user  having weid = '{$weid}' AND storeid='{$storeid}' AND from_user='{$from_user}'");
        $category_arr = array();
        foreach ($category_in_cart as $key => $value) {
            $category_arr[$value['goodstype']] = $value['goodscount'];
        }

        $category = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " GROUP BY weid,storeid  having weid = :weid AND storeid=:storeid", array(':weid' => $weid, ':storeid' => $storeid));

        foreach ($category as $index => $row) {
            $data[$row['id']] = intval($category_arr[$row['id']]);
        }

        $result['data'] = $data;
        message($result, '', 'ajax');
    }

    //从购物车移除
    public function doMobileRemoveDishNumOfCategory()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        $storeid = intval($_GPC['storeid']); //门店id
        $dishid = intval($_GPC['dishid']); //菜品id
        $action = $_GPC['action'];

        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        if (empty($storeid)) {
            message('请先选择门店');
        }

        if ($action != 'remove') {
            $result['msg'] = '非法操作';
            message($result, '', 'ajax');
        }

        //查询购物车有没该商品
        $cart = pdo_fetch("SELECT * FROM " . tablename($this->table_cart) . " WHERE goodsid=:goodsid AND weid=:weid AND storeid=:storeid AND from_user='" . $from_user . "'", array(':goodsid' => $dishid, ':weid' => $weid, ':storeid' => $storeid));

        if (empty($cart)) {
            $result['msg'] = '购物车为空!';
            message($result, '', 'ajax');
        } else {
            pdo_delete('weisrc_dish_cart', array('id' => $cart['id']));
        }
        $result['code'] = 0;
        message($result, '', 'ajax');
    }

    //取得购物车中的菜品
    public function getDishCountInCart($storeid)
    {
        global $_GPC, $_W;
        $weid = $this->_weid;
        $from_user = $this->_fromuser;

        $dishlist = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE  storeid=:storeid AND from_user=:from_user AND weid=:weid", array(':from_user' => $from_user, ':weid' => $weid, ':storeid' => $storeid));
        foreach ($dishlist as $key => $value) {
            $arr[$value['goodsid']] = $value['total'];
        }
        return $arr;
    }

    //购物车增加菜品
    public function doMobileUpdateDishNumOfCategory()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        $storeid = intval($_GPC['storeid']); //门店id
        $dishid = intval($_GPC['dishid']); //菜品id
        $total = intval($_GPC['o2uNum']); //更新数量

        if (empty($from_user)) {
            $result['msg'] = '会话已过期，请重新发送关键字!';
            message($result, '', 'ajax');
        }

        //查询菜品是否存在
        $goods = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE  id=:id", array(":id" => $dishid));
        if (empty($goods)) {
            $result['msg'] = '没有相关商品';
            message($result, '', 'ajax');
        }

        //查询购物车有没该商品
        $cart = pdo_fetch("SELECT * FROM " . tablename($this->table_cart) . " WHERE goodsid=:goodsid AND weid=:weid AND storeid=:storeid AND from_user='" . $from_user . "'", array(':goodsid' => $dishid, ':weid' => $weid, ':storeid' => $storeid));

        if (empty($cart)) {
            //不存在的话增加菜品点击量
            pdo_query("UPDATE " . tablename($this->table_goods) . " SET subcount=subcount+1 WHERE id=:id", array(':id' => $dishid));
            //添加进购物车
            $data = array(
                'weid' => $weid,
                'storeid' => $goods['storeid'],
                'goodsid' => $goods['id'],
                'goodstype' => $goods['pcate'],
                'price' => $goods['isspecial'] == 1 ? $goods['productprice'] : $goods['marketprice'],
                'from_user' => $from_user,
                'total' => 1
            );
            pdo_insert($this->table_cart, $data);
        } else {
            //更新菜品在购物车中的数量
            pdo_query("UPDATE " . tablename($this->table_cart) . " SET total=" . $total . " WHERE id=:id", array(':id' => $cart['id']));
        }

        $result['code'] = 0;
        message($result, '', 'ajax');
    }

    //取得菜品列表
    public function doMobileGetDishList()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        $storeid = intval($_GPC['storeid']);
        $categoryid = intval($_GPC['categoryid']);
        $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = :weid AND status = 1 AND storeid=:storeid AND pcate=:pcate order by displayorder DESC,id DESC", array(':weid' => $weid, ':storeid' => $storeid, ':pcate' => $categoryid));

//        $result['debug'] = 'weid:'.$weid.'storeid'.$storeid.'cate:'.$categoryid;
//        message($result, '', 'ajax');

        $dish_arr = $this->getDishCountInCart($storeid);

        foreach ($list as $key => $row) {
            $subcount = intval($row['subcount']);
            $data[$key] = array(
                'id' => $row['id'],
                'title' => $row['title'],
                'dSpecialPrice' => $row['marketprice'],
                'dPrice' => $row['productprice'],
                'dDescribe' => $row['description'], //描述
                'dTaste' => $row['taste'], //口味
                'dSubCount' => $row['subcount'], //被点次数
                'thumb' => $row['thumb'],
                'unitname' => $row['unitname'],
                'dIsSpecial' => $row['isspecial'],
                'dIsHot' => $subcount > 20 ? 2 : 0,
                'total' => empty($dish_arr) ? 0 : intval($dish_arr[$row['id']]) //菜品数量
            );
        }
        $result['data'] = $data;
        $result['categoryid'] = $categoryid;

        //json_encode($result)

        message($result, '', 'ajax');
    }

    //清空购物车
    public function doMobileClearMenu()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        $storeid = intval($_GPC['storeid']);
        if (empty($storeid)) {
            message('请先选择门店');
        }

        pdo_delete('weisrc_dish_cart', array('weid' => $weid, 'from_user' => $from_user, 'storeid' => $storeid));
        $url = $this->createMobileUrl('waplist', array('storeid' => $storeid), true);
        message('操作成功', $url, 'success');
    }

    //添加菜品到菜单
    public function doMobileAddToMenu()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        if (empty($from_user)) {
            message('会话已过期，请重新发送关键字!');
        }

        $storeid = intval($_GPC['storeid']);

        $clearMenu = intval($_GPC['clearMenu']);
        //清空购物车
        if ($clearMenu == 1) {
            pdo_delete('weisrc_dish_cart', array('weid' => $weid, 'from_user' => $from_user, 'storeid' => $storeid));
        }

        //添加菜单所属菜品到
        $intelligentid = intval($_GPC['intelligentid']);
        $intelligent = pdo_fetch("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE id={$intelligentid} limit 1");

        if (!empty($intelligent)) {
            $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE FIND_IN_SET(id, '{$intelligent['content']}') AND weid={$weid} AND storeid={$storeid}");

            foreach ($goods as $key => $item) {
                //查询购物车有没该商品
                $cart = pdo_fetch("SELECT * FROM " . tablename($this->table_cart) . " WHERE goodsid=:goodsid AND weid=:weid AND storeid=:storeid AND from_user='" . $from_user . "'", array(':goodsid' => $item['id'], ':weid' => $weid, ':storeid' => $storeid));
                if (empty($cart)) {
                    //不存在的话增加菜品点击量
                    pdo_query("UPDATE " . tablename($this->table_goods) . " SET subcount=subcount+1 WHERE id=:id", array(':id' => $item['id']));
                    //添加进购物车
                    $data = array(
                        'weid' => $weid,
                        'storeid' => $item['storeid'],
                        'goodsid' => $item['id'],
                        'goodstype' => $item['pcate'],
                        'price' => $item['isspecial'] == 1 ? $item['productprice'] : $item['marketprice'],
                        'from_user' => $from_user,
                        'total' => 1
                    );
                    pdo_insert($this->table_cart, $data);
                }
            }
        }

        //跳转
        $url = $this->createMobileUrl('wapmenu', array('storeid' => $storeid), true);
        die('<script>location.href = "' . $url . '";</script>');
    }

    //提交订单
    public function doMobileAddToOrder()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;

        $storeid = intval($_GPC['storeid']);

        if (empty($from_user)) {
            $this->showMessageAjax('请重新发送关键字进入系统!', $this->msg_status_bad);
        }

        if (empty($storeid)) {
            $this->showMessageAjax('请先选择门店!', $this->msg_status_bad);
        }

        //查询购物车
        $cart = pdo_fetchall("SELECT * FROM " . tablename($this->table_cart) . " WHERE weid = :weid AND from_user = :from_user AND storeid=:storeid", array(':weid' => $weid, ':from_user' => $from_user, ':storeid' => $storeid), 'goodsid');

        if (empty($cart)) { //购物车为空
            $this->showMessageAjax('请先添加菜品!', $this->msg_status_bad);
        } else {
            $goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unitname FROM " . tablename($this->table_goods) . " WHERE id IN ('" . implode("','", array_keys($cart)) . "')");
        }

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid=:weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $storeid));

        $guest_name = trim($_GPC['guest_name']); //用户名
        $tel = trim($_GPC['tel']); //电话
        $sex = trim($_GPC['sex']); //性别
        $meal_time = trim($_GPC['meal_time']); //订餐时间
        $counts = intval($_GPC['counts']); //预订人数
        $seat_type = intval($_GPC['seat_type']); //就餐形式
        $carports = intval($_GPC['carports']); //预订车位
        $remark = trim($_GPC['remark']); //备注
        $address = trim($_GPC['address']); //地址
        $tables = intval($_GPC['tables']); //桌号
        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid={$weid} LIMIT 1");
        $ordertype = intval($_GPC['ordertype']) == 0 ? 1 : intval($_GPC['ordertype']);

        //更新粉丝信息
        fans_update($from_user, array('realname' => $guest_name, 'mobile' => $tel, 'address' => $address));
        //用户信息判断
        if (empty($guest_name)) {
            $this->showMessageAjax('请输入姓名!', $this->msg_status_bad);
        }
        if (empty($tel)) {
            $this->showMessageAjax('请输入联系电话!', $this->msg_status_bad);
        }

        if ($ordertype == 1) {//店内

            if ($counts <= 0) {
                $this->showMessageAjax('预订人数必须大于0!', $this->msg_status_bad);
            }
            if ($seat_type == 0) {
                $this->showMessageAjax('请选择就餐形式!', $this->msg_status_bad);
            }
            if ($tables == 0) {
//                $this->showMessageAjax('请输入桌号!', $this->msg_status_bad);
            }
        } else if ($ordertype == 2) {//外卖
            if (empty($address)) {
                $this->showMessageAjax('请输入联系地址!', $this->msg_status_bad);
            }
        }

        //2.购物车 //a.添加订单、订单产品
        //保存新订单 //提交、确认、付款、取消
        $totalnum = 0;
        $totalprice = 0;
        $goodsprice = 0;
        $dispatchprice = 0;

        if ($ordertype == 2) {
            $dispatchprice = $store['dispatchprice'];
        }

        foreach ($cart as $value) {
            $totalnum = $totalnum + intval($value['total']);
            $goodsprice = $goodsprice + (intval($value['total']) * floatval($value['price']));
        }

        $totalprice = $goodsprice + $dispatchprice;

        $fansid = $_W['fans']['id'];
        $data = array(
            'weid' => $weid,
            'from_user' => $from_user,
            'storeid' => $storeid,
            'ordersn' => date('md') . sprintf("%04d", $fansid) . random(4, 1), //订单号
            'totalnum' => $totalnum, //产品数量
            'totalprice' => $totalprice, //总价
            'goodsprice' => $goodsprice,
            'dispatchprice' => $dispatchprice,
            'paytype' => 0, //付款类型
            'username' => $guest_name,
            'tel' => $tel,
            'meal_time' => $meal_time,
            'counts' => $counts,
            'seat_type' => $seat_type,
            'tables' => $tables,
            'carports' => $carports,
            //'dining_mode' => $setting['dining_mode'], //用餐模式
            'dining_mode' => $ordertype, //订单类型
            'remark' => $remark, //备注
            'address' => $address, //地址
            'status' => 0, //状态
            'dateline' => TIMESTAMP
        );

        //保存订单
        pdo_insert($this->table_order, $data);
        $orderid = pdo_insertid();
        $prints = pdo_fetchall("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE storeid = :storeid AND print_status=1", array(':storeid' => $storeid));

        foreach ($prints as $key => $value) {
            $print_order_data = array(
                'weid' => $weid,
                'orderid' => $orderid,
                'print_usr' => $value['print_usr'],
                'print_status' => -1,
                'dateline' => TIMESTAMP
            );

            $print_order = pdo_fetch("SELECT * FROM " . tablename($this->table_print_order) . " WHERE orderid=:orderid AND print_usr=:usr LIMIT 1", array(':orderid' => $orderid, ':usr' => $value['print_usr']));

            if (empty($print_order)) {
                pdo_insert('weisrc_dish_print_order', $print_order_data);
            }
        }

        //保存新订单商品
        foreach ($cart as $row) {
            if (empty($row) || empty($row['total'])) {
                continue;
            }

            pdo_insert($this->table_order_goods, array(
                'weid' => $_W['uniacid'],
                'storeid' => $row['storeid'],
                'goodsid' => $row['goodsid'],
                'orderid' => $orderid,
                'price' => $row['price'],
                'total' => $row['total'],
                'dateline' => TIMESTAMP,
            ));
        }

        //清空购物车
        pdo_delete($this->table_cart, array('weid' => $weid, 'from_user' => $from_user, 'storeid' => $storeid));
        $result['orderid'] = $orderid;
        $result['code'] = $this->msg_status_success;
        $result['msg'] = '操作成功';
        message($result, '', 'ajax');
    }

    public function doMobilePay()
    {
        global $_W, $_GPC;
        checkauth();
        $orderid = intval($_GPC['orderid']);
        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = :id", array(':id' => $orderid));
        if ($order['status'] != '0') {
            message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', $this->createMobileUrl('orderlist', array('storeid' => $order['storeid'])), 'error');
        }
        $params['tid'] = $orderid;
        $params['user'] = $order['from_user'];
        $params['fee'] = $order['totalprice'];
        $params['title'] = $_W['account']['name'];
        $params['ordersn'] = $order['ordersn'];
        $params['virtual'] = false;
        include $this->template('pay');
    }

    public function payResult($params)
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $orderid = $params['tid'];
        $fee = intval($params['fee']);
        $data = array('status' => $params['result'] == 'success' ? 1 : 0);
        $paytype = array('credit' => '1', 'wechat' => '2', 'alipay' => '2', 'delivery' => '3');
        $data['paytype'] = $paytype[$params['type']];

        if ($params['type'] == 'wechat') {
            $data['transid'] = $params['tag']['transaction_id'];
        }

        if ($params['type'] == 'delivery') {
            $data['status'] = 1;
        }

        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = '{$orderid}'");
        $storeid = $order['storeid'];
        pdo_update($this->table_order, $data, array('id' => $orderid));
        if ($params['from'] == 'return') {
            //邮件提醒
            $goods_str = '';
            //本订单产品
            $goods = pdo_fetchall("SELECT a.*,b.title,b.unitname FROM " . tablename($this->table_order_goods) . " as a left join  " . tablename($this->table_goods) . " as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$orderid}");
            $goods_str = '';
            $goods_tplstr = '';
            $flag = false;
            foreach ($goods as $key => $value) {
                if (!$flag) {
                    $goods_str .= "{$value['title']} 价格：{$value['price']} 数量：{$value['total']}{$value['unitname']}";
                    $goods_tplstr .= "{$value['title']} {$value['total']}{$value['unitname']}";
                    $flag = true;
                } else {
                    $goods_str .= "<br/>{$value['title']} 价格：{$value['price']} 数量：{$value['total']}{$value['unitname']}";
                    $goods_tplstr .= ",{$value['title']} {$value['total']}{$value['unitname']}";
                }
            }

            //发送邮件提醒
            $emailSetting = pdo_fetch("SELECT * FROM " . tablename($this->table_email_setting) . " WHERE weid=:weid AND storeid=:storeid LIMIT 1", array(':weid' => $weid, ':storeid' => $storeid));
            $email_tpl = $emailSetting['email_business_tpl'];
            //您有新的订单：[sn]，收货人：[name]，菜单：[goods]，电话：[tel]，请及时确认订单！
            $email_tpl = "
        您有新订单：<br/>
        所属门店：[store]<br/>
        单号[sn] 总数量:[totalnum] 总价:[totalprice]<br/>
        菜单：[goods]<br/>
        姓名：[name]<br/>
        电话：[tel]<br/>
        桌号：[tables]<br/>
        备注：[remark]
        ";

            if ($order['dining_mode'] == 2) {
                $email_tpl = "
        您有新订单：<br/>
        所属门店：[store]<br/>
        单号[sn] 总数量:[totalnum] 总价:[totalprice]<br/>
        收货人：[name]<br/>
        送达时间：[mealtime]<br/>
        菜单：<br/>[goods]<br/>
        电话：[tel]<br/>
        地址：[address]<br>
        备注：[remark]
        ";
            }
            $smsStatus = '';
            //发送短信提醒
            $smsSetting = pdo_fetch("SELECT * FROM " . tablename($this->table_sms_setting) . " WHERE weid=:weid AND storeid=:storeid LIMIT 1", array(':weid' => $weid, ':storeid' => $storeid));
            $sendInfo = array();
            if (!empty($smsSetting)) {
                if ($smsSetting['sms_enable'] == 1 && !empty($smsSetting['sms_mobile'])) {
                    //模板
                    if (empty($smsSetting['sms_business_tpl'])) {
                        $smsSetting['sms_business_tpl'] = '您有新的订单：[sn]，收货人：[name]，电话：[tel]，请及时确认订单！';
                    }
                    //订单号
                    $smsSetting['sms_business_tpl'] = str_replace('[sn]', $order['ordersn'], $smsSetting['sms_business_tpl']);
                    //用户名
                    $smsSetting['sms_business_tpl'] = str_replace('[name]', $order['username'], $smsSetting['sms_business_tpl']);
                    //就餐时间
                    $smsSetting['sms_business_tpl'] = str_replace('[date]', $order['meal_time'], $smsSetting['sms_business_tpl']);
                    //电话
                    $smsSetting['sms_business_tpl'] = str_replace('[tel]', $order['tel'], $smsSetting['sms_business_tpl']);
                    $smsSetting['sms_business_tpl'] = str_replace('[totalnum]', $order['totalnum'], $smsSetting['sms_business_tpl']);
                    $smsSetting['sms_business_tpl'] = str_replace('[totalprice]', $order['totalprice'], $smsSetting['sms_business_tpl']);
                    $smsSetting['sms_business_tpl'] = str_replace('[address]', $order['address'], $smsSetting['sms_business_tpl']);
                    $smsSetting['sms_business_tpl'] = str_replace('[remark]', $order['remark'], $smsSetting['sms_business_tpl']);
                    $smsSetting['sms_business_tpl'] = str_replace('[goods]', $goods_str, $smsSetting['sms_business_tpl']);

                    $sendInfo['username'] = $smsSetting['sms_username'];
                    $sendInfo['pwd'] = $smsSetting['sms_pwd'];
                    $sendInfo['mobile'] = $smsSetting['sms_mobile'];
                    $sendInfo['content'] = $smsSetting['sms_business_tpl'];
                    //debug
                    if ($data['status'] == 1) {
                        if ($order['issms'] == 0) {
                            pdo_update($this->table_order, array('issms' => 1), array('id' => $orderid));
                            $return_result_code = $this->_sendSms($sendInfo);
                            $smsStatus = $this->sms_status[$return_result_code];
                        }
                    }
                }
            }


            $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid=:weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $storeid));

            if (!empty($emailSetting) && !empty($emailSetting['email'])) {
                $email_tpl = str_replace('[store]', $store['title'], $email_tpl);
                $email_tpl = str_replace('[sn]', $order['ordersn'], $email_tpl);
                $email_tpl = str_replace('[name]', $order['username'], $email_tpl);
                //用户名
                $email_tpl = str_replace('[name]', $order['username'], $email_tpl);
                //就餐时间
                $email_tpl = str_replace('[mealtime]', $order['meal_time'], $email_tpl);
                //电话
                $email_tpl = str_replace('[tel]', $order['tel'], $email_tpl);
                $email_tpl = str_replace('[tables]', $order['tables'], $email_tpl);
                $email_tpl = str_replace('[goods]', $goods_str, $email_tpl);
                $email_tpl = str_replace('[totalnum]', $order['totalnum'], $email_tpl);
                $email_tpl = str_replace('[totalprice]', $order['totalprice'], $email_tpl);
                $email_tpl = str_replace('[address]', $order['address'], $email_tpl);
                $email_tpl = str_replace('[remark]', $order['remark'], $email_tpl);
//                $email_tpl = str_replace('[mealtime]', $order['mealtime'], $email_tpl);

                if ($emailSetting['email_host'] == 'smtp.qq.com' || $emailSetting['email_host'] == 'smtp.gmail.com') {
                    $secure = 'ssl';
                    $port = '465';
                } else {
                    $secure = 'tls';
                    $port = '25';
                }

                $mail_config = array();
                $mail_config['host'] = $emailSetting['email_host'];
                $mail_config['secure'] = $secure;
                $mail_config['port'] = $port;
                $mail_config['username'] = $emailSetting['email_user'];
                $mail_config['sendmail'] = $emailSetting['email_send'];
                $mail_config['password'] = $emailSetting['email_pwd'];
                $mail_config['mailaddress'] = $emailSetting['email'];
                $mail_config['subject'] = '订单提醒';
                $mail_config['body'] = $email_tpl;

                if ($data['status'] == 1) {
                    if ($order['isemail'] == 0) {
                        pdo_update($this->table_order, array('isemail' => 1), array('id' => $orderid));
                        $result = $this->sendmail($mail_config);
                    }
                }
            }

            $setting = pdo_fetch("select * from " . tablename($this->table_setting) . " where weid =:weid LIMIT 1", array(':weid' => $weid));
            if (!empty($setting) && $setting['istplnotice'] == 1) {
                $templateid = $setting['tplneworder'];
                $noticeuser = $setting['tpluser'];
                $template = array(
                    'touser' => $noticeuser,
                    'template_id' => $templateid,
                    'url' => '',
                    'topcolor' => "#FF0000",
                    'data' => array(
                        'first' => array(
                            'value' => urlencode("您有一个新的订单"),
                            'color' => '#000'
                        ),
                        'keyword1' => array(
                            'value' => urlencode($order['ordersn']),
                            'color' => '#000'
                        ),
                        'keyword2' => array(
                            'value' => urlencode($order['username'] . ' ' . $order['tel']),
                            'color' => '#000'
                        ),
                        'keyword3' => array(
                            'value' => urlencode($goods_tplstr),
                            'color' => '#000'
                        ),
                        'keyword4' => array(
                            'value' => urlencode($order['address']),
                            'color' => '#000'
                        ),
                        'keyword5' => array(
                            'value' => urlencode($order['meal_time']),
                            'color' => '#000'
                        ),
                        'remark' => array(
                            'value' => urlencode('总金额:' . $order['totalprice']),
                            'color' => '#f00'
                        ),
                    )
                );

                if ($data['status'] == 1) {
                    if ($order['istpl'] == 0) {
                        pdo_update($this->table_order, array('istpl' => 1), array('id' => $orderid));
                        $templateMessage = new class_templateMessage($this->_appid, $this->_appsecret);
                        $access_token = WeAccount::token();
                        $templateMessage->send_template_message($template, $access_token);
                    }
                }
            }

            $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
            $credit = $setting['creditbehaviors']['currency'];
            if ($params['type'] == $credit) {
                message('支付成功！' . $smsStatus, $this->createMobileUrl('orderlist', array('storeid' => $storeid), true), 'success');
            } else {
                message('支付成功！' . $smsStatus, '../../app/' . $this->createMobileUrl('orderlist', array('storeid' => $storeid), true), 'success');
            }
        }
    }

    //public

    public function checkModule($name)
    {
        $module = pdo_fetch("SELECT * FROM " . tablename("modules") . " WHERE name=:name ", array(':name' => $name));
        return $module;
    }

    //提示信息
    public function showMessageAjax($msg, $code = 0)
    {
        $result['code'] = $code;
        $result['msg'] = $msg;
        message($result, '', 'ajax');
    }

    public function getStoreID()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid=:weid  ORDER BY id DESC LIMIT 1", array(':weid' => $weid));
        if (!empty($setting)) {
            return intval($setting['storeid']);
        } else {
            $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . "  WHERE weid={$weid}  ORDER BY id DESC LIMIT 1");
            return intval($store['id']);
        }
    }

    public function  doMobileAjaxdelete()
    {
        global $_GPC;
        $delurl = $_GPC['pic'];
        load()->func('file');
        if (file_delete($delurl)) {
            echo 1;
        } else {
            echo 0;
        }
    }

    function img_url($img = '')
    {
        global $_W;
        if (empty($img)) {
            return "";
        }
        if (substr($img, 0, 6) == 'avatar') {
            return $_W['siteroot'] . "resource/image/avatar/" . $img;
        }
        if (substr($img, 0, 8) == './themes') {
            return $_W['siteroot'] . $img;
        }
        if (substr($img, 0, 1) == '.') {
            return $_W['siteroot'] . substr($img, 2);
        }
        if (substr($img, 0, 5) == 'http:') {
            return $img;
        }
        return $_W['attachurl'] . $img;
    }

    //发送短信
    public function _sendSms($sendinfo)
    {
        global $_W;
        load()->func('communication');
        $weid = $_W['uniacid'];
        $username = $sendinfo['username'];
        $pwd = $sendinfo['pwd'];
        $mobile = $sendinfo['mobile'];
        $content = $sendinfo['content'];
        $target = "http://www.dxton.com/webservice/sms.asmx/Submit";
        //替换成自己的测试账号,参数顺序和wenservice对应
        $post_data = "account=" . $username . "&password=" . $pwd . "&mobile=" . $mobile . "&content=" . rawurlencode($content);
        //请自己解析$gets字符串并实现自己的逻辑
        //<result>100</result>表示成功,其它的参考文档

        $result = ihttp_request($target, $post_data);
        $xml = simplexml_load_string($result['content'], 'SimpleXMLElement', LIBXML_NOCDATA);
        $result = (string)$xml->result;
        $message = (string)$xml->message;
        return $result;
    }

    public function sendmail($config)
    {
        include 'plugin/email/class.phpmailer.php';
        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";
        $body = $config['body'];
        $mail->IsSMTP();
        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->SMTPSecure = $config['secure']; // sets the prefix to the servier
        $mail->Host = $config['host']; // sets the SMTP server
        $mail->Port = $config['port'];
        $mail->Username = $config['sendmail']; // 发件邮箱用户名
        $mail->Password = $config['password']; // 发件邮箱密码
        $mail->From = $config['sendmail']; //发件邮箱
        $mail->FromName = $config['username']; //发件人名称
        $mail->Subject = $config['subject']; //主题
        $mail->WordWrap = 50; // set word wrap
        $mail->MsgHTML($body);
        $mail->AddAddress($config['mailaddress'], ''); //收件人地址、名称
        $mail->IsHTML(true); // send as HTML
        if (!$mail->Send()) {
            $status = 0;
        } else {
            $status = 1;
        }
        return $status;
    }

    public function doMobileValidatecheckcode()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $_GPC['from_user'];
        $this->_fromuser = $from_user;
        $mobile = trim($_GPC['mobile']);
        $checkcode = trim($_GPC['checkcode']);

        if (empty($mobile)) {
            $this->showMsg('请输入手机号码!');
        }

        if (empty($checkcode)) {
            $this->showMsg('请输入验证码!');
        }

        $item = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid  AND from_user=:from_user AND checkcode=:checkcode ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user, ':checkcode' => $checkcode));

        if (empty($item)) {
            $this->showMsg('验证码输入错误!');
        } else {
            pdo_update('weisrc_dish_sms_checkcode', array('status' => 1), array('id' => $item['id']));
        }

        $this->showMsg('验证成功!', 1);
    }

    public function showMsg($msg, $status = 0)
    {
        $result = array('msg' => $msg, 'status' => $status);
        echo json_encode($result);
        exit();
    }

    //取得短信验证码
    public function doMobileGetCheckCode()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = trim($_GPC['from_user']);
        $this->_fromuser = $from_user;
        $mobile = trim($_GPC['mobile']);
        $storeid = intval($_GPC['storeid']);

        if (!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|147[0-9]{8}$/", $mobile)) {
            $this->showMsg('手机号码格式不对!');
        }

        $passcheckcode = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid  AND from_user=:from_user AND status=1 ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user));
        if (!empty($passcheckcode)) {
            $this->showMsg('发送成功!', 1);
        }

        $smsSetting = pdo_fetch("SELECT * FROM " . tablename($this->table_sms_setting) . " WHERE weid=:weid AND storeid=:storeid LIMIT 1", array(':weid' => $weid, ':storeid' => $storeid));
        if (empty($smsSetting)) {
            $this->showMsg('请先选择门店!');
        }

        $checkCodeCount = pdo_fetchcolumn("SELECT count(1) FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid  AND from_user=:from_user ", array(':weid' => $weid, ':from_user' => $from_user));
        if ($checkCodeCount >= 3) {
            $this->showMsg('您请求的验证码已超过最大限制..' . $checkCodeCount);
        }

        //判断数据是否已经存在
        $data = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid  AND from_user=:from_user ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user));
        if (!empty($data)) {
            if (TIMESTAMP - $data['dateline'] < 60) {
                $this->showMsg('每分钟只能获取短信一次!');
            }
        }

        //验证码
        $checkcode = random(6, 1);
        $checkcode = $this->getNewCheckCode($checkcode);
        $data = array(
            'weid' => $weid,
            'from_user' => $from_user,
            'mobile' => $mobile,
            'checkcode' => $checkcode,
            'status' => 0,
            'dateline' => TIMESTAMP
        );

        $sendInfo = array();
        $sendInfo['username'] = $smsSetting['sms_username'];
        $sendInfo['pwd'] = $smsSetting['sms_pwd'];
        //$sendInfo['mobile'] = $smsSetting['sms_mobile'];
        $sendInfo['mobile'] = $mobile;
        $sendInfo['content'] = "您的验证码是：" . $checkcode . "。如需帮助请联系客服。";
        $return_result_code = $this->_sendSms($sendInfo);
        if ($return_result_code != '100') {
            $code_msg = $this->sms_status[$return_result_code];
            $this->showMsg($code_msg . $return_result_code);
        } else {
            pdo_insert('weisrc_dish_sms_checkcode', $data);
            $this->showMsg('发送成功!', 1);
        }
    }

    public function getNewCheckCode($checkcode)
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $from_user = $this->_from_user;

        $data = pdo_fetch("SELECT checkcode FROM " . tablename('weisrc_dish_sms_checkcode') . " WHERE weid = :weid AND checkcode = :checkcode AND from_user=:from_user ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':checkcode' => $checkcode, ':from_user' => $from_user));

        if (!empty($data)) {
            $checkcode = random(6, 1);
            $this->getNewCheckCode($checkcode);
        }
        return $checkcode;
    }

    //打印数据
    public function doWebPrint()
    {
        global $_W, $_GPC;
        $weid = $_W['uniacid'];
        $usr = !empty($_GET['usr']) ? $_GET['usr'] : '355839026790719';
        $ord = !empty($_GET['ord']) ? $_GET['ord'] : 'no';
        $sgn = !empty($_GET['sgn']) ? $_GET['sgn'] : 'no';

        header('Content-type: text/html; charset=gbk');

        $print_type_confirmed = 0;
        $print_type_payment = 1;

        //更新打印状态
        if (isset($_GET['sta'])) {
            $id = intval($_GPC['id']); //订单id
            $sta = intval($_GPC['sta']); //状态

            pdo_update($this->table_print_order, array('print_status' => $sta), array('orderid' => $id, 'print_usr' => $usr));
            //id —— 平台下发打印数据的id号,打印机打印后回复打印是否成功带此id号。
            //usr -- 打印机终端系统的IMEI号码或SIM卡的IMSI号码
            //sta —— 打印机状态(0为打印成功, 1为过热,3为缺纸卡纸等)
            exit;
        }

        //打印机配置信息
        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE print_usr = :usr AND print_status=1", array(':usr' => $usr));
        if ($setting == false) {
            exit;
        }

        //门店id
        $storeid = $setting['storeid'];

        $condition = "";
        if ($setting['print_type'] == $print_type_confirmed) {
            //已确认订单 //status == 1
            $condition = ' AND status=1 ';
        } else if ($setting['print_type'] == $print_type_payment) {
            //已付款订单 //已完成
            $condition = ' AND (status=2 or status=3) ';
        }

        //根据订单id读取相关订单
        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE  id IN(SELECT orderid FROM ims_weisrc_dish_print_order WHERE print_status=-1 AND print_usr=:print_usr) AND storeid = :storeid {$condition} ORDER BY id DESC limit 1", array(':storeid' => $storeid, ':print_usr' => $usr));

        //没有新订单
        if ($order == false) {
            message('no data!');
            exit;
        }

        //菜品id数组
        $goodsid = pdo_fetchall("SELECT goodsid, total FROM " . tablename($this->table_order_goods) . " WHERE orderid = '{$order['id']}'", array(), 'goodsid');

        //菜品
        $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . "  WHERE id IN ('" . implode("','", array_keys($goodsid)) . "')");
        $order['goods'] = $goods;

        if (!empty($setting['print_top'])) {
            $content = "%10" . $setting['print_top'] . "\n";
        } else {
            $content = '';
        }

//        {if $item['paytype'] == 0}线下付款{/if}
//                        {if $item['paytype'] == 1}余额支付{/if}
//                        {if $item['paytype'] == 2}在线支付{/if}
//                        {if $item['paytype'] == 3}货到付款{/if}

        $paytype = array('0' => '线下付款', '1' => '余额支付', '2' => 在线支付, '3' => '货到付款');

        $content .= '%00单号:' . $order['ordersn'] . "\n";
        $content .= '支付方式:' . $paytype[$order['paytype']] . "\n";
        $content .= '下单日期:' . date('Y-m-d H:i:s', $order['dateline']) . "\n";
        $content .= '预约时间:' . $order['meal_time'] . "\n";
        if (!empty($order['seat_type'])) {
            $seat_type = $order['seat_type'] == 1 ? '大厅' : '包间';
            $content .= '%10位置类型:' . $seat_type . "\n";
        }
        if (!empty($order['tables'])) {
            $content .= '%10桌号:' . $order['tables'] . "\n";
        }

        if (!empty($order['remark'])) {
            $content .= '%10备注:' . $order['remark'] . "\n";
        }
        $content .= "%00\n名称              数量  单价 \n";
        $content .= "----------------------------\n%10";

        $content1 = '';
        foreach ($order['goods'] as $v) {
            if ($v['isspecial'] == 2) {
                $money = intval($v['marketprice']) == 0 ? $v['productprice'] : $v['marketprice'];
            } else {
                $money = $v['productprice'];
            }

            $content1 .= $this->stringformat($v['title'], 16) . $this->stringformat($goodsid[$v['id']]['total'], 4, false) . $this->stringformat(number_format($money, 1), 7, false) . "\n\n";
        }

        $content2 = "----------------------------\n";
        $content2 .= "%10总数量:" . $order['totalnum'] . "   总价:" . number_format($order['totalprice'], 1) . "元\n%00";
        if (!empty($order['username'])) {
            $content2 .= '姓名:' . $order['username'] . "\n";
        }
        if (!empty($order['tel'])) {
            $content2 .= '手机:' . $order['tel'] . "\n";
        }
        if (!empty($order['address'])) {
            $content2 .= '地址:' . $order['address'] . "\n";
        }

        if (!empty($setting['qrcode_status'])) {
            $qrcode_url = trim($setting['qrcode_url']);
            if (!empty($qrcode_url)) {
                $content2 .= "%%%50372C" . $qrcode_url . "\n";
            }
        }

        //$content2 .= "%%%50372Chttp://www.weisrc.com\n";

        if (!empty($setting['print_bottom'])) {
            $content2 .= "%10" . $setting['print_bottom'] . "\n%00";
        }

        $content = iconv("UTF-8", "GB2312//IGNORE", $content);
        $content1 = iconv("UTF-8", "GB2312//IGNORE", $content1);
        $content2 = iconv("UTF-8", "GB2312//IGNORE", $content2);

        $setting = '<setting>124:' . $setting['print_nums'] . '|134:0</setting>';
        $setting = iconv("UTF-8", "GB2312//IGNORE", $setting);
        echo '<?xml version="1.0" encoding="GBK"?><r><id>' . $order['id'] . '</id><time>' . date('Y-m-d H:i:s', $order['dateline']) . '</time><content>' . $content . $content1 . $content2 . '</content>' . $setting . '</r>';
    }

    //用户打印机处理订单
    private function stringformat($string, $length = 0, $isleft = true)
    {
        $substr = '';
        if ($length == 0 || $string == '') {
            return $string;
        }
        if (strlen($string) > $length) {
            for ($i = 0; $i < $length; $i++) {
                $substr = $substr . "_";
            }
            $string = $string . '%%' . $substr;
        } else {
            for ($i = strlen($string); $i < $length; $i++) {
                $substr = $substr . " ";
            }
            $string = $isleft ? ($string . $substr) : ($substr . $string);
        }
        return $string;
    }

    private $version = '2|8|4|4|6|1|2|8|0';

    public function doMobileVersion()
    {
        message($this->version);
    }

    function authorization()
    {
        $host = get_domain();
        return base64_encode($host);
    }

    function code_compare($a, $b)
    {
        if ($this->_debug == 1) {
            if ($_SERVER['HTTP_HOST'] == '127.0.0.1') {
                return true;
            }
        }
        if ($a != $b) {
            message(base64_decode("5a+55LiN6LW377yM5oKo5L2/55So55qE57O757uf5piv55Sx6Z2e5rOV5rig6YGT5Lyg5pKt55qE77yM6K+35pSv5oyB5q2j54mI44CC6LSt5Lmw6L2v5Lu26K+36IGU57O7UVExNTU5NTc1NeOAgg=="));
        }
    }

    function isWeixin()
    {
        if ($this->_weixin == 1) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            if (!strpos($userAgent, 'MicroMessenger')) {
                include $this->template('s404');
                exit();
            }
        }
    }

    public function oauth2()
    {
        global $_GPC, $_W;
        load()->func('communication');
        $code = $_GPC['code'];
        $token = $this->getAuthorizationCode($code);
        $from_user = $token['openid'];
        $userinfo = $this->getUserInfo($from_user);
        if ($userinfo['subscribe'] == 0) {
            //未关注用户通过网页授权access_token
            $userinfo = $this->getUserInfo($from_user, $token['access_token']);
        }
        if (empty($userinfo) || !is_array($userinfo) || empty($userinfo['openid']) || empty($userinfo['nickname'])) {
            echo '<h1>获取微信公众号授权失败[无法取得userinfo], 请稍后重试！ 公众平台返回原始数据为: <br />' . $userinfo['meta'] . '<h1>';
            exit;
        }
        $headimgurl = $userinfo['headimgurl'];
        $nickname = $userinfo['nickname'];
        //设置cookie信息
        setcookie($this->_auth2_headimgurl, $headimgurl, time() + 3600 * 24);
        setcookie($this->_auth2_nickname, $nickname, time() + 3600 * 24);
        setcookie($this->_auth2_openid, $from_user, time() + 3600 * 24);
        return $userinfo;
    }

    public function getUserInfo($from_user, $ACCESS_TOKEN = '')
    {
        if ($ACCESS_TOKEN == '') {
            $ACCESS_TOKEN = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$ACCESS_TOKEN}&openid={$from_user}&lang=zh_CN";
        } else {
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$ACCESS_TOKEN}&openid={$from_user}&lang=zh_CN";
        }
        $json = ihttp_get($url);
        $userInfo = @json_decode($json['content'], true);
        return $userInfo;
    }

    public function getAuthorizationCode($code)
    {
        $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->_appid}&secret={$this->_appsecret}&code={$code}&grant_type=authorization_code";
        $content = ihttp_get($oauth2_code);
        $token = @json_decode($content['content'], true);
        if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
            echo '<h1>获取微信公众号授权' . $code . '失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
            exit;
        }
        return $token;
    }

    public function getAccessToken()
    {
        global $_W;
        $account = $_W['account'];
        if(is_array($account['access_token']) && !empty($account['access_token']['token']) && !empty($account['access_token']['expire']) && $account['access_token']['expire'] > TIMESTAMP) {
            return $account['access_token']['token'];
        } else {
            if(empty($account['acid'])) {
                message('参数错误.');
            }
            if (empty($account['key']) || empty($account['secret'])) {
                message('请填写公众号的appid及appsecret, (需要你的号码为微信服务号)！', url('account/bind/post', array('acid' => $account['acid'], 'uniacid' => $account['uniacid'])), 'error');
            }
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$account['key']}&secret={$account['secret']}";
            $content = ihttp_get($url);
            if(empty($content)) {
                message('获取微信公众号授权失败, 请稍后重试！');
            }
            $token = @json_decode($content['content'], true);
            if(empty($token) || !is_array($token)) {
                message('获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为: <br />' . $token);
            }
            if(empty($token['access_token']) || empty($token['expires_in'])) {
                message('解析微信公众号授权失败, 请稍后重试！');
            }
            $record = array();
            $record['token'] = $token['access_token'];
            $record['expire'] = TIMESTAMP + $token['expires_in'];
            $row = array();
            $row['access_token'] = iserializer($record);
            pdo_update('account_wechats', $row, array('acid' => $account['acid']));
            return $record['token'];
        }
    }

    public function getCode($url)
    {
        global $_W;
        $url = urlencode($url);
        $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->_appid}&redirect_uri={$url}&response_type=code&scope=snsapi_base&state=0#wechat_redirect";
        header("location:$oauth2_code");
    }

    public $actions_titles = array(
        'stores' => '返回门店管理',
        'order' => '订单管理',
        'category' => '类别管理',
        'goods' => '菜品管理',
        'intelligent' => '智能选菜',
        'smssetting' => '短信设置',
        'emailsetting' => '邮件设置',
        'printsetting' => '打印机设置',
        'printorder' => '打印订单管理'
        //'storesetting' => '门店设置'
    );

    public $sms_status = array(
        '100' => '发送成功',
        '101' => '验证失败',
        '102' => '手机号码格式不正确',
        '103' => '会员级别不够',
        '104' => '内容未审核',
        '105' => '内容过多',
        '106' => '账户余额不足',
        '107' => 'Ip受限',
        '108' => '手机号码发送太频繁，请换号或隔天再发',
        '109' => '帐号被锁定',
        '110' => '手机号发送频率持续过高，黑名单屏蔽数日',
        '111' => '系统升级',
    );

    public function insert_default_nave($name, $type, $link)
    {
        global $_GPC, $_W;
        checklogin();

        $data = array(
            'weid' => $_W['uniacid'],
            'type' => $type,
            'name' => $name,
            'link' => $link,
            'displayorder' => 0,
            'status' => 1,
        );

        $nave = pdo_fetch("SELECT * FROM " . tablename($this->table_nave) . " WHERE name = :name AND weid=:weid", array(':name' => $name, ':weid' => $_W['uniacid']));

        if (empty($nave)) {
            pdo_insert($this->table_nave, $data);
        }
        return pdo_insertid();
    }

    public function doWebNave()
    {
        global $_W, $_GPC;
        checklogin();

        $action = 'nave';
        $title = '导航管理'; //$title = $this->actions_titles[$action];

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            if ($_GPC['type'] == 'default') {
                $this->insert_default_nave('我的菜单', 4, '');
                $this->insert_default_nave('智能点餐', 5, '');
                $this->insert_default_nave('菜品列表', 3, '');
                $this->insert_default_nave('我的订单', 6, '');
                $this->insert_default_nave('门店列表', 2, '');
            }

            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_nave, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $this->createWebUrl('nave', array('op' => 'display')), 'success');
            }
            $children = array();
            $nave = pdo_fetchall("SELECT * FROM " . tablename($this->table_nave) . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC,id DESC");
            include $this->template('site_nave');
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $nave = pdo_fetch("SELECT * FROM " . tablename($this->table_nave) . " WHERE id = '$id'");
            }

            if (checksubmit('submit')) {
                if (empty($_GPC['linkname'])) {
                    message('抱歉，请输入导航名称！');
                }

                $data = array(
                    'weid' => $_W['uniacid'],
                    'type' => intval($_GPC['type']),
                    'name' => trim($_GPC['linkname']),
                    'link' => trim($_GPC['link']),
                    'status' => intval($_GPC['status']),
                    'displayorder' => intval($_GPC['displayorder']),
                );

                if (!empty($id)) {
                    pdo_update($this->table_nave, $data, array('id' => $id));
                } else {
                    pdo_insert($this->table_nave, $data);
                    $id = pdo_insertid();
                }
                message('更新成功！', $this->createWebUrl('nave', array('op' => 'display')), 'success');
            }
            include $this->template('site_nave');
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $nave = pdo_fetch("SELECT id FROM " . tablename($this->table_nave) . " WHERE id = '$id'");
            if (empty($nave)) {
                message('抱歉，不存在或是已经被删除！', $this->createWebUrl('nave', array('op' => 'display')), 'error');
            }
            pdo_delete($this->table_nave, array('id' => $id));
            message('删除成功！', $this->createWebUrl('nave', array('op' => 'display')), 'success');
        }
    }

    public function doWebSmsSetting()
    {
        global $_GPC, $_W;
        checklogin();
        $action = 'smssetting';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid AND id=:storeid ORDER BY `id` DESC", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
        if (empty($store)) {
            message('非法操作.');
        }

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_sms_setting) . " WHERE weid = :weid AND storeid=:storeid", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
        if (checksubmit('submit')) {
            $data = array(
                'weid' => $_W['uniacid'],
                'storeid' => $storeid,
                'sms_enable' => intval($_GPC['sms_enable']),
                'sms_username' => trim($_GPC['sms_username']),
                'sms_pwd' => trim($_GPC['sms_pwd']),
                'sms_verify_enable' => intval($_GPC['sms_verify_enable']),
                'sms_mobile' => trim($_GPC['sms_mobile']),
                'sms_business_tpl' => trim($_GPC['sms_business_tpl']),
                'dateline' => TIMESTAMP
            );

            if (empty($setting)) {
                pdo_insert($this->table_sms_setting, $data);
            } else {
                unset($data['dateline']);
                pdo_update($this->table_sms_setting, $data, array('weid' => $_W['uniacid'], 'storeid' => $storeid));
            }
            message('操作成功', $this->createWebUrl('smssetting', array('storeid' => $storeid)), 'success');
        }
        include $this->template('sms_setting');
    }

    public function doWebEmailSetting()
    {
        global $_GPC, $_W;
        checklogin();
        $action = 'emailsetting';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid AND id=:storeid ORDER BY `id` DESC", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
        if (empty($store)) {
            message('非法操作.');
        }

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_email_setting) . " WHERE weid = :weid AND storeid=:storeid", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));

        if (checksubmit('submit')) {
            if (empty($_GPC['email_send']) || empty($_GPC['email_user']) || empty($_GPC['email_pwd'])) {
                message('请完整填写邮件配置信息', 'refresh', 'error');
            }
            if ($_GPC['email_host'] == 'smtp.qq.com' || $_GPC['email_host'] == 'smtp.gmail.com') {
                $secure = 'ssl';
                $port = '465';
            } else {
                $secure = 'tls';
                $port = '25';
            }
            //$result = $this->sendmail($_GPC['email_host'], $secure, $port, $_GPC['email_send'], $_GPC['email_user'], $_GPC['email_pwd'], $_GPC['email_send']);
            //public function sendmail($cfghost,$cfgsecure,$cfgport,$cfgsendmail,$cfgsenduser,$cfgsendpwd,$mailaddress) {

            $mail_config = array();
            $mail_config['host'] = $_GPC['email_host'];
            $mail_config['secure'] = $secure;
            $mail_config['port'] = $port;
            $mail_config['username'] = $_GPC['email_user'];
            $mail_config['sendmail'] = $_GPC['email_send'];
            $mail_config['password'] = $_GPC['email_pwd'];
            $mail_config['mailaddress'] = $_GPC['email'];
            $mail_config['subject'] = '微点餐提醒';
            $mail_config['body'] = '邮箱测试';


            $data = array(
                'weid' => $_W['uniacid'],
                'storeid' => $storeid,
                'email_enable' => intval($_GPC['email_enable']),
                'email_host' => $_GPC['email_host'],
                'email_send' => $_GPC['email_send'],
                'email_pwd' => $_GPC['email_pwd'],
                'email_user' => $_GPC['email_user'],
                'email' => trim($_GPC['email']),
                'email_business_tpl' => trim($_GPC['email_business_tpl']),
                'dateline' => TIMESTAMP
            );

            if (empty($setting)) {
                pdo_insert($this->table_email_setting, $data);
            } else {
                unset($data['dateline']);
                pdo_update($this->table_email_setting, $data, array('weid' => $_W['uniacid'], 'storeid' => $storeid));
            }

            $result = $this->sendmail($mail_config);
            if ($result == 1) {
                message('邮箱配置成功', 'refresh');
            } else {
                message('邮箱配置信息有误', 'refresh', 'error');
            }

            message('操作成功', $this->createWebUrl('emailsetting', array('storeid' => $storeid)), 'success');
        }
        include $this->template('email_setting');
    }

    //打印机设置
    public function doWebPrintSetting()
    {
        global $_GPC, $_W;
        checklogin();
        $action = 'printsetting';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        $store = pdo_fetch("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid AND id=:storeid ORDER BY `id` DESC", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
        if (empty($store)) {
            message('非法操作！门店不存在.');
        }

        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE weid = :weid AND storeid=:storeid", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
            $print_order_count = pdo_fetchall("SELECT print_usr,COUNT(1) as count FROM ".tablename($this->table_print_order)."  GROUP BY print_usr,weid having weid = :weid", array(':weid' => $_W['weid']), 'print_usr');
        } else if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE weid = :weid AND storeid=:storeid AND id=:id", array(':weid' => $_W['uniacid'], ':storeid' => $storeid, ':id' => $id));
            if (checksubmit('submit')) {
                $data = array(
                    'weid' => $_W['uniacid'],
                    'storeid' => $storeid,
                    'weid' => $_W['uniacid'],
                    'title' => trim($_GPC['title']),
                    'print_status' => trim($_GPC['print_status']),
                    'print_type' => trim($_GPC['print_type']),
                    'print_usr' => trim($_GPC['print_usr']),
                    'print_nums' => trim($_GPC['print_nums']),
                    'print_top' => trim($_GPC['print_top']),
                    'print_bottom' => trim($_GPC['print_bottom']),
                    'qrcode_status' => intval($_GPC['qrcode_status']),
                    'qrcode_url' => trim($_GPC['qrcode_url']),
                    'dateline' => TIMESTAMP
                );
                if (empty($setting)) {
                    $flag = pdo_fetch("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE print_usr=:print_usr LIMIT 1", array(':print_usr' => trim($_GPC['print_usr'])));
                    if (!empty($flag)) {
                        message('打印机终端编号已经被使用,不能重复添加！', $this->createWebUrl('printsetting', array('storeid' => $storeid)), 'success');
                    }
                    pdo_insert($this->table_print_setting, $data);
                } else {
                    unset($data['dateline']);
                    $flag = pdo_fetch("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE print_usr=:print_usr AND id<>:id LIMIT 1", array(':print_usr' => trim($_GPC['print_usr']), ':id' => $id));
                    if (!empty($flag)) {
                        message('打印机终端编号已经被使用,不能重复添加！', $this->createWebUrl('printsetting', array('storeid' => $storeid)), 'success');
                    }

                    pdo_update($this->table_print_setting, $data, array('weid' => $_W['uniacid'], 'storeid' => $storeid, 'id' => $id));
                }
                message('操作成功', $this->createWebUrl('printsetting', array('storeid' => $storeid)), 'success');
            }
        } else if ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $print = pdo_fetch("SELECT id FROM " . tablename($this->table_print_setting) . " WHERE id = '$id'");
            if (empty($print)) {
                message('抱歉，不存在或是已经被删除！', $this->createWebUrl('printsetting', array('op' => 'display', 'storeid' => $storeid)), 'error');
            }

            pdo_delete($this->table_print_setting, array('id' => $id, 'weid' => $_W['uniacid']));
            message('删除成功！', $this->createWebUrl('printsetting', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }

        include $this->template('print_setting');
    }

    public function doWebPrintOrder()
    {
        global $_W, $_GPC;
        checklogin();
        $action = 'printorder';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;

            if (!empty($_GPC['usr'])) {
                $condition = " AND print_usr={$_GPC['usr']} ";
            }

            if (!empty($_GPC['ordersn'])) {
                $condition .= " AND ordersn LIKE '%{$_GPC['ordersn']}%' ";
            }

            if (!empty($_GPC['selusr'])) {
                $condition .= " AND print_usr LIKE '%{$_GPC['selusr']}%' ";
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_order) . " a INNER JOIN " . tablename($this->table_print_order) . " b ON a.id=b.orderid WHERE a.weid = :weid AND a.storeid=:storeid {$condition} ORDER BY b.id DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));

            if (!empty($list)) {
                $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_order) . " a INNER JOIN " . tablename($this->table_print_order) . " b ON a.id=b.orderid WHERE a.weid = :weid AND a.storeid=:storeid  $condition", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
                $pager = pagination($total, $pindex, $psize);
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            pdo_delete($this->table_print_order, array('id' => $id, 'weid' => $_W['uniacid']));
            message('删除成功！', $this->createWebUrl('printorder', array('op' => 'display', 'storeid' => $storeid)), 'success');
        } elseif ($operation == 'deleteprintorder') {
            //删除未打印订单
            pdo_delete($this->table_print_order, array('weid' => $_W['uniacid'], 'print_status'  => -1));
            message('删除成功！', $this->createWebUrl('printorder', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }

        include $this->template('print_order');
    }

    //基本设置
    public function doWebSetting()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        checklogin();
        $action = 'setting';
        $title = '网站设置';

        load()->func('tpl');

        $stores = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " WHERE weid = :weid ORDER BY `id` DESC", array(':weid' => $_W['uniacid']));
        if (empty($stores)) {
            $url = $this->createWebUrl('stores', array('op' => 'display'));
            message('请先添加门店', $url);
        }

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->table_setting) . " WHERE weid = :weid", array(':weid' => $_W['uniacid']));
        if (checksubmit('submit')) {
            $data = array(
                'weid' => $_W['uniacid'],
                'title' => trim($_GPC['title']),
                'thumb' => trim($_GPC['thumb']),
                'storeid' => intval($_GPC['storeid']),
                'entrance_type' => intval($_GPC['entrance_type']),
                'entrance_storeid' => intval($_GPC['entrance_storeid']),
                'order_enable' => intval($_GPC['order_enable']),
                'dining_mode' => intval($_GPC['dining_mode']),
                'istplnotice' => intval($_GPC['istplnotice']),
                'tplneworder' => trim($_GPC['tplneworder']),
                'tpluser' => trim($_GPC['tpluser']),
                'dateline' => TIMESTAMP
            );

            if (empty($setting)) {
                pdo_insert($this->table_setting, $data);
            } else {
                unset($data['dateline']);
                pdo_update($this->table_setting, $data, array('weid' => $_W['uniacid']));
            }

            if (is_array($_GPC['begintime'])) {
                foreach ($_GPC['begintime'] as $id => $val) {
                    $begintime = $_GPC['begintime'][$id];
                    $endtime = $_GPC['endtime'][$id];
                    if (empty($begintime) || empty($endtime)) {
                        continue;
                    }

                    $data = array(
                        'weid' => $weid,
                        'storeid' => 0,
                        'begintime' => $begintime,
                        'endtime' => $endtime,
                    );
                    pdo_update('weisrc_dish_mealtime', $data, array('id' => $id));
                }
            }

            //增加
            if (is_array($_GPC['newbegintime'])) {
                foreach ($_GPC['newbegintime'] as $nid => $val) {
                    $begintime = $_GPC['newbegintime'][$nid];
                    $endtime = $_GPC['newendtime'][$nid];
                    if (empty($begintime) || empty($endtime)) {
                        continue;
                    }

                    $data = array(
                        'weid' => $weid,
                        'storeid' => 0,
                        'begintime' => $begintime,
                        'endtime' => $endtime,
                        'dateline' => TIMESTAMP
                    );
                    pdo_insert('weisrc_dish_mealtime', $data);
                }
            }
            message('操作成功', $this->createWebUrl('setting'), 'success');
        }

        $timelist = pdo_fetchall("SELECT * FROM " . tablename('weisrc_dish_mealtime') . " WHERE weid = '{$_W['uniacid']}' order by id");

        include $this->template('setting');
    }

    public function doWebDeletemealtime()
    {
        global $_W, $_GPC;
        $weid = $this->_weid;
        $id = intval($_GPC['id']);
        $storeid = intval($_GPC['storeid']);

        if (empty($storeid)) {
            $url = $this->createWebUrl('setting');
        }

        pdo_delete('weisrc_dish_mealtime', array('id' => $id, 'weid' => $weid));
        message('操作成功', $url, 'success');
    }

    //门店管理
    public function doWebStores()
    {
        global $_W, $_GPC;
        checklogin();

        $action = 'stores';
        $title = '门店管理';
        $url = $this->createWebUrl($action, array('op' => 'display'));
        $area = pdo_fetchall("SELECT * FROM " . tablename($this->table_area) . " where weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            if (checksubmit('submit')) { //排序
                if (is_array($_GPC['displayorder'])) {
                    foreach ($_GPC['displayorder'] as $id => $val) {
                        $data = array('displayorder' => intval($_GPC['displayorder'][$id]));
                        pdo_update($this->table_stores, $data, array('id' => $id));
                    }
                }
                message('操作成功!', $url);
            }
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $where = "WHERE weid = '{$_W['uniacid']}'";
            $storeslist = pdo_fetchall("SELECT * FROM " . tablename($this->table_stores) . " {$where} order by displayorder desc,id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
            if (!empty($storeslist)) {
                $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_stores) . " $where");
                $pager = pagination($total, $pindex, $psize);
            }
            include $this->template('stores');
        } elseif ($operation == 'post') {
            load()->func('tpl');
            $id = intval($_GPC['id']); //门店编号
            $reply = pdo_fetch("select * from " . tablename($this->table_stores) . " where id=:id and weid =:weid", array(':id' => $id, ':weid' => $_W['uniacid']));
            if (!empty($id)) {
                if (empty($reply)) {
                    message('抱歉，数据不存在或是已经删除！', '', 'error');
                } else {
//                    if (!empty($reply['thumb_url'])) {
//                        $reply['thumbArr'] = explode('|', $reply['thumb_url']);
//                    }
                }
            }
            $piclist = unserialize($reply['thumb_url']);

            if (checksubmit('submit')) {
                $data = array();
                $data['weid'] = intval($_W['uniacid']);
                $data['areaid'] = intval($_GPC['area']);
                $data['title'] = trim($_GPC['title']);
                $data['info'] = trim($_GPC['info']);
                $data['content'] = trim($_GPC['content']);
                $data['tel'] = trim($_GPC['tel']);
                $data['logo'] = trim($_GPC['logo']);
                $data['address'] = trim($_GPC['address']);
                $data['location_p'] = trim($_GPC['location_p']);
                $data['location_c'] = trim($_GPC['location_c']);
                $data['location_a'] = trim($_GPC['location_a']);
                $data['password'] = trim($_GPC['password']);
                $data['recharging_password'] = trim($_GPC['recharging_password']);
                $data['is_show'] = intval($_GPC['is_show']);
                $data['place'] = trim($_GPC['place']);
                $data['hours'] = trim($_GPC['hours']);
                $data['lng'] = trim($_GPC['lng']);
                $data['lat'] = trim($_GPC['lat']);
                $data['enable_wifi'] = intval($_GPC['enable_wifi']);
                $data['enable_card'] = intval($_GPC['enable_card']);
                $data['enable_room'] = intval($_GPC['enable_room']);
                $data['enable_park'] = intval($_GPC['enable_park']);
                $data['is_meal'] = intval($_GPC['is_meal']);
                $data['is_delivery'] = intval($_GPC['is_delivery']);
                $data['is_sms'] = intval($_GPC['is_sms']);
                $data['sendingprice'] = trim($_GPC['sendingprice']);
                $data['dispatchprice'] = trim($_GPC['dispatchprice']);
                $data['updatetime'] = TIMESTAMP;
                $data['dateline'] = TIMESTAMP;

                if (istrlen($data['title']) == 0) {
                    message('没有输入标题.', '', 'error');
                }
                if (istrlen($data['title']) > 30) {
                    message('标题不能多于30个字。', '', 'error');
                }
//                if (istrlen($data['content']) == 0) {
//                    message('没有输入内容.', '', 'error');
//                }
//                if (istrlen(trim($data['content'])) > 1000) {
//                    message('内容过多请重新输入.', '', 'error');
//                }
                if (istrlen($data['tel']) == 0) {
                    message('没有输入联系电话.', '', 'error');
                }
                if (istrlen($data['address']) == 0) {
                    //message('请输入地址。', '', 'error');
                }

                if (is_array($_GPC['thumbs'])) {
                    $data['thumb_url'] = serialize($_GPC['thumbs']);
                }

                if (!empty($reply)) {
                    unset($data['dateline']);
                    pdo_update($this->table_stores, $data, array('id' => $id, 'weid' => $_W['uniacid']));
                } else {
                    pdo_insert($this->table_stores, $data);
                }
                message('操作成功!', $url);
            }
            include $this->template('stores');
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $store = pdo_fetch("SELECT id FROM " . tablename($this->table_stores) . " WHERE id = '$id'");
            if (empty($store)) {
                message('抱歉，不存在或是已经被删除！', $this->createWebUrl('stores', array('op' => 'display')), 'error');
            }
            pdo_delete($this->table_stores, array('id' => $id, 'weid' => $_W['uniacid']));
            message('删除成功！', $this->createWebUrl('stores', array('op' => 'display')), 'success');
        }
    }

    public function doWebArea()
    {
        global $_GPC, $_W;
        checklogin();

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_area, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('区域排序更新成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
            }
            $children = array();
            $area = pdo_fetchall("SELECT * FROM " . tablename($this->table_area) . " WHERE weid = '{$_W['uniacid']}'  ORDER BY parentid ASC, displayorder DESC");
            foreach ($area as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($area[$index]);
                }
            }
            include $this->template('area');
        } elseif ($operation == 'post') {
            $parentid = intval($_GPC['parentid']);
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $area = pdo_fetch("SELECT * FROM " . tablename($this->table_area) . " WHERE id = '$id'");
            } else {
                $area = array(
                    'displayorder' => 0,
                );
            }

            if (checksubmit('submit')) {
                if (empty($_GPC['catename'])) {
                    message('抱歉，请输入区域名称！');
                }

                $data = array(
                    'weid' => $_W['uniacid'],
                    'name' => $_GPC['catename'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'parentid' => intval($parentid),
                );


                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update($this->table_area, $data, array('id' => $id));
                } else {
                    pdo_insert($this->table_area, $data);
                    $id = pdo_insertid();
                }
                message('更新区域成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
            }
            include $this->template('area');

        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $area = pdo_fetch("SELECT id, parentid FROM " . tablename($this->table_area) . " WHERE id = '$id'");
            if (empty($area)) {
                message('抱歉，区域不存在或是已经被删除！', $this->createWebUrl('area', array('op' => 'display')), 'error');
            }
            pdo_delete($this->table_area, array('id' => $id, 'parentid' => $id), 'OR');
            message('区域删除成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
        }

    }

    public function doWebCategory()
    {
        global $_GPC, $_W;
        checklogin();
        $action = 'category';
        $title = $this->actions_titles[$action];
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $storeid = intval($_GPC['storeid']);

        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_category, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
            $children = array();
            $category = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid = '{$_W['uniacid']}'  AND storeid ={$storeid} ORDER BY parentid ASC, displayorder DESC");
            foreach ($category as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($category[$index]);
                }
            }
            include $this->template('category');
        } elseif ($operation == 'post') {
            $parentid = intval($_GPC['parentid']);
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $category = pdo_fetch("SELECT * FROM " . tablename($this->table_category) . " WHERE id = '$id'");
            } else {
                $category = array(
                    'displayorder' => 0,
                );
            }

            if (!empty($parentid)) {
                $parent = pdo_fetch("SELECT id, name FROM " . tablename($this->table_category) . " WHERE id = '$parentid'");
                if (empty($parent)) {
                    message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
                }
            }
            if (checksubmit('submit')) {
                if (empty($_GPC['catename'])) {
                    message('抱歉，请输入分类名称！');
                }

                $data = array(
                    'weid' => $_W['uniacid'],
                    'storeid' => $_GPC['storeid'],
                    'name' => $_GPC['catename'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'parentid' => intval($parentid),
                );

                if (empty($data['storeid'])) {
                    message('非法参数');
                }

                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update($this->table_category, $data, array('id' => $id));
                } else {
                    pdo_insert($this->table_category, $data);
                    $id = pdo_insertid();
                }
                message('更新分类成功！', $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
            include $this->template('category');

        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $category = pdo_fetch("SELECT id, parentid FROM " . tablename($this->table_category) . " WHERE id = '$id'");
            if (empty($category)) {
                message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid)), 'error');
            }
            pdo_delete($this->table_category, array('id' => $id, 'parentid' => $id), 'OR');
            message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid)), 'success');
        } elseif ($operation == 'deleteall') {
            $rowcount = 0;
            $notrowcount = 0;
            foreach ($_GPC['idArr'] as $k => $id) {
                $id = intval($id);
                if (!empty($id)) {
                    $category = pdo_fetch("SELECT * FROM " . tablename($this->table_category) . " WHERE id = :id", array(':id' => $id));
                    if (empty($category)) {
                        $notrowcount++;
                        continue;
                    }
                    pdo_delete($this->table_category, array('id' => $id, 'weid' => $_W['uniacid']));
                    $rowcount++;
                }
            }
            $this->message("操作成功！共删除{$rowcount}条数据,{$notrowcount}条数据不能删除!!", '', 0);
        }
    }

    //菜品
    public function doWebGoods()
    {
        global $_GPC, $_W;
        checklogin();
        $action = 'goods';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);

        if (empty($storeid)) {
            message('请选择门店!');
        }

        $category = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid = '{$_W['uniacid']}' And storeid={$storeid} ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        if (!empty($category)) {
            $children = '';
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        }

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'post') {
            load()->func('tpl');
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，菜品不存在或是已经删除！', '', 'error');
                } else {
                    if (!empty($item['thumb_url'])) {
                        $item['thumbArr'] = explode('|', $item['thumb_url']);
                    }
                }
            }
            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($_W['uniacid']),
                    'storeid' => intval($_GPC['storeid']),
                    'title' => trim($_GPC['goodsname']),
                    'pcate' => intval($_GPC['pcate']),
                    'ccate' => intval($_GPC['ccate']),
                    'thumb' => trim($_GPC['thumb']),
                    'credit' => intval($_GPC['credit']),
                    'unitname' => trim($_GPC['unitname']),
                    'description' => trim($_GPC['description']),
                    'taste' => trim($_GPC['taste']),
                    'isspecial' => intval($_GPC['isspecial']),
                    'marketprice' => trim($_GPC['marketprice']),
                    'productprice' => trim($_GPC['productprice']),
                    'subcount' => intval($_GPC['subcount']),
                    'status' => intval($_GPC['status']),
                    'recommend' => intval($_GPC['recommend']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'dateline' => TIMESTAMP,
                );

                if (empty($data['title'])) {
                    message('请输入菜品名称！');
                }
                if (empty($data['pcate'])) {
                    message('请选择菜品分类！');
                }

                if (!empty($_FILES['thumb']['tmp_name'])) {
                    load()->func('file');
                    file_delete($_GPC['thumb_old']);
                    $upload = file_upload($_FILES['thumb']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['thumb'] = $upload['path'];
                }
                if (empty($id)) {
                    pdo_insert($this->table_goods, $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->table_goods, $data, array('id' => $id));
                }
                message('菜品更新成功！', $this->createWebUrl('goods', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } elseif ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_goods, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $this->createWebUrl('goods', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 8;
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }

            if (!empty($_GPC['category_id'])) {
                $cid = intval($_GPC['category_id']);
                $condition .= " AND pcate = '{$cid}'";
            }

            if (isset($_GPC['status'])) {
                $condition .= " AND status = '" . intval($_GPC['status']) . "'";
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = '{$_W['uniacid']}' AND storeid ={$storeid} $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_goods) . " WHERE weid = '{$_W['uniacid']}' AND storeid ={$storeid} $condition");

            $pager = pagination($total, $pindex, $psize);
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id, thumb FROM " . tablename($this->table_goods) . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，菜品 不存在或是已经被删除！');
            }
            if (!empty($row['thumb'])) {
                load()->func('file');
                file_delete($row['thumb']);
            }
            pdo_delete($this->table_goods, array('id' => $id));
            message('删除成功！', referer(), 'success');
        } elseif ($operation == 'deleteall') {
            $rowcount = 0;
            $notrowcount = 0;
            foreach ($_GPC['idArr'] as $k => $id) {
                $id = intval($id);
                if (!empty($id)) {
                    $goods = pdo_fetch("SELECT * FROM " . tablename($this->table_goods) . " WHERE id = :id", array(':id' => $id));
                    if (empty($goods)) {
                        $notrowcount++;
                        continue;
                    }
                    pdo_delete($this->table_goods, array('id' => $id, 'weid' => $_W['uniacid']));
                    $rowcount++;
                    //$goods['thumb'];
                }
            }
            $this->message("操作成功！共删除{$rowcount}条数据,{$notrowcount}条数据不能删除!", '', 0);
        }
        include $this->template('goods');
    }

    //智能选菜
    public function doWebIntelligent()
    {
        global $_W, $_GPC;
        checklogin();
        $action = 'intelligent';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_intelligent, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('分类排序更新成功！', $this->createWebUrl('intelligent', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
            $children = array();
            $intelligents = pdo_fetchall("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE weid = '{$_W['uniacid']}'  AND storeid ={$storeid} ORDER BY displayorder DESC");

            $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = '{$_W['uniacid']}'  AND storeid ={$storeid} ORDER BY displayorder DESC");
            $goods_arr = array();
            foreach ($goods as $key => $value) {
                $goods_arr[$value['id']] = $value['title'];
            }
            include $this->template('intelligent');
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $intelligent = pdo_fetch("SELECT * FROM " . tablename($this->table_intelligent) . " WHERE id = '$id'");
                if (!empty($intelligent)) {
                    $goodsids = explode(',', $intelligent['content']);
                }
            } else {
                $intelligent = array(
                    'displayorder' => 0,
                );
            }

            $categorys = pdo_fetchall("SELECT * FROM " . tablename($this->table_category) . " WHERE weid = '{$_W['uniacid']}'  AND storeid ={$storeid} ORDER BY displayorder DESC");
            $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE weid = '{$_W['uniacid']}'  AND storeid ={$storeid} ORDER BY displayorder DESC");
            $goods_arr = array();
            foreach ($goods as $key => $value) {
                foreach ($categorys as $key2 => $value2) {
                    if ($value['pcate'] == $value2['id']) {
                        $goods_arr[$value['pcate']][] = array('id' => $value['id'], 'title' => $value['title']);
                    }
                }
            }

            if (checksubmit('submit')) {
                if (empty($_GPC['catename'])) {
                    message('抱歉，请输入分类名称！');
                }

                $data = array(
                    'weid' => $_W['uniacid'],
                    'storeid' => $_GPC['storeid'],
                    'name' => intval($_GPC['catename']),
                    'content' => trim(implode(',', $_GPC['goodsids'])),
                    'displayorder' => intval($_GPC['displayorder']),
                );

                if ($data['name'] <= 0) {
                    message('人数必须大于0!');
                }

                if (empty($data['storeid'])) {
                    message('非法参数');
                }

                if (!empty($id)) {
                    pdo_update($this->table_intelligent, $data, array('id' => $id));
                } else {
                    pdo_insert($this->table_intelligent, $data);
                    $id = pdo_insertid();
                }
                message('更新分类成功！', $this->createWebUrl('intelligent', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
            include $this->template('intelligent');

        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $category = pdo_fetch("SELECT id FROM " . tablename($this->table_intelligent) . " WHERE id = '$id'");
            if (empty($category)) {
                message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('intelligent', array('op' => 'display', 'storeid' => $storeid)), 'error');
            }
            pdo_delete($this->table_intelligent, array('id' => $id), 'OR');
            message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }
    }

    public function doWebBlacklist()
    {
        global $_W, $_GPC;
        checklogin();
        load()->model('mc');
        $weid = $_W['uniacid'];

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;

            $list = pdo_fetchall("SELECT c.id as id,a.nickname as nickname,a.realname as realname,a.mobile as mobile,c.dateline as dateline,c.from_user as from_user,c.status as status FROM " . tablename('mc_members') . " a INNER JOIN " . tablename('mc_mapping_fans') . " b ON a.uid=b.uid INNER JOIN " . tablename('weisrc_dish_blacklist') . " c ON b.openid=c.from_user WHERE c.weid=:weid ORDER BY c.dateline DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $_W['uniacid']));

            if (!empty($list)) {
                $total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->table_blacklist) . "  WHERE weid=:weid", array(':weid' => $_W['uniacid']));
            }

            //ims_mc_members
            //ims_mc_mapping_fans
            //SELECT * FROM ims_mc_members a INNER JOIN ims_mc_mapping_fans b ON a.uid=b.uid WHERE b.openid IN (SELECT from_user FROM ims_weisrc_dish_blacklist)

            $pager = pagination($total, $pindex, $psize);
        } else if ($operation == 'black') {
            $id = $_GPC['id'];

            //pdo_query("UPDATE " . tablename($this->table_blacklist) . " SET status = abs(status - 1) WHERE id=:id AND weid=:weid", array(':id' => $id, ':weid' => $_W['uniacid']));

            pdo_delete($this->table_blacklist, array('id' => $id, 'weid' => $weid));

            message('操作成功！', $this->createWebUrl('blacklist', array('op' => 'display')), 'success');
        }

        include $this->template('blacklist');
    }

    public function doWebOrder()
    {
        global $_W, $_GPC;
        checklogin();
        load()->func('tpl');
        $action = 'order';
        $title = $this->actions_titles[$action];
        $storeid = intval($_GPC['storeid']);
//        if (empty($storeid)) {
//            message('请先选择门店!');
//        }

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {

            //$condition = " weid = '{$_W['uniacid']}' AND storeid={$storeid} ";

            $commoncondition = " weid = '{$_W['uniacid']}' ";
            if ($storeid != 0) {
                $commoncondition .= " AND storeid={$storeid} ";
            }

            $commonconditioncount = " weid = '{$_W['uniacid']}' ";
            if ($storeid != 0) {
                $commonconditioncount .= " AND storeid={$storeid} ";
            }

            if (!empty($_GPC['time'])) {
                $starttime = strtotime($_GPC['time']['start']);
                $endtime = strtotime($_GPC['time']['end']) + 86399;
                $commoncondition .= " AND dateline >= :starttime AND dateline <= :endtime ";
                $paras[':starttime'] = $starttime;
                $paras[':endtime'] = $endtime;
            }

            if (empty($starttime) || empty($endtime)) {
                $starttime = strtotime('-1 month');
                $endtime = time();
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;

            if (!empty($_GPC['ordersn'])) {
                $commoncondition .= " AND ordersn LIKE '%{$_GPC['ordersn']}%' ";
            }

            if (!empty($_GPC['tel'])) {
                $commoncondition .= " AND tel LIKE '%{$_GPC['tel']}%' ";
            }

            if (!empty($_GPC['username'])) {
                $commoncondition .= " AND username LIKE '%{$_GPC['username']}%' ";
            }

            if (isset($_GPC['status']) && $_GPC['status'] != 0) {
                $commoncondition .= " AND status = '" . intval($_GPC['status']) . "'";
            }

            if ($_GPC['out_put'] == 'output') {
                $sql = "select * from " . tablename($this->table_order)
                    . " WHERE $commoncondition ORDER BY status DESC, dateline DESC ";
                $list = pdo_fetchall($sql, $paras);
                $orderstatus = array(
                    '-1' => array('css' => 'default', 'name' => '已取消'),
                    '0' => array('css' => 'danger', 'name' => '待付款'),
                    '1' => array('css' => 'info', 'name' => '已确认'),
                    '2' => array('css' => 'warning', 'name' => '已付款'),
                    '3' => array('css' => 'success', 'name' => '已完成')
                );

                $i = 0;
                foreach ($list as $key => $value) {
                    $arr[$i]['ordersn'] = $value['ordersn'];
                    $arr[$i]['status'] = $orderstatus[$value['status']]['name'];
                    $arr[$i]['totalprice'] = $value['totalprice'];
                    $arr[$i]['username'] = $value['username'];
                    $arr[$i]['tel'] = $value['tel'];
                    $arr[$i]['address'] = $value['address'];
                    $arr[$i]['dateline'] = date('Y-m-d H:i:s', $value['dateline']);

                    $i++;
                }

                $this->exportexcel($arr, array('订单号', '状态', '总价', '真实姓名', '电话号码', '地址', '时间'), time());
                exit();
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_order) . " WHERE $commoncondition ORDER BY id desc, dateline DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $paras);

            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . " WHERE $commoncondition", $paras);

            $pager = pagination($total, $pindex, $psize);

            if (!empty($list)) {
                foreach ($list as $row) {
                    $userids[$row['from_user']] = $row['from_user'];
                }
            }

            $order_count_all = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . "  WHERE {$commonconditioncount} ");
            $order_count_confirm = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . "  WHERE {$commonconditioncount} AND status=1");
            $order_count_pay = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . "  WHERE {$commonconditioncount} AND status=2");
            $order_count_finish = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . "  WHERE {$commonconditioncount} AND status=3");
            $order_count_cancel = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->table_order) . "  WHERE {$commonconditioncount} AND status=-1");

            $users = fans_search($userids, array('realname', 'resideprovince', 'residecity', 'residedist', 'address', 'mobile', 'qq'));

            //打印数量
            $print_order_count = pdo_fetchall("SELECT orderid,COUNT(1) as count FROM ".tablename($this->table_print_order)."  GROUP BY orderid,weid having weid = :weid", array(':weid' => $_W['weid']), 'orderid');

            //黑名单
            $blacklist = pdo_fetchall("SELECT * FROM ".tablename($this->table_blacklist)." WHERE weid = :weid", array(':weid' => $_W['weid']), 'from_user');

            //门店列表
            $storelist = pdo_fetchall("SELECT * FROM ".tablename($this->table_stores)." WHERE weid = :weid", array(':weid' => $_W['weid']), 'id');

        } elseif ($operation == 'detail') {
            //流程 第一步确认付款 第二步确认订单 第三步，完成订单
            $id = intval($_GPC['id']);
            $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id LIMIT 1", array(':id' => $id));

            if (checksubmit('confrimsign')) {
                pdo_update($this->table_order, array('remark' => $_GPC['remark'], 'sign' => $_GPC['sign'], 'reply' => $_GPC['reply']), array('id' => $id));
                message('操作成功！', referer(), 'success');
            }
            if (checksubmit('finish')) {
                //isfinish
                if ($order['isfinish'] == 0) {
                    //计算积分
                    $this->setOrderCredit($order['id']);
                    pdo_update($this->table_order, array('isfinish' => 1), array('id' => $id));
                }
                pdo_update($this->table_order, array('status' => 3, 'remark' => $_GPC['remark']), array('id' => $id));
                message('订单操作成功！', referer(), 'success');
            }
            if (checksubmit('cancel')) {
                pdo_update($this->table_order, array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
                message('取消完成订单操作成功！', referer(), 'success');
            }
            if (checksubmit('confirm')) {
                pdo_update($this->table_order, array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
                message('确认订单操作成功！', referer(), 'success');
            }
            if (checksubmit('cancelpay')) {
                pdo_update($this->table_order, array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
                message('取消订单付款操作成功！', referer(), 'success');
            }
            if (checksubmit('confrimpay')) {
                pdo_update($this->table_order, array('status' => 2, 'remark' => $_GPC['remark']), array('id' => $id));
                message('确认订单付款操作成功！', referer(), 'success');
            }
            if (checksubmit('close')) {
                pdo_update($this->table_order, array('status' => -1, 'remark' => $_GPC['remark']), array('id' => $id));
                message('订单关闭操作成功！', referer(), 'success');
            }
            if (checksubmit('open')) {
                pdo_update($this->table_order, array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
                message('开启订单操作成功！', referer(), 'success');
            }

            $item = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id = :id", array(':id' => $id));

            $item['user'] = fans_search($item['from_user'], array('realname', 'resideprovince', 'residecity', 'residedist', 'address', 'mobile', 'qq'));

            $goodsid = pdo_fetchall("SELECT goodsid, total FROM " . tablename($this->table_order_goods) . " WHERE orderid = '{$item['id']}'", array(), 'goodsid');

            $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . "  WHERE id IN ('" . implode("','", array_keys($goodsid)) . "')");
            $item['goods'] = $goods;
        } else if ($operation == 'delete') {
            $id = $_GPC['id'];
            pdo_delete($this->table_order, array('id' => $id));
            pdo_delete($this->table_order_goods, array('orderid' => $id));
            message('删除成功！', $this->createWebUrl('order', array('op' => 'display', 'storeid' => $storeid)), 'success');
        } else if ($operation == 'print') {
            $id = $_GPC['id'];//订单id
            $flag = false;

            $prints = pdo_fetchall("SELECT * FROM " . tablename($this->table_print_setting) . " WHERE weid = :weid AND storeid=:storeid", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));

            if (empty($prints)) {
                message('请先添加打印机或者开启打印机！');
            }

            foreach($prints as $key => $value) {
                if ($value['print_status'] == 1) {
                    $data = array(
                        'weid' => $_W['uniacid'],
                        'orderid' => $id,
                        'print_usr' => $value['print_usr'],
                        'print_status' => -1,
                        'dateline' => TIMESTAMP
                    );
                    pdo_insert('weisrc_dish_print_order', $data);
                }
            }
            message('操作成功！', $this->createWebUrl('order', array('op' => 'display', 'storeid' => $storeid)), 'success');
        } else if ($operation == 'black') {
            $id = $_GPC['id'];//订单id
            $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id AND weid=:weid  LIMIT 1", array(':id' => $id, ':weid' => $_W['uniacid']));

            if (empty($order)) {
                message('数据不存在!');
            }

            $data = array(
                'weid' => $_W['uniacid'],
                'from_user' => $order['from_user'],
                'status' => 0,
                'dateline' => TIMESTAMP
            );

            $blacker = pdo_fetch("SELECT * FROM " . tablename($this->table_blacklist) . " WHERE from_user=:from_user AND weid=:weid  LIMIT 1", array(':from_user' => $order['from_user'], ':weid' => $_W['uniacid']));

            if (!empty($blacker)) {
                message('该用户已经在黑名单中!', $this->createWebUrl('order', array('op' => 'display', 'storeid' => $storeid)));
            }

            pdo_insert('weisrc_dish_blacklist', $data);
            message('操作成功！', $this->createWebUrl('order', array('op' => 'display', 'storeid' => $storeid)), 'success');
        }
        include $this->template('order');
    }

    protected function exportexcel($data = array(), $title = array(), $filename = 'report')
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        //导出xls 开始
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv("UTF-8", "GB2312", $v);
            }
            $title = implode("\t", $title);
            echo "$title\n";
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
                }
                $data[$key] = implode("\t", $data[$key]);

            }
            echo implode("\n", $data);
        }
    }

    //设置订单积分
    public function setOrderCredit($orderid, $add = true) {
        $order = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE id=:id LIMIT 1", array(':id' => $orderid));
        if (empty($order)) {
            return false;
        }

        $ordergoods = pdo_fetchall("SELECT goodsid, total FROM " . tablename($this->table_order_goods) . " WHERE orderid = '{$orderid}'", array(), 'goodsid');
        if (!empty($ordergoods)) {
            $goods = pdo_fetchall("SELECT * FROM " . tablename($this->table_goods) . " WHERE id IN ('" . implode("','", array_keys($ordergoods)) . "')");
        }

        //增加积分
        if (!empty($goods)) {
            $credits = 0;
            foreach ($goods as $g) {
                $credits += $g['credit'];
            }
            load()->model('mc');
            load()->func('compat.biz');
            $uid = mc_openid2uid($order['from_user']);
            $fans = fans_search($uid, array("credit1"));
            if (!empty($fans)) {
//                if ($add) {
//                    $new_credit = $credits + $fans['credit1'];
//                } else {
//                    $new_credit = $fans['credit1'] - $credits;
//                    if ($new_credit <= 0) {
//                        $new_credit = 0;
//                    }
//                }
                $uid = intval($fans['uid']);
                $remark = $add == true ? '微点餐积分奖励 订单ID:' . $orderid : '微点餐积分扣除 订单ID:' . $orderid;
                $log = array();
                $log[0] = $uid;
                $log[1] = $remark;
                mc_credit_update($uid, 'credit1', $credits, $log);
                //pdo_update('mc_members', array("credit1" => $new_credit), array('uid' => $uid));
            }
        }
        return true;
    }

    /*
    ** 设置切换导航
    */
    public function set_tabbar($action, $storeid)
    {
        $actions_titles = $this->actions_titles;
        $html = '<ul class="nav nav-tabs">';
        foreach ($actions_titles as $key => $value) {
            $url = $this->createWebUrl($key, array('op' => 'display', 'storeid' => $storeid));
            $html .= '<li class="' . ($key == $action ? 'active' : '') . '"><a href="' . $url . '">' . $value . '</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }

    //入口设置
    public function doWebSetRule()
    {
        global $_W;
        $rule = pdo_fetch("SELECT id FROM " . tablename('rule') . " WHERE module = 'weisrc_dish' AND weid = '{$_W['uniacid']}' order by id desc");
        if (empty($rule)) {
            header('Location: ' . $_W['siteroot'] . create_url('rule/post', array('module' => 'weisrc_dish', 'name' => '微点餐')));
            exit;
        } else {
            header('Location: ' . $_W['siteroot'] . create_url('rule/post', array('module' => 'weisrc_dish', 'id' => $rule['id'])));
            exit;
        }
    }

    function uploadFile($file, $filetempname, $array)
    {
        //自己设置的上传文件存放路径
        $filePath = '../addons/weisrc_dish/upload/';

        //require_once '../addons/weisrc_dish/plugin/phpexcelreader/reader.php';
        include 'plugin/phpexcelreader/reader.php';

        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('utf-8');

        //$filepath = './source/modules/iteamlotteryv2/data_' . $weid . '.xls';
        //$tmp = $_FILES['fileexcel']['tmp_name'];

        //注意设置时区
        $time = date("y-m-d-H-i-s"); //去当前上传的时间
        $extend = strrchr($file, '.');
        //上传后的文件名
        $name = $time . $extend;
        $uploadfile = $filePath . $name; //上传后的文件名地址

        //$filetype = $_FILES['fileexcel']['type'];

        if (copy($filetempname, $uploadfile)) {
            if (!file_exists($filePath)) {
                echo '文件路径不存在.';
                return;
            }
            if (!is_readable($uploadfile)) {
                echo ("文件为只读,请修改文件相关权限.");
                return;
            }
            $data->read($uploadfile);
            error_reporting(E_ALL ^ E_NOTICE);
            $count = 0;
            for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) { //$=2 第二行开始
                //以下注释的for循环打印excel表数据
                for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
                    //echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
                }

                $row = $data->sheets[0]['cells'][$i];
                //message($data->sheets[0]['cells'][$i][1]);

                if ($array['ac'] == "category") {
                    $count = $count + $this->upload_category($row, TIMESTAMP, $array);
                } else if ($array['ac'] == "goods") {
                    $count = $count + $this->upload_goods($row, TIMESTAMP, $array);
                } else if ($array['ac'] == "store") {
                    $count = $count + $this->upload_store($row, TIMESTAMP, $array);
                }
            }
        }
        if ($count == 0) {
            $msg = "导入失败！";
        } else {
            $msg = 1;
        }

        return $msg;
    }

    private function checkUploadFileMIME($file)
    {
        // 1.through the file extension judgement 03 or 07
        $flag = 0;
        $file_array = explode(".", $file ["name"]);
        $file_extension = strtolower(array_pop($file_array));

        // 2.through the binary content to detect the file
        switch ($file_extension) {
            case "xls" :
                // 2003 excel
                $fh = fopen($file ["tmp_name"], "rb");
                $bin = fread($fh, 8);
                fclose($fh);
                $strinfo = @unpack("C8chars", $bin);
                $typecode = "";
                foreach ($strinfo as $num) {
                    $typecode .= dechex($num);
                }
                if ($typecode == "d0cf11e0a1b11ae1") {
                    $flag = 1;
                }
                break;
            case "xlsx" :
                // 2007 excel
                $fh = fopen($file ["tmp_name"], "rb");
                $bin = fread($fh, 4);
                fclose($fh);
                $strinfo = @unpack("C4chars", $bin);
                $typecode = "";
                foreach ($strinfo as $num) {
                    $typecode .= dechex($num);
                }
                echo $typecode . 'test';
                if ($typecode == "504b34") {
                    $flag = 1;
                }
                break;
        }

        // 3.return the flag
        return $flag;
    }

    function upload_goods($strs, $time, $array)
    {
        global $_W;
        $insert = array();

        //类别处理
        $category = pdo_fetch("SELECT id FROM " . tablename('weisrc_dish_category') . " WHERE name=:name AND weid=:weid AND storeid=:storeid", array(':name' => $strs[2], ':weid' => $_W['uniacid'], ':storeid' => $array['storeid']));
        $insert['pcate'] = empty($category) ? 0 : intval($category['id']);
        $insert['title'] = $strs[1];
        $insert['thumb'] = $strs[3];
        $insert['unitname'] = $strs[4];
        $insert['description'] = $strs[5];
        $insert['taste'] = $strs[6];
        $insert['isspecial'] = $strs[7];
        $insert['marketprice'] = $strs[8];
        $insert['productprice'] = $strs[9];
        $insert['subcount'] = $strs[10];
        $insert['credit'] = $strs[11];
        $insert['dateline'] = TIMESTAMP;
        $insert['status'] = 1;
        $insert['recommend'] = 0;
        $insert['ccate'] = 0;
        $insert['storeid'] = $array['storeid'];
        $insert['weid'] = $_W['uniacid'];

        $goods = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_goods') . " WHERE title=:title AND weid=:weid AND storeid=:storeid", array(':title' => $strs[1], ':weid' => $_W['uniacid'], ':storeid' => $array['storeid']));

        if (empty($goods)) {
            return pdo_insert('weisrc_dish_goods', $insert);
        } else {
            return 0;
        }
    }

    function upload_category($strs, $time, $array)
    {
        global $_W;
        $insert = array();
        $insert['name'] = $strs[1];
        $insert['parentid'] = 0;
        $insert['displayorder'] = 0;
        $insert['enabled'] = 1;
        $insert['storeid'] = $array['storeid'];
        $insert['weid'] = $_W['uniacid'];

        $category = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_category') . " WHERE name=:name AND weid=:weid AND storeid=:storeid", array(':name' => $strs[1], ':weid' => $_W['uniacid'], ':storeid' => $array['storeid']));

        if (empty($category)) {
            return pdo_insert('weisrc_dish_category', $insert);
        } else {
            return 0;
        }
    }

    function upload_store($strs, $time, $array)
    {
        global $_W;
        $insert = array();
        //类别处理
        $insert['weid'] = $_W['uniacid'];
        $insert['title'] = $strs[1];
        $insert['info'] = $strs[2];
        $insert['logo'] = $strs[3];
        $insert['content'] = $strs[4];
        $insert['tel'] = $strs[5];
        $insert['address'] = $strs[6];
        $insert['place'] = $strs[6];
        $insert['hours'] = $strs[7];
        $insert['location_p'] = $strs[8];
        $insert['location_c'] = $strs[9];
        $insert['location_a'] = $strs[10];
        $insert['password'] = '';
        $insert['recharging_password'] = '';
        $insert['is_show'] = 1;
        $insert['areaid'] = 0;
        $insert['lng'] = '0.000000000';
        $insert['lat'] = '0.000000000';
        $insert['enable_wifi'] = 1;
        $insert['enable_card'] = 1;
        $insert['enable_room'] = 1;
        $insert['enable_park'] = 1;
        $insert['updatetime'] = TIMESTAMP;
        $insert['dateline'] = TIMESTAMP;

        $store = pdo_fetch("SELECT * FROM " . tablename('weisrc_dish_stores') . " WHERE title=:title AND weid=:weid LIMIT 1", array(':title' => $strs[1], ':weid' => $_W['uniacid']));

        if (empty($store)) {
            return pdo_insert('weisrc_dish_stores', $insert);
        } else {
            return 0;
        }
    }

    public function doWebUploadExcel()
    {
        global $_GPC, $_W;

        if ($_GPC['leadExcel'] == "true") {
            $filename = $_FILES['inputExcel']['name'];
            $tmp_name = $_FILES['inputExcel']['tmp_name'];

            $flag = $this->checkUploadFileMIME($_FILES['inputExcel']);
            if ($flag == 0) {
                message('文件格式不对.');
            }

            if (empty($tmp_name)) {
                message('请选择要导入的Excel文件！');
            }

            $msg = $this->uploadFile($filename, $tmp_name, $_GPC);

            if ($msg == 1) {
                message('导入成功！', referer(), 'success');
            } else {
                message($msg, '', 'error');
            }
        }
    }

    public function message($error, $url = '', $errno = -1)
    {
        $data = array();
        $data['errno'] = $errno;
        if (!empty($url)) {
            $data['url'] = $url;
        }
        $data['error'] = $error;
        echo json_encode($data);
        exit;
    }
}