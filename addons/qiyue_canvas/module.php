<?php
/**
 * 画图分享模块定义
 */
defined('IN_IA') or exit('Access Denied');

class Qiyue_canvasModule extends WeModule {

    public function settingsDisplay($settings) {
        global $_W, $_GPC;
        $title = '画图分享';
        load()->func('tpl');
        //点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
        //在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
        if (checksubmit()) {
            $dat = array();
            $dat['title'] = $_GPC['title'];
            $dat['bg'] = $_GPC['bg'];
            $dat['paper'] = $_GPC['paper'];
            $dat['logo'] = $_GPC['logo'];
            $dat['share_title'] = $_GPC['share_title'];
            $dat['share_content'] = $_GPC['share_content'];
            $dat['share_icon'] = $_GPC['share_icon'];
            $dat['follow_txt'] = $_GPC['follow_txt'];
            $dat['follow_link'] = $_GPC['follow_link'];
            $dat['banner_img'] = $_GPC['banner_img'];
            $dat['banner_link'] = $_GPC['banner_link'];
            $this->saveSettings($dat);
            message('设置成功', 'referer', 'success');
        }
        $module_url = MODULE_URL . 'template/mobile/images/';
        if (empty($settings['bg']))
            $settings['bg'] = $module_url . 'bg.jpg';
        if (empty($settings['paper']))
            $settings['paper'] = $module_url . 'paper.jpg';
        if (empty($settings['logo']))
            $settings['logo'] = $module_url . 'logo.png';
        if (empty($settings['share_title']))
            $settings['share_title'] = '画图分享';
        if (empty($settings['share_content']))
            $settings['share_content'] = '画图分享内容';
        if (empty($settings['share_icon']))
            $settings['share_icon'] = $module_url . 'icon.jpg';
        if (empty($settings['follow_txt']))
            $settings['follow_txt'] = '关注我们';
        if (empty($settings['follow_link']))
            $settings['follow_link'] = '#';

        //这里来展示设置项表单
        include $this->template('setting');
    }

}
