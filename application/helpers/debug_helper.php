<?php

 	function debug($item) {
 		if (DEBUG == TRUE) {
 			if (is_array($item)) {
 				_array_debug($item);
 			} else {
 				if (!$item) {
 					$item = "NO VALUE";
 				}
 				
 				echo "<pre>$item</pre>";
 			}
 		}
 		return;	
 	}
	
	function _array_debug() {
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
				echo "<pre>--- START\n";
				print_r($array);
				echo "\n--- END</pre>";
			} else {
				echo "<p><code>-- Invalid array --</code></p>";
			}
		}

		if ($exit) { exit(); }

		return;
	}	
?>