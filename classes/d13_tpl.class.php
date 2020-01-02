<?php

// ========================================================================================
//
// TEMPLATE.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
// ABOUT CLASSES:
//
// Represents the lowest layer, next to the database. All logic checks must be performed
// by a controller beforehand. Any class function calls directly access the database. 
// 
// NOTES:
//
// A simple, home-made template engine. This class is the middleman between the controller
// classes and the template files.
//
// Creates a new template by replacing tokens {{inside curly brackets}} with the provided
// data. Can merge data from the language files and some global data as well and combine
// several templates before they are finally sent to the client. can optionally inject
// additional code at the very end of a template (required to handle popup windows and
// tooltips dynamically).
//
// allows to cache template files created that way for faster access later.
//
// ========================================================================================

class d13_tpl

{
	
	private $node;
	
	// ----------------------------------------------------------------------------------------
	// tpl_get
	// @ Retrieves the content of a template file and returns it
	// 3.0
	// ----------------------------------------------------------------------------------------

	public

	function get($templatename)
	{
		$output = "";
		if ($templatename != NULL && $templatename != "") {
			$filename = CONST_INCLUDE_PATH . "templates/" . $_SESSION[CONST_PREFIX . 'User']['template'] . "/tpl/" . $templatename . ".tpl";
			if (file_exists($filename)) {
				$output = trim(file_get_contents($filename));
			}
		}

		return $output;
	}

	// ----------------------------------------------------------------------------------------
	// tpl_parse
	// @ Replaces all array variables inside a template file that must be provided
	// 3.0
	// ----------------------------------------------------------------------------------------

	public

	function parse($template, $vars)
	{
		if (isset($template) && isset($vars)) {
			foreach($vars as $a => $b) {
				$template = str_ireplace("{{{$a}}}", $b, $template);
			}
		}		
		return $template;
	}

	// ----------------------------------------------------------------------------------------
	// tpl_inject_get
	// @ Returns the injected markup and clears the temporary page cache
	// 3.0
	// ----------------------------------------------------------------------------------------
	private
	
	function inject_get($cache_num=0)
	{
		$html = "";
		$cache_num = "cache-".$cache_num;
		if (isset($GLOBALS[$cache_num])) {
			foreach($GLOBALS[$cache_num] as $cache) {
				$html.= $cache;
				$html.= "\n";
			}
			unset($GLOBALS[$cache_num]);
		}
		return $html;
	}
	
	
	// ----------------------------------------------------------------------------------------
	// tpl_inject_get_all
	// @ Returns the injected markup and clears the temporary page cache
	// 3.0
	// ----------------------------------------------------------------------------------------
	private
	
	function inject_get_all()
	{
		$html = "";
		for ($i = 0; $i < 10; $i++) {
			$cache_num = "cache-".$i;
			if (isset($GLOBALS[$cache_num])) {
				foreach($GLOBALS[$cache_num] as $cache) {
					$html.= $cache;
					$html.= "\n";
				}
			}
			unset($GLOBALS[$cache_num]);
		}
		return $html;
	}
	
	// ----------------------------------------------------------------------------------------
	// tpl_inject
	// @ Is used to manually inject markup at the bottom of the current page (like popups)
	// 3.0
	// ----------------------------------------------------------------------------------------
	public

	function inject($content, $cache_num=0)
	{
		if (!empty($content)) {
			$cache = "cache-".$cache_num;
			if (!isset($GLOBALS[$cache])) {
				$GLOBALS[$cache] = array();
			}
			if (!in_array($content, $GLOBALS[$cache])) {
				$GLOBALS[$cache][] = $content;
				if ($cache_num > 0) {
					$GLOBALS[$cache][] = $this->inject_get($cache_num);
				}
			}
		}
	}

	// ----------------------------------------------------------------------------------------
	// tpl_merge_ui_vars
	// @
	// 3.0
	// ----------------------------------------------------------------------------------------
	private
	
	function merge_ui_vars()
	{
		global $d13;
		$vars = array();
		
		foreach($d13->data->json['userinterface'] as $key => $var) {
			$vars["tvar_ui_" . $key] = $var;
		}

		return $vars;
	}

	// ----------------------------------------------------------------------------------------
	// tpl_global_vars
	// @ Iterates and replaces all global template variables and components
	// 3.0
	// ----------------------------------------------------------------------------------------
	private
	
	function global_vars($vars = "", $cache_num=0)
	{
		global $d13;
		$tvars = array();
		
		$tvars["tvar_global_pagetitle"] = CONST_GAME_TITLE;
		$tvars["tvar_global_directory"] = CONST_DIRECTORY;
		$tvars["tvar_global_basepath"] = CONST_BASE_PATH;
		$tvars["tvar_global_template"] = $_SESSION[CONST_PREFIX . 'User']['template'];
		$tvars["tvar_global_color"] = $_SESSION[CONST_PREFIX . 'User']['color'];
		if($this->node){
            $tvars["tvar_nodeFaction"] = $this->node->data['faction'];
        }

		
		// - - - Hide Bottom Toolbar while logged in

		if (isset($_SESSION[CONST_PREFIX . 'User']['id'])) {
			$tvars["tvar_global_notoolbar"] = "no-toolbar";
		}
		else {
			$tvars["tvar_global_notoolbar"] = "";
		}

		// - - - Setup the Message Box (refactor to NOTES class later)

		if (isset($vars["tvar_global_message"]) && !empty($vars["tvar_global_message"])) {
			$tvars["tpl_pvar_message"] = $this->parse($this->get("sub.messagebox") , $vars);
		}
		else {
			$tvars["tpl_pvar_message"] = "";
		}
		
		// - - - Setup possible sub-cache
		if ($cache_num > 0) {
		$tvars["tpl_page_cache"]		= $this->inject_get($cache_num);
		}

		return $tvars;
	}

