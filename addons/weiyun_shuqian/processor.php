<?php
/**
 * 数钱数到手抽筋
 *
 * @author weiyun
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class weiyun_shuqianModuleProcessor extends WeModuleProcessor
{
    public $name = 'weiyun_shuqianModuleProcessor';

    public function isNeedInitContext()
    {
        return 0;
    }

    public function respond()
    {
        return false;
    }

    public function isNeedSaveContext()
    {
        return false;
    }
}
