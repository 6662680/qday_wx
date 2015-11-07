<?php
/**
 * [Weizan System] Copyright (c) 2014 012WZ.COM
 * Weizan is NOT a free software, it under the license terms, visited http://www.qdaygroup.com/ for more details.
 */

if(!class_exists('PDO')) {

		define('PDO_ATTR_SERVER_VERSION', 4);		define('PDO_ATTR_CLIENT_VERSION', 5);		define('PDO_ATTR_SERVER_INFO', 6);		define('PDO_ATTR_PERSISTENT', 12);	
			define('PDO_FETCH_ASSOC', 2);			define('PDO_FETCH_NUM', 3);			define('PDO_FETCH_BOTH', 4);			define('PDO_FETCH_OBJ', 5);		
			define('PDO_FETCH_LAZY', 1);			define('PDO_FETCH_BOUND', 6);		
	
	class PDO {

		
		const FETCH_ASSOC = PDO_FETCH_ASSOC;
		const FETCH_NUM = PDO_FETCH_NUM;
		const FETCH_BOTH = PDO_FETCH_BOTH;
		const FETCH_OBJ = PDO_FETCH_OBJ;
		const FETCH_LAZY = PDO_FETCH_LAZY;
		const FETCH_BOUND = PDO_FETCH_BOUND;
		const ATTR_SERVER_VERSION = PDO_ATTR_SERVER_VERSION;
		const ATTR_CLIENT_VERSION = PDO_ATTR_CLIENT_VERSION;
		const ATTR_SERVER_INFO = PDO_ATTR_SERVER_INFO;
		const ATTR_PERSISTENT = PDO_ATTR_PERSISTENT;


		
		var $__driver;

		
		function PDO($string_dsn, $string_username = '', $string_password = '', $array_driver_options = null) {
			$con = &$this->__getDNS($string_dsn);
			if($con['dbtype'] === 'mysql') {
				require_once('PDO_mysql.class.php');
				if(isset($con['port']))
					$con['host'] .= ':'.$con['port'];
				$this->__driver = new PDO_mysql(
					$con['host'],
					$con['dbname'],
					$string_username,
					$string_password
				);
			}
			elseif($con['dbtype'] === 'sqlite2' || $con['dbtype'] === 'sqlite') {
				require_once('PDO_sqlite.class.php');
				$this->__driver = new PDO_sqlite($con['dbname']);
			}
			elseif($con['dbtype'] === 'pgsql') {
				require_once('PDO_pgsql.class.php');
				$string_dsn = "host={$con['host']} dbname={$con['dbname']} user={$string_username} password={$string_password}";
				if(isset($con['port']))
					$string_dsn .= " port={$con['port']}";
				$this->__driver = new PDO_pgsql($string_dsn);
			}
		}

		
		function beginTransaction() {
			$this->__driver->beginTransaction();
		}

		
		function close() {
			return $this->__driver->close();
		}

		
		function commit() {
			$this->__driver->commit();
		}

		
		function exec($query) {
			return $this->__driver->exec($query);
		}

		
		function errorCode() {
			return $this->__driver->errorCode();
		}

		
		function errorInfo() {
			return $this->__driver->errorInfo();
		}

		
		function getAttribute($attribute) {
			return $this->__driver->getAttribute($attribute);
		}

		
		function lastInsertId() {
			return $this->__driver->lastInsertId();
		}

		
		function prepare($query, $array = Array()) {
			return $this->__driver->prepare($query, $array = Array());
		}

		
		function query($query) {
			return $this->__driver->query($query);
		}

		
		function quote($string) {
			return $this->__driver->quote($string);
		}

		
		function rollBack() {
			$this->__driver->rollBack();
		}

		
		function setAttribute($attribute, $mixed) {
			return $this->__driver->setAttribute($attribute, $mixed);
		}

						function __getDNS(&$string) {
			$result = array();
			$pos = strpos($string, ':');
			$parameters = explode(';', substr($string, ($pos + 1)));
			$result['dbtype'] = strtolower(substr($string, 0, $pos));
			for($a = 0, $b = count($parameters); $a < $b; $a++) {
				$tmp = explode('=', $parameters[$a]);
				if(count($tmp) == 2)
					$result[$tmp[0]] = $tmp[1];
				else
					$result['dbname'] = $parameters[$a];
			}
			return $result;
		}
	}
}
else {
	
	class _PDO {
		const FETCH_ASSOC = PDO::FETCH_ASSOC;
		const FETCH_NUM = PDO::FETCH_NUM;
		const FETCH_BOTH = PDO::FETCH_BOTH;
		const FETCH_OBJ = PDO::FETCH_OBJ;
		const FETCH_LAZY = PDO::FETCH_LAZY;
		const FETCH_BOUND = PDO::FETCH_BOUND;
		const ATTR_SERVER_VERSION = PDO::ATTR_SERVER_VERSION;
		const ATTR_CLIENT_VERSION = PDO::ATTR_CLIENT_VERSION;
		const ATTR_SERVER_INFO = PDO::ATTR_SERVER_INFO;
		const ATTR_PERSISTENT = PDO::ATTR_PERSISTENT;
		var $__driver;
		function _PDO($string_dsn, $string_username = '', $string_password = '', $array_driver_options = null) {
			$con = &$this->__getDNS($string_dsn);
			if($con['dbtype'] === 'mysql') {
				require_once('PDO_mysql.class.php');
				if(isset($con['port']))
					$con['host'] .= ':'.$con['port'];
				$this->__driver = new PDO_mysql(
					$con['host'],
					$con['dbname'],
					$string_username,
					$string_password
				);
			}
			elseif($con['dbtype'] === 'sqlite2' || $con['dbtype'] === 'sqlite') {
				require_once('PDO_sqlite.class.php');
				$this->__driver = new PDO_sqlite($con['dbname']);
			}
			elseif($con['dbtype'] === 'pgsql') {
				require_once('PDO_pgsql.class.php');
				$string_dsn = "host={$con['host']} dbname={$con['dbname']} user={$string_username} password={$string_password}";
				if(isset($con['port']))
					$string_dsn .= " port={$con['port']}";
				$this->__driver = new PDO_pgsql($string_dsn);
			}
		}
		function beginTransaction() {
			$this->__driver->beginTransaction();
		}
		function close() {
			return $this->__driver->close();
		}
		function commit() {
			$this->__driver->commit();
		}
		function exec($query) {
			return $this->__driver->exec($query);
		}
		function errorCode() {
			return $this->__driver->errorCode();
		}
		function errorInfo() {
			return $this->__driver->errorInfo();
		}
		function getAttribute($attribute) {
			return $this->__driver->getAttribute($attribute);
		}
		function lastInsertId() {
			return $this->__driver->lastInsertId();
		}
		function prepare($query, $array = Array()) {
			return $this->__driver->prepare($query, $array = Array());
		}
		function query($query) {
			return $this->__driver->query($query);
		}
		function quote($string) {
			return $this->__driver->quote($string);
		}
		function rollBack() {
			$this->__driver->rollBack();
		}
		function setAttribute($attribute, $mixed) {
			return $this->__driver->setAttribute($attribute, $mixed);
		}
				function __getDNS(&$string) {
			$result = array();
			$pos = strpos($string, ':');
			$parameters = explode(';', substr($string, ($pos + 1)));
			$result['dbtype'] = strtolower(substr($string, 0, $pos));
			for($a = 0, $b = count($parameters); $a < $b; $a++) {
				$tmp = explode('=', $parameters[$a]);
				if(count($tmp) == 2)
					$result[$tmp[0]] = $tmp[1];
				else
					$result['dbname'] = $parameters[$a];
			}
			return $result;
		}
	}
}
?>