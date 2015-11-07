<?php
/**
 * 数钱数到手抽筋
 *
 * @author weiyun
 * @url
 */
defined('IN_IA') or exit('Access Denied');
define('PATH', '../addons/weiyun_shuqian/template/');

class weiyun_shuqianModuleSite extends WeModuleSite
{
    public function doMobileIndex()
    {
        global $_W, $_GPC;

        $account = account_fetch($_W['uniacid']);
        if (!empty($account['key']) && !empty($account['secret'])) {
            require_once IA_ROOT . '/framework/class/account.class.php';
            $acc = WeAccount::create($_W['uniacid']);
            $_W['account']['jssdkconfig'] = $acc->getJssdkConfig();
            $accountInfo = $acc->fetchAccountInfo();
            $_W['account']['access_token'] = $accountInfo['access_token'];
            $_W['account']['jsapi_ticket'] = $accountInfo['jsapi_ticket'];
        }

        $setting = pdo_fetch("select * from " . tablename($this->modulename . '_setting') . " where weid =:weid LIMIT 1", array(':weid' => $_W['uniacid']));

        $share_image = empty($setting['share_image']) ? $_W['siteroot'] . '../addons/weiyun_shuqian/icon.jpg': tomedia($setting['share_image']);
        $share_title = empty($setting['share_title']) ? '数钱数到手抽筋' : $setting['share_title'];
        $share_desc = empty($setting['share_desc']) ? '数钱数到手抽筋' : $setting['share_desc'];
        $share_url = empty($setting['share_url']) ? $_W['siteroot'] . 'app/' . $this->createMobileUrl('index') : $setting['share_url'];

        include $this->template('index');
    }

    public function doWebSetting()
    {
        global $_W, $_GPC;
        checklogin();
        load()->func('tpl');

        $item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid", array(':weid' => $_W['uniacid']));
        if (!empty($item)) {
            if (!empty($item['share_image'])) {
                $share_image = tomedia($item['share_image']);
            }
        }

        if (checksubmit('submit')) {
            $data = array(
                'weid' => $_W['weid'],
                'share_title' => trim($_GPC['share_title']),
                'share_desc' => trim($_GPC['share_desc']),
                'share_cancel' => trim($_GPC['share_cancel']),
                'share_url' => trim($_GPC['share_url']),
                'follow_url' => trim($_GPC['follow_url'])
            );

            if (!empty($_GPC['share_image'])) {
                $data['share_image'] = $_GPC['share_image'];
                load()->func('file');
                file_delete($_GPC['share_image-old']);
            }

            if (!empty($item)) {
                pdo_update($this->modulename . '_setting', $data, array('weid' => $_W['uniacid']));
            } else {
                pdo_insert($this->modulename . '_setting', $data);
            }
            message('更新成功！', $this->createWebUrl('setting'), 'success');
        }
        include $this->template('setting');
    }
}