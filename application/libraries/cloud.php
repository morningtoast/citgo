<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	Native PHP Session Library
*/

	class Cloud {
		function __construct() {
			session_start();
		}
		
		
		function get($key) {
			if (array_key_exists($key, $_SESSION)) {
				return($_SESSION[$key]);
			} else {
				return(false);
			}
		}
		
		function set($key, $value) {
			$_SESSION[$key] = $value;
			return($value);
		}
		
		function delete($key) {
			if (array_key_exists($key, $_SESSION)) {
				unset($_SESSION[$key]);
				return(true);
			} else {
				return(false);
			}
		}
		
		function clear() {
			foreach ($_SESSION as $k => $v) {
				$this->delete($k);
			}
		}
		
		function stop() {
			$this->clear();
			session_destroy();
		}

		
	} // END class
?>