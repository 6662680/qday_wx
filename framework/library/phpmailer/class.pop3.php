<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */



class POP3
{
    
    public $Version = '5.2.7';

    
    public $POP3_PORT = 110;

    
    public $POP3_TIMEOUT = 30;

    
    public $CRLF = "\r\n";

    
    public $do_debug = 0;

    
    public $host;

    
    public $port;

    
    public $tval;

    
    public $username;

    
    public $password;

    
    private $pop_conn;

    
    private $connected;

    
    private $error;

    
    const CRLF = "\r\n";

    
    public function __construct()
    {
        $this->pop_conn = 0;
        $this->connected = false;
        $this->error = null;
    }

    
    public static function popBeforeSmtp(
        $host,
        $port = false,
        $tval = false,
        $username = '',
        $password = '',
        $debug_level = 0
    ) {
        $pop = new POP3;
        return $pop->authorise($host, $port, $tval, $username, $password, $debug_level);
    }

    
    public function authorise($host, $port = false, $tval = false, $username = '', $password = '', $debug_level = 0)
    {
        $this->host = $host;
                if ($port === false) {
            $this->port = $this->POP3_PORT;
        } else {
            $this->port = $port;
        }
                if ($tval === false) {
            $this->tval = $this->POP3_TIMEOUT;
        } else {
            $this->tval = $tval;
        }
        $this->do_debug = $debug_level;
        $this->username = $username;
        $this->password = $password;
                $this->error = null;
                $result = $this->connect($this->host, $this->port, $this->tval);
        if ($result) {
            $login_result = $this->login($this->username, $this->password);
            if ($login_result) {
                $this->disconnect();
                return true;
            }
        }
                $this->disconnect();
        return false;
    }

    
    public function connect($host, $port = false, $tval = 30)
    {
                if ($this->connected) {
            return true;
        }

                        set_error_handler(array($this, 'catchWarning'));

                $this->pop_conn = fsockopen(
            $host,             $port,             $errno,             $errstr,             $tval
        );                 restore_error_handler();
                if ($this->error && $this->do_debug >= 1) {
            $this->displayErrors();
        }
                if ($this->pop_conn == false) {
                        $this->error = array(
                'error' => "Failed to connect to server $host on port $port",
                'errno' => $errno,
                'errstr' => $errstr
            );
            if ($this->do_debug >= 1) {
                $this->displayErrors();
            }
            return false;
        }

                        if (version_compare(phpversion(), '5.0.0', 'ge')) {
            stream_set_timeout($this->pop_conn, $tval, 0);
        } else {
                        if (substr(PHP_OS, 0, 3) !== 'WIN') {
                socket_set_timeout($this->pop_conn, $tval, 0);
            }
        }

                $pop3_response = $this->getResponse();
                if ($this->checkResponse($pop3_response)) {
                        $this->connected = true;
            return true;
        }
        return false;
    }

    
    public function login($username = '', $password = '')
    {
        if ($this->connected == false) {
            $this->error = 'Not connected to POP3 server';

            if ($this->do_debug >= 1) {
                $this->displayErrors();
            }
        }
        if (empty($username)) {
            $username = $this->username;
        }
        if (empty($password)) {
            $password = $this->password;
        }

                $this->sendString("USER $username" . self::CRLF);
        $pop3_response = $this->getResponse();
        if ($this->checkResponse($pop3_response)) {
                        $this->sendString("PASS $password" . self::CRLF);
            $pop3_response = $this->getResponse();
            if ($this->checkResponse($pop3_response)) {
                return true;
            }
        }
        return false;
    }

    
    public function disconnect()
    {
        $this->sendString('QUIT');
                        @fclose($this->pop_conn);
    }

    
    private function getResponse($size = 128)
    {
        $r = fgets($this->pop_conn, $size);
        if ($this->do_debug >= 1) {
            echo "Server -> Client: $r";
        }
        return $r;
    }

    
    private function sendString($string)
    {
        if ($this->pop_conn) {
            if ($this->do_debug >= 2) {                 echo "Client -> Server: $string";
            }
            return fwrite($this->pop_conn, $string, strlen($string));
        }
        return 0;
    }

    
    private function checkResponse($string)
    {
        if (substr($string, 0, 3) !== '+OK') {
            $this->error = array(
                'error' => "Server reported an error: $string",
                'errno' => 0,
                'errstr' => ''
            );
            if ($this->do_debug >= 1) {
                $this->displayErrors();
            }
            return false;
        } else {
            return true;
        }
    }

    
    private function displayErrors()
    {
        echo '<pre>';
        foreach ($this->error as $single_error) {
            print_r($single_error);
        }
        echo '</pre>';
    }

    
    private function catchWarning($errno, $errstr, $errfile, $errline)
    {
        $this->error[] = array(
            'error' => "Connecting to the POP3 server raised a PHP warning: ",
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline
        );
    }
}
