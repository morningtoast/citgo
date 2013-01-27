<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * View Library
 *
 * Utility wrappers for view output
 * Handles caching and asset loading
 */

	class View {

		function __construct($params=false) {
			$this->core =& get_instance();
			
			// Output default settings, can be overwritten with parameters
			$this->settings = array(
				"header" => "common/site_header",
				"footer" => "common/site_footer",
				"page"   => "common/site_page",
				"assets" => array(
					"core"
				)
			);
			
			
			$this->localAssets    = array();
			$this->localTemplates = array();
			$this->enableCache    = true;
			
		}
		
		
		/**
		 * Loads CSS/JS assets
		 * Can pass any number of arguments
		 *
		 * @param	str		$path		Relative path to asset
		 */		
		function assets() {
			$this->localAssets = array_merge(func_get_args(), $this->localAssets);
		}
		
		
		/**
		 * Loads ineline templates assets
		 * Can pass any number of arguments
		 *
		 * @param	str		$path		Relative path to template
		 */
		function templates() {
			$this->localTemplates = array_merge(func_get_args(), $this->localTemplates);
		}
		
		
		
		/**
		 * Full page output 
		 *
		 * @param	str		$view		CI path to template
		 * @param	str		$pageTitle	Text that will appear in <title> tag
		 * @param	str		$a_view		View data passed to template
		 * @param	str		$a_custom	Custom settings. Overwrite any defaults.
		 */
		function render($view, $pageTitle=false, $a_view=false, $a_custom=false) {
			$a_assets = $this->loadAssets($this->localAssets);
			$a_head   = array(
				"title"       => SITE_TITLE,
				"description" => SITE_DESCRIPTION
			);
			
			if ($pageTitle) {  $a_head["title"] = $pageTitle.= " | ".$a_head["title"]; } 
					
			$c = $this->core->uri->segment(1);
			$m = $this->core->uri->segment(2);
			
			if ($c and $m) { $a_head["bodyId"] = $c."-".$m; } else { $a_head["bodyId"] = "home"; }
			
			$a_head["css"]         = $a_assets["preload"];
			$a_footer["js"]        = $a_assets["postload"];
			
			
			if ($a_custom) {
				$a_settings = array_merge($this->settings, $a_custom);
			} else {
				$a_settings = $this->settings;
			}
			
			// Build body markup
			if (is_array($view)) {
				foreach ($view as $viewPath) {
					$a_page["body"] .= $this->renderFromCache($viewPath, $a_view);
				}
			} else {
				$a_page["body"] .= $this->renderFromCache($view, $a_view);
			}
			
			$a_footer["templates"] = $this->inlineTemplates($this->localTemplates);

			// Load default header/footer
			$a_page["header"]    = $this->core->load->view($a_settings["header"], $a_head, true);
			$a_page["footer"]    = $this->core->load->view($a_settings["footer"], $a_footer, true);	


			
			$this->core->load->view($a_settings["page"], $a_page);
		}
		
		
		/**
		 * Returns cached version of specified template if it exists
		 * Do not use this for full page caching, elements only
		 *
		 * @param	str		$view		CI path to template
		 * @param	array	$a_view		View data passe to template
		 */
		function renderFromCache($view, $a_view=false) {
			if ($this->enableCache) {
				$cachedVersion = $this->core->cache->get($view);
				
				if ($cachedVersion) {
					$cachedVersion = "\n<!-- ## From cache -->\n".$cachedVersion."\n<!-- END cache -->\n\n";
					return($cachedVersion);
				} else {
					$html = $this->core->load->view($view, $a_view, true);
					
					$this->core->cache->save($view, $html);
					
					return($html);
				}
			} else {
				return($this->core->load->view($view, $a_view, true));
			}
		
		}		
		
		
		
		/**
		 * Returns array of postload and preload assets
		 *
		 * @param	array	$a_resources	List of asset paths or shortcuts
		 */
		function loadAssets($a_resources=false) {
			$a_default = $this->settings["assets"];
				
		
			if ($a_resources) { 
				$a_resources = array_merge($a_default, $a_resources); 
			} else {
				$a_resources = $a_default;
			}
			
			$a_ref = array(
				"postload" => array(
					"internal"=>array(),
					"external"=>array()
				),
				"preload" => array(
					"internal"=>array(),
					"external"=>array()
				),
			);
			
			// Look for any bundles
			$a_combine = array();
			foreach ($a_resources as $k => $path) {
				$a_file    = pathinfo($path);
				$a_add     = array();
				
			
				if (!isset($a_file["extension"])) {
					
					// Define shortcut stacks
					switch ($path) {
						default:break;
						
						case "core":
							$a_add = array(
								"/assets/css/bootstrap.min.css",
								"/assets/js/bootstrap.min.js",
								"/assets/css/common.css",
								"/assets/js/common.js"
							);
							
							
							if ($this->core->device->isSmall()) {
								array_push($a_add, "/assets/css/small.css");
							}
							
							if ($this->core->device->isTablet()) {
								array_push($a_add,"/assets/css/medium.css");
							}
							
							if (!$this->core->device->isMobile()) {
								array_push($a_add,"/assets/css/large.css");
							}
							
							
							break;
						
						case "bootstrap":
							$a_add = array(
								"/assets/css/bootstrap.min.css"
							);
							break;
						
						case "example":
							$a_add = array(
								"/assets/js/custom.js",
								"/assets/css/custom.css"
							);
							
							break;
					}			
				
					unset($a_resources[$k]); // Remove bundle name from list
					$a_combine = array_merge($a_combine, $a_add);
				} else {
					$a_combine[] = $path;
				}
			}

			// Loop through all and split out into parts
			foreach ($a_combine as $path) {
				$a_file = pathinfo($path);
				$prefix = substr($path,0,2);
				
				if ($a_file["extension"] == "js") {
					if (strstr($path,"http://") or strstr($path,"https://")) {
						$a_ref["postload"]["external"][] = $path;
					} else {
						if (strstr($path,"//")) {
							if (strstr($path,"//")) { $path = str_replace("//","/",$path); }
							$a_ref["postload"]["external"][] = $path;
						} else {
							$a_ref["postload"]["internal"][] = $path;
						}
					}
				}
				
				if ($a_file["extension"] == "css") {
					if (strstr($path,"http://") or strstr($path,"https://")) {
						$a_ref["preload"]["external"][] = $path;
					} else {
						if (strstr($path,"//")) {
							if (strstr($path,"//")) { $path = str_replace("//","/",$path); }
							$a_ref["preload"]["external"][] = $path;
						} else {
							$a_ref["preload"]["internal"][] = $path;
						}
					}			
				}
			}

		
			// Create combiners for internal resources
			if (count($a_ref["preload"]["internal"]) > 0) {
				$a_ref["preload"]["external"][]  = "/assets/bundle.php?files=".implode(",", $a_ref["preload"]["internal"]);
			}
			
			if (count($a_ref["postload"]["internal"]) > 0) {
				$a_ref["postload"]["external"][] = "/assets/bundle.php?files=".implode(",", $a_ref["postload"]["internal"]);
			}
			
			// Add combiner to list, each item in list will be a separate call in markup
			$a_assets = array(
				"preload"  => $a_ref["preload"]["external"],
				"postload" => $a_ref["postload"]["external"]
			);

			return($a_assets);
		}	


		/**
		 * Returns markup for inline <script> templates
		 *
		 * @param	mixed	$templateFolder		Folder to loop through OR list of template paths
		 *
		 * @return	str		Final markup					
		 */
		function inlineTemplates($templateFolder=false) {
		
			$html = "";
		
			if ($templateFolder) {
				$this->core->load->helper("directory");
				
				if (!is_array($templateFolder)) { 
					$templateFolder = array($templateFolder);
				}
			
				foreach ($templateFolder as $path) {
					$dirpath       = $path."/";
					$templatesPath = CI_VIEWS."/".$path."/";
					
					if (is_dir($templatesPath)) {
						$a_dir = directory_map($templatesPath);

						foreach ($a_dir as $file) {
							$html .= $this->_createTemplate($dirpath.$file, $file);
						}
					} else {
						$html .= $this->_createTemplate($path, basename($path));
					}
				} //END loop
				
			} // END check

			return($html);
		}
	
			/**
			 * Returns <script> template string. Helper function.
			 *
			 * @param	str		$path		CI path to template file
			 * @param	str		$name		Name to use in #id
			 * @param	array	$a_data		Optional. View data passed to template
			 *
			 * @return	str		Final markup					
			 */
			function _createTemplate($path, $name, $a_data=false) {
				$name = str_replace(array(".php"),array(""),$name);
				
				$this->core->cache->mode("none");
				
				$cachedVersion = $this->core->cache->get($name);
		
				if ($cachedVersion) {
					return($cachedVersion);
				} else {
					if (file_exists(CI_VIEWS."/".$path.".php")) {
						$html  = '<script id="tmpl-'.$name.'" type="text/x-jquery-tmpl">';
						$html .= $this->core->load->view($path, $a_data, true);
						$html .= '</script>';
					
						$this->core->cache->save($name, $html);
					
						return($html);
					} else {
						return("");
					}
				}
			}		
		
		
		
		
	} // END class
?>