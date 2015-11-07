<?php 
defined('IN_IA') or die('Access Denied');
class ReturnData
{
    var $rcode;
    var $rdata;
    var $rmsg;
    var $rnode;
    var $rurl;
    function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f = '__construct' . $i)) {
            call_user_func_array(array($this, $f), $a);
        }
    }
    function __construct0()
    {
        $this->rcode = -1;
        $this->rdata = array();
        $this->rmsg = '';
        $this->rnode = '';
        $this->rurl = '';
    }
    function __construct1($data)
    {
        $this->rcode = 100;
        $this->rdata = $data;
        $this->rmsg = '';
        $this->rnode = '';
        $this->rurl = '';
    }
    function __construct5($code = 0, $data, $msg, $node, $url)
    {
        $this->rcode = $code;
        $this->rdata = $data;
        $this->rmsg = $msg;
        $this->rnode = $node;
        $this->rurl = $url;
    }
    public function setCode($code)
    {
        $this->rcode = $code;
    }
    public function getState()
    {
        if ($this->rcode == 100) {
            return true;
        }
        return false;
    }
    public function getData($key = null)
    {
        if ($key == null) {
            return $this->rdata;
        } else {
            return $this->rdata[$key];
        }
    }
    public function setData($value)
    {
        $this->rdata = $value;
    }
    public function addData($key, $value)
    {
        if ($this->rdata == null || !is_array($this->rdata)) {
            $this->rdata = array();
        }
        $this->rdata[$key] = $value;
    }
    public function setMsg($value)
    {
        $this->rmsg = $value;
    }
    public function getMsg()
    {
        return $this->rmsg;
    }
    public function setNode($value)
    {
        $this->rnode = $value;
    }
    public function getNode()
    {
        return $this->rnode;
    }
    public function setUrl($value)
    {
        $this->rurl = $value;
    }
    public function getUrl()
    {
        return $this->rurl;
    }
    public function toJson($isarr = false)
    {
        if ($isarr) {
            $json = json_encode($this->toArr());
        } else {
            $json = json_encode($this);
        }
        return $json;
    }
    public function toArr()
    {
        $array = array('rcode' => $this->rcode, 'rdata' => $this->rdata, 'rmsg' => $this->rmsg, 'rnode' => $this->rnode, 'rurl' => $this->rurl);
        return $array;
    }
    function toMessage()
    {
        return message($this->rmsg);
    }
}
?>