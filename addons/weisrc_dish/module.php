<?php
/**
 * 微点餐
 *
 * 作者:迷失卍国度
 *
 */
defined('IN_IA') or exit('Access Denied');

class weisrc_dishModule extends WeModule
{
    public $name = 'weisrc_dishModule';

    public function fieldsFormDisplay($rid = 0)
    {
        global $_W;
    }

    public function fieldsFormSubmit($rid = 0)
    {
        global $_GPC, $_W;
    }
}