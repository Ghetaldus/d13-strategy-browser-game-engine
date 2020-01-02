<?php

//========================================================================================
//
// INDEX
//
// !!! THIS FREE PROJECT IS DEVELOPED AND MAINTAINED BY A SINGLE HOBBYIST !!!
// # Author......................: Weaver (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//========================================================================================

error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
date_default_timezone_set('Europe/Berlin');
ini_set('default_charset', 'UTF-8');

ob_start();

require_once("config/d13_core.inc.php");

function d13_error($errno, $errstr, $errfile, $errline) {
	global $d13;
  	$d13->logger(date("H:i:s").": ($errno) $errstr [$errfile][$errline]\n");
}

set_error_handler("d13_error");

$d13->routerRoute();
exit();