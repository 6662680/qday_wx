<?php




defined('IN_IA') or exit('Access Denied');

class Eso_SaleModule extends WeModule
{

    public function fieldsFormDisplay($rid = 0)
    {
        global $_W;
        $setting = $_W['account']['modules'][$this->_saveing_params['mid']]['config'];
		load()->func('tpl');
        include $this->template('rule');
    }

    public function fieldsFormSubmit($rid = 0)
    {
        global $_GPC, $_W;
        if (!empty($_GPC['title'])) {
            $data = array(
                'title' => $_GPC['title'],
                'description' => $_GPC['description'],
                'picurl' => $_GPC['thumb-old'],
                'url' => $this->createMoblieUrl('list', array('name' => 'eso_sale', 'uniacid' => $_W['uniacid'])),
            );
            if (!empty($_GPC['thumb'])) {
                $data['picurl'] = $_GPC['thumb'];
                file_delete($_GPC['thumb-old']);
            }
            $this->saveSettings($data);
        }
        return true;
    }

    public function settingsDisplay($settings)
    {
        global $_GPC, $_W;
        load()->func('tpl');
        if (checksubmit()) {
            $cfg = array(
                'noticeemail' => $_GPC['noticeemail'],
                'shopname' => $_GPC['shopname'],
                'zhifuCommission' => $_GPC['zhifuCommission'],
                'globalCommission' => $_GPC['globalCommission'],
                'globalCommission2' => $_GPC['globalCommission2'],
                'globalCommission3' => $_GPC['globalCommission3'],
                'indexss' => intval($_GPC['indexss']),
                'ydyy' => $_GPC['ydyy'],
                'paymsgTemplateid' => $_GPC['paymsgTemplateid'],
                'address' => $_GPC['address'],
                'phone' => $_GPC['phone'],
                'appid' => $_GPC['appid'],
                'secret' => $_GPC['secret'],
                'officialweb' => $_GPC['officialweb'],
                'description' => htmlspecialchars_decode($_GPC['description'])
            );
            if (!empty($_GPC['logo'])) {
                $cfg['logo'] = $_GPC['logo'];
            }

            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }

        include $this->template('setting');
    }

}
