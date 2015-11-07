<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */

require_once('PDOStatement_mysql.class.php');


class PDO_mysql {

	
	var $__connection;
	var $__dbinfo;
	var $__persistent = false;
	var $__errorCode = '';
	var $__errorInfo = Array('');

	
	function PDO_mysql(&$host, &$db, &$user, &$pass) {
		if(!@$this->__connection = &mysql_connect($host, $user, $pass))
			$this->__setErrors('DBCON');
		else {
			if(!@mysql_select_db($db, $this->__connection))
				$this->__setErrors('DBER');
			else
				$this->__dbinfo = Array($host, $user, $pass, $db);
		}
	}

	
	function close() {
		$result = is_resource($this->__connection);
		if($result) {
			mysql_close($this->__connection);
		}
		return $result;
	}

	
	function errorCode() {
		return $this->__errorCode;
	}

	
	function errorInfo() {
		return $this->__errorInfo;
	}

	
	function exec($query) {
		$result = 0;
		if(!is_null($this->__uquery($query)))
			$result = mysql_affected_rows($this->__connection);
		if(is_null($result))
			$result = false;
		return $result;
	}

	
	function lastInsertId() {
		$id = mysql_insert_id($this->__connection);
		if ($id > 0) {
			return $id;	
		} else {
			$query = $this->prepare('SELECT last_insert_id()');
			$query->execute();
			return $query->fetchColumn();
		}
	}

	
	function prepare($query, $array = Array()) {
		return new PDOStatement_mysql($query, $this->__connection, $this->__dbinfo);
	}

	
	function query($query) {
		$query = @mysql_unbuffered_query($query, $this->__connection);
		if($query) {
			$result = Array();
			while($r = mysql_fetch_assoc($query))
				array_push($result, $r);
		}
		else {
			$result = false;
			$this->__setErrors('SQLER');
		}
		return $result;
	}

	
	function quote($string) {
		return ('"'.mysql_escape_string($string).'"');
	}


			
	function getAttribute($attribute) {
		$result = false;
		switch($attribute) {
			case PDO_ATTR_SERVER_INFO:
				$result = mysql_get_host_info($this->__connection);
				break;
			case PDO_ATTR_SERVER_VERSION:
				$result = mysql_get_server_info($this->__connection);
				break;
			case PDO_ATTR_CLIENT_VERSION:
				$result = mysql_get_client_info();
				break;
			case PDO_ATTR_PERSISTENT:
				$result = $this->__persistent;
				break;
		}
		return $result;
	}

	
	function setAttribute($attribute, $mixed) {
		$result = false;
		if($attribute === PDO_ATTR_PERSISTENT && $mixed != $this->__persistent) {
			$result = true;
			$this->__persistent = (boolean) $mixed;
			mysql_close($this->__connection);
			if($this->__persistent === true)
				$this->__connection = &mysql_pconnect($this->__dbinfo[0], $this->__dbinfo[1], $this->__dbinfo[2]);
			else
				$this->__connection = &mysql_connect($this->__dbinfo[0], $this->__dbinfo[1], $this->__dbinfo[2]);
			mysql_select_db($this->__dbinfo[3], $this->__connection);
		}
		return $result;
	}


		function beginTransaction() {
		return false;
	}

	function commit() {
		return false;
	}

	function rollBack() {
		return false;
	}


		function __setErrors($er) {
		if(!is_resource($this->__connection)) {
			$errno = mysql_errno();
			$errst = mysql_error();
		}
		else {
			$errno = mysql_errno($this->__connection);
			$errst = mysql_error($this->__connection);
		}
		$this->__errorCode = &$er;
		$this->__errorInfo = Array($this->__errorCode, $errno, $errst);
	}

	function __uquery(&$query) {
		if(!@$query = mysql_query($query, $this->__connection)) {
			$this->__setErrors('SQLER');
			$query = null;
		}
		return $query;
	}
}
?>