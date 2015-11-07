<?php
/**
 * 微商圈
 *
 *
 * 未经许可，任何盗用代码行为都属于侵权
 */
defined('IN_IA') or exit('Access Denied');
define(EARTH_RADIUS, 6371); //地球半径，平均半径为6371km
define('LOCK', 'Li9zb3VyY2UvbW9kdWxlcy9pYnVzaW5lc3NjZW50ZXIvdGVtcGxhdGUvdGhlbWVzL3ZlcnNpb24uY3Nz');
define('RES', '../addons/weisrc_businesscenter/template');
include "../addons/weisrc_businesscenter/model.php";

class weisrc_businesscenterModuleSite extends WeModuleSite
{
    //模块标识
    public $modulename = 'weisrc_businesscenter';
    public $_appid = '';
    public $_appsecret = '';
    public $_accountlevel = '';

    public $_debug = '1'; //default:0
    public $_weixin = '0'; //default:1

    public $_weid = '';
    public $_fromuser = '';
    public $_nickname = '';
    public $_headimgurl = '';

    public $_auth2_openid = '';
    public $_auth2_nickname = '';
    public $_auth2_headimgurl = '';

    function __construct()
    {
        global $_GPC, $_W;
        $this->_weid = $_W['uniacid'];
        $this->_appid = '';
        $this->_appsecret = '';
        $this->_accountlevel = $_W['account']['level']; //是否为高级号

        $this->_auth2_openid = 'auth2_openid_' . $_W['uniacid'];
        $this->_auth2_nickname = 'auth2_nickname_' . $_W['uniacid'];
        $this->_auth2_headimgurl = 'auth2_headimgurl_' . $_W['uniacid'];

        $lock_path = base64_decode(LOCK);
        if (!file_exists($lock_path)) {
            //message(base64_decode('5a+55LiN6LW377yM5oKo5L2/55So55qE5LiN5piv5q2j54mI6L2v5Lu277yM6LSt5Lmw5q2j54mI6K+36IGU57O7cXE6MTU1OTU3NTU='));
        } else {
            $file_content = file_get_contents($lock_path);
            $validation_code = $this->authorization();
            //$this->code_compare($file_content, $validation_code);
        }

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $this->_weid));
        if ($this->_accountlevel != 4) {
            if (!empty($setting)) {
                $this->_appid = $setting['appid'];
                $this->_appsecret = $setting['secret'];
            }
        } else {
            $this->_appid = $_W['account']['key'];
            $this->_appsecret = $_W['account']['secret'];
        }

        if (!empty($_W['account']['key']) && !empty($_W['account']['secret'])) {
            //require_once IA_ROOT . '/framework/class/account.class.php';
            //$acc = WeAccount::create($this->_weid);
            //$_W['account']['jssdkconfig'] = $acc->getJssdkConfig();
            //$accountInfo = $acc->fetchAccountInfo();
            //$_W['account']['access_token'] = $accountInfo['access_token'];
            //$_W['account']['jsapi_ticket'] = $accountInfo['jsapi_ticket'];
        }
    }

    //网站入口
    public function doMobileIndex()
    {
        global $_GPC, $_W;
        $weid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['weid']);
        $title = "微商圈";
        $modulename = $this->modulename;
        $this->isWeixin();
        $method = 'index';
        $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('authkey' => 1), true);
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true);
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $fromuser = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $fromuser = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->toAuthUrl($url);
                }
            }
        }

        if(!empty($_W['account']['key']) && !empty($_W['account']['secret'])){
            //require_once IA_ROOT . '/framework/class/account.class.php';
            //$acc = WeAccount::create($_W['weid']);
            //$_W['account']['jssdkconfig'] = $acc->getJssdkConfig();
            //$accountInfo = $acc->fetchAccountInfo();
            //$_W['account']['access_token'] = $accountInfo['access_token'];
            //$_W['account']['jsapi_ticket'] = $accountInfo['jsapi_ticket'];
        }

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $_W['uniacid']));
        $title = empty($setting) ? "微商圈" : $setting['title'];

        //幻灯片
        $slide = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_slide') . " WHERE weid = :weid AND storeid=0 AND status=1 ORDER BY displayorder DESC,id DESC LIMIT 6", array(':weid' => $weid));
        //商家一级分类
        $category = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE weid = :weid AND parentid=0  ORDER BY displayorder DESC", array(':weid' => $weid));
        //推荐分类
        $category_first = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE weid = :weid AND parentid<>0 AND isfirst=1 ORDER BY displayorder DESC", array(':weid' => $weid));
        //推荐商家
        $hotstores = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE weid = :weid AND status<>0 AND isfirst=1  ORDER BY displayorder DESC ", array(':weid' => $weid));

        //#share
        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $weid));

        //#share
        if (strpos($setting['share_image'], 'http') === false) {
            $share_image = $_W['attachurl'] . $setting['share_image'];
        } else {
            $share_image = $setting['share_image'];
        }

        $share_title = empty($setting['share_title']) ? $setting['title'] : $setting['share_title'];
        $share_desc = empty($setting['share_desc']) ? $setting['title'] : $setting['share_desc'];
        $share_url = empty($setting['share_url']) ? $_W['siteroot'] . 'app/' . $this->createMobileUrl('index', array(), true) : $setting['share_url'];

        include $this->template('index');
    }

    public function doMobileList()
    {
        global $_GPC, $_W;
        $weid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['weid']);
        $title = "微商圈";
        $modulename = $this->modulename;
        $cid = intval($_GPC['cid']);
        $no_more_data = 0;
        $condition_store = '';
        $this->isWeixin();

        //BEGIN
        $method = 'list';
        $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('cid' => $cid, 'authkey' => 1), true);
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('cid' => $cid), true);

        //END
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $fromuser = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $fromuser = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->toAuthUrl($url);
                }
            }
        }

        $level_star = array(
            '1' => '★',
            '2' => '★★',
            '3' => '★★★',
            '4' => '★★★★',
            '5' => '★★★★★'
        );

        if (empty($cid)) {
            //全部类别
            $categorys = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE 1=1 AND parentid=0 AND weid=:weid ORDER BY displayorder DESC", array(':weid' => $_W['uniacid']));
        } else {
            //按类别查询
            $category = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE 1=1 AND id={$cid} AND weid=:weid", array(':weid' => $_W['uniacid']));
            //属于父级
            if (empty($category['parentid'])) {
                $categorys = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE 1=1 AND (parentid={$category['id']} OR id={$category['id']}) AND weid=:weid ORDER BY parentid,displayorder DESC", array(':weid' => $_W['uniacid']), 'id');
                $categoryids = implode("','", array_keys($categorys));
                $condition_store .= " AND pcate={$cid} ";
            } else {
                //子级
                $categorys = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE 1=1 AND (parentid={$category['parentid']} OR id={$category['parentid']}) AND weid=:weid ORDER BY parentid,displayorder DESC", array(':weid' => $_W['uniacid']), 'id');
                $condition_store .= " AND ccate = {$category['id']} ";
            }
        }

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $weid));
        //#share
        if (strpos($setting['share_image'], 'http') === false) {
            $share_image = $_W['attachurl'] . $setting['share_image'];
        } else {
            $share_image = $setting['share_image'];
        }
        $share_title = empty($setting['share_title']) ? $setting['title'] : $setting['share_title'];
        $share_desc = empty($setting['share_desc']) ? $setting['title'] : $setting['share_desc'];
        $share_url = empty($setting['share_url']) ? $_W['siteroot'] . $this->createMobileUrl('settled') : $setting['share_url'];

        $pindex = max(1, intval($_GPC['page']));
        $psize = empty($setting) ? 5 : intval($setting['pagesize']);

        //商家列表 //搜索处理
        $keyword = trim($_GPC['keyword']);

        $order_condition = " ORDER BY top DESC,displayorder DESC,status DESC,id DESC ";

        if (!empty($keyword)) {
            $condition_store = " AND (title like '%{$keyword}%' OR address like '%{$keyword}%' ) ";
            $stores = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} {$order_condition}", array(':weid' => $weid));
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_stores') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} ", array(':weid' => $weid));
        } else {
            $stores = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} {$order_condition} LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid));
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_stores') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} ", array(':weid' => $weid));

            if ($total <= $psize) {
                $no_more_data = 1;
            }
        }
        include $this->template('list');
    }

    public function doMobileNews()
    {
        global $_GPC, $_W;
        $weid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['weid']);
        $title = "微商圈";
        $modulename = $this->modulename;
        $cid = intval($_GPC['cid']);
        $no_more_data = 0;
        $condition_store = '';

        $this->isWeixin();

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $weid));
        //#share
        if (strpos($setting['share_image'], 'http') === false) {
            $share_image = $_W['attachurl'] . $setting['share_image'];
        } else {
            $share_image = $setting['share_image'];
        }
        $share_title = empty($setting['share_title']) ? $setting['title'] : $setting['share_title'];
        $share_desc = empty($setting['share_desc']) ? $setting['title'] : $setting['share_desc'];
        $share_url = empty($setting['share_url']) ? $_W['siteroot'] . $this->createMobileUrl('settled') : $setting['share_url'];

        $pindex = max(1, intval($_GPC['page']));
        $psize = empty($setting) ? 5 : intval($setting['pagesize']);

        //商家列表 //搜索处理
        $keyword = trim($_GPC['keyword']);
        $order_condition = " ORDER BY top DESC,displayorder DESC,status DESC,id DESC ";

        $stores = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE weid = :weid AND status<>0 AND checked=1 ", array(':weid' => $weid), 'id');

        if (!empty($keyword)) {
            $condition_store = " AND (title like '%{$keyword}%' OR address like '%{$keyword}%' ) ";
            $news = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_news') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} {$order_condition}", array(':weid' => $weid));

            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_news') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} ", array(':weid' => $weid));
        } else {
            $news = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_news') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} {$order_condition} LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid));
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_news') . " WHERE weid = :weid AND status<>0 AND checked=1 {$condition_store} ", array(':weid' => $weid));
            if ($total <= $psize) {
                $no_more_data = 1;
            }
        }
        include $this->template('news');
    }

    public function doMobileNewsDetail()
    {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        if ($id == 0) {
            message('信息不存在!');
        }

        $this->isWeixin();

        $news = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_news') . " WHERE weid = :weid AND id=:id LIMIT 1", array(':weid' => $_W['uniacid'], ':id' => $id));

        if (empty($news)) {
            message('信息不存在!!');
        }

        if (!empty($news)) {
            if (!empty($news['thumb'])) {
                if (strpos($news['thumb'], 'http') === false) {
                    $thumb = $_W['attachurl'] . $news['thumb'];
                } else {
                    $thumb = $news['thumb'];
                }
            }
        }

        include $this->template('news_detail');
    }

    public function doMobileGetMoreAll()
    {
        global $_GPC, $_W;
        $weid = $this->_weid;
        $cid = intval($_GPC['cid']); //类别

        $curlat = $_GPC['curlat'];
        $curlng = $_GPC['curlng'];

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $_W['uniacid']));

        $page = intval($_GPC['page']); //页码
        $pindex = max(1, intval($_GPC['page']));
        $psize = empty($setting) ? 5 : intval($setting['pagesize']);
        $condition = '';

        if (!empty($cid)) {
            $category = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE 1=1 AND id={$cid}");
            if ($category['parentid'] == 0) {
                //属于父级
                $condition = " AND pcate={$cid} ";
            } else {
                $condition = " AND ccate={$cid} ";
            }
        }
        //商家列表
        $stores = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE weid = :weid AND status<>0 {$condition} ORDER BY top DESC,displayorder DESC,status DESC,id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid));
        $level_star = array(
            '1' => '★',
            '2' => '★★',
            '3' => '★★★',
            '4' => '★★★★',
            '5' => '★★★★★'
        );
        $result_str = '';
        foreach ($stores as $key => $value) {
            if (strstr($value['logo'], 'http') || strstr($value['logo'], '../addons/')) {
                $logo = $value['logo'];
            } else {
                $logo = $_W['attachurl'] . $value['logo'];
            }
            $level = $level_star[$value['level']];

            if (!empty($value['discounts'])) {
                $discounts = '<section class="tn-Powered-by-XIUMI line"></section>
                    <p class="VIPzhekou"><span style="color:#50849C;font-size: 15px;">会员折扣：</span><span style="color:rgb(255, 129, 36);text-shadow: 0px -1px 0px rgba(255, 255, 255, 0.5);font-size: 13px;">' . $value['discounts'] . '</span></p>
                    <section class="tn-Powered-by-XIUMI line"></section>';
            }

            $error_img = " onerror=\"this.src='" . RES . "/themes/images/nopic.jpeg'\"";
            $result_str .= '<div class="J-wsq-shoplist">
                <a href="' . $this->createMobileurl('shop', array('id' => $value['id'])) . '" style="overflow:hidden;">
                    <img src="' . $logo . '" ' . $error_img . '>
                    <p class="tt">' . $value['title'] . '</p>
                    <p class="address">' . $value['address'] . '</p>
                    <p class="address">' . $this->getDistance($curlat, $curlng, $value['lat'], $value['lng']) . ' Km</p>
                    <p class="business"><span style="color:#50849C;font-size: 15px;">商家评级：</span><span style="color:#ff0000;">' . $level . '</span></p>
                    ' . $discounts . '
                </a>
                <p class="bar_box">
                    <a href="' . $this->createMobileurl('shop', array('id' => $value['id'])) . '"><i class="icon-login"></i>详情</a>
                    <a href="http://api.map.baidu.com/marker?location=' . $value['lat'] . ',' . $value['lng'] . '&title=' . $value['title'] . '&name=' . $value['title'] . '&content=' . $value['address'] . '&output=html&src=wzj|wzj"><i
                        class="icon-location-2"></i>导航</a><a href="tel:' . $value['tel'] . '"><i class="icon-phone-3"></i>预定</a>
                </p>';
            if ($value['top'] == 1) {
                $result_str .= '<em class="tj_b"></em>';
            }
            $result_str .= '</div>';
        }
        if ($result_str == '') {
            echo json_encode(0);
        } else {
            echo json_encode($result_str);
        }
    }

    public function doMobileLoadFeedback()
    {
        global $_GPC, $_W;
        $storeid = intval($_GPC['storeid']);
        $pindex = max(1, intval($_GPC['page']));
        //$psize = intval($_GPC['pagesize']) == 0? 10 : intval($_GPC['pagesize']);
        $psize = 10;
        $condition = " WHERE weid = " . $_W['uniacid'] . " AND storeid={$storeid} AND status=1";

        $list = pdo_fetchall("SELECT *,date_format(FROM_UNIXTIME(dateline),'%Y-%m-%d') as date FROM " . tablename($this->modulename . '_feedback') . " {$condition} ORDER BY displayorder DESC,id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_feedback') . " {$condition} ");

        $result = array(
            'data' => $list,
            'status' => 10,
            'page' => 11
        );
        if (count($list) == 0) {
            $result['status'] = 0;
        }
        if ($psize * $pindex + count($list) <= $total) {
            $result['status'] = 10;
        } else {
            $result['status'] = 1;
        }

        die(json_encode($result));
    }

    public function doMobileDebugCode()
    {
        if (isset($_GPC['code'])) {
            $userinfo = $this->getUserInfo($_GPC['code']);
            if (!empty($userinfo)) {
                echo $userinfo["nickname"] . '<br/>';
                $headimgurl = $userinfo["headimgurl"];
                echo "<img src='{$headimgurl}'/>";
                //message($userinfo["nickname"]);
            } else {
                message('调试中勿扰...');
            }
        }
    }

    //商家入驻
    public function doMobileSettled()
    {
        global $_GPC, $_W;
        load()->func('tpl');
        $weid = $this->_weid;
        $fromuser = $this->_fromuser;

        $title = "微商圈";
        $modulename = $this->modulename;

        if (isset($_COOKIE[$this->_auth2_openid])) {
            $fromuser = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            $method = 'settled';
            $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('authkey' => 1), true);
            $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array(), true);
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $fromuser = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->toAuthUrl($url);
                }
            }
        }

        //基本信息
        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $weid));
        //#share
        if (strpos($setting['share_image'], 'http') === false) {
            $share_image = $_W['attachurl'] . $setting['share_image'];
        } else {
            $share_image = $setting['share_image'];
        }
        $share_title = empty($setting['share_title']) ? $setting['title'] : $setting['share_title'];
        $share_desc = empty($setting['share_desc']) ? $setting['title'] : $setting['share_desc'];
        $share_url = empty($setting['share_url']) ? $_W['siteroot'] . $this->createMobileUrl('settled') : $setting['share_url'];

        if (!empty($setting) && $setting['settled'] == 0) {
            message("商家没有开启入驻功能！");
        }

        if (empty($fromuser)) {
            message('会话已过期，请重新发送关键字进入系统!');
        }

        //商家类别
        $children = array();
        $category = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        if (!empty($category)) {
            $children = array();
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        }

        $item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE weid = :weid AND from_user=:from_user LIMIT 1", array(':weid' => $weid, ':from_user' => $fromuser));

        if (!empty($item)) {
            $now_date = date('y-m-d', TIMESTAMP);
            //开始时间
            $start_time = strtotime($now_date . ' ' . $item['starttime']);
            $start_hour = date('H', $start_time);
            $start_second = date('i', $start_time);
            //结束时间
            $end_time = strtotime($now_date . ' ' . $item['endtime']);
            $end_hour = date('H', $end_time);
            $end_second = date('i', $end_time);
        }

        //商家提交信息
        if (checksubmit('btnsubmit')) {
            $data = array(
                'weid' => intval($_W['uniacid']),
                'title' => trim($_GPC['title']),
                'from_user' => $fromuser,
                'pcate' => intval($_GPC['category']),
                'ccate' => intval($_GPC['category_child']),
                'services' => trim($_GPC['services']),
                'username' => trim($_GPC['username']),
                'tel' => trim($_GPC['tel']),
                'address' => trim($_GPC['address']),
                'starttime' => trim($_GPC['start_hour'] . ':' . $_GPC['start_second']),
                'endtime' => trim($_GPC['end_hour'] . ':' . $_GPC['end_second']),
                'status' => 0,
                'top' => 0,
                'mode' => 1,
                'checked' => 0,
                'displayorder' => 0,
                'dateline' => TIMESTAMP,
            );

            if (empty($data['title'])) {
                message('请输入商家名称!');
            }
            if (empty($data['username'])) {
                message('请输入您的名称!');
            }
            if (empty($data['tel'])) {
                message('请输入您的联系电话!');
            }
            if (empty($data['address'])) {
                message('请输入您的联系地址!');
            }
            if (empty($data['pcate'])) {
                message('请选择商家类别!');
            }
            if (empty($data['starttime'])) {
                message('请选择营业开始时间');
            }
            if (empty($data['endtime'])) {
                message('请选择营业结束时间');
            }
            if ($data['endtime'] < $data['starttime']) {
                message('请选择正确的营业时间');
            }

            if (!empty($_FILES['fileToUpload']['tmp_name'])) {
                load()->func('file');
                file_delete($_GPC['file_old']);
                $upload = file_upload($_FILES['fileToUpload']);
                if (is_error($upload)) {
                    message($upload['message']);
                }
                $data['businesslicense'] = $upload['path'];
            } else {
                //message('营业执照必须上传!');
            }

            if (empty($item)) { //新增
                pdo_insert($this->modulename . '_stores', $data);
                message('您的申请已经成功提交，我们会尽快联系您！', $this->createMobileurl('index'), 'success');
            } else { //更新
                //pdo_update($this->modulename . '_stores', $data, array('weid' => $weid, 'from_user' => $fromuser));
                message('您已经提交过申请！', $this->createMobileurl('index'));
            }
        }
        include $this->template('settled');
    }

    public function doMobileTest()
    {
        global $_GPC, $_W;
        $weid = 18;
        $cid = intval($_GPC['cid']); //类别
        $page = intval($_GPC['page']); //页码
        $curlat = '23.368000394132';
        $curlng = '116.73467339936';

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $weid));
        $pindex = max(1, intval($_GPC['page']));
        $psize = empty($setting) ? 5 : intval($setting['pagesize']);
        $condition = '';

        if (!empty($cid)) {
            $category = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE 1=1 AND id={$cid}");
            if ($category['parentid'] == 0) {
                //属于父级
                $condition = " AND pcate={$cid} ";
            } else {
                $condition = " AND ccate={$cid} ";
            }
        }

        //商家列表
        $stores = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE weid = :weid AND status<>0 {$condition} ORDER BY top DESC,displayorder DESC,status DESC  LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid));

        $result_str = '当前lat:' . $curlat . 'lng:' . $curlng . '<br/>';
        foreach ($stores as $key => $value) {
            $result_str .= '商家:' . $value['title'] . '</br>';
            $result_str .= '商家lat:' . $value['lat'] . '商家lng:' . $value['lng'] . '</br>';
            $result_str .= '距离:' . $this->getDistance($curlat, $curlng, $value['lat'], $value['lng']) . '</br>';
        }
        if ($result_str == '') {
            echo json_encode(0);
        } else {
            echo $result_str;
        }
    }

    public function domobileversion()
    {
        message($this->curversion);
    }

    public function doMobileGetList()
    {
        global $_GPC, $_W;
        $weid = intval($_GET['weid']);
        $cid = intval($_GPC['cid']);

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $_W['uniacid']));
        $pindex = max(1, intval($_GPC['page']));
        $psize = empty($setting) ? 5 : intval($setting['pagesize']);
        $condition = '';

        $cid = intval($_GPC['cid']);
        if (empty($cid)) {
            exit;
        }

        $condition .= " AND pcate={$cid}";
        //商家列表
        $stores = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE weid = :weid AND status<>0 {$condition} ORDER BY displayorder DESC,status DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid));

        $result = array();
        foreach ($stores as $key => $value) {
            $result[] = array(
                'id' => $value['id'], 'img' => $value['logo'], 'title' => $value['title'], 'telephone' => $value['tel'], 'address' => $value['address']
            );
        }
        exit(json_encode($result));
    }

    public function toAuthUrl($url)
    {
        global $_W;
        $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->_appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_base&state=0#wechat_redirect";
        header("location:$oauth2_code");
    }

    public function doMobileShop()
    {
        global $_GPC, $_W;
        $weid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['weid']);
        $fromuser = $_W['fans']['from_user'];

        $modulename = $this->modulename;
        $title = "微商圈";
        $id = intval($_GPC['id']);

        $this->isWeixin();
        $level_star = array(
            '1' => '★',
            '2' => '★★',
            '3' => '★★★',
            '4' => '★★★★',
            '5' => '★★★★★'
        );

        //BEGIN
        $method = 'shop';
        $authurl = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('id' => $id, 'authkey' => 1), true);
        $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl($method, array('id' => $id), true);
        //END
        if (isset($_COOKIE[$this->_auth2_openid])) {
            $fromuser = $_COOKIE[$this->_auth2_openid];
            $nickname = $_COOKIE[$this->_auth2_nickname];
            $headimgurl = $_COOKIE[$this->_auth2_headimgurl];
        } else {
            if (isset($_GPC['code'])) {
                $userinfo = $this->oauth2($authurl);
                if (!empty($userinfo)) {
                    $fromuser = $userinfo["openid"];
                    $nickname = $userinfo["nickname"];
                    $headimgurl = $userinfo["headimgurl"];
                } else {
                    message('授权失败!');
                }
            } else {
                if (!empty($this->_appsecret)) {
                    $this->toAuthUrl($url);
                }
            }
        }

        if (empty($id)) {
            message('没有相关数据!');
        }

        $stores = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE weid = :weid AND status<>0 AND id=:id", array(':weid' => $weid, ':id' => $id));

        //#share
        if (strpos($stores['logo'], 'http') === false) {
            $share_image = $_W['attachurl'] . $stores['logo'];
        } else {
            $share_image = $stores['logo'];
        }

        $share_title = empty($stores['share_title']) ? $stores['title'] : $stores['share_title'];
        $share_desc = empty($stores['share_desc']) ? $stores['title'] : $stores['share_desc'];
        $share_url = empty($stores['share_url']) ? $_W['siteroot'] . $this->createMobileUrl('shop', array('id' => $stores['id'])) : $stores['share_url'];

        $pcate = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $stores['pcate']));
        $ccate = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE weid = :weid AND id=:id LIMIT 1", array(':weid' => $weid, ':id' => $stores['ccate']));

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $_W['uniacid']));

        $cityid = $stores['cityid'];
        if (!empty($cityid)) {
            $city = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_city') . " WHERE weid = :weid AND id=:id ORDER BY displayorder DESC LIMIT 1", array(':weid' => $weid, ':id' => $cityid));

            $showcity = '';
            if (!empty($setting['showcity'])) {
                $showcity = '<a href="' . $this->createMobileUrl($method, array('do' => 'citylist', 'cityid' => $cityid)) . '" style="display: inline;background:none;">' . $city['name'] . '</a>&gt;';
                $pcateurl = $this->createMobileUrl($method, array('do' => 'list', 'cityid' => $cityid, 'cid' => $pcate['id']));
            }


            $page_nave = $showcity . '<a href="' . $pcateurl . '" style="display: inline;background:none;">' . $pcate['name'] . '</a>&gt;<a href="#" style="display: inline;background:none;">' . $ccate['name'] . '</a>';
        }

        //幻灯片
        $slide = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_slide') . " WHERE weid = :weid AND storeid=:storeid AND status=1 ORDER BY displayorder DESC", array(':weid' => $weid, ':storeid' => $id));

        if (empty($nickname)) {
            if (!empty($_W['fans']['from_user'])) {
                $user = fans_search($_W['fans']['from_user']);
                $nickname = $user['nickname'];
            }
        }
        include $this->template('shop');
    }

    public function oauth2($authurl)
    {
        global $_GPC, $_W;
        load()->func('communication');
        $state = $_GPC['state']; //1为关注用户, 0为未关注用户
        $code = $_GPC['code'];
        $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->_appid . "&secret=" . $this->_appsecret . "&code=" . $code . "&grant_type=authorization_code";
        $content = ihttp_get($oauth2_code);

        $token = @json_decode($content['content'], true);
        if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
            echo '<h1>获取微信公众号授权' . $code . '失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
            exit;
        }
        $from_user = $token['openid'];

        if ($this->_accountlevel != 2) { //普通号
            $authkey = intval($_GPC['authkey']);
            if ($authkey == 0) {
                $url = $authurl;
                $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->_appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
                header("location:$oauth2_code");
            }
        } else {
            //再次查询是否为关注用户
            $profile = fans_search($from_user);
            if ($profile['follow'] == 1) { //关注用户直接获取信息
                $state = 1;
            } else { //未关注用户跳转到授权页
                $url = $authurl;
                $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->_appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";
                header("location:$oauth2_code");
            }
        }

        if ($state == 1) { //已关注用户
            $oauth2_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->_appid . "&secret=" . $this->_appsecret . "";
            $content = ihttp_get($oauth2_url);
            $token_all = @json_decode($content['content'], true);
            if (empty($token_all) || !is_array($token_all) || empty($token_all['access_token'])) {
                echo '<h1>获取微信公众号授权失败[无法取得access_token], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
                exit;
            }
            $access_token = $token_all['access_token'];
            $oauth2_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $from_user . "&lang=zh_CN";
        } else { //未关注用户
            $access_token = $token['access_token'];
            $oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $from_user . "&lang=zh_CN";
            //https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID
        }

        //使用全局ACCESS_TOKEN获取OpenID的详细信息
        $content = ihttp_get($oauth2_url);
        $info = @json_decode($content['content'], true);
        if (empty($info) || !is_array($info) || empty($info['openid']) || empty($info['nickname'])) {
            echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>' . 'state:' . $state . 'nickname' . $profile['nickname'] . 'weid:' . $profile['weid'];
            exit;
        }
        $headimgurl = $info['headimgurl'];
        $nickname = $info['nickname'];
        //设置cookie信息
        setcookie($this->_auth2_headimgurl, $headimgurl, time() + 3600 * 24);
        setcookie($this->_auth2_nickname, $nickname, time() + 3600 * 24);
        setcookie($this->_auth2_openid, $from_user, time() + 3600 * 24);
        return $info;
    }

    public function getAccessToken()
    {
        global $_GPC, $_W;
        load()->func('communication');

        $account = $_W['account'];
        if (is_array($account['access_token']) && !empty($account['access_token']['token']) && !empty($account['access_token']['expire']) && $account['access_token']['expire'] > TIMESTAMP) {
            return $account['access_token']['token'];
        } else {
            if (empty($account['weid'])) {
                message('参数错误.');
            }

            if (empty($account['key']) || empty($account['secret'])) {
                message('请填写公众号的appid及appsecret, (需要你的号码为微信服务号)！', create_url('account/post', array('id' => $account['weid'])), 'error');
            }

            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$account['key']}&secret={$account['secret']}";
            $content = ihttp_get($url);

            if (empty($content)) {
                message('获取微信公众号授权失败, 请稍后重试！');
            }
            $token = @json_decode($content['content'], true);
            if (empty($token) || !is_array($token)) {
                message('获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为: <br />' . $token);
            }
            if (empty($token['access_token']) || empty($token['expires_in'])) {
                message('解析微信公众号授权失败, 请稍后重试！');
            }

            $data = array();
            $data['token'] = $token['access_token'];
            $data['expire'] = TIMESTAMP + $token['expires_in'];
            $row = array();
            $row['access_token'] = iserializer($data);
            pdo_update('wechats', $row, array('weid' => $_W['uniacid']));
            return $data['token'];
        }
    }

    //留言
    public function doMobileFeedback()
    {
        global $_GPC, $_W;

        $storeid = intval($_GPC['storeid']);
        $nickname = trim($_GPC['nick']);
        $content = trim($_GPC['content']);
        $fromuser = trim($_GPC['fromuser']);

        if (isset($_COOKIE[$this->_auth2_openid])) {
            $fromuser = $_COOKIE[$this->_auth2_openid];
        }

        if (isset($_COOKIE[$this->_auth2_nickname])) {
            $nickname = $_COOKIE[$this->_auth2_nickname];
        }

        $result = array(
            'status' => 0,
            'msg' => '留言失败，请稍后重试...'
        );

        $data = array(
            'weid' => $_W['uniacid'],
            'storeid' => $storeid,
            'from_user' => $fromuser,
            'nickname' => $nickname,
            'content' => $content,
            'dateline' => TIMESTAMP
        );

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $_W['uniacid']));

        if (!empty($setting)) {
            if ($setting['feedback_check_enable'] == 1) {
                $data['status'] = 0;
            } else {
                $data['status'] = 1;
            }
        } else {
            $data['status'] = 1;
        }

        if (empty($data['from_user'])) {
            $result['msg'] = '会话已过期,请从微信界面重新发送关键字进入.';
            die(json_encode($result));
        }

        if (empty($data['nickname'])) {
            $result['msg'] = '请输入昵称.';
            die(json_encode($result));
        }

        if (empty($data['content'])) {
            $result['msg'] = '请输入留言内容.';
            die(json_encode($result));
        }

        $rowcount = pdo_insert('weisrc_businesscenter_feedback', $data);
        if ($rowcount > 0) {
            fans_update($data['from_user'], array('nickname' => $nickname));
            $result['status'] = 1;
            $result['msg'] = '操作成功!';
        }
        echo json_encode($result);
    }

    /**
     *计算某个经纬度的周围某段距离的正方形的四个点
     *
     * @param lng float 经度
     * @param lat float 纬度
     * @param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
     * @return array 正方形的四个点的经纬度坐标
     */
    public $curversion = '150*';

    public function squarePoint($lng, $lat, $distance = 0.5)
    {

        $dlng = 2 * asin(sin($distance / (2 * EARTH_RADIUS)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
        $dlat = $distance / EARTH_RADIUS;
        $dlat = rad2deg($dlat);

        return array(
            'left-top' => array('lat' => $lat + $dlat, 'lng' => $lng - $dlng),
            'right-top' => array('lat' => $lat + $dlat, 'lng' => $lng + $dlng),
            'left-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng - $dlng),
            'right-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng + $dlng)
        );
    }

    function getDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2)
    {
        $radLat1 = $lat1 * M_PI / 180;
        $radLat2 = $lat2 * M_PI / 180;
        $a = $lat1 * M_PI / 180 - $lat2 * M_PI / 180;
        $b = $lng1 * M_PI / 180 - $lng2 * M_PI / 180;

        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * EARTH_RADIUS;
        $s = round($s * 1000);
        if ($len_type > 1) {
            $s /= 1000;
        }
        $s /= 1000;
        return round($s, $decimal);
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

    function getUserInfo($code)
    {
        global $_GPC, $_W;

        $appid = $this->_appid;
        $appsecret = $this->_appsecret;

        $access_token = "";
        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
        $access_token_json = $this->https_request($access_token_url);
        $access_token_array = json_decode($access_token_json, true);
        $access_token = $access_token_array['access_token'];
        $openid = $access_token_array['openid'];
        $userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
        $userinfo_json = $this->https_request($userinfo_url);
        $userinfo_array = json_decode($userinfo_json, true);
        return $userinfo_array;
        /*
        {
            "subscribe": 1,
            "openid": "oLVPpjqs2BhvzwPj5A-vTYAX4GLc",
            "nickname": "方倍",
            "sex": 1,
            "language": "zh_CN",
            "city": "深圳",
            "province": "广东",
            "country": "中国",
            "headimgurl": "http://wx.qlogo.cn/mmopen/JcDicrZBlREhnNXZRudod9PmibRkIs5K2f1tUQ7lFjC63pYHaXGxNDgMzjGDEuvzYZbFOqtUXaxSdoZG6iane5ko9H30krIbzGv/0",
            "subscribe_time": 1386160805
        }
        */
    }

    function https_request($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return 'ERROR ' . curl_error($curl);
        }
        curl_close($curl);
        return $data;
    }

    /*
    global $_GPC, $_W;
        $lock_path = base64_decode(LOCK);
        if (!file_exists($lock_path)) {
            $servername = $_SERVER['SERVER_NAME'];
            $get_content = file_get_contents('http://www.weisrc.com/t.php?do='.$servername);
            $validation_code = $this->authorization($servername);
            if (!empty($get_content) && $get_content == 'success') {
                touch($lock_path);
                $fp = fopen($lock_path,'w');
                fwrite($fp, 'success');
                fclose($fp);
            } else {
                message(base64_decode('5a+55LiN6LW377yM5oKo5L2/55So55qE5LiN5piv5q2j54mI6L2v5Lu277yM6LSt5Lmw5q2j54mI6K+36IGU57O7cXE6MTU1OTU3NTU='));
            }
        } else {
            $file_content = file_get_contents($lock_path);
        }
     * */

    //幻灯片
    public function doWebSlide()
    {
        global $_W, $_GPC;
        checklogin();
        load()->func('tpl');
        $modulename = $this->modulename;
        $storeid = intval($_GPC['storeid']);
        $condition = '';

        $photos = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_slide') . " WHERE weid = :weid AND storeid=:storeid {$condition} ORDER BY displayorder DESC,id DESC", array(':weid' => $_W['uniacid'], ':storeid' => $storeid));
        if (checksubmit('submit')) {
            if (!empty($_GPC['attachment-new'])) {
                foreach ($_GPC['attachment-new'] as $index => $row) {
                    if (empty($row)) {
                        continue;
                    }
                    $data = array(
                        'weid' => $_W['uniacid'],
                        //'cityid' => $cityid,
                        'title' => $_GPC['title-new'][$index],
                        'description' => $_GPC['description-new'][$index],
                        'storeid' => $storeid,
                        'attachment' => $_GPC['attachment-new'][$index],
                        'url' => $_GPC['url-new'][$index],
                        'isfirst' => intval($_GPC['isfirst-new'][$index]),
                        'displayorder' => $_GPC['displayorder-new'][$index],
                        'status' => $_GPC['status-new'][$index],
                    );
                    pdo_insert($this->modulename . '_slide', $data);
                }
            }
            if (!empty($_GPC['attachment'])) {
                foreach ($_GPC['attachment'] as $index => $row) {
                    if (empty($row)) {
                        continue;
                    }
                    $data = array(
                        'weid' => $_W['uniacid'],
                        'title' => $_GPC['title'][$index],
                        'description' => $_GPC['description'][$index],
                        'attachment' => $_GPC['attachment'][$index],
                        'url' => $_GPC['url'][$index],
                        'isfirst' => intval($_GPC['isfirst'][$index]),
                        'displayorder' => $_GPC['displayorder'][$index],
                        'status' => $_GPC['status'][$index],
                    );
                    pdo_update($this->modulename . '_slide', $data, array('id' => $index));
                }
            }
            message('幻灯片更新成功！', $this->createWebUrl('slide', array('storeid' => $storeid)));
        }
        include $this->template('slide');
    }

    public function insert_default_category($name, $logo, $parent_name = '', $isfirst = 0)
    {
        global $_GPC, $_W;
        checklogin();
        $path = '../addons/weisrc_businesscenter/template/themes/images/';
        $path = $path . 'icon_' . $logo . '.png';

        $category_parent = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE name = :name AND weid=:weid AND parentid=0", array(':name' => $parent_name, ':weid' => $_W['uniacid']));

        $parentid = intval($category_parent['id']);

        $data = array(
            'weid' => $_W['uniacid'],
            'name' => $name,
            'logo' => $path,
            'displayorder' => 0,
            'isfirst' => $isfirst,
            'parentid' => $parentid,
        );

        $category = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE name = :name AND weid=:weid", array(':name' => $name, ':weid' => $_W['uniacid']));

        if (empty($category)) {
            pdo_insert('weisrc_businesscenter_category', $data);
        }
        return pdo_insertid();
    }

    //类别管理
    public function doWebCategory()
    {
        global $_GPC, $_W;
        checklogin();
        load()->func('tpl');
        $action = 'category';
        $title = '商家类别';
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'display') {
            if ($_GPC['type'] == 'default') {
                $parentid = $this->insert_default_category('餐饮', 'canyin');
                $this->insert_default_category('美食', 'ms', '餐饮', 1);
                $parentid = $this->insert_default_category('娱乐', 'yule');
                $this->insert_default_category('KTV', 'ktv', '娱乐', 1);
                $parentid = $this->insert_default_category('购物', 'gouwu');
                $this->insert_default_category('数码电器', 'smdq', '购物', 1);

                $this->insert_default_category('便民服务', 'bianmin');
                $this->insert_default_category('生活服务', 'shenghuo');
                $this->insert_default_category('人物', 'renwu');
                $this->insert_default_category('汽车', 'qiche');

                $parentid = $this->insert_default_category('其他', 'other');
                $this->insert_default_category('微营销', 'wyx', '其他', 1);

                $this->insert_default_category('拍摄', 'paishe');
                $parentid = $this->insert_default_category('美容保健', 'meirong');
                $this->insert_default_category('丰胸', 'fx', '美容保健', 1);

                $this->insert_default_category('旅游', 'lvyou');
                $this->insert_default_category('酒业', 'jiuye');
                $this->insert_default_category('酒店', 'jiudian');

                $parentid = $this->insert_default_category('教育', 'jiaoyu');
                $this->insert_default_category('亲子教育', 'qzjy', '教育', 1);

                $parentid = $this->insert_default_category('婚庆', 'hunqing');
                $this->insert_default_category('婚纱', 'hs', '婚庆', 1);

                $parentid = $this->insert_default_category('房产', 'fangchan');
                $this->insert_default_category('楼盘', 'lp', '房产', 1);
            }

            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->modulename . '_category', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
            }

            $children = array();
            $category = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid DESC, displayorder DESC");
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
                $item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE id = '$id'");
            } else {
                $item = array(
                    'displayorder' => 0,
                );
            }

            if (!empty($item)) {
                if (!empty($item['logo'])) {
                    if (strpos($item['logo'], 'http') || strstr($item['logo'], '../addons/')) {
                        $logo = $item['logo'];
                    } else {
                        $logo = $_W['attachurl'] . $item['logo'];
                    }
                }
            }

            if (!empty($parentid)) {
                $parent = pdo_fetch("SELECT id, name FROM " . tablename($this->modulename . '_category') . " WHERE id = '$parentid' ORDER BY displayorder DESC,id DESC");
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
                    'name' => $_GPC['catename'],
                    'logo' => $_GPC['logo'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'isfirst' => intval($_GPC['isfirst']),
                    'parentid' => intval($parentid),
                );

                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update($this->modulename . '_category', $data, array('id' => $id));
                } else {
                    pdo_insert($this->modulename . '_category', $data);
                    $id = pdo_insertid();
                }
                message('更新分类成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
            }
            include $this->template('category');
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT id, parentid FROM " . tablename($this->modulename . '_category') . " WHERE id = '$id'");
            if (empty($item)) {
                message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display')), 'error');
            }
            pdo_delete($this->modulename . '_category', array('id' => $id, 'parentid' => $id), 'OR');
            message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
        }
    }

    public function doWebCity()
    {
        global $_GPC, $_W;
        checklogin();
        $modulename = $this->modulename;
        $action = 'city';
        $title = '商家类别';
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->modulename . '_city', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $this->createWebUrl('city', array('op' => 'display')), 'success');
            }
            $children = array();
            $city = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_city') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
            foreach ($city as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($city[$index]);
                }
            }
            include $this->template('city');
        } elseif ($operation == 'post') {
            $parentid = intval($_GPC['parentid']);
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $city = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_city') . " WHERE id = '$id'");
            } else {
                $city = array(
                    'displayorder' => 0,
                );
            }

            if (!empty($parentid)) {
                $parent = pdo_fetch("SELECT id, name FROM " . tablename($this->modulename . '_city') . " WHERE id = '$parentid'");
                if (empty($parent)) {
                    message('数据不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
                }
            }
            if (checksubmit('submit')) {
                if (empty($_GPC['name'])) {
                    message('抱歉，请输入城市名称！');
                }
                $data = array(
                    'weid' => $_W['uniacid'],
                    'name' => $_GPC['catename'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'parentid' => intval($parentid),
                );
                if (!empty($_FILES['logo']['tmp_name'])) {
                    file_delete($_GPC['logo_old']);
                    $upload = file_upload($_FILES['logo']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['logo'] = $upload['path'];
                }
                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update($this->modulename . '_city', $data, array('id' => $id));
                } else {
                    pdo_insert($this->modulename . '_city', $data);
                    $id = pdo_insertid();
                }
                message('更新成功！', $this->createWebUrl('city', array('op' => 'display')), 'success');
            }
            include $this->template('city');

        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $city = pdo_fetch("SELECT id, parentid FROM " . tablename($this->modulename . '_city') . " WHERE id = '$id'");
            if (empty($city)) {
                message('数据不存在或是已经被删除！', $this->createWebUrl('city', array('op' => 'display')), 'error');
            }
            pdo_delete($this->modulename . '_city', array('id' => $id, 'parentid' => $id), 'OR');
            message('删除成功！', $this->createWebUrl('city', array('op' => 'display')), 'success');
        }
    }

    public function doWebStores()
    {
        global $_W, $_GPC;
        checklogin();
        load()->func('tpl');
        $modulename = $this->modulename;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        $children = array();
        $category = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        if (!empty($category)) {
            $children = array();
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        } else {
            message('请先添加分类！', $this->createWebUrl('category', array('op' => 'post')), 'success');
        }

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，商家不存在或是已经删除！', '', 'error');
                }
            }

            if (!empty($item)) {
                $logo = tomedia($item['logo']);
                $qrcode = tomedia($item['qrcode']);
            }

            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($_W['uniacid']),
                    //'cityid' => intval($_GPC['cityid']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'title' => trim($_GPC['title']),
                    'description' => trim($_GPC['description']),
                    'pcate' => intval($_GPC['pcate']),
                    'ccate' => intval($_GPC['ccate']),
                    'hours' => trim($_GPC['hours']),
                    'starttime' => $_GPC['starttime'],
                    'endtime' => $_GPC['endtime'],
                    'services' => trim($_GPC['services']),
                    'discounts' => trim($_GPC['discounts']),
                    'qrcode_url' => trim($_GPC['qrcode_url']),
                    'qrcode_description' => trim($_GPC['qrcode_description']),
                    'consume' => trim($_GPC['consume']),
                    'level' => intval($_GPC['level']),
                    'tel' => trim($_GPC['tel']),
                    'address' => trim($_GPC['address']),
                    'location_p' => trim($_GPC['location_p']),
                    'location_c' => trim($_GPC['location_c']),
                    'location_a' => trim($_GPC['location_a']),
                    'place' => trim($_GPC['place']),
                    'lng' => trim($_GPC['lng']),
                    'lat' => trim($_GPC['lat']),
                    'status' => intval($_GPC['status']),
                    'isfirst' => intval($_GPC['isfirst']),
                    'top' => intval($_GPC['top']),
                    'dateline' => TIMESTAMP,
                    'shop_url' => trim($_GPC['shop_url']),
                    'site_name' => trim($_GPC['site_name']),
                    'site_url' => trim($_GPC['site_url']),
                    'shop_name' => trim($_GPC['shop_name']),
                    'time_enable1' => intval($_GPC['time_enable1']),
                    'time_enable2' => intval($_GPC['time_enable2']),
                    'time_enable3' => intval($_GPC['time_enable3']),
                    'starttime2' => $_GPC['starttime2'],
                    'endtime2' => $_GPC['endtime2'],
                    'starttime3' => $_GPC['starttime3'],
                    'endtime3' => $_GPC['endtime3'],
                    'share_title' => $_GPC['share_title'],
                    'share_desc' => $_GPC['share_desc'],
                    'share_cancel' => $_GPC['share_cancel'],
                    'share_url' => $_GPC['share_url'],
                    'follow_url' => $_GPC['follow_url']
                );

                if (!empty($_GPC['logo'])) {
                    $data['logo'] = $_GPC['logo'];
                    load()->func('file');
                    file_delete($_GPC['logo-old']);
                }

                if (!empty($_GPC['qrcode'])) {
                    $data['qrcode'] = $_GPC['qrcode'];
                    load()->func('file');
                    file_delete($_GPC['qrcode-old']);
                }

                if (empty($data['title'])) {
                    message('请输入商家名称！');
                }
                if (empty($data['pcate'])) {
                    message('请选择商家分类！');
                }
                if (!$this->checkDatetime($data['starttime'])) {
                    message('请输入正确的时间格式！');
                }
                if (!$this->checkDatetime($data['endtime'])) {
                    message('请输入正确的时间格式！');
                }

                if (empty($id)) {
                    pdo_insert($this->modulename . '_stores', $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->modulename . '_stores', $data, array('id' => $id));
                }
                message('数据更新成功！', $this->createWebUrl('stores', array('op' => 'display')), 'success');
            }
        } elseif ($operation == 'display') {
            $type = intval($_GPC['type']);
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->modulename . '_stores', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $this->createWebUrl('stores', array('op' => 'display')), 'success');
            }

            $news = pdo_fetchall("SELECT storeid,COUNT(1) as count FROM " . tablename($this->modulename . '_news') . "  GROUP BY storeid,weid having weid = :weid", array(':weid' => $_W['uniacid']), 'storeid');

            $feedback = pdo_fetchall("SELECT storeid,COUNT(1) as count FROM " . tablename($this->modulename . '_feedback') . "  GROUP BY storeid,weid having weid = :weid", array(':weid' => $_W['uniacid']), 'storeid');

            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }

            if (!empty($_GPC['pcate'])) {
                $pcateid = intval($_GPC['pcate']);
                $condition .= " AND pcate = '{$pcateid}'";
            }
            if (!empty($_GPC['ccate'])) {
                $ccateid = intval($_GPC['ccate']);
                $condition .= " AND ccate = '{$ccateid}'";
            }

            if (isset($_GPC['status'])) {
                $condition .= " AND status = '" . intval($_GPC['status']) . "'";
            }

            if ($type == 1) {
                $condition .= " AND isfirst = 1 ";
            } else if ($type == 2) {
                $condition .= " AND top = 1 ";
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE weid = '{$_W['uniacid']}' $condition ORDER BY status DESC, displayorder DESC, isfirst DESC, top DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
 
            if (!empty($list)) {
                $total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->modulename . '_stores') . " WHERE weid = '{$_W['uniacid']}' $condition");
                $pager = pagination($total, $pindex, $psize);
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，数据不存在或是已经被删除！');
            }
            if (!empty($row['logo'])) {
                file_delete($row['logo']);
            }
            pdo_delete($this->modulename . '_stores', array('id' => $id));
            message('删除成功！', referer(), 'success');
        } elseif ($operation == 'check') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->modulename . '_stores', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $this->createWebUrl('stores', array('op' => 'display')), 'success');
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
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

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE weid = '{$_W['uniacid']}' AND mode=1 $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

            if (!empty($list)) {
                $total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->modulename . '_stores') . " WHERE weid = '{$_W['uniacid']}' AND mode=1 $condition");
                $pager = pagination($total, $pindex, $psize);
            }
        } else if ($operation == 'checkdetail') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_stores') . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，商家不存在或是已经删除！', '', 'error');
                }
            }
            if (checksubmit('submit')) {
                $data = array(
                    'checked' => intval($_GPC['checked']),
                    'status' => intval($_GPC['status']),
                );
                pdo_update($this->modulename . '_stores', $data, array('id' => $id));
                message('数据更新成功！', $this->createWebUrl('stores', array('op' => 'check')), 'success');
            }
        }
        include $this->template('stores');
    }

    public function doWebNews()
    {
        global $_W, $_GPC;
        checklogin();
        load()->func('tpl');
        $modulename = $this->modulename;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $storeid = intval($_GPC['storeid']);

        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_news') . " WHERE id = :id", array(':id' => $id));

            if (!empty($item)) {
                $starttime = date('Y-m-d H:i', $item['starttime']);
                $endtime = date('Y-m-d H:i', $item['endtime']);
            } else {
                $item = array(
                    'status' => 1,
                    'top' => 1
                );
                $starttime = date('Y-m-d H:i');
                $endtime = date('Y-m-d H:i', TIMESTAMP + 86400 * 30);
            }

            if (!empty($item)) {
                $thumb = tomedia($item['thumb']);
            }

            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($_W['uniacid']),
                    'storeid' => $storeid,
                    'title' => trim($_GPC['title']),
                    'summary' => trim($_GPC['summary']),
                    'description' => trim($_GPC['description']),
                    'url' => trim($_GPC['url']),
                    'address' => trim($_GPC['address']),
                    'start_time' => intval(strtotime($_GPC['start_time'])),
                    'end_time' => intval(strtotime($_GPC['end_time'])),
                    'isfirst' => intval($_GPC['isfirst']),
                    'top' => intval($_GPC['top']),
                    'status' => intval($_GPC['status']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'dateline' => TIMESTAMP,
                );

                if (!empty($_GPC['thumb'])) {
                    $data['thumb'] = $_GPC['thumb'];
                    load()->func('file');
                    file_delete($_GPC['thumb-old']);
                }

                if (empty($data['title'])) {
                    message('请输入商家名称！');
                }

                if ($data['end_time'] <= $data['start_time']) {
                    message('请输入正确的时间区间！');
                }

                if (empty($id)) {
                    pdo_insert($this->modulename . '_news', $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->modulename . '_news', $data, array('id' => $id));
                }
                message('数据更新成功！', $this->createWebUrl('news', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } elseif ($operation == 'display') {

            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->modulename . '_news', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $this->createWebUrl('news', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $condition = " WHERE weid = '{$_W['uniacid']}' AND storeid={$storeid} ";
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }

            if (isset($_GPC['status'])) {
                $condition .= " AND status = '" . intval($_GPC['status']) . "'";
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_news') . " $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_news') . " $condition");

            $pager = pagination($total, $pindex, $psize);
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_news') . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，数据不存在或是已经被删除！');
            }
            if (!empty($row['logo'])) {
                file_delete($row['logo']);
            }
            pdo_delete($this->modulename . '_news', array('id' => $id));
            message('删除成功！', referer(), 'success');
        } elseif ($operation == 'check') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->modulename . '_news', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $this->createWebUrl('news', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $condition = " WHERE weid = '{$_W['uniacid']}' AND mode=1 ";
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

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_news') . " $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_news') . " $condition");

            $pager = pagination($total, $pindex, $psize);
        } else if ($operation == 'checkdetail') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_news') . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，商家不存在或是已经删除！', '', 'error');
                }
            }
            if (checksubmit('submit')) {
                $data = array(
                    'checked' => intval($_GPC['checked']),
                    'status' => intval($_GPC['status']),
                );
                pdo_update($this->modulename . '_news', $data, array('id' => $id));
                message('数据更新成功！', $this->createWebUrl('news', array('op' => 'check', 'storeid' => $storeid)), 'success');
            }
        }
        include $this->template('news');
    }

    //留言管理
    public function doWebFeedback()
    {
        global $_W, $_GPC;
        checklogin();
        $modulename = $this->modulename;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $storeid = intval($_GPC['storeid']);

        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_feedback') . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，数据不存在或是已经删除！！', '', 'error');
                }
            } else {
                $item = array(
                    'dateline' => TIMESTAMP,
                    'status' => 1,
                );
            }

            if (checksubmit('submit')) {
                $data = array(
                    'weid' => intval($_W['uniacid']),
                    'storeid' => $storeid,
                    'nickname' => trim($_GPC['nickname']),
                    'content' => trim($_GPC['content']),
                    'top' => intval($_GPC['top']),
                    'status' => intval($_GPC['status']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'dateline' => TIMESTAMP,
                );

                if (empty($data['nickname'])) {
                    message('请输入昵称！');
                }

                if (empty($id)) {
                    pdo_insert($this->modulename . '_feedback', $data);
                } else {
                    unset($data['dateline']);
                    pdo_update($this->modulename . '_feedback', $data, array('id' => $id));
                }
                message('数据更新成功！', $this->createWebUrl('feedback', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }
        } elseif ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->modulename . '_feedback', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $this->createWebUrl('feedback', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $condition = " WHERE weid = '{$_W['uniacid']}' ";

            if (!empty($storeid)) {
                $condition .= "  AND storeid={$storeid} ";
            }

            if (isset($_GPC['status'])) {
                $condition .= " AND status = '" . intval($_GPC['status']) . "'";
            }

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_feedback') . " $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

            if (!empty($list)) {
                $total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename($this->modulename . '_feedback') . " $condition");
                $pager = pagination($total, $pindex, $psize);
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_feedback') . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，数据不存在或是已经被删除！');
            }
            pdo_delete($this->modulename . '_feedback', array('id' => $id));
            message('删除成功！', referer(), 'success');

        } elseif ($operation == 'check') { //审核
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->modulename . '_feedback', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('排序更新成功！', $this->createWebUrl('feedback', array('op' => 'display', 'storeid' => $storeid)), 'success');
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
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

            $list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_feedback') . " WHERE weid = '{$_W['uniacid']}' AND mode=1 $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_feedback') . " WHERE weid = '{$_W['uniacid']}' $condition");

            $pager = pagination($total, $pindex, $psize);
        } elseif ($operation == 'deleteall') {
            $rowcount = 0;
            $notrowcount = 0;
            foreach ($_GPC['idArr'] as $k => $id) {
                $id = intval($id);
                if (!empty($id)) {
                    $feedback = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_feedback') . " WHERE id = :id", array(':id' => $id));
                    if (empty($feedback)) {
                        $notrowcount++;
                        continue;
                    }
                    pdo_delete($this->modulename . '_feedback', array('id' => $id, 'weid' => $_W['uniacid']));
                    $rowcount++;
                }
            }
            $this->message("操作成功！共删除{$rowcount}条数据,{$notrowcount}条数据不能删除!", '', 0);
        } elseif ($operation == 'checkall') {
            $rowcount = 0;
            $notrowcount = 0;
            foreach ($_GPC['idArr'] as $k => $id) {
                $id = intval($id);
                if (!empty($id)) {
                    $feedback = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_feedback') . " WHERE id = :id", array(':id' => $id));
                    if (empty($feedback)) {
                        $notrowcount++;
                        continue;
                    }

                    $data = empty($feedback['status']) ? 1 : 0;
                    pdo_update($this->modulename . '_feedback', array('status' => $data), array("id" => $id, "weid" => $_W['uniacid']));
                    $rowcount++;
                }
            }
            $this->message("操作成功！共审核{$rowcount}条数据,{$notrowcount}条数据不能删除!!", '', 0);
        }
        include $this->template('feedback');
    }

    public function doWebSetStoresProperty()
    {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $type = $_GPC['type'];
        $data = intval($_GPC['data']);
        empty($data) ? ($data = 1) : $data = 0;
        if (!in_array($type, array('isfirst', 'status', 'top'))) {
            die(json_encode(array("result" => 0)));
        }
        pdo_update($this->modulename . '_stores', array($type => $data), array("id" => $id, "weid" => $_W['uniacid']));
        die(json_encode(array("result" => 1, "data" => $data)));
    }

    public function checkDatetime($str, $format = "H:i")
    {
        $str_tmp = date('Y-m-d') . ' ' . $str;
        $unixTime = strtotime($str_tmp);
        $checkDate = date($format, $unixTime);
        if ($checkDate == $str) {
            return 1;
        } else {
            return 0;
        }
    }

    public function doWebDelete()
    {
        global $_W, $_GPC;
        checklogin();
        $weid = $_W['uniacid'];
        $type = $_GPC['type'];
        $id = intval($_GPC['id']);
        if ($type == 'photo') {
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_slide') . " WHERE id = :id AND weid=:weid", array(':id' => $id, ':weid' => $weid));
                if (empty($item)) {
                    message('图片不存在或是已经被删除！');
                }
                pdo_delete($this->modulename . '_slide', array('id' => $item['id'], 'weid' => $weid));
            } else {
                $item['attachment'] = $_GPC['attachment'];
            }
            file_delete($item['attachment']);
        }
        message('删除成功！', referer(), 'success');
    }

    public function doWebSetting()
    {
        global $_W, $_GPC;

        load()->func('tpl');

        $weid = $_W['uniacid'];

        $setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $_W['uniacid']));

        if (!empty($setting)) {
            $share_image = tomedia($setting['share_image']);
            $logo = tomedia($setting['logo']);
        }

        if (checksubmit('submit')) {
            $data = array(
                'weid' => $_W['uniacid'],
                'title' => trim($_GPC['title']),
                'announcement' => trim($_GPC['announcement']),
                'tel' => trim($_GPC['tel']),
                'address' => trim($_GPC['address']),
                'location_p' => trim($_GPC['location_p']),
                'location_c' => trim($_GPC['location_c']),
                'location_a' => trim($_GPC['location_a']),
                'place' => trim($_GPC['place']),
                'lng' => trim($_GPC['lng']),
                'lat' => trim($_GPC['lat']),
                'pagesize' => intval($_GPC['pagesize']),
                'topcolor' => trim($_GPC['topcolor']),
                'topbgcolor' => trim($_GPC['topbgcolor']),
                'announcebordercolor' => trim($_GPC['announcebordercolor']),
                'announcebgcolor' => trim($_GPC['announcebgcolor']),
                'announcecolor' => trim($_GPC['announcecolor']),
                'storestitlecolor' => trim($_GPC['storestitlecolor']),
                'storesstatuscolor' => trim($_GPC['storesstatuscolor']),
                'showcity' => intval($_GPC['showcity']),
                'settled' => intval($_GPC['settled']),
                'dateline' => TIMESTAM,
                'feedback_show_enable' => intval($_GPC['feedback_show_enable']),
                'feedback_check_enable' => intval($_GPC['feedback_check_enable']),
                'scroll_announce_enable' => intval($_GPC['scroll_announce_enable']),
                'scroll_announce' => trim($_GPC['scroll_announce']),
                'scroll_announce_link' => trim($_GPC['scroll_announce_link']),
                'copyright' => trim($_GPC['copyright']),
                'copyright_link' => trim($_GPC['copyright_link']),
                'menuname1' => trim($_GPC['menuname1']),
                'menulink1' => trim($_GPC['menulink1']),
                'menuname2' => trim($_GPC['menuname2']),
                'menulink2' => trim($_GPC['menulink2']),
                'menuname3' => trim($_GPC['menuname3']),
                'menulink3' => trim($_GPC['menulink3']),
                'scroll_announce_speed' => intval($_GPC['scroll_announce_speed']),
                'appid' => trim($_GPC['appid']),
                'secret' => trim($_GPC['secret']),
                'share_title' => $_GPC['share_title'],
                'share_desc' => $_GPC['share_desc'],
                'share_cancel' => $_GPC['share_cancel'],
                'share_url' => $_GPC['share_url'],
                'follow_url' => $_GPC['follow_url'],
                'share_image' => trim($_GPC['share_image']),
            );

            if (!empty($_GPC['share_image'])) {
                $data['share_image'] = $_GPC['share_image'];
                load()->func('file');
                file_delete($_GPC['share_image-old']);
            }

            if (empty($setting)) {
                pdo_insert($this->modulename . '_setting', $data);
            } else {
                unset($data['dateline']);
                pdo_update($this->modulename . '_setting', $data, array('weid' => $_W['uniacid']));
            }
            message('操作成功', $this->createWebUrl('setting'), 'success');
        }
        include $this->template('setting');
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

    function thumn($background, $width, $height, $newfile)
    {
        list($s_w, $s_h) = getimagesize($background); //获取原图片高度、宽度
//        if ($width && ($s_w < $s_h)) {
//            $width = ($height / $s_h) * $s_w;
//        } else {
//            $height = ($width / $s_w) * $s_h;
//        }
        $new = imagecreatetruecolor($width, $height);
        $img = imagecreatefromjpeg($background);
        imagecopyresampled($new, $img, 0, 0, 0, 0, $width, $height, $s_w, $s_h);
        imagejpeg($new, $newfile);
        imagedestroy($new);
        imagedestroy($img);
    }

    //上传图片(裁剪)
    public function doWebUploadPhoto()
    {
        global $_W, $_GPC;
        $weid = intval($_W['uniacid']);
        if (!empty($_FILES['imgFile']['name'])) {
            if ($_FILES['imgFile']['error'] != 0) {
                $result['message'] = '上传失败，请重试！';
                exit(json_encode($result));
            }
            $_W['uploadsetting'] = array();
            $_W['uploadsetting']['image']['folder'] = 'images/' . $_W['uniacid'];
            $_W['uploadsetting']['image']['extentions'] = $_W['config']['upload']['image']['extentions'];
            $_W['uploadsetting']['image']['limit'] = $_W['config']['upload']['image']['limit'];
            $file = file_upload($_FILES['imgFile'], 'image');
            if (is_error($file)) {
                $result['message'] = $file['message'];
                exit(json_encode($result));
            }
            $result['url'] = $file['url'];
            $result['error'] = 0;
            $result['filename'] = $file['path'];
            $result['url'] = $_W['attachurl'] . $result['filename'];
            $oldfile = $_W['attachurl'] . $result['filename'];

            $pos = strrpos($oldfile, '/'); //寻找位置
            if ($pos) $newpath = substr($oldfile, 0, $pos); //删除后面

            $thum = str_replace(".", "thum.", $result['filename']);
            $result['filename'] = $thum;
            $result['url'] = $_W['attachurl'] . $result['filename'];
            pdo_insert('attachment', array(
                'weid' => $_W['uniacid'],
                'uid' => $_W['uid'],
                'filename' => $_FILES['imgFile']['name'],
                'attachment' => $thum,
                'type' => 1,
                'createtime' => TIMESTAMP,
            ));

            $folder = "resource/attachment/";

            //imagetype
            $width = 300;
            $height = 150;
            $imagetype = $_GPC['imagetype'];
            if ($imagetype == 'store') {
                $width = 300;
                $height = 150;
            } else if ($imagetype == 'category') {
                $width = 134;
                $height = 134;
            } else if ($imagetype == 'childcategory') {
                $width = 120;
                $height = 90;
            }

            $this->thumn($oldfile, $width, $height, $folder . $thum);
            file_delete($file['path']);

            $result['message'] = '上传成功！';
            exit(json_encode($result));
        }
    }
}