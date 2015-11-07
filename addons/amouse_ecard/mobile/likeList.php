<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15-4-6
 * Time: 下午12:51
 * To change this template use File | Settings | File Templates.
 */

global $_W,$_GPC;
//$this->checkBrower();
$from_user = $_W['openid'];
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'list';
$id = $_GPC['id'];
$cid = $_GPC['cid'];
$num = pdo_fetchcolumn("SELECT zan FROM ".tablename('amouse_weicard_card')." WHERE id=".$cid." " );
$zanList = pdo_fetchall("SELECT m.realname, z.createtime, m.headimg, m.id FROM ".tablename('amouse_weicard_member')." AS m LEFT JOIN ".tablename('amouse_weicard_zan')." AS z ON m.id=z.mid WHERE z.zan_mid=".$id." AND m.weid=".$_W['uniacid']." LIMIT 50 " );
include $this->template('qianxian/like_list');