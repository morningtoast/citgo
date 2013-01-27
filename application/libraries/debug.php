<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	Debug for variables
*/

	class Debug {
		function __construct() {
			$this->off();
			$this->style  = "background-color:#ffc;margin:8px;border:solid 1px #ccc; padding:6px;";
		}
		
		function on() {
			$this->enable = true;
		}
		
		function off() {
			$this->enable = false;
		}
	
		function show($var) {
			if ($this->enable) {
				if (is_array($var)) {
					$this->array_debug($var);
				} else {
					if (!$var) {
						$var = "NO VALUE";
					}
					
					echo '<pre style="'.$this->style.'">$var</pre>';
				}		
			}
		}
		
		function array_debug() {
			$args  = func_get_args();
			$count = func_num_args();

			if ($count > 1) {
				if (end($args) == 1) {
					$exit = TRUE;
					array_pop($args);
				}
			}

			foreach ($args as $array) {
				if (is_array($array)) {
					echo '<pre style="'.$this->style.'">';
					print_r($array);
					echo "</pre>";
				} else {
					echo '<pre style="background-color:#f00;color:#ff0;">NOT AN ARRAY</pre>';
				}
			}

			return;
		}		

		
	} // END class
?>