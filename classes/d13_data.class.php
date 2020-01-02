<?php

// ========================================================================================
//
// DATA.CLASS
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Author......................: BlackScorp
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
// Reads data from JSON files and converts them into cached files. Also makes use of the
// collection class in order to access data. Reads all json files from the data directory,
// used to handle units, technologies and so on. Also reads all language data files.
//
// ========================================================================================

// ----------------------------------------------------------------------------------------
// d13_data
// @
//
// ----------------------------------------------------------------------------------------

class d13_data

{
	
	public $json;
	
	// ----------------------------------------------------------------------------------------
	// construct
	// @
	//
	// ----------------------------------------------------------------------------------------
	public

	function __construct()
	{
		global $d13;
		
		$this->loadFiles();
		
	}

	// ----------------------------------------------------------------------------------------
	// loadFiles
	//
	// @
	// ----------------------------------------------------------------------------------------
	private

	function loadFiles()
	{

		$this->json = array();
		
		//- - - read language files
		$dir = new DirectoryIterator(CONST_INCLUDE_PATH . "locales/" . $_SESSION[CONST_PREFIX . 'User']['locale'] . "/");
		foreach ($dir as $fileinfo) {
    		if (!$fileinfo->isDot()) {
        		$name = $this->getRealName($fileinfo->getFilename());
        		if ($name) {
        			$this->json[$name] = $this->loadFromJSON($fileinfo->getPath() , $fileinfo->getFilename());
        		}
    		}
		}

		//- - - read data files
		$dir = new DirectoryIterator(CONST_INCLUDE_PATH . "data/");
		foreach ($dir as $fileinfo) {
    		if (!$fileinfo->isDot()) {
       		 	$name = $this->getRealName($fileinfo->getFilename());
       		 	if ($name) {
       		 		$this->json[$name] = $this->loadFromJSON($fileinfo->getPath() , $fileinfo->getFilename());
       		 	}
    		}
		}

	}
	
	// ----------------------------------------------------------------------------------------
	// getFileName
	//
	// @
	// ----------------------------------------------------------------------------------------
	private
	
	function getRealName($url)
	{
		
		if (substr($url, 0, 4) === "d13_") {
			$url = basename($url);
			$url = str_ireplace("d13_", "", $url);
			$url = str_ireplace(".data", "", $url);
			$url = str_ireplace(".locale", "", $url);
			$url = str_ireplace(".json", "", $url);
			$url = str_ireplace(".", "_", $url);
		} else {
			$url = "";
		}
		
		return $url;
	
	}

	// ----------------------------------------------------------------------------------------
	// loadFromJSON
	//
	// @
	// ----------------------------------------------------------------------------------------

	private

	function loadFromJSON($path, $name)
	{
   		
   		
   		$url = $path . "/" . $name;
		$cacheFile = CONST_INCLUDE_PATH . 'cache/data' . DIRECTORY_SEPARATOR . basename($url). ".php";
		
		if (is_file($cacheFile) && filemtime($url) < filemtime($cacheFile)) {
			$classConstruct = require_once $cacheFile;
			return $classConstruct;
		}
		
		$jsonString = file_get_contents($url);
		if (!$jsonString) {
			throw  new Exception('Cannot read url: ' . $url);
		}
		$jsonData = json_decode($jsonString, true);
			
		$vars = var_export($jsonData, true);
		$name = "d13_".$this->getRealName($name) ."_cache";
	
		$cacheFileData = '';
		$cacheFileData .= '<?php '.PHP_EOL;
		$cacheFileData .= 'class '.$name.' extends d13_collection {'.PHP_EOL;
		$cacheFileData .= '	public function __construct() {'.PHP_EOL;
		$cacheFileData .= '	$data = '.$vars.';'.PHP_EOL;
		$cacheFileData .= ' parent::__construct($data);';
		$cacheFileData .= '	}'.PHP_EOL;
		$cacheFileData .= '}'.PHP_EOL;
		$cacheFileData .= 'return new '.$name.'();'.PHP_EOL;
		$cacheFileData .= '?>';
	
		file_put_contents($cacheFile, $cacheFileData);
		$classConstruct = require_once $cacheFile;
		return $classConstruct;
	
	}
	
}

// =====================================================================================EOF