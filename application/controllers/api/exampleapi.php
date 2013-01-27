<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Example API Controller
 *
 * Prefix handler methods with "get_" and "post_" accoringly.
 * Note: All APIs are handled through the CI router
 *
 */
class ExampleApi extends CI_Controller {
	
	
/************************
	GET HANDLERS
*************************/
	
	/**
	 * Basic example of simple API call
	 *
	 * @param	array	$get	GET data
	 * @return	str
	 */	
	function get_simple($request) {
		
		// ## Do something here with data; use models
		
		$success = true;
		
		$a_example = array(
			"Name" => "Bob",
			"ID"   => 99
		);
		
		// Check for proper data so you can format proper response
		if ($success) {
			$return = $this->api->format(true, "Public endpoint. Data processed", $a_example);
		} else {
			$return = $this->api->format(false, "Could not find record.", $request);
		}
		
		return($return);
	}	 
	
	
	/**
	 * Permission-based API call. Checks active session before executing call
	 *
	 * @param	array	$get	GET data
	 * @return	str
	 */	
	function get_permission($request) {
		if ($this->api->access("userid")) { // Checks session for key "userid"
			
			// ## Do something here with data; use models
			
			$success = true;
			
			$a_example = array(
				"Name" => "Bob",
				"ID"   => 99
			);
			
			// Check for proper data so you can format proper response
			if ($success) {
				$return = $this->api->format(true, "Permission granted. Record found.", $a_example);
			} else {
				$return = $this->api->format(false, "Could not find record", $request);
			}
			
			return($return);
		}
	}
	
	
/************************
	POST HANDLERS
*************************/	







/************************
	UTILITY
	Common for all APIs
*************************/

	/**
	 * Constructor. Loads API library.
	 *
	 */
	function __construct() {
		parent::__construct();
		$this->load->library("api");
	}


	/**
	 * Handles incoming GET/POST requests and outputs response string
	 *
	 * @param	str		$endpoint	Relative API endpoint URL
	 * @return	mixed
	 */ 
	function index($endpoint=false) {
		if (isset($endpoint)) {
			$this->api->output($this->api->call($endpoint, $this));
		}
	}
	
} // END class

?>