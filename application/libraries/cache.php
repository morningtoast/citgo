<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Simple file caching library
 *
 * Writes files to folder with shared expiration time
 *
 */

	class Cache {
		function __construct() {
			$this->core =& get_instance();
			
			$this->core->load->helper("file");
			
			// Settings
			$this->path     = HOST_ROOT."/application/cache/"; // Where cached files will be saved
			$this->expire   = 300; // Cached files expiration in seconds (300 = 5 mins, 600 = 10 mins)
			$this->prefix   = "pagecache_"; // Prefix for cached files
			$this->disable  = true; // Disables all caching
			
			
			$this->mode("params"); // Sets type of caching method
		}
		
		
		
		/**
		 * Returns cached version of data if it exists, otherwise returns FALSE
		 *
		 * @param	str		$name		Name of cache file retrieve
		 * @return	mixed
		 */		
		function get($name) {
			if ($this->disable) {
				return(false);
			} else {
				$path = $this->getCachePath($name);
				
				if (file_exists($path)) {
					$a_info = get_file_info($path);
					
					$now  = time();
					$file = $a_info["date"];
					$diff = ($now - $file);
				
					if ($diff <= $this->expire) {
						return(read_file($path));
					} else {
						return(false);
					}
				} else {
					return(false);
				}
			}
		}
		
		
		/**
		 * Writes data to cache file
		 *
		 * @param	str		$name		Name of cache file to save
		 * @param	str		$data		Data to write to file.
		 * @return	str
		 */		
		function save($name, $data) {
			if (!$this->disable) {
				$path = $this->getCachePath($name);
				write_file($path, $data);
			}
			
			return($data);
		}
		
		
		/**
		 * Deletes all cached files
		 *
		 */		
		function clear() {
			$this->core->load->helper("directory");
			
			$a_files = directory_map($this->path);

			foreach ($a_files as $file) {
				$i_get = strlen($this->prefix);
				$match = substr($file, 0, $i_get);

				if ($match == $this->prefix) {
					@unlink($this->path.$file);
				}
			}
		}		
		
		
		/**
		 * Sets mode of caching for file
		 *
		 * "params" will create cache based on URI segments - use this for dynamic
		 * "none" will create cache based on template only - use for static data
		 *
		 * @param	str		$m		Type of caching. [params]
		 * @return	str		 
		 */
		function mode($m="params") {
			switch ($m) {
				default:
				case "params":
					$this->suffix = "_".$this->core->uri->uri_string();
					break;
				
				case "page":
				case "all":
				case "none":
					$this->suffix = "";
					break;
			}
				
			return($this->suffix);
		}
		
		/**
		 * Returns cache file name based on provided name
		 *
		 * @param	str		$name		Name of cache file to save
		 * @return	str
		 */
		function getCacheKey($name) {
			$s_cacheKey    = md5($this->prefix.str_replace("/","_",$name).$this->suffix);
			return($s_cacheKey);
		}
		
		/**
		 * Returns server path for cache file writing
		 *
		 * @param	str		$name		Name of cache file to save
		 * @return	str
		 */		
		function getCachePath($name) {
			$key  = $this->getCacheKey($name);
			$path = $this->path.$key;
			return($path);
		}
		
		



		
	} // END class
?>