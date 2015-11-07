<?php

defined('IN_IA') or exit('Access Denied');

class QuickSpreadModuleReceiver extends WeModuleReceiver {

    public function receive() {
        if ($this->message['msgtype'] == 'event') {
            if ($this->message['event'] == 'subscribe' && !empty($this->message['ticket'])) {
                $scene_id = $this->message['eventkey'];
                WeUtility::logging("Receiver:SUBSCRIBE", $scene_id);
            } elseif ($this->message['event'] == 'SCAN') {
                $scene_id = $this->message['eventkey'];
                WeUtility::logging("Receiver:SCAN", $scene_id);
            }
        }
    }

}
