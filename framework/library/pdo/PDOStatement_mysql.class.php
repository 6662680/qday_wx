<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */


class PDOStatement_mysql {

	
	var $__connection;
	var $__dbinfo;
	var $__persistent = false;
	var $__query = '';
	var $__result = null;
	var $__fetchmode = PDO::FETCH_BOTH;
	var $__errorCode = '';
	var $__errorInfo = Array('');
	var $__boundParams = Array();

	
	function PDOStatement_mysql(&$__query, &$__connection, &$__dbinfo) {
		$this->__query = &$__query;
		$this->__connection = &$__connection;
		$this->__dbinfo = &$__dbinfo;
	}

	
	function bindParam($mixed, &$variable, $type = null, $lenght = null) {
		if(is_string($mixed))
			$this->__boundParams[$mixed] = $variable;
		else
			array_push($this->__boundParams, $variable);
	}

	
	function columnCount() {
		$result = 0;
		if(!is_null($this->__result))
			$result = mysql_num_fields($this->__result);
		return $result;
	}

	
	function errorCode() {
		return $this->__errorCode;
	}

	
	function errorInfo() {
		return $this->__errorInfo;
	}

	
	function execute($array = Array()) {
		if(count($this->__boundParams) > 0)
			$array = &$this->__boundParams;
		$__query = $this->__query;
		if(count($array) > 0) {
			foreach($array as $k => $v) {
				if(!is_int($k) || substr($k, 0, 1) === ':') {
					if(!isset($tempf))
						$tempf = $tempr = array();
					array_push($tempf, $k);
					array_push($tempr, '"'.mysql_escape_string($v).'"');
				}
				else {
					$parse = create_function('$v', 'return \'"\'.mysql_escape_string($v).\'"\';');
					$__query = preg_replace("/(\?)/e", '$parse($array[$k++]);', $__query);
					break;
				}
			}
			if(isset($tempf)) {
				foreach ($tempf as $k=>$v) {
					$search[$k] = '/' . preg_quote($tempf[$k],'`') . '\b/';
				}
				$__query = preg_replace($search, $tempr, $__query);
							}
		}
		if(is_null($this->__result = &$this->__uquery($__query)))
			$keyvars = false;
		else
			$keyvars = true;
		$this->__boundParams = array();
		return $keyvars;
	}

	
	function fetch($mode = PDO_FETCH_ASSOC, $cursor = null, $offset = null) {
		if(func_num_args() == 0)
			$mode = &$this->__fetchmode;
		$result = false;
		if(!is_null($this->__result)) {
			switch($mode) {
				case PDO::FETCH_NUM:
					$result = mysql_fetch_row($this->__result);
					break;
				case PDO::FETCH_ASSOC:
					$result = mysql_fetch_assoc($this->__result);
					break;
				case PDO::FETCH_OBJ:
					$result = mysql_fetch_object($this->__result);
					break;
				case PDO::FETCH_BOTH:
				default:
					$result = mysql_fetch_array($this->__result);
					break;
			}
		}
		if(!$result)
			$this->__result = null;
		return $result;
	}

	
	function fetchAll($mode = PDO_FETCH_ASSOC) {
		$result = array();
		if(!is_null($this->__result)) {
			switch($mode) {
				case PDO::FETCH_NUM:
					while($r = mysql_fetch_row($this->__result))
						array_push($result, $r);
					break;
				case PDO::FETCH_ASSOC:
					while($r = mysql_fetch_assoc($this->__result))
						array_push($result, $r);
					break;
				case PDO::FETCH_OBJ:
					while($r = mysql_fetch_object($this->__result))
						array_push($result, $r);
					break;
				case PDO::FETCH_BOTH:
				default:
					while($r = mysql_fetch_array($this->__result))
						array_push($result, $r);
					break;
			}
		}
		$this->__result = null;
		return $result;
	}

	
	function fetchSingle() {
		$result = null;
		if(!is_null($this->__result)) {
			$result = @mysql_fetch_row($this->__result);
			if($result)
				$result = $result[0];
			else
				$this->__result = null;
		}
		return $result;
	}

	function fetchColumn($column=0) {
		$row = mysql_fetch_row($this->__result);
		return $row[$column];
	}

	
	function rowCount() {
		return mysql_affected_rows($this->__connection);
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

	
	function setFetchMode($mode) {
		$result = false;
		switch($mode) {
			case PDO_FETCH_NUM:
			case PDO_FETCH_ASSOC:
			case PDO_FETCH_OBJ:
			case PDO_FETCH_BOTH:
				$result = true;
				$this->__fetchmode = &$mode;
				break;
		}
		return $result;
	}


			function bindColumn($mixewd, &$param, $type = null, $max_length = null, $driver_option = null) {
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
		$this->__result = null;
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