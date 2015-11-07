<?php

/**
 * 微楼书
 *
 * @author ewei
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
class Mon_houseModuleProcessor extends WeModuleProcessor {
    private $table_house = "mon_house";
    private $sae=false;
    public function respond() {
        global $_W;
        $rid = $this->rule;

        if ($rid) {
            $reply = pdo_fetch ( "SELECT * FROM " . tablename ( $this->table_house ) . " WHERE rid = :rid", array (':rid' => $rid ) );
            if ($reply) {
                $news = array ();
                $news [] = array ('title' => $reply['news_title'], 'description' =>$reply['news_content'], 'picurl' => $this->getpicurl ( $reply ['news_icon'] ), 'url' => $this->createMobileUrl ( 'index',array('hid'=>$reply['id']))  );
                return $this->respNews ( $news );
            }
        }
        return null;
    }
    private function getpicurl($url) {
        global $_W;
        if($this->sae){
            return $url;
        }
        return $_W ['attachurl'] . $url;
    }

}
