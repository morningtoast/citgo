<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Example class
 */

class Example extends CI_Controller {

	/**
	 * Constructor; Loads on class initilizie
	 * Define controllers settings and global resources
	 *
	 */
	function __construct() {
		parent::__construct();

		// Global resources and templates
		$this->view->assets();
		
		$this->view->templates();
	}
	


	
/************************
	LANDING PAGES
*************************/	
	
	/**
	 * Default home page
	 *
	 */
	function index() {
		$this->view->assets();
		$this->view->templates();
		
		$this->view->render("examples/kitchensink", "Home page");
	}
	
	/**
	 * Device checking example
	 * Using the Device library you can load different views
	 * This example shows how you can pass an array of templates to a single render
	 *
	 */	
	function device() {
		if ($this->device->isSmall()) {
			$this->view->render(array("examples/phone","examples/kitchensink"), "Mobile page");
		} else {
			if ($this->device->isTablet()) {
				$this->view->render(array("examples/tablet","examples/kitchensink"), "Tablet page");
			} else {
				$this->view->render(array("examples/desktop","examples/kitchensink"), "Desktop page");
			}
		}
	}
	
	
	
	
} // END class
?>