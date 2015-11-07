<?php
/**
 * 大转盘模块
 *
 * @author 情天
 */
defined('IN_IA') or exit('Access Denied');
 

class Stonefish_bigwheelModuleProcessor extends WeModuleProcessor {

    public function respond() {
        global $_W;
        $rid = $this->rule;
        $sql = "select title,description,start_picurl,isshow,starttime,endtime,end_theme,end_instruction,end_picurl from " . tablename('stonefish_bigwheel_reply') . " where `rid`=:rid LIMIT 1";
        $row = pdo_fetch($sql, array(':rid' => $rid));

        if ($row == false) {
            return $this->respText("活动已取消...");
        }

        if ($row['isshow'] == 0) {
            return $this->respText("活动暂停，请稍后...");
        }

        if ($row['starttime'] > time()) {
            return $this->respText("活动未开始，请等待...");
        }

        $endtime = $row['endtime'];
        if ( $endtime < time()) {
            return $this->respNews(array(
                        'Title' => $row['end_theme'],
                        'description' => $row['end_instruction'],
                        'PicUrl' => toimage($row['end_picurl']),
                        'Url' => $this->createMobileUrl('index', array('rid' => $rid)),
            ));
        } else {
            return $this->respNews(array(
                        'Title' => $row['title'],
                        'description' => $row['description'],
                        'PicUrl' => toimage($row['start_picurl']),
                        'Url' => $this->createMobileUrl('index', array('rid' => $rid)),
            ));
        }
    }

}
