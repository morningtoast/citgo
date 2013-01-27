<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API Library
 *
 * Common methods used by API controllers
 * Handlines security and output
 */

	class Api {
		function __construct() {
			$this->settings = array(
				"allowedDomain" => "*", // Checks to make sure call is coming from this domain
				"allowDirect"   => true // Disables direct access, must come from referrer
			);
		}
		
		
		/**
		 * Checks current session to see if passed key exists and/or matches provided values
		 * If session fails check, stock failure response is sent
		 *
		 * @param	str		$key		Name of session key to check
		 * @param	mixed	$match		Optional. A string or array to check against session value. If array, checks all values.
		 * @return	bool
		 */
		function access($key, $match="*") {
			$sessionValue = $_SESSION[$key];
			$success      = false;
		
			if ($match) {
				if (is_array($match)) {
					if (in_array($sessionValue, $match)) {
						$success = true;
					}
				} else {
					if ($match == "*") {
						if (isset($sessionValue)) {
							$success = true;
						}
					} else {
						if ($sessionValue == $match) {
							$success = true;
						}
					}
				}
			}
			
			if (!$success) {
				$this->response($this->format(false, "Access denied"));
				exit();
			} else {
				return(true);
			}
		}
		
		
		/**
		 * Returns response from API endpoint. Must pass active class object 
		 *
		 * @param	str		$endpoint		Relative or absolute path to API
		 * @param	obj		$class			Active API class object
		 * @return	str
		 */
		function call($endpoint, $class) {
			$a_response = $this->load($endpoint, $class);
			return($this->response($a_response));
		
		}
		
		
		/**
		 * Returns response data from API endpoint
		 *
		 * @param	str		$endpoint		Relative or absolute path to API
		 * @param	obj		$class			Active API class object
		 * @return	array
		 */
		function load($endpoint, $class)	{
			$a_payload = array(
				"success" => false,
				"message" => "Remote access is not allowed",
				"data"    => array()
			);
			
			if (isset($_SERVER["HTTP_REFERER"]) or $this->settings["allowDirect"]) {
				if (stripos($_SERVER["HTTP_REFERER"], $this->settings["allowedDomain"]) or ($this->settings["allowedDomain"] == "*")) {
					$a_payload["message"] = "End point not provided";
				
					if (isset($endpoint)) {
						$a_payload["message"] = "Invalid end point";
						
						if (isset($_GET)) {
							$f = "get_".$endpoint;
							if (method_exists($class,$f)) {
								$a_payload = $class->$f($_GET);
							}
						}
						
						if (isset($_POST)) {
							$f = "post_".$endpoint;
							if (method_exists($class,$f)) {
								$a_payload = $class->$f($_POST);
							}
						}
						
					} // Endpoint check
				} // Domain check
			} // END refer check
			
			return($a_payload);
		}
		
		/**
		 * Returns formatted response string
		 *
		 * @param	array	$p		Data to format
		 * @param	str		$m		Format style. Default is JSON
		 * @return	str
		 */
		function response($p, $m="json") {
			switch($m) {
				case "json":
				default:
					$p = json_encode($p);
					header('Content-type: application/json');
					break;
				
				case "serialize": 
					$p = serialize($p);
					break;
					
				case "text":
					break;
			}
			
			
			echo $p;
		}
		
		
		/**
		 * Returns formatted array ready for response
		 *
		 * @param	bool	$s		Success status either TRUE or FALSE
		 * @param	str		$m		Friendly response message
		 * @param	array	$d		Response data
		 * @return	array
		 */
		function format($s=false, $m="No message available", $d=array()) {
			$a_json = array(
				"success" => $s,
				"message" => $m,
				"data"    => $d
			);
			
			return($a_json);
		}

		
		/**
		 * Echoes values
		 *
		 * @param	mixed	$v		Value to output
		 * @return	void
		 */
		function output($v) { echo $v; }

		
	} // END class
?>