	// ----------------------------------------------------------------------------------------
	// tpl_render
	// @ Renders a page according to the given template and variables or returns it from Cache
	// 3.0
	// ----------------------------------------------------------------------------------------
	public
	
	function render($template, $vars=array(), $cache=TRUE, $cache_num=0)
	{
		
		/*
		if (!isset($this->node)) {
			if (isset($_SESSION[CONST_PREFIX . 'User']['node']) && $_SESSION[CONST_PREFIX . 'User']['node'] > 0) {
				$this->node	= new d13_node();
				$status = $this->node->get('id', $_SESSION[CONST_PREFIX . 'User']['node']);
			}
		}
		*/
		
		$tvars = array();
		
		$tvars = array_merge($tvars, $vars);
		$tvars = array_merge($tvars, $this->global_vars($vars, $cache_num));
		$tvars = array_merge($tvars, $this->merge_ui_vars());

		$name = 'tpl_' . md5(serialize($vars)) . ".".$template.".tpl";
		$cacheFile = CONST_INCLUDE_PATH . 'cache/templates' . DIRECTORY_SEPARATOR . $name;

		if (!empty($tvars) && CONST_FLAG_CACHE) {
			if (is_file($cacheFile)) {
				return file_get_contents($cacheFile);
			}
		}

		$cacheFileData = $this->parse($this->get($template) , $tvars);
		
		if (!empty($tvars) && CONST_FLAG_CACHE && $cache) {
			file_put_contents($cacheFile, $cacheFileData);
		}
		
		// Give a 5% chance of cache to be checked
		if (CONST_FLAG_CACHE) {
			if (rand(1, 100) <= 5) {
				self::clear_cache();
			}
		}
		
		return $cacheFileData;
		
	}
	
	// ----------------------------------------------------------------------------------------
	// clear_cache
	// @ Clears the template cache directory file by file
	// 3.0
	// ----------------------------------------------------------------------------------------
	private

	function clear_cache()
	{
	
    	$dir = CONST_INCLUDE_PATH. 'cache/templates/';
    	$folder = dir($dir);
		while ($file = $folder->read()) {
			if (filetype($dir.$file) != "dir") {
				if (strtotime("-60 minutes") > @filemtime($dir.$file)) {
					@unlink($dir.$file);
				}
			}
		}
		$folder->close();
	}
	
	// ----------------------------------------------------------------------------------------
	// tpl_render
	// @ Renders a page according to the given template and variables and sends it to the client
	// 3.0
	// ----------------------------------------------------------------------------------------
	public

	function render_page($template, $tvars = array(), $node=NULL)
	{
		
		global $d13;
		
		$tvars["tpl_pvar_name"] 		= $template;
		$tvars['tvar_nodeFaction'] 		= 0;
		$tvars["tpl_page_leftPanel"] 	= '';
		$tvars["tpl_page_rightPanel"] 	= '';
		$tvars["tpl_page_navbar"] 		= "";
		$tvars["tpl_page_subbar"] 		= "";
		
		if (isset($node) && !empty($node)) {
			$this->node = $node;
		}
		
		if (!isset($this->node)) {
			if (isset($_SESSION[CONST_PREFIX . 'User']['node']) && $_SESSION[CONST_PREFIX . 'User']['node'] > 0) {
				$this->node	= new d13_node();
				$status = $this->node->get('id', $_SESSION[CONST_PREFIX . 'User']['node']);
				$tvars['tvar_nodeFaction'] = $this->node->data['faction'];
			}
		}
		
		$tvars = array_merge($tvars, $this->global_vars($tvars));
		$tvars = array_merge($tvars, $this->merge_ui_vars());

		$navBar = new d13_navBarController($this->node);
		$tvars["tpl_page_navbar"] = $navBar->getTemplate();
		
		if (isset($_SESSION[CONST_PREFIX . 'User']['node']) && $_SESSION[CONST_PREFIX . 'User']['node'] > 0) {
			$resBar = new d13_resBarController($this->node);
			$tvars["tpl_page_subbar"] = $resBar->getTemplate();
			$tvars["tpl_page_rightPanel"] = $this->node->queues->getQueuesList();
		}

		$tvars["tpl_page_meta_header"] 	= $this->parse($this->get("meta.header") , $tvars);
		$tvars["tpl_page_meta_footer"] 	= $this->parse($this->get("meta.footer") , $tvars);
		$tvars["tpl_page_content"] 		= $this->render($template, $tvars);
		$tvars["tpl_page_cache"]		= $this->inject_get_all();

		echo $this->render("page", $tvars);
		
		if (CONST_FLAG_PROFILER) {
			$d13->debugLog($tvars['tvar_page']);
		}
		
		exit();
		
	}
}

// =====================================================================================EOF